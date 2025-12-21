<?php

namespace App\Controllers\Admin\CMS;

use App\Controllers\BaseController;
use App\Models\CmsHomeSectionModel;
use App\Models\AuditLogModel;

class LandingController extends BaseController
{
    protected $sectionModel;
    protected $auditLog;

    public function __construct()
    {
        $this->sectionModel = new CmsHomeSectionModel();
        $this->auditLog = new AuditLogModel();

        // Only Super Admin can access
        if (session('role') !== 'super_admin') {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Access Denied: Super Admin only.');
        }
    }

    /**
     * Manage landing page sections
     */
    public function index()
    {
        $sections = $this->sectionModel
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Kelola Landing Page - CMS Admin',
            'sections' => $sections,
        ];

        return view('admin/cms/landing/index', $data);
    }

    /**
     * Create new section
     */
    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            return $this->processCreate();
        }

        $data = [
            'title' => 'Tambah Section - Landing Page',
        ];

        return view('admin/cms/landing/create', $data);
    }

    /**
     * Process create section
     */
    protected function processCreate()
    {
        $rules = [
            'section_type' => 'required|in_list[hero,stats,features,news,documents,cta,custom]',
            'title' => 'required|min_length[3]|max_length[200]',
            'content_html' => 'permit_empty',
            'sort_order' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        // Parse JSON settings
        $settingsJson = $this->request->getPost('settings_json');
        $settings = null;

        if (!empty($settingsJson)) {
            $settings = json_decode($settingsJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Format JSON settings tidak valid.');
            }
        }

        $data = [
            'section_type' => $this->request->getPost('section_type'),
            'title' => $this->request->getPost('title'),
            'subtitle' => $this->request->getPost('subtitle'),
            'content_html' => $this->request->getPost('content_html'),
            'settings_json' => $settings ? json_encode($settings) : null,
            'sort_order' => $this->request->getPost('sort_order') ?: 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        // Handle background image upload
        $file = $this->request->getFile('background_image');
        if ($file && $file->isValid()) {
            try {
                $newName = $file->getRandomName();
                $uploadPath = WRITEPATH . 'uploads/landing/';

                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $newName);
                $data['background_image'] = $newName;
            } catch (\Exception $e) {
                log_message('error', 'Landing section image upload error: ' . $e->getMessage());
            }
        }

        try {
            $id = $this->sectionModel->insert($data);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_home_section',
                'target_id' => $id,
                'action' => 'cms.landing.section.created',
                'new_values' => json_encode($data),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/cms/landing')
                           ->with('success', 'Section berhasil ditambahkan.');
        } catch (\Exception $e) {
            log_message('error', 'Landing section creation error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat menambah section.');
        }
    }

    /**
     * Edit section
     */
    public function edit($id)
    {
        $section = $this->sectionModel->find($id);

        if (!$section) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->request->getMethod() === 'post') {
            return $this->processEdit($id, $section);
        }

        $data = [
            'title' => 'Edit Section - Landing Page',
            'section' => $section,
        ];

        return view('admin/cms/landing/edit', $data);
    }

    /**
     * Process edit section
     */
    protected function processEdit($id, $oldSection)
    {
        $rules = [
            'section_type' => 'required|in_list[hero,stats,features,news,documents,cta,custom]',
            'title' => 'required|min_length[3]|max_length[200]',
            'content_html' => 'permit_empty',
            'sort_order' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        // Parse JSON settings
        $settingsJson = $this->request->getPost('settings_json');
        $settings = null;

        if (!empty($settingsJson)) {
            $settings = json_decode($settingsJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Format JSON settings tidak valid.');
            }
        }

        $data = [
            'section_type' => $this->request->getPost('section_type'),
            'title' => $this->request->getPost('title'),
            'subtitle' => $this->request->getPost('subtitle'),
            'content_html' => $this->request->getPost('content_html'),
            'settings_json' => $settings ? json_encode($settings) : null,
            'sort_order' => $this->request->getPost('sort_order') ?: 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        // Handle background image replacement
        $file = $this->request->getFile('background_image');
        if ($file && $file->isValid()) {
            try {
                $newName = $file->getRandomName();
                $uploadPath = WRITEPATH . 'uploads/landing/';

                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $newName);

                // Delete old image
                if (!empty($oldSection['background_image'])) {
                    $oldImagePath = $uploadPath . $oldSection['background_image'];
                    if (file_exists($oldImagePath)) {
                        @unlink($oldImagePath);
                    }
                }

                $data['background_image'] = $newName;
            } catch (\Exception $e) {
                log_message('error', 'Landing section image replacement error: ' . $e->getMessage());
            }
        }

        try {
            $this->sectionModel->update($id, $data);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_home_section',
                'target_id' => $id,
                'action' => 'cms.landing.section.updated',
                'old_values' => json_encode($oldSection),
                'new_values' => json_encode($data),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/cms/landing')
                           ->with('success', 'Section berhasil diupdate.');
        } catch (\Exception $e) {
            log_message('error', 'Landing section update error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat mengupdate section.');
        }
    }

    /**
     * Delete section
     */
    public function delete($id)
    {
        $section = $this->sectionModel->find($id);

        if (!$section) {
            return redirect()->back()
                           ->with('error', 'Section tidak ditemukan.');
        }

        try {
            // Delete background image if exists
            if (!empty($section['background_image'])) {
                $imagePath = WRITEPATH . 'uploads/landing/' . $section['background_image'];
                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }
            }

            $this->sectionModel->delete($id);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_home_section',
                'target_id' => $id,
                'action' => 'cms.landing.section.deleted',
                'old_values' => json_encode($section),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->back()
                           ->with('success', 'Section berhasil dihapus.');
        } catch (\Exception $e) {
            log_message('error', 'Landing section deletion error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menghapus section.');
        }
    }

    /**
     * Toggle section active status
     */
    public function toggleActive($id)
    {
        $section = $this->sectionModel->find($id);

        if (!$section) {
            return redirect()->back()
                           ->with('error', 'Section tidak ditemukan.');
        }

        $newStatus = $section['is_active'] ? 0 : 1;

        try {
            $this->sectionModel->update($id, ['is_active' => $newStatus]);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_home_section',
                'target_id' => $id,
                'action' => 'cms.landing.section.toggled',
                'old_values' => json_encode(['is_active' => $section['is_active']]),
                'new_values' => json_encode(['is_active' => $newStatus]),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $message = $newStatus ? 'Section diaktifkan.' : 'Section dinonaktifkan.';
            return redirect()->back()
                           ->with('success', $message);
        } catch (\Exception $e) {
            log_message('error', 'Landing section toggle error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan.');
        }
    }

    /**
     * Reorder sections via AJAX
     */
    public function reorder()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ])->setStatusCode(400);
        }

        $order = $this->request->getJSON(true)['order'] ?? [];

        if (empty($order)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No order data provided'
            ])->setStatusCode(400);
        }

        try {
            foreach ($order as $index => $sectionId) {
                $this->sectionModel->update($sectionId, ['sort_order' => $index]);
            }

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_home_section',
                'target_id' => null,
                'action' => 'cms.landing.sections.reordered',
                'new_values' => json_encode(['order' => $order]),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Section order updated successfully'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Landing section reorder error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update order'
            ])->setStatusCode(500);
        }
    }
}
