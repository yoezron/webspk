<?= $this->include('layouts/header') ?>

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper" data-bg-src="<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Riwayat Pembayaran Iuran</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                <li>Pembayaran</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Payment History Section
============================== -->
<section class="space-top space-extra-bottom">
    <div class="container">

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Member Summary Card -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-3"><i class="fas fa-user-circle me-2"></i>Informasi Anggota</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <strong>Nama:</strong> <?= esc($member['full_name']) ?>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <strong>No. Anggota:</strong> <?= esc($member['member_number']) ?>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <strong>Iuran Bulanan:</strong>
                                        <span class="text-success fw-bold">Rp <?= number_format($member['monthly_dues_amount'] ?? 0, 0, ',', '.') ?></span>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <strong>Pembayaran Terakhir:</strong>
                                        <?= $member['last_dues_payment_date'] ? date('d M Y', strtotime($member['last_dues_payment_date'])) : '<span class="text-muted">Belum ada</span>' ?>
                                    </div>
                                </div>
                                <?php if ($member['total_arrears'] > 0): ?>
                                    <div class="alert alert-warning mt-3 mb-0">
                                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Tunggakan:</strong>
                                        Rp <?= number_format($member['total_arrears'], 0, ',', '.') ?>
                                        (<?= $member['arrears_months'] ?> bulan)
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <a href="<?= base_url('member/payment/submit') ?>" class="th-btn">
                                    <i class="fas fa-plus-circle me-2"></i>Submit Pembayaran Baru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History Table -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($payments)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada riwayat pembayaran</p>
                                <a href="<?= base_url('member/payment/submit') ?>" class="th-btn mt-3">
                                    <i class="fas fa-plus-circle me-2"></i>Submit Pembayaran Pertama
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Periode</th>
                                            <th>Tgl Bayar</th>
                                            <th>Metode</th>
                                            <th>Jumlah</th>
                                            <th>Status</th>
                                            <th>Verifikasi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1 + (($pager->getCurrentPage() - 1) * $pager->getPerPage());
                                        foreach ($payments as $payment):
                                            $statusClass = [
                                                'pending' => 'warning',
                                                'verified' => 'success',
                                                'rejected' => 'danger'
                                            ];
                                            $statusIcon = [
                                                'pending' => 'clock',
                                                'verified' => 'check-circle',
                                                'rejected' => 'times-circle'
                                            ];
                                            $statusText = [
                                                'pending' => 'Menunggu',
                                                'verified' => 'Terverifikasi',
                                                'rejected' => 'Ditolak'
                                            ];
                                        ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td>
                                                    <strong><?= date('M Y', mktime(0, 0, 0, $payment['payment_month'], 1, $payment['payment_year'])) ?></strong>
                                                </td>
                                                <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                                                <td>
                                                    <?php
                                                    $methodLabels = [
                                                        'transfer_bank' => 'Transfer Bank',
                                                        'cash' => 'Tunai',
                                                        'deduction' => 'Potong Gaji',
                                                        'other' => 'Lainnya'
                                                    ];
                                                    echo $methodLabels[$payment['payment_method']] ?? $payment['payment_method'];
                                                    ?>
                                                </td>
                                                <td><strong>Rp <?= number_format($payment['amount'], 0, ',', '.') ?></strong></td>
                                                <td>
                                                    <span class="badge bg-<?= $statusClass[$payment['status']] ?>">
                                                        <i class="fas fa-<?= $statusIcon[$payment['status']] ?> me-1"></i>
                                                        <?= $statusText[$payment['status']] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($payment['status'] == 'verified' && $payment['verified_at']): ?>
                                                        <small class="text-muted">
                                                            <?= date('d M Y', strtotime($payment['verified_at'])) ?>
                                                        </small>
                                                    <?php elseif ($payment['status'] == 'rejected'): ?>
                                                        <small class="text-danger">Ditolak</small>
                                                    <?php else: ?>
                                                        <small class="text-muted">-</small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url('member/payment/view/' . $payment['id']) ?>" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($pager->getPageCount() > 1): ?>
                                <div class="mt-4">
                                    <?= $pager->links() ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<?= $this->include('layouts/footer') ?>
