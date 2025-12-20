<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
Manajemen Koordinator Wilayah
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Manajemen Koordinator Wilayah</h1>
            <span>Assign koordinator ke wilayah dan monitor performa regional</span>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row">
    <div class="col-md-4">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-primary">
                        <i class="material-icons-outlined">supervisor_account</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Total Koordinator</span>
                        <span class="widget-stats-amount"><?= count($coordinators) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-success">
                        <i class="material-icons-outlined">place</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Total Wilayah</span>
                        <span class="widget-stats-amount"><?= count($regions) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-info">
                        <i class="material-icons-outlined">check_circle</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Wilayah Ter-assign</span>
                        <span class="widget-stats-amount"><?= count(array_filter($regions, fn($r) => !empty($r['coordinator']))) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Coordinators Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Daftar Koordinator</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No. Anggota</th>
                                <th>Wilayah Assigned</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($coordinators)): ?>
                                <?php foreach ($coordinators as $coord): ?>
                                <tr>
                                    <td><?= esc($coord['full_name']) ?></td>
                                    <td><?= esc($coord['email']) ?></td>
                                    <td><?= esc($coord['member_number']) ?></td>
                                    <td>
                                        <?php if ($coord['region_count'] > 0): ?>
                                            <span class="badge badge-primary"><?= $coord['region_count'] ?> wilayah</span>
                                            <?php foreach (array_slice($coord['regions'], 0, 2) as $region): ?>
                                                <small class="text-muted"><?= esc($region['province_name']) ?>, </small>
                                            <?php endforeach; ?>
                                            <?php if ($coord['region_count'] > 2): ?>
                                                <small class="text-muted">+<?= $coord['region_count'] - 2 ?> lainnya</small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">Belum di-assign</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/coordinators/assign/' . $coord['id']) ?>" class="btn btn-sm btn-primary">
                                            <i class="material-icons-outlined">assignment</i> Assign Wilayah
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada koordinator</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Regional Overview -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Overview Wilayah</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Provinsi</th>
                                <th>Kode</th>
                                <th>Total Anggota</th>
                                <th>Koordinator</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($regions as $region): ?>
                            <tr>
                                <td><?= esc($region['province_name']) ?></td>
                                <td><span class="badge badge-secondary"><?= esc($region['region_code']) ?></span></td>
                                <td><?= number_format($region['member_count']) ?></td>
                                <td>
                                    <?php if (!empty($region['coordinator'])): ?>
                                        <span class="badge badge-success">
                                            <?= esc($region['coordinator']['coordinator_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/coordinators/stats?region=' . $region['region_code']) ?>" class="btn btn-sm btn-info">
                                        <i class="material-icons-outlined">analytics</i> Statistik
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
