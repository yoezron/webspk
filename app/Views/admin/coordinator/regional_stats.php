<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Statistik Regional</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/coordinators') ?>">Koordinator</a></li>
                    <li class="breadcrumb-item active">Statistik Regional</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/coordinators') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Regional Statistics Table -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Statistik Per Wilayah</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Wilayah</th>
                            <th>Provinsi</th>
                            <th>Koordinator</th>
                            <th>Total Anggota</th>
                            <th>Anggota Aktif</th>
                            <th>Kandidat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($regions)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Tidak ada data wilayah
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($regions as $region): ?>
                                <tr>
                                    <td><strong><?= esc($region['region_code']) ?></strong></td>
                                    <td><?= esc($region['province_name']) ?></td>
                                    <td>
                                        <?php if (!empty($region['coordinator_name'])): ?>
                                            <div>
                                                <strong><?= esc($region['coordinator_name']) ?></strong><br>
                                                <small class="text-muted"><?= esc($region['coordinator_email'] ?? '') ?></small>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted"><i>Belum ada koordinator</i></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?= number_format($region['total_members'] ?? 0) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <?= number_format($region['active_members'] ?? 0) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= number_format($region['candidates'] ?? 0) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= ($region['is_active'] ?? true) ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= ($region['is_active'] ?? true) ? 'Aktif' : 'Tidak Aktif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/coordinators/stats?region=' . urlencode($region['region_code'])) ?>"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Lihat Detail">
                                            <i class="fas fa-chart-line"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($regions)): ?>
                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3 class="mb-0"><?= count($regions) ?></h3>
                                    <small class="text-muted">Total Wilayah</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">
                                        <?php
                                        $withCoordinator = array_filter($regions, fn($r) => !empty($r['coordinator_name']));
                                        echo count($withCoordinator);
                                        ?>
                                    </h3>
                                    <small class="text-muted">Wilayah dengan Koordinator</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">
                                        <?php
                                        $totalMembers = array_sum(array_column($regions, 'total_members'));
                                        echo number_format($totalMembers);
                                        ?>
                                    </h3>
                                    <small class="text-muted">Total Semua Anggota</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">
                                        <?php
                                        $activeMembers = array_sum(array_column($regions, 'active_members'));
                                        echo number_format($activeMembers);
                                        ?>
                                    </h3>
                                    <small class="text-muted">Total Anggota Aktif</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Help Text -->
    <div class="alert alert-info mt-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Petunjuk:</strong> Klik tombol "Detail" untuk melihat statistik lengkap dari masing-masing wilayah.
        Anda dapat mengelola penugasan koordinator di halaman <a href="<?= base_url('admin/coordinators') ?>" class="alert-link">Manajemen Koordinator</a>.
    </div>
</div>

<?= $this->endSection() ?>
