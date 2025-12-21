<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Preview Import Data</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <?php if (isset($breadcrumbs)): ?>
                        <?php foreach ($breadcrumbs as $crumb): ?>
                            <?php if (!empty($crumb['url'])): ?>
                                <li class="breadcrumb-item"><a href="<?= $crumb['url'] ?>"><?= $crumb['title'] ?></a></li>
                            <?php else: ?>
                                <li class="breadcrumb-item active"><?= $crumb['title'] ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/members/bulk-import/cancel') ?>" class="btn btn-secondary" onclick="return confirm('Apakah Anda yakin ingin membatalkan import?')">
                <i class="fas fa-times me-2"></i>Batal
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Data</h6>
                            <h3 class="mb-0"><?= count($import_data['data']) ?></h3>
                        </div>
                        <div>
                            <i class="fas fa-database fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Data Valid</h6>
                            <h3 class="mb-0"><?= $import_data['validation']['valid_count'] ?></h3>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Data Error</h6>
                            <h3 class="mb-0"><?= $import_data['validation']['invalid_count'] ?></h3>
                        </div>
                        <div>
                            <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">File</h6>
                            <p class="mb-0 small text-truncate"><?= esc($import_data['file_name']) ?></p>
                        </div>
                        <div>
                            <i class="fas fa-file-csv fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    <?php if ($import_data['validation']['invalid_count'] > 0): ?>
        <div class="alert alert-warning">
            <h5 class="alert-heading">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Ditemukan <?= $import_data['validation']['invalid_count'] ?> data dengan error
            </h5>
            <p class="mb-0">Data dengan error tidak akan diimport. Silakan perbaiki error tersebut jika diperlukan.</p>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Error</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th width="80">Baris</th>
                                <th>Error</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($import_data['validation']['errors'] as $index => $errors): ?>
                                <tr>
                                    <td><span class="badge bg-danger"><?= $index + 1 ?></span></td>
                                    <td>
                                        <ul class="mb-0 ps-3">
                                            <?php foreach ($errors as $error): ?>
                                                <li class="small"><?= esc($error) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Preview Data -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Preview Data (20 baris pertama)</h5>
            <span class="badge bg-primary"><?= count($import_data['data']) ?> total</span>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th width="50">#</th>
                            <th width="50">Status</th>
                            <th>Email</th>
                            <th>Nama Lengkap</th>
                            <th>Telepon</th>
                            <th>Jenis Kelamin</th>
                            <th>Tanggal Lahir</th>
                            <th>Universitas</th>
                            <th>Fakultas</th>
                            <th>Departemen</th>
                            <th width="100">Kelengkapan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $previewData = array_slice($import_data['data'], 0, 20);
                        foreach ($previewData as $index => $row):
                            $hasError = isset($import_data['validation']['errors'][$index]);
                            $isComplete = !empty($row['email']) && !empty($row['full_name']) &&
                                         !empty($row['gender']) && !empty($row['birth_date']) &&
                                         !empty($row['address']) && !empty($row['university_name']) &&
                                         !empty($row['faculty']) && !empty($row['department']);
                        ?>
                            <tr class="<?= $hasError ? 'table-danger' : '' ?>">
                                <td><?= $index + 1 ?></td>
                                <td class="text-center">
                                    <?php if ($hasError): ?>
                                        <i class="fas fa-times-circle text-danger" title="Data error"></i>
                                    <?php else: ?>
                                        <i class="fas fa-check-circle text-success" title="Data valid"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($row['email'] ?? '') ?></td>
                                <td><?= esc($row['full_name'] ?? '') ?></td>
                                <td><?= esc($row['phone_number'] ?? '-') ?></td>
                                <td class="text-center"><?= esc($row['gender'] ?? '-') ?></td>
                                <td><?= esc($row['birth_date'] ?? '-') ?></td>
                                <td><?= esc($row['university_name'] ?? '-') ?></td>
                                <td><?= esc($row['faculty'] ?? '-') ?></td>
                                <td><?= esc($row['department'] ?? '-') ?></td>
                                <td class="text-center">
                                    <?php if (!$hasError): ?>
                                        <?php if ($isComplete): ?>
                                            <span class="badge bg-success">Lengkap</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Tidak Lengkap</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Error</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (count($import_data['data']) > 20): ?>
                <div class="alert alert-info mt-3 mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Menampilkan 20 dari <?= count($import_data['data']) ?> data. Semua data akan diproses saat import.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Information Alert -->
    <div class="alert alert-info">
        <h5 class="alert-heading">
            <i class="fas fa-info-circle me-2"></i>Informasi Import:
        </h5>
        <ul class="mb-0">
            <li><strong>Data Lengkap:</strong> Akun akan dibuat dengan status <span class="badge bg-success">ACTIVE</span> dan dapat langsung login</li>
            <li><strong>Data Tidak Lengkap:</strong> Akun akan dibuat dengan status <span class="badge bg-warning">INACTIVE</span> dan harus melengkapi data terlebih dahulu</li>
            <li><strong>Data Error:</strong> Akan dilewati dan tidak diimport</li>
            <li><strong>Email Duplikat:</strong> Akan otomatis dilewati saat proses import</li>
        </ul>
    </div>

    <!-- Action Buttons -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Lanjutkan Import?</h5>
                    <p class="text-muted mb-0">
                        <?= $import_data['validation']['valid_count'] ?> data akan diimport,
                        <?= $import_data['validation']['invalid_count'] ?> data akan dilewati
                    </p>
                </div>
                <div>
                    <a href="<?= base_url('admin/members/bulk-import/cancel') ?>" class="btn btn-secondary me-2" onclick="return confirm('Apakah Anda yakin ingin membatalkan?')">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                    <form action="<?= base_url('admin/members/bulk-import/process') ?>" method="POST" style="display: inline;">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('Apakah Anda yakin ingin memproses import ini?')">
                            <i class="fas fa-check me-2"></i>Proses Import (<?= $import_data['validation']['valid_count'] ?> data)
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}
</style>
<?= $this->endSection() ?>
