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
