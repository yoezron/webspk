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
                'section_key' => 'about',
                'title' => 'Tentang Serikat Pekerja Kampus',
                'body_html' => '<p>Serikat Pekerja Kampus (SPK) memperjuangkan hak dan kesejahteraan pekerja kampus di seluruh Indonesia.</p>',
                'config_json' => null,
                'sort_order' => 1,
                'is_enabled' => 1,
            ],
            [
                'section_key' => 'stats',
                'title' => 'Statistik Anggota',
                'body_html' => null,
                'config_json' => json_encode([
                    'mode' => 'dynamic',
                    'cache_minutes' => 60,
                    'show_gender' => true,
                    'show_province' => true,
                ]),
                'sort_order' => 2,
                'is_enabled' => 1,
            ],
            [
                'section_key' => 'latest_publications',
                'title' => 'Publikasi Terkini',
                'body_html' => null,
                'config_json' => json_encode([
                    'source' => 'cms_documents',
                    'type' => 'publikasi',
                    'limit' => 6,
                ]),
                'sort_order' => 3,
                'is_enabled' => 1,
            ],
            [
                'section_key' => 'cta_join',
                'title' => 'Bergabung',
                'body_html' => null,
                'config_json' => json_encode([
                    'button_text' => 'Bergabung Sekarang',
                    'url' => '/register',
                ]),
                'sort_order' => 4,
                'is_enabled' => 1,
            ],
            [
                'section_key' => 'cta_login',
                'title' => 'Login',
                'body_html' => null,
                'config_json' => json_encode([
                    'button_text' => 'Login',
                    'url' => '/login',
                ]),
                'sort_order' => 5,
                'is_enabled' => 1,
            ],
            [
                'section_key' => 'officers',
                'title' => 'Pengurus SPK',
                'body_html' => null,
                'config_json' => json_encode([
                    'level' => 'pusat',
                    'limit' => 10,
                ]),
                'sort_order' => 6,
                'is_enabled' => 1,
            ],
            [
                'section_key' => 'subscribe',
                'title' => 'Subscribe Newsletter',
                'body_html' => null,
                'config_json' => json_encode([
                    'double_opt_in' => true,
                ]),
                'sort_order' => 7,
                'is_enabled' => 1,
            ],
            [
                'section_key' => 'footer',
                'title' => 'Footer',
                'body_html' => null,
                'config_json' => json_encode([
                    'address' => 'Alamat SPK',
                    'email' => 'info@spk.id',
                    'phone' => '08xx',
                    'socials' => [
                        ['label' => 'Instagram', 'url' => 'https://instagram.com/spk'],
                        ['label' => 'Twitter', 'url' => 'https://twitter.com/spk'],
                    ],
                ]),
                'sort_order' => 8,
                'is_enabled' => 1,
            ],
        ];

        $builder = $this->db->table('cms_home_sections');

        foreach ($sections as $section) {
            $existing = $builder->where('section_key', $section['section_key'])
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
