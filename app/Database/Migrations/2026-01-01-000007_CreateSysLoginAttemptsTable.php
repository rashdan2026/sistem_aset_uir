<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSysLoginAttemptsTable extends Migration
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
                'null'       => true,
                'index'      => true,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
            ],
            'attempt_time' => [
                'type'       => 'DATETIME',
            ],
            'is_success' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'default'    => 0,
                'index'      => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('sys_login_attempts');
    }

    public function down()
    {
        $this->forge->dropTable('sys_login_attempts');
    }
}
