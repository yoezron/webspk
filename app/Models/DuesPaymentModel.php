<?php

namespace App\Models;

use CodeIgniter\Model;

class DuesPaymentModel extends Model
{
    protected $table            = 'sp_dues_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'member_id',
        'payment_type',
        'amount',
        'payment_period',
        'payment_month',
        'payment_year',
        'payment_date',
        'payment_method',
        'payment_proof',
        'payment_reference',
        'status',
        'verified_by',
        'verified_at',
        'verification_notes',
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
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get payments by member ID
     */
    public function getPaymentsByMember(int $memberId, int $limit = 0, int $offset = 0)
    {
        $builder = $this->where('member_id', $memberId)
            ->orderBy('payment_date', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }

    /**
     * Get pending payment verifications
     */
    public function getPendingVerifications(int $limit = 0, int $offset = 0)
    {
        $builder = $this->select('sp_dues_payments.*, sp_members.full_name, sp_members.member_number, sp_members.email')
            ->join('sp_members', 'sp_members.id = sp_dues_payments.member_id')
            ->where('sp_dues_payments.status', 'pending')
            ->orderBy('sp_dues_payments.created_at', 'ASC');

        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        return $builder->findAll();
    }

    /**
     * Get total amount paid by member
     */
    public function getTotalPaidByMember(int $memberId): float
    {
        $result = $this->selectSum('amount')
            ->where('member_id', $memberId)
            ->where('status', 'verified')
            ->first();

        return $result['amount'] ?? 0;
    }

    /**
     * Check if payment exists for specific period
     */
    public function hasPaymentForPeriod(int $memberId, int $month, int $year): bool
    {
        $payment = $this->where('member_id', $memberId)
            ->where('payment_month', $month)
            ->where('payment_year', $year)
            ->whereIn('status', ['pending', 'verified'])
            ->first();

        return !empty($payment);
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStats(): array
    {
        $db = \Config\Database::connect();

        $totalPaid = $db->query("
            SELECT SUM(amount) as total
            FROM sp_dues_payments
            WHERE status = 'verified'
        ")->getRow()->total ?? 0;

        $totalPending = $db->query("
            SELECT COUNT(*) as total
            FROM sp_dues_payments
            WHERE status = 'pending'
        ")->getRow()->total ?? 0;

        $thisMonthPaid = $db->query("
            SELECT SUM(amount) as total
            FROM sp_dues_payments
            WHERE status = 'verified'
            AND MONTH(payment_date) = MONTH(CURRENT_DATE)
            AND YEAR(payment_date) = YEAR(CURRENT_DATE)
        ")->getRow()->total ?? 0;

        return [
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending,
            'this_month_paid' => $thisMonthPaid,
        ];
    }
}
