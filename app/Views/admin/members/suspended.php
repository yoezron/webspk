<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Anggota Ditangguhkan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/members') ?>">Anggota</a></li>
                    <li class="breadcrumb-item active">Ditangguhkan</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/members') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Anggota
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

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="alert alert-warning">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Total Anggota Ditangguhkan:</strong> <?= count($members) ?> anggota
            </div>
        </div>
    </div>

    <!-- Suspended Members Table -->
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
                            <th>Role</th>
                            <th>Ditangguhkan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($members)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                                    Tidak ada anggota yang ditangguhkan
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
                                                <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
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
                                        <span class="badge bg-secondary">
                                            <?= get_user_role_label($member['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php helper('app'); ?>
                                        <small class="text-muted">
                                            <?= format_date_indonesia($member['updated_at'], 'short') ?>
                                        </small>
                                        <?php if (!empty($member['notes'])): ?>
                                            <br>
                                            <small class="text-danger">
                                                <i class="fas fa-sticky-note me-1"></i>Lihat catatan
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('admin/members/view/' . $member['id']) ?>" class="btn btn-outline-primary" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="POST" action="<?= base_url('admin/members/activate/' . $member['id']) ?>" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline-success" title="Aktifkan" onclick="return confirm('Aktifkan anggota ini kembali?')">
                                                    <i class="fas fa-check"></i> Aktifkan
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (!empty($members)): ?>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Menampilkan <?= count($members) ?> anggota ditangguhkan
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
