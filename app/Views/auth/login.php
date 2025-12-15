<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper" data-bg-src="<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Login</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li>Login</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Login Area
==============================-->
<section class="space-top space-extra-bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-6 col-xl-7 col-lg-8">
                <div class="th-login-form shadow-lg">
                    <div class="form-title text-center">
                        <h2 class="sec-title mb-2">Login ke Akun Anda</h2>
                        <p class="mb-4 text-muted">Masukkan email dan password untuk mengakses dashboard</p>
                    </div>

                    <?php if (session()->has('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                                <div><?= session('error') ?></div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle me-2 fs-5"></i>
                                <div><?= session('success') ?></div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('info')): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle me-2 fs-5"></i>
                                <div><?= session('info') ?></div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('auth/login') ?>" method="POST" class="login-form" id="loginForm">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="form-group col-12 mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope me-2"></i>Email <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                       class="form-control form-control-lg <?= isset($validation) && $validation->hasError('email') ? 'is-invalid' : '' ?>"
                                       id="email"
                                       name="email"
                                       placeholder="nama@email.com"
                                       value="<?= old('email') ?>"
                                       autocomplete="email"
                                       required>
                                <?php if (isset($validation) && $validation->hasError('email')): ?>
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        <?= $validation->getError('email') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group col-12 mb-3">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-2"></i>Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control form-control-lg <?= isset($validation) && $validation->hasError('password') ? 'is-invalid' : '' ?>"
                                           id="password"
                                           name="password"
                                           placeholder="Masukkan password Anda"
                                           autocomplete="current-password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="far fa-eye" id="togglePasswordIcon"></i>
                                    </button>
                                    <?php if (isset($validation) && $validation->hasError('password')): ?>
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            <?= $validation->getError('password') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group col-12 mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                                        <label class="form-check-label" for="remember">
                                            Ingat Saya
                                        </label>
                                    </div>
                                    <a href="<?= base_url('forgot-password') ?>" class="text-primary text-decoration-none">
                                        <i class="fas fa-key me-1"></i>Lupa Password?
                                    </a>
                                </div>
                            </div>

                            <div class="form-btn col-12 mt-2">
                                <button type="submit" class="th-btn btn-fw btn-lg" id="submitBtn">
                                    <span id="btnText">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login
                                    </span>
                                    <span id="btnLoading" class="d-none">
                                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        Memproses...
                                    </span>
                                </button>
                            </div>
                        </div>

                        <div class="divider my-4">
                            <span class="divider-text">atau</span>
                        </div>

                        <div class="text-center">
                            <p class="mb-0">Belum punya akun?
                                <a href="<?= base_url('registrasi') ?>" class="text-primary fw-semibold text-decoration-none">
                                    <i class="fas fa-user-plus me-1"></i>Daftar Sekarang
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.th-login-form {
    background: #fff;
    padding: 40px;
    border-radius: 16px;
    border: 1px solid #e8e8e8;
}

.form-control-lg {
    padding: 12px 16px;
    font-size: 15px;
    border-radius: 8px;
    border: 2px solid #e8e8e8;
    transition: all 0.3s ease;
}

.form-control-lg:focus {
    border-color: var(--theme-color);
    box-shadow: 0 0 0 0.2rem rgba(var(--theme-color-rgb), 0.1);
}

.form-label {
    margin-bottom: 8px;
    color: #333;
    font-size: 14px;
}

.input-group .btn-outline-secondary {
    border: 2px solid #e8e8e8;
    border-left: none;
    background: #fff;
    border-radius: 0 8px 8px 0;
    padding: 12px 16px;
    transition: all 0.3s ease;
}

.input-group .btn-outline-secondary:hover {
    background: #f8f9fa;
    border-color: #e8e8e8;
}

.input-group .form-control {
    border-right: none;
    border-radius: 8px 0 0 8px;
}

.input-group .form-control:focus {
    border-right: none;
}

.input-group .form-control:focus + .btn-outline-secondary {
    border-color: var(--theme-color);
}

.alert {
    border-radius: 10px;
    padding: 16px;
    margin-bottom: 24px;
    border: none;
}

.alert-danger {
    background: #fee;
    color: #c33;
}

.alert-success {
    background: #efe;
    color: #3c3;
}

.alert-info {
    background: #e3f2fd;
    color: #1976d2;
}

.divider {
    display: flex;
    align-items: center;
    text-align: center;
}

.divider::before,
.divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #e8e8e8;
}

.divider-text {
    padding: 0 16px;
    color: #999;
    font-size: 14px;
}

.th-btn {
    transition: all 0.3s ease;
    position: relative;
}

.th-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.th-btn:active {
    transform: translateY(0);
}

.form-check-input:checked {
    background-color: var(--theme-color);
    border-color: var(--theme-color);
}

@media (max-width: 768px) {
    .th-login-form {
        padding: 24px;
    }

    .form-control-lg {
        font-size: 14px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');

    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // Toggle icon
            if (type === 'text') {
                togglePasswordIcon.classList.remove('fa-eye');
                togglePasswordIcon.classList.add('fa-eye-slash');
            } else {
                togglePasswordIcon.classList.remove('fa-eye-slash');
                togglePasswordIcon.classList.add('fa-eye');
            }
        });
    }

    // Form submission with loading state
    const loginForm = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoading = document.getElementById('btnLoading');

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            // Show loading state
            submitBtn.disabled = true;
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');
        });
    }

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Email validation on blur
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const email = this.value.trim();
            if (email && !isValidEmail(email)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }

    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Enter key to submit
    document.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && document.activeElement.tagName !== 'BUTTON') {
            loginForm.submit();
        }
    });
});
</script>

<?= $this->endSection() ?>
