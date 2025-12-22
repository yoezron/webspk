<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="page-content">
    <div class="page-info">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/profile') ?>">Profil</a></li>
                <li class="breadcrumb-item active">Edit Profil</li>
            </ol>
        </nav>
    </div>

    <div class="main-wrapper">
        <?php if (session()->has('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                <strong>Error!</strong>
                <ul class="mb-0">
                    <?php foreach (session('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5><i class="material-icons-outlined">edit</i> Edit Profil Lengkap</h5>
                <p class="text-muted small mb-0">Lengkapi semua data untuk mengaktifkan akses penuh fitur keanggotaan</p>
            </div>
            
            <form action="<?= base_url('admin/profile/edit') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab-personal">
                                <i class="material-icons-outlined">person</i> Data Pribadi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-address">
                                <i class="material-icons-outlined">home</i> Alamat
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-emergency">
                                <i class="material-icons-outlined">contact_phone</i> Kontak Darurat
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-work">
                                <i class="material-icons-outlined">work</i> Pekerjaan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-salary">
                                <i class="material-icons-outlined">payments</i> Gaji
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-banking">
                                <i class="material-icons-outlined">account_balance</i> Perbankan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-education">
                                <i class="material-icons-outlined">school</i> Pendidikan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-documents">
                                <i class="material-icons-outlined">description</i> Dokumen
                            </a>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content mt-4">
                        
                        <!-- Personal Data Tab -->
                        <div id="tab-personal" class="tab-pane fade show active">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="full_name" value="<?= old('full_name', $member['full_name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">NIK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="identity_number" value="<?= old('identity_number', $member['identity_number'] ?? '') ?>" maxlength="16" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-select" name="gender" required>
                                        <option value="">- Pilih -</option>
                                        <option value="L" <?= old('gender', $member['gender'] ?? '') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                        <option value="P" <?= old('gender', $member['gender'] ?? '') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="birth_date" value="<?= old('birth_date', $member['birth_date'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="birth_place" value="<?= old('birth_place', $member['birth_place'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="phone_number" value="<?= old('phone_number', $member['phone_number'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor Telepon Alternatif</label>
                                    <input type="tel" class="form-control" name="alt_phone_number" value="<?= old('alt_phone_number', $member['alt_phone_number'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Address Tab -->
                        <div id="tab-address" class="tab-pane fade">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="address" rows="3" required><?= old('address', $member['address'] ?? '') ?></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="province" value="<?= old('province', $member['province'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="city" value="<?= old('city', $member['city'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="district" value="<?= old('district', $member['district'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="postal_code" value="<?= old('postal_code', $member['postal_code'] ?? '') ?>" maxlength="5" required>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Contact Tab -->
                        <div id="tab-emergency" class="tab-pane fade">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Kontak Darurat <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="emergency_contact_name" value="<?= old('emergency_contact_name', $member['emergency_contact_name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Hubungan <span class="text-danger">*</span></label>
                                    <select class="form-select" name="emergency_contact_relation" required>
                                        <option value="">- Pilih -</option>
                                        <?php $relations = ['Suami', 'Istri', 'Ayah', 'Ibu', 'Anak', 'Saudara'];
                                        foreach ($relations as $rel): ?>
                                            <option value="<?= $rel ?>" <?= old('emergency_contact_relation', $member['emergency_contact_relation'] ?? '') == $rel ? 'selected' : '' ?>><?= $rel ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="emergency_contact_phone" value="<?= old('emergency_contact_phone', $member['emergency_contact_phone'] ?? '') ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Work Tab -->
                        <div id="tab-work" class="tab-pane fade">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Universitas <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="university_name" value="<?= old('university_name', $member['university_name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lokasi Kampus <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="campus_location" value="<?= old('campus_location', $member['campus_location'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Fakultas <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="faculty" value="<?= old('faculty', $member['faculty'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Departemen/Prodi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="department" value="<?= old('department', $member['department'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Unit Kerja</label>
                                    <input type="text" class="form-control" name="work_unit" value="<?= old('work_unit', $member['work_unit'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">NIP/NIK Pegawai <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="employee_id_number" value="<?= old('employee_id_number', $member['employee_id_number'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">NIDN</label>
                                    <input type="text" class="form-control" name="lecturer_id_number" value="<?= old('lecturer_id_number', $member['lecturer_id_number'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jabatan Akademik</label>
                                    <select class="form-select" name="academic_rank">
                                        <option value="">- Pilih -</option>
                                        <?php $ranks = ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Profesor', 'Tenaga Kependidikan'];
                                        foreach ($ranks as $rank): ?>
                                            <option value="<?= $rank ?>" <?= old('academic_rank', $member['academic_rank'] ?? '') == $rank ? 'selected' : '' ?>><?= $rank ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status Kepegawaian <span class="text-danger">*</span></label>
                                    <select class="form-select" name="employment_status" required>
                                        <option value="">- Pilih -</option>
                                        <?php $statuses = ['PNS', 'PPPK', 'Dosen Tetap', 'Dosen Tidak Tetap', 'Kontrak'];
                                        foreach ($statuses as $status): ?>
                                            <option value="<?= $status ?>" <?= old('employment_status', $member['employment_status'] ?? '') == $status ? 'selected' : '' ?>><?= $status ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Mulai Bekerja <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="work_start_date" value="<?= old('work_start_date', $member['work_start_date'] ?? '') ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Salary Tab -->
                        <div id="tab-salary" class="tab-pane fade">
                            <div class="alert alert-info">
                                <i class="material-icons-outlined">info</i> Data gaji digunakan untuk perhitungan iuran
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gaji Pokok <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" name="gross_salary" value="<?= old('gross_salary', $member['gross_salary'] ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tunjangan Fungsional</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" name="functional_allowance" value="<?= old('functional_allowance', $member['functional_allowance'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tunjangan Struktural</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" name="structural_allowance" value="<?= old('structural_allowance', $member['structural_allowance'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tunjangan Lainnya</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" name="other_allowances" value="<?= old('other_allowances', $member['other_allowances'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Banking Tab -->
                        <div id="tab-banking" class="tab-pane fade">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Bank <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="bank_name" value="<?= old('bank_name', $member['bank_name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor Rekening <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="bank_account_number" value="<?= old('bank_account_number', $member['bank_account_number'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Pemilik Rekening <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="bank_account_name" value="<?= old('bank_account_name', $member['bank_account_name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">NPWP</label>
                                    <input type="text" class="form-control" name="npwp_number" value="<?= old('npwp_number', $member['npwp_number'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">BPJS Ketenagakerjaan</label>
                                    <input type="text" class="form-control" name="bpjs_tk_number" value="<?= old('bpjs_tk_number', $member['bpjs_tk_number'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">BPJS Kesehatan</label>
                                    <input type="text" class="form-control" name="bpjs_kes_number" value="<?= old('bpjs_kes_number', $member['bpjs_kes_number'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Education Tab -->
                        <div id="tab-education" class="tab-pane fade">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Pendidikan Terakhir <span class="text-danger">*</span></label>
                                    <select class="form-select" name="education_level" required>
                                        <option value="">- Pilih -</option>
                                        <?php $levels = ['SMA/SMK', 'D3', 'S1', 'S2', 'S3'];
                                        foreach ($levels as $level): ?>
                                            <option value="<?= $level ?>" <?= old('education_level', $member['education_level'] ?? '') == $level ? 'selected' : '' ?>><?= $level ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tahun Lulus <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="graduation_year" value="<?= old('graduation_year', $member['graduation_year'] ?? '') ?>" min="1950" max="<?= date('Y') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Institusi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="institution_name" value="<?= old('institution_name', $member['institution_name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bidang Studi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="field_of_study" value="<?= old('field_of_study', $member['field_of_study'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Sertifikasi</label>
                                    <textarea class="form-control" name="certifications" rows="3"><?= old('certifications', $member['certifications'] ?? '') ?></textarea>
                                    <small class="text-muted">Daftar sertifikasi (pisahkan dengan koma)</small>
                                </div>
                            </div>
                        </div>

                        <!-- Documents Tab -->
                        <div id="tab-documents" class="tab-pane fade">
                            <div class="alert alert-warning">
                                <i class="material-icons-outlined">warning</i> Upload dokumen (JPG, PNG, PDF - Maks. 2MB)
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">KTP <span class="text-danger">*</span></label>
                                    <?php if (!empty($member['id_card_photo'])): ?>
                                        <div class="mb-2">
                                            <a href="<?= base_url('uploads/documents/' . $member['id_card_photo']) ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="material-icons-outlined">visibility</i> Lihat
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="id_card_photo" accept="image/*,.pdf">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kartu Keluarga <span class="text-danger">*</span></label>
                                    <?php if (!empty($member['family_card_photo'])): ?>
                                        <div class="mb-2">
                                            <a href="<?= base_url('uploads/documents/' . $member['family_card_photo']) ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="material-icons-outlined">visibility</i> Lihat
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="family_card_photo" accept="image/*,.pdf">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">SK Pengangkatan <span class="text-danger">*</span></label>
                                    <?php if (!empty($member['sk_pengangkatan_photo'])): ?>
                                        <div class="mb-2">
                                            <a href="<?= base_url('uploads/documents/' . $member['sk_pengangkatan_photo']) ?>" target="_blank" class="btn btn-sm btn-info">
                                                <i class="material-icons-outlined">visibility</i> Lihat
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="sk_pengangkatan_photo" accept="image/*,.pdf">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('admin/profile') ?>" class="btn btn-secondary">
                            <i class="material-icons-outlined">arrow_back</i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="material-icons-outlined">save</i> Simpan Semua Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
