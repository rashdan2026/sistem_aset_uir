<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class KondisiBarangModel extends Model
{
    protected $table = 'aset_kondisi_barang';
    protected $primaryKey = 'kd_id';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'kode_kondisi',
        'nama_kondisi',
        'level_kondisi',
        'is_available_for_use',
        'keterangan',
        'is_active'
    ];

    /**
     * Get active kondisi for dropdown
     */
    public function getActiveOptions(): array
    {
        return $this->where('is_active', 1)
                   ->orderBy('level_kondisi', 'ASC')
                   ->findAll();
    }

    /**
     * Get kondisi that can be used
     */
    public function getAvailableForUse(): array
    {
        return $this->where('is_active', 1)
                   ->where('is_available_for_use', 1)
                   ->orderBy('level_kondisi', 'ASC')
                   ->findAll();
    }

    /**
     * Check if kode or nama is unique
     */
    public function isUnique(string $field, string $value, ?int $excludeId = null): bool
    {
        $builder = $this->where($field, $value);

        if ($excludeId) {
            $builder->where('kd_id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }
}