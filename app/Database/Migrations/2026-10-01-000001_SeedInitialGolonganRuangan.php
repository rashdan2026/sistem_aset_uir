<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedInitialGolonganRuangan extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        $golonganRuangan = [
            ['nama_golongan' => 'Ruang Kelas', 'kelompok' => 'ruangan', 'keterangan' => 'Ruang untuk kegiatan perkuliahan', 'is_active' => 1],
            ['nama_golongan' => 'Ruang Kantor', 'kelompok' => 'ruangan', 'keterangan' => 'Ruang kerja administrasi/dosen', 'is_active' => 1],
            ['nama_golongan' => 'Ruang Kerja/Lab', 'kelompok' => 'ruangan', 'keterangan' => 'Ruang laboratorium dan praktikum', 'is_active' => 1],
        ];

        foreach ($golonganRuangan as $g) {
            $exists = $this->db->table('aset_golongan')
                ->where('nama_golongan', $g['nama_golongan'])
                ->countAllResults();
            if ($exists == 0) {
                $g['created_at'] = $now;
                $g['updated_at'] = $now;
                $this->db->table('aset_golongan')->insert($g);

                $inserted = $this->db->table('aset_golongan')
                    ->select('gl_id')
                    ->where('nama_golongan', $g['nama_golongan'])
                    ->orderBy('gl_id', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRow();

                if ($inserted) {
                    $kode = 'GL-' . str_pad((int)$inserted->gl_id, 2, '0', STR_PAD_LEFT);
                    $this->db->table('aset_golongan')
                        ->where('gl_id', $inserted->gl_id)
                        ->update(['kode_golongan' => $kode]);
                }
            }
        }
    }

    public function down()
    {
        $namaGolongan = ['Ruang Kelas', 'Ruang Kantor', 'Ruang Kerja/Lab'];
        foreach ($namaGolongan as $nama) {
            $this->db->table('aset_golongan')->where('nama_golongan', $nama)->delete();
        }
    }
}