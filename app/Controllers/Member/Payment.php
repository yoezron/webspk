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

        // Get member's monthly dues amount
        $monthlyDues = (float) $member['monthly_dues_amount'];

        // Validation rules
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

        // Validate payment amount against member's dues
        $amount = (float) $this->request->getPost('amount');

        if ($monthlyDues > 0) {
            // Minimum: 50% of monthly dues (allow partial payment)
            $minAmount = $monthlyDues * 0.5;
            // Maximum: 200% of monthly dues (allow double payment for arrears)
            $maxAmount = $monthlyDues * 2;

            if ($amount < $minAmount) {
                return redirect()->back()->withInput()->with('error',
                    sprintf('Jumlah pembayaran minimal adalah Rp %s (50%% dari iuran bulanan Rp %s)',
                        number_format($minAmount, 0, ',', '.'),
                        number_format($monthlyDues, 0, ',', '.')
                    )
                );
            }

            if ($amount > $maxAmount) {
                return redirect()->back()->withInput()->with('error',
                    sprintf('Jumlah pembayaran maksimal adalah Rp %s (200%% dari iuran bulanan Rp %s). Untuk pembayaran lebih besar, silakan hubungi bendahara.',
                        number_format($maxAmount, 0, ',', '.'),
                        number_format($monthlyDues, 0, ',', '.')
                    )
                );
            }

            // Warn if amount doesn't match exact dues
            if ($amount != $monthlyDues) {
                session()->setFlashdata('warning',
                    sprintf('Iuran bulanan Anda adalah Rp %s. Anda membayar Rp %s.',
                        number_format($monthlyDues, 0, ',', '.'),
                        number_format($amount, 0, ',', '.')
                    )
                );
            }
        } else {
            // No dues amount set - log warning and allow any amount
            log_message('warning', "Member {$memberId} has no monthly_dues_amount set. Allowing payment of {$amount}");
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

        try {
            // Attempt to insert payment
            if ($this->paymentModel->insert($paymentData)) {
                log_message('info', "Payment submitted by member {$memberId} for period {$month}/{$year}");
                return redirect()->to(base_url('member/payment'))->with('success', 'Pembayaran berhasil disubmit. Menunggu verifikasi admin.');
            } else {
                // Delete uploaded file if insert failed
                $uploadPath = FCPATH . 'uploads/payments/' . $uploadResult['file_name'];
                if (file_exists($uploadPath)) {
                    unlink($uploadPath);
                }

                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data pembayaran');
            }
        } catch (\Exception $e) {
            // Delete uploaded file since insert failed
            $uploadPath = FCPATH . 'uploads/payments/' . $uploadResult['file_name'];
            if (file_exists($uploadPath)) {
                unlink($uploadPath);
            }

            // Check if this is a duplicate key constraint violation
            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'unique_payment_period') !== false ||
                strpos($errorMessage, 'Duplicate entry') !== false) {

                // Race condition detected - log for monitoring
                log_message('warning', "Race condition detected: Duplicate payment attempt by member {$memberId} for period {$month}/{$year}. Error: {$errorMessage}");

                return redirect()->back()->withInput()->with('error',
                    'Pembayaran untuk periode ini sudah ada. Kemungkinan terjadi pengiriman ganda. Silakan cek riwayat pembayaran Anda.');
            }

            // Other database errors
            log_message('error', "Payment insert failed for member {$memberId}: {$errorMessage}");
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan pembayaran. Silakan coba lagi.');
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
