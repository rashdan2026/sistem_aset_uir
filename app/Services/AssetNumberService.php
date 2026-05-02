<?php

namespace App\Services;

use App\Models\Master\AsetModel;
use App\Models\Master\SubKategoriModel;
use App\Models\Master\KategoriModel;
use Config\Database;

class AssetNumberService
{
    private $asetModel;
    private $subKategoriModel;
    private $kategoriModel;

    public function __construct()
    {
        $this->asetModel = new AsetModel();
        $this->subKategoriModel = new SubKategoriModel();
        $this->kategoriModel = new KategoriModel();
    }

    /**
     * Generate unique all_id: all_xxx-yyyyyyy
     * xxx = sub_kategori_id padded to 3 digits
     * yyyyyyy = 7 random lowercase alphanumeric
     */
    public function generateAllId(int $subKategoriId): string
    {
        $subKategoriFormatted = sprintf('%03d', $subKategoriId);
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';

        for ($attempt = 0; $attempt < 100; $attempt++) {
            $random = '';
            for ($i = 0; $i < 7; $i++) {
                $random .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $allId = "all_{$subKategoriFormatted}-{$random}";

            if ($this->asetModel->where('all_id', $allId)->countAllResults() === 0) {
                return $allId;
            }
        }

        throw new \RuntimeException('Unable to generate unique all_id after 100 attempts');
    }

    /**
     * Generate nomor_aset_baru format: {KODE_KATEGORI}-{TAHUN}-{SEQ}
     * Example: IT-2026-00001
     */
    public function generateNomorAsetBaru(int $kategoriId, ?int $tahunPerolehan = null): string
    {
        $tahun = $tahunPerolehan ?? (int) date('Y');

        $kategori = $this->kategoriModel->find($kategoriId);
        $kodeKategori = $kategori['kode_kategori'] ?? 'AS';

        $prefix = strtoupper($kodeKategori) . '-' . $tahun . '-';

        $db = Database::connect();
        $last = $db->table('aset_all_uir')
            ->select('nomor_aset_baru')
            ->like('nomor_aset_baru', $prefix, 'after')
            ->orderBy('nomor_aset_baru', 'DESC')
            ->get()
            ->getRowArray();

        $nextSeq = 1;
        if ($last && preg_match('/' . preg_quote($prefix, '/') . '(\d+)$/', $last['nomor_aset_baru'], $m)) {
            $nextSeq = (int) $m[1] + 1;
        }

        return $prefix . sprintf('%05d', $nextSeq);
    }

    /**
     * Get sub kategori info including wajib flags
     */
    public function getSubKategoriInfo(int $subKategoriId): ?array
    {
        return $this->subKategoriModel->find($subKategoriId);
    }

    /**
     * Check if merk is required based on sub kategori
     */
    public function isMerkRequired(int $subKategoriId): bool
    {
        $sk = $this->getSubKategoriInfo($subKategoriId);
        return !empty($sk['wajib_merk']);
    }

    /**
     * Check if type is required based on sub kategori
     */
    public function isTypeRequired(int $subKategoriId): bool
    {
        $sk = $this->getSubKategoriInfo($subKategoriId);
        return !empty($sk['wajib_type']);
    }

    /**
     * Check if ruangan is required based on sub kategori
     */
    public function isRuanganRequired(int $subKategoriId): bool
    {
        $sk = $this->getSubKategoriInfo($subKategoriId);
        return !empty($sk['wajib_ruangan']);
    }
}