<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Libraries\EmailService;

class MemberManagement extends BaseController
{
    protected $memberModel;
    protected $emailService;
    protected $cache;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->emailService = new EmailService();
        $this->cache = \Config\Services::cache();
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
     * Suspended members list
     */
    public function suspendedMembers()
    {
        $perPage = getenv('app.perPage') ?: 20;

        // Get suspended members
        $members = $this->memberModel
            ->where('account_status', 'suspended')
            ->orderBy('updated_at', 'DESC')
            ->paginate($perPage);

        $pager = $this->memberModel->pager;

        $data = [
            'title' => 'Anggota Ditangguhkan',
            'members' => $members,
            'pager' => $pager,
        ];

        return view('admin/members/suspended', $data);
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

        // Validate approval requirements
        $validationErrors = [];

        // 1. Check if email is verified
        if (empty($member['email_verified_at'])) {
            $validationErrors[] = 'Email belum diverifikasi';
        }

        // 2. Check if registration payment proof uploaded
        if (empty($member['registration_payment_proof'])) {
            $validationErrors[] = 'Bukti pembayaran pendaftaran belum diupload';
        }

        // 3. Check if required documents uploaded
        if (empty($member['id_card_photo'])) {
            $validationErrors[] = 'Foto KTP belum diupload';
        }

        // 4. Check if personal data completed
        if (empty($member['birth_date']) || empty($member['address'])) {
            $validationErrors[] = 'Data pribadi belum lengkap (tanggal lahir, alamat)';
        }

        // 5. Check if professional data completed
        if (empty($member['faculty']) || empty($member['department'])) {
            $validationErrors[] = 'Data pekerjaan belum lengkap (fakultas, departemen)';
        }

        // 6. Check onboarding state
        if ($member['onboarding_state'] !== 'payment_submitted') {
            $validationErrors[] = 'Status pendaftaran belum sesuai. Harus sudah submit pembayaran. Status saat ini: ' . $member['onboarding_state'];
        }

        // 7. Check current role
        if ($member['role'] !== 'candidate') {
            $validationErrors[] = 'Member ini bukan candidate (role: ' . $member['role'] . ')';
        }

        // 8. Check not already approved
        if ($member['account_status'] === 'active' || $member['membership_status'] === 'active') {
            $validationErrors[] = 'Member sudah diapprove sebelumnya';
        }

        // If validation fails, return errors
        if (!empty($validationErrors)) {
            $errorMessage = 'Tidak dapat menyetujui anggota. Persyaratan yang belum dipenuhi:<br>';
            $errorMessage .= '<ul class="mb-0 ps-3">';
            foreach ($validationErrors as $error) {
                $errorMessage .= '<li>' . $error . '</li>';
            }
            $errorMessage .= '</ul>';

            log_message('warning', "Approval blocked for member {$id}: " . implode(', ', $validationErrors));

            return redirect()->back()->with('error', $errorMessage);
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
            // Mark status changed to invalidate active sessions
            $this->memberModel->markStatusChanged($id);

            // Clear dashboard cache - member stats changed
            $this->clearDashboardCache();

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
            // Mark status changed to invalidate active sessions
            $this->memberModel->markStatusChanged($id);

            // Clear dashboard cache - member stats changed
            $this->clearDashboardCache();

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
            // Mark status changed to invalidate active sessions
            $this->memberModel->markStatusChanged($id);

            // Clear dashboard cache - member stats changed
            $this->clearDashboardCache();

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
            // Mark status changed to invalidate active sessions
            $this->memberModel->markStatusChanged($id);

            // Clear dashboard cache - member stats changed
            $this->clearDashboardCache();

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
            // Clear dashboard cache - member stats changed
            $this->clearDashboardCache();

            log_message('warning', "Member deleted: ID {$id} by admin " . session()->get('user_id'));
            return redirect()->to(base_url('admin/members'))->with('success', 'Anggota berhasil dihapus');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus anggota');
        }
    }

    /**
     * Clear dashboard cache when member data changes
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

        log_message('debug', 'Dashboard cache cleared after member data change');
    }
}
