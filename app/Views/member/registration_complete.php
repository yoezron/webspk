<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper" data-bg-src="<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Lengkapi Pendaftaran</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li>Lengkapi Pendaftaran</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Complete Registration
==============================-->
<section class="space-top space-extra-bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-8 col-xl-9 col-lg-10">
                <div class="th-login-form">
                    <div class="form-title text-center mb-4">
                        <h2 class="sec-title mb-2">Lengkapi Data Pendaftaran</h2>
                        <p class="mb-0">Silakan lengkapi data Anda untuk melanjutkan proses pendaftaran</p>
                    </div>

                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Informasi:</strong> Proses pendaftaran Anda belum lengkap. Silakan klik tombol di bawah untuk melengkapi data dan upload dokumen yang diperlukan.
                    </div>

                    <!-- Member Basic Info -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title border-bottom pb-2 mb-3">Data Dasar Anda</h5>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Nama Lengkap:</strong><br>
                                    <?= esc($member['full_name']) ?>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Email:</strong><br>
                                    <?= esc($member['email']) ?>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Nomor HP:</strong><br>
                                    <?= esc($member['phone_number']) ?>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Universitas:</strong><br>
                                    <?= esc($member['university_name']) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- What needs to be completed -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0"><i class="fas fa-tasks me-2"></i> Yang Perlu Dilengkapi</h5>
                        </div>
                        <div class="card-body">
                            <ul class="mb-0">
                                <li>Data pribadi lengkap (alamat, data demografis)</li>
                                <li>Data pekerjaan dan kepegawaian</li>
                                <li>Upload bukti pembayaran pendaftaran</li>
                                <li>Upload dokumen pendukung (KTP, dll)</li>
                                <li>Persetujuan AD/ART dan Kebijakan Privasi</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-center">
                        <a href="<?= base_url('registrasi/step-2') ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-edit me-2"></i> Lengkapi Data Sekarang
                        </a>
                        <a href="<?= base_url('logout') ?>" class="btn btn-outline-secondary btn-lg ms-2">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </div>

                    <div class="alert alert-light mt-4">
                        <i class="fas fa-question-circle me-2"></i>
                        <strong>Butuh bantuan?</strong> Hubungi kami di <a href="mailto:<?= esc($contact_email ?? 'info@spk.local') ?>"><?= esc($contact_email ?? 'info@spk.local') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
