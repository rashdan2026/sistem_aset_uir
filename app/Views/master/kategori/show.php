<?php $this->extend('layouts/main'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($title) ?></h1>
        <a href="<?= base_url('/master/kategori') ?>" class="btn btn-secondary btn-sm">Kembali</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Kategori</h6>
        </div>
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <th width="35%">Kode Kategori</th>
                    <td><?= esc($kategori['kode_kategori']) ?></td>
                </tr>
                <tr>
                    <th>Nama Kategori</th>
                    <td><?= esc($kategori['nama_kategori']) ?></td>
                </tr>
                <tr>
                    <th>Golongan</th>
                    <td><?= esc($golongan['nama_golongan'] ?? '-') ?> (<?= esc($golongan['kode_golongan'] ?? '-') ?>)</td>
                </tr>
                <tr>
                    <th>Keterangan</th>
                    <td><?= esc($kategori['keterangan'] ?? '-') ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <?php if ($kategori['is_active']): ?>
                            <span class="badge bg-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Tidak Aktif</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
            <div class="mt-3">
                <a href="<?= base_url('/master/kategori/' . $kategori['kt_id'] . '/edit') ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>