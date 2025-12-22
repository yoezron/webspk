<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Informasi Pekerjaan</h1>
            <span>Perbarui data pekerjaan Anda</span>
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
        <form method="POST" action="<?= base_url('member/profile/employment') ?>">
            <?= csrf_field() ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="material-icons-outlined">work</i>
                        Data Kepegawaian
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Employee ID (NIP) -->
                        <div class="col-md-6 mb-3">
                            <label for="employee_id" class="form-label">
                                NIP (Nomor Induk Pegawai)
                            </label>
                            <input type="text"
                                   class="form-control <?= session('errors.employee_id') ? 'is-invalid' : '' ?>"
                                   id="employee_id"
                                   name="employee_id"
                                   value="<?= old('employee_id', $member['employee_id']) ?>"
                                   placeholder="Masukkan NIP Anda">
                            <?php if (session('errors.employee_id')): ?>
                                <div class="invalid-feedback"><?= session('errors.employee_id') ?></div>
                            <?php endif; ?>
                            <small class="text-muted">NIP resmi dari institusi</small>
                        </div>

                        <!-- Position -->
                        <div class="col-md-6 mb-3">
                            <label for="position" class="form-label">
                                Jabatan
                            </label>
                            <input type="text"
                                   class="form-control <?= session('errors.position') ? 'is-invalid' : '' ?>"
                                   id="position"
                                   name="position"
                                   value="<?= old('position', $member['position']) ?>"
                                   placeholder="Contoh: Dosen, Staff Administrasi, Asisten">
                            <?php if (session('errors.position')): ?>
                                <div class="invalid-feedback"><?= session('errors.position') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Department -->
                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label">
                                Departemen / Unit Kerja
                            </label>
                            <input type="text"
                                   class="form-control <?= session('errors.department') ? 'is-invalid' : '' ?>"
                                   id="department"
                                   name="department"
                                   value="<?= old('department', $member['department']) ?>"
                                   placeholder="Contoh: Fakultas Teknik, Bagian Keuangan">
                            <?php if (session('errors.department')): ?>
                                <div class="invalid-feedback"><?= session('errors.department') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Join Date -->
                        <div class="col-md-6 mb-3">
                            <label for="join_date" class="form-label">
                                Tanggal Mulai Bekerja
                            </label>
                            <input type="date"
                                   class="form-control <?= session('errors.join_date') ? 'is-invalid' : '' ?>"
                                   id="join_date"
                                   name="join_date"
                                   value="<?= old('join_date', $member['join_date']) ?>">
                            <?php if (session('errors.join_date')): ?>
                                <div class="invalid-feedback"><?= session('errors.join_date') ?></div>
                            <?php endif; ?>
                            <small class="text-muted">Tanggal pertama kali bekerja di institusi</small>
                        </div>

                        <!-- Work Status -->
                        <div class="col-md-6 mb-3">
                            <label for="work_status" class="form-label">
                                Status Kepegawaian
                            </label>
                            <select class="form-select <?= session('errors.work_status') ? 'is-invalid' : '' ?>"
                                    id="work_status"
                                    name="work_status">
                                <option value="">- Pilih Status -</option>
                                <option value="permanent" <?= old('work_status', $member['work_status'] ?? '') == 'permanent' ? 'selected' : '' ?>>
                                    Tetap / PNS
                                </option>
                                <option value="contract" <?= old('work_status', $member['work_status'] ?? '') == 'contract' ? 'selected' : '' ?>>
                                    Kontrak
                                </option>
                                <option value="temporary" <?= old('work_status', $member['work_status'] ?? '') == 'temporary' ? 'selected' : '' ?>>
                                    Honorer / Tidak Tetap
                                </option>
                                <option value="part_time" <?= old('work_status', $member['work_status'] ?? '') == 'part_time' ? 'selected' : '' ?>>
                                    Paruh Waktu
                                </option>
                            </select>
                            <?php if (session('errors.work_status')): ?>
                                <div class="invalid-feedback"><?= session('errors.work_status') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Education Level -->
                        <div class="col-md-6 mb-3">
                            <label for="education_level" class="form-label">
                                Pendidikan Terakhir
                            </label>
                            <select class="form-select <?= session('errors.education_level') ? 'is-invalid' : '' ?>"
                                    id="education_level"
                                    name="education_level">
                                <option value="">- Pilih Pendidikan -</option>
                                <option value="SMA" <?= old('education_level', $member['education_level'] ?? '') == 'SMA' ? 'selected' : '' ?>>SMA/SMK</option>
                                <option value="D3" <?= old('education_level', $member['education_level'] ?? '') == 'D3' ? 'selected' : '' ?>>Diploma (D3)</option>
                                <option value="S1" <?= old('education_level', $member['education_level'] ?? '') == 'S1' ? 'selected' : '' ?>>Sarjana (S1)</option>
                                <option value="S2" <?= old('education_level', $member['education_level'] ?? '') == 'S2' ? 'selected' : '' ?>>Magister (S2)</option>
                                <option value="S3" <?= old('education_level', $member['education_level'] ?? '') == 'S3' ? 'selected' : '' ?>>Doktor (S3)</option>
                            </select>
                            <?php if (session('errors.education_level')): ?>
                                <div class="invalid-feedback"><?= session('errors.education_level') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="material-icons-outlined">info</i>
                        <strong>Catatan:</strong> Data pekerjaan ini digunakan untuk keperluan administrasi keanggotaan dan penghitungan iuran.
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
