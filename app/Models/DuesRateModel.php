<?php

namespace App\Models;

use CodeIgniter\Model;

class DuesRateModel extends Model
{
    protected $table = 'dues_rates';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'rate_name',
        'rate_type',
        'amount',
        'member_category',
        'region_code',
        'effective_from',
        'effective_to',
        'is_active',
        'description',
        'created_by',
        'updated_by',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'rate_name' => 'required|max_length[100]',
        'rate_type' => 'required|in_list[monthly,yearly,one_time]',
        'amount' => 'required|decimal|greater_than[0]',
        'effective_from' => 'required|valid_date',
    ];

    protected $validationMessages = [
        'rate_name' => [
            'required' => 'Nama tarif harus diisi',
        ],
        'amount' => [
            'required' => 'Jumlah tarif harus diisi',
            'greater_than' => 'Jumlah tarif harus lebih dari 0',
        ],
    ];

    /**
     * Get all active rates
     */
    public function getActiveRates(): array
    {
        return $this->where('is_active', 1)
            ->where('effective_from <=', date('Y-m-d'))
            ->groupStart()
                ->where('effective_to >=', date('Y-m-d'))
                ->orWhere('effective_to', null)
            ->groupEnd()
            ->orderBy('rate_type', 'ASC')
            ->orderBy('amount', 'ASC')
            ->findAll();
    }

    /**
     * Get applicable rate for a member
     */
    public function getApplicableRate(string $memberCategory = null, string $regionCode = null, string $rateType = 'monthly'): ?array
    {
        $builder = $this->where('is_active', 1)
            ->where('rate_type', $rateType)
            ->where('effective_from <=', date('Y-m-d'))
            ->groupStart()
                ->where('effective_to >=', date('Y-m-d'))
                ->orWhere('effective_to', null)
            ->groupEnd();

        // Priority: Specific category + region > Specific category > Specific region > General
        $rates = $builder->findAll();

        // Filter by category and region with priority
        $bestMatch = null;
        $bestMatchScore = -1;

        foreach ($rates as $rate) {
            $score = 0;

            // Check category match
            if ($rate['member_category'] === $memberCategory) {
                $score += 2;
            } elseif ($rate['member_category'] !== null) {
                continue; // Skip if category specified but doesn't match
            }

            // Check region match
            if ($rate['region_code'] === $regionCode) {
                $score += 1;
            } elseif ($rate['region_code'] !== null) {
                continue; // Skip if region specified but doesn't match
            }

            // Keep the highest scoring match
            if ($score > $bestMatchScore) {
                $bestMatch = $rate;
                $bestMatchScore = $score;
            }
        }

        return $bestMatch;
    }

    /**
     * Get rates by type
     */
    public function getRatesByType(string $type): array
    {
        return $this->where('rate_type', $type)
            ->where('is_active', 1)
            ->orderBy('amount', 'ASC')
            ->findAll();
    }

    /**
     * Get rate history
     */
    public function getRateHistory(int $rateId): array
    {
        return $this->db->table('dues_rate_history')
            ->select('dues_rate_history.*, sp_members.full_name as changed_by_name')
            ->join('sp_members', 'sp_members.id = dues_rate_history.changed_by', 'left')
            ->where('rate_id', $rateId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Log rate change
     */
    public function logChange(int $rateId, string $action, ?float $oldAmount = null, ?float $newAmount = null, ?string $reason = null): bool
    {
        $data = [
            'rate_id' => $rateId,
            'action' => $action,
            'old_amount' => $oldAmount,
            'new_amount' => $newAmount,
            'changed_by' => session()->get('user_id'),
            'change_reason' => $reason,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return $this->db->table('dues_rate_history')->insert($data);
    }

    /**
     * Toggle rate status
     */
    public function toggleStatus(int $rateId): bool
    {
        $rate = $this->find($rateId);

        if (!$rate) {
            return false;
        }

        $newStatus = $rate['is_active'] == 1 ? 0 : 1;

        $this->update($rateId, [
            'is_active' => $newStatus,
            'updated_by' => session()->get('user_id'),
        ]);

        $action = $newStatus == 1 ? 'activated' : 'deactivated';
        $this->logChange($rateId, $action, null, null, 'Status changed via toggle');

        return true;
    }

    /**
     * Get rates with statistics
     */
    public function getRatesWithStats(): array
    {
        $rates = $this->orderBy('rate_type', 'ASC')
            ->orderBy('effective_from', 'DESC')
            ->findAll();

        foreach ($rates as &$rate) {
            // Count members using this rate (if applicable)
            $rate['usage_count'] = 0; // Placeholder - implement based on payment tracking

            // Get last change
            $lastChange = $this->db->table('dues_rate_history')
                ->where('rate_id', $rate['id'])
                ->orderBy('created_at', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();

            $rate['last_change'] = $lastChange;
        }

        return $rates;
    }

    /**
     * Duplicate rate (for creating new version)
     */
    public function duplicateRate(int $rateId): ?int
    {
        $rate = $this->find($rateId);

        if (!$rate) {
            return null;
        }

        unset($rate['id']);
        $rate['rate_name'] .= ' (Copy)';
        $rate['is_active'] = 0;
        $rate['created_by'] = session()->get('user_id');
        $rate['created_at'] = date('Y-m-d H:i:s');

        if ($this->insert($rate)) {
            return $this->getInsertID();
        }

        return null;
    }
}
