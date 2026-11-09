<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Memperbaiki tipe data sys_users.id_kpe agar konsisten dengan
 * referensi ylpi_karyawan.id_kpe VARCHAR(16) (format 'KPXXXXXXX').
 *
 * Data INT yang sudah ada dikonversi otomatis oleh MariaDB saat MODIFY.
 * down(): konversi balik VARCHAR ke INT hanya aman bila semua nilai numerik.
 */
class FixSysUsersIdKpeType extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('sys_users', [
            'id_kpe' => [
                'type'       => 'VARCHAR',
                'constraint' => 16,
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('sys_users', [
            'id_kpe' => [
                'type'       => 'INT',
                'constraint' => 6,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
    }
}
