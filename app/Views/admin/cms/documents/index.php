<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Kelola Dokumen</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item">CMS</li>
                    <li class="breadcrumb-item active">Dokumen</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/cms/documents/categories') ?>" class="btn btn-outline-primary me-2">
                <i class="fas fa-folder me-2"></i>Kategori
            </a>
            <a href="<?= base_url('admin/cms/documents/create') ?>" class="btn btn-primary">
                <i class="fas fa-upload me-2"></i>Upload Dokumen
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= base_url('admin/cms/documents') ?>">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tipe Dokumen</label>
                        <select class="form-select" name="type">
                            <option value="">Semua Tipe</option>
                            <option value="publikasi" <?= ($doc_type ?? '') == 'publikasi' ? 'selected' : '' ?>>Publikasi</option>
                            <option value="regulasi" <?= ($doc_type ?? '') == 'regulasi' ? 'selected' : '' ?>>Regulasi</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="draft" <?= ($status ?? '') == 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= ($status ?? '') == 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="archived" <?= ($status ?? '') == 'archived' ? 'selected' : '' ?>>Archived</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" name="category">
                            <option value="">Semua Kategori</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($category_filter ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                        <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Documents Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Tipe</th>
                            <th>Kategori</th>
                            <th>File</th>
                            <th>Status</th>
                            <th>Downloads</th>
                            <th>Uploaded</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($documents) && count($documents) > 0): ?>
                            <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                            <div>
                                                <strong><?= esc($doc['title']) ?></strong>
                                                <?php if (!empty($doc['description'])): ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?= esc(substr($doc['description'], 0, 60)) ?>...
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $doc['doc_type'] === 'publikasi' ? 'info' : 'warning' ?>">
                                            <?= ucfirst($doc['doc_type']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= esc($doc['category_name'] ?? '-') ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= number_format($doc['file_size'] / 1024, 2) ?> KB
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeClass = match($doc['status']) {
                                            'published' => 'success',
                                            'draft' => 'warning',
                                            'archived' => 'secondary',
                                            default => 'secondary'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?>">
                                            <?= ucfirst($doc['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= $doc['download_count'] ?? 0 ?> <i class="fas fa-download"></i>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('d M Y', strtotime($doc['created_at'])) ?>
                                            <br>
                                            <small>by <?= esc($doc['creator_name'] ?? 'System') ?></small>
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('documents/preview/' . $doc['id']) ?>"
                                               class="btn btn-sm btn-outline-primary"
                                               target="_blank"
                                               title="Preview">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('documents/download/' . $doc['id']) ?>"
                                               class="btn btn-sm btn-outline-success"
                                               target="_blank"
                                               title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="<?= base_url('admin/cms/documents/edit/' . $doc['id']) ?>"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDelete(<?= $doc['id'] ?>)"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
                                    Belum ada dokumen.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (isset($pager)): ?>
                <div class="mt-3">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus dokumen ini?</p>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian:</strong> File fisik juga akan dihapus dari server.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = '<?= base_url('admin/cms/documents/delete/') ?>' + id;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>

<?= $this->endSection() ?>
