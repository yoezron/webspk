<?php

namespace App\Models;

use CodeIgniter\Model;

class CmsMediaModel extends Model
{
    protected $table = 'cms_media';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'media_type',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'checksum_sha256',
        'alt_text',
        'uploaded_by'
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'media_type' => 'required|in_list[image,file]',
        'file_path' => 'required',
        'mime_type' => 'required',
        'file_size' => 'required|integer',
    ];

    /**
     * Get recent media
     */
    public function getRecentMedia($limit = 50)
    {
        return $this->orderBy('uploaded_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get media by type
     */
    public function getMediaByType($mediaType = 'image', $limit = 50)
    {
        return $this->where('media_type', $mediaType)
                    ->orderBy('uploaded_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Upload media
     */
    public function uploadMedia($data)
    {
        $data['uploaded_at'] = date('Y-m-d H:i:s');
        return $this->insert($data);
    }
}
