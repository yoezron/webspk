<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RBACFilter implements FilterInterface
{
    /**
     * Check if user has required role before allowing access
     *
     * @param RequestInterface $request
     * @param array|null $arguments - Array of allowed roles
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->has('is_logged_in') || !session()->get('is_logged_in')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu');
        }

        // Get user role
        $userRole = session()->get('user_role');

        // If no arguments provided, just check if authenticated
        if (!$arguments) {
            return;
        }

        // Check if user role is in allowed roles
        if (!in_array($userRole, $arguments)) {
            // Redirect based on role
            return $this->redirectUnauthorized($userRole);
        }
    }

    /**
     * Redirect unauthorized user to appropriate page
     *
     * @param string $userRole
     * @return mixed
     */
    private function redirectUnauthorized(string $userRole)
    {
        // Redirect to appropriate dashboard based on role
        switch ($userRole) {
            case 'super_admin':
            case 'admin':
                return redirect()->to(base_url('admin/dashboard'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut');

            case 'coordinator':
            case 'treasurer':
            case 'member':
                return redirect()->to(base_url('member/dashboard'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut');

            case 'candidate':
                return redirect()->to(base_url('member/registration/status'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut');

            default:
                return redirect()->to(base_url('/'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut');
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
