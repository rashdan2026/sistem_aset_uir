<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class LantaiModel extends Model
{
    protected $table = 'aset_lantai';
    protected $primaryKey = 'lt_id';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'gedung_id',
        'kode_lantai',
        'nama_lantai',
        'nomor_lantai',
        'keterangan',
        'is_active'
    ];

    /**
     * Get lantai with gedung info
     */
    public function withGedung()
    {
        return $this->select('aset_lantai.*, aset_gedung.nama_gedung, aset_gedung.kode_gedung, tbl_unit_kerja.nama_unit')
            ->join('aset_gedung', 'aset_gedung.gd_id = aset_lantai.gedung_id', 'left')
            ->join('tbl_unit_kerja', 'tbl_unit_kerja.id_unit_kerja = aset_gedung.unit_kerja_id', 'left');
    }

    /**
     * Get lantai by gedung
     */
    public function getByGedung(int $gedungId): array
    {
        return $this->where('gedung_id', $gedungId)
                    ->where('is_active', 1)
                    ->orderBy('nomor_lantai', 'ASC')
                    ->findAll();
    }

    /**
     * Get all with gedung join (paginated)
     */
    public function getAllWithGedung(int $perPage = 20, int $page = 1): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->select('aset_lantai.*, aset_gedung.nama_gedung, aset_gedung.kode_gedung, tbl_unit_kerja.nama_unit as unit_nama')
                    ->join('aset_gedung', 'aset_gedung.gd_id = aset_lantai.gedung_id', 'left')
                    ->join('tbl_unit_kerja', 'tbl_unit_kerja.id_unit_kerja = aset_gedung.unit_kerja_id', 'left')
                    ->orderBy('aset_gedung.nama_gedung', 'ASC')
                    ->orderBy('aset_lantai.nomor_lantai', 'ASC')
                    ->findAll($perPage, $offset);
    }

    /**
     * Count all records
     */
    public function countAllWithGedung(): int
    {
        return $this->select('aset_lantai.*')
                    ->join('aset_gedung', 'aset_gedung.gd_id = aset_lantai.gedung_id', 'left')
                    ->join('tbl_unit_kerja', 'tbl_unit_kerja.id_unit_kerja = aset_gedung.unit_kerja_id', 'left')
                    ->countAllResults();
    }

    /**
     * Check if kode_lantai is unique within gedung
     */
    public function isKodeUnique(int $gedungId, string $kode, ?int $excludeId = null): bool
    {
        $builder = $this->where('gedung_id', $gedungId)
                        ->where('kode_lantai', $kode);

        if ($excludeId) {
            $builder->where('lt_id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }

    /**
     * Count floors in a building
     */
    public function countByGedung(int $gedungId): int
    {
        return $this->where('gedung_id', $gedungId)->where('is_active', 1)->countAllResults();
    }
}