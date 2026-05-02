<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class AsetModel extends Model
{
    protected $table = 'aset_all_uir';
    protected $primaryKey = 'all_id';
    protected $useAutoIncrement = false;  // all_id is not auto-increment
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $deletedField = 'deleted_at';
    protected $allowedFields = [
        'all_id',
        'nomor_aset_baru',
        'nomor_aset_lama',
        'nama_aset',
        'kategori_id',
        'sub_kategori_id',
        'golongan_id',
        'merk_id',
        'type_id',
        'kondisi_id',
        'sumber_dana_id',
        'unit_kerja_id',
        'sub_unit_id',
        'ruangan_id',
        'penanggung_jawab_id_kpe',
        'serial_number',
        'spesifikasi',
        'tahun_perolehan',
        'tanggal_perolehan',
        'nilai_perolehan',
        'status_aset',
        'is_active',
        'deleted_at'
    ];

    /**
     * Get asset with full details including related data
     */
    public function withDetails()
    {
        return $this->select('
                aset_all_uir.*,
                ak.nama_kategori,
                ask.nama_sub_kategori,
                ag.nama_golongan,
                am.nama_merk,
                at.nama_type,
                akb.nama_kondisi,
                asd.nama_sumber_dana,
                tu.nama_unit,
                asu.nama_sub_unit,
                arg.nama_gedung,
                al.nama_lantai,
                ar.nama_ruangan,
                yk.nama_gelar as nama_penanggung_jawab
            ', false)
            ->join('aset_kategori ak', 'ak.kt_id = aset_all_uir.kategori_id', 'left')
            ->join('aset_sub_kategori ask', 'ask.sk_id = aset_all_uir.sub_kategori_id', 'left')
            ->join('aset_golongan ag', 'ag.gl_id = aset_all_uir.golongan_id', 'left')
            ->join('aset_merk am', 'am.mr_id = aset_all_uir.merk_id', 'left')
            ->join('aset_type at', 'at.ty_id = aset_all_uir.type_id', 'left')
            ->join('aset_kondisi_barang akb', 'akb.kd_id = aset_all_uir.kondisi_id', 'left')
            ->join('aset_sumber_dana asd', 'asd.sd_id = aset_all_uir.sumber_dana_id', 'left')
            ->join('tbl_unit_kerja tu', 'tu.id_unit_kerja = aset_all_uir.unit_kerja_id', 'left')
            ->join('aset_sub_units asu', 'asu.su_id = aset_all_uir.sub_unit_id', 'left')
            ->join('aset_ruangan ar', 'ar.rg_id = aset_all_uir.ruangan_id', 'left')
            ->join('aset_lantai al', 'al.lt_id = ar.lantai_id', 'left')
            ->join('aset_gedung arg', 'arg.gd_id = al.gedung_id', 'left')
            ->join('ylpi_karyawan yk', 'yk.id_kpe = aset_all_uir.penanggung_jawab_id_kpe', 'left');
    }

    /**
     * Generate a unique all_id based on sub_kategori and random string
     */
    public function generateAllId(int $subKategoriId): string
    {
        $subKategoriFormatted = sprintf('%03d', $subKategoriId);
        $randomString = $this->generateRandomString(7);
        
        // Ensure uniqueness
        $allId = "all_{$subKategoriFormatted}-{$randomString}";
        $counter = 1;
        
        while (!$this->isAllIdUnique($allId)) {
            $randomString = $this->generateRandomString(7);
            $allId = "all_{$subKategoriFormatted}-{$randomString}";
            $counter++;
            
            // Safety check to avoid infinite loop
            if ($counter > 100) {
                throw new \RuntimeException("Unable to generate unique all_id after 100 attempts");
            }
        }
        
        return $allId;
    }

    /**
     * Check if all_id is unique
     */
    private function isAllIdUnique(string $allId): bool
    {
        return $this->where('all_id', $allId)->countAllResults() === 0;
    }

    /**
     * Generate random string for all_id
     */
    private function generateRandomString(int $length = 7): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    /**
     * Search assets by keyword
     */
    public function searchAssets(string $keyword): array
    {
        return $this->withDetails()
            ->groupStart()
                ->like('aset_all_uir.nama_aset', $keyword)
                ->orLike('aset_all_uir.nomor_aset_baru', $keyword)
                ->orLike('aset_all_uir.nomor_aset_lama', $keyword)
                ->orLike('ak.nama_kategori', $keyword)
                ->orLike('ask.nama_sub_kategori', $keyword)
                ->orLike('am.nama_merk', $keyword)
                ->orLike('at.nama_type', $keyword)
            ->groupEnd()
            ->where('aset_all_uir.is_active', 1)
            ->orderBy('aset_all_uir.created_at', 'DESC')
            ->findAll();
    }
}