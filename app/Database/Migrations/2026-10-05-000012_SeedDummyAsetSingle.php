<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedDummyAsetSingle extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        // ============================================
        // 1. SEED SUB_UNITS (per unit_kerja)
        // ============================================
        $subUnitSeed = [
            ['unit' => 1,  'kode' => 'SU-FH-01',  'nama' => 'Fakultas Hukum - Tata Usaha'],
            ['unit' => 3,  'kode' => 'SU-FT-01',  'nama' => 'Fakultas Teknik - Lab Informatika'],
            ['unit' => 3,  'kode' => 'SU-FT-02',  'nama' => 'Fakultas Teknik - Bengkel'],
            ['unit' => 5,  'kode' => 'SU-FEB-01', 'nama' => 'Fakultas Ekonomi - Ruang Dekan'],
            ['unit' => 6,  'kode' => 'SU-FKIP-01','nama' => 'FKIP - Lab Bahasa'],
            ['unit' => 10, 'kode' => 'SU-PAS-01', 'nama' => 'Pascasarjana - Sekretariat'],
            ['unit' => 9,  'kode' => 'SU-FIKOM-01','nama' => 'Fakultas Ilmu Komunikasi - Studio'],
        ];

        $subUnitIds = [];
        foreach ($subUnitSeed as $s) {
            $exists = $db->table('aset_sub_units')->where('kode_sub_unit', $s['kode'])->countAllResults();
            if ($exists > 0) {
                $row = $db->table('aset_sub_units')->select('su_id')->where('kode_sub_unit', $s['kode'])->get()->getRow();
                $subUnitIds[$s['kode']] = $row->su_id;
                continue;
            }
            $db->table('aset_sub_units')->insert([
                'kode_sub_unit' => $s['kode'],
                'nama_sub_unit' => $s['nama'],
                'unit_kerja_id' => $s['unit'],
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            $subUnitIds[$s['kode']] = $db->insertID();
        }

        // ============================================
        // 2. SEED TYPE (per merk)
        // ============================================
        $typeSeed = [
            ['merk' => 'Acer',    'kode' => 'TY-002-001', 'nama' => 'Aspire 5 Slim'],
            ['merk' => 'Acer',    'kode' => 'TY-002-002', 'nama' => 'Predator Helios 300'],
            ['merk' => 'Acer',    'kode' => 'TY-002-003', 'nama' => 'Veriton M Desktop'],
            ['merk' => 'Samsung', 'kode' => 'TY-003-001', 'nama' => 'Galaxy Book 4 Pro'],
            ['merk' => 'Samsung', 'kode' => 'TY-003-002', 'nama' => 'UA75CU7000 75" Smart TV'],
            ['merk' => 'LG',      'kode' => 'TY-004-001', 'nama' => 'Gram 16Z90R'],
            ['merk' => 'HP',      'kode' => 'TY-005-001', 'nama' => 'Pavilion 14'],
            ['merk' => 'HP',      'kode' => 'TY-005-002', 'nama' => 'LaserJet Pro M404'],
            ['merk' => 'HP',      'kode' => 'TY-005-003', 'nama' => 'EliteDesk 800 G9'],
            ['merk' => 'APPLE',   'kode' => 'TY-006-001', 'nama' => 'MacBook Air M2 13"'],
            ['merk' => 'APPLE',   'kode' => 'TY-006-002', 'nama' => 'iMac 24" M3'],
            ['merk' => 'XIAOMI',  'kode' => 'TY-007-001', 'nama' => 'RedmiBook 15 Pro'],
            ['merk' => 'XIAOMI',  'kode' => 'TY-007-002', 'nama' => 'Mi 4K Laser Projector'],
            ['merk' => 'Costum',  'kode' => 'TY-001-001', 'nama' => 'Standar Custom Build'],
        ];

        $typeIds = [];
        foreach ($typeSeed as $t) {
            $exists = $db->table('aset_type')->where('kode_type', $t['kode'])->countAllResults();
            if ($exists > 0) {
                $row = $db->table('aset_type')->select('ty_id')->where('kode_type', $t['kode'])->get()->getRow();
                $typeIds[$t['kode']] = $row->ty_id;
                continue;
            }
            $merk = $db->table('aset_merk')->select('mr_id')->where('nama_merk', $t['merk'])->get()->getRow();
            if (!$merk) continue;
            $db->table('aset_type')->insert([
                'kode_type'  => $t['kode'],
                'nama_type'  => $t['nama'],
                'merk_id'    => $merk->mr_id,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $typeIds[$t['kode']] = $db->insertID();
        }

        // ============================================
        // 3. SEED GEDUNG / LANTAI / RUANGAN
        // ============================================
        $gedungSeed = [
            ['kode' => 'GD-01', 'nama' => 'Gedung Rektorat Lt.1'],
            ['kode' => 'GD-02', 'nama' => 'Gedung Fakultas Teknik Lt.2'],
            ['kode' => 'GD-03', 'nama' => 'Gedung Fakultas Hukum'],
        ];
        $gedungIds = [];
        foreach ($gedungSeed as $g) {
            $exists = $db->table('aset_gedung')->where('kode_gedung', $g['kode'])->countAllResults();
            if ($exists > 0) {
                $row = $db->table('aset_gedung')->select('gd_id')->where('kode_gedung', $g['kode'])->get()->getRow();
                $gedungIds[$g['kode']] = $row->gd_id;
                continue;
            }
            $db->table('aset_gedung')->insert([
                'kode_gedung' => $g['kode'],
                'nama_gedung' => $g['nama'],
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $gedungIds[$g['kode']] = $db->insertID();
        }

        $lantaiSeed = [
            ['gedung' => 'GD-01', 'nama' => 'Lantai 1'],
            ['gedung' => 'GD-01', 'nama' => 'Lantai 2'],
            ['gedung' => 'GD-02', 'nama' => 'Lantai 1'],
            ['gedung' => 'GD-02', 'nama' => 'Lantai 2'],
            ['gedung' => 'GD-03', 'nama' => 'Lantai 1'],
        ];
        $lantaiIds = [];
        foreach ($lantaiSeed as $l) {
            $gdId = $gedungIds[$l['gedung']];
            // key matches ruanganSeed 'lt' values
            $key = $l['gedung'] . '-' . $l['nama'];
            $exists = $db->table('aset_lantai')->where('nama_lantai', $l['nama'])->where('gedung_id', $gdId)->countAllResults();
            if ($exists > 0) {
                $row = $db->table('aset_lantai')->select('lt_id')->where('nama_lantai', $l['nama'])->where('gedung_id', $gdId)->get()->getRow();
                $lantaiIds[$key] = $row->lt_id;
                continue;
            }
            $db->table('aset_lantai')->insert([
                'kode_lantai' => $key,
                'nama_lantai' => $l['nama'],
                'gedung_id'   => $gdId,
                'is_active'   => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $lantaiIds[$key] = $db->insertID();
        }

        $ruanganSeed = [
            // GD-01 Lt 1 - Fakultas Hukum Tata Usaha
            ['sub' => 'SU-FH-01',   'lt' => 'GD-01-Lantai 1', 'kode' => 'RG-FH-101', 'nama' => 'Ruang Tata Usaha'],
            ['sub' => 'SU-FH-01',   'lt' => 'GD-01-Lantai 1', 'kode' => 'RG-FH-102', 'nama' => 'Ruang Dekan'],
            ['sub' => 'SU-FH-01',   'lt' => 'GD-01-Lantai 2', 'kode' => 'RG-FH-201', 'nama' => 'Ruang Dosen'],

            // GD-01 Lt 2 - Pascasarjana
            ['sub' => 'SU-PAS-01',  'lt' => 'GD-01-Lantai 2', 'kode' => 'RG-PAS-201', 'nama' => 'Sekretariat Pascasarjana'],
            ['sub' => 'SU-PAS-01',  'lt' => 'GD-01-Lantai 2', 'kode' => 'RG-PAS-202', 'nama' => 'Ruang Sidang'],

            // GD-02 Lt 1 - Fakultas Teknik Lab & Bengkel
            ['sub' => 'SU-FT-01',   'lt' => 'GD-02-Lantai 1', 'kode' => 'RG-FT-101', 'nama' => 'Lab Informatika 1'],
            ['sub' => 'SU-FT-01',   'lt' => 'GD-02-Lantai 1', 'kode' => 'RG-FT-102', 'nama' => 'Lab Informatika 2'],
            ['sub' => 'SU-FT-02',   'lt' => 'GD-02-Lantai 1', 'kode' => 'RG-FT-103', 'nama' => 'Bengkel'],

            // GD-02 Lt 2 - Fakultas Ekonomi
            ['sub' => 'SU-FEB-01',  'lt' => 'GD-02-Lantai 2', 'kode' => 'RG-FEB-201', 'nama' => 'Ruang Dekan Ekonomi'],
            ['sub' => 'SU-FEB-01',  'lt' => 'GD-02-Lantai 2', 'kode' => 'RG-FEB-202', 'nama' => 'Ruang Administrasi'],

            // GD-03 Lt 1 - FKIP & FIKOM
            ['sub' => 'SU-FKIP-01', 'lt' => 'GD-03-Lantai 1', 'kode' => 'RG-FKIP-101', 'nama' => 'Lab Bahasa'],
            ['sub' => 'SU-FIKOM-01','lt' => 'GD-03-Lantai 1', 'kode' => 'RG-FIKOM-101', 'nama' => 'Studio Multimedia'],
        ];

        $ruanganIds = [];
        foreach ($ruanganSeed as $r) {
            $exists = $db->table('aset_ruangan')->where('kode_ruangan', $r['kode'])->countAllResults();
            if ($exists > 0) {
                $row = $db->table('aset_ruangan')->select('rg_id')->where('kode_ruangan', $r['kode'])->get()->getRow();
                $ruanganIds[$r['kode']] = $row->rg_id;
                continue;
            }
            $suId = $subUnitIds[$r['sub']];
            $ltId = $lantaiIds[$r['lt']];
            $db->table('aset_ruangan')->insert([
                'kode_ruangan' => $r['kode'],
                'nama_ruangan' => $r['nama'],
                'lantai_id'    => $ltId,
                'sub_unit_id'  => $suId,
                'is_active'    => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
            $ruanganIds[$r['kode']] = $db->insertID();
        }

        // ============================================
        // 4. SEED 30 ASET SINGLE (varied)
        // ============================================
        // Resolve helper function (code_helper) isn't loaded in migration
        // Format: all_NNN-xxxxxxx and {KODE}-{TAHUN}-{SEQ}
        $nomorSeqPerKategori = [];

        $asetData = [
            // 1-3: Peralatan Elektronik & IT (BBI_A - Peralatan Elektronik & IT)
            [
                'sub_kode' => 'Komputer Desktop', 'merk_kode' => 'HP', 'type_kode' => 'TY-005-003',
                'ruangan_kode' => 'RG-FT-101', 'tahun' => 2024, 'serial' => 'SN-HP-2024-001',
                'spesifikasi' => 'Intel i7-13700, 16GB DDR4, 512GB SSD, Win 11 Pro', 'harga' => 12500000,
                'sumber_dana_id' => 1, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Laptop', 'merk_kode' => 'HP', 'type_kode' => 'TY-005-001',
                'ruangan_kode' => 'RG-FT-101', 'tahun' => 2023, 'serial' => 'SN-HP-LP-23-002',
                'spesifikasi' => 'Intel i5-1340P, 8GB DDR4, 512GB SSD, 14" FHD', 'harga' => 9500000,
                'sumber_dana_id' => 2, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Laptop', 'merk_kode' => 'Acer', 'type_kode' => 'TY-002-001',
                'ruangan_kode' => 'RG-FKIP-101', 'tahun' => 2024, 'serial' => 'SN-AC-LP-24-003',
                'spesifikasi' => 'Intel i5-1335U, 8GB, 512GB SSD, 15.6" FHD', 'harga' => 8750000,
                'sumber_dana_id' => 1, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Laptop', 'merk_kode' => 'APPLE', 'type_kode' => 'TY-006-001',
                'ruangan_kode' => 'RG-FEB-201', 'tahun' => 2025, 'serial' => 'SN-AP-MBA-25-004',
                'spesifikasi' => 'Apple M2 8-core CPU, 8GB RAM, 256GB SSD, 13.3" Retina', 'harga' => 18500000,
                'sumber_dana_id' => 1, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Laptop', 'merk_kode' => 'Samsung', 'type_kode' => 'TY-003-001',
                'ruangan_kode' => 'RG-FIKOM-101', 'tahun' => 2024, 'serial' => 'SN-SM-LP-24-005',
                'spesifikasi' => 'Intel i7-1360P, 16GB, 1TB SSD, 16" AMOLED', 'harga' => 18900000,
                'sumber_dana_id' => 3, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Laptop', 'merk_kode' => 'XIAOMI', 'type_kode' => 'TY-007-001',
                'ruangan_kode' => 'RG-FKIP-101', 'tahun' => 2023, 'serial' => 'SN-XM-LP-23-006',
                'spesifikasi' => 'Intel i5-12500H, 16GB, 512GB SSD, 15.6" FHD', 'harga' => 9400000,
                'sumber_dana_id' => 4, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Server', 'merk_kode' => 'HP', 'type_kode' => 'TY-005-003',
                'ruangan_kode' => 'RG-FT-102', 'tahun' => 2023, 'serial' => 'SN-HP-SRV-23-007',
                'spesifikasi' => 'HP ProLiant DL380 Gen11, Xeon Silver 4314, 32GB ECC, 2x960GB SSD, RAID 1', 'harga' => 85000000,
                'sumber_dana_id' => 1, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Printer', 'merk_kode' => 'HP', 'type_kode' => 'TY-005-002',
                'ruangan_kode' => 'RG-FH-101', 'tahun' => 2022, 'serial' => 'SN-HP-PR-22-008',
                'spesifikasi' => 'HP LaserJet Pro M404dn, 38ppm, duplex, network', 'harga' => 4200000,
                'sumber_dana_id' => 2, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Proyektor', 'merk_kode' => 'XIAOMI', 'type_kode' => 'TY-007-002',
                'ruangan_kode' => 'RG-FKIP-101', 'tahun' => 2024, 'serial' => 'SN-XM-PRJ-24-009',
                'spesifikasi' => 'Xiaomi Mi 4K Laser Projector 150", ALPD 3.0, 5000 lumens', 'harga' => 28000000,
                'sumber_dana_id' => 3, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Smart TV / Display', 'merk_kode' => 'Samsung', 'type_kode' => 'TY-003-002',
                'ruangan_kode' => 'RG-PAS-202', 'tahun' => 2023, 'serial' => 'SN-SM-TV-23-010',
                'spesifikasi' => 'Samsung UA75CU7000 75" 4K UHD, Crystal Processor 4K, Smart TV', 'harga' => 14500000,
                'sumber_dana_id' => 1, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Switch / Router', 'merk_kode' => 'Costum', 'type_kode' => 'TY-001-001',
                'ruangan_kode' => 'RG-FT-102', 'tahun' => 2023, 'serial' => 'SN-CS-SW-23-011',
                'spesifikasi' => 'Cisco Catalyst 2960-X 48-port Gigabit managed switch', 'harga' => 18500000,
                'sumber_dana_id' => 1, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'UPS', 'merk_kode' => 'Costum', 'type_kode' => 'TY-001-001',
                'ruangan_kode' => 'RG-FT-102', 'tahun' => 2022, 'serial' => 'SN-CS-UPS-22-012',
                'spesifikasi' => 'APC Smart-UPS 3000VA, 230V, LCD monitoring, 8 outlet', 'harga' => 9500000,
                'sumber_dana_id' => 2, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'CCTV', 'merk_kode' => 'Costum', 'type_kode' => 'TY-001-001',
                'ruangan_kode' => 'RG-FEB-202', 'tahun' => 2024, 'serial' => 'SN-CS-CCTV-24-013',
                'spesifikasi' => 'Hikvision DS-2CD2143G2-I 4MP IP Camera, IR 30m, PoE', 'harga' => 1850000,
                'sumber_dana_id' => 4, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Access Point', 'merk_kode' => 'Costum', 'type_kode' => 'TY-001-001',
                'ruangan_kode' => 'RG-FIKOM-101', 'tahun' => 2024, 'serial' => 'SN-CS-AP-24-014',
                'spesifikasi' => 'Ubiquiti UAP-AC-Pro 802.11ac Dual-Radio Access Point', 'harga' => 2350000,
                'sumber_dana_id' => 3, 'kondisi_id' => 1, 'status' => 'aktif',
            ],

            // 15-17: Kendaraan (BBII_A)
            [
                'sub_kode' => 'Mobil Operasional Pimpinan', 'merk_kode' => 'Costum', 'type_kode' => 'TY-001-001',
                'ruangan_kode' => null, 'tahun' => 2021, 'serial' => 'MH-2021-TYT-001',
                'spesifikasi' => 'Toyota Camry 2.5 V A/T, Bensin 2500cc, 4 pintu, hitam', 'harga' => 525000000,
                'sumber_dana_id' => 2, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Mobil Operasional Unit', 'merk_kode' => 'Costum', 'type_kode' => 'TY-001-001',
                'ruangan_kode' => null, 'tahun' => 2022, 'serial' => 'MH-2022-INN-002',
                'spesifikasi' => 'Toyota Innova 2.4 G A/T Diesel, 7-seat, silver metalik', 'harga' => 415000000,
                'sumber_dana_id' => 1, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Sepeda Motor Dinas', 'merk_kode' => 'Costum', 'type_kode' => 'TY-001-001',
                'ruangan_kode' => null, 'tahun' => 2023, 'serial' => 'MH-2023-HND-003',
                'spesifikasi' => 'Honda Beat 110cc, CBS-ISS, hitam, 2 tahun pakai operasional', 'harga' => 17500000,
                'sumber_dana_id' => 2, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Ambulans', 'merk_kode' => 'Costum', 'type_kode' => 'TY-001-001',
                'ruangan_kode' => null, 'tahun' => 2020, 'serial' => 'MH-2020-AMB-004',
                'spesifikasi' => 'Toyota HiAce Commuter 2.7 A/T,改装 ambulans (sirene, tandu, O2)', 'harga' => 585000000,
                'sumber_dana_id' => 3, 'kondisi_id' => 2, 'status' => 'aktif',
            ],

            // 19-21: Furniture & Inventaris (BBII_D) - merk 0
            [
                'sub_kode' => 'Meja Kantor', 'merk_kode' => null, 'type_kode' => null,
                'ruangan_kode' => 'RG-FH-101', 'tahun' => 2022, 'serial' => null,
                'spesifikasi' => 'Meja kerja kayu jati 120x60x75cm, laci 3, warna coklat natural', 'harga' => 1850000,
                'sumber_dana_id' => 2, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Kursi Kantor', 'merk_kode' => null, 'type_kode' => null,
                'ruangan_kode' => 'RG-FH-101', 'tahun' => 2022, 'serial' => null,
                'spesifikasi' => 'Kursi putar hidrolik Oscar, fabric mesh, sandaran tinggi, hitam', 'harga' => 1450000,
                'sumber_dana_id' => 2, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Brankas', 'merk_kode' => 'Costum', 'type_kode' => null,
                'ruangan_kode' => 'RG-FEB-201', 'tahun' => 2021, 'serial' => 'SN-BRK-21-005',
                'spesifikasi' => 'Brankas Okida D-80 digital lock, 80x50x45cm, fire resistant', 'harga' => 6500000,
                'sumber_dana_id' => 2, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Lemari Arsip', 'merk_kode' => null, 'type_kode' => null,
                'ruangan_kode' => 'RG-FH-101', 'tahun' => 2021, 'serial' => null,
                'spesifikasi' => 'Lemari arsip besi 4 pintu, 200x90x40cm, kunci master, abu-abu', 'harga' => 2350000,
                'sumber_dana_id' => 2, 'kondisi_id' => 1, 'status' => 'aktif',
            ],

            // 23-24: Peralatan Laboratorium (BBII_C)
            [
                'sub_kode' => 'Mikroskop', 'merk_kode' => 'Costum', 'type_kode' => 'TY-001-001',
                'ruangan_kode' => 'RG-FKIP-101', 'tahun' => 2022, 'serial' => 'SN-MIC-22-006',
                'spesifikasi' => 'Olympus CX23 LED Biological Microscope, 40-1000x magnification', 'harga' => 14500000,
                'sumber_dana_id' => 3, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Timbangan Digital Presisi', 'merk_kode' => 'Costum', 'type_kode' => 'TY-001-001',
                'ruangan_kode' => 'RG-FKIP-101', 'tahun' => 2023, 'serial' => 'SN-TIM-23-007',
                'spesifikasi' => 'Mettler Toledo ME204E Analytical Balance 220g x 0.1mg', 'harga' => 32500000,
                'sumber_dana_id' => 3, 'kondisi_id' => 1, 'status' => 'aktif',
            ],

            // 25: Software & Lisensi (BBI_B)
            [
                'sub_kode' => 'Lisensi Microsoft', 'merk_kode' => 'APPLE', 'type_kode' => null,
                'ruangan_kode' => 'RG-FH-102', 'tahun' => 2024, 'serial' => 'LIC-MS-24-001',
                'spesifikasi' => 'Microsoft Office 365 Business Premium, 50 user, tahunan', 'harga' => 9500000,
                'sumber_dana_id' => 2, 'kondisi_id' => 1, 'status' => 'aktif',
            ],

            // 26-27: Infrastruktur (BTP_A) - no specific ruangan (infrastruktur gedung)
            [
                'sub_kode' => 'Instalasi Listrik', 'merk_kode' => null, 'type_kode' => null,
                'ruangan_kode' => null, 'tahun' => 2019, 'serial' => null,
                'spesifikasi' => 'Instalasi listrik 3 phase 33KVA, panel distribusi utama gedung rektorat', 'harga' => 185000000,
                'sumber_dana_id' => 1, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Sistem Fire Alarm', 'merk_kode' => 'Costum', 'type_kode' => null,
                'ruangan_kode' => null, 'tahun' => 2021, 'serial' => 'SN-FA-21-002',
                'spesifikasi' => 'Honeywell Notifier Fire Alarm System, 32 zone, addressable', 'harga' => 75000000,
                'sumber_dana_id' => 1, 'kondisi_id' => 1, 'status' => 'aktif',
            ],

            // 28-29: Buku (BBII_E)
            [
                'sub_kode' => 'Buku Teks', 'merk_kode' => null, 'type_kode' => null,
                'ruangan_kode' => 'RG-FH-201', 'tahun' => 2023, 'serial' => null,
                'spesifikasi' => 'Buku "Hukum Pidana Indonesia" - Andi Hamzah, Rajawali Pers, 2022', 'harga' => 185000,
                'sumber_dana_id' => 2, 'kondisi_id' => 1, 'status' => 'aktif',
            ],
            [
                'sub_kode' => 'Buku Referensi', 'merk_kode' => null, 'type_kode' => null,
                'ruangan_kode' => 'RG-PAS-201', 'tahun' => 2024, 'serial' => null,
                'spesifikasi' => 'Buku "Manajemen Keuangan Publik" - Mardiasmo, Andi Offset, 2023', 'harga' => 145000,
                'sumber_dana_id' => 2, 'kondisi_id' => 1, 'status' => 'aktif',
            ],

            // 30: ADP
            [
                'sub_kode' => 'Proyek Infrastruktur', 'merk_kode' => null, 'type_kode' => null,
                'ruangan_kode' => null, 'tahun' => 2025, 'serial' => null,
                'spesifikasi' => 'Pembangunan Gedung Lab Terpadu 4 lantai, progres 65%, berlokasi di Fakultas Teknik', 'harga' => 4500000000,
                'sumber_dana_id' => 1, 'kondisi_id' => 1, 'status' => 'draft',
            ],
        ];

        // Map sub_kategori code -> sk_id
        $skMap = [];
        $skResult = $db->table('aset_sub_kategori')->select('sk_id, kode_sub_kategori, nama_sub_kategori, kategori_id, wajib_merk, wajib_type, wajib_ruangan')->get();
        foreach ($skResult->getResultArray() as $r) {
            $skMap[$r['nama_sub_kategori']] = $r;
        }

        // Map merk code -> mr_id (use nama_merk as key since data uses nama_merk like "HP", "Acer")
        $merkMap = [];
        $merkResult = $db->table('aset_merk')->select('mr_id, kode_merk, nama_merk')->get();
        foreach ($merkResult->getResultArray() as $r) {
            $merkMap[$r['nama_merk']] = $r['mr_id'];
        }

        foreach ($asetData as $idx => $item) {
            $sk = $skMap[$item['sub_kode']] ?? null;
            if (!$sk) continue;

            $ktId = $sk['kategori_id'];
            $skId = $sk['sk_id'];

            // Idempotency: skip if record already exists (by serial or by sub_kategori+tahun+spesifikasi)
            if (!empty($item['serial'])) {
                $exists = $db->table('aset_all_uir')
                    ->where('nomor_aset_lama', $item['serial'])
                    ->countAllResults();
                if ($exists > 0) continue;
            } else {
                $exists = $db->table('aset_all_uir')
                    ->where('sub_kategori_id', $skId)
                    ->where('tahun_perolehan', $item['tahun'])
                    ->where('spesifikasi', $item['spesifikasi'])
                    ->countAllResults();
                if ($exists > 0) continue;
            }
            // Resolve required fields based on wajib flags
            $merkId = null;
            if ($sk['wajib_merk'] && !empty($item['merk_kode']) && isset($merkMap[$item['merk_kode']])) {
                $merkId = $merkMap[$item['merk_kode']];
            }

            $typeId = null;
            if ($sk['wajib_type'] && !empty($item['type_kode']) && isset($typeIds[$item['type_kode']])) {
                $typeId = $typeIds[$item['type_kode']];
            }

            $rgId = null;
            $subUnitId = null;
            if (!empty($item['ruangan_kode']) && isset($ruanganIds[$item['ruangan_kode']])) {
                $rgId = $ruanganIds[$item['ruangan_kode']];
                $rRow = $db->table('aset_ruangan')->select('sub_unit_id')->where('rg_id', $rgId)->get()->getRow();
                $subUnitId = $rRow ? $rRow->sub_unit_id : null;
            }

            // Resolve unit_kerja: from sub_unit if any, else default to PASCASARJANA
            $unitKerjaId = null;
            if ($subUnitId) {
                $r2 = $db->table('aset_sub_units')->select('unit_kerja_id')->where('su_id', $subUnitId)->get()->getRow();
                $unitKerjaId = $r2 ? $r2->unit_kerja_id : null;
            }
            if (!$unitKerjaId) {
                $unitKerjaId = 10; // PASCASARJANA as generic fallback (for vehicles, infrastruktur, ADP, etc.)
            }

            // Resolve golongan from kategori (join via gl_id)
            $katRow = $db->table('aset_kategori')->select('gl_id, nama_kategori')->where('kt_id', $ktId)->get()->getRow();
            $glId = $katRow ? $katRow->gl_id : null;

            // Build nama_aset = "{nama_sub_kategori} / {nama_unit_kerja}"
            $ukRow = $db->table('tbl_unit_kerja')->select('nama_unit')->where('id_unit_kerja', $unitKerjaId)->get()->getRow();
            $namaUnit = $ukRow ? $ukRow->nama_unit : '';
            $namaAset = $sk['nama_sub_kategori'] . ' / ' . $namaUnit;

            // Generate all_id and nomor_aset_baru
            $subFmt = sprintf('%03d', $skId);
            $rand = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 7);
            $allId = "all_{$subFmt}-{$rand}";

            // nomor_aset_baru: {KODE_KAT}-{TAHUN}-{SEQ}
            $katKode = $db->table('aset_kategori')->select('kode_kategori')->where('kt_id', $ktId)->get()->getRow();
            $kodePrefix = $katKode ? strtoupper($katKode->kode_kategori) : 'AS';
            $tahun = $item['tahun'];

            $nomorSeqPerKategori[$kodePrefix][$tahun] = ($nomorSeqPerKategori[$kodePrefix][$tahun] ?? 0) + 1;
            $seq = $nomorSeqPerKategori[$kodePrefix][$tahun];
            $nomorAsetBaru = "{$kodePrefix}-{$tahun}-" . sprintf('%05d', $seq);

            $db->table('aset_all_uir')->insert([
                'all_id'         => $allId,
                'nomor_aset_baru' => $nomorAsetBaru,
                'nomor_aset_lama' => $item['serial'] ?? null,
                'nama_aset'      => $namaAset,
                'golongan_id'    => $glId,
                'kategori_id'    => $ktId,
                'sub_kategori_id' => $skId,
                'merk_id'        => $merkId,
                'type_id'        => $typeId,
                'kondisi_id'     => $item['kondisi_id'],
                'sumber_dana_id' => $item['sumber_dana_id'],
                'unit_kerja_id'  => $unitKerjaId,
                'sub_unit_id'    => $subUnitId,
                'ruangan_id'     => $rgId,
                'serial_number'  => $item['serial'] ?? null,
                'spesifikasi'    => $item['spesifikasi'],
                'tahun_perolehan' => $tahun,
                'tanggal_perolehan' => $tahun . '-' . str_pad((($idx % 12) + 1), 2, '0', STR_PAD_LEFT) . '-15',
                'nilai_perolehan' => $item['harga'],
                'status_aset'    => $item['status'],
                'is_active'      => 1,
                'input_mode'     => 'single',
                'batch_id'       => null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        // Delete aset records by nomor_aset_baru pattern (we just inserted)
        $prefixes = ['BBI_A', 'BBII_A', 'BBII_D', 'BBII_C', 'BBI_B', 'BTP_A', 'BBII_E', 'ADP'];
        $db->table('aset_all_uir')->like('nomor_aset_baru', '2024')->orLike('nomor_aset_baru', '2023')
            ->orLike('nomor_aset_baru', '2022')->orLike('nomor_aset_baru', '2021')
            ->orLike('nomor_aset_baru', '2025')->orLike('nomor_aset_baru', '2019')
            ->delete();
    }
}
