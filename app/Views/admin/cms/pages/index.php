<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Kelola Halaman</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item">CMS</li>
                    <li class="breadcrumb-item active">Halaman</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/cms/pages/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Halaman
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
            <form method="GET" action="<?= base_url('admin/cms/pages') ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="draft" <?= ($status ?? '') == 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= ($status ?? '') == 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="archived" <?= ($status ?? '') == 'archived' ? 'selected' : '' ?>>Archived</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Template</label>
                        <select class="form-select" name="template">
                            <option value="">Semua Template</option>
                            <option value="default" <?= ($template_filter ?? '') == 'default' ? 'selected' : '' ?>>Default</option>
                            <option value="legal" <?= ($template_filter ?? '') == 'legal' ? 'selected' : '' ?>>Legal</option>
                            <option value="contact" <?= ($template_filter ?? '') == 'contact' ? 'selected' : '' ?>>Contact</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Pages Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Slug</th>
                            <th>Template</th>
                            <th>Status</th>
                            <th>Visibility</th>
                            <th>Last Updated</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pages) && count($pages) > 0): ?>
                            <?php foreach ($pages as $page): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($page['title']) ?></strong>
                                    </td>
                                    <td>
                                        <code><?= esc($page['slug']) ?></code>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= esc($page['template']) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeClass = match($page['status']) {
                                            'published' => 'success',
                                            'draft' => 'warning',
                                            'archived' => 'secondary',
                                            default => 'secondary'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?>">
                                            <?= ucfirst($page['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($page['visibility'] === 'public'): ?>
                                            <i class="fas fa-globe text-success" title="Public"></i>
                                        <?php else: ?>
                                            <i class="fas fa-lock text-warning" title="Member Only"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('d M Y, H:i', strtotime($page['updated_at'])) ?>
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('page/' . $page['slug']) ?>"
                                               class="btn btn-sm btn-outline-primary"
                                               target="_blank"
                                               title="Preview">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('admin/cms/pages/edit/' . $page['id']) ?>"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('admin/cms/pages/revisions/' . $page['id']) ?>"
                                               class="btn btn-sm btn-outline-info"
                                               title="Revisions">
                                                <i class="fas fa-history"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="confirmDelete(<?= $page['id'] ?>)"
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
                                    Belum ada halaman.
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
                Apakah Anda yakin ingin menghapus halaman ini?
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
    deleteForm.action = '<?= base_url('admin/cms/pages/delete/') ?>' + id;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>

<?= $this->endSection() ?>
