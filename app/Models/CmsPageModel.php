<?php

namespace App\Models;

use CodeIgniter\Model;

class CmsPageModel extends Model
{
    protected $table = 'cms_pages';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'slug',
        'title',
        'content_html',
        'template',
        'status',
        'visibility',
        'primary_document_id',
        'published_at',
        'created_by',
        'updated_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'slug' => 'required|min_length[3]|max_length[100]|is_unique[cms_pages.slug,id,{id}]',
        'title' => 'required|min_length[3]|max_length[200]',
        'status' => 'required|in_list[draft,published,archived]',
        'template' => 'in_list[default,legal,contact]',
        'visibility' => 'in_list[public,member_only]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Get all published pages
     */
    public function getPublishedPages()
    {
        return $this->where('status', 'published')
                    ->where('published_at <=', date('Y-m-d H:i:s'))
                    ->orderBy('title', 'ASC')
                    ->findAll();
    }

    /**
     * Get page by slug
     */
    public function getPageBySlug($slug)
    {
        return $this->where('slug', $slug)
                    ->where('status', 'published')
                    ->first();
    }

    /**
     * Get public pages
     */
    public function getPublicPages()
    {
        return $this->where('status', 'published')
                    ->where('visibility', 'public')
                    ->where('published_at <=', date('Y-m-d H:i:s'))
                    ->orderBy('title', 'ASC')
                    ->findAll();
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
