<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Laporan Komprehensif</h1>
            <span>Analisis mendalam dan laporan sistem</span>
        </div>
    </div>
</div>

<!-- Filter & Export -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="get" action="<?= base_url('admin/reports') ?>" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Jenis Laporan</label>
                        <select name="type" class="form-select" onchange="this.form.submit()">
                            <option value="overview" <?= $report_type === 'overview' ? 'selected' : '' ?>>Overview</option>
                            <option value="members" <?= $report_type === 'members' ? 'selected' : '' ?>>Anggota</option>
                            <option value="financial" <?= $report_type === 'financial' ? 'selected' : '' ?>>Keuangan</option>
                            <option value="regional" <?= $report_type === 'regional' ? 'selected' : '' ?>>Regional</option>
                            <option value="payments" <?= $report_type === 'payments' ? 'selected' : '' ?>>Pembayaran</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="<?= esc($filters['start_date']) ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="<?= esc($filters['end_date']) ?>">
                    </div>
                    <?php if ($report_type !== 'regional'): ?>
                        <div class="col-md-3">
                            <label class="form-label">Filter Wilayah</label>
                            <select name="region" class="form-select">
                                <option value="">Semua Wilayah</option>
                                <?php foreach ($regions as $region): ?>
                                    <option value="<?= esc($region['region_code']) ?>"
                                            <?= ($filters['region'] ?? '') === $region['region_code'] ? 'selected' : '' ?>>
                                        <?= esc($region['province_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="material-icons-outlined">filter_list</i> Filter
                        </button>
                        <a href="<?= base_url('admin/reports/export?' . http_build_query(array_merge(['type' => $report_type], $filters))) ?>"
                           class="btn btn-success">
                            <i class="material-icons-outlined">download</i> Export CSV
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($report_type === 'overview'): ?>
    <!-- Overview Report -->
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
                            <span class="widget-stats-amount"><?= number_format($report_data['total_members']) ?></span>
                            <span class="widget-stats-info">+<?= number_format($report_data['new_members']) ?> baru</span>
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
                            <span class="widget-stats-amount"><?= number_format($report_data['active_members']) ?></span>
                            <span class="widget-stats-info"><?= number_format($report_data['candidates']) ?> kandidat</span>
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
                            <i class="material-icons-outlined">payments</i>
                        </div>
                        <div class="widget-stats-content flex-fill">
                            <span class="widget-stats-title">Total Terkumpul</span>
                            <span class="widget-stats-amount">Rp <?= number_format($report_data['total_collected'], 0, ',', '.') ?></span>
                            <span class="widget-stats-info"><?= number_format($report_data['verified_payments']) ?> transaksi</span>
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
                            <span class="widget-stats-title">Pembayaran Pending</span>
                            <span class="widget-stats-amount"><?= number_format($report_data['pending_payments']) ?></span>
                            <span class="widget-stats-info"><?= $report_data['total_regions'] ?> wilayah</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Pertumbuhan Anggota (12 Bulan)</h5>
                </div>
                <div class="card-body">
                    <div id="memberGrowthChart"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Breakdown Status Anggota</h5>
                </div>
                <div class="card-body">
                    <div id="statusPieChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Regional Distribution -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Top 10 Wilayah</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Provinsi</th>
                                    <th class="text-end">Jumlah Anggota</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report_data['regional_distribution'] as $region): ?>
                                    <tr>
                                        <td><?= esc($region['province_name'] ?? 'Tidak diketahui') ?></td>
                                        <td class="text-end"><strong><?= number_format($region['total']) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($report_type === 'members'): ?>
    <!-- Member Report -->
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
                            <span class="widget-stats-amount"><?= number_format($report_data['total_members']) ?></span>
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
                            <i class="material-icons-outlined">person_add</i>
                        </div>
                        <div class="widget-stats-content flex-fill">
                            <span class="widget-stats-title">Anggota Baru</span>
                            <span class="widget-stats-amount"><?= number_format($report_data['new_members']) ?></span>
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
                            <i class="material-icons-outlined">check_circle</i>
                        </div>
                        <div class="widget-stats-content flex-fill">
                            <span class="widget-stats-title">Aktif</span>
                            <span class="widget-stats-amount"><?= number_format($report_data['active_members']) ?></span>
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
                            <span class="widget-stats-amount"><?= number_format($report_data['candidates']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Breakdown Jenis Kelamin</h5>
                </div>
                <div class="card-body">
                    <div id="genderChart"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Distribusi Usia</h5>
                </div>
                <div class="card-body">
                    <div id="ageChart"></div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($report_data['department_breakdown'])): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Top 10 Departemen</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Departemen</th>
                                        <th class="text-end">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($report_data['department_breakdown'] as $dept): ?>
                                        <tr>
                                            <td><?= esc($dept['department']) ?></td>
                                            <td class="text-end"><strong><?= number_format($dept['total']) ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php elseif ($report_type === 'financial'): ?>
    <!-- Financial Report -->
    <div class="row">
        <div class="col-xl-3 col-sm-6">
            <div class="card widget widget-stats">
                <div class="card-body">
                    <div class="widget-stats-container d-flex">
                        <div class="widget-stats-icon widget-stats-icon-success">
                            <i class="material-icons-outlined">account_balance</i>
                        </div>
                        <div class="widget-stats-content flex-fill">
                            <span class="widget-stats-title">Total Terkumpul</span>
                            <span class="widget-stats-amount">Rp <?= number_format($report_data['total_collected'], 0, ',', '.') ?></span>
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
                            <span class="widget-stats-title">Pending</span>
                            <span class="widget-stats-amount">Rp <?= number_format($report_data['pending_amount'], 0, ',', '.') ?></span>
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
                            <span class="widget-stats-title">Total Transaksi</span>
                            <span class="widget-stats-amount"><?= number_format($report_data['total_transactions']) ?></span>
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
                            <i class="material-icons-outlined">show_chart</i>
                        </div>
                        <div class="widget-stats-content flex-fill">
                            <span class="widget-stats-title">Tingkat Koleksi</span>
                            <span class="widget-stats-amount"><?= number_format($report_data['collection_rate'], 1) ?>%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Metode Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div id="paymentMethodChart"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Outstanding Dues</h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <h3><?= number_format($report_data['outstanding_dues']['count']) ?></h3>
                        <p class="text-muted">Anggota belum bayar bulan ini</p>
                        <h4 class="text-danger">Rp <?= number_format($report_data['outstanding_dues']['estimated_amount'], 0, ',', '.') ?></h4>
                        <p class="text-muted small">Estimasi iuran yang belum terkumpul</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($report_type === 'regional'): ?>
    <!-- Regional Report -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Laporan Per Wilayah</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="alert alert-info">
                                    <strong><?= $report_data['summary']['total_regions'] ?></strong> Total Wilayah
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="alert alert-success">
                                    <strong><?= $report_data['summary']['regions_with_members'] ?></strong> Wilayah Aktif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="alert alert-warning">
                                    <strong><?= number_format($report_data['summary']['total_members']) ?></strong> Total Anggota
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="alert alert-primary">
                                    <strong>Rp <?= number_format($report_data['summary']['total_collected'], 0, ',', '.') ?></strong> Terkumpul
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Provinsi</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Aktif</th>
                                    <th class="text-end">Baru</th>
                                    <th class="text-end">Terkumpul</th>
                                    <th class="text-end">Avg/Anggota</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report_data['regions'] as $region): ?>
                                    <tr>
                                        <td><strong><?= esc($region['region_code']) ?></strong></td>
                                        <td><?= esc($region['province_name']) ?></td>
                                        <td class="text-end"><?= number_format($region['total_members']) ?></td>
                                        <td class="text-end"><?= number_format($region['active_members']) ?></td>
                                        <td class="text-end"><?= number_format($region['new_members']) ?></td>
                                        <td class="text-end text-success">
                                            <strong>Rp <?= number_format($region['total_collected'], 0, ',', '.') ?></strong>
                                        </td>
                                        <td class="text-end">
                                            Rp <?= number_format($region['total_members'] > 0 ? $region['total_collected'] / $region['total_members'] : 0, 0, ',', '.') ?>
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

<?php elseif ($report_type === 'payments'): ?>
    <!-- Payment Report -->
    <div class="row">
        <div class="col-xl-3 col-sm-6">
            <div class="card widget widget-stats">
                <div class="card-body">
                    <div class="widget-stats-container d-flex">
                        <div class="widget-stats-icon widget-stats-icon-primary">
                            <i class="material-icons-outlined">payment</i>
                        </div>
                        <div class="widget-stats-content flex-fill">
                            <span class="widget-stats-title">Total Pembayaran</span>
                            <span class="widget-stats-amount"><?= number_format($report_data['total_payments']) ?></span>
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
                            <span class="widget-stats-title">Terverifikasi</span>
                            <span class="widget-stats-amount"><?= number_format($report_data['verified_count']) ?></span>
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
                            <span class="widget-stats-title">Pending</span>
                            <span class="widget-stats-amount"><?= number_format($report_data['pending_count']) ?></span>
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
                            <i class="material-icons-outlined">percent</i>
                        </div>
                        <div class="widget-stats-content flex-fill">
                            <span class="widget-stats-title">Verification Rate</span>
                            <span class="widget-stats-amount"><?= number_format($report_data['verification_rate'], 1) ?>%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($report_data['payment_trends'])): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Tren Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div id="paymentTrendsChart"></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($report_type === 'overview'): ?>
        // Member Growth Chart
        var memberGrowthChart = new ApexCharts(document.querySelector("#memberGrowthChart"), {
            series: [{
                name: 'Total Anggota',
                data: <?= json_encode($report_data['member_growth']['total']) ?>
            }, {
                name: 'Anggota Baru',
                data: <?= json_encode($report_data['member_growth']['new']) ?>
            }],
            chart: { height: 350, type: 'area', toolbar: { show: false } },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: { categories: <?= json_encode($report_data['member_growth']['months']) ?> },
            colors: ['#5c6bc0', '#66bb6a']
        });
        memberGrowthChart.render();

        // Status Pie Chart
        var statusChart = new ApexCharts(document.querySelector("#statusPieChart"), {
            series: [
                <?= $report_data['status_breakdown']['active'] ?>,
                <?= $report_data['status_breakdown']['candidate'] ?>,
                <?= $report_data['status_breakdown']['suspended'] ?>
            ],
            chart: { height: 350, type: 'pie' },
            labels: ['Aktif', 'Kandidat', 'Ditangguhkan'],
            colors: ['#66bb6a', '#ffa726', '#ef5350']
        });
        statusChart.render();

    <?php elseif ($report_type === 'members'): ?>
        // Gender Chart
        var genderChart = new ApexCharts(document.querySelector("#genderChart"), {
            series: [
                <?php
                $male = array_filter($report_data['gender_breakdown'], fn($g) => $g['gender'] === 'male');
                $female = array_filter($report_data['gender_breakdown'], fn($g) => $g['gender'] === 'female');
                echo !empty($male) ? array_values($male)[0]['total'] : 0;
                ?>,
                <?php echo !empty($female) ? array_values($female)[0]['total'] : 0; ?>
            ],
            chart: { height: 350, type: 'donut' },
            labels: ['Laki-laki', 'Perempuan'],
            colors: ['#42a5f5', '#ec407a']
        });
        genderChart.render();

        // Age Chart
        var ageChart = new ApexCharts(document.querySelector("#ageChart"), {
            series: [{
                name: 'Jumlah',
                data: <?= json_encode(array_values($report_data['age_distribution'])) ?>
            }],
            chart: { height: 350, type: 'bar' },
            xaxis: { categories: <?= json_encode(array_keys($report_data['age_distribution'])) ?> },
            colors: ['#ab47bc']
        });
        ageChart.render();

    <?php elseif ($report_type === 'financial'): ?>
        // Payment Method Chart
        var paymentMethodChart = new ApexCharts(document.querySelector("#paymentMethodChart"), {
            series: [{
                name: 'Jumlah',
                data: <?= json_encode(array_column($report_data['payment_method_breakdown'], 'total')) ?>
            }],
            chart: { height: 350, type: 'bar' },
            xaxis: { categories: <?= json_encode(array_column($report_data['payment_method_breakdown'], 'payment_method')) ?> },
            colors: ['#26a69a'],
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return 'Rp ' + val.toLocaleString('id-ID');
                    }
                }
            }
        });
        paymentMethodChart.render();

    <?php elseif ($report_type === 'payments' && !empty($report_data['payment_trends'])): ?>
        // Payment Trends Chart
        var paymentTrendsChart = new ApexCharts(document.querySelector("#paymentTrendsChart"), {
            series: [{
                name: 'Jumlah Transaksi',
                type: 'column',
                data: <?= json_encode(array_column($report_data['payment_trends'], 'count')) ?>
            }, {
                name: 'Total Amount',
                type: 'line',
                data: <?= json_encode(array_column($report_data['payment_trends'], 'total')) ?>
            }],
            chart: { height: 350, type: 'line', toolbar: { show: false } },
            stroke: { width: [0, 4] },
            xaxis: { categories: <?= json_encode(array_column($report_data['payment_trends'], 'month')) ?> },
            colors: ['#42a5f5', '#66bb6a'],
            yaxis: [{
                title: { text: 'Jumlah' },
            }, {
                opposite: true,
                title: { text: 'Amount (Rp)' },
                labels: {
                    formatter: function(val) {
                        return 'Rp ' + val.toLocaleString('id-ID');
                    }
                }
            }]
        });
        paymentTrendsChart.render();
    <?php endif; ?>
});
</script>

<?= $this->endSection() ?>
