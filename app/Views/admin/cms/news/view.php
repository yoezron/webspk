<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Berita</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/cms/news') ?>">Berita</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/cms/news/edit/' . $post['id']) ?>" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="<?= base_url('admin/cms/news') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Main Content -->
            <div class="card mb-4">
                <div class="card-body">
                    <!-- Status Badge -->
                    <?php
                    $statusClass = [
                        'draft' => 'warning',
                        'published' => 'success',
                        'archived' => 'secondary'
                    ];
                    $statusIcon = [
                        'draft' => 'fa-pen',
                        'published' => 'fa-check-circle',
                        'archived' => 'fa-archive'
                    ];
                    ?>
                    <div class="mb-3">
                        <span class="badge bg-<?= $statusClass[$post['status']] ?? 'secondary' ?> fs-6">
                            <i class="fas <?= $statusIcon[$post['status']] ?? 'fa-question' ?> me-2"></i>
                            <?= ucfirst($post['status']) ?>
                        </span>
                    </div>

                    <!-- Title -->
                    <h1 class="display-5 mb-3"><?= esc($post['title']) ?></h1>

                    <!-- Metadata -->
                    <div class="text-muted mb-4">
                        <i class="fas fa-user me-1"></i>
                        <span class="me-3"><?= esc($post['author_name'] ?? 'Unknown') ?></span>

                        <?php if ($post['status'] === 'published' && !empty($post['published_at'])): ?>
                            <i class="fas fa-calendar me-1"></i>
                            <span class="me-3"><?= date('d F Y, H:i', strtotime($post['published_at'])) ?></span>
                        <?php endif; ?>

                        <i class="fas fa-link me-1"></i>
                        <code><?= esc($post['slug']) ?></code>
                    </div>

                    <!-- Cover Image -->
                    <?php if (!empty($post['cover_image_path'])): ?>
                        <div class="mb-4">
                            <img src="<?= base_url('uploads/media/' . $post['cover_image_path']) ?>"
                                 alt="<?= esc($post['title']) ?>"
                                 class="img-fluid rounded shadow-sm">
                        </div>
                    <?php endif; ?>

                    <!-- Excerpt -->
                    <?php if (!empty($post['excerpt'])): ?>
                        <div class="alert alert-light border-start border-4 border-primary mb-4">
                            <strong><i class="fas fa-quote-left me-2"></i>Ringkasan:</strong><br>
                            <?= esc($post['excerpt']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Content -->
                    <div class="content-view">
                        <?= $post['content_html'] ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Info Panel -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Berita</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%;">
                                <i class="fas fa-hashtag me-1"></i>ID
                            </td>
                            <td><strong><?= $post['id'] ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">
                                <i class="fas fa-signal me-1"></i>Status
                            </td>
                            <td>
                                <span class="badge bg-<?= $statusClass[$post['status']] ?? 'secondary' ?>">
                                    <?= ucfirst($post['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">
                                <i class="fas fa-user me-1"></i>Penulis
                            </td>
                            <td><?= esc($post['author_name'] ?? 'Unknown') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">
                                <i class="fas fa-clock me-1"></i>Dibuat
                            </td>
                            <td><?= date('d M Y H:i', strtotime($post['created_at'])) ?></td>
                        </tr>
                        <?php if (!empty($post['updated_at'])): ?>
                            <tr>
                                <td class="text-muted">
                                    <i class="fas fa-sync me-1"></i>Diupdate
                                </td>
                                <td><?= date('d M Y H:i', strtotime($post['updated_at'])) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($post['status'] === 'published' && !empty($post['published_at'])): ?>
                            <tr>
                                <td class="text-muted">
                                    <i class="fas fa-calendar-check me-1"></i>Publikasi
                                </td>
                                <td><?= date('d M Y H:i', strtotime($post['published_at'])) ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <a href="<?= base_url('admin/cms/news/edit/' . $post['id']) ?>" class="btn btn-warning w-100 mb-2">
                        <i class="fas fa-edit me-2"></i>Edit Berita
                    </a>

                    <?php if ($post['status'] === 'published'): ?>
                        <a href="<?= base_url('berita/' . $post['slug']) ?>"
                           class="btn btn-info w-100 mb-2"
                           target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>Lihat di Website
                        </a>
                    <?php endif; ?>

                    <button type="button"
                            class="btn btn-danger w-100"
                            onclick="confirmDelete()">
                        <i class="fas fa-trash me-2"></i>Hapus Berita
                    </button>
                </div>
            </div>

            <!-- SEO Info (if applicable) -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-globe me-2"></i>SEO
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted d-block">Permalink:</small>
                        <code class="small"><?= base_url('berita/' . $post['slug']) ?></code>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block">Slug:</small>
                        <code class="small"><?= esc($post['slug']) ?></code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Form -->
<form id="deleteForm" method="POST" action="<?= base_url('admin/cms/news/delete/' . $post['id']) ?>" style="display: none;">
    <?= csrf_field() ?>
</form>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.content-view {
    font-size: 1.05rem;
    line-height: 1.8;
}

.content-view img {
    max-width: 100%;
    height: auto;
    margin: 20px 0;
}

.content-view h1, .content-view h2, .content-view h3 {
    margin-top: 30px;
    margin-bottom: 15px;
}

.content-view p {
    margin-bottom: 15px;
}

.content-view ul, .content-view ol {
    margin-bottom: 15px;
    padding-left: 25px;
}

.content-view blockquote {
    border-left: 4px solid #dee2e6;
    padding-left: 20px;
    margin: 20px 0;
    font-style: italic;
    color: #6c757d;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete() {
    if (confirm('Apakah Anda yakin ingin menghapus berita ini?\n\nTindakan ini tidak dapat dibatalkan.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
<?= $this->endSection() ?>
