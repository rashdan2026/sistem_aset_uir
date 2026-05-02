<?php

namespace App\Models\Reference;

use CodeIgniter\Model;
use RuntimeException;

/**
 * Read-only model for penanggung jawab (ylpi_karyawan or vw_penanggung_jawab).
 * No write operations are allowed on this data source.
 */
class PenanggungJawabReadOnlyModel extends Model
{
    protected $table = 'vw_penanggung_jawab';
    protected $primaryKey = 'id_kpe';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';

    /**
     * Get all active penanggung jawab for dropdowns
     */
    public function getActiveOptions(): array
    {
        return $this->orderBy('nama_gelar', 'ASC')->findAll();
    }

    /**
     * Get penanggung jawab by ID (id_kpe)
     */
    public function getById(string $id): ?array
    {
        return $this->where('id_kpe', $id)->first();
    }

    /**
     * Get penanggung jawab by unit kerja
     */
    public function getByUnitKerja(int $unitKerjaId): array
    {
        return $this->where('unit_kerja_id', $unitKerjaId)
                    ->orderBy('nama_gelar', 'ASC')
                    ->findAll();
    }

    /**
     * Search penanggung jawab
     */
    public function search(string $keyword, int $limit = 20): array
    {
        return $this->groupStart()
                    ->like('nama_gelar', $keyword)
                    ->orLike('npk', $keyword)
                    ->orLike('nama_unit', $keyword)
                    ->groupEnd()
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Search active penanggung jawab with both/LIKE search
     */
    public function searchActive(string $keyword, int $limit = 20): array
    {
        return $this->where('is_active', 1)
                    ->groupStart()
                        ->like('nama_gelar', $keyword, 'both')
                        ->orLike('npk', $keyword, 'both')
                    ->groupEnd()
                    ->orderBy('nama_gelar', 'ASC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Override insert to prevent writes
     */
    public function insert($data = null, bool $returnID = false)
    {
        throw new RuntimeException(
            'Penanggung jawab data is READ-ONLY. Insert operations are not allowed.'
        );
    }

    /**
     * Override update to prevent writes
     */
    public function update($id = null, $data = null): bool
    {
        throw new RuntimeException(
            'Penanggung jawab data is READ-ONLY. Update operations are not allowed.'
        );
    }

    /**
     * Override delete to prevent writes
     */
    public function delete($id = null, bool $purge = false): bool
    {
        throw new RuntimeException(
            'Penanggung jawab data is READ-ONLY. Delete operations are not allowed.'
        );
    }
}