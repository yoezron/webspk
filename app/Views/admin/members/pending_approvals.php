<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Persetujuan Anggota Pending</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/members') ?>">Anggota</a></li>
                    <li class="breadcrumb-item active">Pending Approval</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/members') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
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

    <!-- Pending Approvals List -->
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-clock me-2"></i>
                Daftar Calon Anggota yang Menunggu Persetujuan
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($members)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h4>Tidak Ada Pending Approval</h4>
                    <p class="text-muted">Semua pendaftaran sudah diproses</p>
                    <a href="<?= base_url('admin/members') ?>" class="btn btn-primary mt-3">
                        Lihat Semua Anggota
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu Daftar</th>
                                <th>Nama Lengkap</th>
                                <th>Email / HP</th>
                                <th>Universitas</th>
                                <th>Dokumen</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td>
                                        <?php helper('app'); ?>
                                        <small class="text-muted"><?= time_elapsed_string($member['created_at']) ?></small><br>
                                        <small><?= format_date_indonesia($member['created_at'], 'datetime') ?></small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($member['profile_photo']): ?>
                                                <img src="<?= base_url('uploads/photos/' . $member['profile_photo']) ?>" class="rounded-circle me-2" width="50" height="50" alt="">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center me-2" style="width: 50px; height: 50px;">
                                                    <?php helper('app'); echo get_initials($member['full_name']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <strong><?= esc($member['full_name']) ?></strong><br>
                                                <small class="text-muted"><?= esc($member['gender'] == 'L' ? 'Laki-laki' : 'Perempuan') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="fas fa-envelope text-muted me-1"></i> <?= esc($member['email']) ?><br>
                                        <i class="fas fa-phone text-muted me-1"></i> <?= esc($member['phone_number']) ?>
                                    </td>
                                    <td>
                                        <strong><?= esc($member['university_name']) ?></strong><br>
                                        <small class="text-muted"><?= esc($member['campus_location'] ?? '-') ?></small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <?php if ($member['registration_payment_proof']): ?>
                                                <a href="<?= base_url('uploads/payments/' . $member['registration_payment_proof']) ?>" target="_blank" class="badge bg-success" title="Bukti Bayar">
                                                    <i class="fas fa-receipt"></i> Bayar
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($member['id_card_photo']): ?>
                                                <a href="<?= base_url('uploads/documents/' . $member['id_card_photo']) ?>" target="_blank" class="badge bg-info" title="KTP">
                                                    <i class="fas fa-id-card"></i> KTP
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($member['family_card_photo']): ?>
                                                <a href="<?= base_url('uploads/documents/' . $member['family_card_photo']) ?>" target="_blank" class="badge bg-info" title="KK">
                                                    <i class="fas fa-users"></i> KK
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($member['sk_pengangkatan_photo']): ?>
                                                <a href="<?= base_url('uploads/documents/' . $member['sk_pengangkatan_photo']) ?>" target="_blank" class="badge bg-info" title="SK">
                                                    <i class="fas fa-file-alt"></i> SK
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group-vertical btn-group-sm" role="group">
                                            <a href="<?= base_url('admin/members/view/' . $member['id']) ?>" class="btn btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i> Detail
                                            </a>
                                            <form method="POST" action="<?= base_url('admin/members/approve/' . $member['id']) ?>" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Setujui anggota ini?')">
                                                    <i class="fas fa-check me-1"></i> Setujui
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $member['id'] ?>">
                                                <i class="fas fa-times me-1"></i> Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal<?= $member['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="<?= base_url('admin/members/reject/' . $member['id']) ?>">
                                                <?= csrf_field() ?>
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Tolak Pendaftaran</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        Anda akan menolak pendaftaran: <strong><?= esc($member['full_name']) ?></strong>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                                        <textarea class="form-control" name="rejection_reason" rows="4" required placeholder="Jelaskan alasan penolakan secara detail..."></textarea>
                                                        <div class="form-text">
                                                            Email notifikasi akan dikirim ke calon anggota beserta alasan penolakan ini.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-times me-2"></i>Tolak Pendaftaran
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Menampilkan <?= count($members) ?> pendaftaran pending
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
