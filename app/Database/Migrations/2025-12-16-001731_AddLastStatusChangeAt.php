<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLastStatusChangeAt extends Migration
{
    public function up()
    {
        $fields = [
            'last_status_change_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Timestamp when role, status, or permissions changed - used for session invalidation',
                'after' => 'last_login_at',
            ],
        ];

        $this->forge->addColumn('sp_members', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('sp_members', 'last_status_change_at');
    }
}
