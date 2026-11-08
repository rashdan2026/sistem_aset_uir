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
        'keterangan',
        'is_active'
    ];

    public function getActiveOptions(): array
    {
        return $this->where('is_active', 1)
                   ->orderBy('kode_golongan', 'ASC')
                   ->findAll();
    }

    public function getByKategoriId(int $kategoriId): array
    {
        $db = db_connect();
        $kategori = $db->table('aset_kategori')
            ->select('gl_id')
            ->where('kt_id', $kategoriId)
            ->where('is_active', 1)
            ->get()
            ->getRowArray();

        if (!$kategori || empty($kategori['gl_id'])) {
            return [];
        }

        return $this->where('gl_id', $kategori['gl_id'])
                    ->where('is_active', 1)
                    ->findAll();
    }

    public function isUnique(string $field, string $value, ?int $excludeId = null): bool
    {
        $builder = $this->where($field, $value);

        if ($excludeId) {
            $builder->where('gl_id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }
}
