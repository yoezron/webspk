<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RegionCodesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['province_name' => 'Aceh', 'region_code' => 'ACE', 'is_active' => 1],
            ['province_name' => 'Sumatera Utara', 'region_code' => 'SUM', 'is_active' => 1],
            ['province_name' => 'Sumatera Barat', 'region_code' => 'SBA', 'is_active' => 1],
            ['province_name' => 'Riau', 'region_code' => 'RIA', 'is_active' => 1],
            ['province_name' => 'Kepulauan Riau', 'region_code' => 'KEP', 'is_active' => 1],
            ['province_name' => 'Jambi', 'region_code' => 'JAM', 'is_active' => 1],
            ['province_name' => 'Sumatera Selatan', 'region_code' => 'SSE', 'is_active' => 1],
            ['province_name' => 'Bangka Belitung', 'region_code' => 'BAN', 'is_active' => 1],
            ['province_name' => 'Bengkulu', 'region_code' => 'BEN', 'is_active' => 1],
            ['province_name' => 'Lampung', 'region_code' => 'LAM', 'is_active' => 1],
            ['province_name' => 'DKI Jakarta', 'region_code' => 'JKT', 'is_active' => 1],
            ['province_name' => 'Banten', 'region_code' => 'BTN', 'is_active' => 1],
            ['province_name' => 'Jawa Barat', 'region_code' => 'JBA', 'is_active' => 1],
            ['province_name' => 'Jawa Tengah', 'region_code' => 'JTE', 'is_active' => 1],
            ['province_name' => 'DI Yogyakarta', 'region_code' => 'YOG', 'is_active' => 1],
            ['province_name' => 'Jawa Timur', 'region_code' => 'JTI', 'is_active' => 1],
            ['province_name' => 'Bali', 'region_code' => 'BAL', 'is_active' => 1],
            ['province_name' => 'Nusa Tenggara Barat', 'region_code' => 'NTB', 'is_active' => 1],
            ['province_name' => 'Nusa Tenggara Timur', 'region_code' => 'NTT', 'is_active' => 1],
            ['province_name' => 'Kalimantan Barat', 'region_code' => 'KBA', 'is_active' => 1],
            ['province_name' => 'Kalimantan Tengah', 'region_code' => 'KTE', 'is_active' => 1],
            ['province_name' => 'Kalimantan Selatan', 'region_code' => 'KSE', 'is_active' => 1],
            ['province_name' => 'Kalimantan Timur', 'region_code' => 'KTI', 'is_active' => 1],
            ['province_name' => 'Kalimantan Utara', 'region_code' => 'KUT', 'is_active' => 1],
            ['province_name' => 'Sulawesi Utara', 'region_code' => 'SUT', 'is_active' => 1],
            ['province_name' => 'Sulawesi Tengah', 'region_code' => 'STE', 'is_active' => 1],
            ['province_name' => 'Sulawesi Selatan', 'region_code' => 'SSE', 'is_active' => 1],
            ['province_name' => 'Sulawesi Tenggara', 'region_code' => 'STG', 'is_active' => 1],
            ['province_name' => 'Gorontalo', 'region_code' => 'GOR', 'is_active' => 1],
            ['province_name' => 'Sulawesi Barat', 'region_code' => 'SBA', 'is_active' => 1],
            ['province_name' => 'Maluku', 'region_code' => 'MAL', 'is_active' => 1],
            ['province_name' => 'Maluku Utara', 'region_code' => 'MUT', 'is_active' => 1],
            ['province_name' => 'Papua', 'region_code' => 'PAP', 'is_active' => 1],
            ['province_name' => 'Papua Barat', 'region_code' => 'PBA', 'is_active' => 1],
        ];

        foreach ($data as &$item) {
            $item['created_at'] = date('Y-m-d H:i:s');
            $item['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->table('sp_region_codes')->insertBatch($data);

        echo "Region codes seeded successfully! Total: " . count($data) . " provinces\n";
    }
}
