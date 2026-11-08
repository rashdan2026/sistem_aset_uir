<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SimplifyAsetGolonganTable extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $fks = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'aset_golongan' AND COLUMN_NAME = 'parent_id' AND REFERENCED_TABLE_NAME IS NOT NULL")->getResultArray();
        foreach ($fks as $fk) {
            $this->forge->dropForeignKey('aset_golongan', $fk['CONSTRAINT_NAME']);
        }

        $this->forge->dropColumn('aset_golongan', 'parent_id');
        $this->forge->dropColumn('aset_golongan', 'level');
    }

    public function down()
    {
        $fields = [
            'parent_id' => [
                'type' => 'INT',
                'constraint' => 6,
                'unsigned' => true,
                'null' => true,
            ],
            'level' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 1,
            ],
        ];
        $this->forge->addColumn('aset_golongan', $fields);
        $this->forge->addForeignKey('parent_id', 'aset_golongan', 'gl_id', 'SET NULL', 'SET NULL');
    }
}
