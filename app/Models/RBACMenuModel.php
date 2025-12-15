<?php

namespace App\Models;

use CodeIgniter\Model;

class RBACMenuModel extends Model
{
    protected $table = 'rbac_menus';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'parent_id',
        'menu_name',
        'menu_slug',
        'menu_url',
        'menu_icon',
        'menu_order',
        'is_active',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get menu hierarchy (main menus with submenus)
     */
    public function getMenuHierarchy()
    {
        $mainMenus = $this->where('parent_id', null)
            ->where('is_active', 1)
            ->orderBy('menu_order', 'ASC')
            ->findAll();

        foreach ($mainMenus as &$menu) {
            $menu['submenus'] = $this->where('parent_id', $menu['id'])
                ->where('is_active', 1)
                ->orderBy('menu_order', 'ASC')
                ->findAll();
        }

        return $mainMenus;
    }

    /**
     * Get menus accessible to user based on their permissions
     */
    public function getUserMenus($memberId)
    {
        $db = \Config\Database::connect();

        // Get all menu IDs accessible by user
        $accessibleMenuIds = $db->table('rbac_menus as m')
            ->select('m.id')
            ->join('rbac_menu_permissions as mp', 'mp.menu_id = m.id')
            ->join('rbac_role_permissions as rp', 'rp.permission_id = mp.permission_id')
            ->join('rbac_user_roles as ur', 'ur.role_id = rp.role_id')
            ->where('ur.member_id', $memberId)
            ->where('m.is_active', 1)
            ->groupBy('m.id')
            ->get()
            ->getResultArray();

        $menuIds = array_column($accessibleMenuIds, 'id');

        if (empty($menuIds)) {
            return [];
        }

        // Get main menus
        $mainMenus = $this->whereIn('id', $menuIds)
            ->where('parent_id', null)
            ->where('is_active', 1)
            ->orderBy('menu_order', 'ASC')
            ->findAll();

        // Get submenus for each main menu
        foreach ($mainMenus as &$menu) {
            $menu['submenus'] = $this->whereIn('id', $menuIds)
                ->where('parent_id', $menu['id'])
                ->where('is_active', 1)
                ->orderBy('menu_order', 'ASC')
                ->findAll();
        }

        return $mainMenus;
    }

    /**
     * Check if user has access to menu
     */
    public function userHasMenuAccess($memberId, $menuSlug)
    {
        $db = \Config\Database::connect();
        $result = $db->table('rbac_menus as m')
            ->select('m.id')
            ->join('rbac_menu_permissions as mp', 'mp.menu_id = m.id')
            ->join('rbac_role_permissions as rp', 'rp.permission_id = mp.permission_id')
            ->join('rbac_user_roles as ur', 'ur.role_id = rp.role_id')
            ->where('ur.member_id', $memberId)
            ->where('m.menu_slug', $menuSlug)
            ->where('m.is_active', 1)
            ->get()
            ->getRow();

        return $result !== null;
    }
}
