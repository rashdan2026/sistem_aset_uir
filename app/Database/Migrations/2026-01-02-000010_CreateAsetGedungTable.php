<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAsetGedungTable extends Migration
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
            'unit_kerja_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'index'      => true,
                'comment'    => 'Relasi logis ke tbl_unit_kerja.id_unit_kerja (bukan FK fisik)',
            ],
            'kode_gedung' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'unique'     => true,
            ],
            'nama_gedung' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'index'      => true,
            ],
            'alamat_ringkas' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'jumlah_lantai' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->createTable('aset_gedung');
    }

    public function down()
    {
        $this->forge->dropTable('aset_gedung');
    }
}