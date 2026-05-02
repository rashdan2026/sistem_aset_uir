<?php

namespace App\Models\Master;

use CodeIgniter\Model;

class SubKategoriModel extends Model
{
    protected $table = 'aset_sub_kategori';
    protected $primaryKey = 'sk_id';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'kategori_id',
        'kode_sub_kategori',
        'nama_sub_kategori',
        'wajib_merk',
        'wajib_type',
        'wajib_ruangan',
        'keterangan',
        'is_active',
        'deleted_at'
    ];

    /**
     * Get sub kategori with kategori info
     */
    public function withKategori()
    {
        return $this->select('aset_sub_kategori.*, aset_kategori.nama_kategori, aset_kategori.kode_kategori')
            ->join('aset_kategori', 'aset_kategori.kt_id = aset_sub_kategori.kategori_id', 'left');
    }

    /**
     * Get sub kategori by kategori
     */
    public function getByKategori(int $kategoriId): array
    {
        return $this->where('kategori_id', $kategoriId)
                    ->where('is_active', 1)
                    ->orderBy('nama_sub_kategori', 'ASC')
                    ->findAll();
    }

    /**
     * Get all with kategori join
     */
    public function getAllWithKategori(): array
    {
        return $this->select('aset_sub_kategori.*, aset_kategori.nama_kategori, aset_kategori.kode_kategori')
                    ->join('aset_kategori', 'aset_kategori.kt_id = aset_sub_kategori.kategori_id', 'left')
                    ->orderBy('aset_kategori.nama_kategori', 'ASC')
                    ->orderBy('aset_sub_kategori.nama_sub_kategori', 'ASC')
                    ->findAll();
    }

    /**
     * Check if kode is unique within kategori
     */
    public function isKodeUnique(int $kategoriId, string $kode, ?int $excludeId = null): bool
    {
        $builder = $this->where('kategori_id', $kategoriId)
                        ->where('kode_sub_kategori', $kode);

        if ($excludeId) {
            $builder->where('sk_id !=', $excludeId);
        }

        return $builder->countAllResults() === 0;
    }

    /**
     * Count sub kategori by kategori
     */
    public function countByKategori(int $kategoriId): int
    {
        return $this->where('kategori_id', $kategoriId)->where('is_active', 1)->countAllResults();
    }
}