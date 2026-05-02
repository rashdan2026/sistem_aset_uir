<?php $this->extend('layouts/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Gedung</h1>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Gedung</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Kode Gedung</strong></div>
                        <div class="col-sm-8"><?= esc($gedung['kode_gedung']) ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Nama Gedung</strong></div>
                        <div class="col-sm-8"><?= esc($gedung['nama_gedung']) ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Unit Kerja</strong></div>
                        <div class="col-sm-8"><?= esc($gedung['nama_unit'] ?? '-') ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Alamat</strong></div>
                        <div class="col-sm-8"><?= esc($gedung['alamat_ringkas'] ?? '-') ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Jumlah Lantai</strong></div>
                        <div class="col-sm-8"><?= esc($gedung['jumlah_lantai']) ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Keterangan</strong></div>
                        <div class="col-sm-8"><?= esc($gedung['keterangan'] ?? '-') ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Status</strong></div>
                        <div class="col-sm-8">
                            <?php if ($gedung['is_active']): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Tidak Aktif</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Dibuat Tanggal</strong></div>
                        <div class="col-sm-8"><?= $gedung['created_at'] ?? '-' ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4"><strong>Diubah Tanggal</strong></div>
                        <div class="col-sm-8"><?= $gedung['updated_at'] ?? '-' ?></div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?= base_url('/master/gedung') ?>" class="btn btn-secondary">Kembali</a>
                    <a href="<?= base_url('/master/gedung/' . $gedung['gd_id'] . '/edit') ?>" class="btn btn-warning">Edit</a>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Lantai</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($lantai)): ?>
                        <p class="text-muted">Belum ada data lantai.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Lantai</th>
                                        <th>Nama Lantai</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($lantai as $i => $lt): ?>
                                        <tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><?= esc($lt['kode_lantai']) ?></td>
                                            <td><?= esc($lt['nama_lantai']) ?></td>
                                            <td>
                                                <?php if ($lt['is_active']): ?>
                                                    <span class="badge badge-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>