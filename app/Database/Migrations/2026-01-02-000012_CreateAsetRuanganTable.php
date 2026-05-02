<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAsetRuanganTable extends Migration
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
            'lantai_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'index'      => true,
            ],
            'sub_unit_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'index'      => true,
            ],
            'kode_ruangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'nama_ruangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'index'      => true,
            ],
            'jenis_ruangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'index'      => true,
            ],
            'penanggung_jawab_id_kpe' => [
                'type'       => 'VARCHAR',
                'constraint' => 16,
                'null'       => true,
                'index'      => true,
                'comment'    => 'Relasi logis ke ylpi_karyawan.id_kpe (bukan FK fisik)',
            ],
            'kapasitas' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
            ],
            'luas_m2' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
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
        // Unique: kode_ruangan per lantai
        $this->forge->addUniqueKey(['lantai_id', 'kode_ruangan'], 'uk_ruangan_per_lantai');
        
        // Foreign keys
        $this->forge->addForeignKey('lantai_id', 'aset_lantai', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('sub_unit_id', 'aset_sub_units', 'id', 'CASCADE', 'RESTRICT');
        
        $this->forge->createTable('aset_ruangan');
    }

    public function down()
    {
        $this->forge->dropTable('aset_ruangan');
    }
}