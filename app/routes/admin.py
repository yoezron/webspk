from flask import Blueprint, render_template, redirect, url_for, flash, request, current_app
from flask_login import login_required, current_user
from app import db
from app.models import Member, User, Survey, ForumPost
from app.utils import generate_member_number, generate_member_card
from datetime import datetime
import json

bp = Blueprint('admin', __name__, url_prefix='/admin')

def admin_required(f):
    from functools import wraps
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if not current_user.is_authenticated or not current_user.is_admin:
            flash('Akses ditolak. Hanya admin yang dapat mengakses halaman ini.', 'danger')
            return redirect(url_for('main.dashboard'))
        return f(*args, **kwargs)
    return decorated_function

@bp.route('/dashboard')
@login_required
@admin_required
def dashboard():
    total_members = Member.query.count()
    pending_members = Member.query.filter_by(status='pending').count()
    active_members = Member.query.filter_by(status='active').count()
    terminated_members = Member.query.filter_by(status='terminated').count()
    
    stats = {
        'total': total_members,
        'pending': pending_members,
        'active': active_members,
        'terminated': terminated_members
    }
    
    return render_template('admin/dashboard.html', stats=stats)

@bp.route('/member/<int:member_id>/approve', methods=['POST'])
@login_required
@admin_required
def approve_member(member_id):
    member = Member.query.get_or_404(member_id)
    member.status = 'active'
    member.member_number = generate_member_number()
    member.updated_at = datetime.utcnow()
    db.session.commit()
    
    flash(f'Anggota {member.full_name} telah disetujui dengan nomor {member.member_number}', 'success')
    return redirect(url_for('members.list_members'))

@bp.route('/member/<int:member_id>/reject', methods=['POST'])
@login_required
@admin_required
def reject_member(member_id):
    member = Member.query.get_or_404(member_id)
    member.status = 'rejected'
    member.updated_at = datetime.utcnow()
    db.session.commit()
    
    flash(f'Pendaftaran anggota {member.full_name} ditolak', 'warning')
    return redirect(url_for('members.list_members'))

@bp.route('/member/<int:member_id>/terminate', methods=['POST'])
@login_required
@admin_required
def terminate_member(member_id):
    member = Member.query.get_or_404(member_id)
    member.status = 'terminated'
    member.updated_at = datetime.utcnow()
    db.session.commit()
    
    flash(f'Keanggotaan {member.full_name} telah diberhentikan', 'info')
    return redirect(url_for('members.list_members'))

@bp.route('/member/<int:member_id>/issue-card', methods=['POST'])
@login_required
@admin_required
def issue_card(member_id):
    member = Member.query.get_or_404(member_id)
    
    if member.status != 'active':
        flash('Kartu hanya dapat diterbitkan untuk anggota aktif', 'warning')
        return redirect(url_for('members.list_members'))
    
    card_path = generate_member_card(member)
    member.card_issued = True
    member.card_path = card_path
    member.updated_at = datetime.utcnow()
    db.session.commit()
    
    flash(f'Kartu anggota untuk {member.full_name} telah diterbitkan', 'success')
    return redirect(url_for('members.list_members'))

@bp.route('/statistics')
@login_required
@admin_required
def statistics():
    members_by_department = db.session.query(
        Member.department, 
        db.func.count(Member.id)
    ).group_by(Member.department).all()
    
    members_by_status = db.session.query(
        Member.status,
        db.func.count(Member.id)
    ).group_by(Member.status).all()
    
    return render_template('admin/statistics.html', 
                         departments=members_by_department,
                         statuses=members_by_status)
