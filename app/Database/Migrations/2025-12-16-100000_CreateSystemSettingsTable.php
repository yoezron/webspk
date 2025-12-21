<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSystemSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'setting_key' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'setting_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'setting_type' => [
                'type' => 'ENUM',
                'constraint' => ['string', 'integer', 'boolean', 'json', 'decimal'],
                'default' => 'string',
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'general',
                'comment' => 'general, dues, email, notification, system, security',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_public' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 if accessible by non-admin users',
            ],
            'is_encrypted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 if value should be encrypted',
            ],
            'validation_rules' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Validation rules for this setting',
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
        $this->forge->addKey('category');
        $this->forge->addForeignKey('updated_by', 'sp_members', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('system_settings');

        // Insert default settings
        $data = [
            // General Settings
            [
                'setting_key' => 'app_name',
                'setting_value' => 'Serikat Pekerja Kampus',
                'setting_type' => 'string',
                'category' => 'general',
                'description' => 'Nama aplikasi yang ditampilkan',
                'is_public' => 1,
                'validation_rules' => null,
            ],
            [
                'setting_key' => 'app_logo',
                'setting_value' => 'assets/img/logo/logo.png',
                'setting_type' => 'string',
                'category' => 'general',
                'description' => 'Path logo aplikasi',
                'is_public' => 1,
                'validation_rules' => null,
            ],
            [
                'setting_key' => 'maintenance_mode',
                'setting_value' => '0',
                'setting_type' => 'boolean',
                'category' => 'system',
                'description' => 'Aktifkan mode maintenance',
                'is_public' => 0,
                'validation_rules' => null,
            ],
            [
                'setting_key' => 'member_registration_open',
                'setting_value' => '1',
                'setting_type' => 'boolean',
                'category' => 'general',
                'description' => 'Buka pendaftaran anggota baru',
                'is_public' => 1,
                'validation_rules' => null,
            ],

            // Dues Settings
            [
                'setting_key' => 'default_monthly_dues',
                'setting_value' => '50000',
                'setting_type' => 'decimal',
                'category' => 'dues',
                'description' => 'Iuran bulanan default (Rp)',
                'is_public' => 1,
                'validation_rules' => 'required|numeric|greater_than[0]',
            ],
            [
                'setting_key' => 'arrears_penalty_rate',
                'setting_value' => '0',
                'setting_type' => 'decimal',
                'category' => 'dues',
                'description' => 'Denda keterlambatan per bulan (%)',
                'is_public' => 1,
                'validation_rules' => 'numeric|greater_than_equal_to[0]',
            ],
            [
                'setting_key' => 'max_arrears_months',
                'setting_value' => '6',
                'setting_type' => 'integer',
                'category' => 'dues',
                'description' => 'Maksimal bulan tunggakan sebelum penangguhan',
                'is_public' => 0,
                'validation_rules' => 'required|integer|greater_than[0]',
            ],
            [
                'setting_key' => 'payment_verification_days',
                'setting_value' => '3',
                'setting_type' => 'integer',
                'category' => 'dues',
                'description' => 'Target hari verifikasi pembayaran',
                'is_public' => 0,
                'validation_rules' => 'required|integer|greater_than[0]',
            ],

            // Email Settings
            [
                'setting_key' => 'email_from_address',
                'setting_value' => 'noreply@serikatpekerja.id',
                'setting_type' => 'string',
                'category' => 'email',
                'description' => 'Email pengirim',
                'is_public' => 0,
                'validation_rules' => 'required|valid_email',
            ],
            [
                'setting_key' => 'email_from_name',
                'setting_value' => 'Serikat Pekerja Kampus',
                'setting_type' => 'string',
                'category' => 'email',
                'description' => 'Nama pengirim email',
                'is_public' => 0,
                'validation_rules' => null,
            ],
            [
                'setting_key' => 'email_notifications_enabled',
                'setting_value' => '1',
                'setting_type' => 'boolean',
                'category' => 'email',
                'description' => 'Aktifkan notifikasi email',
                'is_public' => 0,
                'validation_rules' => null,
            ],

            // Notification Settings
            [
                'setting_key' => 'notify_member_approval',
                'setting_value' => '1',
                'setting_type' => 'boolean',
                'category' => 'notification',
                'description' => 'Notifikasi saat anggota disetujui',
                'is_public' => 0,
                'validation_rules' => null,
            ],
            [
                'setting_key' => 'notify_payment_verified',
                'setting_value' => '1',
                'setting_type' => 'boolean',
                'category' => 'notification',
                'description' => 'Notifikasi saat pembayaran diverifikasi',
                'is_public' => 0,
                'validation_rules' => null,
            ],
            [
                'setting_key' => 'notify_arrears_warning',
                'setting_value' => '1',
                'setting_type' => 'boolean',
                'category' => 'notification',
                'description' => 'Peringatan tunggakan otomatis',
                'is_public' => 0,
                'validation_rules' => null,
            ],

            // Security Settings
            [
                'setting_key' => 'max_login_attempts',
                'setting_value' => '5',
                'setting_type' => 'integer',
                'category' => 'security',
                'description' => 'Maksimal percobaan login gagal',
                'is_public' => 0,
                'validation_rules' => 'required|integer|greater_than[0]',
            ],
            [
                'setting_key' => 'lockout_duration_minutes',
                'setting_value' => '30',
                'setting_type' => 'integer',
                'category' => 'security',
                'description' => 'Durasi lockout akun (menit)',
                'is_public' => 0,
                'validation_rules' => 'required|integer|greater_than[0]',
            ],
            [
                'setting_key' => 'session_timeout_minutes',
                'setting_value' => '120',
                'setting_type' => 'integer',
                'category' => 'security',
                'description' => 'Timeout sesi login (menit)',
                'is_public' => 0,
                'validation_rules' => 'required|integer|greater_than[0]',
            ],
            [
                'setting_key' => 'password_reset_token_expiry',
                'setting_value' => '60',
                'setting_type' => 'integer',
                'category' => 'security',
                'description' => 'Masa berlaku token reset password (menit)',
                'is_public' => 0,
                'validation_rules' => 'required|integer|greater_than[0]',
            ],

            // System Settings
            [
                'setting_key' => 'audit_log_retention_days',
                'setting_value' => '365',
                'setting_type' => 'integer',
                'category' => 'system',
                'description' => 'Lama penyimpanan audit log (hari)',
                'is_public' => 0,
                'validation_rules' => 'required|integer|greater_than[0]',
            ],
            [
                'setting_key' => 'pagination_per_page',
                'setting_value' => '20',
                'setting_type' => 'integer',
                'category' => 'system',
                'description' => 'Jumlah item per halaman',
                'is_public' => 0,
                'validation_rules' => 'required|integer|greater_than[0]',
            ],
        ];

        $this->db->table('system_settings')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('system_settings');
    }
}
