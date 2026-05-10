<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVwRuanganLengkapView extends Migration
{
    public function up()
    {
        $db = db_connect();
        
        // Drop view if exists
        $db->query('DROP VIEW IF EXISTS vw_ruangan_lengkap');
        
        // Create view
        $sql = "CREATE VIEW vw_ruangan_lengkap AS
            SELECT
                r.rg_id AS ruangan_id,
                r.kode_ruangan,
                r.nama_ruangan,
                r.jenis_ruangan,
                r.kapasitas,
                r.luas_m2,
                r.keterangan,
                r.is_active,
                r.penanggung_jawab_id_kpe,
                l.lt_id AS lantai_id,
                l.nama_lantai,
                l.nomor_lantai,
                g.gd_id AS gedung_id,
                g.nama_gedung,
                g.kode_gedung,
                g.unit_kerja_id AS unit_pemilik_gedung_id,
                su.su_id AS sub_unit_id,
                su.nama_sub_unit,
                uk.nama_unit AS unit_nama,
                k.nama_gelar AS pj_nama,
                k.npk AS pj_npk,
                k.no_hp1 AS pj_hp
            FROM aset_ruangan r
            JOIN aset_lantai l ON l.lt_id = r.lantai_id
            JOIN aset_gedung g ON g.gd_id = l.gedung_id
            JOIN aset_sub_units su ON su.su_id = r.sub_unit_id
            LEFT JOIN tbl_unit_kerja uk ON uk.id_unit_kerja = g.unit_kerja_id
            LEFT JOIN ylpi_karyawan k ON k.id_kpe = r.penanggung_jawab_id_kpe
            WHERE r.deleted_at IS NULL";
        
        $db->query($sql);
    }

    public function down()
    {
        $db = db_connect();
        $db->query('DROP VIEW IF EXISTS vw_ruangan_lengkap');
    }
}