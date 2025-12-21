<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\CmsNewsPostModel;

class NewsController extends BaseController
{
    protected $newsModel;
    protected $perPage = 12;

    public function __construct()
    {
        $this->newsModel = new CmsNewsPostModel();
    }

    /**
     * News Listing Page
     */
    public function index()
    {
        $currentPage = $this->request->getVar('page') ?? 1;

        // Get paginated news posts
        $posts = $this->newsModel->getPublishedPosts($this->perPage);

        $data = [
            'title' => 'Berita - Serikat Pekerja Kampus',
            'meta_description' => 'Berita dan artikel terbaru dari Serikat Pekerja Kampus (SPK)',
            'posts' => $posts,
            'pager' => $this->newsModel->pager,
            'current_page' => $currentPage,
        ];

        return view('public/news/index', $data);
    }

    /**
     * News Detail Page
     */
    public function show($slug)
    {
        $post = $this->newsModel->getPostBySlug($slug);

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Berita '$slug' tidak ditemukan.");
        }

        // Increment view count
        $this->newsModel->incrementViewCount($post['id']);

        // Get related posts
        $relatedPosts = $this->newsModel->getRelatedPosts($post['id'], 3);

        // Generate meta description from excerpt or content
        $metaDescription = $post['excerpt']
            ? strip_tags($post['excerpt'])
            : strip_tags(substr($post['content_html'], 0, 160));

        $data = [
            'title' => $post['title'] . ' - Berita SPK',
            'meta_description' => $metaDescription,
            'post' => $post,
            'related_posts' => $relatedPosts,
        ];

        return view('public/news/show', $data);
    }

    /**
     * News by Year Archive
     */
    public function archive($year = null)
    {
        $year = $year ?? date('Y');

        $posts = $this->newsModel
            ->select('cms_news_posts.*, sp_members.full_name as author_name')
            ->join('sp_members', 'sp_members.id = cms_news_posts.author_id', 'left')
            ->where('cms_news_posts.status', 'published')
            ->where('YEAR(cms_news_posts.published_at)', $year)
            ->orderBy('cms_news_posts.published_at', 'DESC')
            ->paginate($this->perPage);

        $data = [
            'title' => "Arsip Berita Tahun {$year} - SPK",
            'meta_description' => "Arsip berita Serikat Pekerja Kampus tahun {$year}",
            'posts' => $posts,
            'pager' => $this->newsModel->pager,
            'year' => $year,
        ];

        return view('public/news/archive', $data);
    }

    /**
     * Get available archive years
     */
    public function getArchiveYears()
    {
        $years = $this->newsModel
            ->select('YEAR(published_at) as year')
            ->where('status', 'published')
            ->distinct()
            ->orderBy('year', 'DESC')
            ->findAll();

        return array_column($years, 'year');
    }
}
