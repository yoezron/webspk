from flask import Blueprint, render_template, redirect, url_for, flash, request
from flask_login import login_required, current_user
from app import db
from app.models import Survey, SurveyQuestion, SurveyResponse
import json

bp = Blueprint('survey', __name__, url_prefix='/survey')

@bp.route('/')
@login_required
def index():
    surveys = Survey.query.filter_by(is_active=True).order_by(Survey.created_at.desc()).all()
    return render_template('survey/index.html', surveys=surveys)

@bp.route('/<int:survey_id>')
@login_required
def view_survey(survey_id):
    survey = Survey.query.get_or_404(survey_id)
    
    # Check if user already responded
    existing_response = SurveyResponse.query.filter_by(
        survey_id=survey.id,
        user_id=current_user.id
    ).first()
    
    return render_template('survey/view.html', survey=survey, 
                         already_responded=existing_response is not None)

@bp.route('/<int:survey_id>/submit', methods=['POST'])
@login_required
def submit_survey(survey_id):
    survey = Survey.query.get_or_404(survey_id)
    
    # Check if already responded
    existing = SurveyResponse.query.filter_by(
        survey_id=survey.id,
        user_id=current_user.id
    ).first()
    
    if existing:
        flash('Anda sudah mengisi survei ini', 'warning')
        return redirect(url_for('survey.index'))
    
    # Collect answers
    answers = {}
    for question in survey.questions:
        answer_key = f'question_{question.id}'
        answers[str(question.id)] = request.form.get(answer_key, '')
    
    response = SurveyResponse(
        survey_id=survey.id,
        user_id=current_user.id,
        answers=json.dumps(answers)
    )
    db.session.add(response)
    db.session.commit()
    
    flash('Terima kasih telah mengisi survei!', 'success')
    return redirect(url_for('survey.index'))

@bp.route('/create', methods=['GET', 'POST'])
@login_required
def create_survey():
    if not current_user.is_admin:
        flash('Akses ditolak', 'danger')
        return redirect(url_for('survey.index'))
    
    if request.method == 'POST':
        survey = Survey(
            title=request.form.get('title'),
            description=request.form.get('description'),
            is_active=True
        )
        db.session.add(survey)
        db.session.flush()
        
        # Add questions
        question_count = int(request.form.get('question_count', 0))
        for i in range(question_count):
            question_text = request.form.get(f'question_{i}_text')
            question_type = request.form.get(f'question_{i}_type', 'text')
            
            if question_text:
                question = SurveyQuestion(
                    survey_id=survey.id,
                    question_text=question_text,
                    question_type=question_type,
                    order=i
                )
                db.session.add(question)
        
        db.session.commit()
        flash('Survei berhasil dibuat', 'success')
        return redirect(url_for('survey.index'))
    
    return render_template('survey/create.html')

@bp.route('/<int:survey_id>/results')
@login_required
def survey_results(survey_id):
    if not current_user.is_admin:
        flash('Akses ditolak', 'danger')
        return redirect(url_for('survey.index'))
    
    survey = Survey.query.get_or_404(survey_id)
    responses = SurveyResponse.query.filter_by(survey_id=survey.id).all()
    
    # Process results
    results = {}
    for question in survey.questions:
        results[question.id] = {
            'question': question.question_text,
            'type': question.question_type,
            'answers': []
        }
        
        for response in responses:
            answers = json.loads(response.answers)
            if str(question.id) in answers:
                results[question.id]['answers'].append(answers[str(question.id)])
    
    return render_template('survey/results.html', survey=survey, results=results)
