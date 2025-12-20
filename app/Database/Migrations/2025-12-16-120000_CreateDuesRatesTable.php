<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDuesRatesTable extends Migration
{
    public function up()
    {
        // Dues Rates Table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'rate_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Name/description of the rate',
            ],
            'rate_type' => [
                'type' => 'ENUM',
                'constraint' => ['monthly', 'yearly', 'one_time'],
                'default' => 'monthly',
                'comment' => 'Type of dues rate',
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'comment' => 'Dues amount',
            ],
            'member_category' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Applicable member category (null = all)',
            ],
            'region_code' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
                'comment' => 'Applicable region (null = all regions)',
            ],
            'effective_from' => [
                'type' => 'DATE',
                'comment' => 'Rate effective start date',
            ],
            'effective_to' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Rate effective end date (null = indefinite)',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => '1 = active, 0 = inactive',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Rate description or notes',
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
        $this->forge->addKey(['is_active', 'effective_from']);
        $this->forge->addKey(['rate_type', 'member_category']);
        $this->forge->addKey('region_code');

        $this->forge->createTable('dues_rates');

        // Insert default rates
        $data = [
            [
                'rate_name' => 'Iuran Bulanan Standar',
                'rate_type' => 'monthly',
                'amount' => 50000.00,
                'member_category' => null,
                'region_code' => null,
                'effective_from' => date('Y-01-01'),
                'effective_to' => null,
                'is_active' => 1,
                'description' => 'Tarif iuran bulanan standar untuk semua anggota',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'rate_name' => 'Iuran Tahunan Standar',
                'rate_type' => 'yearly',
                'amount' => 500000.00,
                'member_category' => null,
                'region_code' => null,
                'effective_from' => date('Y-01-01'),
                'effective_to' => null,
                'is_active' => 1,
                'description' => 'Tarif iuran tahunan dengan diskon 16.67%',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'rate_name' => 'Biaya Pendaftaran',
                'rate_type' => 'one_time',
                'amount' => 100000.00,
                'member_category' => 'candidate',
                'region_code' => null,
                'effective_from' => date('Y-01-01'),
                'effective_to' => null,
                'is_active' => 1,
                'description' => 'Biaya pendaftaran satu kali untuk anggota baru',
                'created_by' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('dues_rates')->insertBatch($data);

        // Dues Rate History Table (for audit trail)
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'rate_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'action' => [
                'type' => 'ENUM',
                'constraint' => ['created', 'updated', 'activated', 'deactivated'],
                'comment' => 'Action performed',
            ],
            'old_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],
            'new_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],
            'changed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'change_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('rate_id');
        $this->forge->addKey('created_at');

        $this->forge->addForeignKey('rate_id', 'dues_rates', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('dues_rate_history');
    }

    public function down()
    {
        $this->forge->dropTable('dues_rate_history', true);
        $this->forge->dropTable('dues_rates', true);
    }
}
