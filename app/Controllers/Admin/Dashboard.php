<?php

namespace App\Controllers\Admin;

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
     * Admin dashboard with statistics
     */
    public function index()
    {
        // Get statistics
        $stats = [
            'total_members' => $this->memberModel->where('membership_status', 'active')->countAllResults(),
            'total_candidates' => $this->memberModel->where('membership_status', 'candidate')->countAllResults(),
            'pending_approvals' => $this->memberModel->where('onboarding_state', 'email_verified')
                ->where('account_status', 'pending')->countAllResults(),
            'total_suspended' => $this->memberModel->where('account_status', 'suspended')->countAllResults(),
        ];

        // Get pending approvals list
        $pendingApprovals = $this->memberModel->getPendingApprovals(10);

        // Get recent registrations
        $recentRegistrations = $this->memberModel
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'title' => 'Dashboard Admin',
            'description' => 'Dashboard Admin Serikat Pekerja Kampus',
            'stats' => $stats,
            'pending_approvals' => $pendingApprovals,
            'recent_registrations' => $recentRegistrations,
        ];

        return view('admin/dashboard', $data);
    }
}
