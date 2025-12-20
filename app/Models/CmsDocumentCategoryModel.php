<?php

namespace App\Models;

use CodeIgniter\Model;

class CmsDocumentCategoryModel extends Model
{
    protected $table = 'cms_document_categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'doc_type',
        'name',
        'slug',
        'sort_order'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'doc_type' => 'required|in_list[publikasi,regulasi]',
        'name' => 'required|max_length[100]',
        'slug' => 'required|max_length[100]',
    ];

    /**
     * Get categories by document type
     */
    public function getCategories($docType)
    {
        return $this->where('doc_type', $docType)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Generate unique slug for category
     */
    public function generateSlug($name, $docType, $id = null)
    {
        $slug = url_title($name, '-', true);
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = $this->where('slug', $slug)
                         ->where('doc_type', $docType);
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
