    <!--==============================
	Footer Area
    ==============================-->
    <footer class="footer-wrapper footer-layout1" data-bg-src="<?= base_url('assets/img/bg/footer-bg.png') ?>">
        <div class="shape-mockup footer-shape1 jump" data-left="60px" data-top="70px">
            <img src="<?= base_url('assets/img/normal/footer-bg-shape1.png') ?>" alt="shape">
        </div>
        <div class="shape-mockup footer-shape2 jump-reverse" data-right="80px" data-bottom="120px">
            <img src="<?= base_url('assets/img/normal/footer-bg-shape2.png') ?>" alt="shape">
        </div>
        <div class="footer-top">
            <div class="container">
                <div class="footer-contact-wrap">
                    <div class="footer-contact">
                        <div class="footer-contact_icon icon-btn">
                            <i class="fal fa-phone"></i>
                        </div>
                        <div class="media-body">
                            <p class="footer-contact_text">Hubungi Kami:</p>
                            <a href="tel:<?= esc($contact_phone ?? '+62123456789') ?>" class="footer-contact_link"><?= esc($contact_phone ?? '+62 123 456 789') ?></a>
                        </div>
                    </div>
                    <div class="divider"></div>
                    <div class="footer-contact">
                        <div class="footer-contact_icon icon-btn">
                            <i class="fal fa-envelope"></i>
                        </div>
                        <div class="media-body">
                            <p class="footer-contact_text">Email Kami:</p>
                            <a href="mailto:<?= esc($contact_email ?? 'info@spk.local') ?>" class="footer-contact_link"><?= esc($contact_email ?? 'info@spk.local') ?></a>
                        </div>
                    </div>
                    <div class="divider"></div>
                    <div class="footer-contact">
                        <div class="footer-contact_icon icon-btn">
                            <i class="fal fa-location-dot"></i>
                        </div>
                        <div class="media-body">
                            <p class="footer-contact_text">Alamat Kantor:</p>
                            <a href="<?= esc($office_maps_url ?? '#') ?>" class="footer-contact_link"><?= esc($office_address ?? 'Jl. Contoh No. 123, Jakarta') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-wrap" data-bg-src="<?= base_url('assets/img/bg/jiji.png') ?>">
            <div class="widget-area">
                <div class="container">
                    <div class="row justify-content-between">
                        <div class="col-md-6 col-xxl-3 col-xl-3">
                            <div class="widget footer-widget">
                                <div class="th-widget-about">
                                    <div class="about-logo">
                                        <a href="<?= base_url('/') ?>">
                                            <img src="<?= base_url('assets/img/logo/logo.png') ?>" alt="SPK">
                                        </a>
                                    </div>
                                    <p class="about-text">Serikat Pekerja Kampus adalah organisasi yang memperjuangkan hak dan kesejahteraan para pekerja di lingkungan kampus.</p>
                                    <div class="th-social">
                                        <h6 class="title text-white">IKUTI KAMI:</h6>
                                        <a href="<?= esc($social_facebook ?? '#') ?>"><i class="fab fa-facebook-f"></i></a>
                                        <a href="<?= esc($social_twitter ?? '#') ?>"><i class="fab fa-twitter"></i></a>
                                        <a href="<?= esc($social_linkedin ?? '#') ?>"><i class="fab fa-linkedin-in"></i></a>
                                        <a href="<?= esc($social_youtube ?? '#') ?>"><i class="fab fa-youtube"></i></a>
                                        <a href="<?= esc($social_instagram ?? '#') ?>"><i class="fab fa-instagram"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-auto">
                            <div class="widget widget_nav_menu footer-widget">
                                <h3 class="widget_title">Menu Utama</h3>
                                <div class="menu-all-pages-container">
                                    <ul class="menu">
                                        <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                                        <li><a href="<?= base_url('tentang-kami') ?>">Tentang Kami</a></li>
                                        <li><a href="<?= base_url('berita') ?>">Berita</a></li>
                                        <li><a href="<?= base_url('dokumen') ?>">Dokumen</a></li>
                                        <li><a href="<?= base_url('pengurus') ?>">Pengurus</a></li>
                                        <li><a href="<?= base_url('kontak') ?>">Kontak</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-auto">
                            <div class="widget widget_nav_menu footer-widget">
                                <h3 class="widget_title">Layanan</h3>
                                <div class="menu-all-pages-container">
                                    <ul class="menu">
                                        <li><a href="<?= base_url('registrasi') ?>">Pendaftaran Anggota</a></li>
                                        <li><a href="<?= base_url('login') ?>">Login Anggota</a></li>
                                        <li><a href="<?= base_url('dokumen') ?>">Dokumen & AD/ART</a></li>
                                        <li><a href="<?= base_url('faq') ?>">FAQ</a></li>
                                        <li><a href="<?= base_url('bantuan') ?>">Bantuan</a></li>
                                        <li><a href="<?= base_url('privasi') ?>">Kebijakan Privasi</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-xxl-3 col-xl-3">
                            <div class="widget newsletter-widget footer-widget">
                                <h3 class="widget_title">Berlangganan Newsletter</h3>
                                <p class="footer-text">Dapatkan informasi terbaru langsung di email Anda</p>
                                <form class="newsletter-form" action="<?= base_url('newsletter/subscribe') ?>" method="POST">
                                    <?= csrf_field() ?>
                                    <input class="form-control" type="email" name="email" placeholder="Email Anda" required>
                                    <button type="submit" class="th-btn style3">Berlangganan<i class="fa-regular fa-arrow-right ms-2"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright-wrap">
            <div class="container">
                <div class="row justify-content-between align-items-center">
                    <div class="col-md-6">
                        <p class="copyright-text">Copyright <i class="fal fa-copyright"></i> <?= date('Y') ?> <a href="<?= base_url('/') ?>">Serikat Pekerja Kampus</a>. All Rights Reserved.</p>
                    </div>
                    <div class="col-md-6 text-end d-none d-md-block">
                        <div class="footer-links">
                            <ul>
                                <li><a href="<?= base_url('privasi') ?>">Kebijakan Privasi</a></li>
                                <li><a href="<?= base_url('syarat-ketentuan') ?>">Syarat & Ketentuan</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!--==============================
    All Js File
    ============================== -->
    <!-- Jquery -->
    <script src="<?= base_url('assets/js/vendor/jquery-3.6.0.min.js') ?>"></script>
    <!-- Slick Slider -->
    <script src="<?= base_url('assets/js/slick.min.js') ?>"></script>
    <!-- Bootstrap -->
    <script src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
    <!-- Magnific Popup -->
    <script src="<?= base_url('assets/js/jquery.magnific-popup.min.js') ?>"></script>
    <!-- Counter Up -->
    <script src="<?= base_url('assets/js/jquery.counterup.min.js') ?>"></script>
    <!-- Range Slider -->
    <script src="<?= base_url('assets/js/jquery-ui.min.js') ?>"></script>
    <!-- Isotope Filter -->
    <script src="<?= base_url('assets/js/imagesloaded.pkgd.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/isotope.pkgd.min.js') ?>"></script>
    <!-- Nice Select -->
    <script src="<?= base_url('assets/js/nice-select.min.js') ?>"></script>
    <!-- Main Js File -->
    <script src="<?= base_url('assets/js/main.js') ?>"></script>

    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        // CSRF Token for AJAX requests
        const csrfToken = '<?= csrf_hash() ?>';
        const csrfTokenName = '<?= csrf_token() ?>';

        // Add CSRF token to all AJAX requests
        $.ajaxSetup({
            data: {
                [csrfTokenName]: csrfToken
            }
        });
    </script>

    </body>

    </html>