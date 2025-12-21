<?php

namespace App\Controllers\Admin\CMS;

use App\Controllers\BaseController;
use App\Models\CmsPageModel;
use App\Models\CmsPageRevisionModel;
use App\Models\AuditLogModel;

class PageController extends BaseController
{
    protected $pageModel;
    protected $revisionModel;
    protected $auditLog;

    public function __construct()
    {
        $this->pageModel = new CmsPageModel();
        $this->revisionModel = new CmsPageRevisionModel();
        $this->auditLog = new AuditLogModel();
    }

    /**
     * List all pages
     */
    public function index()
    {
        $pages = $this->pageModel->orderBy('title', 'ASC')->findAll();

        $data = [
            'title' => 'Kelola Halaman - CMS Admin',
            'pages' => $pages,
        ];

        return view('admin/cms/pages/index', $data);
    }

    /**
     * Create new page form
     */
    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            return $this->processCreate();
        }

        $data = [
            'title' => 'Buat Halaman Baru - CMS Admin',
        ];

        return view('admin/cms/pages/create', $data);
    }

    /**
     * Process create page
     */
    protected function processCreate()
    {
        $rules = [
            'title' => 'required|min_length[3]|max_length[200]',
            'slug' => 'permit_empty|max_length[100]|is_unique[cms_pages.slug]',
            'content_html' => 'permit_empty',
            'template' => 'in_list[default,legal,contact]',
            'status' => 'required|in_list[draft,published,archived]',
            'visibility' => 'required|in_list[public,member_only]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        // Generate slug if not provided
        $slug = $this->request->getPost('slug');
        if (empty($slug)) {
            $slug = $this->pageModel->generateSlug($this->request->getPost('title'));
        }

        $data = [
            'slug' => $slug,
            'title' => $this->request->getPost('title'),
            'content_html' => $this->request->getPost('content_html'),
            'template' => $this->request->getPost('template') ?: 'default',
            'status' => $this->request->getPost('status'),
            'visibility' => $this->request->getPost('visibility'),
            'created_by' => session('member_id'),
        ];

        if ($data['status'] === 'published' && empty($this->request->getPost('published_at'))) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        try {
            $id = $this->pageModel->insert($data);

            // Create initial revision
            $this->revisionModel->createRevision(
                $id,
                $data['content_html'],
                'Initial creation',
                session('member_id')
            );

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_page',
                'target_id' => $id,
                'action' => 'cms.page.created',
                'new_values' => json_encode($data),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/cms/pages')
                           ->with('success', 'Halaman berhasil dibuat.');
        } catch (\Exception $e) {
            log_message('error', 'Page creation error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat membuat halaman.');
        }
    }

    /**
     * Edit page form
     */
    public function edit($id)
    {
        $page = $this->pageModel->find($id);

        if (!$page) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->request->getMethod() === 'post') {
            return $this->processEdit($id, $page);
        }

        // Get revisions
        $revisions = $this->revisionModel->getPageRevisions($id, 10);

        $data = [
            'title' => 'Edit Halaman - CMS Admin',
            'page' => $page,
            'revisions' => $revisions,
        ];

        return view('admin/cms/pages/edit', $data);
    }

    /**
     * Process edit page
     */
    protected function processEdit($id, $oldPage)
    {
        $rules = [
            'title' => 'required|min_length[3]|max_length[200]',
            'slug' => "permit_empty|max_length[100]|is_unique[cms_pages.slug,id,{$id}]",
            'content_html' => 'permit_empty',
            'template' => 'in_list[default,legal,contact]',
            'status' => 'required|in_list[draft,published,archived]',
            'visibility' => 'required|in_list[public,member_only]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        // Generate slug if changed
        $slug = $this->request->getPost('slug');
        if (empty($slug) || $slug !== $oldPage['slug']) {
            $slug = $this->pageModel->generateSlug($this->request->getPost('title'), $id);
        }

        $data = [
            'slug' => $slug,
            'title' => $this->request->getPost('title'),
            'content_html' => $this->request->getPost('content_html'),
            'template' => $this->request->getPost('template') ?: 'default',
            'status' => $this->request->getPost('status'),
            'visibility' => $this->request->getPost('visibility'),
            'updated_by' => session('member_id'),
        ];

        // Set published_at if status changed to published
        if ($data['status'] === 'published' && empty($oldPage['published_at'])) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        try {
            // Create revision if content changed
            if ($data['content_html'] !== $oldPage['content_html']) {
                $this->revisionModel->createRevision(
                    $id,
                    $oldPage['content_html'],
                    'Content updated',
                    session('member_id')
                );
            }

            $this->pageModel->update($id, $data);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_page',
                'target_id' => $id,
                'action' => 'cms.page.updated',
                'old_values' => json_encode($oldPage),
                'new_values' => json_encode($data),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/cms/pages')
                           ->with('success', 'Halaman berhasil diupdate.');
        } catch (\Exception $e) {
            log_message('error', 'Page update error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat mengupdate halaman.');
        }
    }

    /**
     * Delete page
     */
    public function delete($id)
    {
        $page = $this->pageModel->find($id);

        if (!$page) {
            return redirect()->back()
                           ->with('error', 'Halaman tidak ditemukan.');
        }

        try {
            $this->pageModel->delete($id);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_page',
                'target_id' => $id,
                'action' => 'cms.page.deleted',
                'old_values' => json_encode($page),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/cms/pages')
                           ->with('success', 'Halaman berhasil dihapus.');
        } catch (\Exception $e) {
            log_message('error', 'Page deletion error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menghapus halaman.');
        }
    }

    /**
     * View revision
     */
    public function viewRevision($revisionId)
    {
        $revision = $this->revisionModel->find($revisionId);

        if (!$revision) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $page = $this->pageModel->find($revision['page_id']);

        $data = [
            'title' => 'Lihat Revisi - CMS Admin',
            'revision' => $revision,
            'page' => $page,
        ];

        return view('admin/cms/pages/view_revision', $data);
    }

    /**
     * Restore revision
     */
    public function restoreRevision($revisionId)
    {
        $revision = $this->revisionModel->find($revisionId);

        if (!$revision) {
            return redirect()->back()
                           ->with('error', 'Revisi tidak ditemukan.');
        }

        $page = $this->pageModel->find($revision['page_id']);

        try {
            // Create new revision with current content before restoring
            $this->revisionModel->createRevision(
                $page['id'],
                $page['content_html'],
                'Before restoring revision #' . $revisionId,
                session('member_id')
            );

            // Restore content from revision
            $this->pageModel->update($page['id'], [
                'content_html' => $revision['content_html'],
                'updated_by' => session('member_id'),
            ]);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_page',
                'target_id' => $page['id'],
                'action' => 'cms.page.revision_restored',
                'new_values' => json_encode(['revision_id' => $revisionId]),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/cms/pages/edit/' . $page['id'])
                           ->with('success', 'Revisi berhasil dipulihkan.');
        } catch (\Exception $e) {
            log_message('error', 'Revision restore error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat memulihkan revisi.');
        }
    }
}
