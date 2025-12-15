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

        <!-- Member Statistics Cards -->
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

        <!-- Payment Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-6 col-xl-3 mb-3">
                <div class="card border-start border-success border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Terkumpul</p>
                                <h5 class="mb-0">Rp <?= number_format($payment_stats['total_collected'], 0, ',', '.') ?></h5>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
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
                                <p class="text-muted mb-1">Pending Verifikasi</p>
                                <h3 class="mb-0"><?= number_format($payment_stats['pending_count']) ?></h3>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-hourglass-half fa-3x opacity-50"></i>
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
                                <p class="text-muted mb-1">Bulan Ini</p>
                                <h5 class="mb-0">Rp <?= number_format($payment_stats['this_month'], 0, ',', '.') ?></h5>
                            </div>
                            <div class="text-info">
                                <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 mb-3">
                <div class="card border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Tahun Ini</p>
                                <h5 class="mb-0">Rp <?= number_format($payment_stats['this_year'], 0, ',', '.') ?></h5>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-chart-line fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-calendar-check me-2"></i>Statistik Bulan Ini (<?= date('F Y') ?>)</h5>
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="fas fa-user-plus fa-2x text-primary mb-2"></i>
                                    <h4 class="mb-1"><?= number_format($monthly_stats['new_registrations']) ?></h4>
                                    <p class="text-muted mb-0">Pendaftaran Baru</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <h4 class="mb-1"><?= number_format($monthly_stats['approved']) ?></h4>
                                    <p class="text-muted mb-0">Disetujui</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <i class="fas fa-receipt fa-2x text-info mb-2"></i>
                                    <h4 class="mb-1"><?= number_format($monthly_stats['payments']) ?></h4>
                                    <p class="text-muted mb-0">Pembayaran</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Member Growth Chart -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-chart-area me-2"></i>Pertumbuhan Anggota (12 Bulan)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="memberGrowthChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Payment Trend Chart -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Trend Pembayaran (12 Bulan)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="paymentTrendChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row">
            <!-- Pending Payment Verifications -->
            <?php if (!empty($pending_payments)): ?>
            <div class="col-lg-4 mb-4">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="fas fa-receipt me-2"></i> Pembayaran Pending</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php foreach ($pending_payments as $payment): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1"><?= esc($payment['full_name']) ?></h6>
                                            <small class="text-muted"><?= esc($payment['member_number']) ?></small>
                                        </div>
                                        <span class="badge bg-success">
                                            Rp <?= number_format($payment['amount'], 0, ',', '.') ?>
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?= date('M Y', mktime(0, 0, 0, $payment['payment_month'], 1, $payment['payment_year'])) ?>
                                        </small>
                                        <a href="<?= base_url('admin/payments/view/' . $payment['id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Review
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="<?= base_url('admin/payments/pending') ?>" class="btn btn-link">
                            Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Pending Approvals -->
            <div class="col-lg-<?= !empty($pending_payments) ? '8' : '12' ?> mb-4">
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Member Growth Chart (Line Chart with dual datasets)
const memberGrowthCtx = document.getElementById('memberGrowthChart');
const memberGrowthChart = new Chart(memberGrowthCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($member_growth_chart['months']) ?>,
        datasets: [
            {
                label: 'Total Anggota',
                data: <?= json_encode($member_growth_chart['total_members']) ?>,
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            },
            {
                label: 'Anggota Baru',
                data: <?= json_encode($member_growth_chart['new_members']) ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2,
                pointRadius: 3,
                pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
            },
            tooltip: {
                mode: 'index',
                intersect: false,
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 13
                },
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.parsed.y.toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});

// Payment Trend Chart (Bar Chart)
const paymentTrendCtx = document.getElementById('paymentTrendChart');
const paymentTrendChart = new Chart(paymentTrendCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($payment_trend_chart['months']) ?>,
        datasets: [{
            label: 'Total Pembayaran (Rp)',
            data: <?= json_encode($payment_trend_chart['amounts']) ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.5)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 2,
            borderRadius: 5,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 13
                },
                callbacks: {
                    label: function(context) {
                        return 'Pembayaran: Rp ' + context.parsed.y.toLocaleString('id-ID');
                    },
                    afterLabel: function(context) {
                        const counts = <?= json_encode($payment_trend_chart['counts']) ?>;
                        return 'Jumlah transaksi: ' + counts[context.dataIndex];
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                    }
                }
            }
        }
    }
});
</script>

<?= $this->endSection() ?>
