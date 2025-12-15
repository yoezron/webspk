<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'user_email',
        'user_role',
        'action',
        'action_type',
        'target_type',
        'target_id',
        'target_identifier',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'request_method',
        'request_uri',
        'status',
        'error_message',
        'created_at',
    ];

    // Dates
    protected $useTimestamps = false; // We'll handle created_at manually
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';

    /**
     * Log an action
     *
     * @param array $data
     * @return int|bool Insert ID or false on failure
     */
    public function logAction(array $data)
    {
        // Auto-fill common data if not provided
        if (!isset($data['user_id'])) {
            $data['user_id'] = session()->get('user_id');
        }
        if (!isset($data['user_email'])) {
            $data['user_email'] = session()->get('user_email');
        }
        if (!isset($data['user_role'])) {
            $data['user_role'] = session()->get('user_role');
        }
        if (!isset($data['ip_address'])) {
            $data['ip_address'] = \Config\Services::request()->getIPAddress();
        }
        if (!isset($data['user_agent'])) {
            $data['user_agent'] = \Config\Services::request()->getUserAgent()->getAgentString();
        }
        if (!isset($data['request_method'])) {
            $data['request_method'] = \Config\Services::request()->getMethod();
        }
        if (!isset($data['request_uri'])) {
            $data['request_uri'] = \Config\Services::request()->getUri()->getPath();
        }
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        if (!isset($data['status'])) {
            $data['status'] = 'success';
        }

        // Convert arrays/objects to JSON for old_values and new_values
        if (isset($data['old_values']) && is_array($data['old_values'])) {
            $data['old_values'] = json_encode($data['old_values']);
        }
        if (isset($data['new_values']) && is_array($data['new_values'])) {
            $data['new_values'] = json_encode($data['new_values']);
        }

        try {
            return $this->insert($data);
        } catch (\Exception $e) {
            log_message('error', 'Failed to create audit log: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get logs with pagination and filters
     *
     * @param array $filters
     * @param int $perPage
     * @return array
     */
    public function getLogsPaginated(array $filters = [], int $perPage = 50)
    {
        $builder = $this->builder();

        // Apply filters
        if (!empty($filters['user_id'])) {
            $builder->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['action_type'])) {
            $builder->where('action_type', $filters['action_type']);
        }
        if (!empty($filters['target_type'])) {
            $builder->where('target_type', $filters['target_type']);
        }
        if (!empty($filters['target_id'])) {
            $builder->where('target_id', $filters['target_id']);
        }
        if (!empty($filters['date_from'])) {
            $builder->where('created_at >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $builder->where('created_at <=', $filters['date_to']);
        }
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('description', $filters['search'])
                ->orLike('user_email', $filters['search'])
                ->orLike('target_identifier', $filters['search'])
                ->groupEnd();
        }

        return $builder->orderBy('created_at', 'DESC')->paginate($perPage);
    }

    /**
     * Get logs for a specific user
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getUserLogs(int $userId, int $limit = 100)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    /**
     * Get logs for a specific target
     *
     * @param string $targetType
     * @param int $targetId
     * @param int $limit
     * @return array
     */
    public function getTargetLogs(string $targetType, int $targetId, int $limit = 50)
    {
        return $this->where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    /**
     * Get recent logs
     *
     * @param int $limit
     * @return array
     */
    public function getRecentLogs(int $limit = 20)
    {
        return $this->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->find();
    }

    /**
     * Get log statistics
     *
     * @param string $period (today, week, month, year)
     * @return array
     */
    public function getLogStatistics(string $period = 'today')
    {
        $dateCondition = match ($period) {
            'today' => 'DATE(created_at) = CURDATE()',
            'week' => 'created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)',
            'month' => 'created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)',
            'year' => 'created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)',
            default => 'DATE(created_at) = CURDATE()',
        };

        $db = \Config\Database::connect();

        // Total logs
        $total = $db->table($this->table)
            ->where($dateCondition)
            ->countAllResults();

        // By action type
        $byActionType = $db->table($this->table)
            ->select('action_type, COUNT(*) as count')
            ->where($dateCondition)
            ->groupBy('action_type')
            ->get()
            ->getResultArray();

        // By status
        $byStatus = $db->table($this->table)
            ->select('status, COUNT(*) as count')
            ->where($dateCondition)
            ->groupBy('status')
            ->get()
            ->getResultArray();

        // Most active users
        $mostActiveUsers = $db->table($this->table)
            ->select('user_email, COUNT(*) as action_count')
            ->where($dateCondition)
            ->whereNotNull('user_email')
            ->groupBy('user_email')
            ->orderBy('action_count', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return [
            'total' => $total,
            'by_action_type' => $byActionType,
            'by_status' => $byStatus,
            'most_active_users' => $mostActiveUsers,
            'period' => $period,
        ];
    }

    /**
     * Delete old logs (for cleanup)
     *
     * @param int $daysOld
     * @return int Number of deleted rows
     */
    public function deleteOldLogs(int $daysOld = 365)
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$daysOld} days"));
        return $this->where('created_at <', $date)->delete();
    }
}
