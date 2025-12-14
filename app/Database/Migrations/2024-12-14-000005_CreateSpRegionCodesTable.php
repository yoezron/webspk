<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSpRegionCodesTable extends Migration
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
            'province_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'region_code' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'description' => [
                'type' => 'TEXT',
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
        $this->forge->addUniqueKey('region_code');
        $this->forge->createTable('sp_region_codes');
    }

    public function down()
    {
        $this->forge->dropTable('sp_region_codes');
    }
}
