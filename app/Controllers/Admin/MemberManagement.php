<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Libraries\EmailService;

class MemberManagement extends BaseController
{
    protected $memberModel;
    protected $emailService;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->emailService = new EmailService();
        helper(['app', 'form']);
    }

    /**
     * List all members with filters
     */
    public function index()
    {
        $perPage = getenv('app.perPage') ?: 20;

        // Get filters from query string
        $status = $this->request->getGet('status');
        $role = $this->request->getGet('role');
        $search = $this->request->getGet('search');

        $builder = $this->memberModel;

        // Apply filters
        if ($status) {
            $builder = $builder->where('membership_status', $status);
        }

        if ($role) {
            $builder = $builder->where('role', $role);
        }

        if ($search) {
            $builder = $builder->groupStart()
                ->like('full_name', $search)
                ->orLike('email', $search)
                ->orLike('member_number', $search)
                ->orLike('university_name', $search)
                ->groupEnd();
        }

        $members = $builder->orderBy('created_at', 'DESC')->paginate($perPage);
        $pager = $builder->pager;

        $data = [
            'title' => 'Manajemen Anggota',
            'members' => $members,
            'pager' => $pager,
            'status_filter' => $status,
            'role_filter' => $role,
            'search' => $search,
        ];

        return view('admin/members/index', $data);
    }

    /**
     * View member details
     */
    public function view($id)
    {
        $member = $this->memberModel->find($id);

        if (!$member) {
            return redirect()->to(base_url('admin/members'))->with('error', 'Anggota tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Anggota - ' . $member['full_name'],
            'member' => $member,
        ];

        return view('admin/members/view', $data);
    }

    /**
     * Pending approvals list
     */
    public function pendingApprovals()
    {
        $perPage = getenv('app.perPage') ?: 20;

        // Get candidates who submitted payment and waiting for approval
        $members = $this->memberModel
            ->where('onboarding_state', 'payment_submitted')
            ->where('account_status', 'pending')
            ->orderBy('created_at', 'ASC')
            ->paginate($perPage);

        $pager = $this->memberModel->pager;

        $data = [
            'title' => 'Persetujuan Anggota Pending',
            'members' => $members,
            'pager' => $pager,
        ];

        return view('admin/members/pending_approvals', $data);
    }

    /**
     * Approve member
     */
    public function approve($id)
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        $member = $this->memberModel->find($id);

        if (!$member) {
            return redirect()->back()->with('error', 'Anggota tidak ditemukan');
        }

        // Generate member number
        helper('app');
        $memberNumber = generate_member_number($id, substr($member['province'], 0, 3));

        // Update member status
        $updateData = [
            'member_number' => $memberNumber,
            'membership_status' => 'active',
            'account_status' => 'active',
            'onboarding_state' => 'approved',
            'role' => 'member',
            'approval_date' => date('Y-m-d H:i:s'),
            'approved_by' => session()->get('user_id'),
        ];

        if ($this->memberModel->update($id, $updateData)) {
            // Send approval email
            $this->emailService->sendMembershipApproval(
                $member['email'],
                $member['full_name'],
                $memberNumber
            );

            // Audit log
            helper('audit');
            audit_log_member_action(
                'approve',
                $id,
                $memberNumber,
                "Member {$member['full_name']} ({$memberNumber}) approved by admin",
                ['status' => $member['account_status'], 'onboarding_state' => $member['onboarding_state']],
                $updateData
            );

            log_message('info', "Member approved: ID {$id}, Number: {$memberNumber} by admin " . session()->get('user_id'));

            return redirect()->back()->with('success', 'Anggota berhasil disetujui dan email notifikasi telah dikirim');
        } else {
            return redirect()->back()->with('error', 'Gagal menyetujui anggota');
        }
    }

    /**
     * Reject member
     */
    public function reject($id)
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        $member = $this->memberModel->find($id);

        if (!$member) {
            return redirect()->back()->with('error', 'Anggota tidak ditemukan');
        }

        $reason = $this->request->getPost('rejection_reason');

        if (!$reason) {
            return redirect()->back()->with('error', 'Alasan penolakan wajib diisi');
        }

        // Update member status
        $updateData = [
            'account_status' => 'rejected',
            'onboarding_state' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_by' => session()->get('user_id'),
        ];

        if ($this->memberModel->update($id, $updateData)) {
            // Send rejection email
            $this->emailService->sendMembershipRejection(
                $member['email'],
                $member['full_name'],
                $reason
            );

            // Audit log
            helper('audit');
            audit_log_member_action(
                'reject',
                $id,
                $member['email'],
                "Member {$member['full_name']} ({$member['email']}) rejected: {$reason}",
                ['status' => $member['account_status'], 'onboarding_state' => $member['onboarding_state']],
                $updateData
            );

            log_message('info', "Member rejected: ID {$id} by admin " . session()->get('user_id'));

            return redirect()->back()->with('success', 'Anggota ditolak dan email notifikasi telah dikirim');
        } else {
            return redirect()->back()->with('error', 'Gagal menolak anggota');
        }
    }

    /**
     * Suspend member
     */
    public function suspend($id)
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        $member = $this->memberModel->find($id);

        if (!$member) {
            return redirect()->back()->with('error', 'Anggota tidak ditemukan');
        }

        $reason = $this->request->getPost('suspension_reason');

        if (!$reason) {
            return redirect()->back()->with('error', 'Alasan penangguhan wajib diisi');
        }

        $updateData = [
            'account_status' => 'suspended',
            'notes' => ($member['notes'] ?? '') . "\n[" . date('Y-m-d H:i:s') . "] Suspended by admin " . session()->get('user_id') . ": " . $reason,
        ];

        if ($this->memberModel->update($id, $updateData)) {
            // Audit log
            helper('audit');
            audit_log_member_action(
                'suspend',
                $id,
                $member['member_number'] ?? $member['email'],
                "Member {$member['full_name']} suspended: {$reason}",
                ['status' => $member['account_status']],
                $updateData
            );

            log_message('info', "Member suspended: ID {$id} by admin " . session()->get('user_id'));
            return redirect()->back()->with('success', 'Anggota berhasil ditangguhkan');
        } else {
            return redirect()->back()->with('error', 'Gagal menangguhkan anggota');
        }
    }

    /**
     * Activate suspended member
     */
    public function activate($id)
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        $member = $this->memberModel->find($id);

        if (!$member) {
            return redirect()->back()->with('error', 'Anggota tidak ditemukan');
        }

        $updateData = [
            'account_status' => 'active',
            'notes' => ($member['notes'] ?? '') . "\n[" . date('Y-m-d H:i:s') . "] Activated by admin " . session()->get('user_id'),
        ];

        if ($this->memberModel->update($id, $updateData)) {
            log_message('info', "Member activated: ID {$id} by admin " . session()->get('user_id'));
            return redirect()->back()->with('success', 'Anggota berhasil diaktifkan kembali');
        } else {
            return redirect()->back()->with('error', 'Gagal mengaktifkan anggota');
        }
    }

    /**
     * Delete member (soft delete or permanently based on requirement)
     */
    public function delete($id)
    {
        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        $member = $this->memberModel->find($id);

        if (!$member) {
            return redirect()->back()->with('error', 'Anggota tidak ditemukan');
        }

        // Check if it's safe to delete (e.g., no payment history, etc.)
        // For now, we'll just delete

        if ($this->memberModel->delete($id)) {
            log_message('warning', "Member deleted: ID {$id} by admin " . session()->get('user_id'));
            return redirect()->to(base_url('admin/members'))->with('success', 'Anggota berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus anggota');
        }
    }
}
