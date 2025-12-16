<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\DuesPaymentModel;

class Dashboard extends BaseController
{
    protected $memberModel;
    protected $paymentModel;
    protected $cache;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->paymentModel = new DuesPaymentModel();
        $this->cache = \Config\Services::cache();
        helper(['form', 'url', 'app']);
    }

    /**
     * Admin dashboard with enhanced statistics and charts
     * OPTIMIZED: Reduced from 59+ queries to ~10 queries per page load
     * CACHED: Stats cached for 5 min, charts for 15 min
     */
    public function index()
    {
        // Get member statistics (cached 5 minutes)
        $stats = $this->getCachedData('admin_member_stats', [$this, 'getMemberStatistics'], 300);

        // Get payment statistics (cached 5 minutes)
        $paymentStats = $this->getCachedData('admin_payment_stats', [$this, 'getPaymentStatistics'], 300);

        // Get monthly statistics (cached 5 minutes)
        $monthlyStats = $this->getCachedData('admin_monthly_stats', [$this, 'getMonthlyStatistics'], 300);

        // Get chart data (cached 15 minutes - heavier queries)
        $memberGrowthChart = $this->getCachedData('admin_member_growth_chart', [$this, 'getMemberGrowthChart'], 900);
        $paymentTrendChart = $this->getCachedData('admin_payment_trend_chart', [$this, 'getPaymentTrendChart'], 900);

        // Get pending approvals list
        $pendingApprovals = $this->memberModel->getPendingApprovals(10);

        // Get recent registrations
        $recentRegistrations = $this->memberModel
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        // Get pending payment verifications (limit 5)
        $pendingPayments = $this->paymentModel
            ->select('sp_dues_payments.*, sp_members.full_name, sp_members.member_number')
            ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
            ->where('sp_dues_payments.status', 'pending')
            ->orderBy('sp_dues_payments.created_at', 'DESC')
            ->limit(5)
            ->findAll();

        $data = [
            'title' => 'Dashboard Admin',
            'description' => 'Dashboard Admin Serikat Pekerja Kampus',
            'stats' => $stats,
            'payment_stats' => $paymentStats,
            'monthly_stats' => $monthlyStats,
            'member_growth_chart' => $memberGrowthChart,
            'payment_trend_chart' => $paymentTrendChart,
            'pending_approvals' => $pendingApprovals,
            'recent_registrations' => $recentRegistrations,
            'pending_payments' => $pendingPayments,
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * Get cached data or fetch and cache if not exists
     *
     * @param string $cacheKey Cache key
     * @param callable $callback Function to call if cache miss
     * @param int $ttl Time to live in seconds
     * @return mixed Cached or fresh data
     */
    private function getCachedData(string $cacheKey, callable $callback, int $ttl)
    {
        $data = $this->cache->get($cacheKey);

        if ($data === null) {
            // Cache miss - fetch data and cache it
            $data = call_user_func($callback);
            $this->cache->save($cacheKey, $data, $ttl);
            log_message('debug', "Cache MISS: {$cacheKey} - Data fetched and cached for {$ttl}s");
        } else {
            log_message('debug', "Cache HIT: {$cacheKey}");
        }

        return $data;
    }

    /**
     * Get member statistics
     * OPTIMIZED: 1 query instead of 4 separate queries
     */
    private function getMemberStatistics(): array
    {
        $db = \Config\Database::connect();

        // Single query with conditional aggregation
        $result = $db->query("
            SELECT
                SUM(CASE WHEN membership_status = 'active' THEN 1 ELSE 0 END) as total_members,
                SUM(CASE WHEN membership_status = 'candidate' THEN 1 ELSE 0 END) as total_candidates,
                SUM(CASE WHEN onboarding_state = 'email_verified' AND account_status = 'pending' THEN 1 ELSE 0 END) as pending_approvals,
                SUM(CASE WHEN account_status = 'suspended' THEN 1 ELSE 0 END) as total_suspended
            FROM sp_members
        ")->getRow();

        return [
            'total_members' => (int)$result->total_members,
            'total_candidates' => (int)$result->total_candidates,
            'pending_approvals' => (int)$result->pending_approvals,
            'total_suspended' => (int)$result->total_suspended,
        ];
    }

    /**
     * Get payment statistics
     * OPTIMIZED: 1 query instead of 4 separate queries
     */
    private function getPaymentStatistics(): array
    {
        $db = \Config\Database::connect();
        $currentYear = date('Y');
        $currentMonth = (int)date('m');

        // Single query with conditional aggregation
        $result = $db->query("
            SELECT
                SUM(CASE WHEN status = 'verified' THEN amount ELSE 0 END) as total_collected,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = 'verified' AND payment_year = ? AND payment_month = ? THEN amount ELSE 0 END) as this_month,
                SUM(CASE WHEN status = 'verified' AND payment_year = ? THEN amount ELSE 0 END) as this_year
            FROM sp_dues_payments
        ", [$currentYear, $currentMonth, $currentYear])->getRow();

        return [
            'total_collected' => $result->total_collected ?? 0,
            'pending_count' => (int)$result->pending_count,
            'this_month' => $result->this_month ?? 0,
            'this_year' => $result->this_year ?? 0,
        ];
    }

    /**
     * Get monthly statistics (current month)
     * OPTIMIZED: 1 query instead of 3 separate queries
     */
    private function getMonthlyStatistics(): array
    {
        $db = \Config\Database::connect();
        $currentYear = date('Y');
        $currentMonth = (int)date('m');
        $currentYearMonth = date('Y-m');

        // Single query with subqueries
        $result = $db->query("
            SELECT
                (SELECT COUNT(*) FROM sp_members WHERE DATE_FORMAT(created_at, '%Y-%m') = ?) as new_registrations,
                (SELECT COUNT(*) FROM sp_members WHERE membership_status = 'active' AND DATE_FORMAT(approval_date, '%Y-%m') = ?) as approved,
                (SELECT COUNT(*) FROM sp_dues_payments WHERE payment_year = ? AND payment_month = ?) as payments
        ", [$currentYearMonth, $currentYearMonth, $currentYear, $currentMonth])->getRow();

        return [
            'new_registrations' => (int)$result->new_registrations,
            'approved' => (int)$result->approved,
            'payments' => (int)$result->payments,
        ];
    }

    /**
     * Get member growth chart data (last 12 months)
     * OPTIMIZED: 2 queries instead of 24 queries (12 months × 2)
     */
    private function getMemberGrowthChart(): array
    {
        $db = \Config\Database::connect();

        // Get new members per month in a single query
        $newMembersData = $db->query("
            SELECT
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as count
            FROM sp_members
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ")->getResultArray();

        // Convert to associative array for easy lookup
        $newMembersByMonth = [];
        foreach ($newMembersData as $row) {
            $newMembersByMonth[$row['month']] = (int)$row['count'];
        }

        $months = [];
        $totalMembers = [];
        $newMembers = [];
        $runningTotal = 0;

        // Get total members before 12 months ago
        $twelveMonthsAgo = date('Y-m-01', strtotime('-12 months'));
        $baseMemberCount = $db->table('sp_members')
            ->where('created_at <', $twelveMonthsAgo)
            ->countAllResults();

        $runningTotal = $baseMemberCount;

        // Build arrays for last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $newCount = $newMembersByMonth[$date] ?? 0;
            $runningTotal += $newCount;

            $months[] = date('M Y', strtotime($date . '-01'));
            $totalMembers[] = $runningTotal;
            $newMembers[] = $newCount;
        }

        return [
            'months' => $months,
            'total_members' => $totalMembers,
            'new_members' => $newMembers,
        ];
    }

    /**
     * Get payment trend chart data (last 12 months)
     * OPTIMIZED: 1 query instead of 24 queries (12 months × 2)
     */
    private function getPaymentTrendChart(): array
    {
        $db = \Config\Database::connect();

        // Get payment data for last 12 months in a single query
        $paymentData = $db->query("
            SELECT
                CONCAT(payment_year, '-', LPAD(payment_month, 2, '0')) as month,
                SUM(amount) as total_amount,
                COUNT(*) as count
            FROM sp_dues_payments
            WHERE status = 'verified'
                AND CONCAT(payment_year, '-', LPAD(payment_month, 2, '0')) >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 12 MONTH), '%Y-%m')
            GROUP BY payment_year, payment_month
            ORDER BY payment_year ASC, payment_month ASC
        ")->getResultArray();

        // Convert to associative array for easy lookup
        $paymentsByMonth = [];
        foreach ($paymentData as $row) {
            $paymentsByMonth[$row['month']] = [
                'amount' => $row['total_amount'] ?? 0,
                'count' => (int)$row['count']
            ];
        }

        $months = [];
        $amounts = [];
        $counts = [];

        // Build arrays for last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $data = $paymentsByMonth[$date] ?? ['amount' => 0, 'count' => 0];

            $months[] = date('M Y', strtotime($date . '-01'));
            $amounts[] = $data['amount'];
            $counts[] = $data['count'];
        }

        return [
            'months' => $months,
            'amounts' => $amounts,
            'counts' => $counts,
        ];
    }
}
