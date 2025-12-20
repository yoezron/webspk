<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCoordinatorAssignmentFields extends Migration
{
    public function up()
    {
        // Add coordinator assignment fields to sp_members
        $fields = [
            'assigned_region_code' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
                'comment' => 'Region code assigned to this member (for coordinators)',
            ],
            'coordinator_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Coordinator responsible for this member',
            ],
        ];

        $this->forge->addColumn('sp_members', $fields);

        // Add foreign key constraints
        $this->db->query('
            ALTER TABLE sp_members
            ADD CONSTRAINT fk_member_coordinator
            FOREIGN KEY (coordinator_id) REFERENCES sp_members(id)
            ON DELETE SET NULL ON UPDATE CASCADE
        ');

        // Note: We don't add FK for assigned_region_code yet because region_code is not unique
        // This will be added after data cleanup

        // Add indexes for better query performance
        $this->db->query('CREATE INDEX idx_assigned_region ON sp_members(assigned_region_code)');
        $this->db->query('CREATE INDEX idx_coordinator ON sp_members(coordinator_id)');

        // Create coordinator_regions table for many-to-many relationship
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'coordinator_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'region_code' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'assigned_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'assigned_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
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
        $this->forge->addKey(['coordinator_id', 'region_code'], false, true); // Unique combination
        $this->forge->addForeignKey('coordinator_id', 'sp_members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assigned_by', 'sp_members', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('coordinator_regions');
    }

    public function down()
    {
        // Drop coordinator_regions table
        $this->forge->dropTable('coordinator_regions');

        // Drop foreign key and indexes
        $this->db->query('ALTER TABLE sp_members DROP FOREIGN KEY fk_member_coordinator');
        $this->db->query('DROP INDEX idx_assigned_region ON sp_members');
        $this->db->query('DROP INDEX idx_coordinator ON sp_members');

        // Drop columns
        $this->forge->dropColumn('sp_members', ['assigned_region_code', 'coordinator_id']);
    }
}
