<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAsetSubUnitsTable extends Migration
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
            'kode_sub_unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'nama_sub_unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'index'      => true,
            ],
            'jenis_sub_unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
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
        // Unique: kode_sub_unit per unit_kerja_id
        $this->forge->addUniqueKey(['unit_kerja_id', 'kode_sub_unit'], 'uk_sub_unit_per_unit');
        
        $this->forge->createTable('aset_sub_units');
    }

    public function down()
    {
        $this->forge->dropTable('aset_sub_units');
    }
}