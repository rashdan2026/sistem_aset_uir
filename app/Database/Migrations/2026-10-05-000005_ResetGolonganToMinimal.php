<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ResetGolonganToMinimal extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        $childKode = ['BBI_A', 'BBI_B', 'BBII_A', 'BBII_B', 'BBII_C', 'BBII_D', 'BBII_E', 'BP_A', 'BP_B', 'BTP_A'];
        foreach ($childKode as $kode) {
            $this->db->table('aset_golongan')
                ->where('kode_golongan', $kode)
                ->update(['is_active' => 0]);
        }

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
            $exists = $this->db->table('aset_golongan')
                ->where('kode_golongan', $g['kode'])
                ->countAllResults();

            if ($exists > 0) {
                $this->db->table('aset_golongan')
                    ->where('kode_golongan', $g['kode'])
                    ->update([
                        'nama_golongan' => $g['nama'],
                        'kelompok' => $g['kelompok'],
                        'is_active' => 1,
                        'updated_at' => $now,
                    ]);
            } else {
                $this->db->table('aset_golongan')->insert([
                    'kode_golongan' => $g['kode'],
                    'nama_golongan' => $g['nama'],
                    'kelompok' => $g['kelompok'],
                    'keterangan' => 'Golongan Induk',
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $oldGolongan = [
            'Harta Tetap / Aset Tetap', 'Bangunan', 'Kendaraan', 'Peralatan Elektronik',
            'Furniture', 'Inventaris', 'Perangkat Lunak', 'Tanah', 'Ruang Kelas',
            'Ruang Kantor', 'Ruang Kerja/Lab', 'Bukan Bangunan-I-B',
            'GL-01', 'GL-02', 'GL-03', 'GL-04', 'GL-05', 'GL-06', 'GL-07', 'GL-08',
            'GL-11', 'GL-12', 'GL-13', 'GL-14',
        ];
        $this->db->table('aset_golongan')
            ->whereIn('nama_golongan', $oldGolongan)
            ->update(['is_active' => 0]);

        $this->db->table('aset_golongan')
            ->whereNotIn('kode_golongan', ['BBI', 'BBII', 'BBIII', 'BBIV', 'BP', 'BTP', 'ADP'])
            ->where('is_active', 1)
            ->update(['is_active' => 0]);
    }

    public function down()
    {
        $golonganInduk = [
            ['kode' => 'BBI',   'nama' => 'Bukan Bangunan - I',          'kelompok' => 'non_bangunan'],
            ['kode' => 'BBII',  'nama' => 'Bukan Bangunan - II',         'kelompok' => 'non_bangunan'],
            ['kode' => 'BBIII', 'nama' => 'Bukan Bangunan - III',        'kelompok' => 'non_bangunan'],
            ['kode' => 'BBIV',  'nama' => 'Bukan Bangunan - IV',         'kelompok' => 'non_bangunan'],
            ['kode' => 'BP',    'nama' => 'Bangunan & Prasarana',        'kelompok' => 'bangunan'],
            ['kode' => 'BTP',   'nama' => 'Bangunan Tanah & Prasarana',  'kelompok' => 'bangunan'],
            ['kode' => 'ADP',   'nama' => 'Aset Lainnya / Diverses',     'kelompok' => 'lainnya'],
        ];
        foreach ($golonganInduk as $g) {
            $this->db->table('aset_golongan')
                ->where('kode_golongan', $g['kode'])
                ->update([
                    'nama_golongan' => $g['nama'],
                    'kelompok' => $g['kelompok'],
                ]);
        }
    }
}
