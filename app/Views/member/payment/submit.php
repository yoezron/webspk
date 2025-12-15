<?= $this->include('layouts/header') ?>

<!--==============================
    Breadcumb
============================== -->
<div class="breadcumb-wrapper" data-bg-src="<?= base_url('assets/img/bg/breadcumb-bg.jpg') ?>">
    <div class="container">
        <div class="breadcumb-content">
            <h1 class="breadcumb-title">Submit Pembayaran Iuran</h1>
            <ul class="breadcumb-menu">
                <li><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                <li><a href="<?= base_url('member/payment') ?>">Pembayaran</a></li>
                <li>Submit</li>
            </ul>
        </div>
    </div>
</div>

<!--==============================
    Payment Submit Section
============================== -->
<section class="space-top space-extra-bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="contact-form-wrap">
                    <div class="title-area mb-30">
                        <h2 class="sec-title">Form Pembayaran Iuran</h2>
                        <p class="sec-text">Silakan lengkapi form berikut untuk submit bukti pembayaran iuran Anda.</p>
                    </div>

                    <form action="<?= base_url('member/payment/process') ?>" method="post" enctype="multipart/form-data" class="contact-form ajax-contact">
                        <?= csrf_field() ?>

                        <div class="row">
                            <!-- Member Info -->
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h5 class="mb-2"><i class="fas fa-user me-2"></i>Data Anggota</h5>
                                    <p class="mb-1"><strong>Nama:</strong> <?= esc($member['full_name']) ?></p>
                                    <p class="mb-1"><strong>No. Anggota:</strong> <?= esc($member['member_number']) ?></p>
                                    <p class="mb-1"><strong>Iuran Bulanan:</strong> Rp <?= number_format($member['monthly_dues_amount'] ?? 0, 0, ',', '.') ?></p>
                                    <?php if ($member['total_arrears'] > 0): ?>
                                        <p class="mb-0 text-danger"><strong>Tunggakan:</strong> Rp <?= number_format($member['total_arrears'], 0, ',', '.') ?> (<?= $member['arrears_months'] ?> bulan)</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Payment Period -->
                            <div class="col-md-6 form-group">
                                <label for="payment_month">Bulan Pembayaran <span class="text-danger">*</span></label>
                                <select name="payment_month" id="payment_month" class="form-select <?= isset($validation) && $validation->hasError('payment_month') ? 'is-invalid' : '' ?>" required>
                                    <option value="">Pilih Bulan</option>
                                    <?php
                                    $months = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                    foreach ($months as $key => $month):
                                    ?>
                                        <option value="<?= $key ?>" <?= old('payment_month') == $key ? 'selected' : '' ?>><?= $month ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($validation) && $validation->hasError('payment_month')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('payment_month') ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="payment_year">Tahun Pembayaran <span class="text-danger">*</span></label>
                                <select name="payment_year" id="payment_year" class="form-select <?= isset($validation) && $validation->hasError('payment_year') ? 'is-invalid' : '' ?>" required>
                                    <option value="">Pilih Tahun</option>
                                    <?php
                                    $currentYear = date('Y');
                                    for ($i = $currentYear; $i >= ($currentYear - 2); $i--):
                                    ?>
                                        <option value="<?= $i ?>" <?= old('payment_year') == $i ? 'selected' : ($i == $currentYear ? 'selected' : '') ?>><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                                <?php if (isset($validation) && $validation->hasError('payment_year')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('payment_year') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Payment Date -->
                            <div class="col-md-6 form-group">
                                <label for="payment_date">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" id="payment_date" class="form-control <?= isset($validation) && $validation->hasError('payment_date') ? 'is-invalid' : '' ?>" value="<?= old('payment_date', date('Y-m-d')) ?>" required max="<?= date('Y-m-d') ?>">
                                <?php if (isset($validation) && $validation->hasError('payment_date')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('payment_date') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Amount -->
                            <div class="col-md-6 form-group">
                                <label for="amount">Jumlah Pembayaran <span class="text-danger">*</span></label>
                                <input type="number" name="amount" id="amount" class="form-control <?= isset($validation) && $validation->hasError('amount') ? 'is-invalid' : '' ?>" value="<?= old('amount', $member['monthly_dues_amount'] ?? 0) ?>" required min="1" step="0.01">
                                <?php if (isset($validation) && $validation->hasError('amount')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('amount') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Payment Method -->
                            <div class="col-md-6 form-group">
                                <label for="payment_method">Metode Pembayaran <span class="text-danger">*</span></label>
                                <select name="payment_method" id="payment_method" class="form-select <?= isset($validation) && $validation->hasError('payment_method') ? 'is-invalid' : '' ?>" required>
                                    <option value="">Pilih Metode</option>
                                    <option value="transfer_bank" <?= old('payment_method') == 'transfer_bank' ? 'selected' : '' ?>>Transfer Bank</option>
                                    <option value="cash" <?= old('payment_method') == 'cash' ? 'selected' : '' ?>>Tunai</option>
                                    <option value="deduction" <?= old('payment_method') == 'deduction' ? 'selected' : '' ?>>Potong Gaji</option>
                                    <option value="other" <?= old('payment_method') == 'other' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                                <?php if (isset($validation) && $validation->hasError('payment_method')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('payment_method') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Payment Reference -->
                            <div class="col-md-6 form-group">
                                <label for="payment_reference">No. Referensi / No. Rekening</label>
                                <input type="text" name="payment_reference" id="payment_reference" class="form-control" value="<?= old('payment_reference') ?>" placeholder="Opsional">
                            </div>

                            <!-- Payment Proof -->
                            <div class="col-12 form-group">
                                <label for="payment_proof">Bukti Pembayaran <span class="text-danger">*</span></label>
                                <input type="file" name="payment_proof" id="payment_proof" class="form-control <?= isset($validation) && $validation->hasError('payment_proof') ? 'is-invalid' : '' ?>" accept=".jpg,.jpeg,.png,.pdf" required>
                                <small class="form-text text-muted">Format: JPG, PNG, PDF. Maksimal 2MB</small>
                                <?php if (isset($validation) && $validation->hasError('payment_proof')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('payment_proof') ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Notes -->
                            <div class="col-12 form-group">
                                <label for="notes">Catatan</label>
                                <textarea name="notes" id="notes" rows="3" class="form-control" placeholder="Catatan tambahan (opsional)"><?= old('notes') ?></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="col-12 form-group mb-0">
                                <button type="submit" class="th-btn">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Pembayaran
                                </button>
                                <a href="<?= base_url('member/payment') ?>" class="th-btn style3 ms-2">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>

<?= $this->include('layouts/footer') ?>
