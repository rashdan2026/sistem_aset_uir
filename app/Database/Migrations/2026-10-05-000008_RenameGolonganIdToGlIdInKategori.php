<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameGolonganIdToGlIdInKategori extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $fkExists = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'aset_kategori' AND CONSTRAINT_NAME = 'fk_kategori_golongan' AND CONSTRAINT_SCHEMA = DATABASE()")->getNumRows();
        if ($fkExists > 0) {
            $db->query("ALTER TABLE `aset_kategori` DROP FOREIGN KEY `fk_kategori_golongan`");
        }

        $indexExists = $db->query("SHOW INDEX FROM `aset_kategori` WHERE Key_name = 'idx_kategori_golongan_id'")->getNumRows();
        if ($indexExists > 0) {
            $db->query("ALTER TABLE `aset_kategori` DROP INDEX `idx_kategori_golongan_id`");
        }

        $db->query("ALTER TABLE `aset_kategori` CHANGE `golongan_id` `gl_id` INT(6) UNSIGNED NOT NULL");

        $db->query("ALTER TABLE `aset_kategori` ADD INDEX `idx_kategori_gl_id` (`gl_id`)");

        $db->query("ALTER TABLE `aset_kategori` ADD CONSTRAINT `fk_kategori_golongan` FOREIGN KEY (`gl_id`) REFERENCES `aset_golongan`(`gl_id`) ON UPDATE CASCADE ON DELETE RESTRICT");
    }

    public function down()
    {
        $db = \Config\Database::connect();

        $fkExists = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'aset_kategori' AND CONSTRAINT_NAME = 'fk_kategori_golongan' AND CONSTRAINT_SCHEMA = DATABASE()")->getNumRows();
        if ($fkExists > 0) {
            $db->query("ALTER TABLE `aset_kategori` DROP FOREIGN KEY `fk_kategori_golongan`");
        }

        $indexExists = $db->query("SHOW INDEX FROM `aset_kategori` WHERE Key_name = 'idx_kategori_gl_id'")->getNumRows();
        if ($indexExists > 0) {
            $db->query("ALTER TABLE `aset_kategori` DROP INDEX `idx_kategori_gl_id`");
        }

        $db->query("ALTER TABLE `aset_kategori` CHANGE `gl_id` `golongan_id` INT(6) UNSIGNED NOT NULL");

        $db->query("ALTER TABLE `aset_kategori` ADD INDEX `idx_kategori_golongan_id` (`golongan_id`)");

        $db->query("ALTER TABLE `aset_kategori` ADD CONSTRAINT `fk_kategori_golongan` FOREIGN KEY (`golongan_id`) REFERENCES `aset_golongan`(`gl_id`) ON UPDATE CASCADE ON DELETE RESTRICT");
    }
}
