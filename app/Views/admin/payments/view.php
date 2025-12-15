<?= $this->include('layouts/header') ?>

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper" data-bg-src="<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Detail Pembayaran</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                <li><a href="<?= base_url('admin/payments') ?>">Pembayaran</a></li>
                <li>Detail</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Payment Detail Section
============================== -->
<section class="space-top space-extra-bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">

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

                <?php
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
                    'pending' => 'Menunggu Verifikasi',
                    'verified' => 'Terverifikasi',
                    'rejected' => 'Ditolak'
                ];
                ?>

                <!-- Status Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center py-4">
                        <i class="fas fa-<?= $statusIcon[$payment['status']] ?> fa-4x text-<?= $statusClass[$payment['status']] ?> mb-3"></i>
                        <h3 class="mb-2">Status: <?= $statusText[$payment['status']] ?></h3>
                    </div>
                </div>

                <div class="row">
                    <!-- Member & Payment Info -->
                    <div class="col-md-6">
                        <!-- Member Info -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Anggota</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td width="40%" class="fw-bold">Nama</td>
                                        <td>: <?= esc($payment['full_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">No. Anggota</td>
                                        <td>: <?= esc($payment['member_number']) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Email</td>
                                        <td>: <?= esc($payment['email']) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Telepon</td>
                                        <td>: <?= esc($payment['phone_number']) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Informasi Pembayaran</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td width="40%" class="fw-bold">Periode</td>
                                        <td>: <?= date('F Y', mktime(0, 0, 0, $payment['payment_month'], 1, $payment['payment_year'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Tanggal Bayar</td>
                                        <td>: <?= date('d F Y', strtotime($payment['payment_date'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Metode</td>
                                        <td>:
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
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Jumlah</td>
                                        <td>: <strong class="text-success">Rp <?= number_format($payment['amount'], 0, ',', '.') ?></strong></td>
                                    </tr>
                                    <?php if ($payment['payment_reference']): ?>
                                        <tr>
                                            <td class="fw-bold">No. Referensi</td>
                                            <td>: <?= esc($payment['payment_reference']) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td class="fw-bold">Disubmit</td>
                                        <td>: <?= date('d F Y, H:i', strtotime($payment['created_at'])) ?> WIB</td>
                                    </tr>
                                </table>

                                <?php if ($payment['notes']): ?>
                                    <div class="mt-3 pt-3 border-top">
                                        <p class="fw-bold mb-2">Catatan Member:</p>
                                        <div class="alert alert-info mb-0">
                                            <?= nl2br(esc($payment['notes'])) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Bukti & Verification -->
                    <div class="col-md-6">
                        <!-- Payment Proof -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-file-image me-2"></i>Bukti Pembayaran</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($payment['payment_proof']): ?>
                                    <?php
                                    $fileExt = pathinfo($payment['payment_proof'], PATHINFO_EXTENSION);
                                    $filePath = base_url('uploads/payments/' . $payment['payment_proof']);
                                    ?>
                                    <?php if (in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png'])): ?>
                                        <div class="text-center">
                                            <img src="<?= $filePath ?>" class="img-fluid rounded shadow-sm mb-3" alt="Bukti Pembayaran" style="max-height: 400px; cursor: pointer;" onclick="window.open('<?= $filePath ?>', '_blank')">
                                            <div>
                                                <a href="<?= $filePath ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-external-link-alt me-1"></i>Buka di Tab Baru
                                                </a>
                                            </div>
                                        </div>
                                    <?php elseif (strtolower($fileExt) == 'pdf'): ?>
                                        <div class="text-center">
                                            <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                                            <div>
                                                <a href="<?= $filePath ?>" target="_blank" class="btn btn-primary">
                                                    <i class="fas fa-external-link-alt me-2"></i>Buka PDF
                                                </a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p class="text-muted text-center">Tidak ada file</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Verification Section -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Verifikasi</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($payment['status'] == 'pending'): ?>
                                    <!-- Verify Form -->
                                    <div class="mb-3">
                                        <h6 class="text-success"><i class="fas fa-check-circle me-2"></i>Verifikasi Pembayaran</h6>
                                        <form action="<?= base_url('admin/payments/verify/' . $payment['id']) ?>" method="post">
                                            <?= csrf_field() ?>
                                            <div class="mb-3">
                                                <label class="form-label">Catatan Verifikasi (Opsional)</label>
                                                <textarea name="notes" class="form-control" rows="3" placeholder="Tambahkan catatan jika perlu..."></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Apakah Anda yakin ingin memverifikasi pembayaran ini?')">
                                                <i class="fas fa-check me-2"></i>Verifikasi Pembayaran
                                            </button>
                                        </form>
                                    </div>

                                    <hr>

                                    <!-- Reject Form -->
                                    <div>
                                        <h6 class="text-danger"><i class="fas fa-times-circle me-2"></i>Tolak Pembayaran</h6>
                                        <form action="<?= base_url('admin/payments/reject/' . $payment['id']) ?>" method="post">
                                            <?= csrf_field() ?>
                                            <div class="mb-3">
                                                <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                                <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Jelaskan alasan penolakan..." required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Apakah Anda yakin ingin menolak pembayaran ini?')">
                                                <i class="fas fa-times me-2"></i>Tolak Pembayaran
                                            </button>
                                        </form>
                                    </div>

                                <?php elseif ($payment['status'] == 'verified'): ?>
                                    <div class="alert alert-success">
                                        <h6><i class="fas fa-check-circle me-2"></i>Pembayaran Terverifikasi</h6>
                                        <p class="mb-2"><strong>Tanggal Verifikasi:</strong><br><?= date('d F Y, H:i', strtotime($payment['verified_at'])) ?> WIB</p>
                                        <?php if ($payment['verification_notes']): ?>
                                            <p class="mb-0"><strong>Catatan:</strong><br><?= nl2br(esc($payment['verification_notes'])) ?></p>
                                        <?php endif; ?>
                                    </div>

                                <?php elseif ($payment['status'] == 'rejected'): ?>
                                    <div class="alert alert-danger">
                                        <h6><i class="fas fa-times-circle me-2"></i>Pembayaran Ditolak</h6>
                                        <p class="mb-2"><strong>Tanggal Penolakan:</strong><br><?= date('d F Y, H:i', strtotime($payment['verified_at'])) ?> WIB</p>
                                        <?php if ($payment['verification_notes']): ?>
                                            <p class="mb-0"><strong>Alasan:</strong><br><?= nl2br(esc($payment['verification_notes'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center mt-4">
                    <a href="<?= base_url('admin/payments') ?>" class="th-btn">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke List
                    </a>
                    <?php if ($payment['status'] == 'pending'): ?>
                        <a href="<?= base_url('admin/payments/pending') ?>" class="th-btn style3 ms-2">
                            <i class="fas fa-clock me-2"></i>Lihat Pending Lainnya
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</section>

<?= $this->include('layouts/footer') ?>
