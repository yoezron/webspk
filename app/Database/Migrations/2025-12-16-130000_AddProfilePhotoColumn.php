<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfilePhotoColumn extends Migration
{
    public function up()
    {
        // Check if column already exists
        if (!$this->db->fieldExists('profile_photo', 'sp_members')) {
            $fields = [
                'profile_photo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'gender',
                    'comment' => 'Profile photo filename',
                ],
            ];

            $this->forge->addColumn('sp_members', $fields);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('sp_members', 'profile_photo');
    }
}
