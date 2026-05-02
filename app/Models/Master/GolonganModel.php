<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class GolonganModel extends Model
{
    protected $table = 'aset_golongan';
    protected $primaryKey = 'gl_id';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $returnType = 'array';

    protected $allowedFields = [
        'kode_golongan',
        'nama_golongan',
        'kelompok',
        'keterangan',
        'is_active'
    ];

    /**
     * Get active golongan for dropdown
     */
    public function getActiveOptions(): array
    {
        return $this->where('is_active', 1)
                   ->orderBy('nama_golongan', 'ASC')
                   ->findAll();
    }

    /**
     * Get golongan by kelompok
     */
    public function getByKelompok(string $kelompok): array
    {
        return $this->where('kelompok', $kelompok)
                    ->where('is_active', 1)
                    ->orderBy('nama_golongan', 'ASC')
                    ->findAll();
    }

    /**
     * Get golongan filtered by kategori's jenis_aset
     * Returns active golongan whose kelompok matches the kategori's jenis_aset
     */
    public function getByKategoriId(int $kategoriId): array
    {
        $db = db_connect();
        $kategori = $db->table('aset_kategori')
            ->select('jenis_aset')
            ->where('kt_id', $kategoriId)
            ->where('is_active', 1)
            ->get()
            ->getRowArray();

        if (!$kategori || empty($kategori['jenis_aset'])) {
            return [];
        }

        return $this->where('kelompok', $kategori['jenis_aset'])
                    ->where('is_active', 1)
                    ->orderBy('nama_golongan', 'ASC')
                    ->findAll();
    }

    /**
     * Check if kode or nama is unique
     */
    public function isUnique(string $field, string $value, ?int $excludeId = null): bool
    {
        $builder = $this->where($field, $value);

        if ($excludeId) {
            $builder->where('gl_id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }
}