<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Page Banner
==============================-->
<div class="breadcumb-wrapper" style="background-image: url('<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>');">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Hasil Pencarian Dokumen</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li>Pencarian</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Search Results Section
==============================-->
<section class="space">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">

                <!-- Search Bar -->
                <div class="mb-4">
                    <form action="<?= base_url('documents/search') ?>" method="get" class="search-form-wrapper">
                        <div class="input-group input-group-lg">
                            <input type="text"
                                   name="q"
                                   class="form-control"
                                   placeholder="Cari dokumen..."
                                   value="<?= esc($keyword) ?>"
                                   required>
                            <?php if (!empty($doc_type)): ?>
                                <input type="hidden" name="type" value="<?= $doc_type ?>">
                            <?php endif; ?>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </form>
                </div>

                <div class="mb-4">
                    <h4>Hasil pencarian untuk: "<?= esc($keyword) ?>"</h4>
                    <p class="text-muted">Ditemukan <?= count($documents) ?> dokumen</p>
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
                                                <span class="badge bg-<?= $doc['doc_type'] === 'publikasi' ? 'info' : 'warning' ?> me-2">
                                                    <?= ucfirst($doc['doc_type']) ?>
                                                </span>
                                                <span class="text-muted me-3">
                                                    <i class="far fa-calendar"></i>
                                                    <?= date('d M Y', strtotime($doc['published_at'] ?? $doc['created_at'])) ?>
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
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Tidak ditemukan dokumen dengan kata kunci "<strong><?= esc($keyword) ?></strong>".
                        <br><br>
                        Saran:
                        <ul class="mb-0">
                            <li>Periksa ejaan kata kunci Anda</li>
                            <li>Coba gunakan kata kunci yang lebih umum</li>
                            <li>Coba gunakan kata kunci yang berbeda</li>
                        </ul>
                    </div>

                    <div class="mt-4">
                        <h5>Atau jelajahi dokumen kami:</h5>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <a href="<?= base_url('publikasi') ?>" class="btn btn-lg btn-outline-primary w-100 mb-3">
                                    <i class="fas fa-book"></i> Publikasi SPK
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="<?= base_url('regulasi') ?>" class="btn btn-lg btn-outline-primary w-100 mb-3">
                                    <i class="fas fa-gavel"></i> Regulasi & UU
                                </a>
                            </div>
                        </div>
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
</section>

<?= $this->endSection() ?>
