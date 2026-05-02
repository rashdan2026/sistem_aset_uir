<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class TypeModel extends Model
{
    protected $table = 'aset_type';
    protected $primaryKey = 'ty_id';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $returnType = 'array';

    protected $allowedFields = [
        'merk_id',
        'kode_type',
        'nama_type',
        'keterangan',
        'is_active'
    ];

    /**
     * Get type with merk info
     */
    public function withMerk()
    {
        return $this->select('aset_type.*, aset_merk.nama_merk, aset_merk.kode_merk')
            ->join('aset_merk', 'aset_merk.mr_id = aset_type.merk_id', 'left');
    }

    /**
     * Get type by merk
     */
    public function getByMerk(int $merkId): array
    {
        return $this->where('merk_id', $merkId)
                    ->where('is_active', 1)
                    ->orderBy('nama_type', 'ASC')
                    ->findAll();
    }

    /**
     * Get all active types
     */
    public function getActiveOptions(): array
    {
        return $this->where('is_active', 1)
                   ->orderBy('nama_type', 'ASC')
                   ->findAll();
    }

    /**
     * Get all with merk join
     */
    public function getAllWithMerk(): array
    {
        return $this->select('aset_type.*, aset_merk.nama_merk, aset_merk.kode_merk')
                   ->join('aset_merk', 'aset_merk.mr_id = aset_type.merk_id', 'left')
                   ->orderBy('aset_merk.nama_merk', 'ASC')
                   ->orderBy('aset_type.nama_type', 'ASC')
                   ->findAll();
    }

    /**
     * Check if nama is unique within merk
     */
    public function isNamaUnique(?int $merkId, string $nama, ?int $excludeId = null): bool
    {
        $builder = $this->where('merk_id', $merkId)
                        ->where('nama_type', $nama);

        if ($excludeId) {
            $builder->where('ty_id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }
}