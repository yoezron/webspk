<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\RegionCodeModel;

class AnalyticsController extends BaseController
{
    protected $memberModel;
    protected $regionModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->regionModel = new RegionCodeModel();
        helper(['form', 'url', 'rbac']);
    }

    /**
     * Enhanced Analytics Dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'Analytics Dashboard',
            'kpis' => $this->getKPIs(),
            'trends' => $this->getTrends(),
            'comparisons' => $this->getComparisons(),
            'retention' => $this->getRetentionMetrics(),
            'predictions' => $this->getPredictions(),
            'performance' => $this->getPerformanceMetrics(),
        ];

        return view('admin/analytics/index', $data);
    }

    /**
     * Get Key Performance Indicators
     */
    private function getKPIs(): array
    {
        $db = \Config\Database::connect();

        $currentMonth = date('Y-m');
        $lastMonth = date('Y-m', strtotime('-1 month'));
        $currentYear = date('Y');
        $lastYear = date('Y', strtotime('-1 year'));

        // Member KPIs
        $totalMembers = $db->table('sp_members')
            ->where('account_status !=', 'deleted')
            ->countAllResults();

        $activeMembers = $db->table('sp_members')
            ->where('membership_status', 'active')
            ->countAllResults();

        $newThisMonth = $db->table('sp_members')
            ->where('DATE_FORMAT(created_at, "%Y-%m")', $currentMonth)
            ->countAllResults();

        $newLastMonth = $db->table('sp_members')
            ->where('DATE_FORMAT(created_at, "%Y-%m")', $lastMonth)
            ->countAllResults();

        $memberGrowthRate = $newLastMonth > 0
            ? (($newThisMonth - $newLastMonth) / $newLastMonth) * 100
            : 0;

        // Financial KPIs
        $revenueThisMonth = $db->table('sp_dues_payments')
            ->selectSum('amount')
            ->where('status', 'verified')
            ->where('payment_year', date('Y'))
            ->where('payment_month', (int)date('m'))
            ->get()
            ->getRow()
            ->amount ?? 0;

        $revenueLastMonth = $db->table('sp_dues_payments')
            ->selectSum('amount')
            ->where('status', 'verified')
            ->where('payment_year', date('Y', strtotime('-1 month')))
            ->where('payment_month', (int)date('m', strtotime('-1 month')))
            ->get()
            ->getRow()
            ->amount ?? 0;

        $revenueGrowthRate = $revenueLastMonth > 0
            ? (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100
            : 0;

        $revenueYTD = $db->table('sp_dues_payments')
            ->selectSum('amount')
            ->where('status', 'verified')
            ->where('payment_year', $currentYear)
            ->get()
            ->getRow()
            ->amount ?? 0;

        // Collection Rate KPI
        $expectedCollections = $activeMembers * 50000; // Assuming 50k/month
        $collectionRate = $expectedCollections > 0
            ? ($revenueThisMonth / $expectedCollections) * 100
            : 0;

        // Average Revenue Per User (ARPU)
        $arpu = $activeMembers > 0 ? $revenueThisMonth / $activeMembers : 0;

        // Churn Rate (members who left)
        $churnedThisMonth = $db->table('sp_members')
            ->where('membership_status', 'suspended')
            ->where('DATE_FORMAT(updated_at, "%Y-%m")', $currentMonth)
            ->countAllResults();

        $churnRate = $totalMembers > 0 ? ($churnedThisMonth / $totalMembers) * 100 : 0;

        // Conversion Rate (candidate to active)
        $candidatesLastMonth = $db->table('sp_members')
            ->where('membership_status', 'candidate')
            ->where('DATE_FORMAT(created_at, "%Y-%m")', $lastMonth)
            ->countAllResults();

        $convertedThisMonth = $db->table('sp_members')
            ->where('membership_status', 'active')
            ->where('DATE_FORMAT(created_at, "%Y-%m")', $lastMonth)
            ->countAllResults();

        $conversionRate = $candidatesLastMonth > 0
            ? ($convertedThisMonth / $candidatesLastMonth) * 100
            : 0;

        return [
            'total_members' => $totalMembers,
            'active_members' => $activeMembers,
            'active_rate' => $totalMembers > 0 ? ($activeMembers / $totalMembers) * 100 : 0,
            'new_this_month' => $newThisMonth,
            'member_growth_rate' => $memberGrowthRate,
            'revenue_this_month' => $revenueThisMonth,
            'revenue_last_month' => $revenueLastMonth,
            'revenue_growth_rate' => $revenueGrowthRate,
            'revenue_ytd' => $revenueYTD,
            'collection_rate' => $collectionRate,
            'arpu' => $arpu,
            'churn_rate' => $churnRate,
            'conversion_rate' => $conversionRate,
        ];
    }

    /**
     * Get Trend Analysis
     */
    private function getTrends(): array
    {
        $db = \Config\Database::connect();

        // 12-month member trend
        $memberTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $monthLabel = date('M Y', strtotime("-$i months"));

            $memberTrend['labels'][] = $monthLabel;
            $memberTrend['total'][] = $db->table('sp_members')
                ->where("DATE_FORMAT(created_at, '%Y-%m') <=", $month)
                ->where('account_status !=', 'deleted')
                ->countAllResults();
            $memberTrend['new'][] = $db->table('sp_members')
                ->where("DATE_FORMAT(created_at, '%Y-%m')", $month)
                ->countAllResults();
            $memberTrend['active'][] = $db->table('sp_members')
                ->where("DATE_FORMAT(created_at, '%Y-%m') <=", $month)
                ->where('membership_status', 'active')
                ->countAllResults();
        }

        // 12-month revenue trend
        $revenueTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $year = date('Y', strtotime("-$i months"));
            $month = (int)date('m', strtotime("-$i months"));
            $monthLabel = date('M Y', strtotime("-$i months"));

            $revenueTrend['labels'][] = $monthLabel;
            $revenueTrend['revenue'][] = $db->table('sp_dues_payments')
                ->selectSum('amount')
                ->where('status', 'verified')
                ->where('payment_year', $year)
                ->where('payment_month', $month)
                ->get()
                ->getRow()
                ->amount ?? 0;
            $revenueTrend['transactions'][] = $db->table('sp_dues_payments')
                ->where('status', 'verified')
                ->where('payment_year', $year)
                ->where('payment_month', $month)
                ->countAllResults();
        }

        return [
            'member_trend' => $memberTrend,
            'revenue_trend' => $revenueTrend,
        ];
    }

    /**
     * Get Period Comparisons
     */
    private function getComparisons(): array
    {
        $db = \Config\Database::connect();

        $currentMonth = date('Y-m');
        $lastMonth = date('Y-m', strtotime('-1 month'));
        $currentYear = date('Y');
        $lastYear = (int)date('Y') - 1;

        // Month-over-Month
        $mom = [
            'members_current' => $db->table('sp_members')
                ->where("DATE_FORMAT(created_at, '%Y-%m')", $currentMonth)
                ->countAllResults(),
            'members_last' => $db->table('sp_members')
                ->where("DATE_FORMAT(created_at, '%Y-%m')", $lastMonth)
                ->countAllResults(),
            'revenue_current' => $db->table('sp_dues_payments')
                ->selectSum('amount')
                ->where('status', 'verified')
                ->where('payment_year', date('Y'))
                ->where('payment_month', (int)date('m'))
                ->get()
                ->getRow()
                ->amount ?? 0,
            'revenue_last' => $db->table('sp_dues_payments')
                ->selectSum('amount')
                ->where('status', 'verified')
                ->where('payment_year', date('Y', strtotime('-1 month')))
                ->where('payment_month', (int)date('m', strtotime('-1 month')))
                ->get()
                ->getRow()
                ->amount ?? 0,
        ];

        $mom['members_change'] = $mom['members_last'] > 0
            ? (($mom['members_current'] - $mom['members_last']) / $mom['members_last']) * 100
            : 0;
        $mom['revenue_change'] = $mom['revenue_last'] > 0
            ? (($mom['revenue_current'] - $mom['revenue_last']) / $mom['revenue_last']) * 100
            : 0;

        // Year-over-Year
        $yoy = [
            'members_current' => $db->table('sp_members')
                ->where('YEAR(created_at)', $currentYear)
                ->countAllResults(),
            'members_last' => $db->table('sp_members')
                ->where('YEAR(created_at)', $lastYear)
                ->countAllResults(),
            'revenue_current' => $db->table('sp_dues_payments')
                ->selectSum('amount')
                ->where('status', 'verified')
                ->where('payment_year', $currentYear)
                ->get()
                ->getRow()
                ->amount ?? 0,
            'revenue_last' => $db->table('sp_dues_payments')
                ->selectSum('amount')
                ->where('status', 'verified')
                ->where('payment_year', $lastYear)
                ->get()
                ->getRow()
                ->amount ?? 0,
        ];

        $yoy['members_change'] = $yoy['members_last'] > 0
            ? (($yoy['members_current'] - $yoy['members_last']) / $yoy['members_last']) * 100
            : 0;
        $yoy['revenue_change'] = $yoy['revenue_last'] > 0
            ? (($yoy['revenue_current'] - $yoy['revenue_last']) / $yoy['revenue_last']) * 100
            : 0;

        return [
            'mom' => $mom,
            'yoy' => $yoy,
        ];
    }

    /**
     * Get Retention Metrics
     */
    private function getRetentionMetrics(): array
    {
        $db = \Config\Database::connect();

        // Cohort Analysis - simplified
        $cohorts = [];
        for ($i = 5; $i >= 0; $i--) {
            $cohortMonth = date('Y-m', strtotime("-$i months"));
            $cohortLabel = date('M Y', strtotime("-$i months"));

            $joinedInMonth = $db->table('sp_members')
                ->where("DATE_FORMAT(created_at, '%Y-%m')", $cohortMonth)
                ->countAllResults();

            $stillActive = $db->table('sp_members')
                ->where("DATE_FORMAT(created_at, '%Y-%m')", $cohortMonth)
                ->where('membership_status', 'active')
                ->countAllResults();

            $retentionRate = $joinedInMonth > 0 ? ($stillActive / $joinedInMonth) * 100 : 0;

            $cohorts[] = [
                'month' => $cohortLabel,
                'joined' => $joinedInMonth,
                'active' => $stillActive,
                'retention_rate' => $retentionRate,
            ];
        }

        // Lifetime Value (LTV) estimation
        $avgMemberLifetime = 24; // months (assumption)
        $avgMonthlyRevenue = 50000; // assumption
        $ltv = $avgMemberLifetime * $avgMonthlyRevenue;

        // Payment Consistency
        $consistentPayers = $db->table('sp_members m')
            ->select('m.id')
            ->join('sp_dues_payments p', 'p.member_id = m.id')
            ->where('m.membership_status', 'active')
            ->where('p.status', 'verified')
            ->where('p.payment_year', date('Y'))
            ->groupBy('m.id')
            ->having('COUNT(DISTINCT p.payment_month) >=', (int)date('m') - 1)
            ->countAllResults();

        $activeMembers = $db->table('sp_members')
            ->where('membership_status', 'active')
            ->countAllResults();

        $paymentConsistency = $activeMembers > 0 ? ($consistentPayers / $activeMembers) * 100 : 0;

        return [
            'cohorts' => $cohorts,
            'ltv' => $ltv,
            'payment_consistency' => $paymentConsistency,
            'avg_member_lifetime' => $avgMemberLifetime,
        ];
    }

    /**
     * Get Predictions (Simple Linear Regression)
     */
    private function getPredictions(): array
    {
        $db = \Config\Database::connect();

        // Get last 6 months data for prediction
        $historicalData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $historicalData[] = $db->table('sp_members')
                ->where("DATE_FORMAT(created_at, '%Y-%m')", $month)
                ->countAllResults();
        }

        // Simple linear trend calculation
        $n = count($historicalData);
        $sumX = 0;
        $sumY = array_sum($historicalData);
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumX += $i;
            $sumXY += $i * $historicalData[$i];
            $sumX2 += $i * $i;
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;

        // Predict next 3 months
        $predictions = [];
        for ($i = 1; $i <= 3; $i++) {
            $futureMonth = date('M Y', strtotime("+$i months"));
            $predictedValue = max(0, round($slope * ($n + $i - 1) + $intercept));
            $predictions[] = [
                'month' => $futureMonth,
                'predicted_new_members' => $predictedValue,
            ];
        }

        // Revenue prediction based on member growth
        $avgRevenuePerMember = 50000;
        foreach ($predictions as &$prediction) {
            $prediction['predicted_revenue'] = $prediction['predicted_new_members'] * $avgRevenuePerMember;
        }

        return [
            'member_predictions' => $predictions,
            'growth_trend' => $slope > 0 ? 'increasing' : ($slope < 0 ? 'decreasing' : 'stable'),
            'confidence' => 'moderate', // Simplified
        ];
    }

    /**
     * Get Performance Metrics by Region
     */
    private function getPerformanceMetrics(): array
    {
        $db = \Config\Database::connect();

        // Top performing regions
        $topRegions = $db->table('sp_members m')
            ->select('m.region_code, r.province_name, COUNT(*) as member_count, SUM(p.amount) as total_revenue')
            ->join('sp_region_codes r', 'r.region_code = m.region_code', 'left')
            ->join('sp_dues_payments p', 'p.member_id = m.id AND p.status = "verified"', 'left')
            ->where('m.account_status !=', 'deleted')
            ->groupBy('m.region_code')
            ->orderBy('total_revenue', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // Underperforming regions (low collection rate)
        $allRegions = $this->regionModel->getActiveRegions();
        $underperforming = [];

        foreach ($allRegions as $region) {
            $members = $db->table('sp_members')
                ->where('region_code', $region['region_code'])
                ->where('membership_status', 'active')
                ->countAllResults();

            if ($members > 0) {
                $collected = $db->table('sp_dues_payments p')
                    ->join('sp_members m', 'm.id = p.member_id')
                    ->where('m.region_code', $region['region_code'])
                    ->where('p.payment_year', date('Y'))
                    ->where('p.payment_month', (int)date('m'))
                    ->where('p.status', 'verified')
                    ->countAllResults();

                $collectionRate = ($collected / $members) * 100;

                if ($collectionRate < 50) { // Less than 50% collection
                    $underperforming[] = [
                        'region_code' => $region['region_code'],
                        'province_name' => $region['province_name'],
                        'members' => $members,
                        'collected' => $collected,
                        'collection_rate' => $collectionRate,
                    ];
                }
            }
        }

        // Sort by collection rate (lowest first)
        usort($underperforming, fn($a, $b) => $a['collection_rate'] <=> $b['collection_rate']);
        $underperforming = array_slice($underperforming, 0, 5);

        return [
            'top_regions' => $topRegions,
            'underperforming_regions' => $underperforming,
        ];
    }

    /**
     * API endpoint for real-time KPIs
     */
    public function kpiApi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $kpis = $this->getKPIs();

        return $this->response->setJSON([
            'success' => true,
            'data' => $kpis,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }
}
