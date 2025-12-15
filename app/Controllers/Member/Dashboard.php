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
     */
    private function getPaymentStatistics(int $memberId): array
    {
        $db = \Config\Database::connect();

        // Total payments
        $totalPaid = $db->table('sp_dues_payments')
            ->where('member_id', $memberId)
            ->where('status', 'verified')
            ->selectSum('amount')
            ->get()
            ->getRow()
            ->amount ?? 0;

        // Count verified payments
        $verifiedCount = $db->table('sp_dues_payments')
            ->where('member_id', $memberId)
            ->where('status', 'verified')
            ->countAllResults();

        // Count pending payments
        $pendingCount = $db->table('sp_dues_payments')
            ->where('member_id', $memberId)
            ->where('status', 'pending')
            ->countAllResults();

        // Count rejected payments
        $rejectedCount = $db->table('sp_dues_payments')
            ->where('member_id', $memberId)
            ->where('status', 'rejected')
            ->countAllResults();

        // This year payments
        $thisYearPaid = $db->table('sp_dues_payments')
            ->where('member_id', $memberId)
            ->where('status', 'verified')
            ->where('payment_year', date('Y'))
            ->selectSum('amount')
            ->get()
            ->getRow()
            ->amount ?? 0;

        return [
            'total_paid' => $totalPaid,
            'verified_count' => $verifiedCount,
            'pending_count' => $pendingCount,
            'rejected_count' => $rejectedCount,
            'this_year_paid' => $thisYearPaid,
        ];
    }

    /**
     * Get payment history for chart (last 12 months)
     */
    private function getPaymentHistoryForChart(int $memberId): array
    {
        $months = [];
        $amounts = [];

        // Get last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            list($year, $month) = explode('-', $date);

            $payment = $this->paymentModel
                ->where('member_id', $memberId)
                ->where('payment_year', $year)
                ->where('payment_month', (int)$month)
                ->where('status', 'verified')
                ->first();

            $months[] = date('M Y', strtotime($date . '-01'));
            $amounts[] = $payment ? $payment['amount'] : 0;
        }

        return [
            'months' => $months,
            'amounts' => $amounts,
        ];
    }
}
