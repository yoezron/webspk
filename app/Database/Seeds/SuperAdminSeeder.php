<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'uuid' => $this->generateUUID(),
            'member_number' => 'SPK-ADMIN-001',
            'email' => 'superadmin@spk.local',
            'password' => 'SuperAdmin123!', // Will be hashed by model
            'full_name' => 'Super Administrator',
            'phone_number' => '081234567890',
            'university_name' => 'Kantor Pusat SPK',
            'role' => 'super_admin',
            'membership_status' => 'active',
            'account_status' => 'active',
            'onboarding_state' => 'approved',
            'email_verified_at' => date('Y-m-d H:i:s'),
            'approval_date' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Use MemberModel to insert so password gets hashed
        $memberModel = new \App\Models\MemberModel();
        $memberModel->insert($data);

        echo "Super Admin created successfully!\n";
        echo "Email: superadmin@spk.local\n";
        echo "Password: SuperAdmin123!\n";
    }

    private function generateUUID(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
