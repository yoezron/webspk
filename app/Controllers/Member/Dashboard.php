<?php

namespace App\Controllers\Member;

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
        helper(['form', 'url']);
    }

    /**
     * Member dashboard with enhanced statistics
     * OPTIMIZED: Reduced from 20 queries to ~5 queries per page load
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $member = $this->memberModel->find($userId);

        if (!$member) {
            return redirect()->to(base_url('logout'))->with('error', 'Sesi tidak valid');
        }

        // Get member info
        $memberInfo = [
            'member_number' => $member['member_number'] ?? 'Belum ditetapkan',
            'membership_status' => $member['membership_status'],
            'onboarding_state' => $member['onboarding_state'],
            'account_status' => $member['account_status'],
            'monthly_dues' => $member['monthly_dues_amount'] ?? 0,
            'total_arrears' => $member['total_arrears'] ?? 0,
            'arrears_months' => $member['arrears_months'] ?? 0,
            'last_dues_payment' => $member['last_dues_payment_date'] ?? null,
            'approval_date' => $member['approval_date'] ?? null,
        ];

        // Get payment statistics
        $paymentStats = $this->getPaymentStatistics($userId);

        // Get recent payments (last 5)
        $recentPayments = $this->paymentModel
            ->where('member_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Get payment history for chart (last 12 months)
        $paymentHistory = $this->getPaymentHistoryForChart($userId);

        // Check pending payments
        $pendingPayments = $this->paymentModel
            ->where('member_id', $userId)
            ->where('status', 'pending')
            ->countAllResults();

        $data = [
            'title' => 'Dashboard Member',
            'description' => 'Dashboard Member Serikat Pekerja Kampus',
            'member' => $member,
            'member_info' => $memberInfo,
            'payment_stats' => $paymentStats,
            'recent_payments' => $recentPayments,
            'payment_history' => $paymentHistory,
            'pending_payments' => $pendingPayments,
        ];

        return view('member/dashboard', $data);
    }

    /**
     * Get payment statistics
     * OPTIMIZED: 1 query instead of 5 separate queries
     */
    private function getPaymentStatistics(int $memberId): array
    {
        $db = \Config\Database::connect();
        $currentYear = date('Y');

        // Single query with conditional aggregation
        $result = $db->query("
            SELECT
                SUM(CASE WHEN status = 'verified' THEN amount ELSE 0 END) as total_paid,
                SUM(CASE WHEN status = 'verified' THEN 1 ELSE 0 END) as verified_count,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
                SUM(CASE WHEN status = 'verified' AND payment_year = ? THEN amount ELSE 0 END) as this_year_paid
            FROM sp_dues_payments
            WHERE member_id = ?
        ", [$currentYear, $memberId])->getRow();

        return [
            'total_paid' => $result->total_paid ?? 0,
            'verified_count' => (int)$result->verified_count,
            'pending_count' => (int)$result->pending_count,
            'rejected_count' => (int)$result->rejected_count,
            'this_year_paid' => $result->this_year_paid ?? 0,
        ];
    }

    /**
     * Get payment history for chart (last 12 months)
     * OPTIMIZED: 1 query instead of 12 queries
     */
    private function getPaymentHistoryForChart(int $memberId): array
    {
        $db = \Config\Database::connect();

        // Get payment data for last 12 months in a single query
        $paymentData = $db->query("
            SELECT
                CONCAT(payment_year, '-', LPAD(payment_month, 2, '0')) as month,
                SUM(amount) as total_amount
            FROM sp_dues_payments
            WHERE member_id = ?
                AND status = 'verified'
                AND CONCAT(payment_year, '-', LPAD(payment_month, 2, '0')) >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 12 MONTH), '%Y-%m')
            GROUP BY payment_year, payment_month
            ORDER BY payment_year ASC, payment_month ASC
        ", [$memberId])->getResultArray();

        // Convert to associative array for easy lookup
        $paymentsByMonth = [];
        foreach ($paymentData as $row) {
            $paymentsByMonth[$row['month']] = $row['total_amount'] ?? 0;
        }

        $months = [];
        $amounts = [];

        // Build arrays for last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $months[] = date('M Y', strtotime($date . '-01'));
            $amounts[] = $paymentsByMonth[$date] ?? 0;
        }

        return [
            'months' => $months,
            'amounts' => $amounts,
        ];
    }
}
