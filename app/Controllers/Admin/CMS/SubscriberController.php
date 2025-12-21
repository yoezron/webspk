<?php

namespace App\Controllers\Admin\CMS;

use App\Controllers\BaseController;
use App\Models\CmsSubscriberModel;
use App\Models\AuditLogModel;

class SubscriberController extends BaseController
{
    protected $subscriberModel;
    protected $auditLog;

    public function __construct()
    {
        $this->subscriberModel = new CmsSubscriberModel();
        $this->auditLog = new AuditLogModel();
    }

    /**
     * List all subscribers
     */
    public function index()
    {
        $status = $this->request->getGet('status'); // active, unsubscribed, bounced
        $perPage = 50;

        $builder = $this->subscriberModel;

        if ($status && in_array($status, ['active', 'unsubscribed', 'bounced'])) {
            $builder->where('status', $status);
        }

        $subscribers = $builder->orderBy('created_at', 'DESC')
                              ->paginate($perPage);

        // Get statistics
        $stats = [
            'total' => $this->subscriberModel->countAllResults(false),
            'active' => $this->subscriberModel->where('status', 'active')->countAllResults(false),
            'unsubscribed' => $this->subscriberModel->where('status', 'unsubscribed')->countAllResults(false),
            'bounced' => $this->subscriberModel->where('status', 'bounced')->countAllResults(false),
        ];

        $data = [
            'title' => 'Kelola Subscribers - CMS Admin',
            'subscribers' => $subscribers,
            'pager' => $this->subscriberModel->pager,
            'status' => $status,
            'stats' => $stats,
        ];

        return view('admin/cms/subscribers/index', $data);
    }

    /**
     * Add subscriber manually
     */
    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            return $this->processCreate();
        }

        $data = [
            'title' => 'Tambah Subscriber - CMS Admin',
        ];

        return view('admin/cms/subscribers/create', $data);
    }

    /**
     * Process add subscriber
     */
    protected function processCreate()
    {
        $rules = [
            'email' => 'required|valid_email|is_unique[cms_subscribers.email]',
            'full_name' => 'permit_empty|max_length[150]',
        ];

        $validationMessages = [
            'email' => [
                'is_unique' => 'Email ini sudah terdaftar sebagai subscriber.',
            ],
        ];

        if (!$this->validate($rules, $validationMessages)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'email' => $this->request->getPost('email'),
            'full_name' => $this->request->getPost('full_name'),
            'status' => 'active',
            'is_verified' => 1, // Manually added, so auto-verified
            'subscribed_at' => date('Y-m-d H:i:s'),
        ];

        try {
            $id = $this->subscriberModel->insert($data);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_subscriber',
                'target_id' => $id,
                'action' => 'cms.subscriber.created',
                'new_values' => json_encode($data),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/cms/subscribers')
                           ->with('success', 'Subscriber berhasil ditambahkan.');
        } catch (\Exception $e) {
            log_message('error', 'Subscriber creation error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat menambah subscriber.');
        }
    }

    /**
     * Edit subscriber
     */
    public function edit($id)
    {
        $subscriber = $this->subscriberModel->find($id);

        if (!$subscriber) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->request->getMethod() === 'post') {
            return $this->processEdit($id, $subscriber);
        }

        $data = [
            'title' => 'Edit Subscriber - CMS Admin',
            'subscriber' => $subscriber,
        ];

        return view('admin/cms/subscribers/edit', $data);
    }

    /**
     * Process edit subscriber
     */
    protected function processEdit($id, $oldSubscriber)
    {
        $rules = [
            'email' => "required|valid_email|is_unique[cms_subscribers.email,id,{$id}]",
            'full_name' => 'permit_empty|max_length[150]',
            'status' => 'required|in_list[active,unsubscribed,bounced]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'email' => $this->request->getPost('email'),
            'full_name' => $this->request->getPost('full_name'),
            'status' => $this->request->getPost('status'),
        ];

        // Update unsubscribed_at if status changed to unsubscribed
        if ($data['status'] === 'unsubscribed' && $oldSubscriber['status'] !== 'unsubscribed') {
            $data['unsubscribed_at'] = date('Y-m-d H:i:s');
        }

        try {
            $this->subscriberModel->update($id, $data);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_subscriber',
                'target_id' => $id,
                'action' => 'cms.subscriber.updated',
                'old_values' => json_encode($oldSubscriber),
                'new_values' => json_encode($data),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/cms/subscribers')
                           ->with('success', 'Subscriber berhasil diupdate.');
        } catch (\Exception $e) {
            log_message('error', 'Subscriber update error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat mengupdate subscriber.');
        }
    }

    /**
     * Delete subscriber
     */
    public function delete($id)
    {
        $subscriber = $this->subscriberModel->find($id);

        if (!$subscriber) {
            return redirect()->back()
                           ->with('error', 'Subscriber tidak ditemukan.');
        }

        try {
            $this->subscriberModel->delete($id);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_subscriber',
                'target_id' => $id,
                'action' => 'cms.subscriber.deleted',
                'old_values' => json_encode($subscriber),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->back()
                           ->with('success', 'Subscriber berhasil dihapus.');
        } catch (\Exception $e) {
            log_message('error', 'Subscriber deletion error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menghapus subscriber.');
        }
    }

    /**
     * Export subscribers to CSV
     */
    public function export()
    {
        $status = $this->request->getGet('status');

        $builder = $this->subscriberModel;

        if ($status && in_array($status, ['active', 'unsubscribed', 'bounced'])) {
            $builder->where('status', $status);
        }

        $subscribers = $builder->orderBy('created_at', 'DESC')->findAll();

        // Generate CSV
        $filename = 'subscribers_' . date('Y-m-d_His') . '.csv';
        $filepath = WRITEPATH . 'uploads/exports/' . $filename;

        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $file = fopen($filepath, 'w');

        // CSV Headers
        fputcsv($file, ['Email', 'Full Name', 'Status', 'Verified', 'Subscribed At', 'Unsubscribed At']);

        // CSV Rows
        foreach ($subscribers as $subscriber) {
            fputcsv($file, [
                $subscriber['email'],
                $subscriber['full_name'],
                $subscriber['status'],
                $subscriber['is_verified'] ? 'Yes' : 'No',
                $subscriber['subscribed_at'],
                $subscriber['unsubscribed_at'],
            ]);
        }

        fclose($file);

        // Audit log
        $this->auditLog->insert([
            'actor_id' => session('member_id'),
            'target_type' => 'cms_subscriber',
            'target_id' => null,
            'action' => 'cms.subscriber.exported',
            'new_values' => json_encode([
                'count' => count($subscribers),
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

    /**
     * Bulk delete subscribers
     */
    public function bulkDelete()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/admin/cms/subscribers');
        }

        $ids = $this->request->getPost('subscriber_ids');

        if (empty($ids) || !is_array($ids)) {
            return redirect()->back()
                           ->with('error', 'Tidak ada subscriber yang dipilih.');
        }

        try {
            $deletedCount = 0;

            foreach ($ids as $id) {
                $subscriber = $this->subscriberModel->find($id);
                if ($subscriber) {
                    $this->subscriberModel->delete($id);
                    $deletedCount++;

                    // Audit log
                    $this->auditLog->insert([
                        'actor_id' => session('member_id'),
                        'target_type' => 'cms_subscriber',
                        'target_id' => $id,
                        'action' => 'cms.subscriber.bulk_deleted',
                        'old_values' => json_encode($subscriber),
                        'ip_address' => $this->request->getIPAddress(),
                        'user_agent' => $this->request->getUserAgent(),
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            return redirect()->to('/admin/cms/subscribers')
                           ->with('success', "{$deletedCount} subscriber berhasil dihapus.");
        } catch (\Exception $e) {
            log_message('error', 'Bulk delete subscribers error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menghapus subscriber.');
        }
    }
}
