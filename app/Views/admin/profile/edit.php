<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Profil</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/profile') ?>">Profil</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('admin/profile/edit') ?>">
        <?= csrf_field() ?>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-user me-2"></i>Informasi Pribadi</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="full_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name"
                               value="<?= old('full_name', $member['full_name']) ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone_number" class="form-label">Nomor Telepon</label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number"
                               value="<?= old('phone_number', $member['phone_number'] ?? '') ?>"
                               placeholder="08xxxxxxxxxx">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="">- Pilih -</option>
                            <option value="L" <?= old('gender', $member['gender'] ?? '') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                            <option value="P" <?= old('gender', $member['gender'] ?? '') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="birth_date" class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="birth_date" name="birth_date"
                               value="<?= old('birth_date', $member['birth_date'] ?? '') ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="birth_place" class="form-label">Tempat Lahir</label>
                        <input type="text" class="form-control" id="birth_place" name="birth_place"
                               value="<?= old('birth_place', $member['birth_place'] ?? '') ?>"
                               placeholder="Contoh: Jakarta">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="address" class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" id="address" name="address" rows="3"
                                  placeholder="Jl. Nama Jalan No. XX, Kelurahan, Kecamatan, Kota"><?= old('address', $member['address'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('admin/profile') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
