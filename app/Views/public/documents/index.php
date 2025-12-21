<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Page Banner
==============================-->
<div class="breadcumb-wrapper" style="background-image: url('<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>');">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title"><?= $doc_type === 'publikasi' ? 'Publikasi SPK' : 'Regulasi Terkait' ?></h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li><?= $doc_type === 'publikasi' ? 'Publikasi' : 'Regulasi' ?></li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Documents Section
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
            <!-- Documents List -->
            <div class="col-lg-9">

                <!-- Search Bar -->
                <div class="mb-4">
                    <form action="<?= base_url('documents/search') ?>" method="get" class="search-form-wrapper">
                        <div class="input-group">
                            <input type="text"
                                   name="q"
                                   class="form-control"
                                   placeholder="Cari dokumen..."
                                   value="<?= $keyword ?? '' ?>">
                            <input type="hidden" name="type" value="<?= $doc_type ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </form>
                </div>

                <?php if (!empty($documents) && count($documents) > 0): ?>

                    <!-- Documents List -->
                    <div class="document-list">
                        <?php foreach ($documents as $doc): ?>
                            <div class="document-item card mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-1 text-center">
                                            <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                        </div>
                                        <div class="col-md-8">
                                            <h5 class="mb-2">
                                                <a href="<?= base_url('documents/preview/' . $doc['id']) ?>"
                                                   target="_blank"
                                                   class="text-decoration-none">
                                                    <?= esc($doc['title']) ?>
                                                </a>
                                            </h5>

                                            <?php if (!empty($doc['description'])): ?>
                                                <p class="text-muted mb-2">
                                                    <?= esc(substr(strip_tags($doc['description']), 0, 200)) ?><?= strlen($doc['description']) > 200 ? '...' : '' ?>
                                                </p>
                                            <?php endif; ?>

                                            <div class="document-meta">
                                                <?php if (!empty($doc['category_name'])): ?>
                                                    <span class="badge bg-secondary me-2">
                                                        <i class="fas fa-folder"></i> <?= esc($doc['category_name']) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <span class="text-muted me-3">
                                                    <i class="far fa-calendar"></i>
                                                    <?= date('d M Y', strtotime($doc['published_at'])) ?>
                                                </span>
                                                <span class="text-muted me-3">
                                                    <i class="far fa-file"></i>
                                                    <?= number_format($doc['file_size'] / 1024, 2) ?> KB
                                                </span>
                                                <span class="text-muted">
                                                    <i class="far fa-download"></i>
                                                    <?= $doc['download_count'] ?? 0 ?> unduhan
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <a href="<?= base_url('documents/preview/' . $doc['id']) ?>"
                                               class="btn btn-outline-primary btn-sm mb-2 w-100"
                                               target="_blank">
                                                <i class="far fa-eye"></i> Preview
                                            </a>
                                            <a href="<?= base_url('documents/download/' . $doc['id']) ?>"
                                               class="btn btn-primary btn-sm w-100">
                                                <i class="far fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Belum ada dokumen <?= $doc_type ?> yang dipublikasikan.
                    </div>
                <?php endif; ?>

            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
                <aside class="sidebar-area">

                    <!-- Document Type Filter -->
                    <div class="widget">
                        <h3 class="widget_title">Jenis Dokumen</h3>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="<?= base_url('publikasi') ?>"
                                   class="<?= $doc_type === 'publikasi' ? 'fw-bold text-primary' : '' ?>">
                                    <i class="fas fa-book"></i> Publikasi SPK
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('regulasi') ?>"
                                   class="<?= $doc_type === 'regulasi' ? 'fw-bold text-primary' : '' ?>">
                                    <i class="fas fa-gavel"></i> Regulasi & UU
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Categories Filter -->
                    <?php if (!empty($categories) && count($categories) > 0): ?>
                        <div class="widget">
                            <h3 class="widget_title">Kategori</h3>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <a href="<?= base_url($doc_type) ?>"
                                       class="<?= empty($current_category) ? 'fw-bold text-primary' : '' ?>">
                                        <i class="fas fa-th"></i> Semua Kategori
                                    </a>
                                </li>
                                <?php foreach ($categories as $category): ?>
                                    <li class="mb-2">
                                        <a href="<?= base_url($doc_type . '?category=' . $category['id']) ?>"
                                           class="<?= (isset($current_category) && $current_category == $category['id']) ? 'fw-bold text-primary' : '' ?>">
                                            <i class="fas fa-folder"></i> <?= esc($category['name']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Help Widget -->
                    <div class="widget bg-light p-3">
                        <h5 class="widget_title">Butuh Bantuan?</h5>
                        <p class="small text-muted">
                            Jika Anda mengalami kesulitan dalam mengunduh dokumen,
                            silakan hubungi kami melalui halaman kontak.
                        </p>
                        <a href="<?= base_url('contact') ?>" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-envelope"></i> Hubungi Kami
                        </a>
                    </div>

                </aside>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
