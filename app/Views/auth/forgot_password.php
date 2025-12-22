<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= esc($description ?? 'Lupa Password - Serikat Pekerja Kampus') ?>">
    <?= csrf_meta() ?>

    <title><?= esc($title ?? 'Lupa Password') ?> - Serikat Pekerja Kampus</title>

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

        .info-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }

        .info-box i {
            color: #667eea;
            margin-right: 10px;
        }

        .info-box p {
            margin: 0;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <!-- Logo & Title -->
        <div class="auth-logo">
            <i class="fas fa-key"></i>
            <h3>Lupa Password?</h3>
            <p>Masukkan email Anda untuk reset password</p>
        </div>

        <!-- Flash Messages -->
        <?php if (session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Info Box -->
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <p>Kami akan mengirimkan link reset password ke email Anda.</p>
        </div>

        <!-- Forgot Password Form -->
        <form method="POST" action="<?= base_url('forgot-password') ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope me-1"></i>Email
                </label>
                <input
                    type="email"
                    class="form-control <?= (isset($validation) && $validation->hasError('email')) ? 'is-invalid' : '' ?>"
                    id="email"
                    name="email"
                    placeholder="nama@email.com"
                    value="<?= old('email') ?>"
                    required
                    autofocus
                >
                <?php if (isset($validation) && $validation->hasError('email')): ?>
                    <div class="invalid-feedback">
                        <?= $validation->getError('email') ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Kirim Link Reset Password
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
</body>
</html>
