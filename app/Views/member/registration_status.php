<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper" data-bg-src="<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Status Pendaftaran</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li>Status Pendaftaran</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Registration Status
==============================-->
<section class="space-top space-extra-bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-8 col-xl-9 col-lg-10">
                <div class="th-login-form">
                    <div class="form-title text-center mb-4">
                        <h2 class="sec-title mb-2">Status Pendaftaran Anda</h2>
                        <p class="mb-0">Pantau progres pendaftaran keanggotaan Anda</p>
                    </div>

                    <?php if (session()->has('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= session('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('info')): ?>
                        <div class="alert alert-info alert-dismissible fade show">
                            <i class="fas fa-info-circle me-2"></i>
                            <?= session('info') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Member Info -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title border-bottom pb-2 mb-3">Informasi Dasar</h5>
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

                    <!-- Registration Progress -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title border-bottom pb-2 mb-4">Tahapan Pendaftaran</h5>

                            <div class="registration-timeline">
                                <!-- Step 1: Registered -->
                                <div class="timeline-item completed">
                                    <div class="timeline-marker">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Pendaftaran Dibuat</h6>
                                        <p class="mb-0 small text-muted">
                                            Akun berhasil dibuat pada <?= date('d M Y H:i', strtotime($member['created_at'])) ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Step 2: Payment Submitted -->
                                <div class="timeline-item <?= in_array($member['onboarding_state'], ['payment_submitted', 'email_verified', 'approved']) ? 'completed' : 'pending' ?>">
                                    <div class="timeline-marker">
                                        <?php if (in_array($member['onboarding_state'], ['payment_submitted', 'email_verified', 'approved'])): ?>
                                            <i class="fas fa-check"></i>
                                        <?php else: ?>
                                            <i class="fas fa-clock"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Bukti Pembayaran Diunggah</h6>
                                        <p class="mb-0 small text-muted">
                                            <?php if ($member['registration_payment_date']): ?>
                                                Diunggah pada <?= date('d M Y H:i', strtotime($member['registration_payment_date'])) ?>
                                            <?php else: ?>
                                                Menunggu upload bukti pembayaran
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Step 3: Email Verified -->
                                <div class="timeline-item <?= in_array($member['onboarding_state'], ['email_verified', 'approved']) ? 'completed' : 'pending' ?>">
                                    <div class="timeline-marker">
                                        <?php if (in_array($member['onboarding_state'], ['email_verified', 'approved'])): ?>
                                            <i class="fas fa-check"></i>
                                        <?php else: ?>
                                            <i class="fas fa-clock"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Email Terverifikasi</h6>
                                        <p class="mb-0 small text-muted">
                                            <?php if ($member['email_verified_at']): ?>
                                                Terverifikasi pada <?= date('d M Y H:i', strtotime($member['email_verified_at'])) ?>
                                            <?php else: ?>
                                                Menunggu verifikasi email
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Step 4: Admin Approval -->
                                <div class="timeline-item <?= $member['onboarding_state'] === 'approved' ? 'completed' : 'pending' ?>">
                                    <div class="timeline-marker">
                                        <?php if ($member['onboarding_state'] === 'approved'): ?>
                                            <i class="fas fa-check"></i>
                                        <?php else: ?>
                                            <i class="fas fa-clock"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Persetujuan Admin</h6>
                                        <p class="mb-0 small text-muted">
                                            <?php if ($member['approval_date']): ?>
                                                Disetujui pada <?= date('d M Y H:i', strtotime($member['approval_date'])) ?>
                                            <?php else: ?>
                                                Menunggu persetujuan dari admin
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Status -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title border-bottom pb-2 mb-3">Status Saat Ini</h5>

                            <?php if ($member['onboarding_state'] === 'registered'): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Menunggu Pembayaran</strong><br>
                                    Silakan lengkapi pembayaran dan upload bukti transfer untuk melanjutkan proses pendaftaran.
                                </div>
                            <?php elseif ($member['onboarding_state'] === 'payment_submitted'): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-clock me-2"></i>
                                    <strong>Menunggu Verifikasi Email</strong><br>
                                    Silakan cek email Anda dan klik link verifikasi. Jangan lupa cek folder spam.
                                </div>
                            <?php elseif ($member['onboarding_state'] === 'email_verified'): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-user-check me-2"></i>
                                    <strong>Menunggu Persetujuan Admin</strong><br>
                                    Pembayaran dan email Anda sudah terverifikasi. Tim admin sedang memproses persetujuan (maksimal 1x24 jam).
                                </div>
                            <?php elseif ($member['onboarding_state'] === 'approved'): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Pendaftaran Disetujui!</strong><br>
                                    Selamat! Anda sekarang adalah anggota resmi Serikat Pekerja Kampus.
                                    <?php if ($member['member_number']): ?>
                                        <br>Nomor Anggota: <strong><?= esc($member['member_number']) ?></strong>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($member['onboarding_state'] === 'rejected'): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-times-circle me-2"></i>
                                    <strong>Pendaftaran Ditolak</strong><br>
                                    <?php if ($member['rejection_reason']): ?>
                                        Alasan: <?= esc($member['rejection_reason']) ?>
                                    <?php endif; ?>
                                    <br>Silakan hubungi admin untuk informasi lebih lanjut.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="text-center">
                        <a href="<?= base_url('/') ?>" class="th-btn style3">
                            <i class="fa-solid fa-home me-2"></i> Kembali ke Beranda
                        </a>
                        <a href="<?= base_url('logout') ?>" class="th-btn">
                            <i class="fa-solid fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.registration-timeline {
    position: relative;
    padding-left: 50px;
}

.registration-timeline::before {
    content: '';
    position: absolute;
    left: 19px;
    top: 10px;
    bottom: 10px;
    width: 2px;
    background: #e0e0e0;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -50px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    z-index: 1;
}

.timeline-item.completed .timeline-marker {
    background: #28a745;
    border-color: #28a745;
    color: #fff;
}

.timeline-item.pending .timeline-marker {
    background: #fff;
    border-color: #ffc107;
    color: #ffc107;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-item.completed .timeline-content h6 {
    color: #28a745;
}

.timeline-item.pending .timeline-content h6 {
    color: #999;
}
</style>

<?= $this->endSection() ?>
