<!doctype html>
<html class="no-js" lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= $title ?? 'Serikat Pekerja Kampus' ?> - Sistem Informasi Keanggotaan</title>
    <meta name="author" content="Serikat Pekerja Kampus">
    <meta name="description" content="<?= $description ?? 'Sistem Informasi Keanggotaan Serikat Pekerja Kampus' ?>">
    <meta name="keywords" content="<?= $keywords ?? 'serikat pekerja, kampus, keanggotaan' ?>">
    <meta name="robots" content="INDEX,FOLLOW">

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="<?= csrf_token() ?>" content="<?= csrf_hash() ?>">

    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">

    <!--==============================
	  Google Fonts
	============================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600;700;800&family=Jost:wght@300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700&display=swap" rel="stylesheet">

    <!--==============================
	    All CSS File
	============================== -->
    <!-- Bootstrap -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <!-- Fontawesome Icon -->
    <link rel="stylesheet" href="<?= base_url('assets/css/fontawesome.min.css') ?>">
    <!-- Magnific Popup -->
    <link rel="stylesheet" href="<?= base_url('assets/css/magnific-popup.min.css') ?>">
    <!-- Slick Slider -->
    <link rel="stylesheet" href="<?= base_url('assets/css/slick.min.css') ?>">
    <!-- Nice Select -->
    <link rel="stylesheet" href="<?= base_url('assets/css/nice-select.min.css') ?>">
    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>

</head>

<body>

    <!--[if lte IE 9]>
    	<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
    <![endif]-->

    <!--==============================
     Preloader
    ==============================-->
    <div class="preloader">
        <button class="th-btn style3 preloaderCls">Batalkan Preloader</button>
        <div class="preloader-inner">
            <span class="loader"></span>
        </div>
    </div>

    <!--==============================
    Mobile Menu
    ============================== -->
    <div class="th-menu-wrapper">
        <div class="th-menu-area text-center">
            <button class="th-menu-toggle"><i class="fal fa-times"></i></button>
            <div class="mobile-logo">
                <a href="<?= base_url('/') ?>">
                    <img src="<?= base_url('assets/img/logo.png') ?>" alt="SPK">
                </a>
            </div>
            <div class="th-mobile-menu">
                <ul>
                    <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                    <li><a href="<?= base_url('tentang-kami') ?>">Tentang Kami</a></li>
                    <li class="menu-item-has-children">
                        <a href="#">Informasi</a>
                        <ul class="sub-menu">
                            <li><a href="<?= base_url('berita') ?>">Berita</a></li>
                            <li><a href="<?= base_url('dokumen') ?>">Dokumen</a></li>
                            <li><a href="<?= base_url('pengurus') ?>">Pengurus</a></li>
                        </ul>
                    </li>
                    <li><a href="<?= base_url('kontak') ?>">Kontak</a></li>
                    <?php if (!session()->has('user_id')): ?>
                        <li><a href="<?= base_url('login') ?>">Login</a></li>
                        <li><a href="<?= base_url('registrasi') ?>">Bergabung</a></li>
                    <?php else: ?>
                        <li><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                        <li><a href="<?= base_url('logout') ?>">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <!--==============================
	Header Area
    ==============================-->
    <header class="th-header header-layout1">
        <div class="header-top">
            <div class="container">
                <div class="row justify-content-center justify-content-lg-between align-items-center gy-2">
                    <div class="col-auto d-none d-lg-block">
                        <div class="header-links">
                            <ul>
                                <li><i class="far fa-phone"></i><a href="tel:<?= esc($contact_phone ?? '+62123456789') ?>"><?= esc($contact_phone ?? '+62 123 456 789') ?></a></li>
                                <li class="d-none d-xl-inline-block"><i class="far fa-envelope"></i><a href="mailto:<?= esc($contact_email ?? 'sekretariat@spk.or.id') ?>"><?= esc($contact_email ?? 'sekretariat@spk.or.id') ?></a></li>
                                <li><i class="far fa-clock"></i>Senin - Jumat: 08:00 - 16:00</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="header-links header-right">
                            <ul>
                                <li>
                                    <div class="header-social">
                                        <span class="social-title">Ikuti Kami:</span>
                                        <a href="<?= esc($social_facebook ?? '#') ?>"><i class="fab fa-facebook-f"></i></a>
                                        <a href="<?= esc($social_twitter ?? '#') ?>"><i class="fab fa-twitter"></i></a>
                                        <a href="<?= esc($social_linkedin ?? '#') ?>"><i class="fab fa-linkedin-in"></i></a>
                                        <a href="<?= esc($social_youtube ?? '#') ?>"><i class="fab fa-youtube"></i></a>
                                        <a href="<?= esc($social_instagram ?? '#') ?>"><i class="fab fa-instagram"></i></a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sticky-wrapper">
            <div class="menu-area">
                <div class="container">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-auto">
                            <div class="header-logo">
                                <a href="<?= base_url('/') ?>">
                                    <img src="<?= base_url('assets/img/logo.png') ?>" alt="SPK" style="max-width: 220px; height: auto;">
                                </a>
                            </div>
                        </div>
                        <div class="col-auto">
                            <nav class="main-menu d-none d-lg-inline-block">
                                <ul>
                                    <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                                    <li><a href="<?= base_url('tentang-kami') ?>">Tentang Kami</a></li>
                                    <li class="menu-item-has-children">
                                        <a href="#">Informasi</a>
                                        <ul class="sub-menu">
                                            <li><a href="<?= base_url('berita') ?>">Berita</a></li>
                                            <li><a href="<?= base_url('dokumen') ?>">Dokumen</a></li>
                                            <li><a href="<?= base_url('pengurus') ?>">Pengurus</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="<?= base_url('kontak') ?>">Kontak</a></li>
                                </ul>
                            </nav>
                            <button type="button" class="th-menu-toggle d-block d-lg-none"><i class="far fa-bars"></i></button>
                        </div>
                        <div class="col-auto d-none d-lg-block">
                            <div class="header-button">
                                <?php if (!session()->has('user_id')): ?>
                                    <a href="<?= base_url('login') ?>" class="th-btn ml-25">Login</a>
                                    <a href="<?= base_url('registrasi') ?>" class="th-btn style3 ml-25">Bergabung</a>
                                <?php else: ?>
                                    <span class="user-name mr-3">Halo, <?= esc(session()->get('user_name')) ?></span>
                                    <a href="<?= base_url('dashboard') ?>" class="th-btn ml-25">Dashboard</a>
                                    <a href="<?= base_url('logout') ?>" class="th-btn style3 ml-25">Logout</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>