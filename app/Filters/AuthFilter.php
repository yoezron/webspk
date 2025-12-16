<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Check if user is authenticated before allowing access
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->has('is_logged_in') || !session()->get('is_logged_in')) {
            // Store intended URL for redirect after login
            session()->set('redirect_url', current_url());

            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu');
        }

        // Check if member data has changed since session was created (status, role, permissions)
        $userId = session()->get('user_id');
        $sessionCreatedAt = session()->get('session_created_at');

        if ($userId && $sessionCreatedAt) {
            $memberModel = new \App\Models\MemberModel();

            // Check if status changed after session creation
            if ($memberModel->hasStatusChangedSince($userId, $sessionCreatedAt)) {
                // Status changed - invalidate session and force re-login
                session()->destroy();

                log_message('info', "Session invalidated for user {$userId} due to status change");

                return redirect()->to(base_url('login'))->with('warning', 'Status akun Anda telah diubah. Silakan login kembali untuk memperbarui sesi Anda.');
            }
        }

        // Check if account is active
        if (session()->get('account_status') !== 'active') {
            // Allow candidates to access registration completion
            if (session()->get('membership_status') === 'candidate') {
                $allowedRoutes = [
                    'member/registration/complete',
                    'member/registration/status',
                    'member/profile',
                    'logout',
                ];

                $currentPath = uri_string();
                foreach ($allowedRoutes as $route) {
                    if (strpos($currentPath, $route) !== false) {
                        return;
                    }
                }
            }

            // Redirect to appropriate page based on status
            if (session()->get('account_status') === 'suspended') {
                return redirect()->to(base_url('/'))->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator');
            }

            if (session()->get('account_status') === 'rejected') {
                return redirect()->to(base_url('/'))->with('error', 'Pendaftaran Anda telah ditolak');
            }

            if (session()->get('account_status') === 'pending') {
                return redirect()->to(base_url('member/registration/status'))->with('info', 'Akun Anda sedang dalam proses verifikasi');
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
