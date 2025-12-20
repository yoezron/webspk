<?php

namespace App\Models;

use CodeIgniter\Model;

class CmsOfficerModel extends Model
{
    protected $table = 'cms_officers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'member_id',
        'full_name',
        'position_title',
        'level',
        'region_code',
        'photo_media_id',
        'bio_html',
        'sort_order',
        'is_active',
        'period_start',
        'period_end'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'full_name' => 'required|min_length[3]|max_length[150]',
        'position_title' => 'required|max_length[100]',
        'level' => 'required|in_list[pusat,wilayah]',
    ];

    /**
     * Get active officers by level
     */
    public function getActiveOfficers($level = 'pusat', $regionCode = null)
    {
        $builder = $this->select('cms_officers.*, cms_media.file_path as photo_path')
                        ->join('cms_media', 'cms_media.id = cms_officers.photo_media_id', 'left')
                        ->where('cms_officers.is_active', 1)
                        ->where('cms_officers.level', $level);

        if ($regionCode && $level === 'wilayah') {
            $builder->where('cms_officers.region_code', $regionCode);
        }

        return $builder->orderBy('cms_officers.sort_order', 'ASC')
                       ->findAll();
    }

    /**
     * Get officer by member ID
     */
    public function getOfficerByMemberId($memberId)
    {
        return $this->where('member_id', $memberId)
                    ->where('is_active', 1)
                    ->first();
    }

    /**
     * Get all officers (with pagination)
     */
    public function getAllOfficers($perPage = 20)
    {
        return $this->select('cms_officers.*, sp_members.email as member_email, cms_media.file_path as photo_path')
                    ->join('sp_members', 'sp_members.id = cms_officers.member_id', 'left')
                    ->join('cms_media', 'cms_media.id = cms_officers.photo_media_id', 'left')
                    ->orderBy('cms_officers.level', 'ASC')
                    ->orderBy('cms_officers.sort_order', 'ASC')
                    ->paginate($perPage);
    }
}
