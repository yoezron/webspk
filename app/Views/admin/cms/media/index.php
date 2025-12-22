<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Media Library</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item">CMS</li>
                    <li class="breadcrumb-item active">Media</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/cms/media/upload') ?>" class="btn btn-primary">
                <i class="fas fa-upload me-2"></i>Upload Media
            </a>
        </div>
    </div>

    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <select class="form-select" name="type">
                            <option value="">Semua Tipe</option>
                            <option value="image" <?= ($media_type ?? '') == 'image' ? 'selected' : '' ?>>Image</option>
                            <option value="video" <?= ($media_type ?? '') == 'video' ? 'selected' : '' ?>>Video</option>
                            <option value="document" <?= ($media_type ?? '') == 'document' ? 'selected' : '' ?>>Document</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Media Grid -->
    <div class="card">
        <div class="card-body">
            <?php if (!empty($media)): ?>
                <div class="row g-3">
                    <?php foreach ($media as $item): ?>
                        <div class="col-md-2 col-sm-3 col-6">
                            <div class="card h-100">
                                <div class="position-relative" style="height: 150px; overflow: hidden;">
                                    <?php if ($item['media_type'] == 'image'): ?>
                                        <img src="<?= base_url('writable/uploads/media/' . $item['file_path']) ?>"
                                             class="card-img-top" style="object-fit: cover; height: 100%;">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                            <i class="fas fa-file fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body p-2">
                                    <small class="d-block text-truncate" title="<?= esc($item['title']) ?>">
                                        <?= esc($item['title']) ?>
                                    </small>
                                    <small class="text-muted"><?= number_format($item['file_size'] / 1024, 1) ?> KB</small>
                                </div>
                                <div class="card-footer p-2">
                                    <div class="btn-group w-100">
                                        <a href="<?= base_url('admin/cms/media/edit/' . $item['id']) ?>"
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $item['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (isset($pager)): ?>
                    <div class="mt-4"><?= $pager->links() ?></div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada media. Upload media pertama Anda.</p>
                    <a href="<?= base_url('admin/cms/media/upload') ?>" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Upload Media
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;"><?= csrf_field() ?></form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(id) {
    if (confirm('Hapus media ini?')) {
        document.getElementById('deleteForm').action = '<?= base_url('admin/cms/media/delete/') ?>' + id;
        document.getElementById('deleteForm').submit();
    }
}
</script>
<?= $this->endSection() ?>
