<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Verifikasi Gagal' ?> - SPK</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/fontawesome.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                <i class="fas fa-times-circle text-white" style="font-size: 60px;"></i>
                            </div>
                        </div>

                        <h1 class="mb-3">Verifikasi Gagal</h1>

                        <p class="lead mb-4">
                            <?= esc($message ?? 'Token verifikasi tidak valid atau sudah kadaluarsa.') ?>
                        </p>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Solusi:</strong> Anda dapat meminta link verifikasi baru dengan mengisi form di bawah ini.
                        </div>

                        <form action="<?= base_url('email-verification/resend') ?>" method="POST" class="mt-4">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Anda</label>
                                <input type="email" class="form-control form-control-lg" id="email" name="email" required placeholder="nama@email.com">
                                <div class="form-text">Masukkan email yang Anda gunakan saat mendaftar</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-envelope me-2"></i>
                                    Kirim Link Verifikasi Baru
                                </button>
                                <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary">
                                    Kembali ke Beranda
                                </a>
                            </div>
                        </form>

                        <hr class="my-4">

                        <p class="text-muted small">
                            Jika masalah berlanjut, silakan hubungi tim support kami.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/vendor/jquery-3.6.0.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
</body>
</html>
