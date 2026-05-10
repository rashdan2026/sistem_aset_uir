<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVwUnitKerjaAllowedView extends Migration
{
    public function up()
    {
        $db = db_connect();
        $db->query("
            CREATE OR REPLACE VIEW vw_unit_kerja_allowed AS
            SELECT
                u.id_unit_kerja,
                u.nama_unit,
                u.flag_aktif
            FROM tbl_unit_kerja u
            INNER JOIN setting_unit_kerja_allowed s
                ON s.id_unit_kerja = u.id_unit_kerja
            WHERE s.is_active = 1
              AND u.flag_aktif = 1
        ");
    }

    public function down()
    {
        $db = db_connect();
        $db->query("DROP VIEW IF EXISTS vw_unit_kerja_allowed");
    }
}
