<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper" data-bg-src="<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Dashboard Member</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li>Dashboard</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Member Dashboard
==============================-->
<section class="space-top space-extra-bottom">
    <div class="container">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-white mb-2">Selamat Datang, <?= esc($member['full_name']) ?>!</h4>
                                <p class="mb-0">
                                    Nomor Anggota: <strong><?= esc($member_info['member_number']) ?></strong> |
                                    Status: <span class="badge bg-light text-dark">
                                        <?php helper('app'); echo get_membership_status_label($member_info['membership_status']); ?>
                                    </span>
                                </p>
                            </div>
                            <div class="d-none d-md-block">
                                <?php if (!empty($member['profile_photo'])): ?>
                                    <img src="<?= get_file_url($member['profile_photo'], 'uploads/photos/') ?>"
                                         alt="Profile" class="rounded-circle" width="80" height="80"
                                         style="object-fit: cover; border: 3px solid white;">
                                <?php else: ?>
                                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center"
                                         style="width: 80px; height: 80px; font-size: 32px; font-weight: bold;">
                                        <?php helper('app'); echo get_initials($member['full_name']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="row mb-4">
            <div class="col-md-6 col-xl-3 mb-3">
                <div class="card border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Iuran Bulanan</p>
                                <h4 class="mb-0">
                                    <?php helper('app'); echo format_currency($member_info['monthly_dues']); ?>
                                </h4>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-money-bill-wave fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 mb-3">
                <div class="card border-start <?= $member_info['total_arrears'] > 0 ? 'border-danger' : 'border-success' ?> border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Tunggakan</p>
                                <h4 class="mb-0 <?= $member_info['total_arrears'] > 0 ? 'text-danger' : 'text-success' ?>">
                                    <?php helper('app'); echo format_currency($member_info['total_arrears']); ?>
                                </h4>
                            </div>
                            <div class="<?= $member_info['total_arrears'] > 0 ? 'text-danger' : 'text-success' ?>">
                                <i class="fas <?= $member_info['total_arrears'] > 0 ? 'fa-exclamation-triangle' : 'fa-check-circle' ?> fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 mb-3">
                <div class="card border-start border-warning border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Bulan Tertunggak</p>
                                <h4 class="mb-0"><?= $member_info['arrears_months'] ?> Bulan</h4>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-calendar-times fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 mb-3">
                <div class="card border-start border-info border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Pembayaran Terakhir</p>
                                <h6 class="mb-0">
                                    <?php
                                    if ($member_info['last_dues_payment']) {
                                        helper('app');
                                        echo format_date_indonesia($member_info['last_dues_payment'], 'short');
                                    } else {
                                        echo 'Belum ada';
                                    }
                                    ?>
                                </h6>
                            </div>
                            <div class="text-info">
                                <i class="fas fa-receipt fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row">
            <!-- Profile Summary -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i> Profil Saya</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <?php if (!empty($member['profile_photo'])): ?>
                                <img src="<?= get_file_url($member['profile_photo'], 'uploads/photos/') ?>"
                                     alt="Profile" class="rounded-circle mb-3" width="120" height="120"
                                     style="object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                                     style="width: 120px; height: 120px; font-size: 48px; font-weight: bold;">
                                    <?php helper('app'); echo get_initials($member['full_name']); ?>
                                </div>
                            <?php endif; ?>
                            <h5 class="mb-1"><?= esc($member['full_name']) ?></h5>
                            <p class="text-muted mb-0"><?php helper('app'); echo get_user_role_label($member['role']); ?></p>
                        </div>
                        <hr>
                        <div class="mb-2">
                            <small class="text-muted">Email</small>
                            <p class="mb-0"><?= esc($member['email']) ?></p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Nomor HP</small>
                            <p class="mb-0"><?= esc($member['phone_number']) ?></p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Universitas</small>
                            <p class="mb-0"><?= esc($member['university_name']) ?></p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Fakultas</small>
                            <p class="mb-0"><?= esc($member['faculty'] ?? '-') ?></p>
                        </div>
                        <hr>
                        <a href="<?= base_url('member/profile') ?>" class="btn btn-primary w-100">
                            <i class="fas fa-edit me-2"></i> Edit Profil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Dues & Payments -->
            <div class="col-lg-8 mb-4">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i> Informasi Iuran</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($member_info['total_arrears'] > 0): ?>
                            <div class="alert alert-warning">
                                <h6 class="alert-heading">
                                    <i class="fas fa-exclamation-triangle me-2"></i> Anda Memiliki Tunggakan
                                </h6>
                                <p class="mb-2">
                                    Total tunggakan: <strong><?php helper('app'); echo format_currency($member_info['total_arrears']); ?></strong>
                                    untuk <?= $member_info['arrears_months'] ?> bulan
                                </p>
                                <hr>
                                <p class="mb-0">
                                    Silakan lakukan pembayaran untuk menghindari penangguhan keanggotaan.
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Status Iuran Baik</strong> - Anda tidak memiliki tunggakan.
                            </div>
                        <?php endif; ?>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <small class="text-muted">Iuran Bulanan</small>
                                        <h4 class="mb-0"><?php helper('app'); echo format_currency($member_info['monthly_dues']); ?></h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <small class="text-muted">Pembayaran Terakhir</small>
                                        <h6 class="mb-0">
                                            <?php
                                            if ($member_info['last_dues_payment']) {
                                                helper('app');
                                                echo format_date_indonesia($member_info['last_dues_payment'], 'short');
                                            } else {
                                                echo 'Belum ada pembayaran';
                                            }
                                            ?>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <a href="<?= base_url('member/dues/pay') ?>" class="btn btn-success">
                                <i class="fas fa-credit-card me-2"></i> Bayar Iuran
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i> Menu Cepat</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <a href="<?= base_url('member/profile') ?>" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-user me-2"></i> Edit Profil
                                </a>
                            </div>
                            <div class="col-md-6 mb-2">
                                <a href="<?= base_url('member/dues/history') ?>" class="btn btn-outline-success w-100">
                                    <i class="fas fa-history me-2"></i> Riwayat Pembayaran
                                </a>
                            </div>
                            <div class="col-md-6 mb-2">
                                <a href="<?= base_url('dokumen') ?>" class="btn btn-outline-info w-100">
                                    <i class="fas fa-file-alt me-2"></i> Dokumen & AD/ART
                                </a>
                            </div>
                            <div class="col-md-6 mb-2">
                                <a href="<?= base_url('kontak') ?>" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-phone me-2"></i> Hubungi Admin
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
