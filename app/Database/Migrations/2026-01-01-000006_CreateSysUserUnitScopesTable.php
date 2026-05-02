<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSysUserUnitScopesTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'index'      => true,
            ],
            'unit_kerja_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'index'      => true,
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
        $this->forge->createTable('sys_user_unit_scopes');
    }

    public function down()
    {
        $this->forge->dropTable('sys_user_unit_scopes');
    }
}
