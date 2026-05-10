<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingUnitKerjaAllowedTable extends Migration
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
            'id_unit_kerja' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
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
        $this->forge->addUniqueKey('id_unit_kerja');
        $this->forge->createTable('setting_unit_kerja_allowed', true);

        $db = db_connect();
        $db->query("
            INSERT INTO setting_unit_kerja_allowed (id_unit_kerja, is_active, created_at, updated_at)
            SELECT id_unit_kerja, 1, NOW(), NOW()
            FROM tbl_unit_kerja
            WHERE flag_aktif = 1
        ");
    }

    public function down()
    {
        $this->forge->dropTable('setting_unit_kerja_allowed', true);
    }
}
