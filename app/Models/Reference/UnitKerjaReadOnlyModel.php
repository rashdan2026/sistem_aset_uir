<?php

namespace App\Models\Reference;

use CodeIgniter\Model;
use RuntimeException;

/**
 * Read-only model for tbl_unit_kerja.
 * No write operations are allowed on this table.
 */
class UnitKerjaReadOnlyModel extends Model
{
    protected $table = 'tbl_unit_kerja';
    protected $primaryKey = 'id_unit_kerja';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';

    /**
     * Get all active unit kerja for dropdowns
     */
    public function getActiveOptions(): array
    {
        return $this->where('flag_aktif', 1)
                     ->orderBy('nama_unit', 'ASC')
                     ->findAll();
    }

    /**
     * Get unit kerja by ID
     */
    public function getById(int $id): ?array
    {
        return $this->find($id);
    }

    /**
     * Search unit kerja
     */
    public function search(string $keyword, int $limit = 20): array
    {
        return $this->where('flag_aktif', 1)
                     ->like('nama_unit', $keyword)
                     ->orLike('id_unit_kerja', $keyword)
                     ->limit($limit)
                     ->findAll();
    }

    /**
     * Override insert to prevent writes
     */
    public function insert($data = null, bool $returnID = false)
    {
        throw new RuntimeException(
            'tbl_unit_kerja is a READ-ONLY reference table. Insert operations are not allowed.'
        );
    }

    /**
     * Override update to prevent writes
     */
    public function update($id = null, $data = null): bool
    {
        throw new RuntimeException(
            'tbl_unit_kerja is a READ-ONLY reference table. Update operations are not allowed.'
        );
    }

    /**
     * Override delete to prevent writes
     */
    public function delete($id = null, bool $purge = false): bool
    {
        throw new RuntimeException(
            'tbl_unit_kerja is a READ-ONLY reference table. Delete operations are not allowed.'
        );
    }
}