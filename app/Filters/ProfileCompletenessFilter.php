<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MemberModel;

class ProfileCompletenessFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Skip check for super_admin and admin
        $roles = get_user_roles(session()->get('user_id'));
        $roleNames = array_column($roles, 'role_name');
        
        if (in_array('super_admin', $roleNames) || in_array('admin', $roleNames)) {
            return $request;
        }

        // Check profile completeness for members, coordinators, candidates
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to(base_url('login'));
        }

        $memberModel = new MemberModel();
        $member = $memberModel->find($userId);

        if (!$member) {
            return redirect()->to(base_url('login'));
        }

        // Required fields for full membership access
        $requiredFields = [
            // Personal Data
            'full_name', 'identity_number', 'gender', 'birth_date', 'birth_place', 'phone_number',
            
            // Address
            'address', 'province', 'city', 'district', 'postal_code',
            
            // Emergency Contact
            'emergency_contact_name', 'emergency_contact_relation', 'emergency_contact_phone',
            
            // Work Data
            'university_name', 'campus_location', 'faculty', 'department', 
            'employee_id_number', 'employment_status', 'work_start_date',
            
            // Salary
            'gross_salary',
            
            // Banking
            'bank_name', 'bank_account_number', 'bank_account_name',
            
            // Education
            'education_level', 'graduation_year', 'institution_name', 'field_of_study',
            
            // Documents
            'id_card_photo', 'family_card_photo', 'sk_pengangkatan_photo', 'profile_photo',
        ];

        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (empty($member[$field])) {
                $missingFields[] = $field;
            }
        }

        // If profile is incomplete, redirect to profile edit
        if (!empty($missingFields)) {
            // Allow access to profile routes only
            $currentPath = $request->getPath();
            $allowedPaths = [
                'member/profile',
                'member/profile/edit',
                'coordinator/profile',
                'coordinator/profile/edit',
            ];

            $isAllowedPath = false;
            foreach ($allowedPaths as $allowed) {
                if (strpos($currentPath, $allowed) !== false) {
                    $isAllowedPath = true;
                    break;
                }
            }

            if (!$isAllowedPath) {
                $profileUrl = base_url('member/profile/edit');
                if (in_array('coordinator', $roleNames)) {
                    $profileUrl = base_url('coordinator/profile/edit');
                }

                return redirect()->to($profileUrl)
                    ->with('warning', 'Lengkapi profil Anda terlebih dahulu untuk mengakses fitur keanggotaan. Field yang belum lengkap: ' . count($missingFields) . ' item.');
            }
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
