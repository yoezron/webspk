<?php

namespace App\Libraries;

use CodeIgniter\Email\Email;
use Config\Services;

/**
 * Email Service Library
 * Handles all email sending operations
 */
class EmailService
{
    protected $email;
    protected $config;

    public function __construct()
    {
        $this->config = config('Email');
        $this->email = Services::email();

        // Configure email settings from .env
        $this->email->initialize([
            'protocol' => getenv('email.protocol') ?: 'smtp',
            'SMTPHost' => getenv('email.SMTPHost') ?: 'ssl://smtp.googlemail.com',
            'SMTPPort' => getenv('email.SMTPPort') ?: 465,
            'SMTPUser' => getenv('email.SMTPUser') ?: '',
            'SMTPPass' => getenv('email.SMTPPass') ?: '',
            'SMTPCrypto' => getenv('email.SMTPCrypto') ?: 'ssl',
            'mailType' => getenv('email.mailType') ?: 'html',
            'charset' => getenv('email.charset') ?: 'utf-8',
            'newline' => getenv('email.newline') ?: "\r\n",
            'wordWrap' => true,
            'validate' => true,
        ]);
    }

    /**
     * Send email verification
     */
    public function sendEmailVerification(string $to, string $name, string $token): bool
    {
        try {
            $verificationUrl = base_url('verify-email/' . $token);

            $this->email->setFrom(
                getenv('email.fromEmail') ?: 'spkwebadm@gmail.com',
                getenv('email.fromName') ?: 'Serikat Pekerja Kampus'
            );
            $this->email->setTo($to);
            $this->email->setSubject('Verifikasi Email - Serikat Pekerja Kampus');

            $message = view('emails/email_verification', [
                'name' => $name,
                'verification_url' => $verificationUrl,
                'expiry_hours' => getenv('app.emailVerificationExpiry') ?: 24,
            ]);

            $this->email->setMessage($message);

            if ($this->email->send()) {
                log_message('info', "Email verification sent to: {$to}");
                return true;
            } else {
                log_message('error', "Failed to send email verification to: {$to}. Error: " . $this->email->printDebugger(['headers']));
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', "Email verification exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send password reset email
     */
    public function sendPasswordReset(string $to, string $name, string $token): bool
    {
        try {
            $resetUrl = base_url('reset-password/' . $token);

            $this->email->setFrom(
                getenv('email.fromEmail') ?: 'spkwebadm@gmail.com',
                getenv('email.fromName') ?: 'Serikat Pekerja Kampus'
            );
            $this->email->setTo($to);
            $this->email->setSubject('Reset Password - Serikat Pekerja Kampus');

            $message = view('emails/password_reset', [
                'name' => $name,
                'reset_url' => $resetUrl,
                'expiry_hours' => getenv('app.passwordResetExpiry') ?: 1,
            ]);

            $this->email->setMessage($message);

            if ($this->email->send()) {
                log_message('info', "Password reset email sent to: {$to}");
                return true;
            } else {
                log_message('error', "Failed to send password reset to: {$to}. Error: " . $this->email->printDebugger(['headers']));
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', "Password reset email exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send membership approval notification
     */
    public function sendMembershipApproval(string $to, string $name, string $memberNumber): bool
    {
        try {
            $loginUrl = base_url('login');

            $this->email->setFrom(
                getenv('email.fromEmail') ?: 'spkwebadm@gmail.com',
                getenv('email.fromName') ?: 'Serikat Pekerja Kampus'
            );
            $this->email->setTo($to);
            $this->email->setSubject('Keanggotaan Anda Telah Disetujui - Serikat Pekerja Kampus');

            $message = view('emails/membership_approval', [
                'name' => $name,
                'member_number' => $memberNumber,
                'login_url' => $loginUrl,
            ]);

            $this->email->setMessage($message);

            if ($this->email->send()) {
                log_message('info', "Membership approval sent to: {$to}");
                return true;
            } else {
                log_message('error', "Failed to send membership approval to: {$to}");
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', "Membership approval email exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send membership rejection notification
     */
    public function sendMembershipRejection(string $to, string $name, string $reason): bool
    {
        try {
            $contactUrl = base_url('kontak');

            $this->email->setFrom(
                getenv('email.fromEmail') ?: 'spkwebadm@gmail.com',
                getenv('email.fromName') ?: 'Serikat Pekerja Kampus'
            );
            $this->email->setTo($to);
            $this->email->setSubject('Pemberitahuan Status Keanggotaan - Serikat Pekerja Kampus');

            $message = view('emails/membership_rejection', [
                'name' => $name,
                'reason' => $reason,
                'contact_url' => $contactUrl,
            ]);

            $this->email->setMessage($message);

            if ($this->email->send()) {
                log_message('info', "Membership rejection sent to: {$to}");
                return true;
            } else {
                log_message('error', "Failed to send membership rejection to: {$to}");
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', "Membership rejection email exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send payment confirmation
     */
    public function sendPaymentConfirmation(string $to, string $name, float $amount, string $period): bool
    {
        try {
            $dashboardUrl = base_url('dashboard');

            $this->email->setFrom(
                getenv('email.fromEmail') ?: 'spkwebadm@gmail.com',
                getenv('email.fromName') ?: 'Serikat Pekerja Kampus'
            );
            $this->email->setTo($to);
            $this->email->setSubject('Konfirmasi Pembayaran Iuran - Serikat Pekerja Kampus');

            helper('app');
            $message = view('emails/payment_confirmation', [
                'name' => $name,
                'amount' => format_currency($amount),
                'period' => $period,
                'dashboard_url' => $dashboardUrl,
            ]);

            $this->email->setMessage($message);

            if ($this->email->send()) {
                log_message('info', "Payment confirmation sent to: {$to}");
                return true;
            } else {
                log_message('error', "Failed to send payment confirmation to: {$to}");
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', "Payment confirmation email exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get last email error
     */
    public function getError(): string
    {
        return $this->email->printDebugger(['headers']);
    }
}
