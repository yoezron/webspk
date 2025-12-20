<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
Dashboard Koordinator Wilayah
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/neptune/plugins/apexcharts/apexcharts.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Dashboard Koordinator Wilayah</h1>
            <span>Kelola anggota di wilayah Anda - <?= esc(session()->get('user_name')) ?></span>
        </div>
    </div>
</div>

<?php if (isset($no_regions) && $no_regions): ?>
    <!-- No Regions Assigned Message -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning">
                <h4 class="alert-heading"><i class="material-icons-outlined">warning</i> Tidak Ada Wilayah Assigned</h4>
                <p>Anda belum di-assign ke wilayah manapun. Silakan hubungi Super Admin untuk assignment wilayah.</p>
            </div>
        </div>
    </div>
<?php else: ?>

<!-- Assigned Regions Info -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="text-white mb-2">
                    <i class="material-icons-outlined">location_on</i>
                    Wilayah Yang Anda Kelola
                </h5>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($assigned_regions as $region): ?>
                        <span class="badge bg-white text-primary px-3 py-2">
                            <i class="material-icons-outlined" style="font-size: 16px;">place</i>
                            <?= esc($region['province_name']) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Widgets Row 1 - Members -->
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
                        <span class="widget-stats-amount"><?= number_format($stats['total_members']) ?></span>
                        <span class="widget-stats-info">Di wilayah Anda</span>
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
                        <i class="material-icons-outlined">verified</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Anggota Aktif</span>
                        <span class="widget-stats-amount"><?= number_format($stats['active_members']) ?></span>
                        <span class="widget-stats-info">Terverifikasi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-info">
                        <i class="material-icons-outlined">person_add</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Calon Anggota</span>
                        <span class="widget-stats-amount"><?= number_format($stats['candidates']) ?></span>
                        <span class="widget-stats-info">Dalam proses</span>
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
                        <i class="material-icons-outlined">pending</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Pending Approval</span>
                        <span class="widget-stats-amount"><?= number_format($stats['pending_approvals']) ?></span>
                        <span class="widget-stats-info">Perlu tindakan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Widgets Row 2 - Financial -->
<div class="row">
    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-success">
                        <i class="material-icons-outlined">paid</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Total Terkumpul</span>
                        <span class="widget-stats-amount">Rp <?= number_format($stats['total_collected'], 0, ',', '.') ?></span>
                        <span class="widget-stats-info">Semua waktu</span>
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
                        <span class="widget-stats-title">Pending Verifikasi</span>
                        <span class="widget-stats-amount"><?= number_format($stats['pending_payments']) ?></span>
                        <span class="widget-stats-info">Pembayaran</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-primary">
                        <i class="material-icons-outlined">calendar_month</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Bulan Ini</span>
                        <span class="widget-stats-amount">Rp <?= number_format($stats['monthly_collection'], 0, ',', '.') ?></span>
                        <span class="widget-stats-info"><?= date('F Y') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-info">
                        <i class="material-icons-outlined">receipt</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Total Pembayaran</span>
                        <span class="widget-stats-amount"><?= number_format($payment_stats['total_payments']) ?></span>
                        <span class="widget-stats-info">Terverifikasi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Pertumbuhan Anggota Regional (6 Bulan Terakhir)</h5>
            </div>
            <div class="card-body">
                <div id="memberGrowthChart" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Distribusi Anggota Per Wilayah</h5>
            </div>
            <div class="card-body">
                <div class="info-list">
                    <?php foreach ($members_by_region as $region): ?>
                        <div class="info-list-item">
                            <span class="info-list-item-icon bg-primary text-white">
                                <i class="material-icons-outlined">place</i>
                            </span>
                            <div class="info-list-item-content">
                                <p class="info-list-item-title"><?= esc($region['province_name'] ?? $region['region_code']) ?></p>
                                <p class="info-list-item-description"><?= number_format($region['member_count']) ?> anggota aktif</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Tables Row -->
<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Pending Persetujuan</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Wilayah</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pending_approvals)): ?>
                                <?php foreach ($pending_approvals as $member): ?>
                                <tr>
                                    <td><?= esc($member['full_name']) ?></td>
                                    <td><?= esc($member['email']) ?></td>
                                    <td><?= esc($member['region_code']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($member['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Tidak ada pending approval</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Pendaftaran Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Wilayah</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_registrations)): ?>
                                <?php foreach ($recent_registrations as $member): ?>
                                <tr>
                                    <td><?= esc($member['full_name']) ?></td>
                                    <td><?= esc($member['region_code']) ?></td>
                                    <td>
                                        <?php if ($member['membership_status'] === 'active'): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-info">Calon</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($member['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada pendaftaran</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/neptune/plugins/apexcharts/apexcharts.min.js') ?>"></script>

<?php if (!isset($no_regions)): ?>
<script>
// Member Growth Chart
var memberGrowthOptions = {
    chart: {
        type: 'area',
        height: 350,
        toolbar: { show: false }
    },
    series: [{
        name: 'Total Anggota',
        data: <?= json_encode($chart_data['member_growth']['total'] ?? []) ?>
    }, {
        name: 'Anggota Baru',
        data: <?= json_encode($chart_data['member_growth']['new'] ?? []) ?>
    }],
    xaxis: {
        categories: <?= json_encode($chart_data['member_growth']['months'] ?? []) ?>
    },
    colors: ['#4e73df', '#1cc88a'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    fill: {
        type: 'gradient',
        gradient: { opacityFrom: 0.6, opacityTo: 0.1 }
    },
    legend: { position: 'top' },
    tooltip: {
        y: {
            formatter: function (val) {
                return val + " anggota"
            }
        }
    }
};

var memberGrowthChart = new ApexCharts(document.querySelector("#memberGrowthChart"), memberGrowthOptions);
memberGrowthChart.render();
</script>
<?php endif; ?>
<?= $this->endSection() ?>
