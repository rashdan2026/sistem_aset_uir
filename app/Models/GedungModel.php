<?php

namespace App\Models;

use CodeIgniter\Model;

class GedungModel extends Model
{
    protected $table = 'aset_gedung';
    protected $primaryKey = 'gd_id';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'unit_kerja_id',
        'kode_gedung',
        'nama_gedung',
        'alamat_ringkas',
        'jumlah_lantai',
        'keterangan',
        'is_active'
    ];

    // Custom find with unit name
    public function withUnit()
    {
        return $this->select('aset_gedung.*, tbl_unit_kerja.nama_unit')
            ->join('tbl_unit_kerja', 'tbl_unit_kerja.id_unit_kerja = aset_gedung.unit_kerja_id', 'left');
    }

    // Check if kode_gedung is globally unique
    public function isKodeUnique($kode, $excludeId = null)
    {
        $builder = $this->where('kode_gedung', $kode);

        if ($excludeId) {
            $builder->where('gd_id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }
}
