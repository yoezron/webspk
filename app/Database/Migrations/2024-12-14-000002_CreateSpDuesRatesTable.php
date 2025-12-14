<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSpDuesRatesTable extends Migration
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
            'rate_type' => [
                'type' => 'ENUM',
                'constraint' => ['golongan', 'gaji'],
            ],
            'rate_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'rate_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'monthly_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
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
        $this->forge->addUniqueKey('rate_code');
        $this->forge->addKey('rate_type');
        $this->forge->createTable('sp_dues_rates');
    }

    public function down()
    {
        $this->forge->dropTable('sp_dues_rates');
    }
}
