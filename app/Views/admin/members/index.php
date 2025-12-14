<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Manajemen Anggota</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Anggota</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/members/pending') ?>" class="btn btn-warning">
                <i class="fas fa-clock me-2"></i>Pending Approval
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

    <!-- Filters & Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= base_url('admin/members') ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Cari Anggota</label>
                        <input type="text" class="form-control" name="search" placeholder="Nama, Email, Nomor Anggota..." value="<?= esc($search ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status Keanggotaan</label>
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="candidate" <?= ($status_filter ?? '') == 'candidate' ? 'selected' : '' ?>>Calon Anggota</option>
                            <option value="active" <?= ($status_filter ?? '') == 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="suspended" <?= ($status_filter ?? '') == 'suspended' ? 'selected' : '' ?>>Ditangguhkan</option>
                            <option value="inactive" <?= ($status_filter ?? '') == 'inactive' ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role">
                            <option value="">Semua Role</option>
                            <option value="member" <?= ($role_filter ?? '') == 'member' ? 'selected' : '' ?>>Member</option>
                            <option value="coordinator" <?= ($role_filter ?? '') == 'coordinator' ? 'selected' : '' ?>>Koordinator</option>
                            <option value="treasurer" <?= ($role_filter ?? '') == 'treasurer' ? 'selected' : '' ?>>Bendahara</option>
                            <option value="admin" <?= ($role_filter ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Members Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nomor Anggota</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Universitas</th>
                            <th>Status</th>
                            <th>Role</th>
                            <th>Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($members)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Tidak ada data anggota
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($member['member_number'] ?? '-') ?></strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($member['profile_photo']): ?>
                                                <img src="<?= base_url('uploads/photos/' . $member['profile_photo']) ?>" class="rounded-circle me-2" width="40" height="40" alt="">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                                    <?php helper('app'); echo get_initials($member['full_name']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <strong><?= esc($member['full_name']) ?></strong>
                                        </div>
                                    </td>
                                    <td><?= esc($member['email']) ?></td>
                                    <td><?= esc($member['university_name'] ?? '-') ?></td>
                                    <td>
                                        <?php helper('app'); ?>
                                        <span class="badge <?= get_account_status_badge($member['account_status']) ?>">
                                            <?= get_membership_status_label($member['membership_status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php helper('app'); ?>
                                        <span class="badge bg-secondary">
                                            <?= get_user_role_label($member['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php helper('app'); ?>
                                        <small><?= format_date_indonesia($member['created_at'], 'short') ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('admin/members/view/' . $member['id']) ?>" class="btn btn-outline-primary" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($member['account_status'] == 'active'): ?>
                                                <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#suspendModal<?= $member['id'] ?>" title="Tangguhkan">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            <?php elseif ($member['account_status'] == 'suspended'): ?>
                                                <form method="POST" action="<?= base_url('admin/members/activate/' . $member['id']) ?>" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-outline-success" title="Aktifkan" onclick="return confirm('Aktifkan anggota ini?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Suspend Modal -->
                                <div class="modal fade" id="suspendModal<?= $member['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="<?= base_url('admin/members/suspend/' . $member['id']) ?>">
                                                <?= csrf_field() ?>
                                                <div class="modal-header">
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
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (!empty($members)): ?>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Menampilkan <?= count($members) ?> anggota
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
