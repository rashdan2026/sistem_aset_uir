<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAsetSubKategoriTable extends Migration
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
            'kategori_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'index'      => true,
            ],
            'kode_sub_kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'nama_sub_kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'index'      => true,
            ],
            'wajib_merk' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'default'    => 0,
            ],
            'wajib_type' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'default'    => 0,
            ],
            'wajib_ruangan' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'default'    => 1,
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
        // Unique: kode_sub_kategori per kategori
        $this->forge->addUniqueKey(['kategori_id', 'kode_sub_kategori'], 'uk_sub_kategori_per_kategori');
        
        $this->forge->addForeignKey('kategori_id', 'aset_kategori', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('aset_sub_kategori');
    }

    public function down()
    {
        $this->forge->dropTable('aset_sub_kategori');
    }
}