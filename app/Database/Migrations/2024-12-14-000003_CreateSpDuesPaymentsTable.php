<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSpDuesPaymentsTable extends Migration
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
            'member_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'payment_type' => [
                'type' => 'ENUM',
                'constraint' => ['monthly_dues', 'registration_fee', 'arrears', 'other'],
                'default' => 'monthly_dues',
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'payment_period' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'comment' => 'Format: MM/YYYY',
            ],
            'payment_month' => [
                'type' => 'INT',
                'constraint' => 2,
                'null' => true,
            ],
            'payment_year' => [
                'type' => 'INT',
                'constraint' => 4,
                'null' => true,
            ],
            'payment_date' => [
                'type' => 'DATE',
            ],
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Transfer, Cash, etc',
            ],
            'payment_proof' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'payment_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Transaction reference/ID',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'verified', 'rejected'],
                'default' => 'pending',
            ],
            'verified_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'verification_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('member_id');
        $this->forge->addKey('status');
        $this->forge->addKey(['payment_month', 'payment_year']);
        $this->forge->addForeignKey('member_id', 'sp_members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sp_dues_payments');
    }

    public function down()
    {
        $this->forge->dropTable('sp_dues_payments');
    }
}
