<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Media</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/cms/media') ?>">Media</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <!-- Preview -->
                    <div class="text-center mb-4">
                        <?php if ($media['media_type'] == 'image'): ?>
                            <img src="<?= base_url('writable/uploads/media/' . $media['file_path']) ?>"
                                 class="img-fluid rounded" style="max-height: 300px;">
                        <?php else: ?>
                            <div class="bg-light p-5 rounded">
                                <i class="fas fa-file fa-5x text-muted"></i>
                                <p class="mt-3"><?= esc($media['original_name']) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <form action="<?= base_url('admin/cms/media/edit/' . $media['id']) ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" value="<?= old('title', $media['title']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alt Text</label>
                            <input type="text" class="form-control" name="alt_text" value="<?= old('alt_text', $media['alt_text']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Caption</label>
                            <textarea class="form-control" name="caption" rows="3"><?= old('caption', $media['caption']) ?></textarea>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Tipe: <?= $media['media_type'] ?></small><br>
                                <small class="text-muted">Ukuran: <?= number_format($media['file_size'] / 1024, 2) ?> KB</small><br>
                                <?php if ($media['width'] && $media['height']): ?>
                                    <small class="text-muted">Dimensi: <?= $media['width'] ?> x <?= $media['height'] ?></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update
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
