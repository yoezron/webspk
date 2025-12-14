<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Verifikasi Email' ?> - SPK</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/logo/favicon.png') ?>">
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
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                <i class="fas fa-envelope text-white" style="font-size: 50px;"></i>
                            </div>
                        </div>

                        <h1 class="mb-3">Cek Email Anda</h1>

                        <p class="lead mb-4">
                            Kami telah mengirimkan link verifikasi ke email Anda:
                        </p>

                        <div class="alert alert-info">
                            <strong><?= esc($email ?? session()->get('registration_email') ?? 'email Anda') ?></strong>
                        </div>

                        <div class="text-start mt-4 mb-4">
                            <h5>Langkah Selanjutnya:</h5>
                            <ol>
                                <li>Buka inbox email Anda</li>
                                <li>Cari email dari <strong>Serikat Pekerja Kampus</strong></li>
                                <li>Klik link verifikasi di dalam email</li>
                                <li>Anda akan diarahkan untuk melanjutkan pendaftaran</li>
                            </ol>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Catatan:</strong> Link verifikasi akan kadaluarsa dalam <strong>24 jam</strong>.
                            Jika tidak menemukan email, periksa folder spam/junk Anda.
                        </div>

                        <hr class="my-4">

                        <p class="text-muted mb-3">
                            Tidak menerima email?
                        </p>

                        <form action="<?= base_url('email-verification/resend') ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="email" value="<?= esc($email ?? '') ?>">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-redo me-2"></i>
                                    Kirim Ulang Email Verifikasi
                                </button>
                                <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary">
                                    Kembali ke Beranda
                                </a>
                            </div>
                        </form>

                        <hr class="my-4">

                        <p class="text-muted small">
                            Butuh bantuan? Hubungi kami di <?= getenv('email.fromEmail') ?>
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
