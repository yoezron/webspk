<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Hero Slider Area
==============================-->
<div class="th-hero-wrapper hero-1" id="hero">
    <div class="swiper th-slider hero-slider-1" data-slider-options='{"effect":"fade"}'>
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide">
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

            <!-- Slide 2 -->
            <div class="swiper-slide">
                <div class="hero-inner">
                    <div class="container">
                        <div class="hero-style1">
                            <span class="sub-title" data-ani="slideinup" data-ani-delay="0.1s">
                                <i class="fal fa-users me-2"></i> Lebih dari <?= number_format($total_users) ?> Anggota
                            </span>
                            <h1 class="hero-title">
                                <span class="title1" data-ani="slideinup" data-ani-delay="0.2s">Memperjuangkan</span>
                                <span class="title2" data-ani="slideinup" data-ani-delay="0.3s">Hak Pekerja Kampus</span>
                            </h1>
                            <p class="hero-text" data-ani="slideinup" data-ani-delay="0.4s">
                                Bergabung dengan ribuan pekerja kampus di seluruh Indonesia dalam memperjuangkan hak dan kesejahteraan
                            </p>
                            <div class="btn-group" data-ani="slideinup" data-ani-delay="0.5s">
                                <a href="<?= base_url('registrasi') ?>" class="th-btn">Daftar Sekarang<i class="fa-regular fa-arrow-right ms-2"></i></a>
                                <a href="<?= base_url('login') ?>" class="th-btn style3">Login<i class="fa-regular fa-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="hero-img" data-ani="slideinright" data-ani-delay="0.6s">
                        <img src="<?= base_url('assets/img/hero/hero_thumb_1_1.png') ?>" alt="Hero Image">
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="swiper-slide">
                <div class="hero-inner">
                    <div class="container">
                        <div class="hero-style1">
                            <span class="sub-title" data-ani="slideinup" data-ani-delay="0.1s">
                                <i class="fal fa-handshake me-2"></i> Solidaritas & Advokasi
                            </span>
                            <h1 class="hero-title">
                                <span class="title1" data-ani="slideinup" data-ani-delay="0.2s">Sistem Keanggotaan</span>
                                <span class="title2" data-ani="slideinup" data-ani-delay="0.3s">Digital & Transparan</span>
                            </h1>
                            <p class="hero-text" data-ani="slideinup" data-ani-delay="0.4s">
                                Kelola keanggotaan, iuran, dan dokumen secara digital dengan sistem yang aman dan transparan
                            </p>
                            <div class="btn-group" data-ani="slideinup" data-ani-delay="0.5s">
                                <a href="<?= base_url('registrasi') ?>" class="th-btn">Mulai Bergabung<i class="fa-regular fa-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="hero-img" data-ani="slideinright" data-ani-delay="0.6s">
                        <img src="<?= base_url('assets/img/hero/hero_thumb_1_1.png') ?>" alt="Hero Image">
                    </div>
                </div>
            </div>
        </div>
        <button class="slider-arrow style3 slider-prev"><i class="far fa-arrow-left"></i></button>
        <button class="slider-arrow style3 slider-next"><i class="far fa-arrow-right"></i></button>
    </div>
</div>

<!--==============================
    Category Cards Carousel
==============================-->
<div class="space">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-7 col-md-8">
                <div class="title-area text-center">
                    <span class="sub-title"><i class="fal fa-book me-2"></i> Navigasi Cepat</span>
                    <h2 class="sec-title">Jelajahi Informasi</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="swiper th-slider category-slider" data-slider-options='{"slidesPerView":1,"spaceBetween":24,"breakpoints":{"576":{"slidesPerView":"2"},"768":{"slidesPerView":"3"},"992":{"slidesPerView":"4"},"1200":{"slidesPerView":"4"}}}'>
                    <div class="swiper-wrapper">
                        <!-- Card 1: Sejarah -->
                        <div class="swiper-slide">
                            <div class="category-card style2">
                                <div class="category-card-icon">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div class="category-card-content">
                                    <h3 class="category-card-title">
                                        <a href="<?= base_url('sejarah') ?>">Sejarah</a>
                                    </h3>
                                    <p class="category-card-text">Perjalanan SPK</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Manifesto -->
                        <div class="swiper-slide">
                            <div class="category-card style2">
                                <div class="category-card-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="category-card-content">
                                    <h3 class="category-card-title">
                                        <a href="<?= base_url('manifesto') ?>">Manifesto</a>
                                    </h3>
                                    <p class="category-card-text">Visi & Prinsip</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: Visi Misi -->
                        <div class="swiper-slide">
                            <div class="category-card style2">
                                <div class="category-card-icon">
                                    <i class="fas fa-bullseye"></i>
                                </div>
                                <div class="category-card-content">
                                    <h3 class="category-card-title">
                                        <a href="<?= base_url('visi-misi') ?>">Visi Misi</a>
                                    </h3>
                                    <p class="category-card-text">Tujuan Organisasi</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card 4: AD/ART -->
                        <div class="swiper-slide">
                            <div class="category-card style2">
                                <div class="category-card-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="category-card-content">
                                    <h3 class="category-card-title">
                                        <a href="<?= base_url('ad-art') ?>">AD/ART</a>
                                    </h3>
                                    <p class="category-card-text">Aturan Organisasi</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card 5: Pengurus -->
                        <div class="swiper-slide">
                            <div class="category-card style2">
                                <div class="category-card-icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="category-card-content">
                                    <h3 class="category-card-title">
                                        <a href="<?= base_url('pengurus') ?>">Pengurus</a>
                                    </h3>
                                    <p class="category-card-text">Struktur Kepengurusan</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card 6: Berita -->
                        <div class="swiper-slide">
                            <div class="category-card style2">
                                <div class="category-card-icon">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                                <div class="category-card-content">
                                    <h3 class="category-card-title">
                                        <a href="<?= base_url('berita') ?>">Berita</a>
                                    </h3>
                                    <p class="category-card-text">Informasi Terkini</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card 7: Pengaduan -->
                        <div class="swiper-slide">
                            <div class="category-card style2">
                                <div class="category-card-icon">
                                    <i class="fas fa-comment-alt"></i>
                                </div>
                                <div class="category-card-content">
                                    <h3 class="category-card-title">
                                        <a href="<?= base_url('pengaduan') ?>">Pengaduan</a>
                                    </h3>
                                    <p class="category-card-text">Layanan Pengaduan</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card 8: Bergabung -->
                        <div class="swiper-slide">
                            <div class="category-card style2">
                                <div class="category-card-icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div class="category-card-content">
                                    <h3 class="category-card-title">
                                        <a href="<?= base_url('registrasi') ?>">Bergabung</a>
                                    </h3>
                                    <p class="category-card-text">Daftar Anggota</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--==============================
    About Area
==============================-->
<div class="overflow-hidden space" id="about-sec" data-bg-src="<?= base_url('assets/img/bg/about-bg.png') ?>">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-6">
                <div class="img-box1">
                    <div class="img1">
                        <img src="<?= base_url('assets/img/normal/about_1_1.png') ?>" alt="About SPK">
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
                <div class="row g-4 mb-30">
                    <div class="col-sm-6">
                        <div class="counter-box">
                            <h2 class="counter-box_number">
                                <span class="counter-number"><?= number_format($total_users) ?></span>+
                            </h2>
                            <p class="counter-box_text">Anggota Aktif</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="counter-box">
                            <h2 class="counter-box_number">
                                <span class="counter-number"><?= number_format($total_wilayah) ?></span>+
                            </h2>
                            <p class="counter-box_text">Provinsi</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="counter-box">
                            <h2 class="counter-box_number">
                                <span class="counter-number"><?= number_format($total_kampus) ?></span>+
                            </h2>
                            <p class="counter-box_text">Kampus</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="counter-box">
                            <h2 class="counter-box_number">
                                <span class="counter-number"><?= number_format($total_kota) ?></span>+
                            </h2>
                            <p class="counter-box_text">Kota/Kabupaten</p>
                        </div>
                    </div>
                </div>
                <div class="btn-group">
                    <a href="<?= base_url('tentang-kami') ?>" class="th-btn">Selengkapnya<i class="fa-regular fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!--==============================
Feature Area
==============================-->
<div class="overflow-hidden space">
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
            <div class="col-md-6 col-xl-4">
                <div class="feature-card">
                    <div class="feature-card-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3 class="box-title">Pengembangan Kapasitas</h3>
                    <p class="feature-card_text">Akses ke pelatihan, workshop, dan program pengembangan kompetensi bagi anggota</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="feature-card">
                    <div class="feature-card-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3 class="box-title">Negosiasi Kolektif</h3>
                    <p class="feature-card_text">Kekuatan bernegosiasi bersama untuk kondisi kerja dan kesejahteraan yang lebih baik</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-4">
                <div class="feature-card">
                    <div class="feature-card-icon">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <h3 class="box-title">Sistem Digital</h3>
                    <p class="feature-card_text">Kemudahan akses layanan keanggotaan dan iuran melalui platform digital yang modern</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!--==============================
Blog/News Area
==============================-->
<?php if (!empty($all_posts)): ?>
<div class="space" data-bg-src="<?= base_url('assets/img/bg/blog-bg.png') ?>">
    <div class="container">
        <div class="row justify-content-lg-between justify-content-center align-items-end">
            <div class="col-lg-7 col-md-8">
                <div class="title-area text-center text-lg-start">
                    <span class="sub-title"><i class="fal fa-book me-2"></i> Berita & Artikel</span>
                    <h2 class="sec-title">Informasi Terbaru</h2>
                </div>
            </div>
            <div class="col-lg-auto col-md-4">
                <div class="sec-btn">
                    <a href="<?= base_url('berita') ?>" class="th-btn">Lihat Semua<i class="fa-solid fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
        <div class="row gy-4">
            <?php foreach (array_slice($all_posts, 0, 3) as $post): ?>
            <div class="col-md-6 col-xl-4">
                <div class="blog-card">
                    <div class="blog-img">
                        <img src="<?= base_url('uploads/posts/' . $post['gambar']) ?>" alt="<?= esc($post['judul_tulisan']) ?>">
                    </div>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <a href="#"><i class="fa-light fa-user"></i><?= esc($post['penulis']) ?></a>
                            <a href="#"><i class="fa-light fa-calendar"></i><?php helper('app'); echo format_date_indonesia($post['waktu_posting']) ?></a>
                        </div>
                        <h3 class="box-title">
                            <a href="<?= base_url('berita/' . $post['slug']) ?>"><?= esc($post['judul_tulisan']) ?></a>
                        </h3>
                        <a href="<?= base_url('berita/' . $post['slug']) ?>" class="th-btn style3">Baca Selengkapnya<i class="fa-regular fa-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!--==============================
Counter Area
==============================-->
<div class="counter-area-1 space" data-bg-src="<?= base_url('assets/img/bg/counter-bg.png') ?>">
    <div class="container">
        <div class="row gy-40 justify-content-between">
            <div class="col-sm-6 col-xl-3 counter-card-wrap">
                <div class="counter-card">
                    <div class="media-body">
                        <h2 class="counter-card_number"><span class="counter-number"><?= number_format($total_users) ?></span>+</h2>
                        <p class="counter-card_text">Anggota Aktif</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 counter-card-wrap">
                <div class="counter-card">
                    <div class="media-body">
                        <h2 class="counter-card_number"><span class="counter-number"><?= number_format($total_kampus) ?></span>+</h2>
                        <p class="counter-card_text">Kampus Tergabung</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 counter-card-wrap">
                <div class="counter-card">
                    <div class="media-body">
                        <h2 class="counter-card_number"><span class="counter-number"><?= number_format($total_wilayah) ?></span>+</h2>
                        <p class="counter-card_text">Provinsi</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 counter-card-wrap">
                <div class="counter-card">
                    <div class="media-body">
                        <h2 class="counter-card_number"><span class="counter-number"><?= number_format($total_kota) ?></span>+</h2>
                        <p class="counter-card_text">Kota/Kabupaten</p>
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
