<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Page Banner
==============================-->
<div class="breadcumb-wrapper" style="background-image: url('<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>');">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title"><?= esc($page['title']) ?></h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li><?= esc($page['title']) ?></li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Page Content Section
==============================-->
<section class="space">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="page-content">
                    <h2 class="page-title mb-4"><?= esc($page['title']) ?></h2>

                    <div class="page-body">
                        <?= $page['content_html'] ?>
                    </div>

                    <?php if (!empty($page['updated_at'])): ?>
                        <div class="page-meta mt-5 pt-4 border-top">
                            <p class="text-muted mb-0">
                                <i class="far fa-clock"></i>
                                Terakhir diperbarui: <?= date('d F Y, H:i', strtotime($page['updated_at'])) ?> WIB
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
