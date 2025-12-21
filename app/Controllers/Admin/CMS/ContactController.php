<?php

namespace App\Controllers\Admin\CMS;

use App\Controllers\BaseController;
use App\Models\CmsContactMessageModel;
use App\Models\AuditLogModel;

class ContactController extends BaseController
{
    protected $contactModel;
    protected $auditLog;

    public function __construct()
    {
        $this->contactModel = new CmsContactMessageModel();
        $this->auditLog = new AuditLogModel();
    }

    /**
     * Contact messages inbox
     */
    public function index()
    {
        $status = $this->request->getGet('status'); // new, read, replied, archived
        $perPage = 30;

        $builder = $this->contactModel
            ->select('cms_contact_messages.*, sp_members.full_name as assigned_to_name')
            ->join('sp_members', 'sp_members.id = cms_contact_messages.assigned_to', 'left');

        if ($status && in_array($status, ['new', 'read', 'replied', 'archived'])) {
            $builder->where('cms_contact_messages.status', $status);
        }

        $messages = $builder->orderBy('cms_contact_messages.created_at', 'DESC')
                           ->paginate($perPage);

        // Get statistics
        $stats = [
            'total' => $this->contactModel->countAllResults(false),
            'new' => $this->contactModel->where('status', 'new')->countAllResults(false),
            'read' => $this->contactModel->where('status', 'read')->countAllResults(false),
            'replied' => $this->contactModel->where('status', 'replied')->countAllResults(false),
            'archived' => $this->contactModel->where('status', 'archived')->countAllResults(false),
        ];

        $data = [
            'title' => 'Inbox Kontak - CMS Admin',
            'messages' => $messages,
            'pager' => $this->contactModel->pager,
            'status' => $status,
            'stats' => $stats,
        ];

        return view('admin/cms/contact/index', $data);
    }

    /**
     * View message detail
     */
    public function view($id)
    {
        $message = $this->contactModel
            ->select('cms_contact_messages.*, sp_members.full_name as assigned_to_name')
            ->join('sp_members', 'sp_members.id = cms_contact_messages.assigned_to', 'left')
            ->find($id);

        if (!$message) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Mark as read if status is new
        if ($message['status'] === 'new') {
            $this->contactModel->update($id, [
                'status' => 'read',
                'read_at' => date('Y-m-d H:i:s'),
            ]);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_contact_message',
                'target_id' => $id,
                'action' => 'cms.contact.read',
                'new_values' => json_encode(['status' => 'read']),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $data = [
            'title' => 'Detail Pesan - Inbox Kontak',
            'message' => $message,
        ];

        return view('admin/cms/contact/view', $data);
    }

    /**
     * Assign message to admin
     */
    public function assign($id)
    {
        $message = $this->contactModel->find($id);

        if (!$message) {
            return redirect()->back()
                           ->with('error', 'Pesan tidak ditemukan.');
        }

        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        $assignedTo = $this->request->getPost('assigned_to');

        if (empty($assignedTo)) {
            return redirect()->back()
                           ->with('error', 'Pilih admin yang akan ditugaskan.');
        }

        try {
            $this->contactModel->update($id, [
                'assigned_to' => $assignedTo,
            ]);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_contact_message',
                'target_id' => $id,
                'action' => 'cms.contact.assigned',
                'old_values' => json_encode(['assigned_to' => $message['assigned_to']]),
                'new_values' => json_encode(['assigned_to' => $assignedTo]),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->back()
                           ->with('success', 'Pesan berhasil ditugaskan.');
        } catch (\Exception $e) {
            log_message('error', 'Contact assign error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * Mark message as replied
     */
    public function markReplied($id)
    {
        $message = $this->contactModel->find($id);

        if (!$message) {
            return redirect()->back()
                           ->with('error', 'Pesan tidak ditemukan.');
        }

        try {
            $this->contactModel->update($id, [
                'status' => 'replied',
                'replied_at' => date('Y-m-d H:i:s'),
                'replied_by' => session('member_id'),
            ]);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_contact_message',
                'target_id' => $id,
                'action' => 'cms.contact.replied',
                'old_values' => json_encode(['status' => $message['status']]),
                'new_values' => json_encode(['status' => 'replied']),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->back()
                           ->with('success', 'Pesan ditandai sebagai sudah dibalas.');
        } catch (\Exception $e) {
            log_message('error', 'Contact mark replied error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * Add reply note
     */
    public function addNote($id)
    {
        $message = $this->contactModel->find($id);

        if (!$message) {
            return redirect()->back()
                           ->with('error', 'Pesan tidak ditemukan.');
        }

        if (!$this->request->is('post')) {
            return redirect()->back();
        }

        $replyNote = $this->request->getPost('reply_note');

        if (empty($replyNote)) {
            return redirect()->back()
                           ->with('error', 'Catatan balasan tidak boleh kosong.');
        }

        try {
            $this->contactModel->update($id, [
                'reply_note' => $replyNote,
            ]);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_contact_message',
                'target_id' => $id,
                'action' => 'cms.contact.note_added',
                'new_values' => json_encode(['reply_note' => $replyNote]),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->back()
                           ->with('success', 'Catatan berhasil ditambahkan.');
        } catch (\Exception $e) {
            log_message('error', 'Contact add note error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * Archive message
     */
    public function archive($id)
    {
        $message = $this->contactModel->find($id);

        if (!$message) {
            return redirect()->back()
                           ->with('error', 'Pesan tidak ditemukan.');
        }

        try {
            $this->contactModel->update($id, [
                'status' => 'archived',
            ]);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_contact_message',
                'target_id' => $id,
                'action' => 'cms.contact.archived',
                'old_values' => json_encode(['status' => $message['status']]),
                'new_values' => json_encode(['status' => 'archived']),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->back()
                           ->with('success', 'Pesan berhasil diarsipkan.');
        } catch (\Exception $e) {
            log_message('error', 'Contact archive error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * Delete message
     */
    public function delete($id)
    {
        $message = $this->contactModel->find($id);

        if (!$message) {
            return redirect()->back()
                           ->with('error', 'Pesan tidak ditemukan.');
        }

        try {
            $this->contactModel->delete($id);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_contact_message',
                'target_id' => $id,
                'action' => 'cms.contact.deleted',
                'old_values' => json_encode($message),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->back()
                           ->with('success', 'Pesan berhasil dihapus.');
        } catch (\Exception $e) {
            log_message('error', 'Contact deletion error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menghapus pesan.');
        }
    }

    /**
     * Bulk archive messages
     */
    public function bulkArchive()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/admin/cms/contact');
        }

        $ids = $this->request->getPost('message_ids');

        if (empty($ids) || !is_array($ids)) {
            return redirect()->back()
                           ->with('error', 'Tidak ada pesan yang dipilih.');
        }

        try {
            $archivedCount = 0;

            foreach ($ids as $id) {
                $message = $this->contactModel->find($id);
                if ($message) {
                    $this->contactModel->update($id, ['status' => 'archived']);
                    $archivedCount++;

                    // Audit log
                    $this->auditLog->insert([
                        'actor_id' => session('member_id'),
                        'target_type' => 'cms_contact_message',
                        'target_id' => $id,
                        'action' => 'cms.contact.bulk_archived',
                        'old_values' => json_encode(['status' => $message['status']]),
                        'new_values' => json_encode(['status' => 'archived']),
                        'ip_address' => $this->request->getIPAddress(),
                        'user_agent' => $this->request->getUserAgent(),
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            return redirect()->to('/admin/cms/contact')
                           ->with('success', "{$archivedCount} pesan berhasil diarsipkan.");
        } catch (\Exception $e) {
            log_message('error', 'Bulk archive messages error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * Export messages to CSV
     */
    public function export()
    {
        $status = $this->request->getGet('status');

        $builder = $this->contactModel;

        if ($status && in_array($status, ['new', 'read', 'replied', 'archived'])) {
            $builder->where('status', $status);
        }

        $messages = $builder->orderBy('created_at', 'DESC')->findAll();

        // Generate CSV
        $filename = 'contact_messages_' . date('Y-m-d_His') . '.csv';
        $filepath = WRITEPATH . 'uploads/exports/' . $filename;

        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $file = fopen($filepath, 'w');

        // CSV Headers
        fputcsv($file, ['Name', 'Email', 'Subject', 'Message', 'Status', 'Created At', 'Read At', 'Replied At']);

        // CSV Rows
        foreach ($messages as $message) {
            fputcsv($file, [
                $message['name'],
                $message['email'],
                $message['subject'],
                $message['message'],
                $message['status'],
                $message['created_at'],
                $message['read_at'],
                $message['replied_at'],
            ]);
        }

        fclose($file);

        // Audit log
        $this->auditLog->insert([
            'actor_id' => session('member_id'),
            'target_type' => 'cms_contact_message',
            'target_id' => null,
            'action' => 'cms.contact.exported',
            'new_values' => json_encode([
                'count' => count($messages),
                'status_filter' => $status,
                'filename' => $filename,
            ]),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Download file
        return $this->response->download($filepath, null)->setFileName($filename);
    }
}
