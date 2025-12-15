<?= $this->include('layouts/header') ?>

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper" data-bg-src="<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Manajemen Pembayaran Iuran</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                <li>Pembayaran</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Payment Management Section
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

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?= number_format($stats['pending'] ?? 0) ?></h3>
                        <p class="text-muted mb-0">Menunggu Verifikasi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?= number_format($stats['verified'] ?? 0) ?></h3>
                        <p class="text-muted mb-0">Terverifikasi</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                        </div>
                        <h3 class="fw-bold mb-1"><?= number_format($stats['rejected'] ?? 0) ?></h3>
                        <p class="text-muted mb-0">Ditolak</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="mb-2">
                            <i class="fas fa-money-bill-wave fa-2x text-primary"></i>
                        </div>
                        <h4 class="fw-bold mb-1">Rp <?= number_format($stats['total_amount'] ?? 0, 0, ',', '.') ?></h4>
                        <p class="text-muted mb-0">Total Pembayaran</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <a href="<?= base_url('admin/payments/pending') ?>" class="btn btn-warning me-2">
                    <i class="fas fa-clock me-2"></i>Verifikasi Pending (<?= number_format($stats['pending'] ?? 0) ?>)
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card shadow-sm">
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
            </div>
        </div>

        <!-- Payment List -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($payments)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data pembayaran</p>
                            </div>
                        <?php else: ?>
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
