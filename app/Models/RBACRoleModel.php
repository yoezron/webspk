<?php

namespace App\Models;

use CodeIgniter\Model;

class RBACRoleModel extends Model
{
    protected $table = 'rbac_roles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'role_name',
        'role_slug',
        'description',
        'is_active',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'role_name' => 'required|min_length[3]|max_length[50]',
        'role_slug' => 'required|min_length[3]|max_length[50]|alpha_dash|is_unique[rbac_roles.role_slug,id,{id}]',
        'description' => 'permit_empty|max_length[500]',
    ];

    protected $validationMessages = [
        'role_name' => [
            'required' => 'Nama role harus diisi',
            'min_length' => 'Nama role minimal 3 karakter',
        ],
        'role_slug' => [
            'required' => 'Slug role harus diisi',
            'alpha_dash' => 'Slug hanya boleh berisi huruf, angka, underscore, dan dash',
            'is_unique' => 'Slug role sudah digunakan',
        ],
    ];

    /**
     * Get role with permissions
     */
    public function getRoleWithPermissions($roleId)
    {
        $role = $this->find($roleId);
        if (!$role) {
            return null;
        }

        $db = \Config\Database::connect();
        $permissions = $db->table('rbac_permissions as p')
            ->select('p.*')
            ->join('rbac_role_permissions as rp', 'rp.permission_id = p.id')
            ->where('rp.role_id', $roleId)
            ->where('p.is_active', 1)
            ->get()
            ->getResultArray();

        $role['permissions'] = $permissions;
        return $role;
    }

    /**
     * Get all active roles
     */
    public function getActiveRoles()
    {
        return $this->where('is_active', 1)->findAll();
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions($roleId, array $permissionIds)
    {
        $db = \Config\Database::connect();

        // Delete existing permissions
        $db->table('rbac_role_permissions')->where('role_id', $roleId)->delete();

        // Insert new permissions
        $data = [];
        foreach ($permissionIds as $permId) {
            $data[] = [
                'role_id' => $roleId,
                'permission_id' => $permId,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        if (!empty($data)) {
            return $db->table('rbac_role_permissions')->insertBatch($data);
        }

        return true;
    }

    /**
     * Get users with this role
     */
    public function getRoleUsers($roleId)
    {
        $db = \Config\Database::connect();
        return $db->table('sp_members as m')
            ->select('m.id, m.full_name, m.email, m.member_number, m.status')
            ->join('rbac_user_roles as ur', 'ur.member_id = m.id')
            ->where('ur.role_id', $roleId)
            ->get()
            ->getResultArray();
    }
}
