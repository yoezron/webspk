from flask import Blueprint, render_template, redirect, url_for, flash, request, send_file, current_app
from flask_login import login_required, current_user
from app import db
from app.models import Member
from app.utils import generate_member_number, generate_member_card
from datetime import datetime

bp = Blueprint('members', __name__, url_prefix='/members')

@bp.route('/register', methods=['GET', 'POST'])
@login_required
def register():
    if current_user.member:
        flash('Anda sudah terdaftar sebagai anggota', 'info')
        return redirect(url_for('members.profile'))
    
    if request.method == 'POST':
        member = Member(
            user_id=current_user.id,
            full_name=request.form.get('full_name'),
            id_number=request.form.get('id_number'),
            phone=request.form.get('phone'),
            address=request.form.get('address'),
            department=request.form.get('department'),
            position=request.form.get('position')
        )
        db.session.add(member)
        db.session.commit()
        
        flash('Pendaftaran anggota berhasil! Menunggu persetujuan.', 'success')
        return redirect(url_for('members.profile'))
    
    return render_template('members/register.html')

@bp.route('/profile')
@login_required
def profile():
    member = current_user.member
    if not member:
        return redirect(url_for('members.register'))
    return render_template('members/profile.html', member=member)

@bp.route('/card/<int:member_id>')
@login_required
def download_card(member_id):
    member = Member.query.get_or_404(member_id)
    
    if not current_user.is_admin and member.user_id != current_user.id:
        flash('Anda tidak memiliki akses ke kartu anggota ini', 'danger')
        return redirect(url_for('main.dashboard'))
    
    if not member.card_issued or not member.card_path:
        flash('Kartu anggota belum diterbitkan', 'warning')
        return redirect(url_for('members.profile'))
    
    return send_file(member.card_path, as_attachment=True)

@bp.route('/list')
@login_required
def list_members():
    if not current_user.is_admin:
        flash('Akses ditolak', 'danger')
        return redirect(url_for('main.dashboard'))
    
    status_filter = request.args.get('status', 'all')
    query = Member.query
    
    if status_filter != 'all':
        query = query.filter_by(status=status_filter)
    
    members = query.order_by(Member.created_at.desc()).all()
    return render_template('members/list.html', members=members, status_filter=status_filter)
