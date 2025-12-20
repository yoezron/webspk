<?= $this->extend('layouts/neptune_main') ?>

<?= $this->section('title') ?>
<?= esc($title) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="row">
    <div class="col">
        <div class="page-description">
            <h1>Persetujuan Anggota Pending</h1>
            <span>Review dan rekomendasikan persetujuan anggota baru di wilayah Anda</span>
        </div>
    </div>
</div>

<?php if (isset($message)): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning">
                <i class="material-icons-outlined">info</i>
                <?= esc($message) ?>
            </div>
        </div>
    </div>
<?php else: ?>

<!-- Assigned Regions Info -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="material-icons-outlined">map</i>
                    Wilayah Koordinasi Anda
                </h6>
                <div class="mt-2">
                    <?php foreach ($assigned_regions as $region): ?>
                        <span class="badge badge-primary me-1">
                            <?= esc($region['region_code']) ?> - <?= esc($region['province_name']) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Members List -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="material-icons-outlined">pending_actions</i>
                    Anggota Menunggu Persetujuan (<?= count($members) ?>)
                </h5>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="material-icons-outlined">check_circle</i>
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="material-icons-outlined">error</i>
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (empty($members)): ?>
                    <div class="alert alert-info">
                        <i class="material-icons-outlined">info</i>
                        Tidak ada anggota yang menunggu persetujuan di wilayah Anda.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Wilayah</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Rekomendasi</th>
                                    <th style="width: 200px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($members as $index => $member): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <strong><?= esc($member['full_name']) ?></strong>
                                            <?php if (isset($member['coordinator_recommendation'])): ?>
                                                <br>
                                                <small class="text-muted">
                                                    <?php if ($member['coordinator_recommendation'] === 'approved'): ?>
                                                        <span class="badge badge-success badge-sm">
                                                            <i class="material-icons-outlined" style="font-size: 12px;">check</i>
                                                            Direkomendasikan
                                                        </span>
                                                    <?php elseif ($member['coordinator_recommendation'] === 'rejected'): ?>
                                                        <span class="badge badge-danger badge-sm">
                                                            <i class="material-icons-outlined" style="font-size: 12px;">close</i>
                                                            Ditolak
                                                        </span>
                                                    <?php endif; ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($member['email']) ?></td>
                                        <td><?= esc($member['phone_number'] ?? '-') ?></td>
                                        <td>
                                            <span class="badge badge-light">
                                                <?= esc($member['province_name'] ?? $member['region_code']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d M Y', strtotime($member['created_at'])) ?></td>
                                        <td>
                                            <?php if (isset($member['coordinator_recommendation'])): ?>
                                                <?php if ($member['coordinator_recommendation'] === 'approved'): ?>
                                                    <span class="badge badge-success">Approved</span>
                                                <?php elseif ($member['coordinator_recommendation'] === 'rejected'): ?>
                                                    <span class="badge badge-danger">Rejected</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Belum direview</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('coordinator/members/view/' . $member['id']) ?>"
                                                   class="btn btn-info"
                                                   title="Lihat Detail">
                                                    <i class="material-icons-outlined">visibility</i>
                                                </a>

                                                <?php if (!isset($member['coordinator_recommendation']) || $member['coordinator_recommendation'] === null): ?>
                                                    <button type="button"
                                                            class="btn btn-success"
                                                            onclick="approveModal(<?= $member['id'] ?>, '<?= esc($member['full_name']) ?>')"
                                                            title="Rekomendasikan">
                                                        <i class="material-icons-outlined">check</i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-danger"
                                                            onclick="rejectModal(<?= $member['id'] ?>, '<?= esc($member['full_name']) ?>')"
                                                            title="Tolak">
                                                        <i class="material-icons-outlined">close</i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rekomendasikan Persetujuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" id="approveForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin merekomendasikan persetujuan untuk anggota:</p>
                    <p class="fw-bold" id="approveMemberName"></p>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Tambahkan catatan atau komentar..."></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="material-icons-outlined">info</i>
                        Rekomendasi Anda akan dikirim ke admin untuk persetujuan final.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="material-icons-outlined">check</i> Rekomendasikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tolak Anggota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" id="rejectForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak anggota:</p>
                    <p class="fw-bold" id="rejectMemberName"></p>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" required placeholder="Jelaskan alasan penolakan..."></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="material-icons-outlined">warning</i>
                        Penolakan akan dikirim ke admin untuk keputusan final.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="material-icons-outlined">close</i> Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveModal(memberId, memberName) {
    document.getElementById('approveMemberName').textContent = memberName;
    document.getElementById('approveForm').action = '<?= base_url('coordinator/members/approve') ?>/' + memberId;
    const modal = new bootstrap.Modal(document.getElementById('approveModal'));
    modal.show();
}

function rejectModal(memberId, memberName) {
    document.getElementById('rejectMemberName').textContent = memberName;
    document.getElementById('rejectForm').action = '<?= base_url('coordinator/members/reject') ?>/' + memberId;
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}
</script>

<?= $this->endSection() ?>
