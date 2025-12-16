<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
Audit Log Viewer - Super Admin
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/neptune/plugins/datatables/datatables.min.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Audit Log Viewer</h1>
            <span>Monitor aktivitas sistem dan user actions</span>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row">
    <div class="col-xl-3">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-primary">
                        <i class="material-icons-outlined">article</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Total Logs</span>
                        <span class="widget-stats-amount"><?= number_format($stats['total_logs']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-success">
                        <i class="material-icons-outlined">today</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Hari Ini</span>
                        <span class="widget-stats-amount"><?= number_format($stats['logs_today']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-info">
                        <i class="material-icons-outlined">date_range</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Minggu Ini</span>
                        <span class="widget-stats-amount"><?= number_format($stats['logs_this_week']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-warning">
                        <i class="material-icons-outlined">people</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Active Users</span>
                        <span class="widget-stats-amount"><?= number_format($stats['unique_users_today']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Filter Logs</h5>
            </div>
            <div class="card-body">
                <form method="get" action="<?= base_url('admin/settings/audit') ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" name="search" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Cari deskripsi...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Action Type</label>
                                <select class="form-select" name="action">
                                    <option value="">Semua</option>
                                    <?php foreach ($action_types as $type): ?>
                                        <option value="<?= esc($type) ?>" <?= ($filters['action'] ?? '') === $type ? 'selected' : '' ?>>
                                            <?= ucfirst(esc($type)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" class="form-control" name="date_from" value="<?= esc($filters['date_from'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" class="form-control" name="date_to" value="<?= esc($filters['date_to'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid gap-2 d-md-flex">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-icons-outlined">search</i> Filter
                                    </button>
                                    <a href="<?= base_url('admin/settings/audit') ?>" class="btn btn-secondary">
                                        <i class="material-icons-outlined">clear</i> Reset
                                    </a>
                                    <a href="<?= base_url('admin/settings/audit/export') ?>?date_from=<?= $filters['date_from'] ?? '' ?>&date_to=<?= $filters['date_to'] ?? '' ?>" class="btn btn-success">
                                        <i class="material-icons-outlined">download</i> Export
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Logs Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Audit Logs</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="auditTable">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Target</th>
                                <th>Deskripsi</th>
                                <th>IP Address</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($logs)): ?>
                                <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                                    <td><?= esc($log['user_name'] ?? 'System') ?></td>
                                    <td>
                                        <span class="badge badge-<?= getActionBadgeClass($log['action_type']) ?>">
                                            <?= ucfirst(esc($log['action_type'])) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($log['target_type']) ?></td>
                                    <td><?= esc($log['description']) ?></td>
                                    <td><?= esc($log['ip_address'] ?? '-') ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/settings/audit/view/' . $log['id']) ?>" class="btn btn-sm btn-primary">
                                            <i class="material-icons-outlined">visibility</i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada log ditemukan</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($pager): ?>
                    <div class="mt-3">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/neptune/plugins/datatables/datatables.min.js') ?>"></script>
<script>
// Initialize DataTable (optional - for sorting/searching without page reload)
$(document).ready(function() {
    // Could add DataTables here if needed
});
</script>
<?= $this->endSection() ?>

<?php
function getActionBadgeClass($action) {
    return match($action) {
        'create' => 'success',
        'update' => 'info',
        'delete' => 'danger',
        'login' => 'primary',
        'logout' => 'secondary',
        default => 'secondary',
    };
}
?>
