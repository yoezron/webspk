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
                <li><a href="<?= base_url('member/payment') ?>">Pembayaran</a></li>
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
                        <p class="text-muted mb-0">
                            <?php if ($payment['status'] == 'pending'): ?>
                                Pembayaran Anda sedang menunggu verifikasi dari admin
                            <?php elseif ($payment['status'] == 'verified'): ?>
                                Pembayaran Anda telah diverifikasi dan tercatat
                            <?php elseif ($payment['status'] == 'rejected'): ?>
                                Pembayaran Anda ditolak. Silakan hubungi admin untuk informasi lebih lanjut
                            <?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="row">
                    <!-- Payment Information -->
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Pembayaran</h5>
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
                                        <td class="fw-bold">Tipe</td>
                                        <td>:
                                            <?php
                                            $typeLabels = [
                                                'monthly_dues' => 'Iuran Bulanan',
                                                'registration_fee' => 'Biaya Pendaftaran',
                                                'arrears' => 'Tunggakan',
                                                'other' => 'Lainnya'
                                            ];
                                            echo $typeLabels[$payment['payment_type']] ?? $payment['payment_type'];
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Disubmit</td>
                                        <td>: <?= date('d F Y, H:i', strtotime($payment['created_at'])) ?> WIB</td>
                                    </tr>
                                </table>

                                <?php if ($payment['notes']): ?>
                                    <div class="mt-3 pt-3 border-top">
                                        <p class="fw-bold mb-2">Catatan:</p>
                                        <p class="text-muted mb-0"><?= nl2br(esc($payment['notes'])) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Verification Information -->
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white">
                                <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Informasi Verifikasi</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($payment['status'] == 'pending'): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Pembayaran sedang dalam proses verifikasi
                                    </div>
                                <?php elseif ($payment['status'] == 'verified'): ?>
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td width="40%" class="fw-bold">Status</td>
                                            <td>: <span class="badge bg-success">Terverifikasi</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Tanggal Verifikasi</td>
                                            <td>: <?= date('d F Y, H:i', strtotime($payment['verified_at'])) ?> WIB</td>
                                        </tr>
                                        <?php if ($payment['verification_notes']): ?>
                                            <tr>
                                                <td class="fw-bold" colspan="2">Catatan Admin:</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <div class="alert alert-success mb-0">
                                                        <?= nl2br(esc($payment['verification_notes'])) ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </table>
                                <?php elseif ($payment['status'] == 'rejected'): ?>
                                    <div class="alert alert-danger">
                                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Pembayaran Ditolak</h6>
                                        <?php if ($payment['rejection_reason']): ?>
                                            <p class="mb-0 mt-2"><strong>Alasan:</strong><br><?= nl2br(esc($payment['rejection_reason'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-muted">Silakan submit ulang pembayaran dengan data yang benar.</p>
                                <?php endif; ?>

                                <!-- Bukti Pembayaran -->
                                <div class="mt-4 pt-3 border-top">
                                    <p class="fw-bold mb-3"><i class="fas fa-file-image me-2"></i>Bukti Pembayaran:</p>
                                    <?php if ($payment['payment_proof']): ?>
                                        <?php
                                        $fileExt = pathinfo($payment['payment_proof'], PATHINFO_EXTENSION);
                                        $filePath = base_url('uploads/payments/' . $payment['payment_proof']);
                                        ?>
                                        <?php if (in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png'])): ?>
                                            <div class="text-center">
                                                <img src="<?= $filePath ?>" class="img-fluid rounded shadow-sm" alt="Bukti Pembayaran" style="max-height: 300px;">
                                                <div class="mt-2">
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
                                                        <i class="fas fa-download me-2"></i>Download PDF
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="text-muted">Tidak ada file</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center mt-4">
                    <a href="<?= base_url('member/payment') ?>" class="th-btn">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Riwayat
                    </a>
                    <?php if ($payment['status'] == 'rejected'): ?>
                        <a href="<?= base_url('member/payment/submit') ?>" class="th-btn style3 ms-2">
                            <i class="fas fa-redo me-2"></i>Submit Ulang
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</section>

<?= $this->include('layouts/footer') ?>
