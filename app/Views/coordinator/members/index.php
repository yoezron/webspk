<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Daftar Anggota Wilayah</h1>
            <span>Kelola anggota dalam wilayah yang Anda koordinir</span>
        </div>
    </div>
</div>

<?php if (isset($message)): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning">
                <i class="material-icons-outlined">info</i>
                <?= esc($message) ?>
            </div>
        </div>
    </div>
<?php else: ?>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-primary">
                        <i class="material-icons-outlined">people</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Total Anggota</span>
                        <span class="widget-stats-amount"><?= number_format($stats['total']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-success">
                        <i class="material-icons-outlined">check_circle</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Anggota Aktif</span>
                        <span class="widget-stats-amount"><?= number_format($stats['active']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-warning">
                        <i class="material-icons-outlined">hourglass_empty</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Kandidat</span>
                        <span class="widget-stats-amount"><?= number_format($stats['candidates']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-danger">
                        <i class="material-icons-outlined">block</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Ditangguhkan</span>
                        <span class="widget-stats-amount"><?= number_format($stats['suspended']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assigned Regions Info -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="material-icons-outlined">map</i>
                    Wilayah Koordinasi Anda
                </h6>
                <div class="mt-2">
                    <?php foreach ($assigned_regions as $region): ?>
                        <span class="badge badge-primary me-1">
                            <?= esc($region['region_code']) ?> - <?= esc($region['province_name']) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Member List -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="material-icons-outlined">list</i>
                    Daftar Anggota
                </h5>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="get" action="<?= base_url('coordinator/members') ?>" class="mb-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Cari nama, email, atau nomor anggota..."
                                   value="<?= esc($filters['search'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Aktif</option>
                                <option value="candidate" <?= ($filters['status'] ?? '') === 'candidate' ? 'selected' : '' ?>>Kandidat</option>
                                <option value="suspended" <?= ($filters['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Ditangguhkan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="region" class="form-select">
                                <option value="">Semua Wilayah</option>
                                <?php foreach ($assigned_regions as $region): ?>
                                    <option value="<?= esc($region['region_code']) ?>"
                                            <?= ($filters['region'] ?? '') === $region['region_code'] ? 'selected' : '' ?>>
                                        <?= esc($region['province_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="material-icons-outlined">search</i> Filter
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Member Table -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="material-icons-outlined">check_circle</i>
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No. Anggota</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Wilayah</th>
                                <th>Status</th>
                                <th>Bergabung</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($members)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data anggota</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($members as $member): ?>
                                    <tr>
                                        <td><strong><?= esc($member['member_number'] ?? '-') ?></strong></td>
                                        <td><?= esc($member['full_name']) ?></td>
                                        <td><?= esc($member['email']) ?></td>
                                        <td>
                                            <span class="badge badge-light">
                                                <?= esc($member['province_name'] ?? $member['region_code']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusBadge = match($member['membership_status']) {
                                                'active' => 'badge-success',
                                                'candidate' => 'badge-warning',
                                                'suspended' => 'badge-danger',
                                                default => 'badge-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $statusBadge ?>">
                                                <?= ucfirst($member['membership_status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d M Y', strtotime($member['created_at'])) ?></td>
                                        <td>
                                            <a href="<?= base_url('coordinator/members/view/' . $member['id']) ?>"
                                               class="btn btn-sm btn-primary">
                                                <i class="material-icons-outlined">visibility</i> Lihat
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($pager): ?>
                    <div class="mt-3">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?= $this->endSection() ?>
