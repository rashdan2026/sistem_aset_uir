<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table = 'aset_kategori';
    protected $primaryKey = 'kt_id';
    protected $useSoftDeletes = true;
    protected $dateFormat = 'datetime';
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'kode_kategori',
        'nama_kategori',
        'jenis_aset',
        'keterangan',
        'is_active'
    ];

    /**
     * Get active categories for dropdown
     */
    public function getActiveOptions(): array
    {
        return $this->where('is_active', 1)
                   ->orderBy('nama_kategori', 'ASC')
                   ->findAll();
    }

    /**
     * Get categories by jenis aset
     */
    public function getByJenisAset(string $jenis): array
    {
        return $this->where('jenis_aset', $jenis)
                    ->where('is_active', 1)
                    ->orderBy('nama_kategori', 'ASC')
                    ->findAll();
    }

    /**
     * Check if kode or nama is unique
     */
    public function isUnique(string $field, string $value, ?int $excludeId = null): bool
    {
        $builder = $this->where($field, $value);

        if ($excludeId) {
            $builder->where('kt_id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }
}