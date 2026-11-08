<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveKelompokJenisAsetFields extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $db->query("ALTER TABLE `aset_kategori` MODIFY `golongan_id` INT(6) UNSIGNED NOT NULL");

        $this->forge->dropColumn('aset_golongan', 'kelompok');
        $this->forge->dropColumn('aset_kategori', 'jenis_aset');

        $indexExists = $db->query("SHOW INDEX FROM `aset_kategori` WHERE Key_name = 'idx_kategori_golongan_id'")->getNumRows();
        if ($indexExists == 0) {
            $db->query("ALTER TABLE `aset_kategori` ADD INDEX `idx_kategori_golongan_id` (`golongan_id`)");
        }

        $fkExists = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'aset_kategori' AND COLUMN_NAME = 'golongan_id' AND REFERENCED_TABLE_NAME = 'aset_golongan' AND CONSTRAINT_SCHEMA = DATABASE()")->getNumRows();
        if ($fkExists == 0) {
            $db->query("ALTER TABLE `aset_kategori` ADD CONSTRAINT `fk_kategori_golongan` FOREIGN KEY (`golongan_id`) REFERENCES `aset_golongan`(`gl_id`) ON UPDATE CASCADE ON DELETE RESTRICT");
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        $fkExists = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'aset_kategori' AND CONSTRAINT_NAME = 'fk_kategori_golongan' AND CONSTRAINT_SCHEMA = DATABASE()")->getNumRows();
        if ($fkExists > 0) {
            $db->query("ALTER TABLE `aset_kategori` DROP FOREIGN KEY `fk_kategori_golongan`");
        }

        $this->forge->addColumn('aset_golongan', [
            'kelompok' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
        ]);

        $this->forge->addColumn('aset_kategori', [
            'jenis_aset' => [
                'type'       => 'ENUM',
                'constraint' => ['bangunan', 'ruangan', 'non_bangunan', 'lainnya'],
            ],
        ]);

        $db->query("ALTER TABLE `aset_kategori` MODIFY `golongan_id` INT(6) UNSIGNED NULL");
    }
}
