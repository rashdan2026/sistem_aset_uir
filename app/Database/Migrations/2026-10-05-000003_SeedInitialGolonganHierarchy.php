<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedInitialGolonganHierarchy extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        $golonganInduk = [
            ['kode' => 'BBI', 'nama' => 'Bukan Bangunan - I', 'kelompok' => 'non_bangunan'],
            ['kode' => 'BBII', 'nama' => 'Bukan Bangunan - II', 'kelompok' => 'non_bangunan'],
            ['kode' => 'BBIII', 'nama' => 'Bukan Bangunan - III', 'kelompok' => 'non_bangunan'],
            ['kode' => 'BBIV', 'nama' => 'Bukan Bangunan - IV', 'kelompok' => 'non_bangunan'],
            ['kode' => 'BP', 'nama' => 'Bangunan & Prasarana', 'kelompok' => 'bangunan'],
            ['kode' => 'BTP', 'nama' => 'Bangunan Tanah & Prasarana', 'kelompok' => 'bangunan'],
            ['kode' => 'ADP', 'nama' => 'Aset Lainnya / Diverses', 'kelompok' => 'lainnya'],
        ];

        $parentIds = [];
        foreach ($golonganInduk as $g) {
            $exists = $this->db->table('aset_golongan')
                ->where('kode_golongan', $g['kode'])
                ->countAllResults();

            if ($exists == 0) {
                $this->db->table('aset_golongan')->insert([
                    'kode_golongan' => $g['kode'],
                    'nama_golongan' => $g['nama'],
                    'kelompok' => $g['kelompok'],
                    'parent_id' => null,
                    'level' => 1,
                    'keterangan' => 'Golongan Induk',
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);

                $inserted = $this->db->table('aset_golongan')
                    ->select('gl_id')
                    ->where('kode_golongan', $g['kode'])
                    ->get()
                    ->getRow();

                if ($inserted) {
                    $parentIds[$g['kode']] = $inserted->gl_id;
                }
            } else {
                $inserted = $this->db->table('aset_golongan')
                    ->select('gl_id')
                    ->where('kode_golongan', $g['kode'])
                    ->get()
                    ->getRow();
                if ($inserted) {
                    $parentIds[$g['kode']] = $inserted->gl_id;
                }
            }
        }

        $golonganAnak = [
            ['kode' => 'BBI_A', 'nama' => 'Peralatan Elektronik & IT', 'parent' => 'BBI', 'kelompok' => 'non_bangunan'],
            ['kode' => 'BBI_B', 'nama' => 'Software & Lisensi', 'parent' => 'BBI', 'kelompok' => 'non_bangunan'],
            ['kode' => 'BBII_A', 'nama' => 'Kendaraan', 'parent' => 'BBII', 'kelompok' => 'non_bangunan'],
            ['kode' => 'BBII_B', 'nama' => 'Peralatan & Mesin', 'parent' => 'BBII', 'kelompok' => 'non_bangunan'],
            ['kode' => 'BBII_C', 'nama' => 'Peralatan Laboratorium', 'parent' => 'BBII', 'kelompok' => 'non_bangunan'],
            ['kode' => 'BBII_D', 'nama' => 'Furniture & Inventaris', 'parent' => 'BBII', 'kelompok' => 'non_bangunan'],
            ['kode' => 'BBII_E', 'nama' => 'Buku & Koleksi Perpustakaan', 'parent' => 'BBII', 'kelompok' => 'non_bangunan'],
            ['kode' => 'BP_A', 'nama' => 'Tanah', 'parent' => 'BP', 'kelompok' => 'bangunan'],
            ['kode' => 'BP_B', 'nama' => 'Bangunan & Gedung', 'parent' => 'BP', 'kelompok' => 'bangunan'],
            ['kode' => 'BTP_A', 'nama' => 'Infrastruktur & Utilitas', 'parent' => 'BTP', 'kelompok' => 'bangunan'],
        ];

        foreach ($golonganAnak as $g) {
            if (!isset($parentIds[$g['parent']])) {
                continue;
            }

            $exists = $this->db->table('aset_golongan')
                ->where('kode_golongan', $g['kode'])
                ->countAllResults();

            if ($exists == 0) {
                $parent = $this->db->table('aset_golongan')
                    ->select('level')
                    ->where('gl_id', $parentIds[$g['parent']])
                    ->get()
                    ->getRow();

                $level = $parent ? ($parent->level + 1) : 2;

                $this->db->table('aset_golongan')->insert([
                    'kode_golongan' => $g['kode'],
                    'nama_golongan' => $g['nama'],
                    'kelompok' => $g['kelompok'],
                    'parent_id' => $parentIds[$g['parent']],
                    'level' => $level,
                    'keterangan' => 'Golongan turunan dari ' . $g['parent'],
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }
    }

    public function down()
    {
        $kodeGolongan = ['BBI', 'BBII', 'BBIII', 'BBIV', 'BP', 'BTP', 'ADP', 'BBI_A', 'BBI_B', 'BBII_A', 'BBII_B', 'BBII_C', 'BBII_D', 'BBII_E', 'BP_A', 'BP_B', 'BTP_A'];
        foreach ($kodeGolongan as $kode) {
            $this->db->table('aset_golongan')->where('kode_golongan', $kode)->delete();
        }
    }
}