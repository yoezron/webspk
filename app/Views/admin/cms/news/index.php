<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Kelola Berita</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item">CMS</li>
                    <li class="breadcrumb-item active">Berita</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/cms/news/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Buat Berita
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
            <form method="GET" action="<?= base_url('admin/cms/news') ?>">
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
                        <label class="form-label">Pencarian</label>
                        <input type="text" class="form-control" name="search" placeholder="Cari judul berita..." value="<?= esc($search ?? '') ?>">
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

    <!-- News Table -->
    <div class="card">
        <div class="card-body">
            <?php if (!empty($posts) && count($posts) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Cover</th>
                                <th>Judul</th>
                                <th>Penulis</th>
                                <th>Status</th>
                                <th>Tanggal Publikasi</th>
                                <th>Dibuat</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($post['cover_image_id'])): ?>
                                            <img src="<?= base_url('uploads/media/' . ($post['cover_image_path'] ?? 'placeholder.jpg')) ?>"
                                                 alt="Cover"
                                                 class="img-thumbnail"
                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                 style="width: 60px; height: 60px; border-radius: 4px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= esc($post['title']) ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-link me-1"></i><?= esc($post['slug']) ?>
                                        </small>
                                        <?php if (!empty($post['excerpt'])): ?>
                                            <br>
                                            <small class="text-muted"><?= esc(substr($post['excerpt'], 0, 100)) ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <i class="fas fa-user me-1"></i>
                                        <?= esc($post['author_name'] ?? 'Unknown') ?>
                                    </td>
                                    <td>
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
                                        <span class="badge bg-<?= $statusClass[$post['status']] ?? 'secondary' ?>">
                                            <i class="fas <?= $statusIcon[$post['status']] ?? 'fa-question' ?> me-1"></i>
                                            <?= ucfirst($post['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($post['status'] === 'published' && !empty($post['published_at'])): ?>
                                            <i class="fas fa-calendar me-1"></i>
                                            <?= date('d M Y', strtotime($post['published_at'])) ?>
                                            <br>
                                            <small class="text-muted"><?= date('H:i', strtotime($post['published_at'])) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <i class="fas fa-clock me-1"></i>
                                        <?= date('d M Y', strtotime($post['created_at'])) ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="<?= base_url('admin/cms/news/view/' . $post['id']) ?>"
                                               class="btn btn-sm btn-info"
                                               title="Lihat">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('admin/cms/news/edit/' . $post['id']) ?>"
                                               class="btn btn-sm btn-warning"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete(<?= $post['id'] ?>)"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (isset($pager)): ?>
                    <div class="mt-4">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada berita. Klik tombol "Buat Berita" untuk membuat berita pertama.</p>
                    <a href="<?= base_url('admin/cms/news/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Buat Berita
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Form -->
<form id="deleteForm" method="POST" style="display: none;">
    <?= csrf_field() ?>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus berita ini?')) {
        const form = document.getElementById('deleteForm');
        form.action = '<?= base_url('admin/cms/news/delete/') ?>' + id;
        form.submit();
    }
}
</script>
<?= $this->endSection() ?>
