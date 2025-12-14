<?php

namespace App\Controllers;

use App\Models\MemberModel;

class Register extends BaseController
{
    protected $memberModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        helper(['form', 'url', 'upload', 'app']);
    }

    /**
     * Display registration page - Step 1: Email & Password
     */
    public function index()
    {
        // Redirect if already logged in
        if (session()->has('user_id')) {
            return redirect()->to(base_url('dashboard'));
        }

        $data = [
            'title' => 'Pendaftaran Anggota',
            'description' => 'Daftar sebagai anggota Serikat Pekerja Kampus',
            'validation' => \Config\Services::validation(),
        ];

        return view('public/register_step1', $data);
    }

    /**
     * Process Step 1: Create account with email & password
     */
    public function processStep1()
    {
        $rules = [
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|is_unique[sp_members.email]',
                'errors' => [
                    'required' => 'Email wajib diisi',
                    'valid_email' => 'Email tidak valid',
                    'is_unique' => 'Email sudah terdaftar',
                ],
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
                'errors' => [
                    'required' => 'Password wajib diisi',
                    'min_length' => 'Password minimal 8 karakter',
                    'regex_match' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus',
                ],
            ],
            'password_confirm' => [
                'label' => 'Konfirmasi Password',
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Konfirmasi password wajib diisi',
                    'matches' => 'Konfirmasi password tidak sama',
                ],
            ],
            'full_name' => [
                'label' => 'Nama Lengkap',
                'rules' => 'required|min_length[3]|max_length[150]',
                'errors' => [
                    'required' => 'Nama lengkap wajib diisi',
                    'min_length' => 'Nama lengkap minimal 3 karakter',
                    'max_length' => 'Nama lengkap maksimal 150 karakter',
                ],
            ],
            'phone_number' => [
                'label' => 'Nomor HP',
                'rules' => 'required|numeric|min_length[10]|max_length[15]',
                'errors' => [
                    'required' => 'Nomor HP wajib diisi',
                    'numeric' => 'Nomor HP harus berupa angka',
                    'min_length' => 'Nomor HP minimal 10 digit',
                    'max_length' => 'Nomor HP maksimal 15 digit',
                ],
            ],
            'university_name' => [
                'label' => 'Nama Universitas',
                'rules' => 'required|min_length[3]|max_length[150]',
                'errors' => [
                    'required' => 'Nama universitas wajib diisi',
                    'min_length' => 'Nama universitas minimal 3 karakter',
                    'max_length' => 'Nama universitas maksimal 150 karakter',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Create initial member record
        $memberData = [
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'full_name' => $this->request->getPost('full_name'),
            'phone_number' => $this->request->getPost('phone_number'),
            'university_name' => $this->request->getPost('university_name'),
            'role' => 'candidate',
            'membership_status' => 'candidate',
            'onboarding_state' => 'registered',
            'account_status' => 'pending',
        ];

        $memberId = $this->memberModel->insert($memberData);

        if (!$memberId) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat akun. Silakan coba lagi');
        }

        // Get created member
        $member = $this->memberModel->find($memberId);

        // Set session for continuation
        session()->set([
            'registration_member_id' => $memberId,
            'registration_member_uuid' => $member['uuid'],
            'registration_step' => 2,
        ]);

        return redirect()->to(base_url('registrasi/step-2'))->with('success', 'Akun berhasil dibuat. Silakan lengkapi data diri Anda');
    }

    /**
     * Display Step 2: Personal Data
     */
    public function step2()
    {
        // Check if step 1 completed
        if (!session()->has('registration_member_id')) {
            return redirect()->to(base_url('registrasi'))->with('error', 'Silakan mulai dari langkah pertama');
        }

        $data = [
            'title' => 'Pendaftaran - Data Pribadi',
            'description' => 'Lengkapi data pribadi Anda',
            'validation' => \Config\Services::validation(),
            'step' => 2,
        ];

        return view('public/register_step2', $data);
    }

    /**
     * Process Step 2: Save personal data
     */
    public function processStep2()
    {
        if (!session()->has('registration_member_id')) {
            return redirect()->to(base_url('registrasi'))->with('error', 'Silakan mulai dari langkah pertama');
        }

        $rules = [
            'gender' => 'required|in_list[L,P]',
            'birth_place' => 'required|min_length[3]|max_length[100]',
            'birth_date' => 'required|valid_date',
            'identity_number' => 'required|numeric|min_length[16]|max_length[16]',
            'address' => 'required|min_length[10]',
            'province' => 'required',
            'city' => 'required',
            'district' => 'required',
            'postal_code' => 'required|numeric|min_length[5]|max_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $memberId = session()->get('registration_member_id');

        $updateData = [
            'gender' => $this->request->getPost('gender'),
            'birth_place' => $this->request->getPost('birth_place'),
            'birth_date' => $this->request->getPost('birth_date'),
            'identity_number' => $this->request->getPost('identity_number'),
            'address' => $this->request->getPost('address'),
            'province' => $this->request->getPost('province'),
            'city' => $this->request->getPost('city'),
            'district' => $this->request->getPost('district'),
            'postal_code' => $this->request->getPost('postal_code'),
            'alt_phone_number' => $this->request->getPost('alt_phone_number'),
            'emergency_contact_name' => $this->request->getPost('emergency_contact_name'),
            'emergency_contact_relation' => $this->request->getPost('emergency_contact_relation'),
            'emergency_contact_phone' => $this->request->getPost('emergency_contact_phone'),
        ];

        $this->memberModel->update($memberId, $updateData);

        session()->set('registration_step', 3);

        return redirect()->to(base_url('registrasi/step-3'))->with('success', 'Data pribadi berhasil disimpan');
    }

    /**
     * Display Step 3: Professional Data
     */
    public function step3()
    {
        if (!session()->has('registration_member_id') || session()->get('registration_step') < 3) {
            return redirect()->to(base_url('registrasi'))->with('error', 'Silakan lengkapi langkah sebelumnya');
        }

        $data = [
            'title' => 'Pendaftaran - Data Pekerjaan',
            'description' => 'Lengkapi data pekerjaan Anda',
            'validation' => \Config\Services::validation(),
            'step' => 3,
        ];

        return view('public/register_step3', $data);
    }

    /**
     * Process Step 3: Save professional data & calculate dues
     */
    public function processStep3()
    {
        if (!session()->has('registration_member_id') || session()->get('registration_step') < 3) {
            return redirect()->to(base_url('registrasi'))->with('error', 'Silakan lengkapi langkah sebelumnya');
        }

        $rules = [
            'campus_location' => 'required',
            'faculty' => 'required',
            'department' => 'required',
            'academic_rank' => 'required',
            'employment_status' => 'required',
            'work_start_date' => 'required|valid_date',
            'dues_rate_type' => 'required|in_list[golongan,gaji]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $memberId = session()->get('registration_member_id');

        $updateData = [
            'campus_location' => $this->request->getPost('campus_location'),
            'faculty' => $this->request->getPost('faculty'),
            'department' => $this->request->getPost('department'),
            'work_unit' => $this->request->getPost('work_unit'),
            'employee_id_number' => $this->request->getPost('employee_id_number'),
            'lecturer_id_number' => $this->request->getPost('lecturer_id_number'),
            'academic_rank' => $this->request->getPost('academic_rank'),
            'employment_status' => $this->request->getPost('employment_status'),
            'work_start_date' => $this->request->getPost('work_start_date'),
            'dues_rate_type' => $this->request->getPost('dues_rate_type'),
        ];

        // Calculate monthly dues based on selection
        // This will be implemented when database is available
        // For now, just save the type

        $this->memberModel->update($memberId, $updateData);

        session()->set('registration_step', 4);

        return redirect()->to(base_url('registrasi/step-4'))->with('success', 'Data pekerjaan berhasil disimpan');
    }

    /**
     * Display Step 4: Payment & Document Upload
     */
    public function step4()
    {
        if (!session()->has('registration_member_id') || session()->get('registration_step') < 4) {
            return redirect()->to(base_url('registrasi'))->with('error', 'Silakan lengkapi langkah sebelumnya');
        }

        $memberId = session()->get('registration_member_id');
        $member = $this->memberModel->find($memberId);

        $data = [
            'title' => 'Pendaftaran - Pembayaran & Dokumen',
            'description' => 'Upload dokumen dan bukti pembayaran',
            'validation' => \Config\Services::validation(),
            'step' => 4,
            'member' => $member,
        ];

        return view('public/register_step4', $data);
    }

    /**
     * Process Step 4: Upload documents and complete registration
     */
    public function processStep4()
    {
        if (!session()->has('registration_member_id') || session()->get('registration_step') < 4) {
            return redirect()->to(base_url('registrasi'))->with('error', 'Silakan lengkapi langkah sebelumnya');
        }

        // Validate agreements
        if (!$this->request->getPost('agreement_accepted') || !$this->request->getPost('privacy_accepted')) {
            return redirect()->back()->withInput()->with('error', 'Anda harus menyetujui AD/ART dan Kebijakan Privasi');
        }

        $memberId = session()->get('registration_member_id');
        $updateData = [
            'agreement_accepted_at' => date('Y-m-d H:i:s'),
            'privacy_accepted_at' => date('Y-m-d H:i:s'),
        ];

        $uploadErrors = [];

        // Upload payment proof (required)
        $paymentProof = $this->request->getFile('registration_payment_proof');
        if ($paymentProof && $paymentProof->isValid()) {
            $result = upload_file($paymentProof, 'uploads/payments/', ['jpg', 'jpeg', 'png', 'pdf'], 2048);
            if ($result['success']) {
                $updateData['registration_payment_proof'] = $result['file_name'];
                $updateData['registration_payment_date'] = date('Y-m-d H:i:s');
            } else {
                $uploadErrors[] = 'Bukti Pembayaran: ' . $result['error'];
            }
        } else {
            $uploadErrors[] = 'Bukti Pembayaran: File wajib diupload';
        }

        // Upload ID card (required)
        $idCard = $this->request->getFile('id_card_photo');
        if ($idCard && $idCard->isValid()) {
            $result = upload_file($idCard, 'uploads/documents/', ['jpg', 'jpeg', 'png', 'pdf'], 2048);
            if ($result['success']) {
                $updateData['id_card_photo'] = $result['file_name'];
            } else {
                $uploadErrors[] = 'KTP: ' . $result['error'];
            }
        } else {
            $uploadErrors[] = 'KTP: File wajib diupload';
        }

        // Upload family card (optional)
        $familyCard = $this->request->getFile('family_card_photo');
        if ($familyCard && $familyCard->isValid()) {
            $result = upload_file($familyCard, 'uploads/documents/', ['jpg', 'jpeg', 'png', 'pdf'], 2048);
            if ($result['success']) {
                $updateData['family_card_photo'] = $result['file_name'];
            } else {
                $uploadErrors[] = 'Kartu Keluarga: ' . $result['error'];
            }
        }

        // Upload SK Pengangkatan (optional)
        $skPengangkatan = $this->request->getFile('sk_pengangkatan_photo');
        if ($skPengangkatan && $skPengangkatan->isValid()) {
            $result = upload_file($skPengangkatan, 'uploads/documents/', ['jpg', 'jpeg', 'png', 'pdf'], 2048);
            if ($result['success']) {
                $updateData['sk_pengangkatan_photo'] = $result['file_name'];
            } else {
                $uploadErrors[] = 'SK Pengangkatan: ' . $result['error'];
            }
        }

        // Upload profile photo (optional)
        $profilePhoto = $this->request->getFile('profile_photo');
        if ($profilePhoto && $profilePhoto->isValid()) {
            $result = upload_file($profilePhoto, 'uploads/photos/', ['jpg', 'jpeg', 'png'], 2048);
            if ($result['success']) {
                $updateData['profile_photo'] = $result['file_name'];
            } else {
                $uploadErrors[] = 'Pas Foto: ' . $result['error'];
            }
        }

        // If there are upload errors, return with error messages
        if (!empty($uploadErrors)) {
            return redirect()->back()->withInput()->with('error', 'Gagal upload file:<br>' . implode('<br>', $uploadErrors));
        }

        // Update onboarding state
        $updateData['onboarding_state'] = 'payment_submitted';

        // Update member data
        $this->memberModel->update($memberId, $updateData);

        // Clear registration session
        session()->remove(['registration_member_id', 'registration_member_uuid', 'registration_step']);

        // TODO: Send email verification here

        return redirect()->to(base_url('registrasi/selesai'))->with('success', 'Pendaftaran berhasil diselesaikan');
    }

    /**
     * Registration complete page
     */
    public function complete()
    {
        $data = [
            'title' => 'Pendaftaran Selesai',
            'description' => 'Terima kasih telah mendaftar',
        ];

        return view('public/register_complete', $data);
    }
}
