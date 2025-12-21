<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Tambah Halaman Baru</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/cms/pages') ?>">Halaman</a></li>
                    <li class="breadcrumb-item active">Tambah Baru</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/cms/pages') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <form action="<?= base_url('admin/cms/pages/create') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Konten Halaman</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Halaman <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control <?= session('errors.title') ? 'is-invalid' : '' ?>"
                                   id="title"
                                   name="title"
                                   value="<?= old('title') ?>"
                                   required>
                            <?php if (session('errors.title')): ?>
                                <div class="invalid-feedback"><?= session('errors.title') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug (URL)</label>
                            <input type="text"
                                   class="form-control <?= session('errors.slug') ? 'is-invalid' : '' ?>"
                                   id="slug"
                                   name="slug"
                                   value="<?= old('slug') ?>"
                                   placeholder="Kosongkan untuk generate otomatis">
                            <small class="form-text text-muted">Contoh: tentang-kami, visi-misi</small>
                            <?php if (session('errors.slug')): ?>
                                <div class="invalid-feedback"><?= session('errors.slug') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="content_html" class="form-label">Konten <span class="text-danger">*</span></label>
                            <textarea class="form-control"
                                      id="content_html"
                                      name="content_html"
                                      rows="20"
                                      required><?= old('content_html') ?></textarea>
                            <small class="form-text text-muted">
                                Gunakan HTML editor atau tulis HTML langsung
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Publish Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Pengaturan Publikasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="draft" <?= old('status') == 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="published" <?= old('status') == 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="archived" <?= old('status') == 'archived' ? 'selected' : '' ?>>Archived</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="visibility" class="form-label">Visibility</label>
                            <select class="form-select" id="visibility" name="visibility">
                                <option value="public" <?= old('visibility') == 'public' ? 'selected' : '' ?>>Public</option>
                                <option value="member_only" <?= old('visibility') == 'member_only' ? 'selected' : '' ?>>Member Only</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="template" class="form-label">Template</label>
                            <select class="form-select" id="template" name="template">
                                <option value="default" <?= old('template') == 'default' ? 'selected' : '' ?>>Default</option>
                                <option value="legal" <?= old('template') == 'legal' ? 'selected' : '' ?>>Legal</option>
                                <option value="contact" <?= old('template') == 'contact' ? 'selected' : '' ?>>Contact</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number"
                                   class="form-control"
                                   id="sort_order"
                                   name="sort_order"
                                   value="<?= old('sort_order', 0) ?>">
                            <small class="form-text text-muted">Untuk pengurutan menu</small>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-2"></i>Simpan Halaman
                        </button>
                        <a href="<?= base_url('admin/cms/pages') ?>" class="btn btn-secondary w-100">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- TinyMCE or CKEditor Integration (Optional) -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#content_html',
    height: 500,
    menubar: true,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'preview', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
});

// Auto-generate slug from title
document.getElementById('title').addEventListener('blur', function() {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value) {
        const title = this.value;
        const slug = title.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
        slugInput.value = slug;
    }
});
</script>

<?= $this->endSection() ?>
