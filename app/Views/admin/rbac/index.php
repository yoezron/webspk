<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
RBAC Management - Super Admin
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>RBAC Management</h1>
            <span>Kelola Roles, Permissions, dan User Access Control</span>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row">
    <div class="col-xl-4">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-primary">
                        <i class="material-icons-outlined">shield</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Total Roles</span>
                        <span class="widget-stats-amount"><?= number_format($roles_count) ?></span>
                        <span class="widget-stats-info">Role aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-success">
                        <i class="material-icons-outlined">vpn_key</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Total Permissions</span>
                        <span class="widget-stats-amount"><?= number_format($permissions_count) ?></span>
                        <span class="widget-stats-info">Permission tersedia</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-info">
                        <i class="material-icons-outlined">people</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Users with Roles</span>
                        <span class="widget-stats-amount"><?= number_format($users_with_roles) ?></span>
                        <span class="widget-stats-info">User ter-assign</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="material-icons-outlined text-primary" style="font-size: 48px;">shield</i>
                <h5 class="card-title mt-3">Kelola Roles</h5>
                <p class="card-text">Manage system roles dan permissions</p>
                <a href="<?= base_url('admin/settings/rbac/roles') ?>" class="btn btn-primary">
                    <i class="material-icons-outlined">arrow_forward</i> Buka Roles
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="material-icons-outlined text-success" style="font-size: 48px;">vpn_key</i>
                <h5 class="card-title mt-3">Kelola Permissions</h5>
                <p class="card-text">Atur permission untuk setiap role</p>
                <a href="<?= base_url('admin/settings/rbac/permissions') ?>" class="btn btn-success">
                    <i class="material-icons-outlined">arrow_forward</i> Buka Permissions
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <i class="material-icons-outlined text-info" style="font-size: 48px;">assignment_ind</i>
                <h5 class="card-title mt-3">Assign Roles</h5>
                <p class="card-text">Assign roles ke users</p>
                <a href="<?= base_url('admin/settings/rbac/assign') ?>" class="btn btn-info">
                    <i class="material-icons-outlined">arrow_forward</i> Assign Roles
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
