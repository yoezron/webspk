<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\MemberModel;

class AuditLog extends BaseController
{
    protected $auditModel;
    protected $memberModel;
    protected $db;

    public function __construct()
    {
        $this->auditModel = new AuditLogModel();
        $this->memberModel = new MemberModel();
        $this->db = \Config\Database::connect();
        helper(['form', 'url', 'settings']);
    }

    /**
     * Audit Log Viewer
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $actionFilter = $this->request->getGet('action');
        $targetFilter = $this->request->getGet('target');
        $userFilter = $this->request->getGet('user');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        $page = $this->request->getGet('page') ?? 1;
        $perPage = setting('pagination_per_page', 20);

        // Build query
        $builder = $this->auditModel
            ->select('audit_logs.*, sp_members.full_name as user_name')
            ->join('sp_members', 'sp_members.id = audit_logs.user_id', 'left')
            ->orderBy('audit_logs.created_at', 'DESC');

        // Apply filters
        if ($search) {
            $builder->like('audit_logs.description', $search);
        }

        if ($actionFilter) {
            $builder->where('audit_logs.action_type', $actionFilter);
        }

        if ($targetFilter) {
            $builder->where('audit_logs.target_type', $targetFilter);
        }

        if ($userFilter) {
            $builder->where('audit_logs.user_id', $userFilter);
        }

        if ($dateFrom) {
            $builder->where('audit_logs.created_at >=', $dateFrom . ' 00:00:00');
        }

        if ($dateTo) {
            $builder->where('audit_logs.created_at <=', $dateTo . ' 23:59:59');
        }

        $logs = $builder->paginate($perPage);

        // Get unique action types and target types for filters
        $actionTypes = $this->auditModel->select('action_type')
            ->distinct()
            ->orderBy('action_type')
            ->findColumn('action_type');

        $targetTypes = $this->auditModel->select('target_type')
            ->distinct()
            ->orderBy('target_type')
            ->findColumn('target_type');

        // Get recent active users for filter
        $recentUsers = $this->db->table('audit_logs')
            ->select('DISTINCT user_id, sp_members.full_name')
            ->join('sp_members', 'sp_members.id = audit_logs.user_id', 'left')
            ->where('audit_logs.created_at >=', date('Y-m-d', strtotime('-30 days')))
            ->orderBy('audit_logs.created_at', 'DESC')
            ->limit(50)
            ->get()
            ->getResultArray();

        // Get statistics
        $stats = [
            'total_logs' => $this->auditModel->countAllResults(false),
            'logs_today' => $this->auditModel
                ->where('DATE(created_at)', date('Y-m-d'))
                ->countAllResults(false),
            'logs_this_week' => $this->auditModel
                ->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
                ->countAllResults(false),
            'unique_users_today' => $this->db->table('audit_logs')
                ->select('DISTINCT user_id')
                ->where('DATE(created_at)', date('Y-m-d'))
                ->countAllResults(),
        ];

        $data = [
            'title' => 'Audit Log Viewer',
            'logs' => $logs,
            'pager' => $this->auditModel->pager,
            'stats' => $stats,
            'action_types' => $actionTypes,
            'target_types' => $targetTypes,
            'recent_users' => $recentUsers,
            'filters' => [
                'search' => $search,
                'action' => $actionFilter,
                'target' => $targetFilter,
                'user' => $userFilter,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ];

        return view('admin/audit/index', $data);
    }

    /**
     * View audit log details
     */
    public function view($id = null)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'Log ID tidak valid');
        }

        $log = $this->auditModel
            ->select('audit_logs.*, sp_members.full_name as user_name, sp_members.email as user_email')
            ->join('sp_members', 'sp_members.id = audit_logs.user_id', 'left')
            ->find($id);

        if (!$log) {
            return redirect()->back()->with('error', 'Log tidak ditemukan');
        }

        // Decode JSON fields
        $log['old_values_decoded'] = $log['old_values'] ? json_decode($log['old_values'], true) : null;
        $log['new_values_decoded'] = $log['new_values'] ? json_decode($log['new_values'], true) : null;

        // Get related logs (same target, within 1 hour)
        $relatedLogs = $this->auditModel
            ->select('audit_logs.*, sp_members.full_name as user_name')
            ->join('sp_members', 'sp_members.id = audit_logs.user_id', 'left')
            ->where('audit_logs.target_type', $log['target_type'])
            ->where('audit_logs.target_id', $log['target_id'])
            ->where('audit_logs.id !=', $id)
            ->where('audit_logs.created_at >=', date('Y-m-d H:i:s', strtotime($log['created_at'] . ' -1 hour')))
            ->where('audit_logs.created_at <=', date('Y-m-d H:i:s', strtotime($log['created_at'] . ' +1 hour')))
            ->orderBy('audit_logs.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'title' => 'Detail Audit Log',
            'log' => $log,
            'related_logs' => $relatedLogs,
        ];

        return view('admin/audit/view', $data);
    }

    /**
     * Export audit logs to CSV
     */
    public function export()
    {
        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('-30 days'));
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-d');

        $logs = $this->auditModel
            ->select('audit_logs.*, sp_members.full_name as user_name, sp_members.email as user_email')
            ->join('sp_members', 'sp_members.id = audit_logs.user_id', 'left')
            ->where('audit_logs.created_at >=', $dateFrom . ' 00:00:00')
            ->where('audit_logs.created_at <=', $dateTo . ' 23:59:59')
            ->orderBy('audit_logs.created_at', 'DESC')
            ->findAll();

        // Prepare CSV
        $filename = 'audit_log_' . date('Y-m-d_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV Headers
        fputcsv($output, [
            'ID',
            'Waktu',
            'User',
            'Email User',
            'Aksi',
            'Target Type',
            'Target ID',
            'Deskripsi',
            'IP Address',
            'User Agent',
        ]);

        // CSV Data
        foreach ($logs as $log) {
            fputcsv($output, [
                $log['id'],
                $log['created_at'],
                $log['user_name'] ?? 'System',
                $log['user_email'] ?? '-',
                $log['action_type'],
                $log['target_type'],
                $log['target_id'] ?? '-',
                $log['description'],
                $log['ip_address'] ?? '-',
                $log['user_agent'] ?? '-',
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Clean old audit logs
     */
    public function clean()
    {
        if (!$this->request->is('post')) {
            return redirect()->back()->with('error', 'Invalid request method');
        }

        $retentionDays = setting('audit_log_retention_days', 365);
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$retentionDays} days"));

        $deleted = $this->auditModel
            ->where('created_at <', $cutoffDate)
            ->delete();

        if ($deleted) {
            // Log the cleanup action
            $this->auditModel->log(
                'delete',
                'audit_logs',
                null,
                "Cleaned audit logs older than {$retentionDays} days ({$deleted} records deleted)"
            );

            return redirect()->back()->with('success', "{$deleted} log lama berhasil dihapus");
        }

        return redirect()->back()->with('info', 'Tidak ada log lama yang perlu dihapus');
    }

    /**
     * Get activity statistics (AJAX)
     */
    public function statistics()
    {
        $period = $this->request->getGet('period') ?? '7days';

        $dateFrom = match ($period) {
            '24hours' => date('Y-m-d H:i:s', strtotime('-24 hours')),
            '7days' => date('Y-m-d H:i:s', strtotime('-7 days')),
            '30days' => date('Y-m-d H:i:s', strtotime('-30 days')),
            '90days' => date('Y-m-d H:i:s', strtotime('-90 days')),
            default => date('Y-m-d H:i:s', strtotime('-7 days')),
        };

        // Activity by action type
        $activityByAction = $this->db->table('audit_logs')
            ->select('action_type, COUNT(*) as count')
            ->where('created_at >=', $dateFrom)
            ->groupBy('action_type')
            ->orderBy('count', 'DESC')
            ->get()
            ->getResultArray();

        // Activity by target type
        $activityByTarget = $this->db->table('audit_logs')
            ->select('target_type, COUNT(*) as count')
            ->where('created_at >=', $dateFrom)
            ->groupBy('target_type')
            ->orderBy('count', 'DESC')
            ->get()
            ->getResultArray();

        // Top active users
        $topUsers = $this->db->table('audit_logs')
            ->select('audit_logs.user_id, sp_members.full_name, COUNT(*) as activity_count')
            ->join('sp_members', 'sp_members.id = audit_logs.user_id', 'left')
            ->where('audit_logs.created_at >=', $dateFrom)
            ->groupBy('audit_logs.user_id')
            ->orderBy('activity_count', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'activity_by_action' => $activityByAction,
            'activity_by_target' => $activityByTarget,
            'top_users' => $topUsers,
        ]);
    }
}
