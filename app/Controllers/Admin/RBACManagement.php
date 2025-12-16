<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RBACRoleModel;
use App\Models\RBACPermissionModel;
use App\Models\MemberModel;
use App\Models\AuditLogModel;

class RBACManagement extends BaseController
{
    protected $roleModel;
    protected $permissionModel;
    protected $memberModel;
    protected $auditModel;

    public function __construct()
    {
        $this->roleModel = new RBACRoleModel();
        $this->permissionModel = new RBACPermissionModel();
        $this->memberModel = new MemberModel();
        $this->auditModel = new AuditLogModel();
        helper(['form', 'url', 'rbac']);
    }

    /**
     * RBAC Dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'RBAC Management',
            'roles_count' => $this->roleModel->where('is_active', 1)->countAllResults(),
            'permissions_count' => $this->permissionModel->where('is_active', 1)->countAllResults(),
            'users_with_roles' => $this->db->table('rbac_user_roles')->countAllResults(),
        ];

        return view('admin/rbac/index', $data);
    }

    /**
     * Manage Roles
     */
    public function roles()
    {
        $roles = $this->roleModel->orderBy('role_name')->findAll();

        // Get permission count for each role
        foreach ($roles as &$role) {
            $role['permissions_count'] = $this->db->table('rbac_role_permissions')
                ->where('role_id', $role['id'])
                ->countAllResults();

            $role['users_count'] = $this->db->table('rbac_user_roles')
                ->where('role_id', $role['id'])
                ->countAllResults();
        }

        $data = [
            'title' => 'Kelola Roles',
            'roles' => $roles,
        ];

        return view('admin/rbac/roles', $data);
    }

    /**
     * Manage Permissions
     */
    public function permissions()
    {
        $permissions = $this->permissionModel->orderBy('permission_group, permission_name')->findAll();

        // Group permissions by group
        $grouped = [];
        foreach ($permissions as $perm) {
            $group = $perm['permission_group'] ?: 'other';
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][] = $perm;
        }

        $data = [
            'title' => 'Kelola Permissions',
            'permissions' => $permissions,
            'grouped_permissions' => $grouped,
        ];

        return view('admin/rbac/permissions', $data);
    }

    /**
     * Assign roles to users
     */
    public function assignRoles()
    {
        $search = $this->request->getGet('search');
        $roleFilter = $this->request->getGet('role');
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;

        // Build query
        $builder = $this->memberModel
            ->select('sp_members.id, sp_members.full_name, sp_members.email, sp_members.member_number, sp_members.membership_status')
            ->where('sp_members.account_status !=', 'deleted');

        if ($search) {
            $builder->groupStart()
                ->like('sp_members.full_name', $search)
                ->orLike('sp_members.email', $search)
                ->orLike('sp_members.member_number', $search)
                ->groupEnd();
        }

        $members = $builder->paginate($perPage);

        // Get roles for each member
        foreach ($members as &$member) {
            $member['roles'] = get_user_roles($member['id']);
        }

        $roles = $this->roleModel->where('is_active', 1)->findAll();

        $data = [
            'title' => 'Assign Roles to Users',
            'members' => $members,
            'roles' => $roles,
            'pager' => $this->memberModel->pager,
            'search' => $search,
        ];

        return view('admin/rbac/assign_roles', $data);
    }

    /**
     * Update user roles (AJAX)
     */
    public function updateUserRoles()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $memberId = $this->request->getPost('member_id');
        $roleIds = $this->request->getPost('role_ids') ?? [];

        if (!$memberId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Member ID required']);
        }

        // Get current roles
        $currentRoles = get_user_roles($memberId);
        $currentRoleIds = array_column($currentRoles, 'id');

        // Remove old roles
        foreach ($currentRoleIds as $roleId) {
            if (!in_array($roleId, $roleIds)) {
                remove_role_from_user($memberId, $roleId);
            }
        }

        // Add new roles
        foreach ($roleIds as $roleId) {
            if (!in_array($roleId, $currentRoleIds)) {
                assign_role_to_user($memberId, $roleId, session()->get('user_id'));
            }
        }

        // Log the change
        $member = $this->memberModel->find($memberId);
        $this->auditModel->log(
            'update',
            'rbac_user_roles',
            $memberId,
            "Updated roles for user: {$member['full_name']}",
            ['roles' => $currentRoleIds],
            ['roles' => $roleIds]
        );

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Roles berhasil diperbarui',
        ]);
    }

    /**
     * Edit role permissions
     */
    public function editRolePermissions($roleId = null)
    {
        if (!$roleId) {
            return redirect()->back()->with('error', 'Role ID tidak valid');
        }

        $role = $this->roleModel->find($roleId);
        if (!$role) {
            return redirect()->back()->with('error', 'Role tidak ditemukan');
        }

        if ($this->request->is('post')) {
            $permissionIds = $this->request->getPost('permission_ids') ?? [];

            // Get current permissions
            $currentPerms = $this->db->table('rbac_role_permissions')
                ->where('role_id', $roleId)
                ->get()
                ->getResultArray();
            $currentPermIds = array_column($currentPerms, 'permission_id');

            // Remove old permissions
            $this->db->table('rbac_role_permissions')
                ->where('role_id', $roleId)
                ->delete();

            // Add new permissions
            foreach ($permissionIds as $permId) {
                $this->db->table('rbac_role_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permId,
                ]);
            }

            // Log the change
            $this->auditModel->log(
                'update',
                'rbac_role_permissions',
                $roleId,
                "Updated permissions for role: {$role['role_name']}",
                ['permissions' => $currentPermIds],
                ['permissions' => $permissionIds]
            );

            return redirect()->to(base_url('admin/settings/rbac/roles'))
                ->with('success', 'Permissions berhasil diperbarui');
        }

        // Get all permissions grouped
        $permissions = $this->permissionModel->orderBy('permission_group, permission_name')->findAll();
        $grouped = [];
        foreach ($permissions as $perm) {
            $group = $perm['permission_group'] ?: 'other';
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][] = $perm;
        }

        // Get current role permissions
        $rolePermissions = $this->db->table('rbac_role_permissions')
            ->where('role_id', $roleId)
            ->get()
            ->getResultArray();
        $rolePermIds = array_column($rolePermissions, 'permission_id');

        $data = [
            'title' => 'Edit Permissions - ' . $role['role_name'],
            'role' => $role,
            'grouped_permissions' => $grouped,
            'role_permission_ids' => $rolePermIds,
        ];

        return view('admin/rbac/edit_role_permissions', $data);
    }

    /**
     * Create new role
     */
    public function createRole()
    {
        if ($this->request->is('post')) {
            $rules = [
                'role_name' => 'required|max_length[100]',
                'role_slug' => 'required|max_length[100]|is_unique[rbac_roles.role_slug]|alpha_dash',
                'description' => 'permit_empty|max_length[255]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $data = [
                'role_name' => $this->request->getPost('role_name'),
                'role_slug' => $this->request->getPost('role_slug'),
                'description' => $this->request->getPost('description'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            ];

            if ($this->roleModel->insert($data)) {
                $this->auditModel->log(
                    'create',
                    'rbac_roles',
                    $this->roleModel->getInsertID(),
                    "Created new role: {$data['role_name']}"
                );

                return redirect()->to(base_url('admin/settings/rbac/roles'))
                    ->with('success', 'Role berhasil ditambahkan');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan role');
        }

        $data = ['title' => 'Tambah Role Baru'];
        return view('admin/rbac/create_role', $data);
    }

    /**
     * Toggle role status
     */
    public function toggleRoleStatus($roleId = null)
    {
        if (!$roleId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Role ID tidak valid']);
        }

        $role = $this->roleModel->find($roleId);
        if (!$role) {
            return $this->response->setJSON(['success' => false, 'message' => 'Role tidak ditemukan']);
        }

        // Prevent deactivating super_admin role
        if ($role['role_slug'] === 'super_admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Role Super Admin tidak dapat dinonaktifkan',
            ]);
        }

        $newStatus = $role['is_active'] ? 0 : 1;

        if ($this->roleModel->update($roleId, ['is_active' => $newStatus])) {
            $this->auditModel->log(
                'update',
                'rbac_roles',
                $roleId,
                $newStatus ? "Activated role: {$role['role_name']}" : "Deactivated role: {$role['role_name']}"
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status role berhasil diperbarui',
                'is_active' => $newStatus,
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui status']);
    }
}
