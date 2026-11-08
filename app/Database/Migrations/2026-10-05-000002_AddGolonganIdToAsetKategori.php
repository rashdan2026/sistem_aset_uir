<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGolonganIdToAsetKategori extends Migration
{
    public function up()
    {
        $fields = [
            'golongan_id' => [
                'type' => 'INT',
                'constraint' => 6,
                'unsigned' => true,
                'null' => true,
                'after' => 'jenis_aset',
            ],
        ];

        $this->forge->addColumn('aset_kategori', $fields);

        $this->forge->addForeignKey('golongan_id', 'aset_golongan', 'gl_id', 'SET NULL', 'SET NULL');
    }

    public function down()
    {
        $this->forge->dropForeignKey('aset_kategori', 'aset_kategori_golongan_id_foreign');
        $this->forge->dropColumn('aset_kategori', 'golongan_id');
    }
}