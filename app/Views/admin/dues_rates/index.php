<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description d-flex justify-content-between align-items-center">
            <div>
                <h1>Manajemen Tarif Iuran</h1>
                <span>Kelola tarif iuran anggota serikat</span>
            </div>
            <div>
                <a href="<?= base_url('admin/dues-rates/create') ?>" class="btn btn-primary">
                    <i class="material-icons-outlined">add</i> Tambah Tarif Baru
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-primary">
                        <i class="material-icons-outlined">list</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Total Tarif</span>
                        <span class="widget-stats-amount"><?= $stats['total_rates'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-success">
                        <i class="material-icons-outlined">check_circle</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Tarif Aktif</span>
                        <span class="widget-stats-amount"><?= $stats['active_rates'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-info">
                        <i class="material-icons-outlined">calendar_month</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Tarif Bulanan</span>
                        <span class="widget-stats-amount"><?= $stats['monthly_rates'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card widget widget-stats">
            <div class="card-body">
                <div class="widget-stats-container d-flex">
                    <div class="widget-stats-icon widget-stats-icon-warning">
                        <i class="material-icons-outlined">event</i>
                    </div>
                    <div class="widget-stats-content flex-fill">
                        <span class="widget-stats-title">Tarif Tahunan</span>
                        <span class="widget-stats-amount"><?= $stats['yearly_rates'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rates Table -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="material-icons-outlined">table_chart</i>
                    Daftar Tarif Iuran
                </h5>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="material-icons-outlined">check_circle</i>
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="material-icons-outlined">error</i>
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Tarif</th>
                                <th>Tipe</th>
                                <th class="text-end">Jumlah</th>
                                <th>Kategori</th>
                                <th>Wilayah</th>
                                <th>Berlaku</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rates)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada tarif iuran</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($rates as $rate): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($rate['rate_name']) ?></strong>
                                            <?php if ($rate['description']): ?>
                                                <br><small class="text-muted"><?= esc(substr($rate['description'], 0, 50)) ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $typeBadge = match($rate['rate_type']) {
                                                'monthly' => 'badge-info',
                                                'yearly' => 'badge-warning',
                                                'one_time' => 'badge-secondary',
                                                default => 'badge-light'
                                            };
                                            $typeLabel = match($rate['rate_type']) {
                                                'monthly' => 'Bulanan',
                                                'yearly' => 'Tahunan',
                                                'one_time' => 'Sekali',
                                                default => $rate['rate_type']
                                            };
                                            ?>
                                            <span class="badge <?= $typeBadge ?>"><?= $typeLabel ?></span>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-success">Rp <?= number_format($rate['amount'], 0, ',', '.') ?></strong>
                                        </td>
                                        <td>
                                            <?= $rate['member_category'] ? esc(ucfirst($rate['member_category'])) : '<span class="text-muted">Semua</span>' ?>
                                        </td>
                                        <td>
                                            <?= $rate['region_code'] ? esc($rate['region_code']) : '<span class="text-muted">Semua</span>' ?>
                                        </td>
                                        <td>
                                            <?= date('d M Y', strtotime($rate['effective_from'])) ?>
                                            <?php if ($rate['effective_to']): ?>
                                                <br><small class="text-muted">s/d <?= date('d M Y', strtotime($rate['effective_to'])) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       <?= $rate['is_active'] == 1 ? 'checked' : '' ?>
                                                       onchange="toggleStatus(<?= $rate['id'] ?>, this)">
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('admin/dues-rates/view/' . $rate['id']) ?>"
                                                   class="btn btn-info"
                                                   title="Lihat Detail">
                                                    <i class="material-icons-outlined">visibility</i>
                                                </a>
                                                <a href="<?= base_url('admin/dues-rates/edit/' . $rate['id']) ?>"
                                                   class="btn btn-warning"
                                                   title="Edit">
                                                    <i class="material-icons-outlined">edit</i>
                                                </a>
                                                <a href="<?= base_url('admin/dues-rates/duplicate/' . $rate['id']) ?>"
                                                   class="btn btn-secondary"
                                                   title="Duplikasi">
                                                    <i class="material-icons-outlined">content_copy</i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-danger"
                                                        onclick="confirmDelete(<?= $rate['id'] ?>, '<?= esc($rate['rate_name']) ?>')"
                                                        title="Hapus">
                                                    <i class="material-icons-outlined">delete</i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form (hidden) -->
<form id="deleteForm" method="post" style="display: none;">
    <?= csrf_field() ?>
</form>

<script>
function toggleStatus(rateId, checkbox) {
    const originalState = checkbox.checked;

    fetch('<?= base_url('admin/dues-rates/toggle-status') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'rate_id=' + rateId + '&<?= csrf_token() ?>=<?= csrf_hash() ?>'
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            checkbox.checked = !originalState;
            alert(data.message);
        }
    })
    .catch(error => {
        checkbox.checked = !originalState;
        alert('Gagal mengubah status tarif');
    });
}

function confirmDelete(rateId, rateName) {
    if (confirm('Apakah Anda yakin ingin menghapus tarif "' + rateName + '"?\n\nPeringatan: Pastikan tarif ini tidak sedang digunakan.')) {
        const form = document.getElementById('deleteForm');
        form.action = '<?= base_url('admin/dues-rates/delete') ?>/' + rateId;
        form.submit();
    }
}
</script>

<?= $this->endSection() ?>
