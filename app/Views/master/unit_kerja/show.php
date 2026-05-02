<?php $this->extend('layouts/main'); ?>

<?php $this->section('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white py-3">
                    <h4 class="mb-0"><i class="bi bi-building me-2"></i>Detail Unit Kerja</h4>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted" style="width: 150px;">ID Unit</td>
                            <td class="fw-bold"><?= esc($unitKerja['id_unit_kerja']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama Unit</td>
                            <td class="fw-bold fs-5"><?= esc($unitKerja['nama_unit']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>
                                <span class="badge bg-<?= $unitKerja['flag_aktif'] ? 'success' : 'secondary' ?>">
                                    <?= $unitKerja['flag_aktif'] ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer bg-white">
                    <a href="<?= base_url('/master/unit-kerja') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>