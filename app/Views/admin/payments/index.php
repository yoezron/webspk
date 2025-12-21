<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Manajemen Pembayaran Iuran</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pembayaran</li>
                </ol>
            </nav>
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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Menunggu Verifikasi</p>
                            <h3 class="mb-0"><?= number_format($stats['pending'] ?? 0) ?></h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-clock fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Terverifikasi</p>
                            <h3 class="mb-0"><?= number_format($stats['verified'] ?? 0) ?></h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Ditolak</p>
                            <h3 class="mb-0"><?= number_format($stats['rejected'] ?? 0) ?></h3>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-times-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Pembayaran</p>
                            <h5 class="mb-0">Rp <?= number_format($stats['total_amount'] ?? 0, 0, ',', '.') ?></h5>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <a href="<?= base_url('admin/payments/pending') ?>" class="btn btn-warning">
                <i class="fas fa-clock me-2"></i>Verifikasi Pending (<?= number_format($stats['pending'] ?? 0) ?>)
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= base_url('admin/payments') ?>" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" <?= $status_filter == 'pending' ? 'selected' : '' ?>>Menunggu</option>
                        <option value="verified" <?= $status_filter == 'verified' ? 'selected' : '' ?>>Terverifikasi</option>
                        <option value="rejected" <?= $status_filter == 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cari (Nama/No. Anggota/Referensi)</label>
                    <input type="text" name="search" class="form-control" placeholder="Cari..." value="<?= esc($search ?? '') ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment List -->
    <div class="card">
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
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payments)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Tidak ada data pembayaran
                                </td>
                            </tr>
                        <?php else: ?>
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
                                        <strong><?= esc($payment['full_name']) ?></strong><br>
                                        <small class="text-muted"><?= esc($payment['email']) ?></small>
                                    </td>
                                    <td><?= esc($payment['member_number']) ?></td>
                                    <td>
                                        <strong><?= date('M Y', mktime(0, 0, 0, $payment['payment_month'], 1, $payment['payment_year'])) ?></strong>
                                    </td>
                                    <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                                    <td><strong>Rp <?= number_format($payment['amount'], 0, ',', '.') ?></strong></td>
                                    <td>
                                        <span class="badge bg-<?= $statusClass[$payment['status']] ?>">
                                            <i class="fas fa-<?= $statusIcon[$payment['status']] ?> me-1"></i>
                                            <?= $statusText[$payment['status']] ?>
                                        </span>
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
                        Menampilkan <?= count($payments) ?> pembayaran
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
