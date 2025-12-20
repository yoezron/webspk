<?php

namespace App\Controllers\Coordinator;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\CoordinatorRegionModel;
use App\Models\AuditLogModel;

class MemberController extends BaseController
{
    protected $memberModel;
    protected $coordinatorRegionModel;
    protected $auditModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->coordinatorRegionModel = new CoordinatorRegionModel();
        $this->auditModel = new AuditLogModel();
        helper(['form', 'url', 'rbac']);
    }

    /**
     * List members in coordinator's regions
     */
    public function index()
    {
        $userId = session()->get('user_id');

        // Get coordinator's assigned regions
        $assignedRegions = $this->coordinatorRegionModel->getCoordinatorRegions($userId);
        $regionCodes = array_column($assignedRegions, 'region_code');

        if (empty($regionCodes)) {
            return view('coordinator/members/index', [
                'title' => 'Daftar Anggota Wilayah',
                'members' => [],
                'assigned_regions' => [],
                'pager' => null,
                'message' => 'Anda belum memiliki wilayah yang ter-assign. Hubungi administrator.',
            ]);
        }

        // Filters
        $search = $this->request->getGet('search');
        $statusFilter = $this->request->getGet('status');
        $regionFilter = $this->request->getGet('region');
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;

        // Build query
        $builder = $this->memberModel
            ->select('sp_members.*, sp_region_codes.province_name')
            ->join('sp_region_codes', 'sp_region_codes.region_code = sp_members.region_code', 'left')
            ->whereIn('sp_members.region_code', $regionCodes)
            ->where('sp_members.account_status !=', 'deleted');

        // Apply filters
        if ($search) {
            $builder->groupStart()
                ->like('sp_members.full_name', $search)
                ->orLike('sp_members.email', $search)
                ->orLike('sp_members.member_number', $search)
                ->groupEnd();
        }

        if ($statusFilter) {
            $builder->where('sp_members.membership_status', $statusFilter);
        }

        if ($regionFilter && in_array($regionFilter, $regionCodes)) {
            $builder->where('sp_members.region_code', $regionFilter);
        }

        $members = $builder->orderBy('sp_members.created_at', 'DESC')
            ->paginate($perPage);

        // Get statistics
        $stats = [
            'total' => $this->memberModel->whereIn('region_code', $regionCodes)
                ->where('account_status !=', 'deleted')
                ->countAllResults(),
            'active' => $this->memberModel->whereIn('region_code', $regionCodes)
                ->where('membership_status', 'active')
                ->countAllResults(),
            'candidates' => $this->memberModel->whereIn('region_code', $regionCodes)
                ->where('membership_status', 'candidate')
                ->countAllResults(),
            'suspended' => $this->memberModel->whereIn('region_code', $regionCodes)
                ->where('membership_status', 'suspended')
                ->countAllResults(),
        ];

        $data = [
            'title' => 'Daftar Anggota Wilayah',
            'members' => $members,
            'assigned_regions' => $assignedRegions,
            'pager' => $this->memberModel->pager,
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'status' => $statusFilter,
                'region' => $regionFilter,
            ],
        ];

        return view('coordinator/members/index', $data);
    }

    /**
     * Pending member approvals in coordinator's regions
     */
    public function pending()
    {
        $userId = session()->get('user_id');

        // Get coordinator's assigned regions
        $assignedRegions = $this->coordinatorRegionModel->getCoordinatorRegions($userId);
        $regionCodes = array_column($assignedRegions, 'region_code');

        if (empty($regionCodes)) {
            return view('coordinator/members/pending', [
                'title' => 'Persetujuan Anggota Pending',
                'members' => [],
                'assigned_regions' => [],
                'message' => 'Anda belum memiliki wilayah yang ter-assign.',
            ]);
        }

        // Get pending members in coordinator's regions
        $pendingMembers = $this->memberModel
            ->select('sp_members.*, sp_region_codes.province_name')
            ->join('sp_region_codes', 'sp_region_codes.region_code = sp_members.region_code', 'left')
            ->whereIn('sp_members.region_code', $regionCodes)
            ->where('sp_members.membership_status', 'candidate')
            ->where('sp_members.registration_status', 'completed')
            ->orderBy('sp_members.created_at', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Persetujuan Anggota Pending',
            'members' => $pendingMembers,
            'assigned_regions' => $assignedRegions,
        ];

        return view('coordinator/members/pending', $data);
    }

    /**
     * View member details
     */
    public function view($memberId = null)
    {
        if (!$memberId) {
            return redirect()->back()->with('error', 'Member ID tidak valid');
        }

        $userId = session()->get('user_id');

        // Get coordinator's assigned regions
        $assignedRegions = $this->coordinatorRegionModel->getCoordinatorRegions($userId);
        $regionCodes = array_column($assignedRegions, 'region_code');

        // Get member
        $member = $this->memberModel
            ->select('sp_members.*, sp_region_codes.province_name')
            ->join('sp_region_codes', 'sp_region_codes.region_code = sp_members.region_code', 'left')
            ->find($memberId);

        if (!$member) {
            return redirect()->back()->with('error', 'Member tidak ditemukan');
        }

        // Check if member is in coordinator's region
        if (!in_array($member['region_code'], $regionCodes)) {
            return redirect()->back()->with('error', 'Member tidak dalam wilayah Anda');
        }

        // Get member's payment history
        $payments = $this->db->table('sp_dues_payments')
            ->where('member_id', $memberId)
            ->orderBy('payment_year DESC, payment_month DESC')
            ->limit(12)
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Detail Anggota - ' . $member['full_name'],
            'member' => $member,
            'payments' => $payments,
        ];

        return view('coordinator/members/view', $data);
    }

    /**
     * Approve member (coordinator recommendation)
     */
    public function approve($memberId = null)
    {
        if (!$this->request->is('post')) {
            return redirect()->back()->with('error', 'Invalid request method');
        }

        if (!$memberId) {
            return redirect()->back()->with('error', 'Member ID tidak valid');
        }

        $userId = session()->get('user_id');

        // Get coordinator's assigned regions
        $assignedRegions = $this->coordinatorRegionModel->getCoordinatorRegions($userId);
        $regionCodes = array_column($assignedRegions, 'region_code');

        // Get member
        $member = $this->memberModel->find($memberId);

        if (!$member) {
            return redirect()->back()->with('error', 'Member tidak ditemukan');
        }

        // Check if member is in coordinator's region
        if (!in_array($member['region_code'], $regionCodes)) {
            return redirect()->back()->with('error', 'Member tidak dalam wilayah Anda');
        }

        // Check if already approved
        if ($member['membership_status'] === 'active') {
            return redirect()->back()->with('info', 'Member sudah disetujui');
        }

        // Coordinators can only recommend, not directly approve
        // Add coordinator_recommendation field
        $notes = $this->request->getPost('notes') ?? '';

        $updateData = [
            'coordinator_recommendation' => 'approved',
            'coordinator_notes' => $notes,
            'coordinator_reviewed_at' => date('Y-m-d H:i:s'),
            'coordinator_reviewed_by' => $userId,
        ];

        if ($this->memberModel->update($memberId, $updateData)) {
            // Log the action
            $this->auditModel->log(
                'update',
                'sp_members',
                $memberId,
                "Coordinator recommended approval for member: {$member['full_name']}",
                ['coordinator_recommendation' => null],
                ['coordinator_recommendation' => 'approved']
            );

            return redirect()->back()->with('success', 'Rekomendasi persetujuan berhasil dikirim ke admin');
        }

        return redirect()->back()->with('error', 'Gagal mengirim rekomendasi');
    }

    /**
     * Reject member (coordinator recommendation)
     */
    public function reject($memberId = null)
    {
        if (!$this->request->is('post')) {
            return redirect()->back()->with('error', 'Invalid request method');
        }

        if (!$memberId) {
            return redirect()->back()->with('error', 'Member ID tidak valid');
        }

        $userId = session()->get('user_id');

        // Get coordinator's assigned regions
        $assignedRegions = $this->coordinatorRegionModel->getCoordinatorRegions($userId);
        $regionCodes = array_column($assignedRegions, 'region_code');

        // Get member
        $member = $this->memberModel->find($memberId);

        if (!$member) {
            return redirect()->back()->with('error', 'Member tidak ditemukan');
        }

        // Check if member is in coordinator's region
        if (!in_array($member['region_code'], $regionCodes)) {
            return redirect()->back()->with('error', 'Member tidak dalam wilayah Anda');
        }

        $reason = $this->request->getPost('reason') ?? '';

        if (empty($reason)) {
            return redirect()->back()->with('error', 'Alasan penolakan harus diisi');
        }

        $updateData = [
            'coordinator_recommendation' => 'rejected',
            'coordinator_notes' => $reason,
            'coordinator_reviewed_at' => date('Y-m-d H:i:s'),
            'coordinator_reviewed_by' => $userId,
        ];

        if ($this->memberModel->update($memberId, $updateData)) {
            // Log the action
            $this->auditModel->log(
                'update',
                'sp_members',
                $memberId,
                "Coordinator recommended rejection for member: {$member['full_name']}",
                ['coordinator_recommendation' => null],
                ['coordinator_recommendation' => 'rejected']
            );

            return redirect()->back()->with('success', 'Rekomendasi penolakan berhasil dikirim ke admin');
        }

        return redirect()->back()->with('error', 'Gagal mengirim rekomendasi');
    }
}
