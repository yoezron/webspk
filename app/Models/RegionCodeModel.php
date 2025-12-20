<?php

namespace App\Models;

use CodeIgniter\Model;

class RegionCodeModel extends Model
{
    protected $table = 'sp_region_codes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'province_name',
        'region_code',
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
        'province_name' => 'required|max_length[100]',
        'region_code' => 'required|max_length[10]|is_unique[sp_region_codes.region_code,id,{id}]',
    ];

    /**
     * Get all active regions
     */
    public function getActiveRegions(): array
    {
        return $this->where('is_active', 1)
            ->orderBy('province_name')
            ->findAll();
    }

    /**
     * Get regions as dropdown options
     */
    public function getRegionsDropdown(): array
    {
        $regions = $this->getActiveRegions();
        $options = [];

        foreach ($regions as $region) {
            $options[$region['region_code']] = $region['province_name'];
        }

        return $options;
    }

    /**
     * Get region by code
     */
    public function getByCode(string $code): ?array
    {
        return $this->where('region_code', $code)->first();
    }

    /**
     * Get regions with member counts
     */
    public function getRegionsWithStats(): array
    {
        $regions = $this->getActiveRegions();

        foreach ($regions as &$region) {
            // Count active members in this region
            $region['member_count'] = $this->db->table('sp_members')
                ->where('region_code', $region['region_code'])
                ->where('membership_status', 'active')
                ->countAllResults();

            // Get coordinator for this region
            $coordinator = $this->db->table('coordinator_regions')
                ->select('sp_members.full_name as coordinator_name, sp_members.id as coordinator_id')
                ->join('sp_members', 'sp_members.id = coordinator_regions.coordinator_id')
                ->where('coordinator_regions.region_code', $region['region_code'])
                ->where('coordinator_regions.is_active', 1)
                ->get()
                ->getRowArray();

            $region['coordinator'] = $coordinator;
        }

        return $regions;
    }

    /**
     * Get regions assigned to a coordinator
     */
    public function getCoordinatorRegions(int $coordinatorId): array
    {
        return $this->select('sp_region_codes.*')
            ->join('coordinator_regions', 'coordinator_regions.region_code = sp_region_codes.region_code')
            ->where('coordinator_regions.coordinator_id', $coordinatorId)
            ->where('coordinator_regions.is_active', 1)
            ->findAll();
    }
}
