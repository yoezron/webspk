<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper" data-bg-src="<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Login</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li>Login</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Login Area
==============================-->
<section class="space-top space-extra-bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-6 col-xl-7 col-lg-8">
                <div class="th-login-form">
                    <div class="form-title text-center">
                        <h2 class="sec-title mb-2">Login ke Akun Anda</h2>
                        <p class="mb-4">Masukkan email dan password untuk mengakses dashboard</p>
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

                    <?php if (session()->has('info')): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <?= session('info') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('auth/login') ?>" method="POST" class="login-form ajax-contact">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="form-group col-12">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control <?= isset($validation) && $validation->hasError('email') ? 'is-invalid' : '' ?>"
                                       id="email" name="email" placeholder="Email Anda"
                                       value="<?= old('email') ?>" required>
                                <?php if (isset($validation) && $validation->hasError('email')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('email') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group col-12">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control <?= isset($validation) && $validation->hasError('password') ? 'is-invalid' : '' ?>"
                                       id="password" name="password" placeholder="Password Anda" required>
                                <?php if (isset($validation) && $validation->hasError('password')): ?>
                                    <div class="invalid-feedback">
                                        <?= $validation->getError('password') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                                        <label class="form-check-label" for="remember">
                                            Ingat Saya
                                        </label>
                                    </div>
                                    <a href="<?= base_url('forgot-password') ?>" class="text-primary">Lupa Password?</a>
                                </div>
                            </div>

                            <div class="form-btn col-12 mt-3">
                                <button type="submit" class="th-btn btn-fw">
                                    Login <i class="fa-regular fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <p class="mb-0">Belum punya akun? <a href="<?= base_url('registrasi') ?>" class="text-primary fw-semibold">Daftar Sekarang</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
