<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detail Pesan Kontak</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/cms/contact') ?>">Kontak</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/cms/contact') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <?php
                        $badges = ['new' => 'primary', 'read' => 'info', 'replied' => 'success', 'archived' => 'secondary'];
                        ?>
                        <span class="badge bg-<?= $badges[$message['status']] ?? 'secondary' ?>">
                            <?= ucfirst($message['status']) ?>
                        </span>
                        <?= esc($message['subject']) ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Dari:</strong></p>
                                <p><?= esc($message['name']) ?><br>
                                   <a href="mailto:<?= esc($message['email']) ?>"><?= esc($message['email']) ?></a></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Tanggal:</strong></p>
                                <p><?= date('d F Y, H:i', strtotime($message['created_at'])) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6>Pesan:</h6>
                        <p style="white-space: pre-wrap;"><?= esc($message['message']) ?></p>
                    </div>

                    <?php if (!empty($message['reply_note'])): ?>
                        <div class="alert alert-info">
                            <h6><i class="fas fa-sticky-note me-2"></i>Catatan Balasan:</h6>
                            <p class="mb-0" style="white-space: pre-wrap;"><?= esc($message['reply_note']) ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Add Reply Note Form -->
                    <form action="<?= base_url('admin/cms/contact/add-note/' . $message['id']) ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label">Tambah Catatan Balasan:</label>
                            <textarea class="form-control" name="reply_note" rows="4"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Catatan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <?php if ($message['status'] != 'replied'): ?>
                        <a href="<?= base_url('admin/cms/contact/mark-replied/' . $message['id']) ?>"
                           class="btn btn-success w-100 mb-2"
                           onclick="return confirm('Tandai sebagai sudah dibalas?')">
                            <i class="fas fa-check me-2"></i>Tandai Sudah Dibalas
                        </a>
                    <?php endif; ?>

                    <a href="mailto:<?= esc($message['email']) ?>?subject=Re: <?= urlencode($message['subject']) ?>"
                       class="btn btn-info w-100 mb-2">
                        <i class="fas fa-reply me-2"></i>Balas via Email
                    </a>

                    <?php if ($message['status'] != 'archived'): ?>
                        <a href="<?= base_url('admin/cms/contact/archive/' . $message['id']) ?>"
                           class="btn btn-secondary w-100 mb-2"
                           onclick="return confirm('Arsipkan pesan ini?')">
                            <i class="fas fa-archive me-2"></i>Arsipkan
                        </a>
                    <?php endif; ?>

                    <hr>

                    <button class="btn btn-danger w-100"
                            onclick="confirmDelete()">
                        <i class="fas fa-trash me-2"></i>Hapus Pesan
                    </button>
                </div>
            </div>

            <?php if (!empty($message['assigned_to_name'])): ?>
                <div class="card">
                    <div class="card-body">
                        <h6>Ditugaskan ke:</h6>
                        <p class="mb-0"><?= esc($message['assigned_to_name']) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" action="<?= base_url('admin/cms/contact/delete/' . $message['id']) ?>" style="display: none;">
    <?= csrf_field() ?>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete() {
    if (confirm('Hapus pesan ini?')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
<?= $this->endSection() ?>
