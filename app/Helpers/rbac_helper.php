<?php

use App\Models\RBACPermissionModel;
use App\Models\RBACMenuModel;

if (!function_exists('has_permission')) {
    /**
     * Check if current user has a specific permission
     *
     * @param string $permissionSlug
     * @return bool
     */
    function has_permission(string $permissionSlug): bool
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return false;
        }

        $permissionModel = new RBACPermissionModel();
        return $permissionModel->userHasPermission($userId, $permissionSlug);
    }
}

if (!function_exists('get_user_permissions')) {
    /**
     * Get all permissions for current user
     *
     * @return array
     */
    function get_user_permissions(): array
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return [];
        }

        $permissionModel = new RBACPermissionModel();
        return $permissionModel->getUserPermissions($userId);
    }
}

if (!function_exists('get_user_menus')) {
    /**
     * Get accessible menus for current user
     *
     * @return array
     */
    function get_user_menus(): array
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return [];
        }

        $menuModel = new RBACMenuModel();
        return $menuModel->getUserMenus($userId);
    }
}

if (!function_exists('can_view_menu')) {
    /**
     * Check if user can view a specific menu
     *
     * @param string $menuSlug
     * @return bool
     */
    function can_view_menu(string $menuSlug): bool
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return false;
        }

        $menuModel = new RBACMenuModel();
        return $menuModel->userHasMenuAccess($userId, $menuSlug);
    }
}

if (!function_exists('is_super_admin')) {
    /**
     * Check if current user is super admin
     *
     * @return bool
     */
    function is_super_admin(): bool
    {
        return session()->get('user_role') === 'super_admin';
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if current user is admin or super admin
     *
     * @return bool
     */
    function is_admin(): bool
    {
        $role = session()->get('user_role');
        return in_array($role, ['super_admin', 'admin']);
    }
}

if (!function_exists('has_role')) {
    /**
     * Check if current user has specific role
     *
     * @param string|array $roles
     * @return bool
     */
    function has_role($roles): bool
    {
        $userRole = session()->get('user_role');
        if (!$userRole) {
            return false;
        }

        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }

        return $userRole === $roles;
    }
}

if (!function_exists('assign_role_to_user')) {
    /**
     * Assign role to user
     *
     * @param int $memberId
     * @param int $roleId
     * @return bool
     */
    function assign_role_to_user(int $memberId, int $roleId): bool
    {
        $db = \Config\Database::connect();
        $assignedBy = session()->get('user_id');

        // Check if role already assigned
        $existing = $db->table('rbac_user_roles')
            ->where('member_id', $memberId)
            ->where('role_id', $roleId)
            ->get()
            ->getRow();

        if ($existing) {
            return true; // Already assigned
        }

        // Assign new role
        $data = [
            'member_id' => $memberId,
            'role_id' => $roleId,
            'assigned_by' => $assignedBy,
            'assigned_at' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return $db->table('rbac_user_roles')->insert($data);
    }
}

if (!function_exists('remove_role_from_user')) {
    /**
     * Remove role from user
     *
     * @param int $memberId
     * @param int $roleId
     * @return bool
     */
    function remove_role_from_user(int $memberId, int $roleId): bool
    {
        $db = \Config\Database::connect();
        return $db->table('rbac_user_roles')
            ->where('member_id', $memberId)
            ->where('role_id', $roleId)
            ->delete();
    }
}

if (!function_exists('get_user_roles')) {
    /**
     * Get all roles assigned to a user
     *
     * @param int $memberId
     * @return array
     */
    function get_user_roles(int $memberId): array
    {
        $db = \Config\Database::connect();
        return $db->table('rbac_roles as r')
            ->select('r.*, ur.assigned_at')
            ->join('rbac_user_roles as ur', 'ur.role_id = r.id')
            ->where('ur.member_id', $memberId)
            ->where('r.is_active', 1)
            ->get()
            ->getResultArray();
    }
}
