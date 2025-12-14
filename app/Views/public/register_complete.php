<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper" data-bg-src="<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Pendaftaran Selesai</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li>Pendaftaran Selesai</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Registration Complete
==============================-->
<section class="space-top space-extra-bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-8 col-xl-9 col-lg-10">
                <div class="th-login-form text-center">
                    <!-- Success Icon -->
                    <div class="success-icon mb-4">
                        <div class="success-circle">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="form-title mb-4">
                        <h2 class="sec-title mb-3">Pendaftaran Berhasil!</h2>
                        <p class="lead">Terima kasih telah mendaftar sebagai calon anggota Serikat Pekerja Kampus</p>
                    </div>

                    <!-- Success Message -->
                    <div class="alert alert-success text-start mb-4">
                        <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i> Informasi Penting</h5>
                        <ol class="mb-0">
                            <li class="mb-2">Email konfirmasi telah dikirim ke alamat email Anda</li>
                            <li class="mb-2">Silakan cek email dan klik link verifikasi (cek folder spam jika tidak menemukannya)</li>
                            <li class="mb-2">Tim admin akan memverifikasi pembayaran dan dokumen Anda dalam waktu <strong>maksimal 1x24 jam</strong></li>
                            <li class="mb-2">Anda akan menerima notifikasi email setelah pendaftaran disetujui</li>
                            <li class="mb-0">Setelah disetujui, Anda dapat login menggunakan email dan password yang telah didaftarkan</li>
                        </ol>
                    </div>

                    <!-- Next Steps -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="feature-icon mb-3">
                                        <i class="fas fa-envelope fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">Verifikasi Email</h5>
                                    <p class="card-text small">Cek email Anda dan klik link verifikasi</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="feature-icon mb-3">
                                        <i class="fas fa-user-check fa-3x text-warning"></i>
                                    </div>
                                    <h5 class="card-title">Verifikasi Admin</h5>
                                    <p class="card-text small">Tunggu verifikasi dari tim admin (maks. 1x24 jam)</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <div class="feature-icon mb-3">
                                        <i class="fas fa-sign-in-alt fa-3x text-success"></i>
                                    </div>
                                    <h5 class="card-title">Akun Aktif</h5>
                                    <p class="card-text small">Login dan nikmati seluruh fitur member</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="card bg-light mb-4 text-start">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-question-circle me-2"></i> Butuh Bantuan?</h6>
                            <p class="mb-2">Jika Anda memiliki pertanyaan atau mengalami kendala, silakan hubungi kami:</p>
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-envelope me-2 text-primary"></i> Email: <a href="mailto:<?= esc($contact_email ?? 'info@spk.local') ?>"><?= esc($contact_email ?? 'info@spk.local') ?></a></li>
                                <li><i class="fas fa-phone me-2 text-primary"></i> Telepon: <a href="tel:<?= esc($contact_phone ?? '+62123456789') ?>"><?= esc($contact_phone ?? '+62 123 456 789') ?></a></li>
                                <li><i class="fas fa-clock me-2 text-primary"></i> Jam Operasional: Senin - Jumat, 08:00 - 16:00 WIB</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="btn-group mt-4">
                        <a href="<?= base_url('/') ?>" class="th-btn style3 me-3">
                            <i class="fa-solid fa-home me-2"></i> Kembali ke Beranda
                        </a>
                        <a href="<?= base_url('login') ?>" class="th-btn">
                            <i class="fa-solid fa-sign-in-alt me-2"></i> Login
                        </a>
                    </div>

                    <!-- Status Check -->
                    <div class="mt-4">
                        <p class="text-muted small mb-0">
                            Sudah verifikasi email?
                            <a href="<?= base_url('login') ?>" class="text-primary">Login untuk cek status pendaftaran Anda</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.success-icon {
    display: flex;
    justify-content: center;
    align-items: center;
}

.success-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    animation: scaleIn 0.5s ease-out;
    box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
}

.success-circle i {
    font-size: 48px;
    color: white;
}

@keyframes scaleIn {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.feature-icon {
    opacity: 0.8;
    transition: all 0.3s ease;
}

.card:hover .feature-icon {
    opacity: 1;
    transform: translateY(-5px);
}
</style>

<?= $this->endSection() ?>
