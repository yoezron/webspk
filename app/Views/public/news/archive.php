<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Page Banner
==============================-->
<div class="breadcumb-wrapper" style="background-image: url('<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>');">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Arsip Berita Tahun <?= $year ?></h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li><a href="<?= base_url('/berita') ?>">Berita</a></li>
                <li>Arsip <?= $year ?></li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    News Archive Section
==============================-->
<section class="space">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-4">
                    <h3>Berita Tahun <?= $year ?></h3>
                    <p class="text-muted">Menampilkan semua berita yang dipublikasikan pada tahun <?= $year ?></p>
                </div>

                <?php if (!empty($posts) && count($posts) > 0): ?>
                    <div class="row">
                        <?php foreach ($posts as $post): ?>
                            <div class="col-md-4 mb-4">
                                <div class="blog-card">
                                    <?php if (!empty($post['cover_image_url'])): ?>
                                        <div class="blog-img">
                                            <img src="<?= $post['cover_image_url'] ?>"
                                                 alt="<?= esc($post['title']) ?>"
                                                 class="w-100">
                                            <div class="blog-date">
                                                <span><?= date('d', strtotime($post['published_at'])) ?></span>
                                                <?= date('M Y', strtotime($post['published_at'])) ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="blog-content">
                                        <div class="blog-meta">
                                            <a href="#"><i class="far fa-user"></i>By <?= esc($post['author_name'] ?? 'Admin') ?></a>
                                            <a href="#"><i class="far fa-eye"></i><?= $post['view_count'] ?? 0 ?> views</a>
                                        </div>

                                        <h3 class="blog-title">
                                            <a href="<?= base_url('berita/' . $post['slug']) ?>">
                                                <?= esc($post['title']) ?>
                                            </a>
                                        </h3>

                                        <?php if (!empty($post['excerpt'])): ?>
                                            <p class="blog-text">
                                                <?= esc(substr(strip_tags($post['excerpt']), 0, 120)) ?>...
                                            </p>
                                        <?php endif; ?>

                                        <a href="<?= base_url('berita/' . $post['slug']) ?>" class="link-btn">
                                            Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($pager): ?>
                        <div class="pagination-wrapper mt-4">
                            <?= $pager->links() ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Tidak ada berita yang dipublikasikan pada tahun <?= $year ?>.
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="<?= base_url('/berita') ?>" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Berita
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
