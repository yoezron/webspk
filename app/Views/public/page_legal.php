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
    Legal Page Content
==============================-->
<section class="space">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="legal-page-content">
                    <h1 class="legal-page-title mb-4"><?= esc($page['title']) ?></h1>

                    <div class="legal-page-body">
                        <?= $page['content_html'] ?>
                    </div>

                    <?php if (!empty($page['updated_at'])): ?>
                        <div class="legal-page-footer mt-5 pt-4 border-top bg-light p-4 rounded">
                            <h6>Informasi Dokumen</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <strong>Terakhir Diperbarui:</strong>
                                    <?= date('d F Y', strtotime($page['updated_at'])) ?>
                                </li>
                                <li class="mb-2">
                                    <strong>Status:</strong>
                                    <span class="badge bg-success">Aktif</span>
                                </li>
                            </ul>
                            <p class="text-muted small mt-3 mb-0">
                                Dengan menggunakan layanan kami, Anda menyetujui ketentuan yang tertera dalam dokumen ini.
                            </p>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4">
                        <a href="javascript:history.back()" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
