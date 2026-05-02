<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAsetTypeTable extends Migration
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
            'merk_id' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'null'       => true,
                'index'      => true,
            ],
            'kode_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'nama_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
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
        ]);

        $this->forge->addKey('id', true);
        // Unique: nama_type per merk
        $this->forge->addUniqueKey(['merk_id', 'nama_type'], 'uk_type_per_merk');
        
        $this->forge->addForeignKey('merk_id', 'aset_merk', 'id', 'SET NULL', 'RESTRICT');
        $this->forge->createTable('aset_type');
    }

    public function down()
    {
        $this->forge->dropTable('aset_type');
    }
}