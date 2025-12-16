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

        // Check for remember me token (auto-login)
        $rememberToken = $this->request->getCookie('remember_token');
        if ($rememberToken) {
            $member = $this->memberModel->findByRememberToken($rememberToken);

            if ($member) {
                // Auto-login successful - set session
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
                    'session_created_at' => date('Y-m-d H:i:s'), // For session invalidation
                ];

                session()->set($sessionData);

                // Update last login
                $this->memberModel->updateLastLogin(
                    $member['id'],
                    $this->request->getIPAddress(),
                    $this->request->getUserAgent()->getAgentString()
                );

                // Audit log auto-login
                helper('audit');
                audit_log_login($member['id'], $member['email'], 'auto-login via remember token');

                // Redirect based on role
                return $this->redirectAfterLogin($member);
            } else {
                // Invalid or expired token - delete cookie
                $this->response->deleteCookie('remember_token');
            }
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
                    'valid_email' => 'Format email tidak valid',
                ],
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[8]',
                'errors' => [
                    'required' => 'Password wajib diisi',
                    'min_length' => 'Password minimal 8 karakter',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Sanitize inputs
        $email = trim(strtolower($this->request->getPost('email')));
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');

        // Find member by email
        $member = $this->memberModel->findByEmail($email);

        if (!$member) {
            // Audit log failed login attempt
            helper('audit');
            audit_log_failed_login($email, 'Email not found');

            return redirect()->back()->withInput()->with('error', 'Email atau password yang Anda masukkan salah. Silakan coba lagi.');
        }

        // Check if account is locked
        if ($this->memberModel->isAccountLocked($member)) {
            $lockedUntil = date('H:i', strtotime($member['locked_until']));
            $remainingTime = strtotime($member['locked_until']) - time();
            $remainingMinutes = ceil($remainingTime / 60);

            return redirect()->back()->withInput()->with('error', "Akun Anda terkunci sampai pukul {$lockedUntil} ({$remainingMinutes} menit lagi) karena terlalu banyak percobaan login yang gagal. Silakan coba lagi nanti.");
        }

        // Verify password
        if (!$this->memberModel->verifyPassword($password, $member['password_hash'])) {
            // Increment failed attempts
            $this->memberModel->incrementFailedAttempts($member['id']);

            // Audit log failed login
            helper('audit');
            audit_log_failed_login($email, 'Invalid password');

            // Get failed attempts count
            $updatedMember = $this->memberModel->find($member['id']);
            $attemptsLeft = 5 - ($updatedMember['failed_login_attempts'] ?? 0);

            if ($attemptsLeft > 0 && $attemptsLeft <= 3) {
                return redirect()->back()->withInput()->with('error', "Email atau password yang Anda masukkan salah. Sisa percobaan: {$attemptsLeft}x");
            }

            return redirect()->back()->withInput()->with('error', 'Email atau password yang Anda masukkan salah. Silakan coba lagi.');
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
            'session_created_at' => date('Y-m-d H:i:s'), // For session invalidation
        ];

        session()->set($sessionData);

        // Handle remember me with secure token
        if ($remember) {
            // Generate cryptographically secure random token
            $rememberToken = $this->memberModel->generateRememberToken($member['id']);

            // Set secure cookie for 30 days with httpOnly and secure flags
            $this->response->setCookie([
                'name'     => 'remember_token',
                'value'    => $rememberToken,
                'expire'   => 30 * 24 * 60 * 60, // 30 days
                'path'     => '/',
                'secure'   => true, // HTTPS only
                'httponly' => true, // Not accessible via JavaScript
                'samesite' => 'Lax', // CSRF protection
            ]);
        }

        // Audit log successful login
        helper('audit');
        audit_log_login($member['id'], $member['email'], 'success');

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
        // Audit log logout
        helper('audit');
        $userId = session()->get('user_id');
        $userEmail = session()->get('user_email');
        if ($userId && $userEmail) {
            audit_log_logout($userId, $userEmail);

            // Clear remember token from database
            $this->memberModel->clearRememberToken($userId);
        }

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

        // Send password reset email
        $emailService = new \App\Libraries\EmailService();
        $emailSent = $emailService->sendPasswordReset(
            $member['email'],
            $member['full_name'],
            $token
        );

        if ($emailSent) {
            log_message('info', "Password reset email sent to {$member['email']}");
            return redirect()->back()->with('success', 'Instruksi reset password telah dikirim ke email Anda');
        } else {
            log_message('error', "Failed to send password reset email to {$member['email']}");
            return redirect()->back()->with('error', 'Gagal mengirim email reset password. Silakan coba lagi nanti.');
        }
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
