<?php

namespace App\Models;

use CodeIgniter\Model;

class CmsDocumentModel extends Model
{
    protected $table = 'cms_documents';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'doc_type',
        'title',
        'slug',
        'description',
        'category_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'checksum_sha256',
        'status',
        'published_at',
        'download_count',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'doc_type' => 'required|in_list[publikasi,regulasi]',
        'title' => 'required|min_length[3]|max_length[255]',
        'file_path' => 'required',
        'original_name' => 'required',
        'status' => 'required|in_list[draft,published,archived]',
    ];

    /**
     * Get published documents by type
     */
    public function getPublishedDocuments($docType, $categoryId = null, $limit = null)
    {
        $builder = $this->where('doc_type', $docType)
                        ->where('status', 'published')
                        ->where('published_at <=', date('Y-m-d H:i:s'));

        if ($categoryId) {
            $builder->where('category_id', $categoryId);
        }

        $builder->orderBy('published_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Get latest publications
     */
    public function getLatestPublications($limit = 6)
    {
        return $this->getPublishedDocuments('publikasi', null, $limit);
    }

    /**
     * Increment download count
     */
    public function incrementDownloadCount($id)
    {
        return $this->set('download_count', 'download_count + 1', false)
                    ->where('id', $id)
                    ->update();
    }

    /**
     * Generate unique slug
     */
    public function generateSlug($title, $id = null)
    {
        $slug = url_title($title, '-', true);
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = $this->where('slug', $slug);
            if ($id) {
                $query->where('id !=', $id);
            }

            if (!$query->first()) {
                break;
            }

            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
