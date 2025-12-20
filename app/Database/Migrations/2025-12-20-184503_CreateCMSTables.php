<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCMSTables extends Migration
{
    public function up()
    {
        // ======================================================
        // 1. CMS_PAGES - Halaman Statis
        // ======================================================
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'content_html' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'template' => [
                'type' => 'ENUM',
                'constraint' => ['default', 'legal', 'contact'],
                'default' => 'default',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'published', 'archived'],
                'default' => 'draft',
            ],
            'visibility' => [
                'type' => 'ENUM',
                'constraint' => ['public', 'member_only'],
                'default' => 'public',
            ],
            'primary_document_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'published_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['status', 'published_at']);
        $this->forge->createTable('cms_pages', true);

        // ======================================================
        // 2. CMS_PAGE_REVISIONS - History Revisi Halaman
        // ======================================================
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'page_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'content_html' => [
                'type' => 'LONGTEXT',
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('page_id', 'cms_pages', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cms_page_revisions', true);

        // ======================================================
        // 3. CMS_HOME_SECTIONS - Section Landing Page
        // ======================================================
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'section_key' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'body_html' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'config_json' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'sort_order' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'is_enabled' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('cms_home_sections', true);

        // ======================================================
        // 4. CMS_DOCUMENT_CATEGORIES - Kategori Dokumen
        // ======================================================
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'doc_type' => [
                'type' => 'ENUM',
                'constraint' => ['publikasi', 'regulasi'],
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'sort_order' => [
                'type' => 'INT',
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['doc_type', 'slug']);
        $this->forge->createTable('cms_document_categories', true);

        // ======================================================
        // 5. CMS_DOCUMENTS - Publikasi & Regulasi PDF
        // ======================================================
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'doc_type' => [
                'type' => 'ENUM',
                'constraint' => ['publikasi', 'regulasi'],
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'unique' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'original_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'mime_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'application/pdf',
            ],
            'file_size' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'checksum_sha256' => [
                'type' => 'CHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'published', 'archived'],
                'default' => 'draft',
            ],
            'published_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'download_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['doc_type', 'status', 'published_at']);
        $this->forge->addForeignKey('category_id', 'cms_document_categories', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('cms_documents', true);

        // ======================================================
        // 6. CMS_MEDIA - Media Library (Images)
        // ======================================================
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'media_type' => [
                'type' => 'ENUM',
                'constraint' => ['image', 'file'],
                'default' => 'image',
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'original_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'mime_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'file_size' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'checksum_sha256' => [
                'type' => 'CHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'alt_text' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'uploaded_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'uploaded_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('cms_media', true);

        // ======================================================
        // 7. CMS_NEWS_POSTS - Berita/Blog
        // ======================================================
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'unique' => true,
            ],
            'excerpt' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'content_html' => [
                'type' => 'LONGTEXT',
            ],
            'cover_image_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'published', 'archived'],
                'default' => 'draft',
            ],
            'published_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'author_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'view_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['status', 'published_at']);
        $this->forge->addKey('author_id');
        $this->forge->addForeignKey('cover_image_id', 'cms_media', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('author_id', 'sp_members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cms_news_posts', true);

        // ======================================================
        // 8. CMS_OFFICERS - Struktur Pengurus
        // ======================================================
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'member_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'full_name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'position_title' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'level' => [
                'type' => 'ENUM',
                'constraint' => ['pusat', 'wilayah'],
                'default' => 'pusat',
            ],
            'region_code' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'photo_media_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'bio_html' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sort_order' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'period_start' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'period_end' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['level', 'is_active', 'sort_order']);
        $this->forge->addForeignKey('member_id', 'sp_members', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('photo_media_id', 'cms_media', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('cms_officers', true);

        // ======================================================
        // 9. CMS_SUBSCRIBERS - Newsletter Subscribers
        // ======================================================
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'unique' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'active', 'unsubscribed'],
                'default' => 'pending',
            ],
            'token_hash' => [
                'type' => 'CHAR',
                'constraint' => 64,
                'null' => true,
            ],
            'verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('cms_subscribers', true);

        // ======================================================
        // 10. CMS_CONTACT_MESSAGES - Contact Form Inbox
        // ======================================================
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['new', 'in_progress', 'closed'],
                'default' => 'new',
            ],
            'assigned_to' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'admin_reply' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'replied_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addForeignKey('assigned_to', 'sp_members', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('cms_contact_messages', true);
    }

    public function down()
    {
        // Drop tables in reverse order to respect foreign keys
        $this->forge->dropTable('cms_contact_messages', true);
        $this->forge->dropTable('cms_subscribers', true);
        $this->forge->dropTable('cms_officers', true);
        $this->forge->dropTable('cms_news_posts', true);
        $this->forge->dropTable('cms_media', true);
        $this->forge->dropTable('cms_documents', true);
        $this->forge->dropTable('cms_document_categories', true);
        $this->forge->dropTable('cms_home_sections', true);
        $this->forge->dropTable('cms_page_revisions', true);
        $this->forge->dropTable('cms_pages', true);
    }
}
