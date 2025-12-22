<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Newsletter Subscribers</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item">CMS</li>
                    <li class="breadcrumb-item active">Subscribers</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/cms/subscribers/export') ?>" class="btn btn-success me-2">
                <i class="fas fa-download me-2"></i>Export CSV
            </a>
            <a href="<?= base_url('admin/cms/subscribers/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Subscriber
            </a>
        </div>
    </div>

    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics -->
    <?php if (!empty($stats)): ?>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h3><?= $stats['total'] ?></h3>
                        <p class="mb-0">Total Subscribers</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h3><?= $stats['active'] ?></h3>
                        <p class="mb-0">Active</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h3><?= $stats['unsubscribed'] ?></h3>
                        <p class="mb-0">Unsubscribed</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h3><?= $stats['bounced'] ?></h3>
                        <p class="mb-0">Bounced</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="active" <?= ($status ?? '') == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="unsubscribed" <?= ($status ?? '') == 'unsubscribed' ? 'selected' : '' ?>>Unsubscribed</option>
                            <option value="bounced" <?= ($status ?? '') == 'bounced' ? 'selected' : '' ?>>Bounced</option>
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

    <!-- Subscribers Table -->
    <div class="card">
        <div class="card-body">
            <?php if (!empty($subscribers)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Nama</th>
                                <th>Status</th>
                                <th>Verified</th>
                                <th>Subscribe Date</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subscribers as $subscriber): ?>
                                <tr>
                                    <td><?= esc($subscriber['email']) ?></td>
                                    <td><?= esc($subscriber['full_name'] ?: '-') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $subscriber['status'] == 'active' ? 'success' : ($subscriber['status'] == 'unsubscribed' ? 'warning' : 'danger') ?>">
                                            <?= ucfirst($subscriber['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($subscriber['is_verified']): ?>
                                            <i class="fas fa-check-circle text-success"></i>
                                        <?php else: ?>
                                            <i class="fas fa-times-circle text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d M Y', strtotime($subscriber['subscribed_at'])) ?></td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="<?= base_url('admin/cms/subscribers/edit/' . $subscriber['id']) ?>"
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $subscriber['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (isset($pager)): ?>
                    <div class="mt-4"><?= $pager->links() ?></div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada subscriber.</p>
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
    if (confirm('Hapus subscriber ini?')) {
        document.getElementById('deleteForm').action = '<?= base_url('admin/cms/subscribers/delete/') ?>' + id;
        document.getElementById('deleteForm').submit();
    }
}
</script>
<?= $this->endSection() ?>
