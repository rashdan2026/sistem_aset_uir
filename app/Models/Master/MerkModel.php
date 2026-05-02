<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class MerkModel extends Model
{
    protected $table = 'aset_merk';
    protected $primaryKey = 'mr_id';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $returnType = 'array';

    protected $allowedFields = [
        'kode_merk',
        'nama_merk',
        'keterangan',
        'is_active'
    ];

    /**
     * Get active merk for dropdown
     */
    public function getActiveOptions(): array
    {
        return $this->where('is_active', 1)
                   ->orderBy('nama_merk', 'ASC')
                   ->findAll();
    }

    /**
     * Check if kode or nama is unique
     */
    public function isUnique(string $field, string $value, ?int $excludeId = null): bool
    {
        $builder = $this->where($field, $value);

        if ($excludeId) {
            $builder->where('mr_id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }
}