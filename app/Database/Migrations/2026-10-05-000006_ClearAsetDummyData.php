<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ClearAsetDummyData extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $db->query('SET FOREIGN_KEY_CHECKS = 0');

        $tablesToTruncate = [
            'aset_all_uir',
            'aset_sub_kategori',
            'aset_kategori',
            'aset_ruangan',
            'aset_lantai',
            'aset_gedung',
            'aset_sub_units',
            'aset_type',
            'aset_merk',
            'aset_sumber_dana',
            'aset_kondisi_barang',
            'aset_golongan',
        ];

        foreach ($tablesToTruncate as $table) {
            $db->query("TRUNCATE TABLE `{$table}`");
        }

        $db->query('SET FOREIGN_KEY_CHECKS = 1');

        $now = date('Y-m-d H:i:s');

        $golonganInduk = [
            ['kode' => 'BBI',   'nama' => 'Bukan Bangunan - I',          'kelompok' => 'non_bangunan'],
            ['kode' => 'BBII',  'nama' => 'Bukan Bangunan - II',         'kelompok' => 'non_bangunan'],
            ['kode' => 'BBIII', 'nama' => 'Bukan Bangunan - III',        'kelompok' => 'non_bangunan'],
            ['kode' => 'BBIV',  'nama' => 'Bukan Bangunan - IV',         'kelompok' => 'non_bangunan'],
            ['kode' => 'BP',    'nama' => 'Bangunan - Permanen',         'kelompok' => 'bangunan'],
            ['kode' => 'BTP',   'nama' => 'Bangunan - Tidak Permanen',   'kelompok' => 'bangunan'],
            ['kode' => 'ADP',   'nama' => 'Aset Dalam Penyelesaian',     'kelompok' => 'lainnya'],
        ];

        foreach ($golonganInduk as $g) {
            $db->table('aset_golongan')->insert([
                'kode_golongan' => $g['kode'],
                'nama_golongan' => $g['nama'],
                'kelompok'      => $g['kelompok'],
                'keterangan'    => 'Golongan Induk',
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }

        $kondisiBarang = [
            ['kode' => 'KB-01', 'nama' => 'Baik',                    'level_kondisi' => 1, 'warna' => '#28a745'],
            ['kode' => 'KB-02', 'nama' => 'Rusak Ringan',            'level_kondisi' => 2, 'warna' => '#ffc107'],
            ['kode' => 'KB-03', 'nama' => 'Rusak Berat',             'level_kondisi' => 3, 'warna' => '#fd7e14'],
            ['kode' => 'KB-04', 'nama' => 'Tidak Layak Pakai',       'level_kondisi' => 4, 'warna' => '#dc3545'],
        ];

        foreach ($kondisiBarang as $kb) {
            $db->table('aset_kondisi_barang')->insert([
                'kode_kondisi'         => $kb['kode'],
                'nama_kondisi'         => $kb['nama'],
                'level_kondisi'        => $kb['level_kondisi'],
                'is_available_for_use' => 1,
                'warna'                => $kb['warna'],
                'is_active'            => 1,
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);
        }

        $sumberDana = [
            ['kode' => 'SD-01', 'nama' => 'APBN'],
            ['kode' => 'SD-02', 'nama' => 'APBD'],
            ['kode' => 'SD-03', 'nama' => 'Hibah'],
            ['kode' => 'SD-04', 'nama' => 'Swadaya'],
        ];

        foreach ($sumberDana as $sd) {
            $db->table('aset_sumber_dana')->insert([
                'kode_sumber_dana' => $sd['kode'],
                'nama_sumber_dana' => $sd['nama'],
                'is_active'        => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        }
    }

    public function down()
    {
        // No down migration - data was backed up to backup tables
    }
}
