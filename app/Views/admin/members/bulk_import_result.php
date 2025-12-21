<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Hasil Import Data</h1>
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
    </div>

    <!-- Success Message -->
    <div class="row mb-4">
        <div class="col-12">
            <?php if ($result['success'] > 0 && $result['error'] == 0): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <h4 class="alert-heading">
                        <i class="fas fa-check-circle me-2"></i>Import Berhasil!
                    </h4>
                    <p class="mb-0">
                        Semua data berhasil diimport. Total: <strong><?= $result['success'] ?> anggota</strong>
                    </p>
                </div>
            <?php elseif ($result['success'] > 0 && $result['error'] > 0): ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <h4 class="alert-heading">
                        <i class="fas fa-exclamation-triangle me-2"></i>Import Selesai dengan Peringatan
                    </h4>
                    <p class="mb-0">
                        Berhasil: <strong><?= $result['success'] ?> anggota</strong>,
                        Gagal: <strong><?= $result['error'] ?> data</strong>
                    </p>
                </div>
            <?php else: ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <h4 class="alert-heading">
                        <i class="fas fa-times-circle me-2"></i>Import Gagal
                    </h4>
                    <p class="mb-0">
                        Tidak ada data yang berhasil diimport. Silakan periksa file dan coba lagi.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card <?= $result['success'] > 0 ? 'border-success' : '' ?>">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                    <h2 class="display-4 mb-2"><?= $result['success'] ?></h2>
                    <p class="text-muted mb-0">Data Berhasil Diimport</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card <?= $result['error'] > 0 ? 'border-danger' : '' ?>">
                <div class="card-body text-center">
                    <i class="fas fa-times-circle text-danger fa-4x mb-3"></i>
                    <h2 class="display-4 mb-2"><?= $result['error'] ?></h2>
                    <p class="text-muted mb-0">Data Gagal Diimport</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Details -->
    <?php if ($result['error'] > 0 && !empty($result['errors'])): ?>
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Detail Error (<?= count($result['errors']) ?> error)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-striped">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="50">#</th>
                                <th>Deskripsi Error</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result['errors'] as $index => $error): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= esc($error) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Information Card -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>Informasi Penting
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold">Akun Aktif:</h6>
                    <p class="text-muted">
                        Anggota dengan data lengkap dapat langsung login menggunakan email dan password yang diimport.
                    </p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">Akun Tidak Aktif:</h6>
                    <p class="text-muted">
                        Anggota dengan data tidak lengkap harus melengkapi profil terlebih dahulu sebelum dapat menggunakan sistem.
                    </p>
                </div>
            </div>

            <div class="alert alert-warning mt-3 mb-0">
                <i class="fas fa-key me-2"></i>
                <strong>Password:</strong> Jika password tidak diimport, sistem telah membuat password acak untuk setiap anggota.
                Anggota dapat menggunakan fitur "Lupa Password" untuk mereset password mereka.
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <a href="<?= base_url('admin/members') ?>" class="btn btn-primary">
                        <i class="fas fa-users me-2"></i>Lihat Daftar Anggota
                    </a>
                </div>
                <div>
                    <a href="<?= base_url('admin/members/bulk-import') ?>" class="btn btn-secondary">
                        <i class="fas fa-upload me-2"></i>Import Lagi
                    </a>
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

.display-4 {
    font-size: 3rem;
    font-weight: 300;
}
</style>
<?= $this->endSection() ?>
