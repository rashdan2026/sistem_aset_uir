<?php

namespace App\Models;

use CodeIgniter\Model;

class SubUnitModel extends Model
{
    protected $table = 'aset_sub_units';
    protected $primaryKey = 'su_id';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'unit_kerja_id',
        'kode_sub_unit',
        'nama_sub_unit',
        'jenis_sub_unit',
        'keterangan',
        'is_active',
        'deleted_at'
    ];

    // Custom find with unit name
    public function withUnit()
    {
        return $this->select('aset_sub_units.*, tbl_unit_kerja.nama_unit')
            ->join('tbl_unit_kerja', 'tbl_unit_kerja.id_unit_kerja = aset_sub_units.unit_kerja_id', 'left');
    }

    // Check if kode_sub_unit is unique within unit_kerja_id
    public function isKodeUnique($kode, $unitId, $excludeId = null)
    {
        $builder = $this->where('unit_kerja_id', $unitId)
                        ->where('kode_sub_unit', $kode);

        if ($excludeId) {
            $builder->where('su_id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }
    
    public function withUnitKerja()
    {
        return $this->select('aset_sub_units.*, tbl_unit_kerja.nama_unit as unit_nama')
            ->join('tbl_unit_kerja', 'tbl_unit_kerja.id_unit_kerja = aset_sub_units.unit_kerja_id', 'left');
    }

    public function withUnitKerjaPaginated(int $perPage = 20, int $page = 1): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->select('aset_sub_units.*, tbl_unit_kerja.nama_unit as unit_nama')
            ->join('tbl_unit_kerja', 'tbl_unit_kerja.id_unit_kerja = aset_sub_units.unit_kerja_id', 'left')
            ->orderBy('tbl_unit_kerja.nama_unit', 'ASC')
            ->orderBy('aset_sub_units.nama_sub_unit', 'ASC')
            ->findAll($perPage, $offset);
    }

    public function countWithUnitKerja(): int
    {
        return $this->select('aset_sub_units.*')
            ->join('tbl_unit_kerja', 'tbl_unit_kerja.id_unit_kerja = aset_sub_units.unit_kerja_id', 'left')
            ->countAllResults();
    }
}
