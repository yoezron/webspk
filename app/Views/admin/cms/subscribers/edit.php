<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Subscriber</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/cms/subscribers/edit/' . $subscriber['id']) ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" value="<?= old('email', $subscriber['email']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="full_name" value="<?= old('full_name', $subscriber['full_name']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="active" <?= $subscriber['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="unsubscribed" <?= $subscriber['status'] == 'unsubscribed' ? 'selected' : '' ?>>Unsubscribed</option>
                                <option value="bounced" <?= $subscriber['status'] == 'bounced' ? 'selected' : '' ?>>Bounced</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update
                            </button>
                            <a href="<?= base_url('admin/cms/subscribers') ?>" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
