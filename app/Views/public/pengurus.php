<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!--==============================
    Page Banner
==============================-->
<div class="breadcumb-wrapper" style="background-image: url('<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>');">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Struktur Kepengurusan</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li>Pengurus</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Officers Section
==============================-->
<section class="space">
    <div class="container">

        <!-- Pengurus Pusat -->
        <?php if (!empty($officers_pusat) && count($officers_pusat) > 0): ?>
            <div class="officers-section mb-5">
                <div class="section-title text-center mb-5">
                    <h2>Pengurus Pusat</h2>
                    <p class="text-muted">Struktur Kepengurusan Serikat Pekerja Kampus Tingkat Pusat</p>
                </div>

                <div class="row justify-content-center">
                    <?php foreach ($officers_pusat as $officer): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="officer-card text-center">
                                <div class="officer-photo mb-3">
                                    <?php if (!empty($officer['photo_path'])): ?>
                                        <img src="<?= base_url('writable/uploads/officers/' . $officer['photo_path']) ?>"
                                             alt="<?= esc($officer['member_name']) ?>"
                                             class="rounded-circle img-fluid"
                                             style="width: 150px; height: 150px; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="<?= base_url('assets/img/default-avatar.png') ?>"
                                             alt="<?= esc($officer['member_name']) ?>"
                                             class="rounded-circle img-fluid"
                                             style="width: 150px; height: 150px; object-fit: cover;">
                                    <?php endif; ?>
                                </div>

                                <h5 class="officer-name mb-1"><?= esc($officer['member_name']) ?></h5>
                                <p class="officer-position text-primary mb-2"><?= esc($officer['position_name']) ?></p>

                                <?php if (!empty($officer['period_start']) || !empty($officer['period_end'])): ?>
                                    <p class="officer-period text-muted small">
                                        Periode:
                                        <?= $officer['period_start'] ? date('Y', strtotime($officer['period_start'])) : '?' ?>
                                        -
                                        <?= $officer['period_end'] ? date('Y', strtotime($officer['period_end'])) : 'Sekarang' ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Pengurus Wilayah -->
        <?php if (!empty($officers_wilayah_grouped) && count($officers_wilayah_grouped) > 0): ?>
            <div class="officers-section">
                <div class="section-title text-center mb-5">
                    <h2>Pengurus Wilayah</h2>
                    <p class="text-muted">Struktur Kepengurusan Serikat Pekerja Kampus Tingkat Wilayah</p>
                </div>

                <?php foreach ($officers_wilayah_grouped as $regionCode => $regionOfficers): ?>
                    <div class="region-section mb-5">
                        <h4 class="region-title mb-4">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                            Wilayah <?= esc($regionCode) ?>
                        </h4>

                        <div class="row">
                            <?php foreach ($regionOfficers as $officer): ?>
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                    <div class="officer-card text-center">
                                        <div class="officer-photo mb-3">
                                            <?php if (!empty($officer['photo_path'])): ?>
                                                <img src="<?= base_url('writable/uploads/officers/' . $officer['photo_path']) ?>"
                                                     alt="<?= esc($officer['member_name']) ?>"
                                                     class="rounded-circle img-fluid"
                                                     style="width: 120px; height: 120px; object-fit: cover;">
                                            <?php else: ?>
                                                <img src="<?= base_url('assets/img/default-avatar.png') ?>"
                                                     alt="<?= esc($officer['member_name']) ?>"
                                                     class="rounded-circle img-fluid"
                                                     style="width: 120px; height: 120px; object-fit: cover;">
                                            <?php endif; ?>
                                        </div>

                                        <h6 class="officer-name mb-1"><?= esc($officer['member_name']) ?></h6>
                                        <p class="officer-position text-primary mb-2 small">
                                            <?= esc($officer['position_name']) ?>
                                        </p>

                                        <?php if (!empty($officer['period_start']) || !empty($officer['period_end'])): ?>
                                            <p class="officer-period text-muted small mb-0">
                                                <?= $officer['period_start'] ? date('Y', strtotime($officer['period_start'])) : '?' ?>
                                                -
                                                <?= $officer['period_end'] ? date('Y', strtotime($officer['period_end'])) : 'Sekarang' ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- No Officers Message -->
        <?php if (empty($officers_pusat) && empty($officers_wilayah_grouped)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">Informasi struktur kepengurusan akan segera ditampilkan.</p>
            </div>
        <?php endif; ?>

    </div>
</section>

<?= $this->endSection() ?>
