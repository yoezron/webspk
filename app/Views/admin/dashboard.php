<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper" data-bg-src="<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Dashboard Admin</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li>Dashboard Admin</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Admin Dashboard
==============================-->
<section class="space-top space-extra-bottom">
    <div class="container">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h4 class="text-white mb-2">Selamat Datang, <?= esc(session()->get('user_name')) ?>!</h4>
                        <p class="mb-0">Dashboard Admin - Serikat Pekerja Kampus</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-6 col-xl-3 mb-3">
                <div class="card border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Anggota Aktif</p>
                                <h3 class="mb-0"><?= number_format($stats['total_members']) ?></h3>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-users fa-3x opacity-50"></i>
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
                                <p class="text-muted mb-1">Calon Anggota</p>
                                <h3 class="mb-0"><?= number_format($stats['total_candidates']) ?></h3>
                            </div>
                            <div class="text-info">
                                <i class="fas fa-user-plus fa-3x opacity-50"></i>
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
                                <p class="text-muted mb-1">Menunggu Persetujuan</p>
                                <h3 class="mb-0"><?= number_format($stats['pending_approvals']) ?></h3>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-clock fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 mb-3">
                <div class="card border-start border-danger border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Akun Ditangguhkan</p>
                                <h3 class="mb-0"><?= number_format($stats['total_suspended']) ?></h3>
                            </div>
                            <div class="text-danger">
                                <i class="fas fa-ban fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row">
            <!-- Pending Approvals -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="fas fa-user-check me-2"></i> Menunggu Persetujuan</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($pending_approvals)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Universitas</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pending_approvals as $approval): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= esc($approval['full_name']) ?></strong>
                                                </td>
                                                <td><?= esc($approval['email']) ?></td>
                                                <td><?= esc($approval['university_name']) ?></td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?php
                                                        helper('app');
                                                        echo time_elapsed_string($approval['created_at']);
                                                        ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url('admin/members/review/' . $approval['id']) ?>"
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> Review
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-check-circle fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">Tidak ada pendaftaran yang menunggu persetujuan</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($pending_approvals)): ?>
                        <div class="card-footer text-center">
                            <a href="<?= base_url('admin/members/pending') ?>" class="btn btn-link">
                                Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Registrations -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i> Pendaftaran Terbaru</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($recent_registrations)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach (array_slice($recent_registrations, 0, 5) as $registration): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1"><?= esc($registration['full_name']) ?></h6>
                                                <small class="text-muted"><?= esc($registration['email']) ?></small>
                                            </div>
                                            <span class="<?= get_membership_status_badge($registration['membership_status']) ?>">
                                                <?= get_membership_status_label($registration['membership_status']) ?>
                                            </span>
                                        </div>
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-clock me-1"></i>
                                            <?php
                                            helper('app');
                                            echo time_elapsed_string($registration['created_at']);
                                            ?>
                                        </small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="p-4 text-center text-muted">
                                <i class="fas fa-user-plus fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">Belum ada pendaftaran</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($recent_registrations)): ?>
                        <div class="card-footer text-center">
                            <a href="<?= base_url('admin/members') ?>" class="btn btn-link">
                                Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i> Aksi Cepat</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <a href="<?= base_url('admin/members') ?>" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-users me-2"></i> Kelola Anggota
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="<?= base_url('admin/members/pending') ?>" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-user-check me-2"></i> Review Pendaftaran
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="<?= base_url('admin/dues') ?>" class="btn btn-outline-success w-100">
                                    <i class="fas fa-money-bill-wave me-2"></i> Kelola Iuran
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="<?= base_url('admin/reports') ?>" class="btn btn-outline-info w-100">
                                    <i class="fas fa-chart-bar me-2"></i> Laporan
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
