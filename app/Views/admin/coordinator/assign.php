<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
Assign Regions - <?= esc($coordinator['full_name']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Assign Regions to Coordinator</h1>
            <span>Kelola wilayah untuk <?= esc($coordinator['full_name']) ?></span>
        </div>
    </div>
</div>

<!-- Coordinator Info Card -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="card-title mb-3">
                            <i class="material-icons-outlined">person</i>
                            Coordinator Information
                        </h5>
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th style="width: 200px;">Nama Lengkap</th>
                                <td><?= esc($coordinator['full_name']) ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?= esc($coordinator['email']) ?></td>
                            </tr>
                            <tr>
                                <th>No. Anggota</th>
                                <td><?= esc($coordinator['member_number']) ?></td>
                            </tr>
                            <tr>
                                <th>Total Wilayah Saat Ini</th>
                                <td><span class="badge badge-info"><?= count($assigned_regions) ?> Wilayah</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="<?= base_url('admin/coordinators') ?>" class="btn btn-secondary">
                            <i class="material-icons-outlined">arrow_back</i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Regions Form -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="material-icons-outlined">map</i>
                    Pilih Wilayah untuk Koordinator
                </h5>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="material-icons-outlined">check_circle</i>
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="material-icons-outlined">error</i>
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= base_url('admin/coordinators/assign/' . $coordinator['id']) ?>" id="assignForm">
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                                    <i class="material-icons-outlined">check_box</i> Pilih Semua
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                                    <i class="material-icons-outlined">check_box_outline_blank</i> Hapus Semua
                                </button>
                            </div>
                            <div class="col-md-6 text-end">
                                <span id="selectedCount" class="badge badge-info">
                                    <?= count($assigned_regions) ?> wilayah dipilih
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">
                                        <input type="checkbox" id="selectAllCheckbox" onclick="toggleAll(this)">
                                    </th>
                                    <th>Kode Wilayah</th>
                                    <th>Nama Provinsi</th>
                                    <th>Jumlah Anggota</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($all_regions as $region): ?>
                                    <?php
                                    $isAssigned = in_array($region['region_code'], array_column($assigned_regions, 'region_code'));
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox"
                                                   name="region_codes[]"
                                                   value="<?= esc($region['region_code']) ?>"
                                                   class="region-checkbox"
                                                   <?= $isAssigned ? 'checked' : '' ?>
                                                   onchange="updateCount()">
                                        </td>
                                        <td><strong><?= esc($region['region_code']) ?></strong></td>
                                        <td><?= esc($region['province_name']) ?></td>
                                        <td>
                                            <span class="badge badge-light">
                                                <?= $region['member_count'] ?? 0 ?> anggota
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($isAssigned): ?>
                                                <span class="badge badge-success">
                                                    <i class="material-icons-outlined" style="font-size: 14px;">check</i>
                                                    Ter-assign
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Belum</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="material-icons-outlined">save</i> Simpan Perubahan
                        </button>
                        <a href="<?= base_url('admin/coordinators') ?>" class="btn btn-secondary">
                            <i class="material-icons-outlined">cancel</i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Currently Assigned Regions Summary -->
<?php if (!empty($assigned_regions)): ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="material-icons-outlined">list</i>
                    Wilayah yang Sudah Ter-assign
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($assigned_regions as $region): ?>
                        <div class="col-md-3 mb-2">
                            <div class="alert alert-success mb-2">
                                <strong><?= esc($region['region_code']) ?></strong> -
                                <?= esc($region['province_name']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function selectAll() {
    document.querySelectorAll('.region-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    document.getElementById('selectAllCheckbox').checked = true;
    updateCount();
}

function deselectAll() {
    document.querySelectorAll('.region-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAllCheckbox').checked = false;
    updateCount();
}

function toggleAll(source) {
    document.querySelectorAll('.region-checkbox').forEach(checkbox => {
        checkbox.checked = source.checked;
    });
    updateCount();
}

function updateCount() {
    const checked = document.querySelectorAll('.region-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = checked + ' wilayah dipilih';

    // Update select all checkbox
    const total = document.querySelectorAll('.region-checkbox').length;
    document.getElementById('selectAllCheckbox').checked = (checked === total);
}

// Confirmation on submit
document.getElementById('assignForm').addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('.region-checkbox:checked').length;
    if (checked === 0) {
        e.preventDefault();
        alert('Pilih minimal satu wilayah untuk koordinator ini.');
        return false;
    }

    if (!confirm('Simpan perubahan assignment wilayah untuk koordinator ini?')) {
        e.preventDefault();
        return false;
    }
});
</script>

<?= $this->endSection() ?>
