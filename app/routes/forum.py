from flask import Blueprint, render_template, redirect, url_for, flash, request
from flask_login import login_required, current_user
from app import db
from app.models import ForumPost, ForumReply
from datetime import datetime

bp = Blueprint('forum', __name__, url_prefix='/forum')

@bp.route('/')
@login_required
def index():
    posts = ForumPost.query.order_by(ForumPost.created_at.desc()).all()
    return render_template('forum/index.html', posts=posts)

@bp.route('/post/<int:post_id>')
@login_required
def view_post(post_id):
    post = ForumPost.query.get_or_404(post_id)
    return render_template('forum/view_post.html', post=post)

@bp.route('/create', methods=['GET', 'POST'])
@login_required
def create_post():
    if request.method == 'POST':
        post = ForumPost(
            user_id=current_user.id,
            title=request.form.get('title'),
            content=request.form.get('content')
        )
        db.session.add(post)
        db.session.commit()
        
        flash('Post berhasil dibuat', 'success')
        return redirect(url_for('forum.view_post', post_id=post.id))
    
    return render_template('forum/create_post.html')

@bp.route('/post/<int:post_id>/reply', methods=['POST'])
@login_required
def reply_post(post_id):
    post = ForumPost.query.get_or_404(post_id)
    content = request.form.get('content')
    
    if content:
        reply = ForumReply(
            post_id=post.id,
            user_id=current_user.id,
            content=content
        )
        db.session.add(reply)
        db.session.commit()
        flash('Balasan berhasil ditambahkan', 'success')
    
    return redirect(url_for('forum.view_post', post_id=post_id))

@bp.route('/post/<int:post_id>/delete', methods=['POST'])
@login_required
def delete_post(post_id):
    post = ForumPost.query.get_or_404(post_id)
    
    if post.user_id != current_user.id and not current_user.is_admin:
        flash('Anda tidak memiliki akses untuk menghapus post ini', 'danger')
        return redirect(url_for('forum.view_post', post_id=post_id))
    
    db.session.delete(post)
    db.session.commit()
    flash('Post berhasil dihapus', 'success')
    return redirect(url_for('forum.index'))
