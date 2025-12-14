<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        echo "\n=== Starting Database Seeding ===\n\n";

        // Seed in order
        echo "1. Seeding Region Codes...\n";
        $this->call('RegionCodesSeeder');

        echo "\n2. Seeding Dues Rates...\n";
        $this->call('DuesRatesSeeder');

        echo "\n3. Seeding Super Admin...\n";
        $this->call('SuperAdminSeeder');

        echo "\n=== Database Seeding Completed! ===\n\n";
        echo "You can now login with:\n";
        echo "Email: superadmin@spk.local\n";
        echo "Password: SuperAdmin123!\n\n";
    }
}
