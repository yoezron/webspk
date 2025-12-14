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
                <li>Pendaftaran - Step 4</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Registration Step 4
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
                    <div class="step-item completed">
                        <div class="step-number"><i class="fas fa-check"></i></div>
                        <div class="step-label">Data Pekerjaan</div>
                    </div>
                    <div class="step-item active">
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
                        <h2 class="sec-title mb-2">Pembayaran & Dokumen</h2>
                        <p class="mb-0">Langkah 4 dari 4: Lengkapi Pembayaran dan Upload Dokumen</p>
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

                    <!-- Payment Info Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-success">
                                <h5 class="mb-3"><i class="fas fa-check-circle me-2"></i> Informasi Pembayaran Pendaftaran</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Biaya Pendaftaran:</strong></p>
                                        <h4 class="text-success mb-3">
                                            <?php
                                            $amount = 0;
                                            if (isset($member['dues_rate_type'])) {
                                                if ($member['dues_rate_type'] === 'golongan') {
                                                    // Calculate based on golongan
                                                    $amount = 30000; // Default, will be calculated from DB
                                                } else {
                                                    // Calculate based on salary
                                                    $amount = 15000; // Default, will be calculated from DB
                                                }
                                            }
                                            ?>
                                            Rp <?= number_format($amount ?? 20000, 0, ',', '.') ?>
                                        </h4>
                                        <p class="small text-muted">* Biaya pendaftaran satu kali (non-refundable)</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Transfer ke:</strong></p>
                                        <p class="mb-1">Bank: <strong><?= env('app.paymentBankName', 'Bank BRI') ?></strong></p>
                                        <p class="mb-1">No. Rekening: <strong><?= env('app.paymentAccountNumber', '1234567890') ?></strong></p>
                                        <p class="mb-3">Atas Nama: <strong><?= env('app.paymentAccountName', 'Serikat Pekerja Kampus') ?></strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="<?= base_url('registrasi/step-4') ?>" method="POST" enctype="multipart/form-data" class="login-form">
                        <?= csrf_field() ?>

                        <div class="row">
                            <!-- Payment Proof Section -->
                            <div class="col-12 mb-3">
                                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-receipt me-2"></i> Bukti Pembayaran</h5>
                            </div>

                            <div class="col-12 mb-3">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Penting:</strong> Silakan transfer biaya pendaftaran terlebih dahulu, kemudian upload bukti transfer di bawah ini.
                                    Format file yang diterima: JPG, PNG, atau PDF (maksimal 2MB).
                                </div>
                            </div>

                            <!-- Payment Proof Upload -->
                            <div class="form-group col-12">
                                <label class="form-label">Upload Bukti Transfer <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="registration_payment_proof"
                                       accept=".jpg,.jpeg,.png,.pdf" required>
                                <small class="form-text text-muted">
                                    Upload screenshot atau foto bukti transfer. File maksimal 2MB.
                                </small>
                            </div>

                            <!-- Documents Section -->
                            <div class="col-12 mt-4 mb-3">
                                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-file-upload me-2"></i> Dokumen Pendukung</h5>
                            </div>

                            <div class="col-12 mb-3">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Informasi:</strong> Upload dokumen pendukung berikut untuk mempercepat proses verifikasi.
                                    Dokumen yang wajib: KTP. Dokumen lainnya dapat di-upload kemudian.
                                </div>
                            </div>

                            <!-- ID Card (KTP) -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Foto/Scan KTP <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="id_card_photo"
                                       accept=".jpg,.jpeg,.png,.pdf" required>
                                <small class="form-text text-muted">Wajib: Foto/scan KTP yang jelas</small>
                            </div>

                            <!-- Family Card (KK) -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Foto/Scan Kartu Keluarga</label>
                                <input type="file" class="form-control" name="family_card_photo"
                                       accept=".jpg,.jpeg,.png,.pdf">
                                <small class="form-text text-muted">Opsional: Untuk keperluan data keluarga</small>
                            </div>

                            <!-- SK Pengangkatan -->
                            <div class="form-group col-md-6">
                                <label class="form-label">SK Pengangkatan/Kontrak Kerja</label>
                                <input type="file" class="form-control" name="sk_pengangkatan_photo"
                                       accept=".jpg,.jpeg,.png,.pdf">
                                <small class="form-text text-muted">Opsional: SK atau kontrak kerja terakhir</small>
                            </div>

                            <!-- Profile Photo -->
                            <div class="form-group col-md-6">
                                <label class="form-label">Pas Foto</label>
                                <input type="file" class="form-control" name="profile_photo"
                                       accept=".jpg,.jpeg,.png">
                                <small class="form-text text-muted">Opsional: Foto formal ukuran 3x4 atau 4x6</small>
                            </div>

                            <!-- Agreement Section -->
                            <div class="col-12 mt-4 mb-3">
                                <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-file-contract me-2"></i> Persetujuan</h5>
                            </div>

                            <!-- AD/ART Agreement -->
                            <div class="form-group col-12">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="agreement_adart"
                                           name="agreement_accepted" value="1" required>
                                    <label class="form-check-label" for="agreement_adart">
                                        Saya telah membaca dan menyetujui <a href="<?= base_url('dokumen/adart') ?>" target="_blank">Anggaran Dasar dan Anggaran Rumah Tangga (AD/ART)</a> Serikat Pekerja Kampus
                                        <span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Privacy Policy Agreement -->
                            <div class="form-group col-12">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="agreement_privacy"
                                           name="privacy_accepted" value="1" required>
                                    <label class="form-check-label" for="agreement_privacy">
                                        Saya menyetujui <a href="<?= base_url('privasi') ?>" target="_blank">Kebijakan Privasi</a> dan pemberian data pribadi untuk keperluan administrasi keanggotaan
                                        <span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Info Box -->
                            <div class="col-12 mt-3">
                                <div class="card bg-light border-primary">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary">
                                            <i class="fas fa-lightbulb me-2"></i> Apa yang terjadi selanjutnya?
                                        </h6>
                                        <ol class="mb-0">
                                            <li>Setelah submit, Anda akan menerima email konfirmasi pendaftaran</li>
                                            <li>Tim admin akan memverifikasi pembayaran dan dokumen Anda (maks. 1x24 jam)</li>
                                            <li>Anda akan menerima email verifikasi untuk mengaktifkan akun</li>
                                            <li>Setelah disetujui admin, akun Anda akan aktif dan dapat mengakses seluruh fitur member</li>
                                            <li>Nomor anggota resmi akan diberikan setelah persetujuan</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-12 mt-4">
                                <div class="d-flex justify-content-between">
                                    <a href="<?= base_url('registrasi/step-3') ?>" class="th-btn style3">
                                        <i class="fa-regular fa-arrow-left me-2"></i> Kembali
                                    </a>
                                    <button type="submit" class="th-btn">
                                        <i class="fas fa-check-circle me-2"></i> Selesaikan Pendaftaran
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
