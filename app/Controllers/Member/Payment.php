<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\DuesPaymentModel;
use App\Models\MemberModel;

class Payment extends BaseController
{
    protected $paymentModel;
    protected $memberModel;

    public function __construct()
    {
        $this->paymentModel = new DuesPaymentModel();
        $this->memberModel = new MemberModel();
        helper(['app', 'form', 'upload']);
    }

    /**
     * Payment history for member
     */
    public function index()
    {
        $memberId = session()->get('user_id');
        $member = $this->memberModel->find($memberId);

        $perPage = getenv('app.perPage') ?: 20;

        $payments = $this->paymentModel->getPaymentsByMember($memberId, $perPage);
        $pager = $this->paymentModel->pager;

        $data = [
            'title' => 'Riwayat Pembayaran Iuran',
            'member' => $member,
            'payments' => $payments,
            'pager' => $pager,
        ];

        return view('member/payment/index', $data);
    }

    /**
     * Submit new payment
     */
    public function submit()
    {
        $memberId = session()->get('user_id');
        $member = $this->memberModel->find($memberId);

        if (!$member) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Member tidak ditemukan');
        }

        // Check if member is active
        if ($member['membership_status'] != 'active' || $member['account_status'] != 'active') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Hanya anggota aktif yang dapat melakukan pembayaran');
        }

        $data = [
            'title' => 'Submit Pembayaran Iuran',
            'member' => $member,
            'validation' => \Config\Services::validation(),
        ];

        return view('member/payment/submit', $data);
    }

    /**
     * Process payment submission
     */
    public function processSubmit()
    {
        $memberId = session()->get('user_id');
        $member = $this->memberModel->find($memberId);

        if (!$member) {
            return redirect()->back()->with('error', 'Member tidak ditemukan');
        }

        // Validation
        $rules = [
            'payment_month' => 'required|numeric|in_list[1,2,3,4,5,6,7,8,9,10,11,12]',
            'payment_year' => 'required|numeric|min_length[4]|max_length[4]',
            'payment_date' => 'required|valid_date',
            'payment_method' => 'required',
            'amount' => 'required|numeric|greater_than[0]',
            'payment_proof' => 'uploaded[payment_proof]|max_size[payment_proof,2048]|ext_in[payment_proof,jpg,jpeg,png,pdf]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $month = $this->request->getPost('payment_month');
        $year = $this->request->getPost('payment_year');

        // Check if payment already exists for this period
        if ($this->paymentModel->hasPaymentForPeriod($memberId, $month, $year)) {
            return redirect()->back()->withInput()->with('error', 'Anda sudah melakukan pembayaran untuk periode ini');
        }

        // Upload payment proof
        $file = $this->request->getFile('payment_proof');
        $uploadResult = upload_file($file, 'uploads/payments/', ['jpg', 'jpeg', 'png', 'pdf'], 2048);

        if (!$uploadResult['success']) {
            return redirect()->back()->withInput()->with('error', 'Gagal upload bukti pembayaran: ' . $uploadResult['error']);
        }

        // Create payment record
        $paymentData = [
            'member_id' => $memberId,
            'payment_type' => 'monthly_dues',
            'amount' => $this->request->getPost('amount'),
            'payment_period' => $month . '/' . $year,
            'payment_month' => $month,
            'payment_year' => $year,
            'payment_date' => $this->request->getPost('payment_date'),
            'payment_method' => $this->request->getPost('payment_method'),
            'payment_proof' => $uploadResult['file_name'],
            'payment_reference' => $this->request->getPost('payment_reference'),
            'status' => 'pending',
            'notes' => $this->request->getPost('notes'),
        ];

        if ($this->paymentModel->insert($paymentData)) {
            log_message('info', "Payment submitted by member {$memberId} for period {$month}/{$year}");
            return redirect()->to(base_url('member/payment'))->with('success', 'Pembayaran berhasil disubmit. Menunggu verifikasi admin.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data pembayaran');
        }
    }

    /**
     * View payment detail
     */
    public function view($id)
    {
        $memberId = session()->get('user_id');

        $payment = $this->paymentModel->find($id);

        if (!$payment || $payment['member_id'] != $memberId) {
            return redirect()->to(base_url('member/payment'))->with('error', 'Pembayaran tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Pembayaran',
            'payment' => $payment,
        ];

        return view('member/payment/view', $data);
    }
}
