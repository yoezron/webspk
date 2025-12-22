<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Tambah Section Landing Page</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/cms/landing') ?>">Landing Page</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="<?= base_url('admin/cms/landing/create') ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Tipe Section <span class="text-danger">*</span></label>
                            <select class="form-select" name="section_type" required>
                                <option value="hero">Hero Banner</option>
                                <option value="stats">Statistics</option>
                                <option value="features">Features</option>
                                <option value="news">News Feed</option>
                                <option value="documents">Documents</option>
                                <option value="cta">Call to Action</option>
                                <option value="custom">Custom HTML</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" value="<?= old('title') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subtitle</label>
                            <input type="text" class="form-control" name="subtitle" value="<?= old('subtitle') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konten HTML</label>
                            <textarea class="form-control" name="content_html" rows="10"><?= old('content_html') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Settings JSON</label>
                            <textarea class="form-control" name="settings_json" rows="5" placeholder='{"key": "value"}'><?= old('settings_json') ?></textarea>
                            <small class="text-muted">Format JSON untuk konfigurasi tambahan</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Background Image</label>
                            <input type="file" class="form-control" name="background_image" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Urutan</label>
                            <input type="number" class="form-control" name="sort_order" value="<?= old('sort_order', 0) ?>">
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                            <label class="form-check-label">Aktifkan section</label>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                        <a href="<?= base_url('admin/cms/landing') ?>" class="btn btn-secondary w-100">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
