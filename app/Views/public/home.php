<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Hero Area
==============================-->
<div class="th-hero-wrapper hero-1" id="hero">
    <div class="hero-inner">
        <div class="container">
            <div class="hero-style1">
                <span class="sub-title" data-ani="slideinup" data-ani-delay="0.1s">
                    <i class="fal fa-book me-2"></i> Bersatu untuk Kesejahteraan Bersama
                </span>
                <h1 class="hero-title">
                    <span class="title1" data-ani="slideinup" data-ani-delay="0.2s">Selamat Datang di</span>
                    <span class="title2" data-ani="slideinup" data-ani-delay="0.3s">Serikat Pekerja Kampus</span>
                </h1>
                <p class="hero-text" data-ani="slideinup" data-ani-delay="0.4s">
                    Platform digital untuk pengelolaan keanggotaan, iuran, dan layanan serikat pekerja kampus di seluruh Indonesia
                </p>
                <div class="btn-group" data-ani="slideinup" data-ani-delay="0.5s">
                    <a href="<?= base_url('registrasi') ?>" class="th-btn">Bergabung Sekarang<i class="fa-regular fa-arrow-right ms-2"></i></a>
                    <a href="<?= base_url('tentang-kami') ?>" class="th-btn style3">Tentang Kami<i class="fa-regular fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
        <div class="hero-img" data-ani="slideinright" data-ani-delay="0.6s">
            <img src="<?= base_url('assets/img/hero/hero_thumb_1_1.png') ?>" alt="Hero Image">
        </div>
    </div>
</div>

<!--==============================
Feature Area
==============================-->
<div class="overflow-hidden space" id="about-sec">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-7 col-md-8">
                <div class="title-area text-center">
                    <span class="sub-title"><i class="fal fa-book me-2"></i> Mengapa Bergabung?</span>
                    <h2 class="sec-title">Keuntungan Menjadi Anggota SPK</h2>
                </div>
            </div>
        </div>
        <div class="row gy-4 justify-content-center">
            <div class="col-md-6 col-xl-4">
                <div class="feature-card">
                    <div class="feature-card-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="box-title">Perlindungan Hak Pekerja</h3>
                    <p class="feature-card_text">Kami memperjuangkan hak-hak Anda sebagai pekerja kampus dan memastikan kesejahteraan terlindungi</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="feature-card">
                    <div class="feature-card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="box-title">Komunitas Solid</h3>
                    <p class="feature-card_text">Bergabung dengan ribuan pekerja kampus di seluruh Indonesia dalam satu wadah yang solid</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="feature-card">
                    <div class="feature-card-icon">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <h3 class="box-title">Bantuan & Advokasi</h3>
                    <p class="feature-card_text">Dapatkan bantuan hukum, advokasi, dan dukungan dalam berbagai permasalahan ketenagakerjaan</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!--==============================
About Area
==============================-->
<div class="space" id="about-sec">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-6">
                <div class="img-box1">
                    <div class="img1">
                        <img src="<?= base_url('assets/img/normal/about_1_1.png') ?>" alt="About">
                    </div>
                    <div class="shape1">
                        <img src="<?= base_url('assets/img/normal/about_shape_1.png') ?>" alt="shape">
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="title-area mb-30">
                    <span class="sub-title"><i class="fal fa-book me-2"></i> Tentang Kami</span>
                    <h2 class="sec-title">Serikat Pekerja Kampus Indonesia</h2>
                </div>
                <p class="mt-n2 mb-25">
                    Serikat Pekerja Kampus adalah organisasi yang memperjuangkan hak dan kesejahteraan para pekerja di lingkungan kampus.
                    Kami berkomitmen untuk memberikan perlindungan, advokasi, dan layanan terbaik kepada seluruh anggota.
                </p>
                <div class="about-feature-wrap">
                    <div class="about-feature">
                        <div class="about-feature_icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="media-body">
                            <h3 class="about-feature_title">Sistem Keanggotaan Digital</h3>
                            <p class="about-feature_text">Pendaftaran dan pengelolaan keanggotaan yang mudah dan transparan</p>
                        </div>
                    </div>
                    <div class="about-feature">
                        <div class="about-feature_icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="media-body">
                            <h3 class="about-feature_title">Pengelolaan Iuran Online</h3>
                            <p class="about-feature_text">Sistem pembayaran iuran yang mudah, aman, dan dapat dipantau real-time</p>
                        </div>
                    </div>
                </div>
                <div class="btn-group mt-40">
                    <a href="<?= base_url('tentang-kami') ?>" class="th-btn">Selengkapnya<i class="fa-regular fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!--==============================
Counter Area
==============================-->
<div class="counter-area-1 space" data-bg-src="<?= base_url('assets/img/bg/counter-bg.png') ?>">
    <div class="container">
        <div class="row gy-40 justify-content-between">
            <div class="col-sm-6 col-xl-3 counter-card-wrap">
                <div class="counter-card">
                    <div class="media-body">
                        <h2 class="counter-card_number"><span class="counter-number">1700</span>+</h2>
                        <p class="counter-card_text">Anggota Aktif</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 counter-card-wrap">
                <div class="counter-card">
                    <div class="media-body">
                        <h2 class="counter-card_number"><span class="counter-number">50</span>+</h2>
                        <p class="counter-card_text">Kampus Tergabung</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 counter-card-wrap">
                <div class="counter-card">
                    <div class="media-body">
                        <h2 class="counter-card_number"><span class="counter-number">15</span>+</h2>
                        <p class="counter-card_text">Wilayah</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 counter-card-wrap">
                <div class="counter-card">
                    <div class="media-body">
                        <h2 class="counter-card_number"><span class="counter-number">10</span>+</h2>
                        <p class="counter-card_text">Tahun Pengalaman</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--==============================
CTA Area
==============================-->
<section class="cta-area-1 space">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-30 mb-lg-0">
                <div class="cta-card" data-bg-src="<?= base_url('assets/img/bg/cta-bg1-1.png') ?>">
                    <div class="title-area mb-40">
                        <span class="sub-title text-white"><i class="fal fa-book me-2"></i> Bergabunglah Bersama Kami</span>
                        <h2 class="sec-title text-white">Daftar Sebagai Anggota Baru</h2>
                    </div>
                    <a href="<?= base_url('registrasi') ?>" class="th-btn">Daftar Sekarang<i class="fa-regular fa-arrow-right ms-2"></i></a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="cta-card" data-bg-src="<?= base_url('assets/img/bg/cta-bg1-2.png') ?>">
                    <div class="title-area mb-40">
                        <span class="sub-title text-white"><i class="fal fa-book me-2"></i> Sudah Menjadi Anggota?</span>
                        <h2 class="sec-title text-white">Login ke Dashboard Anda</h2>
                    </div>
                    <a href="<?= base_url('login') ?>" class="th-btn style3">Login Sekarang<i class="fa-regular fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
