<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedMasterMerk extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $merkData = [
            'Costum',
            'Acer',
            'Samsung',
            'LG',
            'HP',
            'APPLE',
            'XIAOMI',
        ];

        foreach ($merkData as $nama) {
            $exists = $db->table('aset_merk')
                ->where('nama_merk', $nama)
                ->where('is_active', 1)
                ->countAllResults();

            if ($exists > 0) {
                continue;
            }

            $db->table('aset_merk')->insert([
                'nama_merk'   => $nama,
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            $mrId = $db->insertID();
            $kodeMerk = 'MR-' . str_pad($mrId, 3, '0', STR_PAD_LEFT);
            $db->table('aset_merk')
                ->where('mr_id', $mrId)
                ->update(['kode_merk' => $kodeMerk]);
        }
    }

    public function down()
    {
        $this->db->table('aset_merk')
            ->whereIn('nama_merk', ['Costum', 'Acer', 'Samsung', 'LG', 'HP', 'APPLE', 'XIAOMI'])
            ->delete();
    }
}
