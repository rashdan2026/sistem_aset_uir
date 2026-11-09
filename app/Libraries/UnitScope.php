<?php

namespace App\Libraries;

/**
 * Unit scope enforcement berdasarkan sys_user_unit_scopes.
 *
 * Semantik sementara (keputusan 2026-11-10):
 * - super_admin / admin_aset_pusat  => null (unrestricted)
 * - User tanpa baris scope aktif    => null (full access sementara)
 * - User dengan baris scope aktif   => array unit_kerja_id yang diizinkan
 *
 * Saat data scope seluruh user sudah diisi, ubah perilaku
 * "tanpa baris = full access" menjadi "tanpa baris = tanpa akses".
 */
class UnitScope
{
    /**
     * @return array|int[]|null Array unit_kerja_id yang diizinkan, atau null bila unrestricted.
     */
    public function allowedUnitIds($userId = null)
    {
        $userId = $userId !== null ? (int) $userId : (int) (session('user_id') ?? 0);
        if ($userId <= 0) {
            return [];
        }

        if ($this->isUnrestricted($userId)) {
            return null;
        }

        $rows = db_connect()->table('sys_user_unit_scopes')
            ->select('unit_kerja_id')
            ->where('user_id', $userId)
            ->where('is_active', 1)
            ->get()
            ->getResultArray();

        if (count($rows) === 0) {
            return null; // tanpa baris scope = full access sementara
        }

        return array_map('intval', array_column($rows, 'unit_kerja_id'));
    }

    public function canAccessUnit($unitKerjaId, $userId = null): bool
    {
        $allowed = $this->allowedUnitIds($userId);
        if ($allowed === null) {
            return true;
        }

        return in_array((int) $unitKerjaId, $allowed, true);
    }

    /**
     * Filter builder dengan scope unit kerja user saat ini.
     * $builder diharapkan sudah memakai alias kolom unit kerja pada $column.
     */
    public function applyToBuilder($builder, string $column)
    {
        $allowed = $this->allowedUnitIds();
        if (is_array($allowed)) {
            if (count($allowed) === 0) {
                // Tidak ada unit yang diizinkan sama sekali
                $builder->where('1 = 0');
            } else {
                $builder->whereIn($column, $allowed);
            }
        }

        return $builder;
    }

    private function isUnrestricted(int $userId): bool
    {
        return db_connect()->table('sys_user_roles ur')
            ->join('sys_roles r', 'r.id = ur.role_id')
            ->where('ur.user_id', $userId)
            ->where('ur.is_active', 1)
            ->where('r.is_active', 1)
            ->whereIn('r.role_code', ['super_admin', 'admin_aset_pusat'])
            ->countAllResults() > 0;
    }
}
