<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVwPenanggungJawabView extends Migration
{
    public function up()
    {
        $db = db_connect();
        
        // Drop view if exists
        $db->query('DROP VIEW IF EXISTS vw_penanggung_jawab');
        
        // Create view
        $sql = "CREATE VIEW vw_penanggung_jawab AS
            SELECT
                k.id_kpe,
                k.npk,
                k.nama_gelar,
                k.jenkel,
                k.kategori,
                k.no_hp1,
                k.no_wa,
                k.email,
                k.unit_kerja AS unit_kerja_id,
                u.nama_unit,
                1 AS is_active
            FROM ylpi_karyawan k
            LEFT JOIN tbl_unit_kerja u
                ON u.id_unit_kerja = k.unit_kerja
                AND u.flag_aktif = 1
            WHERE k.flag_karyawan = '1'";
        
        $db->query($sql);
    }

    public function down()
    {
        $db = db_connect();
        $db->query('DROP VIEW IF EXISTS vw_penanggung_jawab');
    }
}