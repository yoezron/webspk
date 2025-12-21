<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Import Data Anggota</h1>
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
            <a href="<?= base_url('admin/members') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('info')): ?>
        <div class="alert alert-info alert-dismissible fade show">
            <i class="fas fa-info-circle me-2"></i>
            <?= session()->getFlashdata('info') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->get('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session()->get('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Instructions Card -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Petunjuk Import</h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold">Format File:</h6>
                    <ul>
                        <li>Format: CSV (.csv)</li>
                        <li>Ukuran maksimal: 5 MB</li>
                        <li>Encoding: UTF-8</li>
                    </ul>

                    <hr>

                    <h6 class="fw-bold">Kolom yang Diperlukan:</h6>
                    <ul class="small">
                        <li><strong>Email:</strong> Wajib, harus valid</li>
                        <li><strong>Nama Lengkap:</strong> Wajib</li>
                        <li><strong>Password:</strong> Opsional (akan di-generate otomatis jika kosong)</li>
                    </ul>

                    <hr>

                    <h6 class="fw-bold">Data Lengkap:</h6>
                    <p class="small mb-2">Agar akun langsung aktif, pastikan data berikut terisi:</p>
                    <ul class="small">
                        <li>Jenis Kelamin</li>
                        <li>Tanggal Lahir</li>
                        <li>Alamat</li>
                        <li>Universitas</li>
                        <li>Fakultas</li>
                        <li>Departemen</li>
                    </ul>

                    <div class="alert alert-warning small mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Penting:</strong> Jika data tidak lengkap, akun akan dibuat dengan status <strong>INACTIVE</strong> dan anggota harus melengkapi data terlebih dahulu.
                    </div>
                </div>
            </div>

            <!-- Download Template -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-download me-2"></i>Template CSV</h5>
                </div>
                <div class="card-body text-center">
                    <p class="mb-3">Download template CSV untuk memudahkan proses import</p>
                    <a href="<?= base_url('admin/members/bulk-import/download-template') ?>" class="btn btn-success btn-lg w-100">
                        <i class="fas fa-file-csv me-2"></i>Download Template
                    </a>
                </div>
            </div>
        </div>

        <!-- Upload Form Card -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Upload File Import</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/members/bulk-import/upload') ?>" method="POST" enctype="multipart/form-data" id="uploadForm">
                        <?= csrf_field() ?>

                        <div class="mb-4">
                            <label for="import_file" class="form-label fw-bold">Pilih File CSV</label>
                            <input type="file" class="form-control form-control-lg" id="import_file" name="import_file" accept=".csv" required>
                            <div class="form-text">
                                File CSV dengan encoding UTF-8, maksimal 5 MB
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>Proses Import:
                            </h6>
                            <ol class="mb-0 ps-3">
                                <li>Upload file CSV</li>
                                <li>Sistem akan memvalidasi data</li>
                                <li>Preview data yang akan diimport</li>
                                <li>Konfirmasi dan proses import</li>
                            </ol>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-cloud-upload-alt me-2"></i>Upload dan Validasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Imports (Optional - untuk future enhancement) -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Tips Import</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Gunakan Template</h6>
                                    <p class="text-muted small mb-0">Pastikan format CSV sesuai dengan template yang disediakan</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Cek Email Duplikat</h6>
                                    <p class="text-muted small mb-0">Sistem akan menolak email yang sudah terdaftar</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Format Tanggal</h6>
                                    <p class="text-muted small mb-0">Gunakan format YYYY-MM-DD (contoh: 1990-12-31)</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Validasi Data</h6>
                                    <p class="text-muted small mb-0">Data akan divalidasi sebelum diimport</p>
                                </div>
                            </div>
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
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('import_file');
    if (fileInput.files.length === 0) {
        e.preventDefault();
        alert('Silakan pilih file yang akan diupload');
        return false;
    }

    const file = fileInput.files[0];
    const maxSize = 5 * 1024 * 1024; // 5MB

    if (file.size > maxSize) {
        e.preventDefault();
        alert('Ukuran file terlalu besar. Maksimal 5 MB');
        return false;
    }

    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengupload dan memvalidasi...';
});
</script>
<?= $this->endSection() ?>
