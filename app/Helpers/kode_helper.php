<?php

if (!function_exists('generate_kode_sub_unit')) {
    function generate_kode_sub_unit(int $unitKerjaId, int $subId): string
    {
        return 'SU-' . str_pad($unitKerjaId, 2, '0', STR_PAD_LEFT) . '-' . str_pad($subId, 3, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generate_kode_gedung')) {
    function generate_kode_gedung(int $unitKerjaId, int $gedungId): string
    {
        return 'GD-' . str_pad($unitKerjaId, 2, '0', STR_PAD_LEFT) . '-' . str_pad($gedungId, 3, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generate_kode_lantai')) {
    function generate_kode_lantai(int $gedungId, int $lantaiId): string
    {
        return 'LT-' . str_pad($gedungId, 2, '0', STR_PAD_LEFT) . '-' . str_pad($lantaiId, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generate_kode_ruangan')) {
    function generate_kode_ruangan(int $subUnitId, int $ruanganId): string
    {
        return 'RG-' . str_pad($subUnitId, 2, '0', STR_PAD_LEFT) . '-' . str_pad($ruanganId, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generate_kode_kategori')) {
    function generate_kode_kategori(int $kategoriId): string
    {
        return 'KT-' . str_pad($kategoriId, 2, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generate_kode_sub_kategori')) {
    function generate_kode_sub_kategori(int $kategoriId, int $subKategoriId): string
    {
        return 'SK-' . str_pad($kategoriId, 2, '0', STR_PAD_LEFT) . '-' . str_pad($subKategoriId, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generate_kode_golongan')) {
    function generate_kode_golongan(int $golonganId): string
    {
        return 'GL-' . str_pad($golonganId, 2, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generate_kode_merk')) {
    function generate_kode_merk(int $merkId): string
    {
        return 'MR-' . str_pad($merkId, 3, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generate_kode_type')) {
    function generate_kode_type(int $merkId, int $typeId): string
    {
        return 'TY-' . str_pad($merkId, 2, '0', STR_PAD_LEFT) . '-' . str_pad($typeId, 3, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generate_kode_sumber_dana')) {
    function generate_kode_sumber_dana(int $sumberDanaId): string
    {
        return 'SD-' . str_pad($sumberDanaId, 2, '0', STR_PAD_LEFT);
    }
}
