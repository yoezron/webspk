<?php

namespace App\Controllers;

use App\Models\MemberModel;

class Home extends BaseController
{
    protected $memberModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
    }

    public function index(): string
    {
        // Get statistics
        $db = \Config\Database::connect();

        // Total active members
        $total_users = $this->memberModel
            ->where('membership_status', 'active')
            ->where('account_status', 'active')
            ->countAllResults();

        // Members by province
        $per_provinsi = $db->query("
            SELECT province, COUNT(*) as total
            FROM sp_members
            WHERE membership_status = 'active'
            AND account_status = 'active'
            AND province IS NOT NULL
            GROUP BY province
            ORDER BY total DESC
        ")->getResultArray();

        // Total universities/campuses
        $total_kampus = $db->query("
            SELECT COUNT(DISTINCT university_name) as total
            FROM sp_members
            WHERE membership_status = 'active'
            AND university_name IS NOT NULL
        ")->getRow()->total ?? 0;

        // Total regions
        $total_wilayah = $db->query("
            SELECT COUNT(DISTINCT province) as total
            FROM sp_members
            WHERE membership_status = 'active'
            AND province IS NOT NULL
        ")->getRow()->total ?? 0;

        // Total cities
        $total_kota = $db->query("
            SELECT COUNT(DISTINCT city) as total
            FROM sp_members
            WHERE membership_status = 'active'
            AND city IS NOT NULL
        ")->getRow()->total ?? 0;

        // Get recent posts/news (placeholder - nanti dari CMS/Blog module)
        $all_posts = [
            // Contoh data untuk testing
            // [
            //     'slug' => 'berita-1',
            //     'gambar' => 'news1.jpg',
            //     'penulis' => 'Admin SPK',
            //     'waktu_posting' => date('Y-m-d H:i:s'),
            //     'tag' => 'Berita',
            //     'judul_tulisan' => 'Judul Berita 1'
            // ]
        ];

        $data = [
            'title' => 'Beranda - Serikat Pekerja Kampus',
            'description' => 'Sistem Informasi Keanggotaan Serikat Pekerja Kampus - Memperjuangkan Hak dan Kesejahteraan Pekerja Kampus',
            'keywords' => 'serikat pekerja, kampus, keanggotaan, kesejahteraan pekerja',

            // Statistics
            'total_users' => $total_users,
            'total_kampus' => $total_kampus,
            'total_wilayah' => $total_wilayah,
            'total_kota' => $total_kota,
            'per_provinsi' => $per_provinsi,

            // Posts/News (akan diisi dari module Blog nanti)
            'all_posts' => $all_posts,

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
