<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberModel;
use App\Models\AuditLogModel;

class ProfileController extends BaseController
{
    protected $memberModel;
    protected $auditModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->auditModel = new AuditLogModel();
        helper(['form', 'url', 'filesystem']);
    }

    /**
     * View admin profile
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $member = $this->memberModel->find($userId);

        if (!$member) {
            return redirect()->to(base_url('login'));
        }

        // Calculate profile completion
        $completion = $this->calculateProfileCompletion($member);

        $data = [
            'title' => 'Profil Saya',
            'member' => $member,
            'completion' => $completion,
        ];

        return view('admin/profile/index', $data);
    }

    /**
     * Edit admin profile
     */
    public function edit()
    {
        $userId = session()->get('user_id');
        $member = $this->memberModel->find($userId);

        if (!$member) {
            return redirect()->to(base_url('login'));
        }

        if ($this->request->is('post')) {
            $rules = [
                'full_name' => 'required|min_length[3]|max_length[100]',
                'phone_number' => 'permit_empty|min_length[10]|max_length[15]',
                'address' => 'permit_empty|max_length[255]',
                'date_of_birth' => 'permit_empty|valid_date',
                'place_of_birth' => 'permit_empty|max_length[100]',
                'gender' => 'required|in_list[male,female]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $postData = $this->request->getPost([
                'full_name',
                'phone_number',
                'address',
                'date_of_birth',
                'place_of_birth',
                'gender',
            ]);

            // Clean empty values
            foreach ($postData as $key => $value) {
                if ($value === '') {
                    $postData[$key] = null;
                }
            }

            if ($this->memberModel->update($userId, $postData)) {
                // Audit log
                helper('audit');
                audit_log(
                    $userId,
                    'profile.updated',
                    'sp_members',
                    $userId,
                    'Admin updated their profile'
                );

                return redirect()->to(base_url('admin/profile'))
                    ->with('success', 'Profil berhasil diperbarui');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui profil');
        }

        $data = [
            'title' => 'Edit Profil',
            'member' => $member,
        ];

        return view('admin/profile/edit', $data);
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        $userId = session()->get('user_id');

        if ($this->request->is('post')) {
            $rules = [
                'current_password' => 'required',
                'new_password' => 'required|min_length[8]',
                'confirm_password' => 'required|matches[new_password]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()
                    ->with('errors', $this->validator->getErrors());
            }

            $member = $this->memberModel->find($userId);

            // Verify current password
            if (!password_verify($this->request->getPost('current_password'), $member['password'])) {
                return redirect()->back()
                    ->with('error', 'Password saat ini tidak sesuai');
            }

            // Update password
            $newPassword = password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT);

            if ($this->memberModel->update($userId, ['password' => $newPassword])) {
                // Audit log
                helper('audit');
                audit_log(
                    $userId,
                    'password.changed',
                    'sp_members',
                    $userId,
                    'Admin changed password'
                );

                return redirect()->to(base_url('admin/profile'))
                    ->with('success', 'Password berhasil diubah');
            }

            return redirect()->back()
                ->with('error', 'Gagal mengubah password');
        }

        $data = [
            'title' => 'Ubah Password',
        ];

        return view('admin/profile/change_password', $data);
    }

    /**
     * Upload profile photo
     */
    public function uploadPhoto()
    {
        if (!$this->request->is('post')) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        $userId = session()->get('user_id');
        $member = $this->memberModel->find($userId);

        $validationRule = [
            'photo' => [
                'label' => 'Photo',
                'rules' => 'uploaded[photo]'
                    . '|is_image[photo]'
                    . '|mime_in[photo,image/jpg,image/jpeg,image/png]'
                    . '|max_size[photo,2048]', // 2MB max
            ],
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->back()
                ->with('error', $this->validator->getError('photo'));
        }

        $photo = $this->request->getFile('photo');

        if (!$photo->isValid()) {
            return redirect()->back()
                ->with('error', 'File upload gagal');
        }

        // Generate unique filename
        $newName = 'profile_' . $userId . '_' . time() . '.' . $photo->getExtension();

        // Create directory if not exists
        $uploadPath = FCPATH . 'uploads/profiles/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Move file
        if ($photo->move($uploadPath, $newName)) {
            // Delete old photo if exists
            if (!empty($member['profile_photo']) && file_exists($uploadPath . $member['profile_photo'])) {
                unlink($uploadPath . $member['profile_photo']);
            }

            // Update database
            if ($this->memberModel->update($userId, ['profile_photo' => $newName])) {
                // Audit log
                helper('audit');
                audit_log(
                    $userId,
                    'profile.photo.uploaded',
                    'sp_members',
                    $userId,
                    'Admin uploaded profile photo'
                );

                return redirect()->to(base_url('admin/profile'))
                    ->with('success', 'Foto profil berhasil diupload');
            }

            // Clean up if database update fails
            unlink($uploadPath . $newName);
            return redirect()->back()
                ->with('error', 'Gagal menyimpan foto profil');
        }

        return redirect()->back()
            ->with('error', 'Gagal mengupload foto');
    }

    /**
     * Delete profile photo
     */
    public function deletePhoto()
    {
        if (!$this->request->is('post')) {
            return redirect()->back()->with('error', 'Invalid request');
        }

        $userId = session()->get('user_id');
        $member = $this->memberModel->find($userId);

        if (empty($member['profile_photo'])) {
            return redirect()->back()
                ->with('error', 'Tidak ada foto profil');
        }

        $uploadPath = FCPATH . 'uploads/profiles/';
        $photoPath = $uploadPath . $member['profile_photo'];

        // Delete file if exists
        if (file_exists($photoPath)) {
            unlink($photoPath);
        }

        // Update database
        if ($this->memberModel->update($userId, ['profile_photo' => null])) {
            // Audit log
            helper('audit');
            audit_log(
                $userId,
                'profile.photo.deleted',
                'sp_members',
                $userId,
                'Admin deleted profile photo'
            );

            return redirect()->to(base_url('admin/profile'))
                ->with('success', 'Foto profil berhasil dihapus');
        }

        return redirect()->back()
            ->with('error', 'Gagal menghapus foto profil');
    }

    /**
     * Calculate profile completion percentage
     */
    private function calculateProfileCompletion(array $member): array
    {
        $fields = [
            'full_name' => 'Nama Lengkap',
            'email' => 'Email',
            'phone_number' => 'Nomor Telepon',
            'address' => 'Alamat',
            'date_of_birth' => 'Tanggal Lahir',
            'place_of_birth' => 'Tempat Lahir',
            'gender' => 'Jenis Kelamin',
            'profile_photo' => 'Foto Profil',
        ];

        $completed = 0;
        $total = count($fields);
        $missing = [];

        foreach ($fields as $field => $label) {
            if (!empty($member[$field])) {
                $completed++;
            } else {
                $missing[] = $label;
            }
        }

        $percentage = ($completed / $total) * 100;

        return [
            'percentage' => $percentage,
            'completed' => $completed,
            'total' => $total,
            'missing' => $missing,
        ];
    }
}
