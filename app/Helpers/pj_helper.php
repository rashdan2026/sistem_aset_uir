<?php

if (!function_exists('format_penanggung_jawab')) {
    function format_penanggung_jawab(array $pj): string
    {
        if (empty($pj)) {
            return '-';
        }

        $namaGelar = $pj['nama_gelar'] ?? '-';

        $namaJabatan = $pj['nama_jabatan']
            ?? $pj['pj_nama_jabatan']
            ?? null;

        $tglLahir = $pj['tgl_lahir']
            ?? $pj['pj_tgl_lahir']
            ?? null;

        if (!empty($namaJabatan)) {
            return $namaGelar . ' (' . $namaJabatan . ')';
        }

        if (!empty($tglLahir) && $tglLahir !== '0000-00-00') {
            $tglFormatted = date('d-m-Y', strtotime($tglLahir));
            return $namaGelar . ' (' . $tglFormatted . ')';
        }

        return $namaGelar;
    }
}

if (!function_exists('format_pj_display')) {
    function format_pj_display(array $pj): string
    {
        return format_penanggung_jawab($pj);
    }
}