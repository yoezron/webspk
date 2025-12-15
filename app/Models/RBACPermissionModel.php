<?php

namespace App\Models;

use CodeIgniter\Model;

class RBACPermissionModel extends Model
{
    protected $table = 'rbac_permissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'permission_name',
        'permission_slug',
        'permission_group',
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
        'permission_name' => 'required|min_length[3]|max_length[100]',
        'permission_slug' => 'required|min_length[3]|max_length[100]|alpha_dash|is_unique[rbac_permissions.permission_slug,id,{id}]',
        'permission_group' => 'required|max_length[50]',
    ];

    protected $validationMessages = [
        'permission_name' => [
            'required' => 'Nama permission harus diisi',
        ],
        'permission_slug' => [
            'required' => 'Slug permission harus diisi',
            'is_unique' => 'Slug permission sudah digunakan',
        ],
        'permission_group' => [
            'required' => 'Grup permission harus diisi',
        ],
    ];

    /**
     * Get all permissions grouped by permission_group
     */
    public function getGroupedPermissions()
    {
        $permissions = $this->where('is_active', 1)
            ->orderBy('permission_group', 'ASC')
            ->orderBy('permission_name', 'ASC')
            ->findAll();

        $grouped = [];
        foreach ($permissions as $perm) {
            $grouped[$perm['permission_group']][] = $perm;
        }

        return $grouped;
    }

    /**
     * Get permissions for a specific role
     */
    public function getPermissionsByRole($roleId)
    {
        $db = \Config\Database::connect();
        return $db->table('rbac_permissions as p')
            ->select('p.*')
            ->join('rbac_role_permissions as rp', 'rp.permission_id = p.id')
            ->where('rp.role_id', $roleId)
            ->where('p.is_active', 1)
            ->get()
            ->getResultArray();
    }

    /**
     * Check if user has permission
     */
    public function userHasPermission($memberId, $permissionSlug)
    {
        $db = \Config\Database::connect();
        $result = $db->table('rbac_permissions as p')
            ->select('p.id')
            ->join('rbac_role_permissions as rp', 'rp.permission_id = p.id')
            ->join('rbac_user_roles as ur', 'ur.role_id = rp.role_id')
            ->where('ur.member_id', $memberId)
            ->where('p.permission_slug', $permissionSlug)
            ->where('p.is_active', 1)
            ->get()
            ->getRow();

        return $result !== null;
    }

    /**
     * Get all permissions for a user (across all their roles)
     */
    public function getUserPermissions($memberId)
    {
        $db = \Config\Database::connect();
        return $db->table('rbac_permissions as p')
            ->select('p.permission_slug, p.permission_name, p.permission_group')
            ->join('rbac_role_permissions as rp', 'rp.permission_id = p.id')
            ->join('rbac_user_roles as ur', 'ur.role_id = rp.role_id')
            ->where('ur.member_id', $memberId)
            ->where('p.is_active', 1)
            ->groupBy('p.id')
            ->get()
            ->getResultArray();
    }
}
