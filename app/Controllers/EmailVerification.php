<?php

namespace App\Controllers;

use App\Models\MemberModel;
use App\Libraries\EmailService;

class EmailVerification extends BaseController
{
    protected $memberModel;
    protected $emailService;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->emailService = new EmailService();
    }

    /**
     * Verify email using token
     */
    public function verify($token = null)
    {
        if (!$token) {
            return redirect()->to(base_url('/'))->with('error', 'Token verifikasi tidak valid');
        }

        // Hash token untuk lookup
        $tokenHash = hash('sha256', $token);

        // Find member by token
        $member = $this->memberModel
            ->where('reset_token_hash', $tokenHash)
            ->where('reset_token_expires_at >', date('Y-m-d H:i:s'))
            ->first();

        if (!$member) {
            return view('auth/verification_failed', [
                'title' => 'Verifikasi Gagal',
                'message' => 'Token verifikasi tidak valid atau sudah kadaluarsa. Silakan minta token baru.',
            ]);
        }

        // Check if already verified
        if ($member['email_verified_at']) {
            return redirect()->to(base_url('login'))->with('info', 'Email Anda sudah terverifikasi sebelumnya. Silakan login.');
        }

        // Update member status
        $updateData = [
            'email_verified_at' => date('Y-m-d H:i:s'),
            'onboarding_state' => 'email_verified',
            'reset_token_hash' => null,
            'reset_token_expires_at' => null,
        ];

        if ($this->memberModel->update($member['id'], $updateData)) {
            // Set session untuk auto-login after verification
            session()->set([
                'registration_member_id' => $member['id'],
                'registration_member_uuid' => $member['uuid'],
                'registration_step' => 2, // Lanjut ke step 2
            ]);

            return view('auth/verification_success', [
                'title' => 'Verifikasi Berhasil',
                'member' => $member,
            ]);
        } else {
            return view('auth/verification_failed', [
                'title' => 'Verifikasi Gagal',
                'message' => 'Terjadi kesalahan saat memverifikasi email. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * Resend verification email with rate limiting
     */
    public function resend()
    {
        if (!$this->request->is('post')) {
            return redirect()->to(base_url('/'));
        }

        $email = $this->request->getPost('email');

        if (!$email) {
            return redirect()->back()->with('error', 'Email harus diisi');
        }

        // SECURITY: Rate limiting to prevent email bombing
        $rateLimitCheck = $this->checkRateLimit($email);
        if (!$rateLimitCheck['allowed']) {
            return redirect()->back()->with('error', $rateLimitCheck['message']);
        }

        // Find member
        $member = $this->memberModel->findByEmail($email);

        if (!$member) {
            // Don't reveal if email exists or not for security
            // But still apply rate limiting
            $this->recordResendAttempt($email);
            return redirect()->back()->with('success', 'Jika email terdaftar, link verifikasi baru telah dikirim.');
        }

        // Check if already verified
        if ($member['email_verified_at']) {
            return redirect()->to(base_url('login'))->with('info', 'Email Anda sudah terverifikasi. Silakan login.');
        }

        // Generate new token
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        $expiryHours = getenv('app.emailVerificationExpiry') ?: 24;

        // Update token
        $this->memberModel->update($member['id'], [
            'reset_token_hash' => $tokenHash,
            'reset_token_expires_at' => date('Y-m-d H:i:s', strtotime("+{$expiryHours} hours")),
        ]);

        // Send email
        if ($this->emailService->sendEmailVerification($member['email'], $member['full_name'], $token)) {
            // Record successful resend for rate limiting
            $this->recordResendAttempt($email);

            return redirect()->back()->with('success', 'Link verifikasi baru telah dikirim ke email Anda.');
        } else {
            log_message('error', 'Failed to resend verification email to: ' . $member['email']);
            return redirect()->back()->with('error', 'Gagal mengirim email verifikasi. Silakan coba lagi.');
        }
    }

    /**
     * Check rate limit for email resend
     *
     * @param string $email Email address
     * @return array ['allowed' => bool, 'message' => string]
     */
    private function checkRateLimit(string $email): array
    {
        $result = ['allowed' => true, 'message' => ''];

        // Get rate limit data from session
        $rateLimitKey = 'email_resend_' . md5($email);
        $rateLimitData = session()->get($rateLimitKey);

        if (!$rateLimitData) {
            // First attempt - allowed
            return $result;
        }

        $now = time();
        $lastResend = $rateLimitData['last_resend'] ?? 0;
        $resendCount = $rateLimitData['count'] ?? 0;
        $windowStart = $rateLimitData['window_start'] ?? $now;

        // Rule 1: Minimum 60 seconds between resends
        $timeSinceLastResend = $now - $lastResend;
        if ($timeSinceLastResend < 60) {
            $waitTime = 60 - $timeSinceLastResend;
            $result['allowed'] = false;
            $result['message'] = "Mohon tunggu {$waitTime} detik sebelum mengirim ulang email verifikasi.";
            return $result;
        }

        // Rule 2: Maximum 3 resends per 1 hour window
        $windowDuration = $now - $windowStart;
        if ($windowDuration < 3600) {
            // Still within 1-hour window
            if ($resendCount >= 3) {
                $remainingTime = ceil((3600 - $windowDuration) / 60);
                $result['allowed'] = false;
                $result['message'] = "Anda telah mencapai batas maksimum pengiriman ulang email (3x per jam). Silakan coba lagi dalam {$remainingTime} menit.";
                return $result;
            }
        }

        return $result;
    }

    /**
     * Record email resend attempt for rate limiting
     *
     * @param string $email Email address
     * @return void
     */
    private function recordResendAttempt(string $email): void
    {
        $rateLimitKey = 'email_resend_' . md5($email);
        $rateLimitData = session()->get($rateLimitKey);

        $now = time();

        if (!$rateLimitData) {
            // First attempt
            $rateLimitData = [
                'last_resend' => $now,
                'count' => 1,
                'window_start' => $now,
            ];
        } else {
            $windowStart = $rateLimitData['window_start'] ?? $now;
            $windowDuration = $now - $windowStart;

            if ($windowDuration >= 3600) {
                // Window expired - start new window
                $rateLimitData = [
                    'last_resend' => $now,
                    'count' => 1,
                    'window_start' => $now,
                ];
            } else {
                // Within window - increment count
                $rateLimitData['last_resend'] = $now;
                $rateLimitData['count'] = ($rateLimitData['count'] ?? 0) + 1;
            }
        }

        session()->set($rateLimitKey, $rateLimitData);

        // Log for security monitoring
        log_message('info', "Email resend attempt: {$email}, Count: {$rateLimitData['count']}, IP: " . $this->request->getIPAddress());
    }
}
