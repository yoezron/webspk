<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Anggota</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/members') ?>">Anggota</a></li>
                    <li class="breadcrumb-item active"><?= esc($member['full_name']) ?></li>
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

    <div class="row">
        <!-- Left Column - Profile Summary -->
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <?php if ($member['profile_photo']): ?>
                        <img src="<?= base_url('uploads/photos/' . $member['profile_photo']) ?>" class="rounded-circle mb-3" width="150" height="150" alt="Profile Photo">
                    <?php else: ?>
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 150px; font-size: 48px;">
                            <?php helper('app'); echo get_initials($member['full_name']); ?>
                        </div>
                    <?php endif; ?>

                    <h4 class="mb-1"><?= esc($member['full_name']) ?></h4>
                    <p class="text-muted mb-3"><?= esc($member['email']) ?></p>

                    <?php if ($member['member_number']): ?>
                        <div class="alert alert-info mb-3">
                            <small class="text-muted d-block">Nomor Anggota</small>
                            <strong><?= esc($member['member_number']) ?></strong>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <?php helper('app'); ?>
                        <span class="badge <?= get_account_status_badge($member['account_status']) ?>">
                            <?= get_membership_status_label($member['membership_status']) ?>
                        </span>
                        <span class="badge bg-secondary">
                            <?= get_user_role_label($member['role']) ?>
                        </span>
                    </div>

                    <!-- Quick Actions -->
                    <?php if ($member['account_status'] == 'pending' && $member['onboarding_state'] == 'payment_submitted'): ?>
                        <div class="d-grid gap-2">
                            <form method="POST" action="<?= base_url('admin/members/approve/' . $member['id']) ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Setujui anggota ini?')">
                                    <i class="fas fa-check me-2"></i>Setujui Keanggotaan
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="fas fa-times me-2"></i>Tolak
                            </button>
                        </div>
                    <?php elseif ($member['account_status'] == 'active'): ?>
                        <button type="button" class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#suspendModal">
                            <i class="fas fa-ban me-2"></i>Tangguhkan Akun
                        </button>
                    <?php elseif ($member['account_status'] == 'suspended'): ?>
                        <form method="POST" action="<?= base_url('admin/members/activate/' . $member['id']) ?>">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Aktifkan kembali anggota ini?')">
                                <i class="fas fa-check me-2"></i>Aktifkan Kembali
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Kontak</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-phone text-muted me-2"></i>
                            <strong>HP:</strong><br>
                            <a href="tel:<?= esc($member['phone_number']) ?>"><?= esc($member['phone_number']) ?></a>
                        </li>
                        <?php if ($member['alt_phone_number']): ?>
                            <li class="mb-2">
                                <i class="fas fa-phone text-muted me-2"></i>
                                <strong>HP Alternatif:</strong><br>
                                <a href="tel:<?= esc($member['alt_phone_number']) ?>"><?= esc($member['alt_phone_number']) ?></a>
                            </li>
                        <?php endif; ?>
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt text-muted me-2"></i>
                            <strong>Alamat:</strong><br>
                            <?= esc($member['address'] ?? '-') ?><br>
                            <small class="text-muted">
                                <?= esc($member['district'] ?? '-') ?>, <?= esc($member['city'] ?? '-') ?><br>
                                <?= esc($member['province'] ?? '-') ?> <?= esc($member['postal_code'] ?? '') ?>
                            </small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right Column - Detailed Information -->
        <div class="col-lg-8">
            <!-- Personal Data -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Data Pribadi</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Jenis Kelamin</label>
                            <p class="mb-0"><strong><?= $member['gender'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tempat, Tanggal Lahir</label>
                            <p class="mb-0"><strong><?= esc($member['birth_place'] ?? '-') ?>, <?= esc($member['birth_date'] ?? '-') ?></strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">NIK</label>
                            <p class="mb-0"><strong><?= esc($member['identity_number'] ?? '-') ?></strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">NPWP</label>
                            <p class="mb-0"><strong><?= esc($member['npwp_number'] ?? '-') ?></strong></p>
                        </div>
                    </div>

                    <?php if ($member['emergency_contact_name']): ?>
                        <hr>
                        <h6 class="mb-3">Kontak Darurat</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Nama</label>
                                <p class="mb-0"><strong><?= esc($member['emergency_contact_name']) ?></strong></p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Hubungan</label>
                                <p class="mb-0"><strong><?= esc($member['emergency_contact_relation']) ?></strong></p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Telepon</label>
                                <p class="mb-0"><strong><?= esc($member['emergency_contact_phone']) ?></strong></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Work Data -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Data Pekerjaan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Universitas</label>
                            <p class="mb-0"><strong><?= esc($member['university_name']) ?></strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Lokasi Kampus</label>
                            <p class="mb-0"><strong><?= esc($member['campus_location'] ?? '-') ?></strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Fakultas</label>
                            <p class="mb-0"><strong><?= esc($member['faculty'] ?? '-') ?></strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Departemen</label>
                            <p class="mb-0"><strong><?= esc($member['department'] ?? '-') ?></strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Status Kepegawaian</label>
                            <p class="mb-0"><strong><?= esc($member['employment_status'] ?? '-') ?></strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tanggal Mulai Kerja</label>
                            <p class="mb-0"><strong><?= esc($member['work_start_date'] ?? '-') ?></strong></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Dokumen</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if ($member['registration_payment_proof']): ?>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Bukti Pembayaran Pendaftaran</label>
                                <p>
                                    <a href="<?= base_url('uploads/payments/' . $member['registration_payment_proof']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-receipt me-2"></i>Lihat Dokumen
                                    </a>
                                </p>
                            </div>
                        <?php endif; ?>
                        <?php if ($member['id_card_photo']): ?>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">KTP</label>
                                <p>
                                    <a href="<?= base_url('uploads/documents/' . $member['id_card_photo']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-id-card me-2"></i>Lihat Dokumen
                                    </a>
                                </p>
                            </div>
                        <?php endif; ?>
                        <?php if ($member['family_card_photo']): ?>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Kartu Keluarga</label>
                                <p>
                                    <a href="<?= base_url('uploads/documents/' . $member['family_card_photo']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-users me-2"></i>Lihat Dokumen
                                    </a>
                                </p>
                            </div>
                        <?php endif; ?>
                        <?php if ($member['sk_pengangkatan_photo']): ?>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">SK Pengangkatan</label>
                                <p>
                                    <a href="<?= base_url('uploads/documents/' . $member['sk_pengangkatan_photo']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-alt me-2"></i>Lihat Dokumen
                                    </a>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Riwayat Aktivitas</h6>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li>
                            <i class="fas fa-user-plus text-primary"></i>
                            <strong>Mendaftar</strong>
                            <br><small class="text-muted"><?php helper('app'); echo format_date_indonesia($member['created_at'], 'datetime'); ?></small>
                        </li>
                        <?php if ($member['email_verified_at']): ?>
                            <li>
                                <i class="fas fa-envelope-open text-success"></i>
                                <strong>Email Terverifikasi</strong>
                                <br><small class="text-muted"><?php echo format_date_indonesia($member['email_verified_at'], 'datetime'); ?></small>
                            </li>
                        <?php endif; ?>
                        <?php if ($member['approval_date']): ?>
                            <li>
                                <i class="fas fa-check-circle text-success"></i>
                                <strong>Keanggotaan Disetujui</strong>
                                <br><small class="text-muted"><?php echo format_date_indonesia($member['approval_date'], 'datetime'); ?></small>
                            </li>
                        <?php endif; ?>
                        <?php if ($member['last_login_at']): ?>
                            <li>
                                <i class="fas fa-sign-in-alt text-info"></i>
                                <strong>Login Terakhir</strong>
                                <br><small class="text-muted"><?php echo format_date_indonesia($member['last_login_at'], 'datetime'); ?></small>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= base_url('admin/members/reject/' . $member['id']) ?>">
                <?= csrf_field() ?>
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Tolak Pendaftaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Anda akan menolak pendaftaran: <strong><?= esc($member['full_name']) ?></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="rejection_reason" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Suspend Modal -->
<div class="modal fade" id="suspendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= base_url('admin/members/suspend/' . $member['id']) ?>">
                <?= csrf_field() ?>
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Tangguhkan Anggota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Anda akan menangguhkan anggota: <strong><?= esc($member['full_name']) ?></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penangguhan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="suspension_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Tangguhkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.timeline {
    list-style: none;
    padding-left: 0;
}
.timeline li {
    padding-left: 35px;
    position: relative;
    padding-bottom: 20px;
}
.timeline li i {
    position: absolute;
    left: 0;
    top: 0;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}
.timeline li:not(:last-child):before {
    content: '';
    position: absolute;
    left: 12px;
    top: 25px;
    height: calc(100% - 25px);
    width: 2px;
    background: #dee2e6;
}
</style>

<?= $this->endSection() ?>
