<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedMasterSubKategori extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        $subKategoriData = [
            'BBII_A' => [ // Kendaraan
                ['nama' => 'Mobil Operasional Pimpinan',   'merk' => 1, 'type' => 1, 'ruangan' => 0],
                ['nama' => 'Mobil Operasional Unit',        'merk' => 1, 'type' => 1, 'ruangan' => 0],
                ['nama' => 'Bus Kampus',                    'merk' => 1, 'type' => 1, 'ruangan' => 0],
                ['nama' => 'Sepeda Motor Dinas',           'merk' => 1, 'type' => 1, 'ruangan' => 0],
                ['nama' => 'Ambulans',                     'merk' => 1, 'type' => 1, 'ruangan' => 0],
                ['nama' => 'Kendaraan Lapangan',           'merk' => 1, 'type' => 1, 'ruangan' => 0],
            ],
            'BBII_B' => [ // Peralatan & Mesin
                ['nama' => 'Mesin Genset',                  'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Mesin Produksi (Workshop)',    'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Mesin Pendingin Industri',     'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Mesin Pompa Air',              'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Mesin Fotokopi Besar',         'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Lift / Eskalator',            'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Mesin Laboratorium Besar',     'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Peralatan Teknik Berat',      'merk' => 1, 'type' => 1, 'ruangan' => 1],
            ],
            'BBI_A' => [ // Peralatan Elektronik & IT
                ['nama' => 'Komputer Desktop',             'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Laptop',                      'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Server',                      'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Printer',                     'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Scanner',                     'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Proyektor',                   'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Smart TV / Display',          'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'CCTV',                        'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Access Point',                'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Switch / Router',            'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'UPS',                         'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Perangkat Audio',             'merk' => 1, 'type' => 1, 'ruangan' => 1],
            ],
            'BBII_C' => [ // Peralatan Laboratorium
                ['nama' => 'Alat Lab Kimia',              'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Alat Lab Biologi',            'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Alat Lab Fisika',             'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Alat Lab Teknik Mesin',       'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Alat Lab Elektro',            'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Alat Lab Komputer',           'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Alat Lab Kesehatan',          'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Mikroskop',                   'merk' => 1, 'type' => 1, 'ruangan' => 1],
                ['nama' => 'Timbangan Digital Presisi',   'merk' => 1, 'type' => 1, 'ruangan' => 1],
            ],
            'BBII_D' => [ // Furniture & Inventaris
                ['nama' => 'Meja Kantor',                 'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Kursi Kantor',                'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Kursi Kuliah',                'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Lemari Arsip',                'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Rak Buku',                   'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Meja Rapat',                 'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Papan Tulis',                'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Partisi Ruangan',             'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Mimbar',                     'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Brankas',                    'merk' => 1, 'type' => 0, 'ruangan' => 1],
            ],
            'BBII_E' => [ // Buku & Koleksi Perpustakaan
                ['nama' => 'Buku Teks',                  'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Buku Referensi',              'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Buku Digital (E-Book)',      'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Jurnal Berlangganan',         'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Koleksi Skripsi/Tesis',      'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Koleksi Multimedia',         'merk' => 0, 'type' => 0, 'ruangan' => 1],
            ],
            'BTP_A' => [ // Infrastruktur & Utilitas
                ['nama' => 'Instalasi Listrik',           'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Instalasi Air',               'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Instalasi Internet',          'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Instalasi AC Central',       'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Drainase',                    'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Pagar & Gerbang',             'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Jalan Internal Kampus',       'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Sistem Fire Alarm',           'merk' => 1, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Hydrant',                    'merk' => 0, 'type' => 0, 'ruangan' => 1],
            ],
            'BBI_B' => [ // Software & Lisensi
                ['nama' => 'Sistem Informasi Akademik',   'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Sistem Keuangan',             'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Sistem Aset',                'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Software Statistik',         'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Software Desain',            'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Lisensi Microsoft',          'merk' => 1, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Lisensi Antivirus',          'merk' => 1, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'ERP System',                 'merk' => 0, 'type' => 0, 'ruangan' => 1],
            ],
            'ADP' => [ // Aset Dalam Penyelesaian
                ['nama' => 'Gedung Dalam Pembangunan',    'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Renovasi Besar',              'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Instalasi Dalam Proses',     'merk' => 0, 'type' => 0, 'ruangan' => 1],
                ['nama' => 'Proyek Infrastruktur',       'merk' => 0, 'type' => 0, 'ruangan' => 1],
            ],
        ];

        foreach ($subKategoriData as $kategoriKode => $items) {
            $kategori = $db->table('aset_kategori')
                ->select('kt_id')
                ->where('kode_kategori', $kategoriKode)
                ->where('is_active', 1)
                ->get()
                ->getRowArray();

            if (!$kategori) {
                continue;
            }

            $ktId = $kategori['kt_id'];

            foreach ($items as $item) {
                $exists = $db->table('aset_sub_kategori')
                    ->where('kategori_id', $ktId)
                    ->where('nama_sub_kategori', $item['nama'])
                    ->countAllResults();

                if ($exists > 0) {
                    continue;
                }

                $db->table('aset_sub_kategori')->insert([
                    'kategori_id'         => $ktId,
                    'nama_sub_kategori'   => $item['nama'],
                    'wajib_merk'          => $item['merk'],
                    'wajib_type'          => $item['type'],
                    'wajib_ruangan'       => $item['ruangan'],
                    'is_active'           => 1,
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ]);

                $skId = $db->insertID();
                $kodeSubKategori = 'SK-' . str_pad($ktId, 2, '0', STR_PAD_LEFT) . '-' . str_pad($skId, 4, '0', STR_PAD_LEFT);
                $db->table('aset_sub_kategori')
                    ->where('sk_id', $skId)
                    ->update(['kode_sub_kategori' => $kodeSubKategori]);
            }
        }
    }

    public function down()
    {
        $names = [
            'Mobil Operasional Pimpinan', 'Mobil Operasional Unit', 'Bus Kampus',
            'Sepeda Motor Dinas', 'Ambulans', 'Kendaraan Lapangan',
            'Mesin Genset', 'Mesin Produksi (Workshop)', 'Mesin Pendingin Industri',
            'Mesin Pompa Air', 'Mesin Fotokopi Besar', 'Lift / Eskalator',
            'Mesin Laboratorium Besar', 'Peralatan Teknik Berat',
            'Komputer Desktop', 'Laptop', 'Server', 'Printer', 'Scanner',
            'Proyektor', 'Smart TV / Display', 'CCTV', 'Access Point',
            'Switch / Router', 'UPS', 'Perangkat Audio',
            'Alat Lab Kimia', 'Alat Lab Biologi', 'Alat Lab Fisika',
            'Alat Lab Teknik Mesin', 'Alat Lab Elektro', 'Alat Lab Komputer',
            'Alat Lab Kesehatan', 'Mikroskop', 'Timbangan Digital Presisi',
            'Meja Kantor', 'Kursi Kantor', 'Kursi Kuliah', 'Lemari Arsip',
            'Rak Buku', 'Meja Rapat', 'Papan Tulis', 'Partisi Ruangan',
            'Mimbar', 'Brankas',
            'Buku Teks', 'Buku Referensi', 'Buku Digital (E-Book)',
            'Jurnal Berlangganan', 'Koleksi Skripsi/Tesis', 'Koleksi Multimedia',
            'Instalasi Listrik', 'Instalasi Air', 'Instalasi Internet',
            'Instalasi AC Central', 'Drainase', 'Pagar & Gerbang',
            'Jalan Internal Kampus', 'Sistem Fire Alarm', 'Hydrant',
            'Sistem Informasi Akademik', 'Sistem Keuangan', 'Sistem Aset',
            'Software Statistik', 'Software Desain', 'Lisensi Microsoft',
            'Lisensi Antivirus', 'ERP System',
            'Gedung Dalam Pembangunan', 'Renovasi Besar',
            'Instalasi Dalam Proses', 'Proyek Infrastruktur',
        ];
        $this->db->table('aset_sub_kategori')->whereIn('nama_sub_kategori', $names)->delete();
    }
}
