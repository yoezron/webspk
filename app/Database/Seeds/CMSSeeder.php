<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CMSSeeder extends Seeder
{
    public function run()
    {
        // Seed default CMS pages
        $this->seedPages();

        // Seed document categories
        $this->seedDocumentCategories();

        // Seed default landing page sections
        $this->seedLandingSections();

        // Optional: Seed sample news (commented out by default)
        // $this->seedSampleNews();

        echo "CMS seeder completed successfully!\n";
    }

    /**
     * Seed default CMS pages
     */
    private function seedPages()
    {
        $pages = [
            [
                'slug' => 'tentang-spk',
                'title' => 'Tentang Serikat Pekerja Kampus',
                'content_html' => '<h2>Tentang Kami</h2><p>Serikat Pekerja Kampus (SPK) adalah organisasi yang memperjuangkan hak dan kesejahteraan pekerja kampus di seluruh Indonesia.</p><p>Kami berkomitmen untuk memberikan perlindungan, advokasi, dan pemberdayaan bagi seluruh anggota kami.</p>',
                'template' => 'default',
                'status' => 'published',
                'visibility' => 'public',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'sejarah',
                'title' => 'Sejarah SPK',
                'content_html' => '<h2>Sejarah Serikat Pekerja Kampus</h2><p>SPK didirikan pada tahun 2020 dengan tujuan...</p>',
                'template' => 'default',
                'status' => 'published',
                'visibility' => 'public',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'visi-misi',
                'title' => 'Visi dan Misi',
                'content_html' => '<h2>Visi</h2><p>Menjadi organisasi serikat pekerja kampus yang kuat, mandiri, dan demokratis.</p><h2>Misi</h2><ul><li>Memperjuangkan kesejahteraan anggota</li><li>Memberikan perlindungan hukum</li><li>Meningkatkan kapasitas anggota</li></ul>',
                'template' => 'default',
                'status' => 'published',
                'visibility' => 'public',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'manifesto',
                'title' => 'Manifesto SPK',
                'content_html' => '<h2>Manifesto Serikat Pekerja Kampus</h2><p>Kami percaya bahwa setiap pekerja kampus berhak atas...</p>',
                'template' => 'default',
                'status' => 'published',
                'visibility' => 'public',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'ad-art',
                'title' => 'Anggaran Dasar dan Anggaran Rumah Tangga',
                'content_html' => '<h2>AD/ART SPK</h2><h3>Anggaran Dasar</h3><p>...</p><h3>Anggaran Rumah Tangga</h3><p>...</p>',
                'template' => 'legal',
                'status' => 'published',
                'visibility' => 'member_only',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Kebijakan Privasi',
                'content_html' => '<h2>Kebijakan Privasi</h2><p>Kami menghormati privasi Anda dan berkomitmen untuk melindungi data pribadi Anda...</p>',
                'template' => 'legal',
                'status' => 'published',
                'visibility' => 'public',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'slug' => 'terms-of-service',
                'title' => 'Syarat dan Ketentuan',
                'content_html' => '<h2>Syarat dan Ketentuan</h2><p>Dengan menggunakan layanan kami, Anda menyetujui...</p>',
                'template' => 'legal',
                'status' => 'published',
                'visibility' => 'public',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $builder = $this->db->table('cms_pages');

        foreach ($pages as $page) {
            // Check if page already exists
            $existing = $builder->where('slug', $page['slug'])->get()->getRowArray();

            if (!$existing) {
                $builder->insert($page);
                echo "Created page: {$page['title']}\n";
            } else {
                echo "Page already exists: {$page['title']}\n";
            }
        }
    }

    /**
     * Seed document categories
     */
    private function seedDocumentCategories()
    {
        $categories = [
            // Publikasi categories
            ['doc_type' => 'publikasi', 'name' => 'Newsletter', 'slug' => 'newsletter', 'sort_order' => 1],
            ['doc_type' => 'publikasi', 'name' => 'Laporan Tahunan', 'slug' => 'laporan-tahunan', 'sort_order' => 2],
            ['doc_type' => 'publikasi', 'name' => 'Jurnal & Artikel', 'slug' => 'jurnal-artikel', 'sort_order' => 3],
            ['doc_type' => 'publikasi', 'name' => 'Buku Panduan', 'slug' => 'buku-panduan', 'sort_order' => 4],
            ['doc_type' => 'publikasi', 'name' => 'Policy Brief', 'slug' => 'policy-brief', 'sort_order' => 5],

            // Regulasi categories
            ['doc_type' => 'regulasi', 'name' => 'Undang-Undang', 'slug' => 'undang-undang', 'sort_order' => 1],
            ['doc_type' => 'regulasi', 'name' => 'Peraturan Pemerintah', 'slug' => 'peraturan-pemerintah', 'sort_order' => 2],
            ['doc_type' => 'regulasi', 'name' => 'Peraturan Menteri', 'slug' => 'peraturan-menteri', 'sort_order' => 3],
            ['doc_type' => 'regulasi', 'name' => 'Peraturan Daerah', 'slug' => 'peraturan-daerah', 'sort_order' => 4],
            ['doc_type' => 'regulasi', 'name' => 'Keputusan Menteri', 'slug' => 'keputusan-menteri', 'sort_order' => 5],
        ];

        $builder = $this->db->table('cms_document_categories');

        foreach ($categories as $category) {
            $existing = $builder->where('slug', $category['slug'])->get()->getRowArray();

            if (!$existing) {
                $builder->insert($category);
                echo "Created document category: {$category['name']}\n";
            } else {
                echo "Document category already exists: {$category['name']}\n";
            }
        }
    }

    /**
     * Seed default landing page sections
     */
    private function seedLandingSections()
    {
        $sections = [
            [
                'section_type' => 'hero',
                'title' => 'Selamat Datang di Serikat Pekerja Kampus',
                'subtitle' => 'Bersama Memperjuangkan Hak dan Kesejahteraan Pekerja Kampus',
                'content_html' => '<p>Bergabunglah dengan kami untuk mewujudkan kesejahteraan pekerja kampus yang lebih baik.</p>',
                'settings_json' => json_encode([
                    'button_text' => 'Daftar Sekarang',
                    'button_link' => '/register',
                    'show_stats' => true,
                ]),
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'section_type' => 'stats',
                'title' => 'Statistik SPK',
                'subtitle' => 'Data Keanggotaan dan Aktivitas',
                'content_html' => null,
                'settings_json' => json_encode([
                    'show_members' => true,
                    'show_regions' => true,
                    'show_events' => true,
                ]),
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'section_type' => 'features',
                'title' => 'Keunggulan Menjadi Anggota',
                'subtitle' => 'Berbagai manfaat yang Anda dapatkan',
                'content_html' => '<ul><li>Perlindungan hukum</li><li>Advokasi kesejahteraan</li><li>Networking dan pengembangan karir</li></ul>',
                'settings_json' => json_encode([
                    'features' => [
                        ['icon' => 'fa-shield', 'title' => 'Perlindungan Hukum', 'desc' => 'Bantuan hukum untuk anggota'],
                        ['icon' => 'fa-heart', 'title' => 'Kesejahteraan', 'desc' => 'Program kesejahteraan anggota'],
                        ['icon' => 'fa-users', 'title' => 'Networking', 'desc' => 'Jaringan profesional luas'],
                    ]
                ]),
                'sort_order' => 3,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'section_type' => 'news',
                'title' => 'Berita Terbaru',
                'subtitle' => 'Update terkini dari SPK',
                'content_html' => null,
                'settings_json' => json_encode([
                    'limit' => 3,
                    'show_excerpt' => true,
                ]),
                'sort_order' => 4,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'section_type' => 'cta',
                'title' => 'Bergabung dengan Kami',
                'subtitle' => 'Daftarkan diri Anda sebagai anggota SPK',
                'content_html' => '<p>Bersama kita lebih kuat. Mari berjuang bersama untuk kesejahteraan pekerja kampus.</p>',
                'settings_json' => json_encode([
                    'button_text' => 'Daftar Sekarang',
                    'button_link' => '/register',
                    'button_style' => 'primary',
                ]),
                'sort_order' => 5,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $builder = $this->db->table('cms_home_sections');

        foreach ($sections as $section) {
            $existing = $builder->where('section_type', $section['section_type'])
                               ->where('sort_order', $section['sort_order'])
                               ->get()->getRowArray();

            if (!$existing) {
                $builder->insert($section);
                echo "Created landing section: {$section['title']}\n";
            } else {
                echo "Landing section already exists: {$section['title']}\n";
            }
        }
    }

    /**
     * Seed sample news posts (optional)
     */
    private function seedSampleNews()
    {
        // Get super admin ID for author
        $admin = $this->db->table('sp_members')
                         ->where('role', 'super_admin')
                         ->get()
                         ->getRowArray();

        if (!$admin) {
            echo "Warning: No super admin found. Skipping sample news.\n";
            return;
        }

        $newsPosts = [
            [
                'title' => 'Selamat Datang di Portal SPK',
                'slug' => 'selamat-datang-di-portal-spk',
                'excerpt' => 'Kami dengan bangga mempersembahkan portal baru Serikat Pekerja Kampus.',
                'content_html' => '<p>Kami dengan bangga mempersembahkan portal baru Serikat Pekerja Kampus yang dirancang untuk memberikan layanan lebih baik kepada seluruh anggota.</p><p>Portal ini dilengkapi dengan berbagai fitur untuk memudahkan komunikasi dan akses informasi.</p>',
                'status' => 'published',
                'author_id' => $admin['id'],
                'published_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $builder = $this->db->table('cms_news_posts');

        foreach ($newsPosts as $post) {
            $existing = $builder->where('slug', $post['slug'])->get()->getRowArray();

            if (!$existing) {
                $builder->insert($post);
                echo "Created news post: {$post['title']}\n";
            } else {
                echo "News post already exists: {$post['title']}\n";
            }
        }
    }
}
