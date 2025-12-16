<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRememberTokenExpiry extends Migration
{
    public function up()
    {
        $fields = [
            'remember_token_expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'remember_token_hash',
            ],
        ];

        $this->forge->addColumn('sp_members', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('sp_members', 'remember_token_expires_at');
    }
}
