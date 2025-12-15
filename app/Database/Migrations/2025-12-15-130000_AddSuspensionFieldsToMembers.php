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
                'after' => 'status',
            ],
            'suspended_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'suspension_reason',
            ],
            'suspended_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'suspended_at',
            ],
        ];

        $this->forge->addColumn('sp_members', $fields);

        // Add foreign key for suspended_by
        $this->forge->addForeignKey('suspended_by', 'sp_members', 'id', 'SET NULL', 'CASCADE', 'fk_suspended_by');
    }

    public function down()
    {
        $this->forge->dropForeignKey('sp_members', 'fk_suspended_by');
        $this->forge->dropColumn('sp_members', ['suspension_reason', 'suspended_at', 'suspended_by']);
    }
}
