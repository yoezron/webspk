<?php

namespace App\Models;

use CodeIgniter\Model;

class CoordinatorRegionModel extends Model
{
    protected $table = 'coordinator_regions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'coordinator_id',
        'region_code',
        'assigned_at',
        'assigned_by',
        'is_active',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'coordinator_id' => 'required|integer',
        'region_code' => 'required|max_length[10]',
    ];

    /**
     * Assign region to coordinator
     */
    public function assignRegion(int $coordinatorId, string $regionCode, ?int $assignedBy = null): bool
    {
        // Check if already assigned
        $existing = $this->where('coordinator_id', $coordinatorId)
            ->where('region_code', $regionCode)
            ->first();

        if ($existing) {
            // Reactivate if inactive
            if ($existing['is_active'] == 0) {
                return $this->update($existing['id'], [
                    'is_active' => 1,
                    'assigned_at' => date('Y-m-d H:i:s'),
                    'assigned_by' => $assignedBy ?? session()->get('user_id'),
                ]);
            }
            return true; // Already active
        }

        // Create new assignment
        return $this->insert([
            'coordinator_id' => $coordinatorId,
            'region_code' => $regionCode,
            'assigned_at' => date('Y-m-d H:i:s'),
            'assigned_by' => $assignedBy ?? session()->get('user_id'),
            'is_active' => 1,
        ]) !== false;
    }

    /**
     * Remove region from coordinator
     */
    public function removeRegion(int $coordinatorId, string $regionCode): bool
    {
        return $this->where('coordinator_id', $coordinatorId)
            ->where('region_code', $regionCode)
            ->set(['is_active' => 0])
            ->update();
    }

    /**
     * Get coordinator for a region
     */
    public function getRegionCoordinator(string $regionCode): ?array
    {
        return $this->select('coordinator_regions.*, sp_members.full_name, sp_members.email, sp_members.phone_number')
            ->join('sp_members', 'sp_members.id = coordinator_regions.coordinator_id')
            ->where('coordinator_regions.region_code', $regionCode)
            ->where('coordinator_regions.is_active', 1)
            ->first();
    }

    /**
     * Get all regions for a coordinator
     */
    public function getCoordinatorRegions(int $coordinatorId): array
    {
        return $this->select('coordinator_regions.*, sp_region_codes.province_name')
            ->join('sp_region_codes', 'sp_region_codes.region_code = coordinator_regions.region_code')
            ->where('coordinator_regions.coordinator_id', $coordinatorId)
            ->where('coordinator_regions.is_active', 1)
            ->findAll();
    }

    /**
     * Get coordinators with their assigned regions
     */
    public function getCoordinatorsWithRegions(): array
    {
        $coordinators = $this->db->table('sp_members')
            ->select('sp_members.id, sp_members.full_name, sp_members.email, sp_members.member_number')
            ->join('rbac_user_roles', 'rbac_user_roles.member_id = sp_members.id')
            ->join('rbac_roles', 'rbac_roles.id = rbac_user_roles.role_id')
            ->where('rbac_roles.role_slug', 'coordinator')
            ->where('sp_members.account_status', 'active')
            ->groupBy('sp_members.id')
            ->get()
            ->getResultArray();

        foreach ($coordinators as &$coordinator) {
            $coordinator['regions'] = $this->getCoordinatorRegions($coordinator['id']);
            $coordinator['region_count'] = count($coordinator['regions']);
        }

        return $coordinators;
    }

    /**
     * Check if user is coordinator for specific region
     */
    public function isCoordinatorForRegion(int $userId, string $regionCode): bool
    {
        return $this->where('coordinator_id', $userId)
            ->where('region_code', $regionCode)
            ->where('is_active', 1)
            ->countAllResults() > 0;
    }

    /**
     * Get regional statistics for coordinator
     */
    public function getRegionalStats(int $coordinatorId): array
    {
        $regions = $this->getCoordinatorRegions($coordinatorId);
        $regionCodes = array_column($regions, 'region_code');

        if (empty($regionCodes)) {
            return [
                'total_members' => 0,
                'active_members' => 0,
                'candidates' => 0,
                'total_collected' => 0,
            ];
        }

        $db = \Config\Database::connect();

        // Member statistics
        $stats = [
            'total_members' => $db->table('sp_members')
                ->whereIn('region_code', $regionCodes)
                ->countAllResults(),

            'active_members' => $db->table('sp_members')
                ->whereIn('region_code', $regionCodes)
                ->where('membership_status', 'active')
                ->countAllResults(),

            'candidates' => $db->table('sp_members')
                ->whereIn('region_code', $regionCodes)
                ->where('membership_status', 'candidate')
                ->countAllResults(),

            'total_collected' => $db->table('sp_dues_payments')
                ->select('SUM(amount) as total')
                ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->whereIn('sp_members.region_code', $regionCodes)
                ->where('sp_dues_payments.status', 'verified')
                ->get()
                ->getRow()
                ->total ?? 0,
        ];

        return $stats;
    }
}
