<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DuesPaymentModel;
use App\Models\MemberModel;
use App\Libraries\EmailService;

class PaymentManagement extends BaseController
{
    protected $paymentModel;
    protected $memberModel;
    protected $emailService;
    protected $cache;

    public function __construct()
    {
        $this->paymentModel = new DuesPaymentModel();
        $this->memberModel = new MemberModel();
        $this->emailService = new EmailService();
        $this->cache = \Config\Services::cache();
        helper(['app', 'form']);
    }

    /**
     * List all payments with filters
     */
    public function index()
    {
        $perPage = getenv('app.perPage') ?: 20;

        // Get filters
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');

        $builder = $this->paymentModel
            ->select('sp_dues_payments.*, sp_members.full_name, sp_members.member_number, sp_members.email')
            ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id');

        // Apply filters
        if ($status) {
            $builder = $builder->where('sp_dues_payments.status', $status);
        }

        if ($search) {
            $builder = $builder->groupStart()
                ->like('sp_members.full_name', $search)
                ->orLike('sp_members.member_number', $search)
                ->orLike('sp_dues_payments.payment_reference', $search)
                ->groupEnd();
        }

        $payments = $builder->orderBy('sp_dues_payments.created_at', 'DESC')->paginate($perPage);
        $pager = $builder->pager;

        // Get statistics
        $stats = $this->paymentModel->getPaymentStats();

        $data = [
            'title' => 'Manajemen Pembayaran Iuran',
            'payments' => $payments,
            'pager' => $pager,
            'stats' => $stats,
            'status_filter' => $status,
            'search' => $search,
        ];

        return view('admin/payments/index', $data);
    }

    /**
     * Pending payment verifications
     */
    public function pendingVerifications()
    {
        $perPage = getenv('app.perPage') ?: 20;

        $payments = $this->paymentModel->getPendingVerifications($perPage);
        $pager = $this->paymentModel->pager;

        $data = [
            'title' => 'Verifikasi Pembayaran Pending',
            'payments' => $payments,
            'pager' => $pager,
        ];

        return view('admin/payments/pending_verifications', $data);
    }

    /**
     * Verify payment
     */
    public function verify($id)
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        $payment = $this->paymentModel->find($id);

        if (!$payment) {
            return redirect()->back()->with('error', 'Pembayaran tidak ditemukan');
        }

        $member = $this->memberModel->find($payment['member_id']);

        if (!$member) {
            return redirect()->back()->with('error', 'Anggota tidak ditemukan');
        }

        // Update payment status
        $updateData = [
            'status' => 'verified',
            'verified_by' => session()->get('user_id'),
            'verified_at' => date('Y-m-d H:i:s'),
            'verification_notes' => $this->request->getPost('notes'),
        ];

        if ($this->paymentModel->update($id, $updateData)) {
            // Clear dashboard cache - payment stats changed
            $this->clearDashboardCache();

            // Update member dues info
            $this->memberModel->update($payment['member_id'], [
                'last_dues_payment_date' => $payment['payment_date'],
            ]);

            // Recalculate arrears
            $this->recalculateArrears($payment['member_id']);

            // Send confirmation email
            $periodText = date('F Y', mktime(0, 0, 0, $payment['payment_month'], 1, $payment['payment_year']));
            $this->emailService->sendPaymentConfirmation(
                $member['email'],
                $member['full_name'],
                $payment['amount'],
                $periodText
            );

            // Audit log
            helper('audit');
            audit_log_payment_action(
                'verify',
                $id,
                "Payment #{$id} - {$member['member_number']}",
                "Payment Rp " . number_format($payment['amount'], 0, ',', '.') . " for {$periodText} verified",
                ['status' => $payment['status']],
                $updateData
            );

            log_message('info', "Payment verified: ID {$id} by admin " . session()->get('user_id'));

            return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi dan email konfirmasi telah dikirim');
        } else {
            return redirect()->back()->with('error', 'Gagal memverifikasi pembayaran');
        }
    }

    /**
     * Reject payment
     */
    public function reject($id)
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        $payment = $this->paymentModel->find($id);

        if (!$payment) {
            return redirect()->back()->with('error', 'Pembayaran tidak ditemukan');
        }

        $reason = $this->request->getPost('rejection_reason');

        if (!$reason) {
            return redirect()->back()->with('error', 'Alasan penolakan wajib diisi');
        }

        // Update payment status
        $updateData = [
            'status' => 'rejected',
            'verified_by' => session()->get('user_id'),
            'verified_at' => date('Y-m-d H:i:s'),
            'verification_notes' => $reason,
        ];

        if ($this->paymentModel->update($id, $updateData)) {
            // Clear dashboard cache - payment stats changed
            $this->clearDashboardCache();

            // Audit log
            helper('audit');
            $member = $this->memberModel->find($payment['member_id']);
            audit_log_payment_action(
                'reject',
                $id,
                "Payment #{$id} - " . ($member['member_number'] ?? $payment['member_id']),
                "Payment rejected: {$reason}",
                ['status' => $payment['status']],
                $updateData
            );

            log_message('info', "Payment rejected: ID {$id} by admin " . session()->get('user_id'));
            return redirect()->back()->with('success', 'Pembayaran ditolak');
        } else {
            return redirect()->back()->with('error', 'Gagal menolak pembayaran');
        }
    }

    /**
     * Recalculate member arrears
     */
    private function recalculateArrears(int $memberId)
    {
        $member = $this->memberModel->find($memberId);

        if (!$member || !$member['approval_date']) {
            return;
        }

        // Get approval date
        $approvalDate = new \DateTime($member['approval_date']);
        $currentDate = new \DateTime();

        // Calculate months since approval
        $interval = $approvalDate->diff($currentDate);
        $monthsSinceApproval = ($interval->y * 12) + $interval->m;

        if ($monthsSinceApproval <= 0) {
            $monthsSinceApproval = 1;
        }

        // Get total months paid
        $paidPayments = $this->paymentModel
            ->where('member_id', $memberId)
            ->where('status', 'verified')
            ->where('payment_type', 'monthly_dues')
            ->findAll();

        $monthsPaid = count($paidPayments);

        // Calculate arrears
        $monthsInArrears = max(0, $monthsSinceApproval - $monthsPaid);
        $monthlyAmount = $member['monthly_dues_amount'] ?? 0;
        $totalArrears = $monthsInArrears * $monthlyAmount;

        // Update member
        $this->memberModel->update($memberId, [
            'arrears_months' => $monthsInArrears,
            'total_arrears' => $totalArrears,
        ]);
    }

    /**
     * View payment details
     */
    public function view($id)
    {
        $payment = $this->paymentModel
            ->select('sp_dues_payments.*, sp_members.full_name, sp_members.member_number, sp_members.email, sp_members.phone_number')
            ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
            ->find($id);

        if (!$payment) {
            return redirect()->to(base_url('admin/payments'))->with('error', 'Pembayaran tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Pembayaran',
            'payment' => $payment,
        ];

        return view('admin/payments/view', $data);
    }

    /**
     * Clear dashboard cache when payment data changes
     */
    private function clearDashboardCache(): void
    {
        $cacheKeys = [
            'admin_member_stats',
            'admin_payment_stats',
            'admin_monthly_stats',
            'admin_member_growth_chart',
            'admin_payment_trend_chart',
        ];

        foreach ($cacheKeys as $key) {
            $this->cache->delete($key);
        }

        log_message('debug', 'Dashboard cache cleared after payment data change');
    }
}
