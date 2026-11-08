<?php $this->extend('layouts/main'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($title) ?></h1>
        <a href="<?= base_url('/master/golongan') ?>" class="btn btn-secondary btn-sm">Kembali</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Golongan</h6>
        </div>
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <th width="35%">Kode Golongan</th>
                    <td><?= esc($record['kode_golongan']) ?></td>
                </tr>
                <tr>
                    <th>Nama Golongan</th>
                    <td><?= esc($record['nama_golongan']) ?></td>
                </tr>
                <tr>
                    <th>Keterangan</th>
                    <td><?= esc($record['keterangan'] ?? '-') ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <?php if ($record['is_active']): ?>
                            <span class="badge bg-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Tidak Aktif</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            <div class="mt-3">
                <a href="<?= base_url('/master/golongan/' . $record['gl_id'] . '/edit') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>
