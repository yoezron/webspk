<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Pembayaran Terverifikasi</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/payments') ?>">Pembayaran</a></li>
                    <li class="breadcrumb-item active">Terverifikasi</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/payments') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke List
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Verified Payments Table -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Pembayaran Terverifikasi</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Anggota</th>
                            <th>No. Anggota</th>
                            <th>Periode</th>
                            <th>Tgl Bayar</th>
                            <th>Jumlah</th>
                            <th>Tgl Verifikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payments)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Tidak ada pembayaran terverifikasi
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php
                            $no = 1 + (($pager->getCurrentPage() - 1) * $pager->getPerPage());
                            foreach ($payments as $payment):
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= esc($payment['full_name']) ?></strong><br>
                                        <small class="text-muted"><?= esc($payment['email']) ?></small>
                                    </td>
                                    <td><?= esc($payment['member_number']) ?></td>
                                    <td>
                                        <strong><?= date('M Y', mktime(0, 0, 0, $payment['payment_month'], 1, $payment['payment_year'])) ?></strong>
                                    </td>
                                    <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                                    <td><strong class="text-success">Rp <?= number_format($payment['amount'], 0, ',', '.') ?></strong></td>
                                    <td>
                                        <small><?= date('d M Y, H:i', strtotime($payment['verified_at'])) ?></small>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/payments/view/' . $payment['id']) ?>" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (!empty($payments) && $pager->getPageCount() > 1): ?>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Menampilkan <?= count($payments) ?> pembayaran terverifikasi
                    </div>
                    <div>
                        <?= $pager->links() ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
