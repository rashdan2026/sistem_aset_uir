<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateJenisRuanganToEnum extends Migration
{
    public function up()
    {
        $db = db_connect();

        $db->query("ALTER TABLE `aset_ruangan` MODIFY COLUMN `jenis_ruangan`
            ENUM('Kantor', 'Ruang Kuliah', 'Laboratorium', 'Perpustakaan', 'Gudang', 'Lainnya')
            NULL
            AFTER `nama_ruangan`");
    }

    public function down()
    {
        $db = db_connect();

        $db->query("ALTER TABLE `aset_ruangan` MODIFY COLUMN `jenis_ruangan`
            VARCHAR(50)
            NULL
            AFTER `nama_ruangan`");
    }
}