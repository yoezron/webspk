<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Kelola Pengurus</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item">CMS</li>
                    <li class="breadcrumb-item active">Pengurus</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/cms/officers/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Pengurus
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
            <form method="GET" action="<?= base_url('admin/cms/officers') ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Level</label>
                        <select class="form-select" name="level">
                            <option value="">Semua Level</option>
                            <option value="pusat" <?= ($level ?? '') == 'pusat' ? 'selected' : '' ?>>Pusat</option>
                            <option value="wilayah" <?= ($level ?? '') == 'wilayah' ? 'selected' : '' ?>>Wilayah</option>
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

    <!-- Officers Table -->
    <div class="card">
        <div class="card-body">
            <?php if (!empty($officers) && count($officers) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Foto</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>Level</th>
                                <th>Wilayah</th>
                                <th>Periode</th>
                                <th>Status</th>
                                <th>Urutan</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($officers as $officer): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($officer['photo_path'])): ?>
                                            <img src="<?= base_url('writable/uploads/officers/' . $officer['photo_path']) ?>"
                                                 alt="<?= esc($officer['full_name']) ?>"
                                                 class="rounded-circle"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white"
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= esc($officer['full_name']) ?></strong><br>
                                        <small class="text-muted">
                                            <i class="fas fa-envelope me-1"></i><?= esc($officer['email']) ?>
                                        </small>
                                    </td>
                                    <td><?= esc($officer['position_name']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $officer['level'] == 'pusat' ? 'primary' : 'info' ?>">
                                            <?= ucfirst($officer['level']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($officer['level'] == 'wilayah'): ?>
                                            <code><?= esc($officer['region_code'] ?? '-') ?></code>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($officer['period_start']) && !empty($officer['period_end'])): ?>
                                            <small>
                                                <?= date('Y', strtotime($officer['period_start'])) ?> -
                                                <?= date('Y', strtotime($officer['period_end'])) ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($officer['is_active']): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Tidak Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $officer['sort_order'] ?></td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="<?= base_url('admin/cms/officers/toggle-active/' . $officer['id']) ?>"
                                               class="btn btn-sm btn-<?= $officer['is_active'] ? 'secondary' : 'success' ?>"
                                               title="<?= $officer['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>"
                                               onclick="return confirm('Ubah status aktif pengurus?')">
                                                <i class="fas fa-<?= $officer['is_active'] ? 'eye-slash' : 'eye' ?>"></i>
                                            </a>
                                            <a href="<?= base_url('admin/cms/officers/edit/' . $officer['id']) ?>"
                                               class="btn btn-sm btn-warning"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="confirmDelete(<?= $officer['id'] ?>)"
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
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada pengurus. Klik tombol "Tambah Pengurus" untuk menambahkan pengurus.</p>
                    <a href="<?= base_url('admin/cms/officers/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Pengurus
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
    if (confirm('Apakah Anda yakin ingin menghapus pengurus ini?')) {
        const form = document.getElementById('deleteForm');
        form.action = '<?= base_url('admin/cms/officers/delete/') ?>' + id;
        form.submit();
    }
}
</script>
<?= $this->endSection() ?>
