<?php

use App\Models\AuditLogModel;

if (!function_exists('audit_log')) {
    /**
     * Quick audit logging helper
     *
     * @param string $action - Action performed (e.g., 'member.approve', 'payment.verify')
     * @param string $actionType - Type: create, read, update, delete, approve, reject, verify, suspend, activate, login, logout
     * @param string|null $targetType - Type of target (member, payment, role, etc.)
     * @param int|null $targetId - ID of target
     * @param string|null $targetIdentifier - Human-readable identifier
     * @param string|null $description - Description of action
     * @param array|null $oldValues - Old values before change
     * @param array|null $newValues - New values after change
     * @param string $status - success, failed, error
     * @return bool
     */
    function audit_log(
        string $action,
        string $actionType,
        ?string $targetType = null,
        ?int $targetId = null,
        ?string $targetIdentifier = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        string $status = 'success'
    ): bool {
        try {
            $auditModel = new AuditLogModel();

            $data = [
                'action' => $action,
                'action_type' => $actionType,
                'status' => $status,
            ];

            if ($targetType !== null) {
                $data['target_type'] = $targetType;
            }
            if ($targetId !== null) {
                $data['target_id'] = $targetId;
            }
            if ($targetIdentifier !== null) {
                $data['target_identifier'] = $targetIdentifier;
            }
            if ($description !== null) {
                $data['description'] = $description;
            }
            if ($oldValues !== null) {
                $data['old_values'] = $oldValues;
            }
            if ($newValues !== null) {
                $data['new_values'] = $newValues;
            }

            return $auditModel->logAction($data) !== false;
        } catch (\Exception $e) {
            log_message('error', 'Audit log failed: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('audit_log_member_action')) {
    /**
     * Log member-related action
     *
     * @param string $actionType
     * @param int $memberId
     * @param string $memberIdentifier
     * @param string $description
     * @param array|null $oldValues
     * @param array|null $newValues
     * @return bool
     */
    function audit_log_member_action(
        string $actionType,
        int $memberId,
        string $memberIdentifier,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null
    ): bool {
        return audit_log(
            "member.{$actionType}",
            $actionType,
            'member',
            $memberId,
            $memberIdentifier,
            $description,
            $oldValues,
            $newValues
        );
    }
}

if (!function_exists('audit_log_payment_action')) {
    /**
     * Log payment-related action
     *
     * @param string $actionType
     * @param int $paymentId
     * @param string $paymentIdentifier
     * @param string $description
     * @param array|null $oldValues
     * @param array|null $newValues
     * @return bool
     */
    function audit_log_payment_action(
        string $actionType,
        int $paymentId,
        string $paymentIdentifier,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null
    ): bool {
        return audit_log(
            "payment.{$actionType}",
            $actionType,
            'payment',
            $paymentId,
            $paymentIdentifier,
            $description,
            $oldValues,
            $newValues
        );
    }
}

if (!function_exists('audit_log_login')) {
    /**
     * Log user login
     *
     * @param int $userId
     * @param string $userEmail
     * @param string $status
     * @return bool
     */
    function audit_log_login(int $userId, string $userEmail, string $status = 'success'): bool
    {
        return audit_log(
            'auth.login',
            'login',
            'member',
            $userId,
            $userEmail,
            "User {$userEmail} logged in",
            null,
            null,
            $status
        );
    }
}

if (!function_exists('audit_log_logout')) {
    /**
     * Log user logout
     *
     * @param int $userId
     * @param string $userEmail
     * @return bool
     */
    function audit_log_logout(int $userId, string $userEmail): bool
    {
        return audit_log(
            'auth.logout',
            'logout',
            'member',
            $userId,
            $userEmail,
            "User {$userEmail} logged out"
        );
    }
}

if (!function_exists('audit_log_failed_login')) {
    /**
     * Log failed login attempt
     *
     * @param string $email
     * @param string $reason
     * @return bool
     */
    function audit_log_failed_login(string $email, string $reason): bool
    {
        return audit_log(
            'auth.login_failed',
            'login',
            null,
            null,
            $email,
            "Failed login attempt for {$email}: {$reason}",
            null,
            null,
            'failed'
        );
    }
}

if (!function_exists('audit_log_rbac_change')) {
    /**
     * Log RBAC changes (role/permission assignments)
     *
     * @param string $actionType
     * @param string $description
     * @param array|null $oldValues
     * @param array|null $newValues
     * @return bool
     */
    function audit_log_rbac_change(
        string $actionType,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null
    ): bool {
        return audit_log(
            "rbac.{$actionType}",
            $actionType,
            'rbac',
            null,
            null,
            $description,
            $oldValues,
            $newValues
        );
    }
}

if (!function_exists('get_recent_audit_logs')) {
    /**
     * Get recent audit logs
     *
     * @param int $limit
     * @return array
     */
    function get_recent_audit_logs(int $limit = 20): array
    {
        $auditModel = new AuditLogModel();
        return $auditModel->getRecentLogs($limit);
    }
}

if (!function_exists('get_user_audit_logs')) {
    /**
     * Get audit logs for specific user
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    function get_user_audit_logs(int $userId, int $limit = 100): array
    {
        $auditModel = new AuditLogModel();
        return $auditModel->getUserLogs($userId, $limit);
    }
}

if (!function_exists('get_target_audit_logs')) {
    /**
     * Get audit logs for specific target
     *
     * @param string $targetType
     * @param int $targetId
     * @param int $limit
     * @return array
     */
    function get_target_audit_logs(string $targetType, int $targetId, int $limit = 50): array
    {
        $auditModel = new AuditLogModel();
        return $auditModel->getTargetLogs($targetType, $targetId, $limit);
    }
}
