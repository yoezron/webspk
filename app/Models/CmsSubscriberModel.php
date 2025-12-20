<?php

namespace App\Models;

use CodeIgniter\Model;

class CmsSubscriberModel extends Model
{
    protected $table = 'cms_subscribers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'email',
        'status',
        'token_hash',
        'verified_at'
    ];

    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[cms_subscribers.email,id,{id}]',
        'status' => 'in_list[pending,active,unsubscribed]',
    ];

    /**
     * Get active subscribers
     */
    public function getActiveSubscribers()
    {
        return $this->where('status', 'active')
                    ->orderBy('verified_at', 'DESC')
                    ->findAll();
    }

    /**
     * Subscribe email
     */
    public function subscribe($email, $token)
    {
        $data = [
            'email' => $email,
            'status' => 'pending',
            'token_hash' => hash('sha256', $token),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->insert($data);
    }

    /**
     * Verify subscriber
     */
    public function verifyByToken($token)
    {
        $tokenHash = hash('sha256', $token);
        $subscriber = $this->where('token_hash', $tokenHash)
                          ->where('status', 'pending')
                          ->first();

        if (!$subscriber) {
            return false;
        }

        return $this->update($subscriber['id'], [
            'status' => 'active',
            'verified_at' => date('Y-m-d H:i:s'),
            'token_hash' => null
        ]);
    }

    /**
     * Unsubscribe
     */
    public function unsubscribe($email)
    {
        $subscriber = $this->where('email', $email)->first();

        if (!$subscriber) {
            return false;
        }

        return $this->update($subscriber['id'], [
            'status' => 'unsubscribed'
        ]);
    }

    /**
     * Get subscribers count
     */
    public function getActiveCount()
    {
        return $this->where('status', 'active')->countAllResults();
    }
}
