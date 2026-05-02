<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAsetKategoriTable extends Migration
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
            'kode_kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'unique'     => true,
            ],
            'nama_kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'unique'     => true,
            ],
            'jenis_aset' => [
                'type'       => 'ENUM',
                'constraint' => ['bangunan', 'ruangan', 'non_bangunan', 'lainnya'],
                'index'      => true,
            ],
            'keterangan' => [
                'type'       => 'TEXT',
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
            'deleted_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'index'      => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('aset_kategori');
    }

    public function down()
    {
        $this->forge->dropTable('aset_kategori');
    }
}