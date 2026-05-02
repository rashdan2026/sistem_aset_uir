<?php $this->extend('layouts/main'); ?>

<?php $this->section('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white py-3">
                    <h4 class="mb-0"><i class="bi bi-person-badge me-2"></i>Detail Penanggung Jawab</h4>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted" style="width: 150px;">ID KPE</td>
                            <td class="fw-bold"><?= esc($pj['id_kpe']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">NPK</td>
                            <td class="fw-bold"><?= esc($pj['npk'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nama</td>
                            <td class="fw-bold fs-5"><?= esc($pj['nama_gelar'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Unit Kerja</td>
                            <td><?= esc($pj['nama_unit'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kategori</td>
                            <td><span class="badge bg-secondary"><?= esc($pj['kategori'] ?? '-') ?></span></td>
                        </tr>
                        <?php if (!empty($pj['email'])): ?>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td><?= esc($pj['email']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($pj['no_hp1'])): ?>
                        <tr>
                            <td class="text-muted">No. HP</td>
                            <td><?= esc($pj['no_hp1']) ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="card-footer bg-white">
                    <a href="<?= base_url('/master/penanggung-jawab') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>