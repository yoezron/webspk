<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description d-flex justify-content-between align-items-center">
            <div>
                <h1>Edit Tarif Iuran</h1>
                <span>Perbarui informasi tarif iuran</span>
            </div>
            <div>
                <a href="<?= base_url('admin/dues-rates') ?>" class="btn btn-secondary">
                    <i class="material-icons-outlined">arrow_back</i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Edit Form -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="material-icons-outlined">error</i>
                        <strong>Terjadi kesalahan:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= base_url('admin/dues-rates/edit/' . $rate['id']) ?>">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="rate_name" class="form-label">Nama Tarif <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="rate_name"
                                   name="rate_name"
                                   required
                                   value="<?= old('rate_name', $rate['rate_name']) ?>"
                                   placeholder="Contoh: Iuran Bulanan Standar">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="rate_type" class="form-label">Tipe Tarif <span class="text-danger">*</span></label>
                            <select class="form-select" id="rate_type" name="rate_type" required>
                                <option value="monthly" <?= old('rate_type', $rate['rate_type']) === 'monthly' ? 'selected' : '' ?>>Bulanan</option>
                                <option value="yearly" <?= old('rate_type', $rate['rate_type']) === 'yearly' ? 'selected' : '' ?>>Tahunan</option>
                                <option value="one_time" <?= old('rate_type', $rate['rate_type']) === 'one_time' ? 'selected' : '' ?>>Sekali</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Jumlah Tarif (Rp) <span class="text-danger">*</span></label>
                        <input type="number"
                               class="form-control"
                               id="amount"
                               name="amount"
                               required
                               min="0"
                               step="0.01"
                               value="<?= old('amount', $rate['amount']) ?>"
                               placeholder="50000">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="member_category" class="form-label">Kategori Anggota</label>
                            <select class="form-select" id="member_category" name="member_category">
                                <option value="">Semua Kategori</option>
                                <option value="candidate" <?= old('member_category', $rate['member_category']) === 'candidate' ? 'selected' : '' ?>>Kandidat</option>
                                <option value="active" <?= old('member_category', $rate['member_category']) === 'active' ? 'selected' : '' ?>>Aktif</option>
                            </select>
                            <small class="text-muted">Kosongkan untuk semua kategori</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="region_code" class="form-label">Wilayah</label>
                            <select class="form-select" id="region_code" name="region_code">
                                <option value="">Semua Wilayah</option>
                                <?php foreach ($regions as $region): ?>
                                    <option value="<?= esc($region['region_code']) ?>"
                                            <?= old('region_code', $rate['region_code']) === $region['region_code'] ? 'selected' : '' ?>>
                                        <?= esc($region['region_code']) ?> - <?= esc($region['province_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Kosongkan untuk semua wilayah</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="effective_from" class="form-label">Berlaku Dari <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control"
                                   id="effective_from"
                                   name="effective_from"
                                   required
                                   value="<?= old('effective_from', $rate['effective_from']) ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="effective_to" class="form-label">Berlaku Sampai</label>
                            <input type="date"
                                   class="form-control"
                                   id="effective_to"
                                   name="effective_to"
                                   value="<?= old('effective_to', $rate['effective_to']) ?>">
                            <small class="text-muted">Kosongkan untuk berlaku selamanya</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control"
                                  id="description"
                                  name="description"
                                  rows="3"
                                  placeholder="Deskripsi atau catatan tentang tarif ini..."><?= old('description', $rate['description']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   <?= old('is_active', $rate['is_active']) == '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">
                                Aktifkan tarif ini
                            </label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="material-icons-outlined">save</i> Simpan Tarif
                        </button>
                        <a href="<?= base_url('admin/dues-rates') ?>" class="btn btn-secondary">
                            <i class="material-icons-outlined">cancel</i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="material-icons-outlined">info</i>
                    Informasi
                </h5>
            </div>
            <div class="card-body">
                <h6>Tipe Tarif:</h6>
                <ul class="small">
                    <li><strong>Bulanan:</strong> Tarif yang dibayar setiap bulan</li>
                    <li><strong>Tahunan:</strong> Tarif yang dibayar sekali setahun</li>
                    <li><strong>Sekali:</strong> Tarif yang dibayar satu kali (mis: pendaftaran)</li>
                </ul>

                <hr>

                <h6>Kategori & Wilayah:</h6>
                <p class="small">
                    Anda dapat membuat tarif khusus untuk kategori anggota tertentu (kandidat/aktif)
                    atau wilayah tertentu. Jika kosong, tarif berlaku untuk semua.
                </p>

                <hr>

                <h6>Masa Berlaku:</h6>
                <p class="small">
                    Tentukan kapan tarif mulai berlaku. Jika tidak ada tanggal akhir,
                    tarif akan berlaku selamanya atau sampai dinonaktifkan.
                </p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
