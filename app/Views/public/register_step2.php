<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper" data-bg-src="<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Pendaftaran Anggota</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li>Pendaftaran - Step 2</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Registration Step 2
==============================-->
<section class="space-top space-extra-bottom">
    <div class="container">
        <!-- Progress Steps -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="progress-steps">
                    <div class="step-item completed">
                        <div class="step-number"><i class="fas fa-check"></i></div>
                        <div class="step-label">Akun</div>
                    </div>
                    <div class="step-item active">
                        <div class="step-number">2</div>
                        <div class="step-label">Data Pribadi</div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="step-label">Data Pekerjaan</div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">4</div>
                        <div class="step-label">Pembayaran</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xxl-10 col-xl-11">
                <div class="th-login-form">
                    <div class="form-title text-center mb-4">
                        <h2 class="sec-title mb-2">Data Pribadi</h2>
                        <p class="mb-0">Langkah 2 dari 4: Lengkapi Data Pribadi Anda</p>
                    </div>

                    <?php if (session()->has('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= session('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= session('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('registrasi/step-2') ?>" method="POST" class="login-form">
                        <?= csrf_field() ?>

                        <div class="row">
                            <!-- Demografis Section -->
                            <div class="col-12 mb-3">
                                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-user me-2"></i> Data Demografis</h5>
                            </div>

                            <!-- Gender -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                <select class="form-select <?= isset($validation) && $validation->hasError('gender') ? 'is-invalid' : '' ?>"
                                        name="gender" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L" <?= old('gender') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= old('gender') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                                <?php if (isset($validation) && $validation->hasError('gender')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('gender') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Birth Place -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($validation) && $validation->hasError('birth_place') ? 'is-invalid' : '' ?>"
                                       name="birth_place" placeholder="Kota tempat lahir"
                                       value="<?= old('birth_place') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('birth_place')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('birth_place') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Birth Date -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?= isset($validation) && $validation->hasError('birth_date') ? 'is-invalid' : '' ?>"
                                       name="birth_date" value="<?= old('birth_date') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('birth_date')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('birth_date') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Identity Number (KTP) -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Nomor KTP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($validation) && $validation->hasError('identity_number') ? 'is-invalid' : '' ?>"
                                       name="identity_number" placeholder="16 digit nomor KTP"
                                       value="<?= old('identity_number') ?>" maxlength="16" required>
                                <?php if (isset($validation) && $validation->hasError('identity_number')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('identity_number') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Address Section -->
                            <div class="col-12 mt-4 mb-3">
                                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-map-marker-alt me-2"></i> Alamat Lengkap</h5>
                            </div>

                            <!-- Full Address -->
                            <div class="form-group col-12">
                                <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea class="form-control <?= isset($validation) && $validation->hasError('address') ? 'is-invalid' : '' ?>"
                                          name="address" rows="3" placeholder="Jalan, RT/RW, Kelurahan" required><?= old('address') ?></textarea>
                                <?php if (isset($validation) && $validation->hasError('address')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('address') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Province -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($validation) && $validation->hasError('province') ? 'is-invalid' : '' ?>"
                                       name="province" placeholder="Nama provinsi"
                                       value="<?= old('province') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('province')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('province') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- City -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($validation) && $validation->hasError('city') ? 'is-invalid' : '' ?>"
                                       name="city" placeholder="Nama kota/kabupaten"
                                       value="<?= old('city') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('city')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('city') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- District -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($validation) && $validation->hasError('district') ? 'is-invalid' : '' ?>"
                                       name="district" placeholder="Nama kecamatan"
                                       value="<?= old('district') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('district')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('district') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Postal Code -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($validation) && $validation->hasError('postal_code') ? 'is-invalid' : '' ?>"
                                       name="postal_code" placeholder="5 digit kode pos"
                                       value="<?= old('postal_code') ?>" maxlength="5" required>
                                <?php if (isset($validation) && $validation->hasError('postal_code')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('postal_code') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Alternative Phone -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Nomor HP Alternatif</label>
                                <input type="tel" class="form-control" name="alt_phone_number"
                                       placeholder="Nomor HP cadangan (opsional)"
                                       value="<?= old('alt_phone_number') ?>">
                            </div>

                            <!-- Emergency Contact Section -->
                            <div class="col-12 mt-4 mb-3">
                                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-phone-alt me-2"></i> Kontak Darurat</h5>
                            </div>

                            <!-- Emergency Contact Name -->
                            <div class="form-group col-md-4">
                                <label class="form-label">Nama Kontak Darurat</label>
                                <input type="text" class="form-control" name="emergency_contact_name"
                                       placeholder="Nama lengkap"
                                       value="<?= old('emergency_contact_name') ?>">
                            </div>

                            <!-- Emergency Contact Relation -->
                            <div class="form-group col-md-4">
                                <label class="form-label">Hubungan</label>
                                <input type="text" class="form-control" name="emergency_contact_relation"
                                       placeholder="Contoh: Suami, Istri, Orang Tua"
                                       value="<?= old('emergency_contact_relation') ?>">
                            </div>

                            <!-- Emergency Contact Phone -->
                            <div class="form-group col-md-4">
                                <label class="form-label">Nomor HP Kontak Darurat</label>
                                <input type="tel" class="form-control" name="emergency_contact_phone"
                                       placeholder="Nomor HP yang dapat dihubungi"
                                       value="<?= old('emergency_contact_phone') ?>">
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-12 mt-4">
                                <div class="d-flex justify-content-between">
                                    <a href="<?= base_url('registrasi') ?>" class="th-btn style3">
                                        <i class="fa-regular fa-arrow-left me-2"></i> Kembali
                                    </a>
                                    <button type="submit" class="th-btn">
                                        Lanjutkan ke Step 3 <i class="fa-regular fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.progress-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    max-width: 600px;
    margin: 0 auto;
}

.progress-steps::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: #e0e0e0;
    z-index: 0;
}

.step-item {
    text-align: center;
    position: relative;
    z-index: 1;
    flex: 1;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    font-weight: 600;
    color: #999;
}

.step-item.active .step-number {
    background: var(--theme-color);
    border-color: var(--theme-color);
    color: #fff;
}

.step-item.completed .step-number {
    background: #28a745;
    border-color: #28a745;
    color: #fff;
}

.step-label {
    font-size: 14px;
    color: #999;
}

.step-item.active .step-label {
    color: var(--theme-color);
    font-weight: 600;
}

.step-item.completed .step-label {
    color: #28a745;
}
</style>

<?= $this->endSection() ?>
