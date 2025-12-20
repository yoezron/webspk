<?php

namespace App\Controllers\Coordinator;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\DuesPaymentModel;
use App\Models\CoordinatorRegionModel;
use App\Models\RegionCodeModel;

class Dashboard extends BaseController
{
    protected $memberModel;
    protected $paymentModel;
    protected $coordinatorRegionModel;
    protected $regionModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->paymentModel = new DuesPaymentModel();
        $this->coordinatorRegionModel = new CoordinatorRegionModel();
        $this->regionModel = new RegionCodeModel();
        helper(['form', 'url', 'rbac']);
    }

    /**
     * Coordinator Dashboard with Regional Statistics
     */
    public function index()
    {
        $userId = session()->get('user_id');

        // Get coordinator's assigned regions
        $assignedRegions = $this->coordinatorRegionModel->getCoordinatorRegions($userId);
        $regionCodes = array_column($assignedRegions, 'region_code');

        // If no regions assigned, show message
        if (empty($regionCodes)) {
            $data = [
                'title' => 'Dashboard Koordinator',
                'no_regions' => true,
            ];
            return view('coordinator/dashboard_neptune', $data);
        }

        // Get regional statistics
        $stats = $this->getRegionalStatistics($regionCodes);

        // Get member statistics by region
        $membersByRegion = $this->getMembersByRegion($regionCodes);

        // Get payment statistics
        $paymentStats = $this->getPaymentStatistics($regionCodes);

        // Get recent registrations in assigned regions
        $recentRegistrations = $this->memberModel
            ->select('id, full_name, email, region_code, created_at, membership_status')
            ->whereIn('region_code', $regionCodes)
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        // Get pending approvals in assigned regions
        $pendingApprovals = $this->memberModel
            ->select('id, full_name, email, region_code, created_at')
            ->whereIn('region_code', $regionCodes)
            ->where('membership_status', 'candidate')
            ->where('account_status', 'pending')
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        // Get member growth chart data
        $memberGrowthChart = $this->getMemberGrowthChart($regionCodes);

        // Get payment trend chart data
        $paymentTrendChart = $this->getPaymentTrendChart($regionCodes);

        $data = [
            'title' => 'Dashboard Koordinator Wilayah',
            'assigned_regions' => $assignedRegions,
            'stats' => $stats,
            'members_by_region' => $membersByRegion,
            'payment_stats' => $paymentStats,
            'recent_registrations' => $recentRegistrations,
            'pending_approvals' => $pendingApprovals,
            'chart_data' => [
                'member_growth' => $memberGrowthChart,
                'payment_trend' => $paymentTrendChart,
            ],
        ];

        return view('coordinator/dashboard_neptune', $data);
    }

    /**
     * Get regional statistics
     */
    private function getRegionalStatistics(array $regionCodes): array
    {
        $db = \Config\Database::connect();

        return [
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

            'pending_approvals' => $db->table('sp_members')
                ->whereIn('region_code', $regionCodes)
                ->where('membership_status', 'candidate')
                ->where('account_status', 'pending')
                ->countAllResults(),

            'total_collected' => $db->table('sp_dues_payments')
                ->selectSum('amount')
                ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->whereIn('sp_members.region_code', $regionCodes)
                ->where('sp_dues_payments.status', 'verified')
                ->get()
                ->getRow()
                ->amount ?? 0,

            'pending_payments' => $db->table('sp_dues_payments')
                ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->whereIn('sp_members.region_code', $regionCodes)
                ->where('sp_dues_payments.status', 'pending')
                ->countAllResults(),

            'monthly_collection' => $db->table('sp_dues_payments')
                ->selectSum('amount')
                ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->whereIn('sp_members.region_code', $regionCodes)
                ->where('sp_dues_payments.status', 'verified')
                ->where('sp_dues_payments.payment_year', date('Y'))
                ->where('sp_dues_payments.payment_month', (int)date('m'))
                ->get()
                ->getRow()
                ->amount ?? 0,
        ];
    }

    /**
     * Get member counts by region
     */
    private function getMembersByRegion(array $regionCodes): array
    {
        $db = \Config\Database::connect();

        $result = $db->table('sp_members')
            ->select('region_code, sp_region_codes.province_name, COUNT(*) as member_count')
            ->join('sp_region_codes', 'sp_region_codes.region_code = sp_members.region_code', 'left')
            ->whereIn('sp_members.region_code', $regionCodes)
            ->where('sp_members.membership_status', 'active')
            ->groupBy('sp_members.region_code')
            ->orderBy('member_count', 'DESC')
            ->get()
            ->getResultArray();

        return $result;
    }

    /**
     * Get payment statistics
     */
    private function getPaymentStatistics(array $regionCodes): array
    {
        $db = \Config\Database::connect();

        return [
            'total_payments' => $db->table('sp_dues_payments')
                ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->whereIn('sp_members.region_code', $regionCodes)
                ->where('sp_dues_payments.status', 'verified')
                ->countAllResults(),

            'this_month' => $db->table('sp_dues_payments')
                ->selectSum('amount')
                ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->whereIn('sp_members.region_code', $regionCodes)
                ->where('sp_dues_payments.status', 'verified')
                ->where('sp_dues_payments.payment_year', date('Y'))
                ->where('sp_dues_payments.payment_month', (int)date('m'))
                ->get()
                ->getRow()
                ->amount ?? 0,

            'this_year' => $db->table('sp_dues_payments')
                ->selectSum('amount')
                ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->whereIn('sp_members.region_code', $regionCodes)
                ->where('sp_dues_payments.status', 'verified')
                ->where('sp_dues_payments.payment_year', date('Y'))
                ->get()
                ->getRow()
                ->amount ?? 0,
        ];
    }

    /**
     * Get member growth chart (last 6 months)
     */
    private function getMemberGrowthChart(array $regionCodes): array
    {
        $db = \Config\Database::connect();
        $months = [];
        $totalMembers = [];
        $newMembers = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $endDate = date('Y-m-t', strtotime($date . '-01'));

            // Total members up to end of month
            $total = $db->table('sp_members')
                ->whereIn('region_code', $regionCodes)
                ->where('created_at <=', $endDate . ' 23:59:59')
                ->countAllResults();

            // New members in that month
            $new = $db->table('sp_members')
                ->whereIn('region_code', $regionCodes)
                ->where('DATE_FORMAT(created_at, "%Y-%m")', $date)
                ->countAllResults();

            $months[] = date('M Y', strtotime($date . '-01'));
            $totalMembers[] = $total;
            $newMembers[] = $new;
        }

        return [
            'months' => $months,
            'total' => $totalMembers,
            'new' => $newMembers,
        ];
    }

    /**
     * Get payment trend chart (last 6 months)
     */
    private function getPaymentTrendChart(array $regionCodes): array
    {
        $db = \Config\Database::connect();
        $months = [];
        $amounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            list($year, $month) = explode('-', $date);

            $amount = $db->table('sp_dues_payments')
                ->selectSum('amount')
                ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->whereIn('sp_members.region_code', $regionCodes)
                ->where('sp_dues_payments.status', 'verified')
                ->where('sp_dues_payments.payment_year', $year)
                ->where('sp_dues_payments.payment_month', (int)$month)
                ->get()
                ->getRow()
                ->amount ?? 0;

            $months[] = date('M Y', strtotime($date . '-01'));
            $amounts[] = $amount;
        }

        return [
            'months' => $months,
            'amounts' => $amounts,
        ];
    }
}
