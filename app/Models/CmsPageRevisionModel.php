<?php

namespace App\Models;

use CodeIgniter\Model;

class CmsPageRevisionModel extends Model
{
    protected $table = 'cms_page_revisions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'page_id',
        'content_html',
        'note',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = false;

    protected $validationRules = [
        'page_id' => 'required|integer',
        'content_html' => 'required',
    ];

    /**
     * Get revisions for a page
     */
    public function getPageRevisions($pageId, $limit = 10)
    {
        return $this->where('page_id', $pageId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Create revision before updating page
     */
    public function createRevision($pageId, $contentHtml, $note = null, $userId = null)
    {
        return $this->insert([
            'page_id' => $pageId,
            'content_html' => $contentHtml,
            'note' => $note,
            'created_by' => $userId
        ]);
    }
}
