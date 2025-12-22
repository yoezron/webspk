<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Section Landing Page</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/cms/landing') ?>">Landing Page</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="<?= base_url('admin/cms/landing/edit/' . $section['id']) ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Tipe Section <span class="text-danger">*</span></label>
                            <select class="form-select" name="section_type" required>
                                <option value="hero" <?= $section['section_type'] == 'hero' ? 'selected' : '' ?>>Hero Banner</option>
                                <option value="stats" <?= $section['section_type'] == 'stats' ? 'selected' : '' ?>>Statistics</option>
                                <option value="features" <?= $section['section_type'] == 'features' ? 'selected' : '' ?>>Features</option>
                                <option value="news" <?= $section['section_type'] == 'news' ? 'selected' : '' ?>>News Feed</option>
                                <option value="documents" <?= $section['section_type'] == 'documents' ? 'selected' : '' ?>>Documents</option>
                                <option value="cta" <?= $section['section_type'] == 'cta' ? 'selected' : '' ?>>Call to Action</option>
                                <option value="custom" <?= $section['section_type'] == 'custom' ? 'selected' : '' ?>>Custom HTML</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" value="<?= old('title', $section['title']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subtitle</label>
                            <input type="text" class="form-control" name="subtitle" value="<?= old('subtitle', $section['subtitle']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konten HTML</label>
                            <textarea class="form-control" name="content_html" rows="10"><?= old('content_html', $section['content_html']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Settings JSON</label>
                            <textarea class="form-control" name="settings_json" rows="5"><?= old('settings_json', $section['settings_json']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Background Image</label>
                            <?php if (!empty($section['background_image'])): ?>
                                <div class="mb-2">
                                    <img src="<?= base_url('writable/uploads/landing/' . $section['background_image']) ?>"
                                         class="img-thumbnail" style="max-height: 150px;">
                                </div>
                            <?php endif; ?>
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
                            <input type="number" class="form-control" name="sort_order" value="<?= old('sort_order', $section['sort_order']) ?>">
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" <?= $section['is_active'] ? 'checked' : '' ?>>
                            <label class="form-check-label">Aktifkan section</label>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-2"></i>Update
                        </button>
                        <a href="<?= base_url('admin/cms/landing') ?>" class="btn btn-secondary w-100">Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
