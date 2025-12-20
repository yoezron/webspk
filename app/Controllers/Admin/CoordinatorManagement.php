<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\CoordinatorRegionModel;
use App\Models\RegionCodeModel;
use App\Models\AuditLogModel;

class CoordinatorManagement extends BaseController
{
    protected $memberModel;
    protected $coordinatorRegionModel;
    protected $regionModel;
    protected $auditModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->coordinatorRegionModel = new CoordinatorRegionModel();
        $this->regionModel = new RegionCodeModel();
        $this->auditModel = new AuditLogModel();
        helper(['form', 'url', 'rbac']);
    }

    /**
     * Coordinator assignment dashboard
     */
    public function index()
    {
        // Get all coordinators with their regions
        $coordinators = $this->coordinatorRegionModel->getCoordinatorsWithRegions();

        // Get regions with stats
        $regions = $this->regionModel->getRegionsWithStats();

        $data = [
            'title' => 'Manajemen Koordinator Wilayah',
            'coordinators' => $coordinators,
            'regions' => $regions,
        ];

        return view('admin/coordinator/index', $data);
    }

    /**
     * Assign coordinator to regions
     */
    public function assign($coordinatorId = null)
    {
        if (!$coordinatorId) {
            return redirect()->back()->with('error', 'Coordinator ID tidak valid');
        }

        $coordinator = $this->memberModel->find($coordinatorId);

        if (!$coordinator) {
            return redirect()->back()->with('error', 'Coordinator tidak ditemukan');
        }

        // Verify coordinator role
        if (!has_role(['coordinator'], $coordinatorId)) {
            return redirect()->back()->with('error', 'User bukan koordinator');
        }

        if ($this->request->is('post')) {
            $regionCodes = $this->request->getPost('region_codes') ?? [];

            // Get current regions
            $currentRegions = $this->coordinatorRegionModel->getCoordinatorRegions($coordinatorId);
            $currentCodes = array_column($currentRegions, 'region_code');

            // Remove regions not in new list
            foreach ($currentCodes as $code) {
                if (!in_array($code, $regionCodes)) {
                    $this->coordinatorRegionModel->removeRegion($coordinatorId, $code);
                }
            }

            // Add new regions
            foreach ($regionCodes as $code) {
                if (!in_array($code, $currentCodes)) {
                    $this->coordinatorRegionModel->assignRegion(
                        $coordinatorId,
                        $code,
                        session()->get('user_id')
                    );
                }
            }

            // Log the change
            $this->auditModel->log(
                'update',
                'coordinator_regions',
                $coordinatorId,
                "Updated region assignments for coordinator: {$coordinator['full_name']}",
                ['regions' => $currentCodes],
                ['regions' => $regionCodes]
            );

            return redirect()->to(base_url('admin/coordinators'))
                ->with('success', 'Region assignment berhasil diperbarui');
        }

        // Get all active regions with member counts
        $allRegions = $this->regionModel->getActiveRegions();

        // Add member counts to regions
        foreach ($allRegions as &$region) {
            $region['member_count'] = $this->db->table('sp_members')
                ->where('region_code', $region['region_code'])
                ->where('membership_status', 'active')
                ->countAllResults();
        }

        // Get coordinator's current regions
        $assignedRegions = $this->coordinatorRegionModel->getCoordinatorRegions($coordinatorId);

        $data = [
            'title' => 'Assign Koordinator ke Wilayah',
            'coordinator' => $coordinator,
            'all_regions' => $allRegions,
            'assigned_regions' => $assignedRegions,
        ];

        return view('admin/coordinator/assign', $data);
    }

    /**
     * Regional statistics
     */
    public function regionalStats()
    {
        $regionCode = $this->request->getGet('region');

        if ($regionCode) {
            // Get specific region stats
            $region = $this->regionModel->getByCode($regionCode);

            if (!$region) {
                return redirect()->back()->with('error', 'Region tidak ditemukan');
            }

            $stats = $this->getRegionStatistics($regionCode);

            $data = [
                'title' => 'Statistik Wilayah - ' . $region['province_name'],
                'region' => $region,
                'stats' => $stats,
            ];

            return view('admin/coordinator/region_stats', $data);
        }

        // Show all regions
        $regions = $this->regionModel->getRegionsWithStats();

        $data = [
            'title' => 'Statistik Regional',
            'regions' => $regions,
        ];

        return view('admin/coordinator/regional_stats', $data);
    }

    /**
     * Get statistics for a specific region
     */
    private function getRegionStatistics(string $regionCode): array
    {
        $db = \Config\Database::connect();

        $stats = [
            'total_members' => $db->table('sp_members')
                ->where('region_code', $regionCode)
                ->countAllResults(),

            'active_members' => $db->table('sp_members')
                ->where('region_code', $regionCode)
                ->where('membership_status', 'active')
                ->countAllResults(),

            'candidates' => $db->table('sp_members')
                ->where('region_code', $regionCode)
                ->where('membership_status', 'candidate')
                ->countAllResults(),

            'total_collected' => $db->table('sp_dues_payments')
                ->selectSum('amount')
                ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->where('sp_members.region_code', $regionCode)
                ->where('sp_dues_payments.status', 'verified')
                ->get()
                ->getRow()
                ->amount ?? 0,

            'monthly_collection' => $db->table('sp_dues_payments')
                ->selectSum('amount')
                ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->where('sp_members.region_code', $regionCode)
                ->where('sp_dues_payments.status', 'verified')
                ->where('sp_dues_payments.payment_year', date('Y'))
                ->where('sp_dues_payments.payment_month', (int)date('m'))
                ->get()
                ->getRow()
                ->amount ?? 0,
        ];

        // Get coordinator
        $coordinator = $this->coordinatorRegionModel->getRegionCoordinator($regionCode);
        $stats['coordinator'] = $coordinator;

        return $stats;
    }

    /**
     * Unassign coordinator from region (AJAX)
     */
    public function unassign()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $coordinatorId = $this->request->getPost('coordinator_id');
        $regionCode = $this->request->getPost('region_code');

        if (!$coordinatorId || !$regionCode) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
        }

        if ($this->coordinatorRegionModel->removeRegion($coordinatorId, $regionCode)) {
            // Log the change
            $coordinator = $this->memberModel->find($coordinatorId);
            $this->auditModel->log(
                'delete',
                'coordinator_regions',
                null,
                "Unassigned coordinator {$coordinator['full_name']} from region {$regionCode}"
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Coordinator berhasil di-unassign dari wilayah',
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal unassign coordinator']);
    }
}
