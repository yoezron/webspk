<?php

namespace App\Controllers\Public;

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
     * Publikasi (Publications) Page
     */
    public function publikasi()
    {
        $categoryId = $this->request->getGet('category');

        // Get all publications
        $documents = $this->documentModel->getPublishedDocuments('publikasi', $categoryId);

        // Get categories
        $categories = $this->categoryModel->getCategories('publikasi');

        $data = [
            'title' => 'Publikasi - Serikat Pekerja Kampus',
            'meta_description' => 'Publikasi dan dokumen resmi dari Serikat Pekerja Kampus (SPK)',
            'doc_type' => 'publikasi',
            'documents' => $documents,
            'categories' => $categories,
            'current_category' => $categoryId,
        ];

        return view('public/documents/index', $data);
    }

    /**
     * Regulasi (Regulations) Page
     */
    public function regulasi()
    {
        $categoryId = $this->request->getGet('category');

        // Get all regulations
        $documents = $this->documentModel->getPublishedDocuments('regulasi', $categoryId);

        // Get categories
        $categories = $this->categoryModel->getCategories('regulasi');

        $data = [
            'title' => 'Regulasi - Serikat Pekerja Kampus',
            'meta_description' => 'Regulasi, UU, PP, dan Permen terkait pekerja kampus',
            'doc_type' => 'regulasi',
            'documents' => $documents,
            'categories' => $categories,
            'current_category' => $categoryId,
        ];

        return view('public/documents/index', $data);
    }

    /**
     * Download Document
     */
    public function download($id)
    {
        $document = $this->documentModel->find($id);

        if (!$document) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Dokumen tidak ditemukan.');
        }

        // Check if published
        if ($document['status'] !== 'published') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Dokumen tidak tersedia.');
        }

        // Build file path
        $filepath = WRITEPATH . 'uploads/documents/' . $document['file_path'];

        // Check if file exists
        if (!file_exists($filepath)) {
            log_message('error', "Document file not found: {$filepath}");
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File dokumen tidak ditemukan di server.');
        }

        // Increment download count
        $this->documentModel->incrementDownloadCount($id);

        // Audit log
        $this->auditLog->insert([
            'actor_id' => session('member_id') ?? null,
            'actor_type' => session('member_id') ? 'member' : 'anonymous',
            'target_type' => 'document',
            'target_id' => $id,
            'action' => 'document.downloaded',
            'new_values' => json_encode([
                'title' => $document['title'],
                'doc_type' => $document['doc_type'],
            ]),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Serve file for download
        return $this->response
            ->download($filepath, null)
            ->setFileName($document['original_name']);
    }

    /**
     * View/Preview Document (if supported)
     */
    public function preview($id)
    {
        $document = $this->documentModel->find($id);

        if (!$document || $document['status'] !== 'published') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Dokumen tidak ditemukan.');
        }

        $filepath = WRITEPATH . 'uploads/documents/' . $document['file_path'];

        if (!file_exists($filepath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File dokumen tidak ditemukan di server.');
        }

        // For PDF files, we can serve inline
        if ($document['mime_type'] === 'application/pdf') {
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="' . $document['original_name'] . '"')
                ->setBody(file_get_contents($filepath));
        }

        // For other file types, redirect to download
        return redirect()->to("/documents/download/{$id}");
    }

    /**
     * Search Documents
     */
    public function search()
    {
        $keyword = $this->request->getGet('q');
        $docType = $this->request->getGet('type'); // publikasi or regulasi

        if (!$keyword) {
            return redirect()->back();
        }

        $builder = $this->documentModel
            ->where('status', 'published')
            ->groupStart()
                ->like('title', $keyword)
                ->orLike('description', $keyword)
            ->groupEnd();

        if ($docType && in_array($docType, ['publikasi', 'regulasi'])) {
            $builder->where('doc_type', $docType);
        }

        $documents = $builder->orderBy('published_at', 'DESC')
                            ->findAll();

        $data = [
            'title' => "Hasil Pencarian: {$keyword} - SPK",
            'meta_description' => "Hasil pencarian dokumen dengan kata kunci: {$keyword}",
            'keyword' => $keyword,
            'documents' => $documents,
            'doc_type' => $docType,
        ];

        return view('public/documents/search', $data);
    }
}
