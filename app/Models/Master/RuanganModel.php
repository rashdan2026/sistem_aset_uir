<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class RuanganModel extends Model
{
    protected $table = 'aset_ruangan';
    protected $primaryKey = 'rg_id';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'lantai_id',
        'sub_unit_id',
        'kode_ruangan',
        'nama_ruangan',
        'jenis_ruangan',
        'penanggung_jawab_id_kpe',
        'kapasitas',
        'luas_m2',
        'keterangan',
        'is_active',
        'deleted_at'
    ];

    /**
     * Get ruangan with full location info
     */
    public function withFullLocation()
    {
        return $this->select('aset_ruangan.*, 
            aset_lantai.nama_lantai, aset_lantai.nomor_lantai,
            aset_gedung.gd_id as gedung_id_col, aset_gedung.nama_gedung, aset_gedung.kode_gedung,
            aset_sub_units.nama_sub_unit,
            tbl_unit_kerja.nama_unit,
            ylpi_karyawan.nama_gelar as pj_nama,
            ylpi_karyawan.npk as pj_npk')
            ->join('aset_lantai', 'aset_lantai.lt_id = aset_ruangan.lantai_id', 'left')
            ->join('aset_gedung', 'aset_gedung.gd_id = aset_lantai.gedung_id', 'left')
            ->join('aset_sub_units', 'aset_sub_units.su_id = aset_ruangan.sub_unit_id', 'left')
            ->join('tbl_unit_kerja', 'tbl_unit_kerja.id_unit_kerja = aset_gedung.unit_kerja_id', 'left')
            ->join('ylpi_karyawan', 'ylpi_karyawan.id_kpe = aset_ruangan.penanggung_jawab_id_kpe', 'left');
    }

    /**
     * Get ruangan by lantai
     */
    public function getByLantai(int $lantaiId): array
    {
        return $this->where('lantai_id', $lantaiId)
                    ->where('is_active', 1)
                    ->orderBy('kode_ruangan', 'ASC')
                    ->findAll();
    }

    /**
     * Get ruangan by sub unit
     */
    public function getBySubUnit(int $subUnitId): array
    {
        return $this->where('sub_unit_id', $subUnitId)
                    ->where('is_active', 1)
                    ->orderBy('kode_ruangan', 'ASC')
                    ->findAll();
    }

    /**
     * Get ruangan without penanggung jawab
     */
    public function getWithoutPenanggungJawab(): array
    {
        return $this->where('penanggung_jawab_id_kpe IS NULL')
                    ->where('is_active', 1)
                    ->withFullLocation()
                    ->findAll();
    }

    /**
     * Get all with full relations for listing
     */
    public function getAllWithRelations(): array
    {
        return $this->select('aset_ruangan.*,
                    aset_lantai.nama_lantai,
                    aset_lantai.nomor_lantai,
                    aset_gedung.gd_id as gedung_id_col, aset_gedung.nama_gedung,
                    aset_sub_units.nama_sub_unit,
                    tbl_unit_kerja.nama_unit as unit_nama,
                    ylpi_karyawan.nama_gelar as pj_nama,
                    ylpi_karyawan.npk as pj_npk')
                ->join('aset_lantai', 'aset_lantai.lt_id = aset_ruangan.lantai_id', 'left')
                ->join('aset_gedung', 'aset_gedung.gd_id = aset_lantai.gedung_id', 'left')
                ->join('aset_sub_units', 'aset_sub_units.su_id = aset_ruangan.sub_unit_id', 'left')
                ->join('tbl_unit_kerja', 'tbl_unit_kerja.id_unit_kerja = aset_gedung.unit_kerja_id', 'left')
                ->join('ylpi_karyawan', 'ylpi_karyawan.id_kpe = aset_ruangan.penanggung_jawab_id_kpe', 'left')
                ->orderBy('aset_gedung.nama_gedung', 'ASC')
                ->orderBy('aset_lantai.nomor_lantai', 'ASC')
                ->orderBy('aset_ruangan.nama_ruangan', 'ASC')
                ->findAll();
    }

    /**
     * Get paginated records with full relations
     */
    public function getAllWithRelationsPaginated(int $perPage = 20, int $page = 1): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->select('aset_ruangan.*,
                    aset_lantai.nama_lantai,
                    aset_lantai.nomor_lantai,
                    aset_gedung.gd_id as gedung_id_col, aset_gedung.nama_gedung,
                    aset_sub_units.nama_sub_unit,
                    tbl_unit_kerja.nama_unit as unit_nama,
                    ylpi_karyawan.nama_gelar as pj_nama,
                    ylpi_karyawan.npk as pj_npk')
                ->join('aset_lantai', 'aset_lantai.lt_id = aset_ruangan.lantai_id', 'left')
                ->join('aset_gedung', 'aset_gedung.gd_id = aset_lantai.gedung_id', 'left')
                ->join('aset_sub_units', 'aset_sub_units.su_id = aset_ruangan.sub_unit_id', 'left')
                ->join('tbl_unit_kerja', 'tbl_unit_kerja.id_unit_kerja = aset_gedung.unit_kerja_id', 'left')
                ->join('ylpi_karyawan', 'ylpi_karyawan.id_kpe = aset_ruangan.penanggung_jawab_id_kpe', 'left')
                ->orderBy('aset_gedung.nama_gedung', 'ASC')
                ->orderBy('aset_lantai.nomor_lantai', 'ASC')
                ->orderBy('aset_ruangan.nama_ruangan', 'ASC')
                ->findAll($perPage, $offset);
    }

    /**
     * Count all records with relations
     */
    public function countAllWithRelations(): int
    {
        return $this->join('aset_lantai', 'aset_lantai.lt_id = aset_ruangan.lantai_id', 'left')
                    ->join('aset_gedung', 'aset_gedung.gd_id = aset_lantai.gedung_id', 'left')
                    ->join('aset_sub_units', 'aset_sub_units.su_id = aset_ruangan.sub_unit_id', 'left')
                    ->join('tbl_unit_kerja', 'tbl_unit_kerja.id_unit_kerja = aset_gedung.unit_kerja_id', 'left')
                    ->join('ylpi_karyawan', 'ylpi_karyawan.id_kpe = aset_ruangan.penanggung_jawab_id_kpe', 'left')
                    ->countAllResults();
    }

    /**
     * Check if kode_ruangan is unique within lantai
     */
    public function isKodeUnique(int $lantaiId, string $kode, ?int $excludeId = null): bool
    {
        $builder = $this->where('lantai_id', $lantaiId)
                        ->where('kode_ruangan', $kode);

        if ($excludeId) {
            $builder->where('rg_id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }

    /**
     * Get ENUM values for jenis_ruangan column from database
     */
    public function getJenisRuanganOptions(): array
    {
        $db = db_connect();
        $query = $db->query("SHOW COLUMNS FROM aset_ruangan LIKE 'jenis_ruangan'");
        $row = $query->getRow();

        if (!$row) {
            return [];
        }

        preg_match("/^enum\(\'(.*)\'\)$/i", $row->Type, $matches);

        if (!isset($matches[1])) {
            return [];
        }

        return explode("','", $matches[1]);
    }
}