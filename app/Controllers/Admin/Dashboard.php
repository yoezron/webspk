<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\DuesPaymentModel;

class Dashboard extends BaseController
{
    protected $memberModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->paymentModel = new DuesPaymentModel();
        helper(['form', 'url', 'app']);
    }

    /**
     * Admin dashboard with enhanced statistics and charts
     */
    public function index()
    {
        // Get member statistics
        $stats = [
            'total_members' => $this->memberModel->where('membership_status', 'active')->countAllResults(),
            'total_candidates' => $this->memberModel->where('membership_status', 'candidate')->countAllResults(),
            'pending_approvals' => $this->memberModel->where('onboarding_state', 'email_verified')
                ->where('account_status', 'pending')->countAllResults(),
            'total_suspended' => $this->memberModel->where('account_status', 'suspended')->countAllResults(),
        ];

        // Get payment statistics
        $paymentStats = $this->getPaymentStatistics();

        // Get monthly statistics
        $monthlyStats = $this->getMonthlyStatistics();

        // Get chart data
        $memberGrowthChart = $this->getMemberGrowthChart();
        $paymentTrendChart = $this->getPaymentTrendChart();

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

        // Prepare stats data for Neptune theme
        $statsData = array_merge($stats, [
            'total_collected' => $paymentStats['total_collected'],
            'pending_verification' => $paymentStats['pending_count'],
            'monthly_collection' => $paymentStats['this_month'],
            'yearly_collection' => $paymentStats['this_year'],
        ]);

        // Prepare chart data for Neptune theme
        $chartData = [
            'member_growth' => [
                'months' => $memberGrowthChart['months'],
                'total' => $memberGrowthChart['total_members'],
                'new' => $memberGrowthChart['new_members'],
            ],
            'payment_trend' => [
                'months' => $paymentTrendChart['months'],
                'amounts' => $paymentTrendChart['amounts'],
                'counts' => $paymentTrendChart['counts'],
            ],
        ];

        // Prepare pending payments for Neptune table
        $pendingPaymentsList = [];
        foreach ($pendingPayments as $payment) {
            $pendingPaymentsList[] = [
                'member_name' => $payment['full_name'] ?? 'N/A',
                'month' => date('F Y', mktime(0, 0, 0, $payment['payment_month'], 1, $payment['payment_year'])),
                'amount' => $payment['amount'],
                'id' => $payment['id'],
            ];
        }

        // Prepare recent registrations
        $recentRegList = [];
        foreach ($recentRegistrations as $member) {
            $recentRegList[] = [
                'name' => $member['full_name'] ?? 'N/A',
                'email' => $member['email'] ?? 'N/A',
                'created_at' => $member['created_at'],
                'id' => $member['id'],
            ];
        }

        $data = [
            'title' => 'Dashboard Admin',
            'description' => 'Dashboard Admin Serikat Pekerja Kampus',
            'stats' => $statsData,
            'monthly_stats' => [
                'new_registrations' => $monthlyStats['new_registrations'],
                'approvals' => $monthlyStats['approved'],
                'payments' => $monthlyStats['payments'],
            ],
            'chart_data' => $chartData,
            'pending_payments' => $pendingPaymentsList,
            'recent_registrations' => $recentRegList,
        ];

        return view('admin/dashboard_neptune', $data);
    }

    /**
     * Get payment statistics
     */
    private function getPaymentStatistics(): array
    {
        $db = \Config\Database::connect();

        // Total collected (verified)
        $totalCollected = $db->table('sp_dues_payments')
            ->where('status', 'verified')
            ->selectSum('amount')
            ->get()
            ->getRow()
            ->amount ?? 0;

        // Pending verifications count
        $pendingCount = $db->table('sp_dues_payments')
            ->where('status', 'pending')
            ->countAllResults();

        // This month collected
        $thisMonth = $db->table('sp_dues_payments')
            ->where('status', 'verified')
            ->where('payment_year', date('Y'))
            ->where('payment_month', (int)date('m'))
            ->selectSum('amount')
            ->get()
            ->getRow()
            ->amount ?? 0;

        // This year collected
        $thisYear = $db->table('sp_dues_payments')
            ->where('status', 'verified')
            ->where('payment_year', date('Y'))
            ->selectSum('amount')
            ->get()
            ->getRow()
            ->amount ?? 0;

        return [
            'total_collected' => $totalCollected,
            'pending_count' => $pendingCount,
            'this_month' => $thisMonth,
            'this_year' => $thisYear,
        ];
    }

    /**
     * Get monthly statistics (current month)
     */
    private function getMonthlyStatistics(): array
    {
        $db = \Config\Database::connect();
        $currentMonth = date('Y-m');

        // New registrations this month
        $newRegistrations = $db->table('sp_members')
            ->where('DATE_FORMAT(created_at, "%Y-%m")', $currentMonth)
            ->countAllResults();

        // Approved this month
        $approved = $db->table('sp_members')
            ->where('membership_status', 'active')
            ->where('DATE_FORMAT(approval_date, "%Y-%m")', $currentMonth)
            ->countAllResults();

        // Payments this month
        $payments = $db->table('sp_dues_payments')
            ->where('payment_year', date('Y'))
            ->where('payment_month', (int)date('m'))
            ->countAllResults();

        return [
            'new_registrations' => $newRegistrations,
            'approved' => $approved,
            'payments' => $payments,
        ];
    }

    /**
     * Get member growth chart data (last 12 months)
     */
    private function getMemberGrowthChart(): array
    {
        $db = \Config\Database::connect();
        $months = [];
        $totalMembers = [];
        $newMembers = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $endDate = date('Y-m-t', strtotime($date . '-01'));

            // Total members up to end of month
            $total = $db->table('sp_members')
                ->where('created_at <=', $endDate . ' 23:59:59')
                ->countAllResults();

            // New members in that month
            $new = $db->table('sp_members')
                ->where('DATE_FORMAT(created_at, "%Y-%m")', $date)
                ->countAllResults();

            $months[] = date('M Y', strtotime($date . '-01'));
            $totalMembers[] = $total;
            $newMembers[] = $new;
        }

        return [
            'months' => $months,
            'total_members' => $totalMembers,
            'new_members' => $newMembers,
        ];
    }

    /**
     * Get payment trend chart data (last 12 months)
     */
    private function getPaymentTrendChart(): array
    {
        $db = \Config\Database::connect();
        $months = [];
        $amounts = [];
        $counts = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            list($year, $month) = explode('-', $date);

            // Total amount collected in that month
            $amount = $db->table('sp_dues_payments')
                ->where('payment_year', $year)
                ->where('payment_month', (int)$month)
                ->where('status', 'verified')
                ->selectSum('amount')
                ->get()
                ->getRow()
                ->amount ?? 0;

            // Count of payments in that month
            $count = $db->table('sp_dues_payments')
                ->where('payment_year', $year)
                ->where('payment_month', (int)$month)
                ->where('status', 'verified')
                ->countAllResults();

            $months[] = date('M Y', strtotime($date . '-01'));
            $amounts[] = $amount;
            $counts[] = $count;
        }

        return [
            'months' => $months,
            'amounts' => $amounts,
            'counts' => $counts,
        ];
    }
}
