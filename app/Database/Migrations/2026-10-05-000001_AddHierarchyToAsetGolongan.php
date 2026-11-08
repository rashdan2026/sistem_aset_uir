<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHierarchyToAsetGolongan extends Migration
{
    public function up()
    {
        $fields = [
            'parent_id' => [
                'type' => 'INT',
                'constraint' => 6,
                'unsigned' => true,
                'null' => true,
                'after' => 'kelompok',
            ],
            'level' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 1,
                'after' => 'parent_id',
            ],
        ];

        $this->forge->addColumn('aset_golongan', $fields);

        $this->forge->addForeignKey('parent_id', 'aset_golongan', 'gl_id', 'SET NULL', 'SET NULL');
    }

    public function down()
    {
        $this->forge->dropForeignKey('aset_golongan', 'aset_golongan_parent_id_foreign');
        $this->forge->dropColumn('aset_golongan', 'parent_id');
        $this->forge->dropColumn('aset_golongan', 'level');
    }
}