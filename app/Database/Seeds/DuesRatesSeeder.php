<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DuesRatesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Tarif Golongan
            [
                'rate_type' => 'golongan',
                'rate_code' => 'GOL1',
                'rate_name' => 'Golongan I',
                'description' => 'Golongan I (Gol I/a - I/d)',
                'monthly_amount' => 20000.00,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'rate_type' => 'golongan',
                'rate_code' => 'GOL2',
                'rate_name' => 'Golongan II',
                'description' => 'Golongan II (Gol II/a - II/d)',
                'monthly_amount' => 25000.00,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'rate_type' => 'golongan',
                'rate_code' => 'GOL3',
                'rate_name' => 'Golongan III',
                'description' => 'Golongan III (Gol III/a - III/d)',
                'monthly_amount' => 30000.00,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'rate_type' => 'golongan',
                'rate_code' => 'GOL4',
                'rate_name' => 'Golongan IV',
                'description' => 'Golongan IV (Gol IV/a - IV/e)',
                'monthly_amount' => 35000.00,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Tarif Gaji (0.25% dari gaji)
            [
                'rate_type' => 'gaji',
                'rate_code' => 'GAJI1',
                'rate_name' => 'Gaji < 3 Juta',
                'description' => 'Iuran untuk gaji dibawah 3 juta',
                'monthly_amount' => 7500.00, // 0.25% dari 3 juta
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'rate_type' => 'gaji',
                'rate_code' => 'GAJI2',
                'rate_name' => 'Gaji 3-5 Juta',
                'description' => 'Iuran untuk gaji 3-5 juta',
                'monthly_amount' => 10000.00, // 0.25% dari 4 juta (rata-rata)
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'rate_type' => 'gaji',
                'rate_code' => 'GAJI3',
                'rate_name' => 'Gaji 5-10 Juta',
                'description' => 'Iuran untuk gaji 5-10 juta',
                'monthly_amount' => 18750.00, // 0.25% dari 7.5 juta (rata-rata)
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'rate_type' => 'gaji',
                'rate_code' => 'GAJI4',
                'rate_name' => 'Gaji > 10 Juta',
                'description' => 'Iuran untuk gaji diatas 10 juta',
                'monthly_amount' => 30000.00, // 0.25% dari 12 juta
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('sp_dues_rates')->insertBatch($data);

        echo "Dues rates seeded successfully! Total: " . count($data) . " rates\n";
    }
}
