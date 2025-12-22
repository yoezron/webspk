<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Inbox Kontak</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item">CMS</li>
                    <li class="breadcrumb-item active">Kontak</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="<?= base_url('admin/cms/contact/export') ?>" class="btn btn-success">
                <i class="fas fa-download me-2"></i>Export CSV
            </a>
        </div>
    </div>

    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics -->
    <?php if (!empty($stats)): ?>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h3><?= $stats['total'] ?></h3>
                        <p class="mb-0 text-muted">Total Pesan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body">
                        <h3 class="text-primary"><?= $stats['new'] ?></h3>
                        <p class="mb-0 text-muted">Pesan Baru</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h3><?= $stats['read'] ?></h3>
                        <p class="mb-0 text-muted">Sudah Dibaca</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body">
                        <h3 class="text-success"><?= $stats['replied'] ?></h3>
                        <p class="mb-0 text-muted">Sudah Dibalas</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="new" <?= ($status ?? '') == 'new' ? 'selected' : '' ?>>Baru</option>
                            <option value="read" <?= ($status ?? '') == 'read' ? 'selected' : '' ?>>Sudah Dibaca</option>
                            <option value="replied" <?= ($status ?? '') == 'replied' ? 'selected' : '' ?>>Sudah Dibalas</option>
                            <option value="archived" <?= ($status ?? '') == 'archived' ? 'selected' : '' ?>>Diarsipkan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="card">
        <div class="card-body">
            <?php if (!empty($messages)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Dari</th>
                                <th>Subjek</th>
                                <th>Pesan</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $message): ?>
                                <tr class="<?= $message['status'] == 'new' ? 'table-primary' : '' ?>">
                                    <td>
                                        <strong><?= esc($message['name']) ?></strong><br>
                                        <small class="text-muted"><?= esc($message['email']) ?></small>
                                    </td>
                                    <td><?= esc($message['subject']) ?></td>
                                    <td>
                                        <small><?= esc(substr($message['message'], 0, 100)) ?>...</small>
                                    </td>
                                    <td>
                                        <?php
                                        $badges = [
                                            'new' => 'primary',
                                            'read' => 'info',
                                            'replied' => 'success',
                                            'archived' => 'secondary'
                                        ];
                                        ?>
                                        <span class="badge bg-<?= $badges[$message['status']] ?? 'secondary' ?>">
                                            <?= ucfirst($message['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?= date('d M Y H:i', strtotime($message['created_at'])) ?></small>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= base_url('admin/cms/contact/view/' . $message['id']) ?>"
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (isset($pager)): ?>
                    <div class="mt-4"><?= $pager->links() ?></div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada pesan kontak.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
