<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Edit Profil</h1>
            <span>Perbarui informasi pribadi Anda</span>
        </div>
    </div>
</div>

<!-- Flash Messages -->
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

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="material-icons-outlined">error</i>
        <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <form method="POST" action="<?= base_url('member/profile/edit') ?>">
            <?= csrf_field() ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="material-icons-outlined">person</i>
                        Informasi Pribadi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Full Name -->
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control <?= session('errors.full_name') ? 'is-invalid' : '' ?>"
                                   id="full_name"
                                   name="full_name"
                                   value="<?= old('full_name', $member['full_name']) ?>"
                                   required>
                            <?php if (session('errors.full_name')): ?>
                                <div class="invalid-feedback"><?= session('errors.full_name') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Phone Number -->
                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label">
                                Nomor Telepon
                            </label>
                            <input type="tel"
                                   class="form-control <?= session('errors.phone_number') ? 'is-invalid' : '' ?>"
                                   id="phone_number"
                                   name="phone_number"
                                   value="<?= old('phone_number', $member['phone_number']) ?>"
                                   placeholder="08xxxxxxxxxx">
                            <?php if (session('errors.phone_number')): ?>
                                <div class="invalid-feedback"><?= session('errors.phone_number') ?></div>
                            <?php endif; ?>
                            <small class="text-muted">Format: 08xxxxxxxxxx atau +628xxxxxxxxxx</small>
                        </div>

                        <!-- Gender -->
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">
                                Jenis Kelamin <span class="text-danger">*</span>
                            </label>
                            <select class="form-select <?= session('errors.gender') ? 'is-invalid' : '' ?>"
                                    id="gender"
                                    name="gender"
                                    required>
                                <option value="">- Pilih Jenis Kelamin -</option>
                                <option value="male" <?= old('gender', $member['gender']) == 'male' ? 'selected' : '' ?>>
                                    Laki-laki
                                </option>
                                <option value="female" <?= old('gender', $member['gender']) == 'female' ? 'selected' : '' ?>>
                                    Perempuan
                                </option>
                            </select>
                            <?php if (session('errors.gender')): ?>
                                <div class="invalid-feedback"><?= session('errors.gender') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label">
                                Tanggal Lahir
                            </label>
                            <input type="date"
                                   class="form-control <?= session('errors.date_of_birth') ? 'is-invalid' : '' ?>"
                                   id="date_of_birth"
                                   name="date_of_birth"
                                   value="<?= old('date_of_birth', $member['date_of_birth']) ?>">
                            <?php if (session('errors.date_of_birth')): ?>
                                <div class="invalid-feedback"><?= session('errors.date_of_birth') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Place of Birth -->
                        <div class="col-md-6 mb-3">
                            <label for="place_of_birth" class="form-label">
                                Tempat Lahir
                            </label>
                            <input type="text"
                                   class="form-control <?= session('errors.place_of_birth') ? 'is-invalid' : '' ?>"
                                   id="place_of_birth"
                                   name="place_of_birth"
                                   value="<?= old('place_of_birth', $member['place_of_birth']) ?>"
                                   placeholder="Contoh: Jakarta">
                            <?php if (session('errors.place_of_birth')): ?>
                                <div class="invalid-feedback"><?= session('errors.place_of_birth') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- National ID (NIK) -->
                        <div class="col-md-6 mb-3">
                            <label for="national_id" class="form-label">
                                NIK (Nomor Induk Kependudukan)
                            </label>
                            <input type="text"
                                   class="form-control <?= session('errors.national_id') ? 'is-invalid' : '' ?>"
                                   id="national_id"
                                   name="national_id"
                                   value="<?= old('national_id', $member['national_id']) ?>"
                                   placeholder="16 digit"
                                   maxlength="16"
                                   readonly>
                            <small class="text-muted">NIK tidak dapat diubah. Hubungi admin jika ada kesalahan.</small>
                        </div>

                        <!-- Address -->
                        <div class="col-md-12 mb-3">
                            <label for="address" class="form-label">
                                Alamat Lengkap
                            </label>
                            <textarea class="form-control <?= session('errors.address') ? 'is-invalid' : '' ?>"
                                      id="address"
                                      name="address"
                                      rows="3"
                                      placeholder="Jl. Nama Jalan No. XX, Kelurahan, Kecamatan, Kota/Kabupaten, Provinsi"><?= old('address', $member['address']) ?></textarea>
                            <?php if (session('errors.address')): ?>
                                <div class="invalid-feedback"><?= session('errors.address') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('member/profile') ?>" class="btn btn-secondary">
                            <i class="material-icons-outlined">arrow_back</i>
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="material-icons-outlined">save</i>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Phone number formatting
document.getElementById('phone_number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.startsWith('62')) {
        value = '0' + value.substring(2);
    }
    e.target.value = value;
});
</script>
<?= $this->endSection() ?>
