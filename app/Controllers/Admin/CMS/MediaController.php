<?php

namespace App\Controllers\Admin\CMS;

use App\Controllers\BaseController;
use App\Models\CmsMediaModel;
use App\Models\AuditLogModel;

class MediaController extends BaseController
{
    protected $mediaModel;
    protected $auditLog;

    public function __construct()
    {
        $this->mediaModel = new CmsMediaModel();
        $this->auditLog = new AuditLogModel();
    }

    /**
     * Media library index
     */
    public function index()
    {
        $mediaType = $this->request->getGet('type'); // image, video, document, other
        $perPage = 24; // Grid view

        $builder = $this->mediaModel
            ->select('cms_media.*, sp_members.full_name as uploader_name')
            ->join('sp_members', 'sp_members.id = cms_media.uploaded_by', 'left');

        if ($mediaType && in_array($mediaType, ['image', 'video', 'document', 'other'])) {
            $builder->where('cms_media.media_type', $mediaType);
        }

        $media = $builder->orderBy('cms_media.created_at', 'DESC')
                        ->paginate($perPage);

        $data = [
            'title' => 'Media Library - CMS Admin',
            'media' => $media,
            'pager' => $this->mediaModel->pager,
            'media_type' => $mediaType,
        ];

        return view('admin/cms/media/index', $data);
    }

    /**
     * Upload media
     */
    public function upload()
    {
        if ($this->request->getMethod() === 'post') {
            return $this->processUpload();
        }

        $data = [
            'title' => 'Upload Media - CMS Admin',
        ];

        return view('admin/cms/media/upload', $data);
    }

    /**
     * Process media upload
     */
    protected function processUpload()
    {
        $rules = [
            'media_file' => 'uploaded[media_file]|max_size[media_file,10240]',
            'title' => 'permit_empty|max_length[255]',
            'alt_text' => 'permit_empty|max_length[255]',
            'caption' => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('media_file');

        if (!$file->isValid()) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'File upload tidak valid.');
        }

        try {
            // Determine media type
            $mimeType = $file->getClientMimeType();
            $mediaType = $this->getMediaType($mimeType);

            // Generate unique filename
            $newName = $file->getRandomName();

            // Organize by media type
            $uploadPath = WRITEPATH . 'uploads/media/' . $mediaType . '/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $file->move($uploadPath, $newName);

            // Calculate checksum
            $filepath = $uploadPath . $newName;
            $checksum = hash_file('sha256', $filepath);

            // Get file dimensions for images
            $width = null;
            $height = null;
            if ($mediaType === 'image') {
                $imageInfo = getimagesize($filepath);
                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                }
            }

            $data = [
                'media_type' => $mediaType,
                'file_path' => $mediaType . '/' . $newName,
                'original_name' => $file->getClientName(),
                'mime_type' => $mimeType,
                'file_size' => $file->getSize(),
                'title' => $this->request->getPost('title') ?: $file->getClientName(),
                'alt_text' => $this->request->getPost('alt_text'),
                'caption' => $this->request->getPost('caption'),
                'width' => $width,
                'height' => $height,
                'checksum_sha256' => $checksum,
                'uploaded_by' => session('member_id'),
            ];

            $id = $this->mediaModel->insert($data);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_media',
                'target_id' => $id,
                'action' => 'cms.media.uploaded',
                'new_values' => json_encode($data),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/cms/media')
                           ->with('success', 'Media berhasil diupload.');
        } catch (\Exception $e) {
            log_message('error', 'Media upload error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat mengupload media.');
        }
    }

    /**
     * AJAX upload for better UX
     */
    public function ajaxUpload()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ])->setStatusCode(400);
        }

        $file = $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File upload tidak valid'
            ])->setStatusCode(400);
        }

        try {
            $mimeType = $file->getClientMimeType();
            $mediaType = $this->getMediaType($mimeType);
            $newName = $file->getRandomName();

            $uploadPath = WRITEPATH . 'uploads/media/' . $mediaType . '/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $file->move($uploadPath, $newName);
            $filepath = $uploadPath . $newName;
            $checksum = hash_file('sha256', $filepath);

            $width = null;
            $height = null;
            if ($mediaType === 'image') {
                $imageInfo = getimagesize($filepath);
                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                }
            }

            $data = [
                'media_type' => $mediaType,
                'file_path' => $mediaType . '/' . $newName,
                'original_name' => $file->getClientName(),
                'mime_type' => $mimeType,
                'file_size' => $file->getSize(),
                'title' => $file->getClientName(),
                'width' => $width,
                'height' => $height,
                'checksum_sha256' => $checksum,
                'uploaded_by' => session('member_id'),
            ];

            $id = $this->mediaModel->insert($data);
            $media = $this->mediaModel->find($id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Media uploaded successfully',
                'media' => $media
            ]);
        } catch (\Exception $e) {
            log_message('error', 'AJAX media upload error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Upload failed'
            ])->setStatusCode(500);
        }
    }

    /**
     * Edit media metadata
     */
    public function edit($id)
    {
        $media = $this->mediaModel->find($id);

        if (!$media) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->request->getMethod() === 'post') {
            return $this->processEdit($id, $media);
        }

        $data = [
            'title' => 'Edit Media - CMS Admin',
            'media' => $media,
        ];

        return view('admin/cms/media/edit', $data);
    }

    /**
     * Process edit media metadata
     */
    protected function processEdit($id, $oldMedia)
    {
        $rules = [
            'title' => 'required|max_length[255]',
            'alt_text' => 'permit_empty|max_length[255]',
            'caption' => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'alt_text' => $this->request->getPost('alt_text'),
            'caption' => $this->request->getPost('caption'),
        ];

        try {
            $this->mediaModel->update($id, $data);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_media',
                'target_id' => $id,
                'action' => 'cms.media.updated',
                'old_values' => json_encode($oldMedia),
                'new_values' => json_encode($data),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/cms/media')
                           ->with('success', 'Media berhasil diupdate.');
        } catch (\Exception $e) {
            log_message('error', 'Media update error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat mengupdate media.');
        }
    }

    /**
     * Delete media
     */
    public function delete($id)
    {
        $media = $this->mediaModel->find($id);

        if (!$media) {
            return redirect()->back()
                           ->with('error', 'Media tidak ditemukan.');
        }

        try {
            // Delete physical file
            $filepath = WRITEPATH . 'uploads/media/' . $media['file_path'];
            if (file_exists($filepath)) {
                @unlink($filepath);
            }

            $this->mediaModel->delete($id);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_media',
                'target_id' => $id,
                'action' => 'cms.media.deleted',
                'old_values' => json_encode($media),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->back()
                           ->with('success', 'Media berhasil dihapus.');
        } catch (\Exception $e) {
            log_message('error', 'Media deletion error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menghapus media.');
        }
    }

    /**
     * Get media type from MIME type
     */
    protected function getMediaType($mimeType)
    {
        if (strpos($mimeType, 'image/') === 0) {
            return 'image';
        } elseif (strpos($mimeType, 'video/') === 0) {
            return 'video';
        } elseif (in_array($mimeType, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])) {
            return 'document';
        } else {
            return 'other';
        }
    }

    /**
     * Get media URL for API/AJAX requests
     */
    public function getUrl($id)
    {
        $media = $this->mediaModel->find($id);

        if (!$media) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Media not found'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'media' => $media,
            'url' => base_url('uploads/media/' . $media['file_path'])
        ]);
    }
}
