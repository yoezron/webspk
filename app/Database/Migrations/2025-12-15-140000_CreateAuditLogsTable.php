<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuditLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'User who performed the action',
            ],
            'user_email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'user_role' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Action performed (e.g., member.approve, payment.verify)',
            ],
            'action_type' => [
                'type' => 'ENUM',
                'constraint' => ['create', 'read', 'update', 'delete', 'approve', 'reject', 'verify', 'suspend', 'activate', 'login', 'logout'],
                'default' => 'read',
            ],
            'target_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Type of target (member, payment, role, etc.)',
            ],
            'target_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID of affected record',
            ],
            'target_identifier' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Human-readable identifier (member number, email, etc.)',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Human-readable description of action',
            ],
            'old_values' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Old values before change (for updates)',
            ],
            'new_values' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'New values after change (for updates)',
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'request_method' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'request_uri' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['success', 'failed', 'error'],
                'default' => 'success',
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('action');
        $this->forge->addKey('action_type');
        $this->forge->addKey('target_type');
        $this->forge->addKey('target_id');
        $this->forge->addKey('created_at');
        $this->forge->addKey(['user_id', 'created_at']);

        $this->forge->addForeignKey('user_id', 'sp_members', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('audit_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('audit_logs', true);
    }
}
