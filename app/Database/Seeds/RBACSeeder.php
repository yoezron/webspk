<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RBACSeeder extends Seeder
{
    public function run()
    {
        // 1. Insert Roles
        $roles = [
            [
                'role_name' => 'Super Admin',
                'role_slug' => 'super_admin',
                'description' => 'Full system access with all permissions',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role_name' => 'Admin',
                'role_slug' => 'admin',
                'description' => 'Administrative access to manage members and payments',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role_name' => 'Koordinator',
                'role_slug' => 'coordinator',
                'description' => 'Regional coordinator with limited administrative access',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role_name' => 'Bendahara',
                'role_slug' => 'treasurer',
                'description' => 'Treasurer with payment verification access',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role_name' => 'Anggota',
                'role_slug' => 'member',
                'description' => 'Regular member with basic access',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'role_name' => 'Calon Anggota',
                'role_slug' => 'candidate',
                'description' => 'Candidate member pending approval',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('rbac_roles')->insertBatch($roles);

        // 2. Insert Permissions
        $permissions = [
            // Member Management Permissions
            ['permission_name' => 'View Members', 'permission_slug' => 'members.view', 'permission_group' => 'members', 'description' => 'View member list and details'],
            ['permission_name' => 'Create Members', 'permission_slug' => 'members.create', 'permission_group' => 'members', 'description' => 'Add new members'],
            ['permission_name' => 'Edit Members', 'permission_slug' => 'members.edit', 'permission_group' => 'members', 'description' => 'Edit member information'],
            ['permission_name' => 'Delete Members', 'permission_slug' => 'members.delete', 'permission_group' => 'members', 'description' => 'Delete members'],
            ['permission_name' => 'Approve Members', 'permission_slug' => 'members.approve', 'permission_group' => 'members', 'description' => 'Approve or reject member applications'],
            ['permission_name' => 'Suspend Members', 'permission_slug' => 'members.suspend', 'permission_group' => 'members', 'description' => 'Suspend or activate members'],

            // Payment Permissions
            ['permission_name' => 'View Payments', 'permission_slug' => 'payments.view', 'permission_group' => 'payments', 'description' => 'View payment list and history'],
            ['permission_name' => 'Submit Payments', 'permission_slug' => 'payments.submit', 'permission_group' => 'payments', 'description' => 'Submit payment proofs'],
            ['permission_name' => 'Verify Payments', 'permission_slug' => 'payments.verify', 'permission_group' => 'payments', 'description' => 'Verify or reject payments'],
            ['permission_name' => 'Delete Payments', 'permission_slug' => 'payments.delete', 'permission_group' => 'payments', 'description' => 'Delete payment records'],

            // Dashboard Permissions
            ['permission_name' => 'View Admin Dashboard', 'permission_slug' => 'dashboard.admin', 'permission_group' => 'dashboard', 'description' => 'Access admin dashboard'],
            ['permission_name' => 'View Member Dashboard', 'permission_slug' => 'dashboard.member', 'permission_group' => 'dashboard', 'description' => 'Access member dashboard'],

            // Profile Permissions
            ['permission_name' => 'View Own Profile', 'permission_slug' => 'profile.view_own', 'permission_group' => 'profile', 'description' => 'View own profile'],
            ['permission_name' => 'Edit Own Profile', 'permission_slug' => 'profile.edit_own', 'permission_group' => 'profile', 'description' => 'Edit own profile'],
            ['permission_name' => 'View All Profiles', 'permission_slug' => 'profile.view_all', 'permission_group' => 'profile', 'description' => 'View all member profiles'],

            // Reports Permissions
            ['permission_name' => 'View Reports', 'permission_slug' => 'reports.view', 'permission_group' => 'reports', 'description' => 'View system reports'],
            ['permission_name' => 'Export Reports', 'permission_slug' => 'reports.export', 'permission_group' => 'reports', 'description' => 'Export reports to Excel/PDF'],

            // System Settings
            ['permission_name' => 'Manage Roles', 'permission_slug' => 'system.roles', 'permission_group' => 'system', 'description' => 'Manage roles and permissions'],
            ['permission_name' => 'Manage Settings', 'permission_slug' => 'system.settings', 'permission_group' => 'system', 'description' => 'Manage system settings'],
            ['permission_name' => 'View Audit Logs', 'permission_slug' => 'system.audit', 'permission_group' => 'system', 'description' => 'View audit logs'],

            // Forum Permissions
            ['permission_name' => 'View Forum', 'permission_slug' => 'forum.view', 'permission_group' => 'forum', 'description' => 'View forum threads'],
            ['permission_name' => 'Create Threads', 'permission_slug' => 'forum.create', 'permission_group' => 'forum', 'description' => 'Create new forum threads'],
            ['permission_name' => 'Moderate Forum', 'permission_slug' => 'forum.moderate', 'permission_group' => 'forum', 'description' => 'Moderate forum content'],

            // Survey Permissions
            ['permission_name' => 'View Surveys', 'permission_slug' => 'surveys.view', 'permission_group' => 'surveys', 'description' => 'View and respond to surveys'],
            ['permission_name' => 'Create Surveys', 'permission_slug' => 'surveys.create', 'permission_group' => 'surveys', 'description' => 'Create new surveys'],
            ['permission_name' => 'View Survey Results', 'permission_slug' => 'surveys.results', 'permission_group' => 'surveys', 'description' => 'View survey results'],
        ];

        foreach ($permissions as &$perm) {
            $perm['is_active'] = 1;
            $perm['created_at'] = date('Y-m-d H:i:s');
        }
        $this->db->table('rbac_permissions')->insertBatch($permissions);

        // 3. Assign Permissions to Roles

        // Get role and permission IDs
        $rolesData = $this->db->table('rbac_roles')->get()->getResultArray();
        $permissionsData = $this->db->table('rbac_permissions')->get()->getResultArray();

        $roleMap = [];
        foreach ($rolesData as $role) {
            $roleMap[$role['role_slug']] = $role['id'];
        }

        $permMap = [];
        foreach ($permissionsData as $perm) {
            $permMap[$perm['permission_slug']] = $perm['id'];
        }

        $rolePermissions = [];

        // Super Admin - ALL PERMISSIONS
        foreach ($permMap as $permId) {
            $rolePermissions[] = [
                'role_id' => $roleMap['super_admin'],
                'permission_id' => $permId,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        // Admin - Most permissions except system management
        $adminPerms = [
            'members.view', 'members.create', 'members.edit', 'members.approve', 'members.suspend',
            'payments.view', 'payments.verify', 'payments.delete',
            'dashboard.admin', 'profile.view_all',
            'reports.view', 'reports.export',
            'forum.view', 'forum.moderate',
            'surveys.view', 'surveys.create', 'surveys.results',
        ];
        foreach ($adminPerms as $slug) {
            if (isset($permMap[$slug])) {
                $rolePermissions[] = [
                    'role_id' => $roleMap['admin'],
                    'permission_id' => $permMap[$slug],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }
        }

        // Coordinator - Regional management
        $coordinatorPerms = [
            'members.view', 'members.approve',
            'payments.view',
            'dashboard.admin',
            'reports.view',
            'forum.view', 'forum.moderate',
        ];
        foreach ($coordinatorPerms as $slug) {
            if (isset($permMap[$slug])) {
                $rolePermissions[] = [
                    'role_id' => $roleMap['coordinator'],
                    'permission_id' => $permMap[$slug],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }
        }

        // Treasurer - Payment management
        $treasurerPerms = [
            'members.view',
            'payments.view', 'payments.verify',
            'dashboard.admin',
            'reports.view', 'reports.export',
        ];
        foreach ($treasurerPerms as $slug) {
            if (isset($permMap[$slug])) {
                $rolePermissions[] = [
                    'role_id' => $roleMap['treasurer'],
                    'permission_id' => $permMap[$slug],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }
        }

        // Member - Basic access
        $memberPerms = [
            'payments.view', 'payments.submit',
            'dashboard.member',
            'profile.view_own', 'profile.edit_own',
            'forum.view', 'forum.create',
            'surveys.view',
        ];
        foreach ($memberPerms as $slug) {
            if (isset($permMap[$slug])) {
                $rolePermissions[] = [
                    'role_id' => $roleMap['member'],
                    'permission_id' => $permMap[$slug],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }
        }

        // Candidate - Very limited access
        $candidatePerms = [
            'profile.view_own',
        ];
        foreach ($candidatePerms as $slug) {
            if (isset($permMap[$slug])) {
                $rolePermissions[] = [
                    'role_id' => $roleMap['candidate'],
                    'permission_id' => $permMap[$slug],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }
        }

        $this->db->table('rbac_role_permissions')->insertBatch($rolePermissions);

        // 4. Insert Menu Structure
        $menus = [
            // Admin Main Menus
            ['parent_id' => null, 'menu_name' => 'Dashboard', 'menu_slug' => 'admin-dashboard', 'menu_url' => '/admin/dashboard', 'menu_icon' => 'fas fa-tachometer-alt', 'menu_order' => 1],
            ['parent_id' => null, 'menu_name' => 'Anggota', 'menu_slug' => 'admin-members', 'menu_url' => '/admin/members', 'menu_icon' => 'fas fa-users', 'menu_order' => 2],
            ['parent_id' => null, 'menu_name' => 'Pembayaran', 'menu_slug' => 'admin-payments', 'menu_url' => '/admin/payments', 'menu_icon' => 'fas fa-money-bill-wave', 'menu_order' => 3],
            ['parent_id' => null, 'menu_name' => 'Laporan', 'menu_slug' => 'admin-reports', 'menu_url' => '/admin/reports', 'menu_icon' => 'fas fa-chart-bar', 'menu_order' => 4],
            ['parent_id' => null, 'menu_name' => 'Forum', 'menu_slug' => 'admin-forum', 'menu_url' => '/admin/forum', 'menu_icon' => 'fas fa-comments', 'menu_order' => 5],
            ['parent_id' => null, 'menu_name' => 'Survei', 'menu_slug' => 'admin-surveys', 'menu_url' => '/admin/surveys', 'menu_icon' => 'fas fa-poll', 'menu_order' => 6],
            ['parent_id' => null, 'menu_name' => 'Pengaturan', 'menu_slug' => 'admin-settings', 'menu_url' => '#', 'menu_icon' => 'fas fa-cog', 'menu_order' => 7],
        ];

        foreach ($menus as &$menu) {
            $menu['is_active'] = 1;
            $menu['created_at'] = date('Y-m-d H:i:s');
        }
        $this->db->table('rbac_menus')->insertBatch($menus);

        // Get menu IDs for submenus
        $mainMenus = $this->db->table('rbac_menus')->where('parent_id', null)->get()->getResultArray();
        $menuMap = [];
        foreach ($mainMenus as $menu) {
            $menuMap[$menu['menu_slug']] = $menu['id'];
        }

        // Submenus for Anggota
        $submenus = [
            ['parent_id' => $menuMap['admin-members'], 'menu_name' => 'Daftar Anggota', 'menu_slug' => 'members-list', 'menu_url' => '/admin/members', 'menu_icon' => null, 'menu_order' => 1],
            ['parent_id' => $menuMap['admin-members'], 'menu_name' => 'Persetujuan', 'menu_slug' => 'members-pending', 'menu_url' => '/admin/members/pending', 'menu_icon' => null, 'menu_order' => 2],

            // Submenus for Pembayaran
            ['parent_id' => $menuMap['admin-payments'], 'menu_name' => 'Riwayat Pembayaran', 'menu_slug' => 'payments-history', 'menu_url' => '/admin/payments', 'menu_icon' => null, 'menu_order' => 1],
            ['parent_id' => $menuMap['admin-payments'], 'menu_name' => 'Verifikasi', 'menu_slug' => 'payments-pending', 'menu_url' => '/admin/payments/pending', 'menu_icon' => null, 'menu_order' => 2],

            // Submenus for Pengaturan
            ['parent_id' => $menuMap['admin-settings'], 'menu_name' => 'Role & Permission', 'menu_slug' => 'settings-rbac', 'menu_url' => '/admin/settings/rbac', 'menu_icon' => null, 'menu_order' => 1],
            ['parent_id' => $menuMap['admin-settings'], 'menu_name' => 'Audit Log', 'menu_slug' => 'settings-audit', 'menu_url' => '/admin/settings/audit', 'menu_icon' => null, 'menu_order' => 2],
        ];

        foreach ($submenus as &$submenu) {
            $submenu['is_active'] = 1;
            $submenu['created_at'] = date('Y-m-d H:i:s');
        }
        $this->db->table('rbac_menus')->insertBatch($submenus);

        // 5. Link Menus to Permissions
        $allMenus = $this->db->table('rbac_menus')->get()->getResultArray();
        $menuPermissions = [];

        foreach ($allMenus as $menu) {
            // Map menu slugs to permissions
            $menuPermMap = [
                'admin-dashboard' => 'dashboard.admin',
                'admin-members' => 'members.view',
                'members-list' => 'members.view',
                'members-pending' => 'members.approve',
                'admin-payments' => 'payments.view',
                'payments-history' => 'payments.view',
                'payments-pending' => 'payments.verify',
                'admin-reports' => 'reports.view',
                'admin-forum' => 'forum.view',
                'admin-surveys' => 'surveys.view',
                'settings-rbac' => 'system.roles',
                'settings-audit' => 'system.audit',
            ];

            if (isset($menuPermMap[$menu['menu_slug']]) && isset($permMap[$menuPermMap[$menu['menu_slug']]])) {
                $menuPermissions[] = [
                    'menu_id' => $menu['id'],
                    'permission_id' => $permMap[$menuPermMap[$menu['menu_slug']]],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }
        }

        if (!empty($menuPermissions)) {
            $this->db->table('rbac_menu_permissions')->insertBatch($menuPermissions);
        }

        echo "RBAC seeder completed successfully!\n";
    }
}
