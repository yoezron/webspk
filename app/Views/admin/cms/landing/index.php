<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Kelola Landing Page</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item">CMS</li>
                    <li class="breadcrumb-item active">Landing Page</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/cms/landing/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Section
            </a>
        </div>
    </div>

    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($sections)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tipe Section</th>
                                <th>Judul</th>
                                <th>Urutan</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sections as $section): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary"><?= ucfirst($section['section_type']) ?></span>
                                    </td>
                                    <td>
                                        <strong><?= esc($section['title']) ?></strong>
                                        <?php if (!empty($section['subtitle'])): ?>
                                            <br><small class="text-muted"><?= esc($section['subtitle']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $section['sort_order'] ?></td>
                                    <td>
                                        <?php if ($section['is_active']): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Tidak Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="<?= base_url('admin/cms/landing/toggle-active/' . $section['id']) ?>"
                                               class="btn btn-sm btn-<?= $section['is_active'] ? 'secondary' : 'success' ?>"
                                               onclick="return confirm('Ubah status?')">
                                                <i class="fas fa-<?= $section['is_active'] ? 'eye-slash' : 'eye' ?>"></i>
                                            </a>
                                            <a href="<?= base_url('admin/cms/landing/edit/' . $section['id']) ?>"
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete(<?= $section['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-pager fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada section. Tambahkan section pertama.</p>
                    <a href="<?= base_url('admin/cms/landing/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Section
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    <?= csrf_field() ?>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(id) {
    if (confirm('Hapus section ini?')) {
        document.getElementById('deleteForm').action = '<?= base_url('admin/cms/landing/delete/') ?>' + id;
        document.getElementById('deleteForm').submit();
    }
}
</script>
<?= $this->endSection() ?>
