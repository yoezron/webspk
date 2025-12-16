<!-- App Sidebar -->
<div class="app-sidebar">
    <div class="logo">
        <a href="<?= base_url('/') ?>" class="logo-icon">
            <span class="logo-text">SPK</span>
        </a>
        <div class="sidebar-user-switcher user-activity-online">
            <a href="#">
                <img src="<?= base_url('assets/neptune/images/avatars/avatar.png') ?>" alt="User Avatar">
                <span class="activity-indicator"></span>
                <span class="user-info-text">
                    <?= esc(session()->get('user_name')) ?><br>
                    <span class="user-state-info">
                        <?php if (session()->get('role_name') === 'admin'): ?>
                            Administrator
                        <?php else: ?>
                            Member
                        <?php endif; ?>
                    </span>
                </span>
            </a>
        </div>
    </div>

    <div class="app-menu">
        <ul class="accordion-menu">
            <li class="sidebar-title">Menu Utama</li>

            <?php if (session()->get('role_name') === 'admin'): ?>
                <!-- Admin Menu -->
                <li class="<?= (current_url() === base_url('admin/dashboard')) ? 'active-page' : '' ?>">
                    <a href="<?= base_url('admin/dashboard') ?>" class="<?= (current_url() === base_url('admin/dashboard')) ? 'active' : '' ?>">
                        <i class="material-icons-two-tone">dashboard</i>Dashboard
                    </a>
                </li>

                <li>
                    <a href="#"><i class="material-icons-two-tone">people</i>Manajemen Anggota<i class="material-icons has-sub-menu">keyboard_arrow_right</i></a>
                    <ul class="sub-menu">
                        <li><a href="<?= base_url('admin/members') ?>">Daftar Anggota</a></li>
                        <li><a href="<?= base_url('admin/members/pending') ?>">Persetujuan Anggota</a></li>
                        <li><a href="<?= base_url('admin/members/suspended') ?>">Anggota Ditangguhkan</a></li>
                    </ul>
                </li>

                <li>
                    <a href="#"><i class="material-icons-two-tone">payment</i>Manajemen Iuran<i class="material-icons has-sub-menu">keyboard_arrow_right</i></a>
                    <ul class="sub-menu">
                        <li><a href="<?= base_url('admin/payments') ?>">Semua Pembayaran</a></li>
                        <li><a href="<?= base_url('admin/payments/pending') ?>">Menunggu Verifikasi</a></li>
                        <li><a href="<?= base_url('admin/payments/verified') ?>">Terverifikasi</a></li>
                    </ul>
                </li>

                <li>
                    <a href="<?= base_url('admin/reports') ?>"><i class="material-icons-two-tone">assessment</i>Laporan</a>
                </li>

            <?php else: ?>
                <!-- Member Menu -->
                <li class="<?= (current_url() === base_url('member/dashboard')) ? 'active-page' : '' ?>">
                    <a href="<?= base_url('member/dashboard') ?>" class="<?= (current_url() === base_url('member/dashboard')) ? 'active' : '' ?>">
                        <i class="material-icons-two-tone">dashboard</i>Dashboard
                    </a>
                </li>

                <li>
                    <a href="#"><i class="material-icons-two-tone">payment</i>Iuran<i class="material-icons has-sub-menu">keyboard_arrow_right</i></a>
                    <ul class="sub-menu">
                        <li><a href="<?= base_url('member/payment') ?>">Bayar Iuran</a></li>
                        <li><a href="<?= base_url('member/payment/history') ?>">Riwayat Pembayaran</a></li>
                    </ul>
                </li>

                <li>
                    <a href="<?= base_url('member/profile') ?>"><i class="material-icons-two-tone">person</i>Profil Saya</a>
                </li>
            <?php endif; ?>

            <li class="sidebar-title">Pengaturan</li>

            <li>
                <a href="<?= base_url('settings') ?>"><i class="material-icons-two-tone">settings</i>Pengaturan</a>
            </li>

            <li>
                <a href="<?= base_url('auth/logout') ?>" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                    <i class="material-icons-two-tone">exit_to_app</i>Keluar
                </a>
            </li>
        </ul>
    </div>
</div>
