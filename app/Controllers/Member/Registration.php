<?php

namespace App\Controllers\Member;

use App\Controllers\BaseController;
use App\Models\MemberModel;

class Registration extends BaseController
{
    protected $memberModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        helper(['form', 'url']);
    }

    /**
     * Display registration status page for candidates
     */
    public function status()
    {
        $userId = session()->get('user_id');
        $member = $this->memberModel->find($userId);

        if (!$member) {
            return redirect()->to(base_url('logout'))->with('error', 'Sesi tidak valid');
        }

        $data = [
            'title' => 'Status Pendaftaran',
            'description' => 'Status pendaftaran keanggotaan Anda',
            'member' => $member,
        ];

        return view('member/registration_status', $data);
    }

    /**
     * Complete registration for candidates (if they have incomplete data)
     */
    public function complete()
    {
        $userId = session()->get('user_id');
        $member = $this->memberModel->find($userId);

        if (!$member) {
            return redirect()->to(base_url('logout'))->with('error', 'Sesi tidak valid');
        }

        // Check if already completed
        if ($member['onboarding_state'] !== 'registered') {
            return redirect()->to(base_url('member/registration/status'));
        }

        $data = [
            'title' => 'Lengkapi Pendaftaran',
            'description' => 'Lengkapi data pendaftaran Anda',
            'member' => $member,
        ];

        return view('member/registration_complete', $data);
    }

    /**
     * Process complete registration
     */
    public function processComplete()
    {
        $userId = session()->get('user_id');
        $member = $this->memberModel->find($userId);

        if (!$member || $member['onboarding_state'] !== 'registered') {
            return redirect()->to(base_url('member/registration/status'));
        }

        // Handle completion logic here
        // This would be similar to Register::processStep4

        return redirect()->to(base_url('member/registration/status'))
            ->with('success', 'Data berhasil dilengkapi. Menunggu verifikasi admin.');
    }
}
