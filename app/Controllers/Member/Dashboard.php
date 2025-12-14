<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\MemberModel;

class Dashboard extends BaseController
{
    protected $memberModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        helper(['form', 'url']);
    }

    /**
     * Member dashboard
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
        ];

        $data = [
            'title' => 'Dashboard Member',
            'description' => 'Dashboard Member Serikat Pekerja Kampus',
            'member' => $member,
            'member_info' => $memberInfo,
        ];

        return view('member/dashboard', $data);
    }
}
