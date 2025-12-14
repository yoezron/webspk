<?php

namespace App\Controllers;

use App\Models\MemberModel;

class Dashboard extends BaseController
{
    protected $memberModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        helper(['form', 'url']);
    }

    /**
     * General dashboard - redirect based on role
     */
    public function index()
    {
        $userRole = session()->get('user_role');

        // Redirect to appropriate dashboard based on role
        switch ($userRole) {
            case 'super_admin':
            case 'admin':
                return redirect()->to(base_url('admin/dashboard'));

            case 'coordinator':
            case 'treasurer':
            case 'member':
                return redirect()->to(base_url('member/dashboard'));

            case 'candidate':
                // Check onboarding state
                $onboardingState = session()->get('onboarding_state');

                if ($onboardingState === 'registered') {
                    return redirect()->to(base_url('member/registration/complete'));
                } elseif (in_array($onboardingState, ['payment_submitted', 'email_verified'])) {
                    return redirect()->to(base_url('member/registration/status'));
                }

                return redirect()->to(base_url('member/dashboard'));

            default:
                return redirect()->to(base_url('/'))->with('error', 'Role tidak dikenali');
        }
    }
}
