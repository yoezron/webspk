<?php

namespace App\Controllers\Admin\CMS;

use App\Controllers\BaseController;
use App\Models\CmsDocumentModel;
use App\Models\CmsDocumentCategoryModel;
use App\Models\AuditLogModel;

class DocumentController extends BaseController
{
    protected $documentModel;
    protected $categoryModel;
    protected $auditLog;

    public function __construct()
    {
        $this->documentModel = new CmsDocumentModel();
        $this->categoryModel = new CmsDocumentCategoryModel();
        $this->auditLog = new AuditLogModel();
    }

    /**
     * List all documents
     */
    public function index()
    {
        $docType = $this->request->getGet('type'); // publikasi or regulasi
        $perPage = 20;

        $builder = $this->documentModel
            ->select('cms_documents.*, cms_document_categories.name as category_name, sp_members.full_name as creator_name')
            ->join('cms_document_categories', 'cms_document_categories.id = cms_documents.category_id', 'left')
            ->join('sp_members', 'sp_members.id = cms_documents.created_by', 'left');

        if ($docType && in_array($docType, ['publikasi', 'regulasi'])) {
            $builder->where('cms_documents.doc_type', $docType);
        }

        $documents = $builder->orderBy('cms_documents.created_at', 'DESC')
                            ->paginate($perPage);

        $data = [
            'title' => 'Kelola Dokumen - CMS Admin',
            'documents' => $documents,
            'pager' => $this->documentModel->pager,
            'doc_type' => $docType,
            'categories' => $this->categoryModel->findAll(),
        ];

        return view('admin/cms/documents/index', $data);
    }

    /**
     * Create new document form
     */
    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            return $this->processCreate();
        }

        $data = [
            'title' => 'Upload Dokumen Baru - CMS Admin',
            'categories' => $this->categoryModel->findAll(),
        ];

        return view('admin/cms/documents/create', $data);
    }

    /**
     * Process create document with file upload
     */
    protected function processCreate()
    {
        $rules = [
            'doc_type' => 'required|in_list[publikasi,regulasi]',
            'title' => 'required|min_length[3]|max_length[255]',
            'slug' => 'permit_empty|is_unique[cms_documents.slug]',
            'description' => 'permit_empty',
            'category_id' => 'permit_empty|integer',
            'status' => 'required|in_list[draft,published,archived]',
            'document_file' => 'uploaded[document_file]|max_size[document_file,10240]|ext_in[document_file,pdf]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('document_file');

        if (!$file->isValid()) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'File upload tidak valid.');
        }

        try {
            // Generate unique filename
            $newName = $file->getRandomName();
            $docType = $this->request->getPost('doc_type');

            // Move file
            $uploadPath = WRITEPATH . 'uploads/documents/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $file->move($uploadPath, $newName);

            // Calculate checksum
            $filepath = $uploadPath . $newName;
            $checksum = hash_file('sha256', $filepath);

            // Generate slug
            $slug = $this->request->getPost('slug');
            if (empty($slug)) {
                $slug = $this->documentModel->generateSlug($this->request->getPost('title'));
            }

            $data = [
                'doc_type' => $docType,
                'title' => $this->request->getPost('title'),
                'slug' => $slug,
                'description' => $this->request->getPost('description'),
                'category_id' => $this->request->getPost('category_id') ?: null,
                'file_path' => $newName,
                'original_name' => $file->getClientName(),
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'checksum_sha256' => $checksum,
                'status' => $this->request->getPost('status'),
                'created_by' => session('member_id'),
            ];

            if ($data['status'] === 'published') {
                $data['published_at'] = date('Y-m-d H:i:s');
            }

            $id = $this->documentModel->insert($data);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_document',
                'target_id' => $id,
                'action' => 'cms.document.created',
                'new_values' => json_encode($data),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/cms/documents?type=' . $docType)
                           ->with('success', 'Dokumen berhasil diupload.');
        } catch (\Exception $e) {
            log_message('error', 'Document upload error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat mengupload dokumen.');
        }
    }

    /**
     * Edit document form
     */
    public function edit($id)
    {
        $document = $this->documentModel->find($id);

        if (!$document) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->request->getMethod() === 'post') {
            return $this->processEdit($id, $document);
        }

        $data = [
            'title' => 'Edit Dokumen - CMS Admin',
            'document' => $document,
            'categories' => $this->categoryModel->getCategories($document['doc_type']),
        ];

        return view('admin/cms/documents/edit', $data);
    }

    /**
     * Process edit document
     */
    protected function processEdit($id, $oldDocument)
    {
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'slug' => "permit_empty|is_unique[cms_documents.slug,id,{$id}]",
            'description' => 'permit_empty',
            'category_id' => 'permit_empty|integer',
            'status' => 'required|in_list[draft,published,archived]',
            'document_file' => 'permit_empty|max_size[document_file,10240]|ext_in[document_file,pdf]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $slug = $this->request->getPost('slug');
        if (empty($slug) || $slug !== $oldDocument['slug']) {
            $slug = $this->documentModel->generateSlug($this->request->getPost('title'), $id);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'slug' => $slug,
            'description' => $this->request->getPost('description'),
            'category_id' => $this->request->getPost('category_id') ?: null,
            'status' => $this->request->getPost('status'),
            'updated_by' => session('member_id'),
        ];

        if ($data['status'] === 'published' && empty($oldDocument['published_at'])) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        // Handle file replacement
        $file = $this->request->getFile('document_file');
        if ($file && $file->isValid()) {
            try {
                $newName = $file->getRandomName();
                $uploadPath = WRITEPATH . 'uploads/documents/';
                $file->move($uploadPath, $newName);

                // Delete old file
                $oldFilePath = $uploadPath . $oldDocument['file_path'];
                if (file_exists($oldFilePath)) {
                    @unlink($oldFilePath);
                }

                // Update file info
                $filepath = $uploadPath . $newName;
                $data['file_path'] = $newName;
                $data['original_name'] = $file->getClientName();
                $data['mime_type'] = $file->getClientMimeType();
                $data['file_size'] = $file->getSize();
                $data['checksum_sha256'] = hash_file('sha256', $filepath);
            } catch (\Exception $e) {
                log_message('error', 'File replacement error: ' . $e->getMessage());
            }
        }

        try {
            $this->documentModel->update($id, $data);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_document',
                'target_id' => $id,
                'action' => 'cms.document.updated',
                'old_values' => json_encode($oldDocument),
                'new_values' => json_encode($data),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/cms/documents?type=' . $oldDocument['doc_type'])
                           ->with('success', 'Dokumen berhasil diupdate.');
        } catch (\Exception $e) {
            log_message('error', 'Document update error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat mengupdate dokumen.');
        }
    }

    /**
     * Delete document
     */
    public function delete($id)
    {
        $document = $this->documentModel->find($id);

        if (!$document) {
            return redirect()->back()
                           ->with('error', 'Dokumen tidak ditemukan.');
        }

        try {
            // Delete physical file
            $filepath = WRITEPATH . 'uploads/documents/' . $document['file_path'];
            if (file_exists($filepath)) {
                @unlink($filepath);
            }

            $this->documentModel->delete($id);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_document',
                'target_id' => $id,
                'action' => 'cms.document.deleted',
                'old_values' => json_encode($document),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->back()
                           ->with('success', 'Dokumen berhasil dihapus.');
        } catch (\Exception $e) {
            log_message('error', 'Document deletion error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menghapus dokumen.');
        }
    }

    /**
     * Manage categories
     */
    public function categories()
    {
        if ($this->request->getMethod() === 'post') {
            return $this->processCategoryCreate();
        }

        $categories = $this->categoryModel->orderBy('doc_type', 'ASC')
                                         ->orderBy('sort_order', 'ASC')
                                         ->findAll();

        $data = [
            'title' => 'Kelola Kategori Dokumen - CMS Admin',
            'categories' => $categories,
        ];

        return view('admin/cms/documents/categories', $data);
    }

    /**
     * Process create category
     */
    protected function processCategoryCreate()
    {
        $rules = [
            'doc_type' => 'required|in_list[publikasi,regulasi]',
            'name' => 'required|max_length[100]',
            'sort_order' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->with('errors', $this->validator->getErrors());
        }

        $docType = $this->request->getPost('doc_type');
        $name = $this->request->getPost('name');

        $data = [
            'doc_type' => $docType,
            'name' => $name,
            'slug' => $this->categoryModel->generateSlug($name, $docType),
            'sort_order' => $this->request->getPost('sort_order') ?: 0,
        ];

        $this->categoryModel->insert($data);

        return redirect()->to('/admin/cms/documents/categories')
                       ->with('success', 'Kategori berhasil ditambahkan.');
    }
}
