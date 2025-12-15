<?= $this->include('layouts/header') ?>

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper" data-bg-src="<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Verifikasi Pembayaran Pending</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                <li><a href="<?= base_url('admin/payments') ?>">Pembayaran</a></li>
                <li>Pending</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Pending Payments Section
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

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <a href="<?= base_url('admin/payments') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali ke List
                </a>
            </div>
        </div>

        <!-- Pending Payments List -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Pembayaran Menunggu Verifikasi</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($payments)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5>Tidak Ada Pembayaran Pending</h5>
                                <p class="text-muted">Semua pembayaran sudah diverifikasi</p>
                            </div>
                        <?php else: ?>
                            <!-- Payment Cards -->
                            <?php foreach ($payments as $payment): ?>
                                <div class="card mb-3 border border-warning">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <!-- Member Info -->
                                            <div class="col-md-4">
                                                <h6 class="mb-2"><i class="fas fa-user me-2 text-primary"></i><?= esc($payment['full_name']) ?></h6>
                                                <p class="mb-1 text-muted"><small>No. Anggota: <?= esc($payment['member_number']) ?></small></p>
                                                <p class="mb-0 text-muted"><small><i class="fas fa-envelope me-1"></i><?= esc($payment['email']) ?></small></p>
                                            </div>

                                            <!-- Payment Info -->
                                            <div class="col-md-4">
                                                <p class="mb-1"><strong>Periode:</strong> <?= date('F Y', mktime(0, 0, 0, $payment['payment_month'], 1, $payment['payment_year'])) ?></p>
                                                <p class="mb-1"><strong>Tgl Bayar:</strong> <?= date('d F Y', strtotime($payment['payment_date'])) ?></p>
                                                <p class="mb-1"><strong>Jumlah:</strong> <span class="text-success fw-bold">Rp <?= number_format($payment['amount'], 0, ',', '.') ?></span></p>
                                                <p class="mb-0"><strong>Metode:</strong>
                                                    <?php
                                                    $methodLabels = [
                                                        'transfer_bank' => 'Transfer Bank',
                                                        'cash' => 'Tunai',
                                                        'deduction' => 'Potong Gaji',
                                                        'other' => 'Lainnya'
                                                    ];
                                                    echo $methodLabels[$payment['payment_method']] ?? $payment['payment_method'];
                                                    ?>
                                                </p>
                                            </div>

                                            <!-- Actions -->
                                            <div class="col-md-4 text-end">
                                                <div class="d-grid gap-2">
                                                    <a href="<?= base_url('admin/payments/view/' . $payment['id']) ?>" class="btn btn-primary">
                                                        <i class="fas fa-eye me-2"></i>Lihat Detail & Verifikasi
                                                    </a>
                                                    <?php if ($payment['payment_proof']): ?>
                                                        <?php
                                                        $fileExt = pathinfo($payment['payment_proof'], PATHINFO_EXTENSION);
                                                        $filePath = base_url('uploads/payments/' . $payment['payment_proof']);
                                                        ?>
                                                        <a href="<?= $filePath ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                                                            <i class="fas fa-file-image me-1"></i>Lihat Bukti
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted d-block mt-2">
                                                    <i class="far fa-clock me-1"></i>Disubmit: <?= date('d M Y, H:i', strtotime($payment['created_at'])) ?>
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Notes if any -->
                                        <?php if ($payment['notes']): ?>
                                            <div class="mt-3 pt-3 border-top">
                                                <p class="mb-1"><strong><i class="fas fa-sticky-note me-2"></i>Catatan Member:</strong></p>
                                                <p class="text-muted mb-0"><?= nl2br(esc($payment['notes'])) ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>

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
