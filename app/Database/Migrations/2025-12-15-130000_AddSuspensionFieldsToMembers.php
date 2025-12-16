<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSuspensionFieldsToMembers extends Migration
{
    public function up()
    {
        $fields = [
            'suspension_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'suspended_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'suspended_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ];

        $this->forge->addColumn('sp_members', $fields);

        // Add foreign key for suspended_by (wrap in try-catch to handle if already exists)
        try {
            $this->db->query('ALTER TABLE sp_members ADD CONSTRAINT fk_suspended_by FOREIGN KEY (suspended_by) REFERENCES sp_members(id) ON DELETE SET NULL ON UPDATE CASCADE');
        } catch (\Exception $e) {
            // Foreign key might already exist, ignore
            log_message('info', 'Foreign key fk_suspended_by might already exist: ' . $e->getMessage());
        }
    }

    public function down()
    {
        $this->forge->dropForeignKey('sp_members', 'fk_suspended_by');
        $this->forge->dropColumn('sp_members', ['suspension_reason', 'suspended_at', 'suspended_by']);
    }
}
