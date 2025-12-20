<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\RegionCodeModel;
use App\Models\DuesRateModel;

class ReportsController extends BaseController
{
    protected $memberModel;
    protected $regionModel;
    protected $duesRateModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->regionModel = new RegionCodeModel();
        $this->duesRateModel = new DuesRateModel();
        helper(['form', 'url', 'rbac']);
    }

    /**
     * Reports dashboard
     */
    public function index()
    {
        // Get filter parameters
        $reportType = $this->request->getGet('type') ?? 'overview';
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $regionFilter = $this->request->getGet('region');

        // Get report data based on type
        $reportData = match($reportType) {
            'members' => $this->getMemberReport($startDate, $endDate, $regionFilter),
            'financial' => $this->getFinancialReport($startDate, $endDate, $regionFilter),
            'regional' => $this->getRegionalReport($startDate, $endDate),
            'payments' => $this->getPaymentReport($startDate, $endDate, $regionFilter),
            default => $this->getOverviewReport($startDate, $endDate),
        };

        $data = [
            'title' => 'Laporan Komprehensif',
            'report_type' => $reportType,
            'report_data' => $reportData,
            'regions' => $this->regionModel->getActiveRegions(),
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'region' => $regionFilter,
            ],
        ];

        return view('admin/reports/index', $data);
    }

    /**
     * Overview report
     */
    private function getOverviewReport(string $startDate, string $endDate): array
    {
        $db = \Config\Database::connect();

        return [
            'total_members' => $db->table('sp_members')
                ->where('account_status !=', 'deleted')
                ->countAllResults(),

            'new_members' => $db->table('sp_members')
                ->where('created_at >=', $startDate . ' 00:00:00')
                ->where('created_at <=', $endDate . ' 23:59:59')
                ->countAllResults(),

            'active_members' => $db->table('sp_members')
                ->where('membership_status', 'active')
                ->countAllResults(),

            'candidates' => $db->table('sp_members')
                ->where('membership_status', 'candidate')
                ->countAllResults(),

            'total_collected' => $db->table('sp_dues_payments')
                ->selectSum('amount')
                ->where('status', 'verified')
                ->where('payment_date >=', $startDate)
                ->where('payment_date <=', $endDate)
                ->get()
                ->getRow()
                ->amount ?? 0,

            'pending_payments' => $db->table('sp_dues_payments')
                ->where('status', 'pending')
                ->countAllResults(),

            'verified_payments' => $db->table('sp_dues_payments')
                ->where('status', 'verified')
                ->where('payment_date >=', $startDate)
                ->where('payment_date <=', $endDate)
                ->countAllResults(),

            'total_regions' => $db->table('sp_region_codes')
                ->where('is_active', 1)
                ->countAllResults(),

            // Charts data
            'member_growth' => $this->getMemberGrowthData(12),
            'status_breakdown' => $this->getStatusBreakdown(),
            'regional_distribution' => $this->getRegionalDistribution(),
        ];
    }

    /**
     * Member report
     */
    private function getMemberReport(string $startDate, string $endDate, ?string $regionFilter): array
    {
        $db = \Config\Database::connect();

        $builder = $db->table('sp_members');

        if ($regionFilter) {
            $builder->where('region_code', $regionFilter);
        }

        $data = [
            'total_members' => (clone $builder)->where('account_status !=', 'deleted')->countAllResults(),
            'new_members' => (clone $builder)
                ->where('created_at >=', $startDate . ' 00:00:00')
                ->where('created_at <=', $endDate . ' 23:59:59')
                ->countAllResults(),
            'active_members' => (clone $builder)->where('membership_status', 'active')->countAllResults(),
            'candidates' => (clone $builder)->where('membership_status', 'candidate')->countAllResults(),
            'suspended' => (clone $builder)->where('membership_status', 'suspended')->countAllResults(),

            // Demographics
            'gender_breakdown' => $this->getGenderBreakdown($regionFilter),
            'age_distribution' => $this->getAgeDistribution($regionFilter),
            'department_breakdown' => $this->getDepartmentBreakdown($regionFilter),

            // Trends
            'registration_trend' => $this->getRegistrationTrend($startDate, $endDate, $regionFilter),
            'status_history' => $this->getStatusHistory(6, $regionFilter),
        ];

        return $data;
    }

    /**
     * Financial report
     */
    private function getFinancialReport(string $startDate, string $endDate, ?string $regionFilter): array
    {
        $db = \Config\Database::connect();

        $builder = $db->table('sp_dues_payments')
            ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id');

        if ($regionFilter) {
            $builder->where('sp_members.region_code', $regionFilter);
        }

        $data = [
            'total_collected' => (clone $builder)
                ->selectSum('sp_dues_payments.amount')
                ->where('sp_dues_payments.status', 'verified')
                ->where('sp_dues_payments.payment_date >=', $startDate)
                ->where('sp_dues_payments.payment_date <=', $endDate)
                ->get()
                ->getRow()
                ->amount ?? 0,

            'pending_amount' => (clone $builder)
                ->selectSum('sp_dues_payments.amount')
                ->where('sp_dues_payments.status', 'pending')
                ->get()
                ->getRow()
                ->amount ?? 0,

            'total_transactions' => (clone $builder)
                ->where('sp_dues_payments.payment_date >=', $startDate)
                ->where('sp_dues_payments.payment_date <=', $endDate)
                ->countAllResults(),

            'verified_transactions' => (clone $builder)
                ->where('sp_dues_payments.status', 'verified')
                ->where('sp_dues_payments.payment_date >=', $startDate)
                ->where('sp_dues_payments.payment_date <=', $endDate)
                ->countAllResults(),

            // Analysis
            'payment_method_breakdown' => $this->getPaymentMethodBreakdown($startDate, $endDate, $regionFilter),
            'monthly_revenue' => $this->getMonthlyRevenue($startDate, $endDate, $regionFilter),
            'collection_rate' => $this->getCollectionRate($regionFilter),
            'outstanding_dues' => $this->getOutstandingDues($regionFilter),
        ];

        return $data;
    }

    /**
     * Regional report
     */
    private function getRegionalReport(string $startDate, string $endDate): array
    {
        $regions = $this->regionModel->getActiveRegions();
        $db = \Config\Database::connect();

        $regionalData = [];

        foreach ($regions as $region) {
            $code = $region['region_code'];

            $regionalData[] = [
                'region_code' => $code,
                'province_name' => $region['province_name'],
                'total_members' => $db->table('sp_members')
                    ->where('region_code', $code)
                    ->where('account_status !=', 'deleted')
                    ->countAllResults(),
                'active_members' => $db->table('sp_members')
                    ->where('region_code', $code)
                    ->where('membership_status', 'active')
                    ->countAllResults(),
                'new_members' => $db->table('sp_members')
                    ->where('region_code', $code)
                    ->where('created_at >=', $startDate . ' 00:00:00')
                    ->where('created_at <=', $endDate . ' 23:59:59')
                    ->countAllResults(),
                'total_collected' => $db->table('sp_dues_payments')
                    ->selectSum('amount')
                    ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                    ->where('sp_members.region_code', $code)
                    ->where('sp_dues_payments.status', 'verified')
                    ->where('sp_dues_payments.payment_date >=', $startDate)
                    ->where('sp_dues_payments.payment_date <=', $endDate)
                    ->get()
                    ->getRow()
                    ->amount ?? 0,
            ];
        }

        return [
            'regions' => $regionalData,
            'summary' => [
                'total_regions' => count($regionalData),
                'regions_with_members' => count(array_filter($regionalData, fn($r) => $r['total_members'] > 0)),
                'total_members' => array_sum(array_column($regionalData, 'total_members')),
                'total_collected' => array_sum(array_column($regionalData, 'total_collected')),
            ],
        ];
    }

    /**
     * Payment report
     */
    private function getPaymentReport(string $startDate, string $endDate, ?string $regionFilter): array
    {
        $db = \Config\Database::connect();

        $builder = $db->table('sp_dues_payments')
            ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id');

        if ($regionFilter) {
            $builder->where('sp_members.region_code', $regionFilter);
        }

        return [
            'total_payments' => (clone $builder)
                ->where('sp_dues_payments.payment_date >=', $startDate)
                ->where('sp_dues_payments.payment_date <=', $endDate)
                ->countAllResults(),

            'verified_count' => (clone $builder)
                ->where('sp_dues_payments.status', 'verified')
                ->where('sp_dues_payments.payment_date >=', $startDate)
                ->where('sp_dues_payments.payment_date <=', $endDate)
                ->countAllResults(),

            'pending_count' => (clone $builder)
                ->where('sp_dues_payments.status', 'pending')
                ->countAllResults(),

            'rejected_count' => (clone $builder)
                ->where('sp_dues_payments.status', 'rejected')
                ->where('sp_dues_payments.payment_date >=', $startDate)
                ->where('sp_dues_payments.payment_date <=', $endDate)
                ->countAllResults(),

            'payment_trends' => $this->getPaymentTrends($startDate, $endDate, $regionFilter),
            'verification_rate' => $this->getVerificationRate($startDate, $endDate, $regionFilter),
        ];
    }

    // Helper methods for data generation

    private function getMemberGrowthData(int $months): array
    {
        $db = \Config\Database::connect();
        $data = ['months' => [], 'total' => [], 'new' => []];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = date('Y-m-01', strtotime("-$i months"));
            $monthName = date('M Y', strtotime($date));

            $data['months'][] = $monthName;
            $data['total'][] = $db->table('sp_members')
                ->where('created_at <=', date('Y-m-t', strtotime($date)) . ' 23:59:59')
                ->where('account_status !=', 'deleted')
                ->countAllResults();
            $data['new'][] = $db->table('sp_members')
                ->where('created_at >=', $date . ' 00:00:00')
                ->where('created_at <=', date('Y-m-t', strtotime($date)) . ' 23:59:59')
                ->countAllResults();
        }

        return $data;
    }

    private function getStatusBreakdown(): array
    {
        $db = \Config\Database::connect();

        return [
            'active' => $db->table('sp_members')->where('membership_status', 'active')->countAllResults(),
            'candidate' => $db->table('sp_members')->where('membership_status', 'candidate')->countAllResults(),
            'suspended' => $db->table('sp_members')->where('membership_status', 'suspended')->countAllResults(),
            'inactive' => $db->table('sp_members')->where('account_status', 'inactive')->countAllResults(),
        ];
    }

    private function getRegionalDistribution(): array
    {
        return $this->db->table('sp_members')
            ->select('sp_region_codes.province_name, COUNT(*) as total')
            ->join('sp_region_codes', 'sp_region_codes.region_code = sp_members.region_code', 'left')
            ->where('sp_members.account_status !=', 'deleted')
            ->groupBy('sp_members.region_code')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
    }

    private function getGenderBreakdown(?string $regionFilter): array
    {
        $builder = $this->db->table('sp_members')
            ->select('gender, COUNT(*) as total')
            ->where('account_status !=', 'deleted');

        if ($regionFilter) {
            $builder->where('region_code', $regionFilter);
        }

        return $builder->groupBy('gender')->get()->getResultArray();
    }

    private function getAgeDistribution(?string $regionFilter): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('sp_members')
            ->where('account_status !=', 'deleted')
            ->where('date_of_birth IS NOT NULL');

        if ($regionFilter) {
            $builder->where('region_code', $regionFilter);
        }

        $members = $builder->get()->getResultArray();
        $distribution = ['<25' => 0, '25-35' => 0, '36-45' => 0, '46-55' => 0, '>55' => 0];

        foreach ($members as $member) {
            $age = date_diff(date_create($member['date_of_birth']), date_create('now'))->y;
            if ($age < 25) $distribution['<25']++;
            elseif ($age <= 35) $distribution['25-35']++;
            elseif ($age <= 45) $distribution['36-45']++;
            elseif ($age <= 55) $distribution['46-55']++;
            else $distribution['>55']++;
        }

        return $distribution;
    }

    private function getDepartmentBreakdown(?string $regionFilter): array
    {
        $builder = $this->db->table('sp_members')
            ->select('department, COUNT(*) as total')
            ->where('account_status !=', 'deleted')
            ->where('department IS NOT NULL');

        if ($regionFilter) {
            $builder->where('region_code', $regionFilter);
        }

        return $builder->groupBy('department')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
    }

    private function getRegistrationTrend(string $startDate, string $endDate, ?string $regionFilter): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('sp_members')
            ->select("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate);

        if ($regionFilter) {
            $builder->where('region_code', $regionFilter);
        }

        return $builder->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function getStatusHistory(int $months, ?string $regionFilter): array
    {
        // Placeholder - would need status change tracking
        return [];
    }

    private function getPaymentMethodBreakdown(string $startDate, string $endDate, ?string $regionFilter): array
    {
        $builder = $this->db->table('sp_dues_payments')
            ->select('payment_method, COUNT(*) as count, SUM(amount) as total')
            ->where('payment_date >=', $startDate)
            ->where('payment_date <=', $endDate)
            ->where('status', 'verified');

        if ($regionFilter) {
            $builder->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->where('sp_members.region_code', $regionFilter);
        }

        return $builder->groupBy('payment_method')->get()->getResultArray();
    }

    private function getMonthlyRevenue(string $startDate, string $endDate, ?string $regionFilter): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('sp_dues_payments')
            ->select("CONCAT(payment_year, '-', LPAD(payment_month, 2, '0')) as month, SUM(amount) as revenue")
            ->where('status', 'verified');

        if ($regionFilter) {
            $builder->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->where('sp_members.region_code', $regionFilter);
        }

        return $builder->groupBy('payment_year, payment_month')
            ->orderBy('payment_year', 'ASC')
            ->orderBy('payment_month', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function getCollectionRate(?string $regionFilter): float
    {
        $db = \Config\Database::connect();

        $totalMembers = $db->table('sp_members')
            ->where('membership_status', 'active');

        if ($regionFilter) {
            $totalMembers->where('region_code', $regionFilter);
        }

        $total = $totalMembers->countAllResults();

        if ($total == 0) return 0;

        // Members who paid this month
        $builder = $db->table('sp_dues_payments')
            ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
            ->where('sp_members.membership_status', 'active')
            ->where('sp_dues_payments.payment_year', date('Y'))
            ->where('sp_dues_payments.payment_month', (int)date('m'))
            ->where('sp_dues_payments.status', 'verified');

        if ($regionFilter) {
            $builder->where('sp_members.region_code', $regionFilter);
        }

        $paid = $builder->countAllResults();

        return ($paid / $total) * 100;
    }

    private function getOutstandingDues(?string $regionFilter): array
    {
        // Members who haven't paid this month
        $db = \Config\Database::connect();

        $activeMembers = $db->table('sp_members')
            ->select('id')
            ->where('membership_status', 'active');

        if ($regionFilter) {
            $activeMembers->where('region_code', $regionFilter);
        }

        $activeMemberIds = array_column($activeMembers->get()->getResultArray(), 'id');

        if (empty($activeMemberIds)) {
            return ['count' => 0, 'estimated_amount' => 0];
        }

        $paidMemberIds = $db->table('sp_dues_payments')
            ->select('member_id')
            ->whereIn('member_id', $activeMemberIds)
            ->where('payment_year', date('Y'))
            ->where('payment_month', (int)date('m'))
            ->where('status', 'verified')
            ->get()
            ->getResultArray();

        $paidIds = array_column($paidMemberIds, 'member_id');
        $outstandingCount = count($activeMemberIds) - count($paidIds);

        // Get standard monthly rate
        $standardRate = $this->duesRateModel->getApplicableRate(null, null, 'monthly');
        $estimatedAmount = $outstandingCount * ($standardRate['amount'] ?? 50000);

        return [
            'count' => $outstandingCount,
            'estimated_amount' => $estimatedAmount,
        ];
    }

    private function getPaymentTrends(string $startDate, string $endDate, ?string $regionFilter): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('sp_dues_payments')
            ->select("DATE_FORMAT(payment_date, '%Y-%m') as month, COUNT(*) as count, SUM(amount) as total")
            ->where('payment_date >=', $startDate)
            ->where('payment_date <=', $endDate)
            ->where('status', 'verified');

        if ($regionFilter) {
            $builder->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->where('sp_members.region_code', $regionFilter);
        }

        return $builder->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function getVerificationRate(string $startDate, string $endDate, ?string $regionFilter): float
    {
        $db = \Config\Database::connect();
        $builder = $db->table('sp_dues_payments')
            ->where('payment_date >=', $startDate)
            ->where('payment_date <=', $endDate);

        if ($regionFilter) {
            $builder->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
                ->where('sp_members.region_code', $regionFilter);
        }

        $total = (clone $builder)->countAllResults();

        if ($total == 0) return 0;

        $verified = (clone $builder)->where('sp_dues_payments.status', 'verified')->countAllResults();

        return ($verified / $total) * 100;
    }

    /**
     * Export report to CSV
     */
    public function export()
    {
        $reportType = $this->request->getGet('type') ?? 'overview';
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $regionFilter = $this->request->getGet('region');

        $reportData = match($reportType) {
            'members' => $this->getMemberReport($startDate, $endDate, $regionFilter),
            'financial' => $this->getFinancialReport($startDate, $endDate, $regionFilter),
            'regional' => $this->getRegionalReport($startDate, $endDate),
            default => $this->getOverviewReport($startDate, $endDate),
        };

        $filename = 'laporan_' . $reportType . '_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

        // Export based on report type
        $this->exportCSV($output, $reportType, $reportData, $startDate, $endDate);

        fclose($output);
        exit;
    }

    private function exportCSV($output, string $type, array $data, string $startDate, string $endDate): void
    {
        fputcsv($output, ['Laporan ' . ucfirst($type) . ' - Periode: ' . $startDate . ' s/d ' . $endDate]);
        fputcsv($output, []);

        switch ($type) {
            case 'regional':
                fputcsv($output, ['Kode', 'Provinsi', 'Total Anggota', 'Anggota Aktif', 'Anggota Baru', 'Total Terkumpul']);
                foreach ($data['regions'] as $row) {
                    fputcsv($output, [
                        $row['region_code'],
                        $row['province_name'],
                        $row['total_members'],
                        $row['active_members'],
                        $row['new_members'],
                        'Rp ' . number_format($row['total_collected'], 0, ',', '.'),
                    ]);
                }
                break;

            default:
                fputcsv($output, ['Metrik', 'Nilai']);
                foreach ($data as $key => $value) {
                    if (!is_array($value)) {
                        fputcsv($output, [$key, is_numeric($value) ? number_format($value, 0, ',', '.') : $value]);
                    }
                }
                break;
        }
    }
}
