<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedMasterKategori extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        $kategoriData = [
            ['kode' => 'ADP',    'nama' => 'Aset Dalam Penyelesaian',     'golongan' => 'ADP'],
            ['kode' => 'BBI_A',  'nama' => 'Peralatan Elektronik & IT',   'golongan' => 'BBI'],
            ['kode' => 'BBI_B',  'nama' => 'Software & Lisensi',          'golongan' => 'BBI'],
            ['kode' => 'BBII_A', 'nama' => 'Kendaraan',                   'golongan' => 'BBII'],
            ['kode' => 'BBII_B', 'nama' => 'Peralatan & Mesin',           'golongan' => 'BBII'],
            ['kode' => 'BBII_C', 'nama' => 'Peralatan Laboratorium',      'golongan' => 'BBII'],
            ['kode' => 'BBII_D', 'nama' => 'Furniture & Inventaris',      'golongan' => 'BBII'],
            ['kode' => 'BBII_E', 'nama' => 'Buku & Koleksi Perpustakaan', 'golongan' => 'BBII'],
            ['kode' => 'BP_A',   'nama' => 'Tanah',                       'golongan' => 'BP'],
            ['kode' => 'BP_B',   'nama' => 'Bangunan & Gedung',           'golongan' => 'BP'],
            ['kode' => 'BTP_A',  'nama' => 'Infrastruktur & Utilitas',    'golongan' => 'BTP'],
        ];

        foreach ($kategoriData as $k) {
            $exists = $this->db->table('aset_kategori')
                ->groupStart()
                    ->where('kode_kategori', $k['kode'])
                    ->orWhere('nama_kategori', $k['nama'])
                ->groupEnd()
                ->countAllResults();

            if ($exists > 0) {
                continue;
            }

            $golongan = $this->db->table('aset_golongan')
                ->select('gl_id')
                ->where('kode_golongan', $k['golongan'])
                ->where('is_active', 1)
                ->get()
                ->getRow();

            if (!$golongan) {
                continue;
            }

            $this->db->table('aset_kategori')->insert([
                'kode_kategori' => $k['kode'],
                'nama_kategori' => $k['nama'],
                'gl_id'         => $golongan->gl_id,
                'keterangan'    => null,
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }

    public function down()
    {
        $kode = ['ADP', 'BBI_A', 'BBI_B', 'BBII_A', 'BBII_B', 'BBII_C', 'BBII_D', 'BBII_E', 'BP_A', 'BP_B', 'BTP_A'];
        $this->db->table('aset_kategori')->whereIn('kode_kategori', $kode)->delete();
    }
}
