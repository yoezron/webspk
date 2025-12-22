<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Upload Media</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/cms/media') ?>">Media</a></li>
                    <li class="breadcrumb-item active">Upload</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url('admin/cms/media/upload') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label">Pilih File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="media_file" required>
                            <small class="text-muted">Maksimal 10MB. Format: JPG, PNG, PDF, DOC, XLS, dll.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" class="form-control" name="title" placeholder="Nama file akan digunakan jika kosong">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alt Text (untuk gambar)</label>
                            <input type="text" class="form-control" name="alt_text">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Caption</label>
                            <textarea class="form-control" name="caption" rows="3"></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Upload
                            </button>
                            <a href="<?= base_url('admin/cms/media') ?>" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
