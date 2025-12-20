<?php

namespace App\Models;

use CodeIgniter\Model;

class CmsHomeSectionModel extends Model
{
    protected $table = 'cms_home_sections';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'section_key',
        'title',
        'body_html',
        'config_json',
        'sort_order',
        'is_enabled',
        'updated_by'
    ];

    protected $useTimestamps = false;
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'section_key' => 'required|max_length[50]|is_unique[cms_home_sections.section_key,id,{id}]',
    ];

    /**
     * Get enabled sections
     */
    public function getEnabledSections()
    {
        return $this->where('is_enabled', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Get section by key
     */
    public function getSectionByKey($key)
    {
        return $this->where('section_key', $key)->first();
    }

    /**
     * Update section
     */
    public function updateSection($key, $data, $userId = null)
    {
        $section = $this->getSectionByKey($key);
        if (!$section) {
            return false;
        }

        if ($userId) {
            $data['updated_by'] = $userId;
        }
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->update($section['id'], $data);
    }
}
