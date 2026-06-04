<?php

if (!function_exists('get_aset_visible_columns')) {
    function get_aset_visible_columns(): array
    {
        $defaults = ['nomor_aset_baru', 'nama_aset', 'nama_kategori', 'nama_sub_kategori', 'nama_kondisi', 'status_aset'];
        if (isset($_COOKIE['aset_column_prefs'])) {
            $saved = json_decode($_COOKIE['aset_column_prefs'], true);
            if (is_array($saved) && count($saved) > 0) {
                return $saved;
            }
        }
        return $defaults;
    }
}

if (!function_exists('get_aset_all_columns')) {
    function get_aset_all_columns(): array
    {
        return [
            'nomor_aset_baru' => 'No Aset Baru',
            'nama_aset' => 'Nama Aset',
            'nama_kategori' => 'Kategori',
            'nama_sub_kategori' => 'Sub Kategori',
            'nama_kondisi' => 'Kondisi',
            'input_mode' => 'Mode',
            'status_aset' => 'Status',
            'nama_merk' => 'Merk',
            'nama_type' => 'Type',
            'nama_sumber_dana' => 'Sumber Dana',
            'nama_unit' => 'Unit Kerja',
            'nama_sub_unit' => 'Sub Unit',
            'nama_ruangan' => 'Ruangan',
            'nama_penanggung_jawab' => 'Penanggung Jawab',
            'nilai_perolehan' => 'Nilai Perolehan',
            'tahun_perolehan' => 'Tahun Perolehan',
        ];
    }
}

if (!function_exists('get_aset_column_map')) {
    function get_aset_column_map(): array
    {
        return [
            'nomor_aset_baru' => 'a.nomor_aset_baru',
            'nama_aset' => 'a.nama_aset',
            'nama_kategori' => 'ak.nama_kategori',
            'nama_sub_kategori' => 'ask.nama_sub_kategori',
            'nama_kondisi' => 'akb.nama_kondisi',
            'input_mode' => 'a.input_mode',
            'status_aset' => 'a.status_aset',
            'nama_merk' => 'am.nama_merk',
            'nama_type' => 'at.nama_type',
            'nama_sumber_dana' => 'asd.nama_sumber_dana',
            'nama_unit' => 'tu.nama_unit',
            'nama_sub_unit' => 'asu.nama_sub_unit',
            'nama_ruangan' => 'ar.nama_ruangan',
            'nama_penanggung_jawab' => 'yk.nama_gelar',
            'nilai_perolehan' => 'a.nilai_perolehan',
            'tahun_perolehan' => 'a.tahun_perolehan',
        ];
    }
}
