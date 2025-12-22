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
                // Personal Data
                'full_name' => 'required|min_length[3]|max_length[150]',
                'identity_number' => 'required|exact_length[16]|numeric',
                'gender' => 'required|in_list[L,P]',
                'birth_date' => 'required|valid_date',
                'birth_place' => 'required|max_length[100]',
                'phone_number' => 'required|min_length[10]|max_length[15]',
                'alt_phone_number' => 'permit_empty|min_length[10]|max_length[15]',

                // Address
                'address' => 'required|max_length[500]',
                'province' => 'required|max_length[100]',
                'city' => 'required|max_length[100]',
                'district' => 'required|max_length[100]',
                'postal_code' => 'required|exact_length[5]|numeric',

                // Emergency Contact
                'emergency_contact_name' => 'required|max_length[150]',
                'emergency_contact_relation' => 'required|max_length[50]',
                'emergency_contact_phone' => 'required|min_length[10]|max_length[15]',

                // Work Data
                'university_name' => 'required|max_length[150]',
                'campus_location' => 'required|max_length[100]',
                'faculty' => 'required|max_length[100]',
                'department' => 'required|max_length[100]',
                'work_unit' => 'permit_empty|max_length[100]',
                'employee_id_number' => 'required|max_length[50]',
                'lecturer_id_number' => 'permit_empty|max_length[50]',
                'academic_rank' => 'permit_empty|max_length[50]',
                'employment_status' => 'required|max_length[50]',
                'work_start_date' => 'required|valid_date',

                // Salary
                'gross_salary' => 'required|numeric',
                'functional_allowance' => 'permit_empty|numeric',
                'structural_allowance' => 'permit_empty|numeric',
                'other_allowances' => 'permit_empty|numeric',

                // Banking
                'bank_name' => 'required|max_length[100]',
                'bank_account_number' => 'required|max_length[50]',
                'bank_account_name' => 'required|max_length[150]',
                'npwp_number' => 'permit_empty|max_length[20]',
                'bpjs_tk_number' => 'permit_empty|max_length[20]',
                'bpjs_kes_number' => 'permit_empty|max_length[20]',

                // Education
                'education_level' => 'required|max_length[50]',
                'graduation_year' => 'required|numeric|greater_than[1950]|less_than_equal_to[' . date('Y') . ']',
                'institution_name' => 'required|max_length[150]',
                'field_of_study' => 'required|max_length[100]',
                'certifications' => 'permit_empty',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $postData = $this->request->getPost([
                'full_name', 'identity_number', 'gender', 'birth_date', 'birth_place',
                'phone_number', 'alt_phone_number', 'address', 'province', 'city',
                'district', 'postal_code', 'emergency_contact_name', 'emergency_contact_relation',
                'emergency_contact_phone', 'university_name', 'campus_location', 'faculty',
                'department', 'work_unit', 'employee_id_number', 'lecturer_id_number',
                'academic_rank', 'employment_status', 'work_start_date', 'gross_salary',
                'functional_allowance', 'structural_allowance', 'other_allowances',
                'bank_name', 'bank_account_number', 'bank_account_name', 'npwp_number',
                'bpjs_tk_number', 'bpjs_kes_number', 'education_level', 'graduation_year',
                'institution_name', 'field_of_study', 'certifications'
            ]);

            // Clean empty values
            foreach ($postData as $key => $value) {
                if ($value === '') {
                    $postData[$key] = null;
                }
            }

            // Handle document uploads
            $documentsPath = FCPATH . 'uploads/documents/';
            if (!is_dir($documentsPath)) {
                mkdir($documentsPath, 0777, true);
            }

            $documentFields = ['id_card_photo', 'family_card_photo', 'sk_pengangkatan_photo'];
            foreach ($documentFields as $field) {
                $file = $this->request->getFile($field);
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $newName = $field . '_' . $userId . '_' . time() . '.' . $file->getExtension();
                    if ($file->move($documentsPath, $newName)) {
                        // Delete old file if exists
                        if (!empty($member[$field]) && file_exists($documentsPath . $member[$field])) {
                            unlink($documentsPath . $member[$field]);
                        }
                        $postData[$field] = $newName;
                    }
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
            // Personal Data
            'full_name' => 'Nama Lengkap',
            'identity_number' => 'NIK',
            'gender' => 'Jenis Kelamin',
            'birth_date' => 'Tanggal Lahir',
            'birth_place' => 'Tempat Lahir',
            'phone_number' => 'Nomor Telepon',

            // Address
            'address' => 'Alamat',
            'province' => 'Provinsi',
            'city' => 'Kota',
            'district' => 'Kecamatan',
            'postal_code' => 'Kode Pos',

            // Emergency Contact
            'emergency_contact_name' => 'Nama Kontak Darurat',
            'emergency_contact_relation' => 'Hubungan Kontak Darurat',
            'emergency_contact_phone' => 'Telepon Kontak Darurat',

            // Work Data
            'university_name' => 'Nama Universitas',
            'campus_location' => 'Lokasi Kampus',
            'faculty' => 'Fakultas',
            'department' => 'Departemen',
            'employee_id_number' => 'NIP',
            'employment_status' => 'Status Kepegawaian',
            'work_start_date' => 'Tanggal Mulai Bekerja',

            // Salary
            'gross_salary' => 'Gaji Pokok',

            // Banking
            'bank_name' => 'Nama Bank',
            'bank_account_number' => 'Nomor Rekening',
            'bank_account_name' => 'Nama Pemilik Rekening',

            // Education
            'education_level' => 'Pendidikan Terakhir',
            'graduation_year' => 'Tahun Lulus',
            'institution_name' => 'Nama Institusi',
            'field_of_study' => 'Bidang Studi',

            // Documents
            'id_card_photo' => 'KTP',
            'family_card_photo' => 'Kartu Keluarga',
            'sk_pengangkatan_photo' => 'SK Pengangkatan',
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
