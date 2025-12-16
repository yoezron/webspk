<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
Dashboard Admin - Serikat Pekerja Kampus
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- ApexCharts for better charts -->
<link href="<?= base_url('assets/neptune/plugins/apexcharts/apexcharts.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Dashboard Admin</h1>
            <span>Selamat datang, <?= esc(session()->get('user_name')) ?>! Kelola sistem serikat pekerja kampus dengan mudah.</span>
        </div>
    </div>
</div>

<!-- Stats Widgets Row 1 -->
<div class="row">
    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-primary">
                        <i class="material-icons-outlined">people</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Total Anggota Aktif</span>
                        <span class="widget-stats-amount"><?= number_format($stats['total_members']) ?></span>
                        <span class="widget-stats-info">Anggota terdaftar</span>
                    </div>
                    <div class="widget-stats-indicator widget-stats-indicator-positive align-self-start">
                        <i class="material-icons">keyboard_arrow_up</i>
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
                        <span class="widget-stats-amount"><?= number_format($stats['total_candidates']) ?></span>
                        <span class="widget-stats-info">Dalam proses</span>
                    </div>
                    <div class="widget-stats-indicator widget-stats-indicator-positive align-self-start">
                        <i class="material-icons">keyboard_arrow_up</i>
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
                        <span class="widget-stats-title">Menunggu Persetujuan</span>
                        <span class="widget-stats-amount"><?= number_format($stats['pending_approvals']) ?></span>
                        <span class="widget-stats-info">Perlu tindakan</span>
                    </div>
                    <div class="widget-stats-indicator widget-stats-indicator-negative align-self-start">
                        <i class="material-icons">keyboard_arrow_down</i>
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
                        <span class="widget-stats-title">Akun Ditangguhkan</span>
                        <span class="widget-stats-amount"><?= number_format($stats['total_suspended']) ?></span>
                        <span class="widget-stats-info">Tidak aktif</span>
                    </div>
                    <div class="widget-stats-indicator align-self-start">
                        <i class="material-icons">remove</i>
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
                    <div class="widget-stats-indicator widget-stats-indicator-positive align-self-start">
                        <i class="material-icons">keyboard_arrow_up</i> 12%
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
                        <span class="widget-stats-title">Menunggu Verifikasi</span>
                        <span class="widget-stats-amount">Rp <?= number_format($stats['pending_verification'], 0, ',', '.') ?></span>
                        <span class="widget-stats-info">Perlu diverifikasi</span>
                    </div>
                    <div class="widget-stats-indicator align-self-start">
                        <i class="material-icons">schedule</i>
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
                        <span class="widget-stats-title">Iuran Bulan Ini</span>
                        <span class="widget-stats-amount">Rp <?= number_format($stats['monthly_collection'], 0, ',', '.') ?></span>
                        <span class="widget-stats-info"><?= date('F Y') ?></span>
                    </div>
                    <div class="widget-stats-indicator widget-stats-indicator-positive align-self-start">
                        <i class="material-icons">keyboard_arrow_up</i> 8%
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
                        <i class="material-icons-outlined">calendar_today</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Iuran Tahun Ini</span>
                        <span class="widget-stats-amount">Rp <?= number_format($stats['yearly_collection'], 0, ',', '.') ?></span>
                        <span class="widget-stats-info"><?= date('Y') ?></span>
                    </div>
                    <div class="widget-stats-indicator widget-stats-indicator-positive align-self-start">
                        <i class="material-icons">keyboard_arrow_up</i> 15%
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
                <h5 class="card-title">Grafik Pertumbuhan Anggota</h5>
            </div>
            <div class="card-body">
                <div id="memberGrowthChart" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Statistik Bulanan</h5>
            </div>
            <div class="card-body">
                <div class="info-list">
                    <div class="info-list-item">
                        <span class="info-list-item-icon bg-primary text-white">
                            <i class="material-icons-outlined">person_add</i>
                        </span>
                        <div class="info-list-item-content">
                            <p class="info-list-item-title">Pendaftaran Baru</p>
                            <p class="info-list-item-description"><?= isset($monthly_stats['new_registrations']) ? number_format($monthly_stats['new_registrations']) : '0' ?> anggota baru</p>
                        </div>
                    </div>
                    <div class="info-list-item">
                        <span class="info-list-item-icon bg-success text-white">
                            <i class="material-icons-outlined">done</i>
                        </span>
                        <div class="info-list-item-content">
                            <p class="info-list-item-title">Persetujuan</p>
                            <p class="info-list-item-description"><?= isset($monthly_stats['approvals']) ? number_format($monthly_stats['approvals']) : '0' ?> disetujui</p>
                        </div>
                    </div>
                    <div class="info-list-item">
                        <span class="info-list-item-icon bg-warning text-white">
                            <i class="material-icons-outlined">payment</i>
                        </span>
                        <div class="info-list-item-content">
                            <p class="info-list-item-title">Pembayaran</p>
                            <p class="info-list-item-description"><?= isset($monthly_stats['payments']) ? number_format($monthly_stats['payments']) : '0' ?> transaksi</p>
                        </div>
                    </div>
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
                <h5 class="card-title">Pembayaran Tertunda</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Anggota</th>
                                <th>Bulan</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pending_payments)): ?>
                                <?php foreach ($pending_payments as $payment): ?>
                                <tr>
                                    <td><?= esc($payment['member_name']) ?></td>
                                    <td><?= esc($payment['month']) ?></td>
                                    <td>Rp <?= number_format($payment['amount'], 0, ',', '.') ?></td>
                                    <td><span class="badge badge-warning">Menunggu</span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Tidak ada pembayaran tertunda</td>
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
                                <th>Email</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_registrations)): ?>
                                <?php foreach ($recent_registrations as $member): ?>
                                <tr>
                                    <td><?= esc($member['name']) ?></td>
                                    <td><?= esc($member['email']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($member['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/members/view/' . $member['id']) ?>" class="btn btn-sm btn-primary">
                                            <i class="material-icons-outlined">visibility</i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Tidak ada pendaftaran baru</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- ApexCharts -->
<script src="<?= base_url('assets/neptune/plugins/apexcharts/apexcharts.min.js') ?>"></script>

<script>
// Member Growth Chart
var memberGrowthOptions = {
    chart: {
        type: 'area',
        height: 350,
        toolbar: {
            show: false
        }
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
    dataLabels: {
        enabled: false
    },
    stroke: {
        curve: 'smooth',
        width: 2
    },
    fill: {
        type: 'gradient',
        gradient: {
            opacityFrom: 0.6,
            opacityTo: 0.1
        }
    },
    legend: {
        position: 'top'
    },
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
<?= $this->endSection() ?>
