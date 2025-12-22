<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Buat Berita Baru</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/cms/news') ?>">Berita</a></li>
                    <li class="breadcrumb-item active">Buat Baru</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/cms/news') ?>" class="btn btn-secondary">
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
    <form action="<?= base_url('admin/cms/news/create') ?>" method="POST">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Konten Berita</h5>
                    </div>
                    <div class="card-body">
                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Berita <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control <?= session('errors.title') ? 'is-invalid' : '' ?>"
                                   id="title"
                                   name="title"
                                   value="<?= old('title') ?>"
                                   placeholder="Masukkan judul berita..."
                                   required>
                            <?php if (session('errors.title')): ?>
                                <div class="invalid-feedback"><?= session('errors.title') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Slug -->
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug (URL)</label>
                            <input type="text"
                                   class="form-control <?= session('errors.slug') ? 'is-invalid' : '' ?>"
                                   id="slug"
                                   name="slug"
                                   value="<?= old('slug') ?>"
                                   placeholder="Kosongkan untuk generate otomatis dari judul">
                            <small class="form-text text-muted">Contoh: berita-terbaru-tahun-2024</small>
                            <?php if (session('errors.slug')): ?>
                                <div class="invalid-feedback"><?= session('errors.slug') ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Excerpt -->
                        <div class="mb-3">
                            <label for="excerpt" class="form-label">Ringkasan (Excerpt)</label>
                            <textarea class="form-control"
                                      id="excerpt"
                                      name="excerpt"
                                      rows="3"
                                      placeholder="Ringkasan singkat berita untuk preview..."><?= old('excerpt') ?></textarea>
                            <small class="form-text text-muted">Maksimal 200 karakter. Digunakan untuk preview di halaman daftar berita.</small>
                        </div>

                        <!-- Content -->
                        <div class="mb-3">
                            <label for="content_html" class="form-label">Konten Berita <span class="text-danger">*</span></label>
                            <textarea class="form-control"
                                      id="content_html"
                                      name="content_html"
                                      rows="20"
                                      required><?= old('content_html') ?></textarea>
                            <small class="form-text text-muted">
                                Tulis konten berita lengkap. Anda bisa menggunakan HTML untuk formatting.
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
                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="draft" <?= old('status', 'draft') == 'draft' ? 'selected' : '' ?>>
                                    Draft
                                </option>
                                <option value="published" <?= old('status') == 'published' ? 'selected' : '' ?>>
                                    Published
                                </option>
                                <option value="archived" <?= old('status') == 'archived' ? 'selected' : '' ?>>
                                    Archived
                                </option>
                            </select>
                            <small class="text-muted">
                                Draft: Tidak tampil di website<br>
                                Published: Tampil di website<br>
                                Archived: Diarsipkan
                            </small>
                        </div>

                        <!-- Published At -->
                        <div class="mb-3" id="publishedAtGroup">
                            <label for="published_at" class="form-label">Tanggal Publikasi</label>
                            <input type="datetime-local"
                                   class="form-control"
                                   id="published_at"
                                   name="published_at"
                                   value="<?= old('published_at', date('Y-m-d\TH:i')) ?>">
                            <small class="form-text text-muted">Kosongkan untuk menggunakan waktu sekarang</small>
                        </div>
                    </div>
                </div>

                <!-- Cover Image -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Cover Image</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="cover_image_id" class="form-label">Pilih Gambar</label>
                            <select class="form-select" id="cover_image_id" name="cover_image_id">
                                <option value="">- Tidak ada cover -</option>
                                <?php if (!empty($recent_media)): ?>
                                    <?php foreach ($recent_media as $media): ?>
                                        <option value="<?= $media['id'] ?>" <?= old('cover_image_id') == $media['id'] ? 'selected' : '' ?>>
                                            <?= esc($media['title']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Atau <a href="<?= base_url('admin/cms/media/upload') ?>" target="_blank">upload gambar baru</a></small>
                        </div>

                        <!-- Image Preview -->
                        <div id="imagePreview" class="text-center" style="display: none;">
                            <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-2"></i>Simpan Berita
                        </button>
                        <a href="<?= base_url('admin/cms/news') ?>" class="btn btn-secondary w-100">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Auto-generate slug from title
document.getElementById('title').addEventListener('input', function() {
    const title = this.value;
    const slug = title
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();

    // Only auto-fill if slug is empty
    const slugInput = document.getElementById('slug');
    if (!slugInput.value) {
        slugInput.value = slug;
    }
});

// Show/hide published_at based on status
document.getElementById('status').addEventListener('change', function() {
    const publishedAtGroup = document.getElementById('publishedAtGroup');
    if (this.value === 'published') {
        publishedAtGroup.style.display = 'block';
    } else {
        publishedAtGroup.style.display = 'none';
    }
});

// Trigger on page load
document.getElementById('status').dispatchEvent(new Event('change'));
</script>
<?= $this->endSection() ?>
