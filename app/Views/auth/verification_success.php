<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Verifikasi Berhasil' ?> - SPK</title>
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
                            <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                <i class="fas fa-check-circle text-white" style="font-size: 60px;"></i>
                            </div>
                        </div>

                        <h1 class="mb-3">Email Berhasil Diverifikasi!</h1>

                        <p class="lead mb-4">
                            Selamat, email Anda telah berhasil diverifikasi.
                        </p>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Langkah Selanjutnya:</strong> Silakan lengkapi data keanggotaan Anda untuk melanjutkan proses pendaftaran.
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <a href="<?= base_url('registrasi/step-2') ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-arrow-right me-2"></i>
                                Lanjutkan Pendaftaran
                            </a>
                            <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary">
                                Kembali ke Beranda
                            </a>
                        </div>

                        <hr class="my-4">

                        <p class="text-muted small">
                            Jika Anda mengalami kesulitan, silakan hubungi kami melalui email atau telepon yang tertera di website.
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
