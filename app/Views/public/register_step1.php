<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper" data-bg-src="<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Pendaftaran Anggota</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li>Pendaftaran</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Registration Area
==============================-->
<section class="space-top space-extra-bottom">
    <div class="container">
        <!-- Progress Steps -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="progress-steps">
                    <div class="step-item active">
                        <div class="step-icon">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="step-label">Akun</div>
                    </div>
                    <div class="step-connector"></div>
                    <div class="step-item">
                        <div class="step-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="step-label">Data Pribadi</div>
                    </div>
                    <div class="step-connector"></div>
                    <div class="step-item">
                        <div class="step-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="step-label">Data Pekerjaan</div>
                    </div>
                    <div class="step-connector"></div>
                    <div class="step-item">
                        <div class="step-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div class="step-label">Pembayaran Iuran</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xxl-8 col-xl-9 col-lg-10">
                <div class="th-login-form shadow-lg">
                    <div class="form-title text-center mb-4">
                        <h2 class="sec-title mb-2">Daftar Sebagai Anggota Baru</h2>
                        <p class="text-muted mb-0">Langkah 1 dari 4: Buat Akun Anda</p>
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

                    <form action="<?= base_url('registrasi/step-1') ?>" method="POST" class="login-form" id="registerForm">
                        <?= csrf_field() ?>

                        <div class="row">
                            <!-- Email -->
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
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Email akan digunakan untuk login dan komunikasi
                                </small>
                                <?php if (isset($validation) && $validation->hasError('email')): ?>
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        <?= $validation->getError('email') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Password -->
                            <div class="form-group col-md-6 mb-3">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-2"></i>Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control form-control-lg <?= isset($validation) && $validation->hasError('password') ? 'is-invalid' : '' ?>"
                                        id="password"
                                        name="password"
                                        placeholder="Minimal 8 karakter"
                                        autocomplete="new-password"
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
                                <!-- Password Strength Meter -->
                                <div class="password-strength mt-2" id="passwordStrength" style="display: none;">
                                    <div class="strength-meter">
                                        <div class="strength-meter-fill" id="strengthMeterFill"></div>
                                    </div>
                                    <small class="strength-text" id="strengthText"></small>
                                </div>
                                <small class="form-text text-muted d-block mt-1">
                                    <i class="fas fa-shield-alt me-1"></i>Minimal 8 karakter dengan huruf besar, kecil, angka, dan simbol
                                </small>
                            </div>

                            <!-- Password Confirmation -->
                            <div class="form-group col-md-6 mb-3">
                                <label for="password_confirm" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-2"></i>Konfirmasi Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control form-control-lg <?= isset($validation) && $validation->hasError('password_confirm') ? 'is-invalid' : '' ?>"
                                        id="password_confirm"
                                        name="password_confirm"
                                        placeholder="Ulangi password"
                                        autocomplete="new-password"
                                        required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                        <i class="far fa-eye" id="togglePasswordConfirmIcon"></i>
                                    </button>
                                    <?php if (isset($validation) && $validation->hasError('password_confirm')): ?>
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            <?= $validation->getError('password_confirm') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <small class="form-text" id="passwordMatchText"></small>
                            </div>

                            <div class="col-12">
                                <hr class="my-4">
                                <h5 class="mb-3"><i class="fas fa-user me-2"></i>Informasi Dasar</h5>
                            </div>

                            <!-- Full Name -->
                            <div class="form-group col-12 mb-3">
                                <label for="full_name" class="form-label fw-semibold">
                                    <i class="fas fa-user me-2"></i>Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control form-control-lg <?= isset($validation) && $validation->hasError('full_name') ? 'is-invalid' : '' ?>"
                                    id="full_name"
                                    name="full_name"
                                    placeholder="Nama lengkap sesuai KTP"
                                    value="<?= old('full_name') ?>"
                                    autocomplete="name"
                                    required>
                                <?php if (isset($validation) && $validation->hasError('full_name')): ?>
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        <?= $validation->getError('full_name') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Phone Number -->
                            <div class="form-group col-md-6 mb-3">
                                <label for="phone_number" class="form-label fw-semibold">
                                    <i class="fas fa-phone me-2"></i>Nomor HP <span class="text-danger">*</span>
                                </label>
                                <input type="tel"
                                    class="form-control form-control-lg <?= isset($validation) && $validation->hasError('phone_number') ? 'is-invalid' : '' ?>"
                                    id="phone_number"
                                    name="phone_number"
                                    placeholder="628xxxxxxxxxx"
                                    value="<?= old('phone_number') ?>"
                                    autocomplete="tel"
                                    required>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Format: 628xx (10-15 digit)
                                </small>
                                <?php if (isset($validation) && $validation->hasError('phone_number')): ?>
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        <?= $validation->getError('phone_number') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- University Name -->
                            <div class="form-group col-md-6 mb-3">
                                <label for="university_name" class="form-label fw-semibold">
                                    <i class="fas fa-university me-2"></i>Asal Kampus <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control form-control-lg <?= isset($validation) && $validation->hasError('university_name') ? 'is-invalid' : '' ?>"
                                    id="university_name"
                                    name="university_name"
                                    placeholder="Nama kampus tempat bekerja"
                                    value="<?= old('university_name') ?>"
                                    autocomplete="organization"
                                    required>
                                <?php if (isset($validation) && $validation->hasError('university_name')): ?>
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        <?= $validation->getError('university_name') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-12 mt-3">
                                <div class="alert alert-info border-0">
                                    <div class="d-flex">
                                        <i class="fas fa-info-circle me-3 fs-5"></i>
                                        <div>
                                            <strong>Informasi Pendaftaran:</strong>
                                            <ul class="mb-0 mt-2 ps-3">
                                                <li>Setelah membuat akun, verifikasi email Anda terlebih dahulu</li>
                                                <li>Lengkapi data pribadi dan data pekerjaan di langkah berikutnya</li>
                                                <li>Upload dokumen dan bukti pembayaran pendaftaran</li>
                                                <li>Proses verifikasi akan dilakukan oleh admin dalam 1x24 jam</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-btn col-12 mt-4">
                                <button type="submit" class="th-btn btn-fw btn-lg" id="submitBtn">
                                    <span id="btnText">
                                        <i class="fas fa-arrow-right me-2"></i>Lanjutkan ke Langkah 2
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
                            <p class="mb-0">Sudah punya akun?
                                <a href="<?= base_url('login') ?>" class="text-primary fw-semibold text-decoration-none">
                                    <i class="fas fa-sign-in-alt me-1"></i>Login di sini
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
    /* Form Styling */
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

    .input-group .form-control:focus+.btn-outline-secondary {
        border-color: var(--theme-color);
    }

    /* Progress Steps */
    .progress-steps {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 800px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .step-item {
        text-align: center;
        position: relative;
        z-index: 2;
    }

    .step-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        font-size: 24px;
        color: #999;
        transition: all 0.3s ease;
    }

    .step-item.active .step-icon {
        background: var(--theme-color);
        border-color: var(--theme-color);
        color: #fff;
        box-shadow: 0 4px 12px rgba(var(--theme-color-rgb), 0.3);
        transform: scale(1.1);
    }

    .step-label {
        font-size: 14px;
        font-weight: 600;
        color: #999;
        transition: all 0.3s ease;
    }

    .step-item.active .step-label {
        color: var(--theme-color);
    }

    .step-connector {
        flex: 1;
        height: 3px;
        background: #e0e0e0;
        margin: 0 10px 30px;
        position: relative;
        z-index: 1;
    }

    /* Password Strength Meter */
    .password-strength {
        margin-top: 8px;
    }

    .strength-meter {
        height: 4px;
        background: #e0e0e0;
        border-radius: 2px;
        overflow: hidden;
        margin-bottom: 4px;
    }

    .strength-meter-fill {
        height: 100%;
        width: 0%;
        transition: all 0.3s ease;
        border-radius: 2px;
    }

    .strength-text {
        font-size: 12px;
        font-weight: 600;
    }

    .strength-weak .strength-meter-fill {
        width: 33%;
        background: #dc3545;
    }

    .strength-medium .strength-meter-fill {
        width: 66%;
        background: #ffc107;
    }

    .strength-strong .strength-meter-fill {
        width: 100%;
        background: #28a745;
    }

    .strength-weak .strength-text {
        color: #dc3545;
    }

    .strength-medium .strength-text {
        color: #ffc107;
    }

    .strength-strong .strength-text {
        color: #28a745;
    }

    /* Alerts */
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

    /* Divider */
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

    /* Button */
    .th-btn {
        transition: all 0.3s ease;
    }

    .th-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .th-btn:active {
        transform: translateY(0);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .th-login-form {
            padding: 24px;
        }

        .form-control-lg {
            font-size: 14px;
        }

        .progress-steps {
            padding: 0 10px;
        }

        .step-icon {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }

        .step-label {
            font-size: 12px;
        }

        .step-connector {
            margin: 0 5px 25px;
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
                togglePasswordIcon.classList.toggle('fa-eye');
                togglePasswordIcon.classList.toggle('fa-eye-slash');
            });
        }

        // Toggle password confirm visibility
        const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
        const passwordConfirm = document.getElementById('password_confirm');
        const togglePasswordConfirmIcon = document.getElementById('togglePasswordConfirmIcon');

        if (togglePasswordConfirm) {
            togglePasswordConfirm.addEventListener('click', function() {
                const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirm.setAttribute('type', type);
                togglePasswordConfirmIcon.classList.toggle('fa-eye');
                togglePasswordConfirmIcon.classList.toggle('fa-eye-slash');
            });
        }

        // Password strength meter
        const passwordStrength = document.getElementById('passwordStrength');
        const strengthMeterFill = document.getElementById('strengthMeterFill');
        const strengthText = document.getElementById('strengthText');

        if (password) {
            password.addEventListener('input', function() {
                const value = this.value;
                if (value.length > 0) {
                    passwordStrength.style.display = 'block';
                    const strength = calculatePasswordStrength(value);
                    updateStrengthMeter(strength);
                } else {
                    passwordStrength.style.display = 'none';
                }
            });
        }

        function calculatePasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[@$!%*?&]/.test(password)) strength++;

            if (strength <= 2) return 'weak';
            if (strength <= 4) return 'medium';
            return 'strong';
        }

        function updateStrengthMeter(strength) {
            passwordStrength.className = 'password-strength strength-' + strength;

            if (strength === 'weak') {
                strengthText.textContent = 'Lemah - Tambahkan lebih banyak karakter';
            } else if (strength === 'medium') {
                strengthText.textContent = 'Sedang - Hampir kuat';
            } else {
                strengthText.textContent = 'Kuat - Password aman!';
            }
        }

        // Password match validation
        const passwordMatchText = document.getElementById('passwordMatchText');

        if (passwordConfirm) {
            passwordConfirm.addEventListener('input', function() {
                if (this.value.length > 0) {
                    if (this.value === password.value) {
                        passwordMatchText.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i><span class="text-success">Password cocok</span>';
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        passwordMatchText.innerHTML = '<i class="fas fa-times-circle text-danger me-1"></i><span class="text-danger">Password tidak cocok</span>';
                        this.classList.remove('is-valid');
                        this.classList.add('is-invalid');
                    }
                } else {
                    passwordMatchText.innerHTML = '';
                    this.classList.remove('is-valid', 'is-invalid');
                }
            });
        }

        // Form submission with loading state
        const registerForm = document.getElementById('registerForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoading = document.getElementById('btnLoading');

        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                // Validate passwords match
                if (password.value !== passwordConfirm.value) {
                    e.preventDefault();
                    passwordConfirm.classList.add('is-invalid');
                    passwordMatchText.innerHTML = '<i class="fas fa-times-circle text-danger me-1"></i><span class="text-danger">Password tidak cocok</span>';
                    return false;
                }

                // Show loading state
                submitBtn.disabled = true;
                btnText.classList.add('d-none');
                btnLoading.classList.remove('d-none');
            });
        }

        // Auto-dismiss alerts
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });

        // Phone number formatting
        const phoneInput = document.getElementById('phone_number');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                // Remove non-digits
                this.value = this.value.replace(/\D/g, '');
            });
        }

        // Email validation
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
    });
</script>

<?= $this->endSection() ?>