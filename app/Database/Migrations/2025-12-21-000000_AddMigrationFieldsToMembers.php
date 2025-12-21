<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMigrationFieldsToMembers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('sp_members', [
            'is_migrated' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'comment' => 'Flag untuk data yang diimport dari sistem lama',
                'after' => 'notes'
            ],
            'migrated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Waktu data diimport',
                'after' => 'is_migrated'
            ]
        ]);

        // Add index for better query performance
        $this->forge->addKey('is_migrated', false, false, 'idx_is_migrated');
        $this->db->query('ALTER TABLE sp_members ADD INDEX idx_is_migrated (is_migrated)');
    }

    public function down()
    {
        // Drop index first
        $this->db->query('ALTER TABLE sp_members DROP INDEX IF EXISTS idx_is_migrated');

        // Drop columns
        $this->forge->dropColumn('sp_members', ['is_migrated', 'migrated_at']);
    }
}
