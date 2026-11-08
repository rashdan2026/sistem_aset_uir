<?php

namespace App\Controllers\Public;

use App\Controllers\BaseController;
use App\Models\Master\AsetModel;

class QrController extends BaseController
{
    protected AsetModel $asetModel;

    public function __construct()
    {
        $this->asetModel = new AsetModel();
        helper(['url', 'qr']);
    }

    /**
     * Display the public asset information page for an asset identified
     * by its `all_id` segment. This endpoint is intentionally public and
     * exposes only fields that are safe to be shown to the general public.
     */
    public function show(string $allId)
    {
        $allId = trim($allId);
        if ($allId === '' || !preg_match('/^all_[0-9]{3}-[a-z0-9]{1,15}$/', $allId)) {
            return $this->renderNotFound('ID aset tidak valid.');
        }

        $record = $this->asetModel
            ->withDetails()
            ->where('aset_all_uir.all_id', $allId)
            ->asArray()
            ->first();

        if (!$record) {
            return $this->renderNotFound('Data aset tidak ditemukan.');
        }

        if (!empty($record['deleted_at'])) {
            return $this->renderUnavailable('Aset ini telah dihapus dari sistem dan tidak lagi ditampilkan kepada publik.');
        }

        if ((int) ($record['is_active'] ?? 0) !== 1) {
            return $this->renderUnavailable('Aset ini sedang tidak aktif dan tidak ditampilkan kepada publik.');
        }

        $status = strtolower((string) ($record['status_aset'] ?? ''));
        if (!in_array($status, ['aktif'], true)) {
            return $this->renderUnavailable('Aset ini berstatus "' . ($record['status_aset'] ?? 'tidak diketahui') . '" dan belum tersedia untuk publik.');
        }

        return $this->renderPublic($record);
    }

    /**
     * Render the elegant public asset info page using a public view that
     * does NOT extend the master layout (no admin chrome, no sidebar).
     */
    protected function renderPublic(array $record)
    {
        $publicUrl = qr_public_url($record['all_id']);
        $qrSrc     = qr_image_src($publicUrl, 360);

        $data = [
            'title'         => 'Informasi Aset ' . ($record['nomor_aset_baru'] ?? $record['all_id']),
            'record'        => $record,
            'publicUrl'     => $publicUrl,
            'qrSrc'         => $qrSrc,
        ];

        return view('public/qr/show', $data);
    }

    protected function renderNotFound(string $message)
    {
        return view('public/qr/not_found', [
            'title'   => 'Data Aset Tidak Ditemukan',
            'message' => $message,
            'qrSrc'   => qr_image_src(base_url(), 200),
        ]);
    }

    protected function renderUnavailable(string $message)
    {
        return view('public/qr/unavailable', [
            'title'   => 'Aset Tidak Tersedia',
            'message' => $message,
            'qrSrc'   => qr_image_src(base_url(), 200),
        ]);
    }
}
