<?php

namespace App\Models\Reference;

use CodeIgniter\Model;

/**
 * Model for reading allowed Unit Kerja from vw_unit_kerja_allowed view.
 * This is the SINGLE source of truth for all Unit Kerja data in the application.
 * tbl_unit_kerja is NEVER queried directly by application features.
 */
class UnitKerjaAllowedModel extends Model
{
    protected $table = 'vw_unit_kerja_allowed';
    protected $primaryKey = 'id_unit_kerja';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_unit_kerja', 'nama_unit', 'flag_aktif'];

    /**
     * Get all allowed unit kerja for dropdowns
     */
    public function getActiveOptions(): array
    {
        return $this->orderBy('nama_unit', 'ASC')->findAll();
    }

    /**
     * Get unit kerja by ID
     */
    public function getById(int $id): ?array
    {
        return $this->find($id);
    }

    /**
     * Search allowed unit kerja for Select2 AJAX
     */
    public function search(string $keyword, int $limit = 20): array
    {
        return $this->like('nama_unit', $keyword)
            ->orLike('id_unit_kerja', $keyword)
            ->orderBy('nama_unit', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get allowed unit kerja for Select2 format
     */
    public function getForSelect2(string $keyword = '', int $limit = 20): array
    {
        $query = $this->orderBy('nama_unit', 'ASC');
        if (!empty($keyword)) {
            $query->groupStart()
                ->like('nama_unit', $keyword)
                ->orLike('id_unit_kerja', $keyword)
                ->groupEnd();
        }
        $results = $query->limit($limit)->findAll();

        return array_map(function($r) {
            return [
                'id' => $r['id_unit_kerja'],
                'text' => $r['nama_unit'] . ' (' . $r['id_unit_kerja'] . ')'
            ];
        }, $results);
    }
}
