<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Ubah Password</h1>
            <span>Perbarui password akun Anda</span>
        </div>
    </div>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="material-icons-outlined">check_circle</i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="material-icons-outlined">error</i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="material-icons-outlined">error</i>
        <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="material-icons-outlined">lock</i>
                    Keamanan Akun
                </h5>
            </div>
            <div class="card-body">
                <!-- Security Info -->
                <div class="alert alert-info">
                    <i class="material-icons-outlined">info</i>
                    <strong>Tips Keamanan Password:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Gunakan minimal 8 karakter</li>
                        <li>Kombinasikan huruf besar, huruf kecil, angka, dan simbol</li>
                        <li>Jangan gunakan informasi pribadi yang mudah ditebak</li>
                        <li>Gunakan password yang berbeda untuk setiap akun</li>
                        <li>Ubah password secara berkala</li>
                    </ul>
                </div>

                <form method="POST" action="<?= base_url('member/profile/change-password') ?>" id="changePasswordForm">
                    <?= csrf_field() ?>

                    <!-- Current Password -->
                    <div class="mb-3">
                        <label for="current_password" class="form-label">
                            Password Saat Ini <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password"
                                   class="form-control <?= session('errors.current_password') ? 'is-invalid' : '' ?>"
                                   id="current_password"
                                   name="current_password"
                                   required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                <i class="material-icons-outlined" id="currentPasswordIcon">visibility</i>
                            </button>
                            <?php if (session('errors.current_password')): ?>
                                <div class="invalid-feedback"><?= session('errors.current_password') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label for="new_password" class="form-label">
                            Password Baru <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password"
                                   class="form-control <?= session('errors.new_password') ? 'is-invalid' : '' ?>"
                                   id="new_password"
                                   name="new_password"
                                   required
                                   minlength="8">
                            <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                <i class="material-icons-outlined" id="newPasswordIcon">visibility</i>
                            </button>
                            <?php if (session('errors.new_password')): ?>
                                <div class="invalid-feedback"><?= session('errors.new_password') ?></div>
                            <?php endif; ?>
                        </div>
                        <!-- Password Strength Indicator -->
                        <div class="mt-2">
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" id="passwordStrength" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small id="passwordStrengthText" class="text-muted"></small>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">
                            Konfirmasi Password Baru <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password"
                                   class="form-control <?= session('errors.confirm_password') ? 'is-invalid' : '' ?>"
                                   id="confirm_password"
                                   name="confirm_password"
                                   required
                                   minlength="8">
                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                <i class="material-icons-outlined" id="confirmPasswordIcon">visibility</i>
                            </button>
                            <?php if (session('errors.confirm_password')): ?>
                                <div class="invalid-feedback"><?= session('errors.confirm_password') ?></div>
                            <?php endif; ?>
                        </div>
                        <small id="passwordMatch" class="text-muted"></small>
                    </div>

                    <hr>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('member/profile') ?>" class="btn btn-secondary">
                            <i class="material-icons-outlined">arrow_back</i>
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            <i class="material-icons-outlined">save</i>
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Toggle password visibility
document.getElementById('toggleCurrentPassword').addEventListener('click', function() {
    const input = document.getElementById('current_password');
    const icon = document.getElementById('currentPasswordIcon');
    togglePasswordVisibility(input, icon);
});

document.getElementById('toggleNewPassword').addEventListener('click', function() {
    const input = document.getElementById('new_password');
    const icon = document.getElementById('newPasswordIcon');
    togglePasswordVisibility(input, icon);
});

document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
    const input = document.getElementById('confirm_password');
    const icon = document.getElementById('confirmPasswordIcon');
    togglePasswordVisibility(input, icon);
});

function togglePasswordVisibility(input, icon) {
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
}

// Password strength checker
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');

    let strength = 0;
    const checks = {
        length: password.length >= 8,
        lowercase: /[a-z]/.test(password),
        uppercase: /[A-Z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[^A-Za-z0-9]/.test(password)
    };

    // Calculate strength
    Object.values(checks).forEach(check => {
        if (check) strength++;
    });

    // Update progress bar
    const percentage = (strength / 5) * 100;
    strengthBar.style.width = percentage + '%';

    // Set color and text based on strength
    strengthBar.className = 'progress-bar';
    if (strength <= 2) {
        strengthBar.classList.add('bg-danger');
        strengthText.textContent = 'Password lemah';
        strengthText.className = 'text-danger';
    } else if (strength <= 3) {
        strengthBar.classList.add('bg-warning');
        strengthText.textContent = 'Password sedang';
        strengthText.className = 'text-warning';
    } else if (strength <= 4) {
        strengthBar.classList.add('bg-info');
        strengthText.textContent = 'Password kuat';
        strengthText.className = 'text-info';
    } else {
        strengthBar.classList.add('bg-success');
        strengthText.textContent = 'Password sangat kuat';
        strengthText.className = 'text-success';
    }

    checkPasswordMatch();
});

// Check password match
document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);

function checkPasswordMatch() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const matchText = document.getElementById('passwordMatch');
    const submitBtn = document.getElementById('submitBtn');

    if (confirmPassword === '') {
        matchText.textContent = '';
        submitBtn.disabled = true;
        return;
    }

    if (newPassword === confirmPassword) {
        matchText.textContent = '✓ Password cocok';
        matchText.className = 'text-success';
        submitBtn.disabled = false;
    } else {
        matchText.textContent = '✗ Password tidak cocok';
        matchText.className = 'text-danger';
        submitBtn.disabled = true;
    }
}

// Form validation
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('Password baru dan konfirmasi password tidak cocok!');
        return false;
    }

    if (newPassword.length < 8) {
        e.preventDefault();
        alert('Password minimal 8 karakter!');
        return false;
    }
});
</script>
<?= $this->endSection() ?>
