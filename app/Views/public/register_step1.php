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
                <li>Pendaftaran</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Registration Area
==============================-->
<section class="space-top space-extra-bottom">
    <div class="container">
        <!-- Progress Steps -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="progress-steps">
                    <div class="step-item active">
                        <div class="step-number">1</div>
                        <div class="step-label">Akun</div>
                    </div>
                    <div class="step-item">
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
            <div class="col-xxl-8 col-xl-9 col-lg-10">
                <div class="th-login-form">
                    <div class="form-title text-center mb-4">
                        <h2 class="sec-title mb-2">Daftar Sebagai Anggota Baru</h2>
                        <p class="mb-0">Langkah 1 dari 4: Buat Akun Anda</p>
                    </div>

                    <?php if (session()->has('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= session('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= session('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('registrasi/step-1') ?>" method="POST" class="login-form">
                        <?= csrf_field() ?>

                        <div class="row">
                            <!-- Email -->
                            <div class="form-group col-12">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control <?= isset($validation) && $validation->hasError('email') ? 'is-invalid' : '' ?>"
                                       id="email" name="email" placeholder="Email Anda"
                                       value="<?= old('email') ?>" required>
                                <small class="form-text text-muted">Email akan digunakan untuk login dan komunikasi</small>
                                <?php if (isset($validation) && $validation->hasError('email')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('email') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Password -->
                            <div class="form-group col-md-6">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control <?= isset($validation) && $validation->hasError('password') ? 'is-invalid' : '' ?>"
                                       id="password" name="password" placeholder="Minimal 8 karakter" required>
                                <small class="form-text text-muted">Minimal 8 karakter dengan huruf besar, kecil, angka, dan simbol</small>
                                <?php if (isset($validation) && $validation->hasError('password')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('password') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Password Confirmation -->
                            <div class="form-group col-md-6">
                                <label for="password_confirm" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control <?= isset($validation) && $validation->hasError('password_confirm') ? 'is-invalid' : '' ?>"
                                       id="password_confirm" name="password_confirm" placeholder="Ulangi password" required>
                                <?php if (isset($validation) && $validation->hasError('password_confirm')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('password_confirm') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-12"><hr class="my-4"></div>

                            <!-- Full Name -->
                            <div class="form-group col-12">
                                <label for="full_name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($validation) && $validation->hasError('full_name') ? 'is-invalid' : '' ?>"
                                       id="full_name" name="full_name" placeholder="Nama lengkap sesuai KTP"
                                       value="<?= old('full_name') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('full_name')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('full_name') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Phone Number -->
                            <div class="form-group col-md-6">
                                <label for="phone_number" class="form-label">Nomor HP <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control <?= isset($validation) && $validation->hasError('phone_number') ? 'is-invalid' : '' ?>"
                                       id="phone_number" name="phone_number" placeholder="08xxxxxxxxxx"
                                       value="<?= old('phone_number') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('phone_number')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('phone_number') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- University Name -->
                            <div class="form-group col-md-6">
                                <label for="university_name" class="form-label">Nama Universitas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($validation) && $validation->hasError('university_name') ? 'is-invalid' : '' ?>"
                                       id="university_name" name="university_name" placeholder="Nama kampus tempat bekerja"
                                       value="<?= old('university_name') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('university_name')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('university_name') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Informasi:</strong> Setelah membuat akun, Anda akan melengkapi data pribadi, data pekerjaan,
                                    dan melakukan pembayaran pendaftaran. Proses pendaftaran akan diverifikasi oleh admin dalam waktu 1x24 jam.
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-btn col-12 mt-3">
                                <button type="submit" class="th-btn btn-fw">
                                    Lanjutkan ke Langkah 2 <i class="fa-regular fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <p class="mb-0">Sudah punya akun? <a href="<?= base_url('login') ?>" class="text-primary fw-semibold">Login di sini</a></p>
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

.step-label {
    font-size: 14px;
    color: #999;
}

.step-item.active .step-label {
    color: var(--theme-color);
    font-weight: 600;
}
</style>

<?= $this->endSection() ?>
