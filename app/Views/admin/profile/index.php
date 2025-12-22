<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Profil Saya</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Profil</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Profile Completion Alert -->
    <?php if ($completion['percentage'] < 100): ?>
        <div class="alert alert-warning alert-dismissible fade show">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Lengkapi Profil Anda (<?= number_format($completion['percentage'], 0) ?>% selesai)</strong>
                    <p class="mb-0 small mt-2">
                        Belum lengkap: <?= implode(', ', array_slice($completion['missing'], 0, 5)) ?>
                        <?php if (count($completion['missing']) > 5): ?>
                            dan <?= count($completion['missing']) - 5 ?> lainnya
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <div class="progress" style="width: 200px; height: 25px;">
                        <div class="progress-bar bg-warning" role="progressbar"
                             style="width: <?= $completion['percentage'] ?>%">
                            <?= number_format($completion['percentage'], 0) ?>%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Profile Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Profile Photo -->
                    <div class="mb-3">
                        <?php if (!empty($member['profile_photo'])): ?>
                            <img src="<?= base_url('uploads/profiles/' . $member['profile_photo']) ?>"
                                 alt="Profile Photo"
                                 class="rounded-circle img-thumbnail"
                                 style="width: 150px; height: 150px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center"
                                 style="width: 150px; height: 150px;">
                                <i class="fas fa-user fa-4x text-white"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <h4 class="mb-1"><?= esc($member['full_name']) ?></h4>
                    <p class="text-muted mb-2"><?= esc($member['email']) ?></p>

                    <!-- Role Badge -->
                    <div class="mb-3">
                        <?php
                        $roleBadge = match($member['role']) {
                            'super_admin' => 'badge bg-danger',
                            'admin' => 'badge bg-primary',
                            'coordinator' => 'badge bg-info',
                            'treasurer' => 'badge bg-success',
                            default => 'badge bg-secondary'
                        };
                        $roleText = match($member['role']) {
                            'super_admin' => 'Super Admin',
                            'admin' => 'Admin',
                            'coordinator' => 'Coordinator',
                            'treasurer' => 'Treasurer',
                            default => ucfirst($member['role'])
                        };
                        ?>
                        <span class="<?= $roleBadge ?> fs-6">
                            <i class="fas fa-shield-alt me-1"></i><?= $roleText ?>
                        </span>
                    </div>

                    <hr>

                    <!-- Photo Upload Form -->
                    <form method="POST" action="<?= base_url('admin/profile/upload-photo') ?>" enctype="multipart/form-data" id="photoUploadForm">
                        <?= csrf_field() ?>
                        <div class="mb-2">
                            <label class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-upload me-2"></i>Upload Foto Profil
                                <input type="file"
                                       name="photo"
                                       accept="image/*"
                                       class="d-none"
                                       onchange="previewAndSubmit(this)">
                            </label>
                        </div>
                    </form>

                    <?php if (!empty($member['profile_photo'])): ?>
                        <form method="POST" action="<?= base_url('admin/profile/delete-photo') ?>">
                            <?= csrf_field() ?>
                            <button type="submit"
                                    class="btn btn-outline-danger btn-sm w-100"
                                    onclick="return confirm('Hapus foto profil?')">
                                <i class="fas fa-trash me-2"></i>Hapus Foto
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?= base_url('admin/profile/edit') ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-2"></i>Edit Profil
                        </a>
                        <a href="<?= base_url('admin/profile/change-password') ?>" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-lock me-2"></i>Ubah Password
                        </a>
                        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="col-md-8">
            <!-- Personal Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-user me-2"></i>Informasi Pribadi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 200px;">Nama Lengkap</th>
                                <td><strong><?= esc($member['full_name']) ?></strong></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>
                                    <?= esc($member['email']) ?>
                                    <?php if ($member['email_verified_at']): ?>
                                        <span class="badge bg-success ms-2">
                                            <i class="fas fa-check-circle"></i> Terverifikasi
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>No. Telepon</th>
                                <td><?= esc($member['phone_number'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th>Jenis Kelamin</th>
                                <td>
                                    <?php if (!empty($member['gender'])): ?>
                                        <?= $member['gender'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Tempat, Tanggal Lahir</th>
                                <td>
                                    <?= esc($member['birth_place'] ?? '-') ?>,
                                    <?= !empty($member['birth_date']) ? date('d F Y', strtotime($member['birth_date'])) : '-' ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td><?= esc($member['address'] ?? '-') ?></td>
                            </tr>
                            <?php if (!empty($member['region_code'])): ?>
                                <tr>
                                    <th>Wilayah</th>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?= esc($member['region_code']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>Informasi Akun
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <th style="width: 200px;">Role</th>
                                <td>
                                    <span class="<?= $roleBadge ?>">
                                        <?= $roleText ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Status Keanggotaan</th>
                                <td>
                                    <?php
                                    $statusBadge = match($member['membership_status']) {
                                        'active' => 'badge bg-success',
                                        'candidate' => 'badge bg-warning',
                                        'suspended' => 'badge bg-danger',
                                        default => 'badge bg-secondary'
                                    };
                                    ?>
                                    <span class="<?= $statusBadge ?>">
                                        <?= ucfirst($member['membership_status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Bergabung Sejak</th>
                                <td><?= date('d F Y', strtotime($member['created_at'])) ?></td>
                            </tr>
                            <tr>
                                <th>Login Terakhir</th>
                                <td>
                                    <?php if (!empty($member['last_login_at'])): ?>
                                        <?= date('d F Y, H:i', strtotime($member['last_login_at'])) ?> WIB
                                    <?php else: ?>
                                        Belum pernah login
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function previewAndSubmit(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB!');
            input.value = '';
            return;
        }

        // Validate file type
        if (!file.type.match('image.*')) {
            alert('File harus berupa gambar!');
            input.value = '';
            return;
        }

        // Submit form
        if (confirm('Upload foto profil ini?')) {
            input.form.submit();
        }
    }
}
</script>
<?= $this->endSection() ?>
