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
                        <?php
                        $roles = get_user_roles(session()->get('user_id'));
                        $roleNames = array_column($roles, 'role_name');
                        echo esc(implode(', ', $roleNames) ?: 'Member');
                        ?>
                    </span>
                </span>
            </a>
        </div>
    </div>

    <div class="app-menu">
        <ul class="accordion-menu">
            <li class="sidebar-title">Menu Utama</li>

            <?php if (has_role(['super_admin', 'admin'])): ?>
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
                        <li><a href="<?= base_url('admin/members/bulk-import') ?>">Import Data Anggota</a></li>
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
                    <a href="#"><i class="material-icons-two-tone">map</i>Koordinator Wilayah<i class="material-icons has-sub-menu">keyboard_arrow_right</i></a>
                    <ul class="sub-menu">
                        <li><a href="<?= base_url('admin/coordinators') ?>">Kelola Koordinator</a></li>
                        <li><a href="<?= base_url('admin/coordinators/stats') ?>">Statistik Regional</a></li>
                    </ul>
                </li>

                <li>
                    <a href="<?= base_url('admin/dues-rates') ?>"><i class="material-icons-two-tone">attach_money</i>Tarif Iuran</a>
                </li>

                <li>
                    <a href="<?= base_url('admin/analytics') ?>"><i class="material-icons-two-tone">analytics</i>Analytics</a>
                </li>

                <li>
                    <a href="<?= base_url('admin/reports') ?>"><i class="material-icons-two-tone">assessment</i>Laporan</a>
                </li>

            <?php elseif (has_role(['coordinator'])): ?>
                <!-- Coordinator Menu -->
                <li class="<?= (current_url() === base_url('coordinator/dashboard')) ? 'active-page' : '' ?>">
                    <a href="<?= base_url('coordinator/dashboard') ?>" class="<?= (current_url() === base_url('coordinator/dashboard')) ? 'active' : '' ?>">
                        <i class="material-icons-two-tone">dashboard</i>Dashboard Regional
                    </a>
                </li>

                <li>
                    <a href="#"><i class="material-icons-two-tone">people</i>Anggota Wilayah<i class="material-icons has-sub-menu">keyboard_arrow_right</i></a>
                    <ul class="sub-menu">
                        <li><a href="<?= base_url('coordinator/members') ?>">Daftar Anggota</a></li>
                        <li><a href="<?= base_url('coordinator/members/pending') ?>">Persetujuan Pending</a></li>
                    </ul>
                </li>

                <li>
                    <a href="#"><i class="material-icons-two-tone">payment</i>Iuran Regional<i class="material-icons has-sub-menu">keyboard_arrow_right</i></a>
                    <ul class="sub-menu">
                        <li><a href="<?= base_url('coordinator/payments') ?>">Pembayaran Wilayah</a></li>
                        <li><a href="<?= base_url('coordinator/payments/pending') ?>">Verifikasi Pending</a></li>
                    </ul>
                </li>

                <li>
                    <a href="<?= base_url('coordinator/reports') ?>"><i class="material-icons-two-tone">assessment</i>Laporan Regional</a>
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

            <?php if (is_super_admin()): ?>
                <li>
                    <a href="#"><i class="material-icons-two-tone">admin_panel_settings</i>Super Admin<i class="material-icons has-sub-menu">keyboard_arrow_right</i></a>
                    <ul class="sub-menu">
                        <li><a href="<?= base_url('admin/settings') ?>">System Settings</a></li>
                        <li><a href="<?= base_url('admin/settings/rbac') ?>">RBAC Management</a></li>
                        <li><a href="<?= base_url('admin/settings/audit') ?>">Audit Log</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <li>
                <a href="<?= base_url('auth/logout') ?>" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                    <i class="material-icons-two-tone">exit_to_app</i>Keluar
                </a>
            </li>
        </ul>
    </div>
</div>
