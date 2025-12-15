<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRBACTables extends Migration
{
    public function up()
    {
        // 1. RBAC Roles Table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'role_name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'role_slug' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
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
        $this->forge->addKey('role_slug');
        $this->forge->createTable('rbac_roles', true);

        // 2. RBAC Permissions Table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'permission_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'permission_slug' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'permission_group' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'members, payments, admin, reports, etc.',
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
        $this->forge->addKey('permission_slug');
        $this->forge->addKey('permission_group');
        $this->forge->createTable('rbac_permissions', true);

        // 3. RBAC Role-Permissions Junction Table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'permission_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['role_id', 'permission_id'], false, true); // Unique composite key
        $this->forge->addForeignKey('role_id', 'rbac_roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('permission_id', 'rbac_permissions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rbac_role_permissions', true);

        // 4. RBAC Menus Table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'parent_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'NULL for main menu, ID for submenu',
            ],
            'menu_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'menu_slug' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'menu_url' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'menu_icon' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Font Awesome icon class',
            ],
            'menu_order' => [
                'type' => 'INT',
                'constraint' => 5,
                'default' => 0,
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
        $this->forge->addKey('parent_id');
        $this->forge->addKey('menu_order');
        $this->forge->addForeignKey('parent_id', 'rbac_menus', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rbac_menus', true);

        // 5. RBAC Menu-Permissions Junction Table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'menu_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'permission_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['menu_id', 'permission_id'], false, true); // Unique composite key
        $this->forge->addForeignKey('menu_id', 'rbac_menus', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('permission_id', 'rbac_permissions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rbac_menu_permissions', true);

        // 6. User-Role Junction Table (for sp_members)
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
            'role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'assigned_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'assigned_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['member_id', 'role_id'], false, true); // Unique composite key
        $this->forge->addForeignKey('member_id', 'sp_members', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('role_id', 'rbac_roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assigned_by', 'sp_members', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('rbac_user_roles', true);
    }

    public function down()
    {
        $this->forge->dropTable('rbac_user_roles', true);
        $this->forge->dropTable('rbac_menu_permissions', true);
        $this->forge->dropTable('rbac_menus', true);
        $this->forge->dropTable('rbac_role_permissions', true);
        $this->forge->dropTable('rbac_permissions', true);
        $this->forge->dropTable('rbac_roles', true);
    }
}
