<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Statistik Wilayah - <?= esc($region['province_name']) ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/coordinators') ?>">Koordinator</a></li>
                    <li class="breadcrumb-item active">Statistik Wilayah</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/coordinators/stats') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Region Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fas fa-map-marker-alt me-2"></i>Informasi Wilayah</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <p class="mb-2"><strong>Kode Wilayah:</strong> <?= esc($region['region_code']) ?></p>
                            <p class="mb-2"><strong>Provinsi:</strong> <?= esc($region['province_name']) ?></p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-2"><strong>Koordinator:</strong>
                                <?php if (!empty($stats['coordinator'])): ?>
                                    <?= esc($stats['coordinator']['full_name']) ?>
                                    <br><small class="text-muted"><?= esc($stats['coordinator']['email']) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">Belum ada koordinator</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-2"><strong>Status:</strong>
                                <span class="badge <?= $region['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $region['is_active'] ? 'Aktif' : 'Tidak Aktif' ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Anggota</p>
                            <h3 class="mb-0"><?= number_format($stats['total_members']) ?></h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Anggota Aktif</p>
                            <h3 class="mb-0"><?= number_format($stats['active_members']) ?></h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-user-check fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Calon Anggota</p>
                            <h3 class="mb-0"><?= number_format($stats['candidates']) ?></h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-user-plus fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Iuran Terkumpul</p>
                            <h5 class="mb-0">Rp <?= number_format($stats['total_collected'], 0, ',', '.') ?></h5>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Collection -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-calendar-alt me-2"></i>Pembayaran Bulan Ini (<?= date('F Y') ?>)
                    </h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="text-success mb-0">Rp <?= number_format($stats['monthly_collection'], 0, ',', '.') ?></h3>
                                    <p class="text-muted mb-0">Total pembayaran iuran bulan ini</p>
                                </div>
                                <div>
                                    <i class="fas fa-chart-line fa-3x text-success opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Members List Preview -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Anggota di Wilayah Ini</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Untuk melihat daftar lengkap anggota di wilayah ini, silakan kunjungi halaman
                        <a href="<?= base_url('admin/members?region=' . urlencode($region['region_code'])) ?>">Manajemen Anggota</a>
                        dan gunakan filter wilayah.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
