<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Page Banner
==============================-->
<div class="breadcumb-wrapper" style="background-image: url('<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>');">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Hubungi Kami</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li>Kontak</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Contact Section
==============================-->
<section class="space">
    <div class="container">

        <?php if (session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Contact Form -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="contact-form-wrapper">
                    <h3 class="mb-4">Kirim Pesan</h3>

                    <form action="<?= base_url('contact/submit') ?>" method="post" class="contact-form ajax-contact">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <input type="text"
                                       name="name"
                                       class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>"
                                       placeholder="Nama Lengkap *"
                                       value="<?= old('name') ?>"
                                       required>
                                <?php if (session('errors.name')): ?>
                                    <div class="invalid-feedback"><?= session('errors.name') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 form-group">
                                <input type="email"
                                       name="email"
                                       class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
                                       placeholder="Alamat Email *"
                                       value="<?= old('email') ?>"
                                       required>
                                <?php if (session('errors.email')): ?>
                                    <div class="invalid-feedback"><?= session('errors.email') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-12 form-group">
                                <input type="text"
                                       name="subject"
                                       class="form-control <?= session('errors.subject') ? 'is-invalid' : '' ?>"
                                       placeholder="Subjek"
                                       value="<?= old('subject') ?>">
                                <?php if (session('errors.subject')): ?>
                                    <div class="invalid-feedback"><?= session('errors.subject') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-12 form-group">
                                <textarea name="message"
                                          rows="6"
                                          class="form-control <?= session('errors.message') ? 'is-invalid' : '' ?>"
                                          placeholder="Pesan Anda *"
                                          required><?= old('message') ?></textarea>
                                <?php if (session('errors.message')): ?>
                                    <div class="invalid-feedback"><?= session('errors.message') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-12 form-group mb-0">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i> Kirim Pesan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-4">
                <div class="contact-info-wrapper">
                    <h3 class="mb-4">Informasi Kontak</h3>

                    <div class="contact-info-card mb-3">
                        <div class="contact-info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-info-content">
                            <h6>Alamat</h6>
                            <p>Jl. Contoh No. 123<br>Jakarta 12345, Indonesia</p>
                        </div>
                    </div>

                    <div class="contact-info-card mb-3">
                        <div class="contact-info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-info-content">
                            <h6>Telepon</h6>
                            <p><a href="tel:+622112345678">+62 21 1234 5678</a></p>
                        </div>
                    </div>

                    <div class="contact-info-card mb-3">
                        <div class="contact-info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-info-content">
                            <h6>Email</h6>
                            <p><a href="mailto:info@serikatpekerkakampus.org">info@serikatpekerkakampus.org</a></p>
                        </div>
                    </div>

                    <div class="contact-info-card mb-3">
                        <div class="contact-info-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="contact-info-content">
                            <h6>WhatsApp</h6>
                            <p><a href="https://wa.me/6281234567890" target="_blank">+62 812 3456 7890</a></p>
                        </div>
                    </div>

                    <div class="contact-info-card">
                        <div class="contact-info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-info-content">
                            <h6>Jam Operasional</h6>
                            <p>Senin - Jumat<br>09:00 - 17:00 WIB</p>
                        </div>
                    </div>

                    <div class="social-media-links mt-4">
                        <h6>Ikuti Kami</h6>
                        <div class="social-icons">
                            <a href="#" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon twitter"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-icon instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-icon youtube"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Section (Optional) -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="map-wrapper" style="height: 400px; background: #e0e0e0; border-radius: 8px;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322283!2d106.8195613!3d-6.1751141!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5d2e764b12d%3A0x3d2ad6e1e0e9bcc8!2sMonumen%20Nasional!5e0!3m2!1sen!2sid!4v1234567890"
                            width="100%"
                            height="400"
                            style="border:0; border-radius: 8px;"
                            allowfullscreen=""
                            loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
