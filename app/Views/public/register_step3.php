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
                <li>Pendaftaran - Step 3</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Registration Step 3
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
                    <div class="step-item completed">
                        <div class="step-number"><i class="fas fa-check"></i></div>
                        <div class="step-label">Data Pribadi</div>
                    </div>
                    <div class="step-item active">
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
                        <h2 class="sec-title mb-2">Data Pekerjaan</h2>
                        <p class="mb-0">Langkah 3 dari 4: Lengkapi Data Pekerjaan dan Kepegawaian</p>
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

                    <form action="<?= base_url('registrasi/step-3') ?>" method="POST" class="login-form">
                        <?= csrf_field() ?>

                        <div class="row">
                            <!-- Institution Section -->
                            <div class="col-12 mb-3">
                                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-university me-2"></i> Data Institusi</h5>
                            </div>

                            <!-- Campus Location -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Lokasi Kampus <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($validation) && $validation->hasError('campus_location') ? 'is-invalid' : '' ?>"
                                       name="campus_location" placeholder="Contoh: Kampus Pusat, Kampus 2"
                                       value="<?= old('campus_location') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('campus_location')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('campus_location') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Faculty -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Fakultas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($validation) && $validation->hasError('faculty') ? 'is-invalid' : '' ?>"
                                       name="faculty" placeholder="Nama fakultas"
                                       value="<?= old('faculty') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('faculty')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('faculty') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Department -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Jurusan/Program Studi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($validation) && $validation->hasError('department') ? 'is-invalid' : '' ?>"
                                       name="department" placeholder="Nama jurusan/prodi"
                                       value="<?= old('department') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('department')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('department') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Work Unit -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Unit Kerja</label>
                                <input type="text" class="form-control" name="work_unit"
                                       placeholder="Contoh: Laboratorium, Perpustakaan (opsional)"
                                       value="<?= old('work_unit') ?>">
                            </div>

                            <!-- Employment Data Section -->
                            <div class="col-12 mt-4 mb-3">
                                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-id-card me-2"></i> Data Kepegawaian</h5>
                            </div>

                            <!-- Employee ID -->
                            <div class="form-group col-md-6">
                                <label class="form-label">NIP/NIK</label>
                                <input type="text" class="form-control" name="employee_id_number"
                                       placeholder="Nomor Induk Pegawai"
                                       value="<?= old('employee_id_number') ?>">
                            </div>

                            <!-- Lecturer ID (NIDN) -->
                            <div class="form-group col-md-6">
                                <label class="form-label">NIDN/NIDK</label>
                                <input type="text" class="form-control" name="lecturer_id_number"
                                       placeholder="Nomor Induk Dosen Nasional (khusus dosen)"
                                       value="<?= old('lecturer_id_number') ?>">
                            </div>

                            <!-- Academic Rank -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Jabatan Akademik <span class="text-danger">*</span></label>
                                <select class="form-select <?= isset($validation) && $validation->hasError('academic_rank') ? 'is-invalid' : '' ?>"
                                        name="academic_rank" required>
                                    <option value="">Pilih Jabatan Akademik</option>
                                    <option value="Tenaga Pengajar" <?= old('academic_rank') === 'Tenaga Pengajar' ? 'selected' : '' ?>>Tenaga Pengajar</option>
                                    <option value="Asisten Ahli" <?= old('academic_rank') === 'Asisten Ahli' ? 'selected' : '' ?>>Asisten Ahli</option>
                                    <option value="Lektor" <?= old('academic_rank') === 'Lektor' ? 'selected' : '' ?>>Lektor</option>
                                    <option value="Lektor Kepala" <?= old('academic_rank') === 'Lektor Kepala' ? 'selected' : '' ?>>Lektor Kepala</option>
                                    <option value="Guru Besar" <?= old('academic_rank') === 'Guru Besar' ? 'selected' : '' ?>>Guru Besar</option>
                                    <option value="Tendik/Staff" <?= old('academic_rank') === 'Tendik/Staff' ? 'selected' : '' ?>>Tenaga Kependidikan/Staff</option>
                                    <option value="Lainnya" <?= old('academic_rank') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                                <?php if (isset($validation) && $validation->hasError('academic_rank')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('academic_rank') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Employment Status -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Status Kepegawaian <span class="text-danger">*</span></label>
                                <select class="form-select <?= isset($validation) && $validation->hasError('employment_status') ? 'is-invalid' : '' ?>"
                                        name="employment_status" required>
                                    <option value="">Pilih Status Kepegawaian</option>
                                    <option value="PNS" <?= old('employment_status') === 'PNS' ? 'selected' : '' ?>>PNS</option>
                                    <option value="PPPK" <?= old('employment_status') === 'PPPK' ? 'selected' : '' ?>>PPPK</option>
                                    <option value="Tetap Non-PNS" <?= old('employment_status') === 'Tetap Non-PNS' ? 'selected' : '' ?>>Tetap Non-PNS</option>
                                    <option value="Kontrak/PKWT" <?= old('employment_status') === 'Kontrak/PKWT' ? 'selected' : '' ?>>Kontrak/PKWT</option>
                                    <option value="Dosen Luar Biasa" <?= old('employment_status') === 'Dosen Luar Biasa' ? 'selected' : '' ?>>Dosen Luar Biasa</option>
                                    <option value="Honorer" <?= old('employment_status') === 'Honorer' ? 'selected' : '' ?>>Honorer</option>
                                    <option value="Lainnya" <?= old('employment_status') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                                <?php if (isset($validation) && $validation->hasError('employment_status')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('employment_status') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Work Start Date -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Tanggal Mulai Bekerja <span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?= isset($validation) && $validation->hasError('work_start_date') ? 'is-invalid' : '' ?>"
                                       name="work_start_date" value="<?= old('work_start_date') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('work_start_date')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('work_start_date') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Dues Rate Section -->
                            <div class="col-12 mt-4 mb-3">
                                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-money-bill-wave me-2"></i> Iuran Bulanan</h5>
                            </div>

                            <div class="col-12 mb-3">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Informasi Iuran:</strong> Iuran bulanan dapat ditentukan berdasarkan Golongan atau Penghasilan.
                                    Pilih salah satu yang sesuai dengan kondisi Anda.
                                </div>
                            </div>

                            <!-- Dues Rate Type -->
                            <div class="form-group col-12">
                                <label class="form-label">Basis Iuran <span class="text-danger">*</span></label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="dues_rate_type"
                                                   id="dues_golongan" value="golongan"
                                                   <?= old('dues_rate_type') === 'golongan' ? 'checked' : '' ?> required>
                                            <label class="form-check-label" for="dues_golongan">
                                                <strong>Berdasarkan Golongan</strong><br>
                                                <small class="text-muted">Untuk PNS/ASN dengan golongan ruang</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="dues_rate_type"
                                                   id="dues_gaji" value="gaji"
                                                   <?= old('dues_rate_type') === 'gaji' ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="dues_gaji">
                                                <strong>Berdasarkan Penghasilan</strong><br>
                                                <small class="text-muted">Untuk non-PNS atau penghasilan variabel</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <?php if (isset($validation) && $validation->hasError('dues_rate_type')): ?>
                                    <div class="invalid-feedback d-block"><?= $validation->getError('dues_rate_type') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Tarif Iuran Serikat:</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-2"><strong>Berdasarkan Golongan:</strong></p>
                                                <ul class="small">
                                                    <li>Golongan I: Rp 20.000/bulan</li>
                                                    <li>Golongan II: Rp 30.000/bulan</li>
                                                    <li>Golongan III: Rp 35.000/bulan</li>
                                                    <li>Golongan IV: Rp 45.000/bulan</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-2"><strong>Berdasarkan Penghasilan:</strong></p>
                                                <ul class="small">
                                                    <li>Rp 0 - Rp 1.500.000: Rp 7.500/bulan</li>
                                                    <li>Rp 1.500.000 - Rp 3.000.000: Rp 15.000/bulan</li>
                                                    <li>Rp 3.000.001 - Rp 6.000.000: Rp 30.000/bulan</li>
                                                    <li>Di atas Rp 6.000.000: Rp 60.000/bulan</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-12 mt-4">
                                <div class="d-flex justify-content-between">
                                    <a href="<?= base_url('registrasi/step-2') ?>" class="th-btn style3">
                                        <i class="fa-regular fa-arrow-left me-2"></i> Kembali
                                    </a>
                                    <button type="submit" class="th-btn">
                                        Lanjutkan ke Pembayaran <i class="fa-regular fa-arrow-right ms-2"></i>
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
