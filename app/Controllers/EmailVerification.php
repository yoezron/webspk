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
     * Resend verification email
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

        // Find member
        $member = $this->memberModel->findByEmail($email);

        if (!$member) {
            // Don't reveal if email exists or not for security
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
            return redirect()->back()->with('success', 'Link verifikasi baru telah dikirim ke email Anda.');
        } else {
            log_message('error', 'Failed to resend verification email to: ' . $member['email']);
            return redirect()->back()->with('error', 'Gagal mengirim email verifikasi. Silakan coba lagi.');
        }
    }
}
