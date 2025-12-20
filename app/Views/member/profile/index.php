<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Profil Saya</h1>
            <span>Kelola informasi profil Anda</span>
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

<!-- Profile Completion -->
<?php if ($completion['percentage'] < 100): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="material-icons-outlined">info</i>
                        <strong>Lengkapi Profil Anda (<?= number_format($completion['percentage'], 0) ?>% selesai)</strong>
                        <p class="mb-0 small mt-2">
                            Belum lengkap: <?= implode(', ', array_slice($completion['missing'], 0, 5)) ?>
                            <?= count($completion['missing']) > 5 ? ' dan lainnya' : '' ?>
                        </p>
                    </div>
                    <div class="progress" style="width: 200px; height: 25px;">
                        <div class="progress-bar bg-warning" role="progressbar"
                             style="width: <?= $completion['percentage'] ?>%"
                             aria-valuenow="<?= $completion['percentage'] ?>"
                             aria-valuemin="0" aria-valuemax="100">
                            <?= number_format($completion['percentage'], 0) ?>%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Profile Info & Photo -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <?php if (!empty($member['profile_photo'])): ?>
                        <img src="<?= base_url('uploads/profiles/' . $member['profile_photo']) ?>"
                             alt="Profile Photo"
                             class="rounded-circle"
                             style="width: 150px; height: 150px; object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center"
                             style="width: 150px; height: 150px;">
                            <i class="material-icons-outlined" style="font-size: 80px; color: white;">person</i>
                        </div>
                    <?php endif; ?>
                </div>

                <h4><?= esc($member['full_name']) ?></h4>
                <p class="text-muted"><?= esc($member['email']) ?></p>

                <div class="mb-3">
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

                <hr>

                <!-- Photo Upload -->
                <form method="post" action="<?= base_url('member/profile/upload-photo') ?>" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="btn btn-primary btn-sm w-100">
                            <i class="material-icons-outlined">upload</i> Upload Foto
                            <input type="file" name="photo" accept="image/*" class="d-none" onchange="this.form.submit()">
                        </label>
                    </div>
                </form>

                <?php if (!empty($member['profile_photo'])): ?>
                    <form method="post" action="<?= base_url('member/profile/delete-photo') ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                onclick="return confirm('Hapus foto profil?')">
                            <i class="material-icons-outlined">delete</i> Hapus Foto
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('member/profile/edit') ?>" class="btn btn-outline-primary btn-sm">
                        <i class="material-icons-outlined">edit</i> Edit Profil
                    </a>
                    <a href="<?= base_url('member/profile/employment') ?>" class="btn btn-outline-info btn-sm">
                        <i class="material-icons-outlined">work</i> Info Pekerjaan
                    </a>
                    <a href="<?= base_url('member/profile/change-password') ?>" class="btn btn-outline-warning btn-sm">
                        <i class="material-icons-outlined">lock</i> Ubah Password
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
                            <?php if ($member['region_code']): ?>
                                <span class="badge badge-primary">
                                    <?= esc($member['region_code']) ?>
                                </span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
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

        <!-- Recent Activities -->
        <?php if (!empty($recent_activities)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="material-icons-outlined">history</i>
                        Aktivitas Terakhir
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <span><?= esc($activity['description']) ?></span>
                                    <small class="text-muted">
                                        <?= date('d M Y, H:i', strtotime($activity['created_at'])) ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
