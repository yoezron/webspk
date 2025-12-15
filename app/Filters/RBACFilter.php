<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\RBACPermissionModel;

class RBACFilter implements FilterInterface
{
    /**
     * Check if user has required role or permission before allowing access
     *
     * @param RequestInterface $request
     * @param array|null $arguments - Array of allowed roles OR permissions (prefixed with 'perm:')
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in
        if (!session()->has('is_logged_in') || !session()->get('is_logged_in')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu');
        }

        // Get user data
        $userId = session()->get('user_id');
        $userRole = session()->get('user_role');

        // If no arguments provided, just check if authenticated
        if (!$arguments) {
            return;
        }

        // Check if arguments contain permissions (prefixed with 'perm:')
        $hasPermissionCheck = false;
        $requiredPermissions = [];
        $allowedRoles = [];

        foreach ($arguments as $arg) {
            if (strpos($arg, 'perm:') === 0) {
                $hasPermissionCheck = true;
                $requiredPermissions[] = substr($arg, 5); // Remove 'perm:' prefix
            } else {
                $allowedRoles[] = $arg;
            }
        }

        // If permission check is required
        if ($hasPermissionCheck) {
            $permissionModel = new RBACPermissionModel();

            // Check if user has at least one of the required permissions
            foreach ($requiredPermissions as $permission) {
                if ($permissionModel->userHasPermission($userId, $permission)) {
                    return; // User has permission, allow access
                }
            }

            // User doesn't have required permissions
            return $this->redirectUnauthorized($userRole);
        }

        // Otherwise, check role-based access
        if (!empty($allowedRoles) && !in_array($userRole, $allowedRoles)) {
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
