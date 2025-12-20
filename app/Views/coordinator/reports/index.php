<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Laporan Regional</h1>
            <span>Analisis dan statistik wilayah koordinasi Anda</span>
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

<!-- Filter Form -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="get" action="<?= base_url('coordinator/reports') ?>" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="<?= esc($filters['start_date']) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control" value="<?= esc($filters['end_date']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Filter Wilayah</label>
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
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="material-icons-outlined">filter_list</i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
                        <span class="widget-stats-amount"><?= number_format($stats['total_members']) ?></span>
                        <span class="widget-stats-info">Aktif: <?= number_format($stats['active_members']) ?></span>
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
                        <span class="widget-stats-amount"><?= number_format($stats['new_members']) ?></span>
                        <span class="widget-stats-info">Periode ini</span>
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
                        <span class="widget-stats-info">Kandidat: <?= number_format($stats['candidates']) ?></span>
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
                        <span class="widget-stats-amount">Rp <?= number_format($stats['total_collected'], 0, ',', '.') ?></span>
                        <span class="widget-stats-info"><?= number_format($stats['verified_payments']) ?> pembayaran</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <!-- Member Growth Chart -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="material-icons-outlined">trending_up</i>
                    Pertumbuhan Anggota (6 Bulan Terakhir)
                </h5>
            </div>
            <div class="card-body">
                <div id="memberGrowthChart"></div>
            </div>
        </div>
    </div>

    <!-- Payment Trends Chart -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="material-icons-outlined">show_chart</i>
                    Tren Pembayaran Iuran (6 Bulan Terakhir)
                </h5>
            </div>
            <div class="card-body">
                <div id="paymentTrendsChart"></div>
            </div>
        </div>
    </div>
</div>

<!-- Regional Breakdown -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="material-icons-outlined">table_chart</i>
                    Breakdown per Wilayah
                </h5>
                <a href="<?= base_url('coordinator/reports/export?' . http_build_query($filters)) ?>"
                   class="btn btn-success btn-sm">
                    <i class="material-icons-outlined">download</i> Export CSV
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Provinsi</th>
                                <th class="text-end">Anggota Aktif</th>
                                <th class="text-end">Kandidat</th>
                                <th class="text-end">Total Anggota</th>
                                <th class="text-end">Total Terkumpul</th>
                                <th class="text-end">Rata-rata/Anggota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($regional_breakdown)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data</td>
                                </tr>
                            <?php else: ?>
                                <?php
                                $totalMembers = 0;
                                $totalCandidates = 0;
                                $totalCollected = 0;
                                ?>
                                <?php foreach ($regional_breakdown as $region): ?>
                                    <?php
                                    $totalMembers += $region['members'];
                                    $totalCandidates += $region['candidates'];
                                    $totalCollected += $region['total_collected'];
                                    $avgPerMember = $region['members'] > 0 ? $region['total_collected'] / $region['members'] : 0;
                                    ?>
                                    <tr>
                                        <td><strong><?= esc($region['region_code']) ?></strong></td>
                                        <td><?= esc($region['province_name']) ?></td>
                                        <td class="text-end"><?= number_format($region['members']) ?></td>
                                        <td class="text-end"><?= number_format($region['candidates']) ?></td>
                                        <td class="text-end">
                                            <strong><?= number_format($region['members'] + $region['candidates']) ?></strong>
                                        </td>
                                        <td class="text-end text-success">
                                            <strong>Rp <?= number_format($region['total_collected'], 0, ',', '.') ?></strong>
                                        </td>
                                        <td class="text-end">
                                            Rp <?= number_format($avgPerMember, 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <!-- Total Row -->
                                <tr class="table-primary fw-bold">
                                    <td colspan="2">TOTAL</td>
                                    <td class="text-end"><?= number_format($totalMembers) ?></td>
                                    <td class="text-end"><?= number_format($totalCandidates) ?></td>
                                    <td class="text-end"><?= number_format($totalMembers + $totalCandidates) ?></td>
                                    <td class="text-end text-success">
                                        Rp <?= number_format($totalCollected, 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end">
                                        Rp <?= number_format($totalMembers > 0 ? $totalCollected / $totalMembers : 0, 0, ',', '.') ?>
                                    </td>
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

<!-- ApexCharts JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!isset($message)): ?>

    // Member Growth Chart
    var memberGrowthOptions = {
        series: [{
            name: 'Total Anggota',
            data: <?= json_encode($member_growth['total']) ?>
        }, {
            name: 'Anggota Baru',
            data: <?= json_encode($member_growth['new']) ?>
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            categories: <?= json_encode($member_growth['months']) ?>
        },
        colors: ['#5c6bc0', '#66bb6a'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
            }
        }
    };

    var memberGrowthChart = new ApexCharts(document.querySelector("#memberGrowthChart"), memberGrowthOptions);
    memberGrowthChart.render();

    // Payment Trends Chart
    var paymentTrendsOptions = {
        series: [{
            name: 'Jumlah Pembayaran',
            type: 'column',
            data: <?= json_encode($payment_trends['count']) ?>
        }, {
            name: 'Total (Rp)',
            type: 'line',
            data: <?= json_encode($payment_trends['amount']) ?>
        }],
        chart: {
            height: 350,
            type: 'line',
            toolbar: {
                show: false
            }
        },
        stroke: {
            width: [0, 4]
        },
        dataLabels: {
            enabled: true,
            enabledOnSeries: [1]
        },
        xaxis: {
            categories: <?= json_encode($payment_trends['months']) ?>
        },
        yaxis: [{
            title: {
                text: 'Jumlah Pembayaran',
            },
        }, {
            opposite: true,
            title: {
                text: 'Total (Rp)'
            }
        }],
        colors: ['#42a5f5', '#66bb6a']
    };

    var paymentTrendsChart = new ApexCharts(document.querySelector("#paymentTrendsChart"), paymentTrendsOptions);
    paymentTrendsChart.render();

    <?php endif; ?>
});
</script>

<?= $this->endSection() ?>
