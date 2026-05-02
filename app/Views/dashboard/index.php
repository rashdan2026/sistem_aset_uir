<?php $this->extend('layouts/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-4 border-primary h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Unit Kerja</div>
                            <div class="h5 mb-0 fw-bold"><?= $totalUnitKerja ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-buildings fs-1 text-muted opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-4 border-success h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Total Sub Unit</div>
                            <div class="h5 mb-0 fw-bold"><?= $totalSubUnit ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-house-door fs-1 text-muted opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-4 border-info h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Total Gedung</div>
                            <div class="h5 mb-0 fw-bold"><?= $totalGedung ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-building fs-1 text-muted opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-4 border-warning h-100 py-2">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Total Ruangan</div>
                            <div class="h5 mb-0 fw-bold"><?= $totalRuangan ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-door-open fs-1 text-muted opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 fw-bold">Daftar Ruangan Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Nama Ruangan</th>
                                    <th>Gedung</th>
                                    <th>Lantai</th>
                                    <th>Penanggung Jawab</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($latestRuangan)): ?>
                                    <?php foreach ($latestRuangan as $ruangan): ?>
                                        <tr>
                                            <td><?= esc($ruangan['nama_ruangan'] ?? '-') ?></td>
                                            <td><?= esc($ruangan['nama_gedung'] ?? '-') ?></td>
                                            <td><?= esc($ruangan['nama_lantai'] ?? '-') ?></td>
                                            <td><?= esc($ruangan['pj_nama'] ?? '-') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data ruangan</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="m-0 fw-bold">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="<?= base_url('/master/sub-units/new') ?>" class="btn btn-primary w-100">
                                <i class="bi bi-plus-lg"></i> Tambah Sub Unit
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= base_url('/master/gedung/new') ?>" class="btn btn-success w-100">
                                <i class="bi bi-plus-lg"></i> Tambah Gedung
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= base_url('/master/ruangan/new') ?>" class="btn btn-info w-100">
                                <i class="bi bi-plus-lg"></i> Tambah Ruangan
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="<?= base_url('/master/kategori/new') ?>" class="btn btn-warning w-100">
                                <i class="bi bi-plus-lg"></i> Tambah Kategori
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>