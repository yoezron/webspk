<?php

namespace App\Models;

use CodeIgniter\Model;

class CmsContactMessageModel extends Model
{
    protected $table = 'cms_contact_messages';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'name',
        'email',
        'subject',
        'message',
        'status',
        'assigned_to',
        'admin_reply',
        'replied_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[150]',
        'email' => 'required|valid_email',
        'message' => 'required|min_length[10]',
        'status' => 'in_list[new,in_progress,closed]',
    ];

    /**
     * Get messages by status
     */
    public function getMessagesByStatus($status = 'new', $perPage = 20)
    {
        return $this->where('status', $status)
                    ->orderBy('created_at', 'DESC')
                    ->paginate($perPage);
    }

    /**
     * Get new messages count
     */
    public function getNewMessagesCount()
    {
        return $this->where('status', 'new')->countAllResults();
    }

    /**
     * Get all messages (with pagination)
     */
    public function getAllMessages($perPage = 20)
    {
        return $this->select('cms_contact_messages.*, sp_members.full_name as assigned_to_name')
                    ->join('sp_members', 'sp_members.id = cms_contact_messages.assigned_to', 'left')
                    ->orderBy('cms_contact_messages.created_at', 'DESC')
                    ->paginate($perPage);
    }

    /**
     * Assign message to admin
     */
    public function assignMessage($id, $adminId)
    {
        return $this->update($id, [
            'assigned_to' => $adminId,
            'status' => 'in_progress'
        ]);
    }

    /**
     * Reply to message
     */
    public function replyMessage($id, $reply, $adminId)
    {
        $data = [
            'admin_reply' => $reply,
            'replied_at' => date('Y-m-d H:i:s'),
            'status' => 'closed',
            'assigned_to' => $adminId
        ];

        return $this->update($id, $data);
    }

    /**
     * Close message
     */
    public function closeMessage($id)
    {
        return $this->update($id, ['status' => 'closed']);
    }
}
