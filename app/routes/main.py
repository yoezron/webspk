from flask import Blueprint, render_template
from flask_login import current_user

bp = Blueprint('main', __name__)

@bp.route('/')
def index():
    return render_template('index.html')

@bp.route('/dashboard')
def dashboard():
    if current_user.is_authenticated:
        if current_user.is_admin:
            return render_template('admin_dashboard.html')
        return render_template('member_dashboard.html')
    return render_template('index.html')
