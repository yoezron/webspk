<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
Pengaturan Sistem - Super Admin
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.setting-item {
    border-bottom: 1px solid #eee;
    padding: 1rem 0;
}
.setting-item:last-child {
    border-bottom: none;
}
.setting-label {
    font-weight: 500;
    margin-bottom: 0.25rem;
}
.setting-description {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
}
.setting-input {
    max-width: 400px;
}
.category-section {
    margin-bottom: 2rem;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Pengaturan Sistem</h1>
            <span>Kelola konfigurasi aplikasi dan parameter sistem</span>
        </div>
    </div>
</div>

<!-- Alert Messages -->
<?php if (session()->has('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong><i class="material-icons-outlined">check_circle</i> Berhasil!</strong>
    <?= session('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (session()->has('error')): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong><i class="material-icons-outlined">error</i> Error!</strong>
    <?= session('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Settings Tabs -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <?php $first = true; foreach ($categories as $catKey => $catName): ?>
                        <?php if (isset($settings[$catKey])): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?= $first ? 'active' : '' ?>"
                                        id="<?= $catKey ?>-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#<?= $catKey ?>-pane"
                                        type="button"
                                        role="tab">
                                    <i class="material-icons-outlined me-2"><?= getCategoryIcon($catKey) ?></i>
                                    <?= esc($catName) ?>
                                </button>
                            </li>
                            <?php $first = false; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <?php $first = true; foreach ($categories as $catKey => $catName): ?>
                        <?php if (isset($settings[$catKey])): ?>
                            <div class="tab-pane fade <?= $first ? 'show active' : '' ?>"
                                 id="<?= $catKey ?>-pane"
                                 role="tabpanel">

                                <form id="form-<?= $catKey ?>" class="settings-form" data-category="<?= $catKey ?>">
                                    <?= csrf_field() ?>

                                    <?php foreach ($settings[$catKey] as $key => $setting): ?>
                                        <div class="setting-item">
                                            <div class="row align-items-center">
                                                <div class="col-md-5">
                                                    <label class="setting-label"><?= esc(ucwords(str_replace('_', ' ', $key))) ?></label>
                                                    <?php if (!empty($setting['description'])): ?>
                                                        <div class="setting-description"><?= esc($setting['description']) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-5">
                                                    <?= renderSettingInput($key, $setting) ?>
                                                </div>
                                                <div class="col-md-2 text-end">
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-secondary reset-btn"
                                                            data-id="<?= $setting['id'] ?>"
                                                            data-key="<?= esc($key) ?>"
                                                            title="Reset ke default">
                                                        <i class="material-icons-outlined">restart_alt</i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons-outlined">save</i>
                                            Simpan Perubahan
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="location.reload()">
                                            <i class="material-icons-outlined">cancel</i>
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <?php $first = false; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Card -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('admin/settings/rbac') ?>" class="btn btn-outline-primary">
                        <i class="material-icons-outlined">admin_panel_settings</i>
                        Kelola RBAC (Roles & Permissions)
                    </a>
                    <a href="<?= base_url('admin/settings/audit') ?>" class="btn btn-outline-info">
                        <i class="material-icons-outlined">history</i>
                        Lihat Audit Log
                    </a>
                    <a href="<?= base_url('admin/settings/backup') ?>" class="btn btn-outline-success">
                        <i class="material-icons-outlined">backup</i>
                        Backup Database
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Informasi Sistem</h5>
            </div>
            <div class="card-body">
                <div class="info-list">
                    <div class="info-list-item">
                        <span class="info-list-item-icon bg-primary text-white">
                            <i class="material-icons-outlined">info</i>
                        </span>
                        <div class="info-list-item-content">
                            <p class="info-list-item-title">Versi Aplikasi</p>
                            <p class="info-list-item-description">1.0.0</p>
                        </div>
                    </div>
                    <div class="info-list-item">
                        <span class="info-list-item-icon bg-success text-white">
                            <i class="material-icons-outlined">code</i>
                        </span>
                        <div class="info-list-item-content">
                            <p class="info-list-item-title">PHP Version</p>
                            <p class="info-list-item-description"><?= phpversion() ?></p>
                        </div>
                    </div>
                    <div class="info-list-item">
                        <span class="info-list-item-icon bg-info text-white">
                            <i class="material-icons-outlined">dns</i>
                        </span>
                        <div class="info-list-item-content">
                            <p class="info-list-item-title">Environment</p>
                            <p class="info-list-item-description"><?= ENVIRONMENT ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Handle form submission
    $('.settings-form').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const category = form.data('category');
        const formData = new FormData(form[0]);

        // Show loading
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="material-icons-outlined">hourglass_empty</i> Menyimpan...');

        $.ajax({
            url: '<?= base_url('admin/settings/update') ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                } else {
                    showAlert('danger', response.message);
                    if (response.errors) {
                        let errorMsg = '<ul>';
                        for (let key in response.errors) {
                            errorMsg += '<li>' + response.errors[key] + '</li>';
                        }
                        errorMsg += '</ul>';
                        showAlert('danger', errorMsg);
                    }
                }
            },
            error: function() {
                showAlert('danger', 'Terjadi kesalahan saat menyimpan pengaturan');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Handle reset button
    $('.reset-btn').on('click', function() {
        const btn = $(this);
        const settingId = btn.data('id');
        const settingKey = btn.data('key');

        if (!confirm('Reset pengaturan "' + settingKey + '" ke nilai default?')) {
            return;
        }

        $.ajax({
            url: '<?= base_url('admin/settings/reset') ?>/' + settingId,
            method: 'POST',
            data: { <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    // Update the input field
                    const input = $('input[name="settings[' + settingKey + ']"]');
                    if (input.attr('type') === 'checkbox') {
                        input.prop('checked', response.value == '1');
                    } else {
                        input.val(response.value);
                    }
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                showAlert('danger', 'Terjadi kesalahan saat mereset pengaturan');
            }
        });
    });

    function showAlert(type, message) {
        const alert = $('<div>')
            .addClass('alert alert-' + type + ' alert-dismissible fade show')
            .attr('role', 'alert')
            .html('<strong>' + message + '</strong><button type="button" class="btn-close" data-bs-dismiss="alert"></button>');

        $('.page-description').after(alert);

        setTimeout(function() {
            alert.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }
});
</script>
<?= $this->endSection() ?>

<?php
// Helper function to render setting input
function renderSettingInput($key, $setting) {
    $name = "settings[{$key}]";
    $value = $setting['raw_value'] ?? $setting['value'];

    switch ($setting['type']) {
        case 'boolean':
            $checked = $setting['value'] ? 'checked' : '';
            return "<div class='form-check form-switch'>
                        <input class='form-check-input' type='checkbox' name='{$name}' value='1' {$checked}>
                    </div>";

        case 'integer':
            return "<input type='number' class='form-control setting-input' name='{$name}' value='" . esc($value) . "' step='1'>";

        case 'decimal':
            return "<input type='number' class='form-control setting-input' name='{$name}' value='" . esc($value) . "' step='0.01'>";

        case 'json':
            return "<textarea class='form-control setting-input' name='{$name}' rows='3'>" . esc(json_encode($setting['value'], JSON_PRETTY_PRINT)) . "</textarea>";

        default:
            return "<input type='text' class='form-control setting-input' name='{$name}' value='" . esc($value) . "'>";
    }
}

// Helper function to get category icon
function getCategoryIcon($category) {
    $icons = [
        'general' => 'settings',
        'dues' => 'payments',
        'email' => 'email',
        'notification' => 'notifications',
        'security' => 'security',
        'system' => 'dns',
    ];
    return $icons[$category] ?? 'settings';
}
?>
