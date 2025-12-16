<!-- App Header -->
<div class="app-header">
    <nav class="navbar navbar-light navbar-expand-lg">
        <div class="container-fluid">
            <div class="navbar-nav" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link hide-sidebar-toggle-button" href="#"><i class="material-icons">first_page</i></a>
                    </li>
                    <?php if (session()->get('role_name') === 'admin'): ?>
                    <li class="nav-item dropdown hidden-on-mobile">
                        <a class="nav-link dropdown-toggle" href="#" id="quickActionsDropDown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="material-icons">add</i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="quickActionsDropDown">
                            <li><a class="dropdown-item" href="<?= base_url('admin/members/add') ?>">Tambah Anggota</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('admin/payments/verify') ?>">Verifikasi Pembayaran</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('admin/reports') ?>">Buat Laporan</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="d-flex">
                <ul class="navbar-nav">
                    <li class="nav-item hidden-on-mobile">
                        <a class="nav-link active" href="<?= base_url('/') ?>">Beranda</a>
                    </li>
                    <?php if (session()->get('role_name') === 'admin'): ?>
                    <li class="nav-item hidden-on-mobile">
                        <a class="nav-link" href="<?= base_url('admin/reports') ?>">Laporan</a>
                    </li>
                    <li class="nav-item hidden-on-mobile">
                        <a class="nav-link" href="<?= base_url('admin/members') ?>">Anggota</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link toggle-search" href="#"><i class="material-icons">search</i></a>
                    </li>
                    <li class="nav-item hidden-on-mobile">
                        <a class="nav-link nav-notifications-toggle" id="notificationsDropDown" href="#" data-bs-toggle="dropdown">
                            <?php
                            $notification_count = 0; // TODO: Implement notification count
                            echo $notification_count > 0 ? $notification_count : '0';
                            ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end notifications-dropdown" aria-labelledby="notificationsDropDown">
                            <h6 class="dropdown-header">Notifikasi</h6>
                            <div class="notifications-dropdown-list">
                                <?php if ($notification_count > 0): ?>
                                    <!-- TODO: Loop through notifications -->
                                <?php else: ?>
                                    <div class="p-3 text-center text-muted">
                                        <p class="mb-0">Tidak ada notifikasi baru</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item hidden-on-mobile">
                        <a class="nav-link" href="<?= base_url('settings') ?>">
                            <i class="material-icons">settings</i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>
