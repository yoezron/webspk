<?php

namespace App\Models;

use CodeIgniter\Model;

class CmsNewsPostModel extends Model
{
    protected $table = 'cms_news_posts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'title',
        'slug',
        'excerpt',
        'content_html',
        'cover_image_id',
        'status',
        'published_at',
        'author_id',
        'view_count'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'slug' => 'required|is_unique[cms_news_posts.slug,id,{id}]',
        'content_html' => 'required',
        'author_id' => 'required|integer',
        'status' => 'required|in_list[draft,published,archived]',
    ];

    /**
     * Get published posts
     */
    public function getPublishedPosts($perPage = 12)
    {
        return $this->select('cms_news_posts.*, sp_members.full_name as author_name')
                    ->join('sp_members', 'sp_members.id = cms_news_posts.author_id', 'left')
                    ->where('cms_news_posts.status', 'published')
                    ->where('cms_news_posts.published_at <=', date('Y-m-d H:i:s'))
                    ->orderBy('cms_news_posts.published_at', 'DESC')
                    ->paginate($perPage);
    }

    /**
     * Get latest posts
     */
    public function getLatestPosts($limit = 3)
    {
        return $this->select('cms_news_posts.*, sp_members.full_name as author_name')
                    ->join('sp_members', 'sp_members.id = cms_news_posts.author_id', 'left')
                    ->where('cms_news_posts.status', 'published')
                    ->where('cms_news_posts.published_at <=', date('Y-m-d H:i:s'))
                    ->orderBy('cms_news_posts.published_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get post by slug
     */
    public function getPostBySlug($slug)
    {
        return $this->select('cms_news_posts.*, sp_members.full_name as author_name, sp_members.email as author_email')
                    ->join('sp_members', 'sp_members.id = cms_news_posts.author_id', 'left')
                    ->where('cms_news_posts.slug', $slug)
                    ->where('cms_news_posts.status', 'published')
                    ->first();
    }

    /**
     * Get related posts
     */
    public function getRelatedPosts($currentId, $limit = 3)
    {
        return $this->where('status', 'published')
                    ->where('id !=', $currentId)
                    ->orderBy('published_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Increment view count
     */
    public function incrementViewCount($id)
    {
        return $this->set('view_count', 'view_count + 1', false)
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
