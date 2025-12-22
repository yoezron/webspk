<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= esc($description ?? 'Reset Password - Serikat Pekerja Kampus') ?>">
    <?= csrf_meta() ?>

    <title><?= esc($title ?? 'Reset Password') ?> - Serikat Pekerja Kampus</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="<?= base_url('assets/neptune/plugins/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Auth Styles -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-logo i {
            font-size: 60px;
            color: #667eea;
            margin-bottom: 15px;
        }

        .auth-logo h3 {
            color: #333;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .auth-logo p {
            color: #888;
            font-size: 14px;
        }

        .form-label {
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            margin-top: 20px;
            transition: transform 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }

        .back-to-login a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .back-to-login a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .password-requirements {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }

        .password-requirements h6 {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .password-requirements ul {
            margin: 0;
            padding-left: 20px;
            font-size: 13px;
            color: #555;
        }

        .password-requirements li {
            margin-bottom: 5px;
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle .toggle-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
        }

        .password-toggle .toggle-icon:hover {
            color: #667eea;
        }

        .password-strength {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s;
        }

        .password-strength-bar.weak {
            width: 33%;
            background: #dc3545;
        }

        .password-strength-bar.medium {
            width: 66%;
            background: #ffc107;
        }

        .password-strength-bar.strong {
            width: 100%;
            background: #28a745;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <!-- Logo & Title -->
        <div class="auth-logo">
            <i class="fas fa-lock-open"></i>
            <h3>Reset Password</h3>
            <p>Buat password baru yang kuat dan aman</p>
        </div>

        <!-- Flash Messages -->
        <?php if (session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Password Requirements -->
        <div class="password-requirements">
            <h6><i class="fas fa-shield-alt me-2"></i>Persyaratan Password:</h6>
            <ul>
                <li>Minimal 8 karakter</li>
                <li>Mengandung huruf besar (A-Z)</li>
                <li>Mengandung huruf kecil (a-z)</li>
                <li>Mengandung angka (0-9)</li>
                <li>Mengandung karakter khusus (@$!%*?&)</li>
            </ul>
        </div>

        <!-- Reset Password Form -->
        <form method="POST" action="<?= base_url('reset-password') ?>" id="resetForm">
            <?= csrf_field() ?>

            <!-- Hidden Token -->
            <input type="hidden" name="token" value="<?= esc($token ?? '') ?>">

            <!-- New Password -->
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-key me-1"></i>Password Baru
                </label>
                <div class="password-toggle">
                    <input
                        type="password"
                        class="form-control <?= (isset($validation) && $validation->hasError('password')) ? 'is-invalid' : '' ?>"
                        id="password"
                        name="password"
                        placeholder="Masukkan password baru"
                        required
                        autofocus
                    >
                    <i class="fas fa-eye toggle-icon" id="togglePassword"></i>
                    <?php if (isset($validation) && $validation->hasError('password')): ?>
                        <div class="invalid-feedback">
                            <?= $validation->getError('password') ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="password-strength">
                    <div class="password-strength-bar" id="strengthBar"></div>
                </div>
                <small class="text-muted" id="strengthText"></small>
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label for="password_confirm" class="form-label">
                    <i class="fas fa-check-double me-1"></i>Konfirmasi Password
                </label>
                <div class="password-toggle">
                    <input
                        type="password"
                        class="form-control <?= (isset($validation) && $validation->hasError('password_confirm')) ? 'is-invalid' : '' ?>"
                        id="password_confirm"
                        name="password_confirm"
                        placeholder="Ketik ulang password baru"
                        required
                    >
                    <i class="fas fa-eye toggle-icon" id="togglePasswordConfirm"></i>
                    <?php if (isset($validation) && $validation->hasError('password_confirm')): ?>
                        <div class="invalid-feedback">
                            <?= $validation->getError('password_confirm') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Reset Password
            </button>
        </form>

        <!-- Back to Login -->
        <div class="back-to-login">
            <a href="<?= base_url('login') ?>">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Login
            </a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('assets/neptune/plugins/jquery/jquery-3.5.1.min.js') ?>"></script>
    <script src="<?= base_url('assets/neptune/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

    <script>
        // Toggle Password Visibility
        $('#togglePassword').click(function() {
            const password = $('#password');
            const type = password.attr('type') === 'password' ? 'text' : 'password';
            password.attr('type', type);
            $(this).toggleClass('fa-eye fa-eye-slash');
        });

        $('#togglePasswordConfirm').click(function() {
            const password = $('#password_confirm');
            const type = password.attr('type') === 'password' ? 'text' : 'password';
            password.attr('type', type);
            $(this).toggleClass('fa-eye fa-eye-slash');
        });

        // Password Strength Checker
        $('#password').on('input', function() {
            const password = $(this).val();
            const strengthBar = $('#strengthBar');
            const strengthText = $('#strengthText');

            if (password.length === 0) {
                strengthBar.removeClass('weak medium strong').css('width', '0%');
                strengthText.text('');
                return;
            }

            let strength = 0;

            // Length check
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;

            // Character variety checks
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[@$!%*?&]/.test(password)) strength++;

            // Update strength bar
            strengthBar.removeClass('weak medium strong');

            if (strength <= 2) {
                strengthBar.addClass('weak');
                strengthText.text('Password lemah').css('color', '#dc3545');
            } else if (strength <= 4) {
                strengthBar.addClass('medium');
                strengthText.text('Password sedang').css('color', '#ffc107');
            } else {
                strengthBar.addClass('strong');
                strengthText.text('Password kuat').css('color', '#28a745');
            }
        });

        // Form validation
        $('#resetForm').on('submit', function(e) {
            const password = $('#password').val();
            const confirmPassword = $('#password_confirm').val();

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Password dan konfirmasi password tidak sama!');
                return false;
            }
        });
    </script>
</body>
</html>
