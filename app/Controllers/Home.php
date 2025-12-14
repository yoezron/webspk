<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $data = [
            'title' => 'Beranda',
            'description' => 'Sistem Informasi Keanggotaan Serikat Pekerja Kampus - Memperjuangkan Hak dan Kesejahteraan Pekerja Kampus',
            'keywords' => 'serikat pekerja, kampus, keanggotaan, kesejahteraan pekerja',

            // Contact info (nanti dari CMS)
            'contact_phone' => '+62 123 456 789',
            'contact_email' => 'info@spk.local',
            'office_address' => 'Jl. Kampus Raya No. 123, Jakarta',
            'office_maps_url' => '#',

            // Social media (nanti dari CMS)
            'social_facebook' => '#',
            'social_twitter' => '#',
            'social_linkedin' => '#',
            'social_youtube' => '#',
            'social_instagram' => '#',
        ];

        return view('public/home', $data);
    }
}
