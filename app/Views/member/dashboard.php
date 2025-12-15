<?= $this->include('layouts/header') ?>

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper" data-bg-src="<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Dashboard Member</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li>Dashboard</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Dashboard Section
============================== -->
<section class="space-top space-extra-bottom">
    <div class="container">

        <!-- Welcome Message -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="mb-2">Selamat Datang, <?= esc($member['full_name']) ?>!</h3>
                                <p class="mb-1"><strong>No. Anggota:</strong> <?= esc($member_info['member_number']) ?></p>
                                <p class="mb-0">
                                    <span class="badge bg-<?= $member_info['membership_status'] == 'active' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($member_info['membership_status']) ?>
                                    </span>
                                    <?php if ($member_info['approval_date']): ?>
                                        <small class="text-muted ms-2">Sejak: <?= date('d M Y', strtotime($member_info['approval_date'])) ?></small>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="<?= base_url('member/payment/submit') ?>" class="th-btn">
                                    <i class="fas fa-plus-circle me-2"></i>Submit Pembayaran
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Arrears Warning -->
        <?php if ($member_info['total_arrears'] > 0): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Perhatian: Tunggakan Iuran</h5>
                        <p class="mb-2">Anda memiliki tunggakan iuran sebesar <strong>Rp <?= number_format($member_info['total_arrears'], 0, ',', '.') ?></strong> (<?= $member_info['arrears_months'] ?> bulan).</p>
                        <hr>
                        <p class="mb-0">
                            <a href="<?= base_url('member/payment/submit') ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-money-bill-wave me-1"></i>Bayar Sekarang
                            </a>
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Pending Notification -->
        <?php if ($pending_payments > 0): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        Anda memiliki <strong><?= $pending_payments ?></strong> pembayaran yang sedang menunggu verifikasi admin.
                        <a href="<?= base_url('member/payment') ?>" class="alert-link">Lihat detail</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <!-- Monthly Dues -->
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-money-check-alt fa-3x text-primary"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Rp <?= number_format($member_info['monthly_dues'], 0, ',', '.') ?></h5>
                        <p class="text-muted mb-0">Iuran Bulanan</p>
                    </div>
                </div>
            </div>

            <!-- Total Paid -->
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-coins fa-3x text-success"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Rp <?= number_format($payment_stats['total_paid'], 0, ',', '.') ?></h5>
                        <p class="text-muted mb-0">Total Dibayar</p>
                    </div>
                </div>
            </div>

            <!-- Verified Payments -->
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-check-circle fa-3x text-info"></i>
                        </div>
                        <h5 class="fw-bold mb-1"><?= number_format($payment_stats['verified_count']) ?></h5>
                        <p class="text-muted mb-0">Pembayaran Terverifikasi</p>
                    </div>
                </div>
            </div>

            <!-- This Year -->
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-calendar-alt fa-3x text-warning"></i>
                        </div>
                        <h5 class="fw-bold mb-1">Rp <?= number_format($payment_stats['this_year_paid'], 0, ',', '.') ?></h5>
                        <p class="text-muted mb-0">Tahun <?= date('Y') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Chart -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Riwayat Pembayaran (12 Bulan Terakhir)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="paymentChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Payments -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Pembayaran Terbaru</h5>
                        <a href="<?= base_url('member/payment') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recent_payments)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada riwayat pembayaran</p>
                                <a href="<?= base_url('member/payment/submit') ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus-circle me-1"></i>Submit Pembayaran Pertama
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Periode</th>
                                            <th>Tanggal</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $statusClass = ['pending' => 'warning', 'verified' => 'success', 'rejected' => 'danger'];
                                        $statusIcon = ['pending' => 'clock', 'verified' => 'check-circle', 'rejected' => 'times-circle'];
                                        $statusText = ['pending' => 'Menunggu', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak'];

                                        foreach ($recent_payments as $payment):
                                        ?>
                                            <tr>
                                                <td><?= date('M Y', mktime(0, 0, 0, $payment['payment_month'], 1, $payment['payment_year'])) ?></td>
                                                <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                                                <td><strong>Rp <?= number_format($payment['amount'], 0, ',', '.') ?></strong></td>
                                                <td>
                                                    <span class="badge bg-<?= $statusClass[$payment['status']] ?>">
                                                        <i class="fas fa-<?= $statusIcon[$payment['status']] ?> me-1"></i>
                                                        <?= $statusText[$payment['status']] ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Info -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('member/payment/submit') ?>" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>Submit Pembayaran
                            </a>
                            <a href="<?= base_url('member/payment') ?>" class="btn btn-outline-primary">
                                <i class="fas fa-history me-2"></i>Riwayat Pembayaran
                            </a>
                            <a href="<?= base_url('member/profile') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-user me-2"></i>Lihat Profil
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Member Info Summary -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Keanggotaan</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td width="50%"><i class="fas fa-id-card me-2 text-primary"></i>No. Anggota</td>
                                <td><strong><?= esc($member_info['member_number']) ?></strong></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-calendar-check me-2 text-success"></i>Bergabung</td>
                                <td>
                                    <?= $member_info['approval_date'] ? date('d M Y', strtotime($member_info['approval_date'])) : '-' ?>
                                </td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-money-bill-wave me-2 text-warning"></i>Iuran</td>
                                <td><strong>Rp <?= number_format($member_info['monthly_dues'], 0, ',', '.') ?></strong></td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-calendar-day me-2 text-info"></i>Terakhir Bayar</td>
                                <td>
                                    <?= $member_info['last_dues_payment'] ? date('d M Y', strtotime($member_info['last_dues_payment'])) : 'Belum ada' ?>
                                </td>
                            </tr>
                            <?php if ($member_info['total_arrears'] > 0): ?>
                                <tr>
                                    <td><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Tunggakan</td>
                                    <td><strong class="text-danger">Rp <?= number_format($member_info['total_arrears'], 0, ',', '.') ?></strong></td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Payment Chart
const ctx = document.getElementById('paymentChart');
const paymentChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($payment_history['months']) ?>,
        datasets: [{
            label: 'Pembayaran (Rp)',
            data: <?= json_encode($payment_history['amounts']) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
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
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});
</script>

<?= $this->include('layouts/footer') ?>
