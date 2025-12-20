<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description d-flex justify-content-between align-items-center">
            <div>
                <h1>Detail Anggota</h1>
                <span>Informasi lengkap anggota di wilayah Anda</span>
            </div>
            <div>
                <a href="<?= base_url('coordinator/members') ?>" class="btn btn-secondary">
                    <i class="material-icons-outlined">arrow_back</i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="material-icons-outlined">check_circle</i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="material-icons-outlined">error</i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Member Information -->
<div class="row">
    <!-- Personal Information -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="material-icons-outlined">person</i>
                    Informasi Pribadi
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th style="width: 200px;">No. Anggota</th>
                        <td><strong><?= esc($member['member_number'] ?? 'Belum digenerate') ?></strong></td>
                    </tr>
                    <tr>
                        <th>Nama Lengkap</th>
                        <td><?= esc($member['full_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= esc($member['email']) ?></td>
                    </tr>
                    <tr>
                        <th>No. Telepon</th>
                        <td><?= esc($member['phone_number'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>NIK</th>
                        <td><?= esc($member['national_id'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Tempat, Tanggal Lahir</th>
                        <td>
                            <?= esc($member['place_of_birth'] ?? '-') ?>,
                            <?= $member['date_of_birth'] ? date('d F Y', strtotime($member['date_of_birth'])) : '-' ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Jenis Kelamin</th>
                        <td><?= $member['gender'] === 'male' ? 'Laki-laki' : 'Perempuan' ?></td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td><?= esc($member['address'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Wilayah</th>
                        <td>
                            <span class="badge badge-primary">
                                <?= esc($member['province_name'] ?? $member['region_code']) ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Employment Information -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="material-icons-outlined">work</i>
                    Informasi Pekerjaan
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th style="width: 200px;">NIP</th>
                        <td><?= esc($member['employee_id'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Jabatan</th>
                        <td><?= esc($member['position'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Departemen</th>
                        <td><?= esc($member['department'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Bergabung</th>
                        <td><?= $member['join_date'] ? date('d F Y', strtotime($member['join_date'])) : '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Status & Actions -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="material-icons-outlined">info</i>
                    Status Keanggotaan
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Status Keanggotaan</label>
                    <div>
                        <?php
                        $statusBadge = match($member['membership_status']) {
                            'active' => 'badge-success',
                            'candidate' => 'badge-warning',
                            'suspended' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                        ?>
                        <span class="badge <?= $statusBadge ?> fs-6">
                            <?= ucfirst($member['membership_status']) ?>
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small">Status Akun</label>
                    <div>
                        <?php
                        $accountBadge = match($member['account_status']) {
                            'active' => 'badge-success',
                            'inactive' => 'badge-secondary',
                            'suspended' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                        ?>
                        <span class="badge <?= $accountBadge ?>">
                            <?= ucfirst($member['account_status']) ?>
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small">Tanggal Registrasi</label>
                    <div><?= date('d F Y', strtotime($member['created_at'])) ?></div>
                </div>

                <?php if (isset($member['coordinator_recommendation'])): ?>
                    <div class="mb-3">
                        <label class="text-muted small">Rekomendasi Koordinator</label>
                        <div>
                            <?php if ($member['coordinator_recommendation'] === 'approved'): ?>
                                <span class="badge badge-success">
                                    <i class="material-icons-outlined" style="font-size: 14px;">check</i>
                                    Direkomendasikan
                                </span>
                            <?php elseif ($member['coordinator_recommendation'] === 'rejected'): ?>
                                <span class="badge badge-danger">
                                    <i class="material-icons-outlined" style="font-size: 14px;">close</i>
                                    Ditolak
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($member['coordinator_notes'])): ?>
                            <small class="text-muted d-block mt-2">
                                Catatan: <?= esc($member['coordinator_notes']) ?>
                            </small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <hr>

                <!-- Action Buttons -->
                <?php if ($member['membership_status'] === 'candidate' &&
                          (!isset($member['coordinator_recommendation']) || $member['coordinator_recommendation'] === null)): ?>
                    <div class="d-grid gap-2">
                        <button type="button"
                                class="btn btn-success"
                                onclick="approveModal(<?= $member['id'] ?>, '<?= esc($member['full_name']) ?>')">
                            <i class="material-icons-outlined">check_circle</i> Rekomendasikan
                        </button>
                        <button type="button"
                                class="btn btn-danger"
                                onclick="rejectModal(<?= $member['id'] ?>, '<?= esc($member['full_name']) ?>')">
                            <i class="material-icons-outlined">cancel</i> Tolak
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="material-icons-outlined">payment</i>
                    Statistik Iuran
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Total Pembayaran</label>
                    <div class="fw-bold"><?= count($payments) ?> pembayaran</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Total Terbayar</label>
                    <div class="fw-bold text-success">
                        Rp <?= number_format(array_sum(array_column(array_filter($payments, fn($p) => $p['status'] === 'verified'), 'amount')), 0, ',', '.') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment History -->
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="material-icons-outlined">history</i>
                    Riwayat Pembayaran Iuran
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($payments)): ?>
                    <div class="alert alert-info">
                        <i class="material-icons-outlined">info</i>
                        Belum ada riwayat pembayaran.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Periode</th>
                                    <th>Jumlah</th>
                                    <th>Metode</th>
                                    <th>Tanggal Bayar</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td>
                                            <?= date('F Y', mktime(0, 0, 0, $payment['payment_month'], 1, $payment['payment_year'])) ?>
                                        </td>
                                        <td><strong>Rp <?= number_format($payment['amount'], 0, ',', '.') ?></strong></td>
                                        <td><?= ucfirst($payment['payment_method'] ?? '-') ?></td>
                                        <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                                        <td>
                                            <?php
                                            $paymentBadge = match($payment['status']) {
                                                'verified' => 'badge-success',
                                                'pending' => 'badge-warning',
                                                'rejected' => 'badge-danger',
                                                default => 'badge-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $paymentBadge ?>">
                                                <?= ucfirst($payment['status']) ?>
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
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rekomendasikan Persetujuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" id="approveForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin merekomendasikan persetujuan untuk anggota:</p>
                    <p class="fw-bold" id="approveMemberName"></p>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Tambahkan catatan atau komentar..."></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="material-icons-outlined">info</i>
                        Rekomendasi Anda akan dikirim ke admin untuk persetujuan final.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="material-icons-outlined">check</i> Rekomendasikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tolak Anggota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" id="rejectForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak anggota:</p>
                    <p class="fw-bold" id="rejectMemberName"></p>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" required placeholder="Jelaskan alasan penolakan..."></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="material-icons-outlined">warning</i>
                        Penolakan akan dikirim ke admin untuk keputusan final.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="material-icons-outlined">close</i> Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveModal(memberId, memberName) {
    document.getElementById('approveMemberName').textContent = memberName;
    document.getElementById('approveForm').action = '<?= base_url('coordinator/members/approve') ?>/' + memberId;
    const modal = new bootstrap.Modal(document.getElementById('approveModal'));
    modal.show();
}

function rejectModal(memberId, memberName) {
    document.getElementById('rejectMemberName').textContent = memberName;
    document.getElementById('rejectForm').action = '<?= base_url('coordinator/members/reject') ?>/' + memberId;
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}
</script>

<?= $this->endSection() ?>
