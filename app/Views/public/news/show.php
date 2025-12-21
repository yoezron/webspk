<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Page Banner
==============================-->
<div class="breadcumb-wrapper" style="background-image: url('<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>');">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title"><?= esc($post['title']) ?></h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li><a href="<?= base_url('/berita') ?>">Berita</a></li>
                <li><?= esc(substr($post['title'], 0, 30)) ?><?= strlen($post['title']) > 30 ? '...' : '' ?></li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    News Detail Section
==============================-->
<section class="space">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="blog-single">
                    <div class="blog-img">
                        <?php if (!empty($post['cover_image_url'])): ?>
                            <img src="<?= $post['cover_image_url'] ?>"
                                 alt="<?= esc($post['title']) ?>"
                                 class="w-100">
                        <?php else: ?>
                            <img src="<?= base_url('assets/img/blog/blog-default.jpg') ?>"
                                 alt="<?= esc($post['title']) ?>"
                                 class="w-100">
                        <?php endif; ?>
                    </div>

                    <div class="blog-content">
                        <div class="blog-meta">
                            <a href="#"><i class="far fa-user"></i>Oleh <?= esc($post['author_name'] ?? 'Admin') ?></a>
                            <a href="#"><i class="far fa-calendar"></i><?= date('d F Y', strtotime($post['published_at'])) ?></a>
                            <a href="#"><i class="far fa-eye"></i><?= $post['view_count'] ?? 0 ?> kali dibaca</a>
                        </div>

                        <h2 class="blog-title"><?= esc($post['title']) ?></h2>

                        <?php if (!empty($post['excerpt'])): ?>
                            <div class="blog-excerpt">
                                <p class="lead"><?= nl2br(esc($post['excerpt'])) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="blog-text">
                            <?= $post['content_html'] ?>
                        </div>

                        <!-- Share Buttons -->
                        <div class="share-links clearfix">
                            <span class="share-links-title">Bagikan:</span>
                            <ul class="social-links">
                                <li>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>"
                                       target="_blank"
                                       class="facebook">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode(current_url()) ?>&text=<?= urlencode($post['title']) ?>"
                                       target="_blank"
                                       class="twitter">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://wa.me/?text=<?= urlencode($post['title'] . ' - ' . current_url()) ?>"
                                       target="_blank"
                                       class="whatsapp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode(current_url()) ?>"
                                       target="_blank"
                                       class="linkedin">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Related Posts -->
                <?php if (!empty($related_posts) && count($related_posts) > 0): ?>
                    <div class="related-post-wrap mt-5">
                        <h3 class="h4 mb-4">Berita Terkait</h3>
                        <div class="row">
                            <?php foreach ($related_posts as $related): ?>
                                <div class="col-md-4">
                                    <div class="blog-card style2">
                                        <?php if (!empty($related['cover_image_url'])): ?>
                                            <div class="blog-img">
                                                <a href="<?= base_url('berita/' . $related['slug']) ?>">
                                                    <img src="<?= $related['cover_image_url'] ?>"
                                                         alt="<?= esc($related['title']) ?>"
                                                         class="w-100">
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        <div class="blog-content">
                                            <h4 class="blog-title">
                                                <a href="<?= base_url('berita/' . $related['slug']) ?>">
                                                    <?= esc(substr($related['title'], 0, 50)) ?><?= strlen($related['title']) > 50 ? '...' : '' ?>
                                                </a>
                                            </h4>
                                            <div class="blog-meta">
                                                <a href="#"><i class="far fa-calendar"></i><?= date('d M Y', strtotime($related['published_at'])) ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <aside class="sidebar-area">

                    <!-- Recent Posts Widget -->
                    <?php if (!empty($related_posts) && count($related_posts) > 0): ?>
                        <div class="widget">
                            <h3 class="widget_title">Berita Lainnya</h3>
                            <div class="recent-post-wrap">
                                <?php foreach ($related_posts as $recentPost): ?>
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

                    <!-- Back to News List -->
                    <div class="widget">
                        <a href="<?= base_url('/berita') ?>" class="btn btn-primary w-100">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Berita
                        </a>
                    </div>

                </aside>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
