<?php

namespace App\Models\Reference;

use CodeIgniter\Model;

/**
 * Model for managing the whitelist of allowed Unit Kerja.
 * Only used by the Setting admin controller.
 */
class SettingUnitKerjaModel extends Model
{
    protected $table = 'setting_unit_kerja_allowed';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['id_unit_kerja', 'is_active', 'created_by', 'updated_by'];

    /**
     * Check if a unit kerja is in the allowed list
     */
    public function isAllowed(int $unitKerjaId): bool
    {
        return $this->where('id_unit_kerja', $unitKerjaId)
            ->where('is_active', 1)
            ->countAllResults() > 0;
    }

    /**
     * Get all settings with unit names from reference table
     */
    public function getAllWithUnitNames(): array
    {
        $db = db_connect();
        return $db->table('setting_unit_kerja_allowed s')
            ->select('s.id, s.id_unit_kerja, s.is_active, s.created_at, s.updated_at, u.nama_unit')
            ->join('tbl_unit_kerja u', 'u.id_unit_kerja = s.id_unit_kerja', 'left')
            ->orderBy('u.nama_unit', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Toggle active status for a unit kerja
     */
    public function toggleStatus(int $unitKerjaId, bool $active): bool
    {
        $userId = session('user_id') ?? null;
        $existing = $this->where('id_unit_kerja', $unitKerjaId)->first();
        $now = date('Y-m-d H:i:s');

        if ($existing) {
            return $this->update($existing['id'], [
                'is_active' => $active ? 1 : 0,
                'updated_by' => $userId,
                'updated_at' => $now,
            ]);
        }
        return $this->insert([
            'id_unit_kerja' => $unitKerjaId,
            'is_active' => $active ? 1 : 0,
            'created_by' => $userId,
            'updated_by' => $userId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * Insert or update a setting with audit fields
     */
    public function upsert(int $unitKerjaId, bool $isAllowed): bool
    {
        $userId = session('user_id') ?? null;
        $existing = $this->where('id_unit_kerja', $unitKerjaId)->first();
        $now = date('Y-m-d H:i:s');

        if ($existing) {
            return $this->update($existing['id'], [
                'is_active' => $isAllowed ? 1 : 0,
                'updated_by' => $userId,
                'updated_at' => $now,
            ]);
        }
        return $this->insert([
            'id_unit_kerja' => $unitKerjaId,
            'is_active' => $isAllowed ? 1 : 0,
            'created_by' => $userId,
            'updated_by' => $userId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}