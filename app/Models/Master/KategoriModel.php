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
        'gl_id',
        'keterangan',
        'is_active',
        'deleted_at'
    ];

    public function getActiveOptions(): array
    {
        return $this->where('is_active', 1)
                   ->where('deleted_at', null)
                   ->orderBy('nama_kategori', 'ASC')
                   ->findAll();
    }

    public function getByGolonganId(int $golonganId): array
    {
        return $this->where('gl_id', $golonganId)
                    ->where('is_active', 1)
                    ->where('deleted_at', null)
                    ->orderBy('nama_kategori', 'ASC')
                    ->findAll();
    }

    public function getWithGolongan(): array
    {
        $db = db_connect();
        return $db->table('aset_kategori k')
            ->select('k.*, g.nama_golongan, g.kode_golongan, g.gl_id as gol_id')
            ->join('aset_golongan g', 'g.gl_id = k.gl_id', 'left')
            ->where('k.is_active', 1)
            ->where('k.deleted_at', null)
            ->orderBy('k.nama_kategori', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function isUnique(string $field, string $value, ?int $excludeId = null): bool
    {
        $builder = $this->where($field, $value);

        if ($excludeId) {
            $builder->where('kt_id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }
}
