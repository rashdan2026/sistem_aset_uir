<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAsetLantaiTable extends Migration
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
            'gedung_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'index'      => true,
            ],
            'kode_lantai' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'nama_lantai' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'nomor_lantai' => [
                'type'       => 'INT',
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
        // Unique: kode_lantai per gedung
        $this->forge->addUniqueKey(['gedung_id', 'kode_lantai'], 'uk_lantai_per_gedung');
        // Index for ordering
        $this->forge->addKey(['gedung_id', 'nomor_lantai'], false, false, 'idx_lantai_gedung_nomor');
        
        $this->forge->addForeignKey('gedung_id', 'aset_gedung', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('aset_lantai');
    }

    public function down()
    {
        $this->forge->dropTable('aset_lantai');
    }
}