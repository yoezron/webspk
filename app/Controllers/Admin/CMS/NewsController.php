<?php

namespace App\Controllers\Admin\CMS;

use App\Controllers\BaseController;
use App\Models\CmsNewsPostModel;
use App\Models\CmsMediaModel;
use App\Models\AuditLogModel;

class NewsController extends BaseController
{
    protected $newsModel;
    protected $mediaModel;
    protected $auditLog;

    public function __construct()
    {
        $this->newsModel = new CmsNewsPostModel();
        $this->mediaModel = new CmsMediaModel();
        $this->auditLog = new AuditLogModel();
    }

    /**
     * List all news posts
     */
    public function index()
    {
        $perPage = 20;
        $posts = $this->newsModel
            ->select('cms_news_posts.*, sp_members.full_name as author_name')
            ->join('sp_members', 'sp_members.id = cms_news_posts.author_id', 'left')
            ->orderBy('cms_news_posts.created_at', 'DESC')
            ->paginate($perPage);

        $data = [
            'title' => 'Kelola Berita - CMS Admin',
            'posts' => $posts,
            'pager' => $this->newsModel->pager,
        ];

        return view('admin/cms/news/index', $data);
    }

    /**
     * Create new news post form
     */
    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            return $this->processCreate();
        }

        $data = [
            'title' => 'Buat Berita Baru - CMS Admin',
            'recent_media' => $this->mediaModel->getMediaByType('image', 20),
        ];

        return view('admin/cms/news/create', $data);
    }

    /**
     * Process create news post
     */
    protected function processCreate()
    {
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'slug' => 'permit_empty|is_unique[cms_news_posts.slug]',
            'excerpt' => 'permit_empty',
            'content_html' => 'required',
            'cover_image_id' => 'permit_empty|integer',
            'status' => 'required|in_list[draft,published,archived]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        // Generate slug if not provided
        $slug = $this->request->getPost('slug');
        if (empty($slug)) {
            $slug = $this->newsModel->generateSlug($this->request->getPost('title'));
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'slug' => $slug,
            'excerpt' => $this->request->getPost('excerpt'),
            'content_html' => $this->request->getPost('content_html'),
            'cover_image_id' => $this->request->getPost('cover_image_id') ?: null,
            'status' => $this->request->getPost('status'),
            'author_id' => session('member_id'),
        ];

        if ($data['status'] === 'published') {
            $data['published_at'] = $this->request->getPost('published_at') ?: date('Y-m-d H:i:s');
        }

        try {
            $id = $this->newsModel->insert($data);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_news',
                'target_id' => $id,
                'action' => 'cms.news.created',
                'new_values' => json_encode($data),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/cms/news')
                           ->with('success', 'Berita berhasil dibuat.');
        } catch (\Exception $e) {
            log_message('error', 'News creation error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat membuat berita.');
        }
    }

    /**
     * Edit news post form
     */
    public function edit($id)
    {
        $post = $this->newsModel->find($id);

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->request->getMethod() === 'post') {
            return $this->processEdit($id, $post);
        }

        $data = [
            'title' => 'Edit Berita - CMS Admin',
            'post' => $post,
            'recent_media' => $this->mediaModel->getMediaByType('image', 20),
        ];

        return view('admin/cms/news/edit', $data);
    }

    /**
     * Process edit news post
     */
    protected function processEdit($id, $oldPost)
    {
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'slug' => "permit_empty|is_unique[cms_news_posts.slug,id,{$id}]",
            'excerpt' => 'permit_empty',
            'content_html' => 'required',
            'cover_image_id' => 'permit_empty|integer',
            'status' => 'required|in_list[draft,published,archived]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        // Generate slug if changed
        $slug = $this->request->getPost('slug');
        if (empty($slug) || $slug !== $oldPost['slug']) {
            $slug = $this->newsModel->generateSlug($this->request->getPost('title'), $id);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'slug' => $slug,
            'excerpt' => $this->request->getPost('excerpt'),
            'content_html' => $this->request->getPost('content_html'),
            'cover_image_id' => $this->request->getPost('cover_image_id') ?: null,
            'status' => $this->request->getPost('status'),
        ];

        // Set published_at if status changed to published
        if ($data['status'] === 'published' && empty($oldPost['published_at'])) {
            $data['published_at'] = $this->request->getPost('published_at') ?: date('Y-m-d H:i:s');
        }

        try {
            $this->newsModel->update($id, $data);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_news',
                'target_id' => $id,
                'action' => 'cms.news.updated',
                'old_values' => json_encode($oldPost),
                'new_values' => json_encode($data),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/cms/news')
                           ->with('success', 'Berita berhasil diupdate.');
        } catch (\Exception $e) {
            log_message('error', 'News update error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat mengupdate berita.');
        }
    }

    /**
     * Delete news post
     */
    public function delete($id)
    {
        $post = $this->newsModel->find($id);

        if (!$post) {
            return redirect()->back()
                           ->with('error', 'Berita tidak ditemukan.');
        }

        try {
            $this->newsModel->delete($id);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_news',
                'target_id' => $id,
                'action' => 'cms.news.deleted',
                'old_values' => json_encode($post),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/cms/news')
                           ->with('success', 'Berita berhasil dihapus.');
        } catch (\Exception $e) {
            log_message('error', 'News deletion error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menghapus berita.');
        }
    }

    /**
     * View news post details
     */
    public function view($id)
    {
        $post = $this->newsModel
            ->select('cms_news_posts.*, sp_members.full_name as author_name, cms_media.file_path as cover_image_path')
            ->join('sp_members', 'sp_members.id = cms_news_posts.author_id', 'left')
            ->join('cms_media', 'cms_media.id = cms_news_posts.cover_image_id', 'left')
            ->find($id);

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => 'Detail Berita - CMS Admin',
            'post' => $post,
        ];

        return view('admin/cms/news/view', $data);
    }
}
