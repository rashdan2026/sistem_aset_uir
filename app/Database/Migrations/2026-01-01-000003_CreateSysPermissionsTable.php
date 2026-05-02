<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSysPermissionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 6,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'permission_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'module_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'index'      => true,
            ],
            'action_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'index'      => true,
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'default'    => 1,
                'index'      => true,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('sys_permissions');
    }

    public function down()
    {
        $this->forge->dropTable('sys_permissions');
    }
}
