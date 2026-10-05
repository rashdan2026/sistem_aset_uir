<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWarnaToAsetKondisiBarangTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('aset_kondisi_barang', [
            'warna' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'null'       => true,
                'after'      => 'is_available_for_use',
                'comment'    => 'Hex color #RRGGBB untuk badge di listing',
            ],
        ]);

        $defaults = [
            'Baik'              => '#198754',
            'Rusak Ringan'      => '#FD7E14',
            'Rusak Berat'       => '#DC3545',
            'Tidak Ditemukan'   => '#6C757D',
            'Dalam Perbaikan'   => '#0D6EFD',
            'Nonaktif/Dihapus'  => '#212529',
        ];
        foreach ($defaults as $nama => $warna) {
            $this->db->table('aset_kondisi_barang')
                ->where('nama_kondisi', $nama)
                ->where('warna IS NULL', null, false)
                ->update(['warna' => $warna]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('aset_kondisi_barang', 'warna');
    }
}