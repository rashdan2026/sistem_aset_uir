<?php $this->extend('layouts/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Sub Unit</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Sub Unit</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-3"><strong>Kode Sub Unit</strong></div>
                        <div class="col-sm-9"><?= esc($subUnit['kode_sub_unit']) ?></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3"><strong>Nama Sub Unit</strong></div>
                        <div class="col-sm-9"><?= esc($subUnit['nama_sub_unit']) ?></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3"><strong>Unit Kerja</strong></div>
                        <div class="col-sm-9"><?= esc($subUnit['unit_nama'] ?? '-') ?></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3"><strong>Jenis Sub Unit</strong></div>
                        <div class="col-sm-9"><?= esc($subUnit['jenis_sub_unit'] ?? '-') ?></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3"><strong>Keterangan</strong></div>
                        <div class="col-sm-9"><?= esc($subUnit['keterangan'] ?? '-') ?></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3"><strong>Status</strong></div>
                        <div class="col-sm-9">
                            <?php if ($subUnit['is_active']): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Tidak Aktif</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3"><strong>Dibuat Tanggal</strong></div>
                        <div class="col-sm-9"><?= $subUnit['created_at'] ?></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3"><strong>Diubah Tanggal</strong></div>
                        <div class="col-sm-9"><?= $subUnit['updated_at'] ?></div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?= base_url('/master/sub-units') ?>" class="btn btn-secondary">Kembali</a>
                    <a href="<?= base_url('/master/sub-units/' . $subUnit['su_id'] . '/edit') ?>" class="btn btn-warning">Edit</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>