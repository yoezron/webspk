<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Page Banner
==============================-->
<div class="breadcumb-wrapper" style="background-image: url('<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>');">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Berita SPK</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li>Berita</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    News Listing Section
==============================-->
<section class="space">
    <div class="container">

        <?php if (session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- News Articles -->
            <div class="col-lg-8">
                <?php if (!empty($posts) && count($posts) > 0): ?>
                    <div class="row">
                        <?php foreach ($posts as $post): ?>
                            <div class="col-md-6 mb-4">
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
                        Belum ada berita yang dipublikasikan.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <aside class="sidebar-area">

                    <!-- Search Widget -->
                    <div class="widget widget_search">
                        <h3 class="widget_title">Cari Berita</h3>
                        <form class="search-form" action="<?= base_url('berita/search') ?>" method="get">
                            <input type="text" name="q" placeholder="Cari berita..." required>
                            <button type="submit"><i class="far fa-search"></i></button>
                        </form>
                    </div>

                    <!-- Recent Posts Widget -->
                    <?php if (!empty($posts) && count($posts) > 0): ?>
                        <div class="widget">
                            <h3 class="widget_title">Berita Terbaru</h3>
                            <div class="recent-post-wrap">
                                <?php foreach (array_slice($posts, 0, 5) as $recentPost): ?>
                                    <div class="recent-post">
                                        <?php if (!empty($recentPost['cover_image_url'])): ?>
                                            <div class="media-img">
                                                <a href="<?= base_url('berita/' . $recentPost['slug']) ?>">
                                                    <img src="<?= $recentPost['cover_image_url'] ?>"
                                                         alt="<?= esc($recentPost['title']) ?>">
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <div class="media-body">
                                            <div class="recent-post-meta">
                                                <a href="#"><i class="fal fa-calendar-days"></i><?= date('d M Y', strtotime($recentPost['published_at'])) ?></a>
                                            </div>
                                            <h4 class="post-title">
                                                <a class="text-inherit" href="<?= base_url('berita/' . $recentPost['slug']) ?>">
                                                    <?= esc(substr($recentPost['title'], 0, 60)) ?><?= strlen($recentPost['title']) > 60 ? '...' : '' ?>
                                                </a>
                                            </h4>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Archive Widget -->
                    <div class="widget">
                        <h3 class="widget_title">Arsip</h3>
                        <ul>
                            <li><a href="<?= base_url('berita/arsip/' . date('Y')) ?>">Tahun <?= date('Y') ?></a></li>
                            <li><a href="<?= base_url('berita/arsip/' . (date('Y') - 1)) ?>">Tahun <?= date('Y') - 1 ?></a></li>
                            <li><a href="<?= base_url('berita/arsip/' . (date('Y') - 2)) ?>">Tahun <?= date('Y') - 2 ?></a></li>
                        </ul>
                    </div>

                </aside>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
