<?php

namespace App\Controllers\Admin\CMS;

use App\Controllers\BaseController;
use App\Models\CmsOfficerModel;
use App\Models\MemberModel;
use App\Models\AuditLogModel;

class OfficerController extends BaseController
{
    protected $officerModel;
    protected $memberModel;
    protected $auditLog;

    public function __construct()
    {
        $this->officerModel = new CmsOfficerModel();
        $this->memberModel = new MemberModel();
        $this->auditLog = new AuditLogModel();
    }

    /**
     * List all officers
     */
    public function index()
    {
        $level = $this->request->getGet('level'); // pusat or wilayah
        $perPage = 20;

        $builder = $this->officerModel
            ->select('cms_officers.*, sp_members.full_name, sp_members.email')
            ->join('sp_members', 'sp_members.id = cms_officers.member_id', 'left');

        if ($level && in_array($level, ['pusat', 'wilayah'])) {
            $builder->where('cms_officers.level', $level);
        }

        $officers = $builder->orderBy('cms_officers.sort_order', 'ASC')
                           ->orderBy('cms_officers.level', 'ASC')
                           ->paginate($perPage);

        $data = [
            'title' => 'Kelola Pengurus - CMS Admin',
            'officers' => $officers,
            'pager' => $this->officerModel->pager,
            'level' => $level,
        ];

        return view('admin/cms/officers/index', $data);
    }

    /**
     * Create new officer form
     */
    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            return $this->processCreate();
        }

        // Get all active members for selection
        $members = $this->memberModel
            ->select('id, full_name, email, region_code')
            ->where('membership_status', 'active')
            ->orderBy('full_name', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Tambah Pengurus - CMS Admin',
            'members' => $members,
        ];

        return view('admin/cms/officers/create', $data);
    }

    /**
     * Process create officer
     */
    protected function processCreate()
    {
        $rules = [
            'member_id' => 'required|integer|is_not_unique[sp_members.id]',
            'level' => 'required|in_list[pusat,wilayah]',
            'position_name' => 'required|min_length[3]|max_length[100]',
            'region_code' => 'permit_empty|max_length[10]',
            'sort_order' => 'permit_empty|integer',
            'period_start' => 'permit_empty|valid_date',
            'period_end' => 'permit_empty|valid_date',
            'photo' => 'permit_empty|max_size[photo,2048]|is_image[photo]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'member_id' => $this->request->getPost('member_id'),
            'level' => $this->request->getPost('level'),
            'position_name' => $this->request->getPost('position_name'),
            'region_code' => $this->request->getPost('region_code'),
            'sort_order' => $this->request->getPost('sort_order') ?: 0,
            'period_start' => $this->request->getPost('period_start'),
            'period_end' => $this->request->getPost('period_end'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        // Handle photo upload
        $file = $this->request->getFile('photo');
        if ($file && $file->isValid()) {
            try {
                $newName = $file->getRandomName();
                $uploadPath = WRITEPATH . 'uploads/officers/';

                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $newName);
                $data['photo_path'] = $newName;
            } catch (\Exception $e) {
                log_message('error', 'Officer photo upload error: ' . $e->getMessage());
            }
        }

        try {
            $id = $this->officerModel->insert($data);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_officer',
                'target_id' => $id,
                'action' => 'cms.officer.created',
                'new_values' => json_encode($data),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/cms/officers')
                           ->with('success', 'Pengurus berhasil ditambahkan.');
        } catch (\Exception $e) {
            log_message('error', 'Officer creation error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat menambah pengurus.');
        }
    }

    /**
     * Edit officer form
     */
    public function edit($id)
    {
        $officer = $this->officerModel->find($id);

        if (!$officer) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($this->request->getMethod() === 'post') {
            return $this->processEdit($id, $officer);
        }

        // Get all active members for selection
        $members = $this->memberModel
            ->select('id, full_name, email, region_code')
            ->where('membership_status', 'active')
            ->orderBy('full_name', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Edit Pengurus - CMS Admin',
            'officer' => $officer,
            'members' => $members,
        ];

        return view('admin/cms/officers/edit', $data);
    }

    /**
     * Process edit officer
     */
    protected function processEdit($id, $oldOfficer)
    {
        $rules = [
            'member_id' => 'required|integer|is_not_unique[sp_members.id]',
            'level' => 'required|in_list[pusat,wilayah]',
            'position_name' => 'required|min_length[3]|max_length[100]',
            'region_code' => 'permit_empty|max_length[10]',
            'sort_order' => 'permit_empty|integer',
            'period_start' => 'permit_empty|valid_date',
            'period_end' => 'permit_empty|valid_date',
            'photo' => 'permit_empty|max_size[photo,2048]|is_image[photo]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'member_id' => $this->request->getPost('member_id'),
            'level' => $this->request->getPost('level'),
            'position_name' => $this->request->getPost('position_name'),
            'region_code' => $this->request->getPost('region_code'),
            'sort_order' => $this->request->getPost('sort_order') ?: 0,
            'period_start' => $this->request->getPost('period_start'),
            'period_end' => $this->request->getPost('period_end'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        // Handle photo replacement
        $file = $this->request->getFile('photo');
        if ($file && $file->isValid()) {
            try {
                $newName = $file->getRandomName();
                $uploadPath = WRITEPATH . 'uploads/officers/';

                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $newName);

                // Delete old photo
                if (!empty($oldOfficer['photo_path'])) {
                    $oldPhotoPath = $uploadPath . $oldOfficer['photo_path'];
                    if (file_exists($oldPhotoPath)) {
                        @unlink($oldPhotoPath);
                    }
                }

                $data['photo_path'] = $newName;
            } catch (\Exception $e) {
                log_message('error', 'Officer photo replacement error: ' . $e->getMessage());
            }
        }

        try {
            $this->officerModel->update($id, $data);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_officer',
                'target_id' => $id,
                'action' => 'cms.officer.updated',
                'old_values' => json_encode($oldOfficer),
                'new_values' => json_encode($data),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/cms/officers')
                           ->with('success', 'Pengurus berhasil diupdate.');
        } catch (\Exception $e) {
            log_message('error', 'Officer update error: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Terjadi kesalahan saat mengupdate pengurus.');
        }
    }

    /**
     * Delete officer
     */
    public function delete($id)
    {
        $officer = $this->officerModel->find($id);

        if (!$officer) {
            return redirect()->back()
                           ->with('error', 'Pengurus tidak ditemukan.');
        }

        try {
            // Delete photo file if exists
            if (!empty($officer['photo_path'])) {
                $photoPath = WRITEPATH . 'uploads/officers/' . $officer['photo_path'];
                if (file_exists($photoPath)) {
                    @unlink($photoPath);
                }
            }

            $this->officerModel->delete($id);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_officer',
                'target_id' => $id,
                'action' => 'cms.officer.deleted',
                'old_values' => json_encode($officer),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->back()
                           ->with('success', 'Pengurus berhasil dihapus.');
        } catch (\Exception $e) {
            log_message('error', 'Officer deletion error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat menghapus pengurus.');
        }
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $officer = $this->officerModel->find($id);

        if (!$officer) {
            return redirect()->back()
                           ->with('error', 'Pengurus tidak ditemukan.');
        }

        $newStatus = $officer['is_active'] ? 0 : 1;

        try {
            $this->officerModel->update($id, ['is_active' => $newStatus]);

            // Audit log
            $this->auditLog->insert([
                'actor_id' => session('member_id'),
                'target_type' => 'cms_officer',
                'target_id' => $id,
                'action' => 'cms.officer.toggled',
                'old_values' => json_encode(['is_active' => $officer['is_active']]),
                'new_values' => json_encode(['is_active' => $newStatus]),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $message = $newStatus ? 'Pengurus diaktifkan.' : 'Pengurus dinonaktifkan.';
            return redirect()->back()
                           ->with('success', $message);
        } catch (\Exception $e) {
            log_message('error', 'Officer toggle error: ' . $e->getMessage());
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan.');
        }
    }
}
