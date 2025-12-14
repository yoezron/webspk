<?php

namespace App\Controllers;

use App\Models\MemberModel;

class Auth extends BaseController
{
    protected $memberModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        helper(['form', 'url']);
    }

    /**
     * Display login page
     */
    public function login()
    {
        // Redirect if already logged in
        if (session()->has('user_id')) {
            return redirect()->to(base_url('dashboard'));
        }

        $data = [
            'title' => 'Login',
            'description' => 'Login ke Sistem Informasi Serikat Pekerja Kampus',
            'validation' => \Config\Services::validation(),
        ];

        return view('auth/login', $data);
    }

    /**
     * Process login
     */
    public function processLogin()
    {
        // Validation rules
        $rules = [
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Email wajib diisi',
                    'valid_email' => 'Email tidak valid',
                ],
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password wajib diisi',
                    'min_length' => 'Password minimal 6 karakter',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');

        // Find member by email
        $member = $this->memberModel->findByEmail($email);

        if (!$member) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah');
        }

        // Check if account is locked
        if ($this->memberModel->isAccountLocked($member)) {
            $lockedUntil = date('H:i', strtotime($member['locked_until']));
            return redirect()->back()->withInput()->with('error', "Akun Anda terkunci sampai {$lockedUntil} karena terlalu banyak percobaan login gagal");
        }

        // Verify password
        if (!$this->memberModel->verifyPassword($password, $member['password_hash'])) {
            // Increment failed attempts
            $this->memberModel->incrementFailedAttempts($member['id']);

            return redirect()->back()->withInput()->with('error', 'Email atau password salah');
        }

        // Check account status
        if ($member['account_status'] === 'suspended') {
            return redirect()->back()->withInput()->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator');
        }

        if ($member['account_status'] === 'rejected') {
            return redirect()->back()->withInput()->with('error', 'Pendaftaran Anda telah ditolak. Silakan hubungi administrator');
        }

        // Reset failed attempts
        $this->memberModel->resetFailedAttempts($member['id']);

        // Update last login
        $this->memberModel->updateLastLogin(
            $member['id'],
            $this->request->getIPAddress(),
            $this->request->getUserAgent()->getAgentString()
        );

        // Set session data
        $sessionData = [
            'user_id' => $member['id'],
            'user_uuid' => $member['uuid'],
            'user_email' => $member['email'],
            'user_name' => $member['full_name'],
            'user_role' => $member['role'],
            'membership_status' => $member['membership_status'],
            'onboarding_state' => $member['onboarding_state'],
            'account_status' => $member['account_status'],
            'is_logged_in' => true,
        ];

        session()->set($sessionData);

        // Handle remember me
        if ($remember) {
            // Set cookie for 30 days
            $this->response->setCookie('remember_token', $member['uuid'], 30 * 24 * 60 * 60);
        }

        // Log successful login (will implement audit log later)

        // Redirect based on role and status
        return $this->redirectAfterLogin($member);
    }

    /**
     * Redirect after successful login based on role and status
     */
    private function redirectAfterLogin(array $member)
    {
        // Super Admin, Admin always go to admin dashboard
        if (in_array($member['role'], ['super_admin', 'admin'])) {
            return redirect()->to(base_url('admin/dashboard'))->with('success', 'Selamat datang, ' . $member['full_name']);
        }

        // Coordinator, Treasurer go to their respective dashboards
        if (in_array($member['role'], ['coordinator', 'treasurer'])) {
            return redirect()->to(base_url('dashboard'))->with('success', 'Selamat datang, ' . $member['full_name']);
        }

        // Candidate members with pending status
        if ($member['role'] === 'candidate' && $member['onboarding_state'] === 'registered') {
            return redirect()->to(base_url('member/registration/complete'))->with('info', 'Silakan lengkapi data pendaftaran Anda');
        }

        // Candidate members waiting for verification
        if ($member['role'] === 'candidate' && $member['onboarding_state'] === 'payment_submitted') {
            return redirect()->to(base_url('member/registration/status'))->with('info', 'Pendaftaran Anda sedang diverifikasi');
        }

        // Active members
        if ($member['membership_status'] === 'active') {
            return redirect()->to(base_url('member/dashboard'))->with('success', 'Selamat datang, ' . $member['full_name']);
        }

        // Default redirect to dashboard
        return redirect()->to(base_url('dashboard'))->with('success', 'Selamat datang, ' . $member['full_name']);
    }

    /**
     * Logout
     */
    public function logout()
    {
        // Log logout (will implement audit log later)

        // Destroy session
        session()->destroy();

        // Remove remember me cookie
        $this->response->deleteCookie('remember_token');

        return redirect()->to(base_url('login'))->with('success', 'Anda telah berhasil logout');
    }

    /**
     * Display forgot password page
     */
    public function forgotPassword()
    {
        $data = [
            'title' => 'Lupa Password',
            'description' => 'Reset password Anda',
            'validation' => \Config\Services::validation(),
        ];

        return view('auth/forgot_password', $data);
    }

    /**
     * Process forgot password
     */
    public function processForgotPassword()
    {
        $rules = [
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'Email wajib diisi',
                    'valid_email' => 'Email tidak valid',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $email = $this->request->getPost('email');
        $member = $this->memberModel->findByEmail($email);

        if (!$member) {
            // Don't reveal if email exists or not (security best practice)
            return redirect()->back()->with('success', 'Jika email terdaftar, instruksi reset password telah dikirim ke email Anda');
        }

        // Generate reset token
        $token = $this->memberModel->generateResetToken($member['id']);

        // Send email with reset link (will implement email service later)
        // For now, just return success message
        $resetLink = base_url("reset-password/{$token}");

        // TODO: Send email with $resetLink

        return redirect()->back()->with('success', 'Instruksi reset password telah dikirim ke email Anda');
    }

    /**
     * Display reset password page
     */
    public function resetPassword($token = null)
    {
        if (!$token) {
            return redirect()->to(base_url('login'))->with('error', 'Token tidak valid');
        }

        // Verify token
        $member = $this->memberModel->findByResetToken($token);

        if (!$member) {
            return redirect()->to(base_url('login'))->with('error', 'Token tidak valid atau sudah kadaluarsa');
        }

        $data = [
            'title' => 'Reset Password',
            'description' => 'Buat password baru',
            'token' => $token,
            'validation' => \Config\Services::validation(),
        ];

        return view('auth/reset_password', $data);
    }

    /**
     * Process reset password
     */
    public function processResetPassword()
    {
        $rules = [
            'token' => 'required',
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
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        // Find member by token
        $member = $this->memberModel->findByResetToken($token);

        if (!$member) {
            return redirect()->to(base_url('login'))->with('error', 'Token tidak valid atau sudah kadaluarsa');
        }

        // Update password
        $this->memberModel->update($member['id'], [
            'password' => $password,  // Will be hashed by beforeUpdate callback
        ]);

        // Clear reset token
        $this->memberModel->clearResetToken($member['id']);

        return redirect()->to(base_url('login'))->with('success', 'Password berhasil diubah. Silakan login dengan password baru Anda');
    }
}
