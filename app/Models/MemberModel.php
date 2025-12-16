<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $table            = 'sp_members';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid',
        'member_number',
        'email',
        'password_hash',
        'role',
        'membership_status',
        'onboarding_state',
        'account_status',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'last_user_agent',
        'last_status_change_at',
        'failed_login_attempts',
        'locked_until',
        'password_changed_at',
        'reset_token_hash',
        'reset_token_expires_at',
        'remember_token_hash',
        'remember_token_expires_at',
        'full_name',
        'gender',
        'birth_place',
        'birth_date',
        'identity_number',
        'phone_number',
        'alt_phone_number',
        'address',
        'province',
        'city',
        'district',
        'postal_code',
        'region_code',
        'emergency_contact_name',
        'emergency_contact_relation',
        'emergency_contact_phone',
        'university_name',
        'campus_location',
        'faculty',
        'department',
        'work_unit',
        'employee_id_number',
        'lecturer_id_number',
        'academic_rank',
        'employment_status',
        'work_start_date',
        'gross_salary',
        'salary_range',
        'functional_allowance',
        'structural_allowance',
        'other_allowances',
        'dues_rate_type',
        'dues_rate_id',
        'monthly_dues_amount',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'npwp_number',
        'bpjs_tk_number',
        'bpjs_kes_number',
        'education_level',
        'graduation_year',
        'institution_name',
        'field_of_study',
        'certifications',
        'languages_spoken',
        'skills',
        'registration_payment_proof',
        'registration_payment_date',
        'registration_verified_by',
        'registration_verified_at',
        'registration_notes',
        'approval_date',
        'approved_by',
        'rejection_reason',
        'rejected_by',
        'last_dues_payment_date',
        'total_arrears',
        'arrears_months',
        'profile_photo',
        'id_card_photo',
        'family_card_photo',
        'sk_pengangkatan_photo',
        'agreement_accepted_at',
        'privacy_accepted_at',
        'notes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword', 'generateUUID'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Hash password before insert/update
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            unset($data['data']['password']);
            $data['data']['password_changed_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Generate UUID before insert
     */
    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['uuid'])) {
            $data['data']['uuid'] = $this->generateUUIDv4();
        }
        return $data;
    }

    /**
     * Generate UUID v4
     */
    private function generateUUIDv4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Find member by email
     */
    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Find member by UUID
     */
    public function findByUUID(string $uuid)
    {
        return $this->where('uuid', $uuid)->first();
    }

    /**
     * Find member by member number
     */
    public function findByMemberNumber(string $memberNumber)
    {
        return $this->where('member_number', $memberNumber)->first();
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Check if account is locked
     */
    public function isAccountLocked(array $member): bool
    {
        if (empty($member['locked_until'])) {
            return false;
        }

        $lockedUntil = strtotime($member['locked_until']);
        return $lockedUntil > time();
    }

    /**
     * Increment failed login attempts
     */
    public function incrementFailedAttempts(int $memberId)
    {
        $member = $this->find($memberId);
        $attempts = ($member['failed_login_attempts'] ?? 0) + 1;

        $updateData = ['failed_login_attempts' => $attempts];

        // Lock account after 5 failed attempts for 30 minutes
        if ($attempts >= 5) {
            $updateData['locked_until'] = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        }

        return $this->update($memberId, $updateData);
    }

    /**
     * Reset failed login attempts
     */
    public function resetFailedAttempts(int $memberId)
    {
        return $this->update($memberId, [
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /**
     * Update last login info
     */
    public function updateLastLogin(int $memberId, string $ip, string $userAgent)
    {
        return $this->update($memberId, [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $ip,
            'last_user_agent' => $userAgent,
        ]);
    }

    /**
     * Generate and store reset token
     */
    public function generateResetToken(int $memberId): string
    {
        $token = bin2hex(random_bytes(32));
        $hash = hash('sha256', $token);

        $this->update($memberId, [
            'reset_token_hash' => $hash,
            'reset_token_expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
        ]);

        return $token;
    }

    /**
     * Find member by reset token
     */
    public function findByResetToken(string $token)
    {
        $hash = hash('sha256', $token);

        return $this->where('reset_token_hash', $hash)
            ->where('reset_token_expires_at >', date('Y-m-d H:i:s'))
            ->first();
    }

    /**
     * Clear reset token
     */
    public function clearResetToken(int $memberId)
    {
        return $this->update($memberId, [
            'reset_token_hash' => null,
            'reset_token_expires_at' => null,
        ]);
    }

    /**
     * Generate and store remember token (for "Remember Me" functionality)
     *
     * @param int $memberId Member ID
     * @return string Plain token to be stored in cookie
     */
    public function generateRememberToken(int $memberId): string
    {
        // Generate cryptographically secure random token
        $token = bin2hex(random_bytes(32));
        $hash = hash('sha256', $token);

        // Store hashed token with 30 days expiry
        $this->update($memberId, [
            'remember_token_hash' => $hash,
            'remember_token_expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
        ]);

        return $token;
    }

    /**
     * Find member by remember token
     *
     * @param string $token Plain token from cookie
     * @return array|null Member data if token valid, null otherwise
     */
    public function findByRememberToken(string $token): ?array
    {
        $hash = hash('sha256', $token);

        return $this->where('remember_token_hash', $hash)
            ->where('remember_token_expires_at >', date('Y-m-d H:i:s'))
            ->where('account_status', 'active') // Only active accounts
            ->first();
    }

    /**
     * Clear remember token (on logout)
     *
     * @param int $memberId Member ID
     * @return bool Success status
     */
    public function clearRememberToken(int $memberId): bool
    {
        return $this->update($memberId, [
            'remember_token_hash' => null,
            'remember_token_expires_at' => null,
        ]);
    }

    /**
     * Mark status change timestamp
     * This will invalidate active sessions for this member
     *
     * @param int $memberId Member ID
     * @return bool Success status
     */
    public function markStatusChanged(int $memberId): bool
    {
        return $this->update($memberId, [
            'last_status_change_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Check if member data changed after session was created
     *
     * @param int $memberId Member ID
     * @param string $sessionCreatedAt Session creation timestamp
     * @return bool True if data changed, false otherwise
     */
    public function hasStatusChangedSince(int $memberId, string $sessionCreatedAt): bool
    {
        $member = $this->find($memberId);

        if (!$member || empty($member['last_status_change_at'])) {
            return false;
        }

        return strtotime($member['last_status_change_at']) > strtotime($sessionCreatedAt);
    }

    /**
     * Get members by role
     */
    public function getMembersByRole(string $role, int $limit = 0, int $offset = 0)
    {
        $builder = $this->where('role', $role);

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }

    /**
     * Get active members
     */
    public function getActiveMembers(int $limit = 0, int $offset = 0)
    {
        $builder = $this->where('membership_status', 'active')
            ->where('account_status', 'active');

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }

    /**
     * Get pending approvals
     */
    public function getPendingApprovals(int $limit = 0, int $offset = 0)
    {
        $builder = $this->where('onboarding_state', 'email_verified')
            ->where('account_status', 'pending');

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }
}
