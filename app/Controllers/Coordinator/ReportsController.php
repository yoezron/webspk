<?php

namespace App\Controllers\Coordinator;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\CoordinatorRegionModel;
use App\Models\RegionCodeModel;

class ReportsController extends BaseController
{
    protected $memberModel;
    protected $coordinatorRegionModel;
    protected $regionModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->coordinatorRegionModel = new CoordinatorRegionModel();
        $this->regionModel = new RegionCodeModel();
        helper(['form', 'url', 'rbac']);
    }

    /**
     * Regional reports dashboard
     */
    public function index()
    {
        $userId = session()->get('user_id');

        // Get coordinator's assigned regions
        $assignedRegions = $this->coordinatorRegionModel->getCoordinatorRegions($userId);
        $regionCodes = array_column($assignedRegions, 'region_code');

        if (empty($regionCodes)) {
            return view('coordinator/reports/index', [
                'title' => 'Laporan Regional',
                'assigned_regions' => [],
                'message' => 'Anda belum memiliki wilayah yang ter-assign.',
            ]);
        }

        // Date range filters
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $selectedRegion = $this->request->getGet('region');

        // Filter by selected region or all regions
        $filterRegions = $selectedRegion && in_array($selectedRegion, $regionCodes)
            ? [$selectedRegion]
            : $regionCodes;

        // Get statistics
        $stats = $this->getRegionalStatistics($filterRegions, $startDate, $endDate);

        // Get member growth data
        $memberGrowth = $this->getMemberGrowthData($filterRegions, 6);

        // Get payment trends
        $paymentTrends = $this->getPaymentTrends($filterRegions, 6);

        // Get regional breakdown
        $regionalBreakdown = $this->getRegionalBreakdown($regionCodes);

        $data = [
            'title' => 'Laporan Regional',
            'assigned_regions' => $assignedRegions,
            'stats' => $stats,
            'member_growth' => $memberGrowth,
            'payment_trends' => $paymentTrends,
            'regional_breakdown' => $regionalBreakdown,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'region' => $selectedRegion,
            ],
        ];

        return view('coordinator/reports/index', $data);
    }

    /**
     * Get regional statistics
     */
    private function getRegionalStatistics(array $regionCodes, string $startDate, string $endDate): array
    {
        $db = \Config\Database::connect();

        $stats = [
            'total_members' => $db->table('sp_members')
                ->whereIn('region_code', $regionCodes)
                ->where('account_status !=', 'deleted')
                ->countAllResults(),

            'active_members' => $db->table('sp_members')
                ->whereIn('region_code', $regionCodes)
                ->where('membership_status', 'active')
                ->countAllResults(),

            'new_members' => $db->table('sp_members')
                ->whereIn('region_code', $regionCodes)
                ->where('created_at >=', $startDate . ' 00:00:00')
                ->where('created_at <=', $endDate . ' 23:59:59')
                ->countAllResults(),

            'candidates' => $db->table('sp_members')
                ->whereIn('region_code', $regionCodes)
                ->where('membership_status', 'candidate')
                ->countAllResults(),

            'total_collected' => $db->table('sp_dues_payments')
                ->selectSum('amount')
                ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->whereIn('sp_members.region_code', $regionCodes)
                ->where('sp_dues_payments.status', 'verified')
                ->where('sp_dues_payments.payment_date >=', $startDate)
                ->where('sp_dues_payments.payment_date <=', $endDate)
                ->get()
                ->getRow()
                ->amount ?? 0,

            'pending_payments' => $db->table('sp_dues_payments')
                ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->whereIn('sp_members.region_code', $regionCodes)
                ->where('sp_dues_payments.status', 'pending')
                ->countAllResults(),

            'verified_payments' => $db->table('sp_dues_payments')
                ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->whereIn('sp_members.region_code', $regionCodes)
                ->where('sp_dues_payments.status', 'verified')
                ->where('sp_dues_payments.payment_date >=', $startDate)
                ->where('sp_dues_payments.payment_date <=', $endDate)
                ->countAllResults(),

            'pending_approvals' => $db->table('sp_members')
                ->whereIn('region_code', $regionCodes)
                ->where('membership_status', 'candidate')
                ->where('registration_status', 'completed')
                ->countAllResults(),
        ];

        return $stats;
    }

    /**
     * Get member growth data for charts
     */
    private function getMemberGrowthData(array $regionCodes, int $months): array
    {
        $db = \Config\Database::connect();
        $data = ['months' => [], 'total' => [], 'new' => []];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = date('Y-m-01', strtotime("-$i months"));
            $monthName = date('M Y', strtotime($date));

            $data['months'][] = $monthName;

            // Total members up to this month
            $total = $db->table('sp_members')
                ->whereIn('region_code', $regionCodes)
                ->where('created_at <=', date('Y-m-t', strtotime($date)) . ' 23:59:59')
                ->where('account_status !=', 'deleted')
                ->countAllResults();

            $data['total'][] = $total;

            // New members in this month
            $new = $db->table('sp_members')
                ->whereIn('region_code', $regionCodes)
                ->where('created_at >=', $date . ' 00:00:00')
                ->where('created_at <=', date('Y-m-t', strtotime($date)) . ' 23:59:59')
                ->countAllResults();

            $data['new'][] = $new;
        }

        return $data;
    }

    /**
     * Get payment trends for charts
     */
    private function getPaymentTrends(array $regionCodes, int $months): array
    {
        $db = \Config\Database::connect();
        $data = ['months' => [], 'amount' => [], 'count' => []];

        for ($i = $months - 1; $i >= 0; $i--) {
            $year = date('Y', strtotime("-$i months"));
            $month = (int)date('m', strtotime("-$i months"));
            $monthName = date('M Y', strtotime("-$i months"));

            $data['months'][] = $monthName;

            // Payment amount
            $amount = $db->table('sp_dues_payments')
                ->selectSum('amount')
                ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->whereIn('sp_members.region_code', $regionCodes)
                ->where('sp_dues_payments.payment_year', $year)
                ->where('sp_dues_payments.payment_month', $month)
                ->where('sp_dues_payments.status', 'verified')
                ->get()
                ->getRow()
                ->amount ?? 0;

            $data['amount'][] = (float)$amount;

            // Payment count
            $count = $db->table('sp_dues_payments')
                ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->whereIn('sp_members.region_code', $regionCodes)
                ->where('sp_dues_payments.payment_year', $year)
                ->where('sp_dues_payments.payment_month', $month)
                ->where('sp_dues_payments.status', 'verified')
                ->countAllResults();

            $data['count'][] = $count;
        }

        return $data;
    }

    /**
     * Get breakdown by region
     */
    private function getRegionalBreakdown(array $regionCodes): array
    {
        $db = \Config\Database::connect();
        $breakdown = [];

        foreach ($regionCodes as $code) {
            $region = $this->regionModel->getByCode($code);

            if ($region) {
                $breakdown[] = [
                    'region_code' => $code,
                    'province_name' => $region['province_name'],
                    'members' => $db->table('sp_members')
                        ->where('region_code', $code)
                        ->where('membership_status', 'active')
                        ->countAllResults(),
                    'candidates' => $db->table('sp_members')
                        ->where('region_code', $code)
                        ->where('membership_status', 'candidate')
                        ->countAllResults(),
                    'total_collected' => $db->table('sp_dues_payments')
                        ->selectSum('amount')
                        ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                        ->where('sp_members.region_code', $code)
                        ->where('sp_dues_payments.status', 'verified')
                        ->get()
                        ->getRow()
                        ->amount ?? 0,
                ];
            }
        }

        return $breakdown;
    }

    /**
     * Export report to CSV
     */
    public function export()
    {
        $userId = session()->get('user_id');

        // Get coordinator's assigned regions
        $assignedRegions = $this->coordinatorRegionModel->getCoordinatorRegions($userId);
        $regionCodes = array_column($assignedRegions, 'region_code');

        if (empty($regionCodes)) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diekspor');
        }

        // Get filters
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $selectedRegion = $this->request->getGet('region');

        $filterRegions = $selectedRegion && in_array($selectedRegion, $regionCodes)
            ? [$selectedRegion]
            : $regionCodes;

        // Get data
        $breakdown = $this->getRegionalBreakdown($filterRegions);

        // Generate CSV
        $filename = 'laporan_regional_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header
        fputcsv($output, ['Laporan Regional - Periode: ' . $startDate . ' s/d ' . $endDate]);
        fputcsv($output, []);
        fputcsv($output, ['Kode Wilayah', 'Nama Provinsi', 'Anggota Aktif', 'Kandidat', 'Total Terkumpul']);

        // Data
        foreach ($breakdown as $row) {
            fputcsv($output, [
                $row['region_code'],
                $row['province_name'],
                $row['members'],
                $row['candidates'],
                'Rp ' . number_format($row['total_collected'], 0, ',', '.'),
            ]);
        }

        fclose($output);
        exit;
    }
}
