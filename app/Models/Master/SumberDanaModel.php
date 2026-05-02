<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class SumberDanaModel extends Model
{
    protected $table = 'aset_sumber_dana';
    protected $primaryKey = 'sd_id';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'kode_sumber_dana',
        'nama_sumber_dana',
        'keterangan',
        'is_active'
    ];

    /**
     * Get active sumber dana for dropdown
     */
    public function getActiveOptions(): array
    {
        return $this->where('is_active', 1)
                   ->orderBy('nama_sumber_dana', 'ASC')
                   ->findAll();
    }

    /**
     * Check if kode or nama is unique
     */
    public function isUnique(string $field, string $value, ?int $excludeId = null): bool
    {
        $builder = $this->where($field, $value);

        if ($excludeId) {
            $builder->where('sd_id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }
}