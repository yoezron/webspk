<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Pengurus</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/cms/officers') ?>">Pengurus</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/cms/officers') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

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

    <form action="<?= base_url('admin/cms/officers/edit/' . $officer['id']) ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Data Pengurus</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="member_id" class="form-label">Pilih Member <span class="text-danger">*</span></label>
                            <select class="form-select" id="member_id" name="member_id" required>
                                <?php if (!empty($members)): ?>
                                    <?php foreach ($members as $member): ?>
                                        <option value="<?= $member['id'] ?>"
                                                <?= old('member_id', $officer['member_id']) == $member['id'] ? 'selected' : '' ?>>
                                            <?= esc($member['full_name']) ?> (<?= esc($member['email']) ?>)
                                            <?php if (!empty($member['region_code'])): ?>
                                                - <?= esc($member['region_code']) ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="position_name" class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="position_name" name="position_name"
                                   value="<?= old('position_name', $officer['position_name']) ?>"
                                   placeholder="Contoh: Ketua Umum, Sekretaris, Bendahara"
                                   required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="level" class="form-label">Level <span class="text-danger">*</span></label>
                                <select class="form-select" id="level" name="level" required>
                                    <option value="pusat" <?= old('level', $officer['level']) == 'pusat' ? 'selected' : '' ?>>Pusat</option>
                                    <option value="wilayah" <?= old('level', $officer['level']) == 'wilayah' ? 'selected' : '' ?>>Wilayah</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3" id="regionCodeGroup">
                                <label for="region_code" class="form-label">Kode Wilayah</label>
                                <input type="text" class="form-control" id="region_code" name="region_code"
                                       value="<?= old('region_code', $officer['region_code']) ?>"
                                       placeholder="Contoh: JKT, BDG, SBY">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="period_start" class="form-label">Periode Mulai</label>
                                <input type="date" class="form-control" id="period_start" name="period_start"
                                       value="<?= old('period_start', $officer['period_start']) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="period_end" class="form-label">Periode Selesai</label>
                                <input type="date" class="form-control" id="period_end" name="period_end"
                                       value="<?= old('period_end', $officer['period_end']) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Urutan Tampil</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order"
                                   value="<?= old('sort_order', $officer['sort_order']) ?>">
                            <small class="text-muted">Semakin kecil angka, semakin di depan urutannya</small>
                        </div>

                        <div class="mb-3">
                            <label for="photo" class="form-label">Foto Pengurus</label>

                            <?php if (!empty($officer['photo_path'])): ?>
                                <div class="mb-2">
                                    <img src="<?= base_url('writable/uploads/officers/' . $officer['photo_path']) ?>"
                                         alt="Current Photo"
                                         class="img-thumbnail"
                                         style="max-height: 150px;">
                                    <p class="small text-muted mt-1">Foto saat ini</p>
                                </div>
                            <?php endif; ?>

                            <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                            <small class="text-muted">Upload foto baru untuk mengganti. Format: JPG, PNG. Maksimal 2MB.</small>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                   <?= old('is_active', $officer['is_active']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">
                                Aktifkan pengurus
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-2"></i>Update Pengurus
                        </button>
                        <a href="<?= base_url('admin/cms/officers') ?>" class="btn btn-secondary w-100">
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
document.getElementById('level').addEventListener('change', function() {
    const regionCodeGroup = document.getElementById('regionCodeGroup');
    if (this.value === 'wilayah') {
        regionCodeGroup.style.display = 'block';
    } else {
        regionCodeGroup.style.display = 'none';
    }
});

// Trigger on page load
document.getElementById('level').dispatchEvent(new Event('change'));
</script>
<?= $this->endSection() ?>
