<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
Dashboard Member - Serikat Pekerja Kampus
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
            <h1>Dashboard Member</h1>
            <span>Selamat datang kembali, <?= esc($member_info['name']) ?>! (No. Anggota: <?= esc($member_info['member_number']) ?>)</span>
        </div>
    </div>
</div>

<!-- Alert Section -->
<?php if (!empty($arrears)): ?>
<div class="row">
    <div class="col-12">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="material-icons-outlined">warning</i> Perhatian!</strong>
            Anda memiliki tunggakan iuran sebesar <strong>Rp <?= number_format($arrears, 0, ',', '.') ?></strong>. Segera lakukan pembayaran.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($pending_payments_count)): ?>
<div class="row">
    <div class="col-12">
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong><i class="material-icons-outlined">info</i> Informasi</strong>
            Anda memiliki <?= $pending_payments_count ?> pembayaran yang sedang menunggu verifikasi.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Stats Widgets -->
<div class="row">
    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-primary">
                        <i class="material-icons-outlined">calendar_month</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Iuran Bulanan</span>
                        <span class="widget-stats-amount">Rp <?= number_format($stats['monthly_dues'], 0, ',', '.') ?></span>
                        <span class="widget-stats-info">Per bulan</span>
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
                        <i class="material-icons-outlined">paid</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Total Dibayar</span>
                        <span class="widget-stats-amount">Rp <?= number_format($stats['total_paid'], 0, ',', '.') ?></span>
                        <span class="widget-stats-info">Semua waktu</span>
                    </div>
                    <div class="widget-stats-indicator widget-stats-indicator-positive align-self-start">
                        <i class="material-icons">check_circle</i>
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
                        <i class="material-icons-outlined">verified</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Pembayaran Terverifikasi</span>
                        <span class="widget-stats-amount"><?= number_format($stats['verified_payments']) ?></span>
                        <span class="widget-stats-info">Transaksi</span>
                    </div>
                    <div class="widget-stats-indicator widget-stats-indicator-positive align-self-start">
                        <i class="material-icons">done_all</i>
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
                        <i class="material-icons-outlined">calendar_today</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Tahun Ini</span>
                        <span class="widget-stats-amount">Rp <?= number_format($stats['yearly_paid'], 0, ',', '.') ?></span>
                        <span class="widget-stats-info"><?= date('Y') ?></span>
                    </div>
                    <div class="widget-stats-indicator widget-stats-indicator-positive align-self-start">
                        <i class="material-icons">trending_up</i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Quick Actions Row -->
<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Riwayat Pembayaran Iuran (12 Bulan Terakhir)</h5>
            </div>
            <div class="card-body">
                <div id="paymentHistoryChart" style="height: 350px;"></div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('member/payment') ?>" class="btn btn-primary btn-lg">
                        <i class="material-icons-outlined">payment</i>
                        <span>Bayar Iuran</span>
                    </a>
                    <a href="<?= base_url('member/payment/history') ?>" class="btn btn-outline-info btn-lg">
                        <i class="material-icons-outlined">history</i>
                        <span>Riwayat Pembayaran</span>
                    </a>
                    <a href="<?= base_url('member/profile') ?>" class="btn btn-outline-secondary btn-lg">
                        <i class="material-icons-outlined">person</i>
                        <span>Profil Saya</span>
                    </a>
                </div>

                <hr class="my-4">

                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-3 text-muted">Informasi Keanggotaan</h6>
                        <div class="info-list-item mb-2">
                            <small class="text-muted">Nomor Anggota</small>
                            <p class="mb-0"><strong><?= esc($member_info['member_number']) ?></strong></p>
                        </div>
                        <div class="info-list-item mb-2">
                            <small class="text-muted">Status</small>
                            <p class="mb-0">
                                <?php if ($member_info['status'] === 'active'): ?>
                                    <span class="badge badge-success">Aktif</span>
                                <?php elseif ($member_info['status'] === 'candidate'): ?>
                                    <span class="badge badge-info">Calon Anggota</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><?= ucfirst($member_info['status']) ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="info-list-item mb-2">
                            <small class="text-muted">Bergabung Sejak</small>
                            <p class="mb-0"><?= date('d F Y', strtotime($member_info['join_date'])) ?></p>
                        </div>
                        <div class="info-list-item">
                            <small class="text-muted">Email</small>
                            <p class="mb-0"><?= esc($member_info['email']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Payments Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Pembayaran Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Bulan Pembayaran</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_payments)): ?>
                                <?php foreach ($recent_payments as $payment): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($payment['payment_date'])) ?></td>
                                    <td><?= date('F Y', strtotime($payment['month'])) ?></td>
                                    <td>Rp <?= number_format($payment['amount'], 0, ',', '.') ?></td>
                                    <td><?= ucfirst(esc($payment['payment_method'])) ?></td>
                                    <td>
                                        <?php if ($payment['verification_status'] === 'verified'): ?>
                                            <span class="badge badge-success">
                                                <i class="material-icons-outlined" style="font-size: 14px;">verified</i> Terverifikasi
                                            </span>
                                        <?php elseif ($payment['verification_status'] === 'pending'): ?>
                                            <span class="badge badge-warning">
                                                <i class="material-icons-outlined" style="font-size: 14px;">pending</i> Menunggu
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">
                                                <i class="material-icons-outlined" style="font-size: 14px;">cancel</i> Ditolak
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('member/payment/view/' . $payment['id']) ?>" class="btn btn-sm btn-primary">
                                            <i class="material-icons-outlined">visibility</i> Detail
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="material-icons-outlined" style="font-size: 48px;">receipt_long</i>
                                        <p class="mb-0">Belum ada riwayat pembayaran</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($recent_payments)): ?>
                <div class="text-center mt-3">
                    <a href="<?= base_url('member/payment/history') ?>" class="btn btn-outline-primary">
                        Lihat Semua Pembayaran <i class="material-icons">arrow_forward</i>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- ApexCharts -->
<script src="<?= base_url('assets/neptune/plugins/apexcharts/apexcharts.min.js') ?>"></script>

<script>
// Payment History Chart
var paymentHistoryOptions = {
    chart: {
        type: 'bar',
        height: 350,
        toolbar: {
            show: false
        }
    },
    series: [{
        name: 'Pembayaran',
        data: <?= json_encode($chart_data['payments'] ?? []) ?>
    }],
    xaxis: {
        categories: <?= json_encode($chart_data['months'] ?? []) ?>
    },
    colors: ['#4e73df'],
    plotOptions: {
        bar: {
            borderRadius: 8,
            dataLabels: {
                position: 'top'
            }
        }
    },
    dataLabels: {
        enabled: true,
        formatter: function (val) {
            return "Rp " + val.toLocaleString('id-ID');
        },
        offsetY: -20,
        style: {
            fontSize: '12px',
            colors: ["#304758"]
        }
    },
    yaxis: {
        labels: {
            formatter: function (val) {
                return "Rp " + val.toLocaleString('id-ID');
            }
        }
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return "Rp " + val.toLocaleString('id-ID');
            }
        }
    }
};

var paymentHistoryChart = new ApexCharts(document.querySelector("#paymentHistoryChart"), paymentHistoryOptions);
paymentHistoryChart.render();
</script>
<?= $this->endSection() ?>
