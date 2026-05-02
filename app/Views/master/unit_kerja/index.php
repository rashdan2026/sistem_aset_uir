<?php $this->extend('layouts/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Referensi Unit Kerja</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Unit Kerja (<?= number_format($total) ?> data)</h6>
        </div>
        <div class="card-body">
            <form method="get" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" 
                                   placeholder="Cari nama unit kerja..." 
                                   value="<?= esc($search ?? '') ?>">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i> Cari
                            </button>
                            <?php if (!empty($search)): ?>
                                <a href="<?= base_url('/master/unit-kerja') ?>" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle"></i> Reset
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="100">ID</th>
                            <th>Nama Unit Kerja</th>
                            <th width="80">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($unitKerja)): ?>
                        <?php foreach ($unitKerja as $unit): ?>
                        <tr>
                            <td><?= esc($unit['id_unit_kerja']) ?></td>
                            <td><?= esc($unit['nama_unit']) ?></td>
                            <td>
                                <div class="action-btn-group">
                                    <a href="<?= base_url('/master/unit-kerja/' . $unit['id_unit_kerja']) ?>" class="icon-btn detail-btn" title="Detail">
                                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" /></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <?php if (!empty($search)): ?>
                                    <i class="bi bi-search" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                                    Tidak ada hasil untuk pencarian "<?= esc($search) ?>"
                                <?php else: ?>
                                    <i class="bi bi-inbox" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                                    Belum ada data unit kerja.
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php
            $page = $currentPage;
            $totalPages = $totalPages;
            $searchParam = !empty($search) ? '&q=' . urlencode($search) : '';
            $baseUrl = base_url('/master/unit-kerja?page=');

            function renderPageItem($page, $currentPage, $baseUrl, $searchParam, $label = null, $class = '') {
                $label = $label ?? $page;
                $isActive = ($page === $currentPage);
                $url = $baseUrl . $page . $searchParam;
                return '<li class="page-item ' . ($isActive ? 'active' : '') . ($class ? ' ' . $class : '') . '">'
                     . '<a class="page-link" href="' . $url . '">' . $label . '</a></li>';
            }
            ?>

            <?php if ($totalPages > 1): ?>
            <nav class="mt-3">
                <ul class="pagination justify-content-center">
                    <?= renderPageItem(1, $currentPage, $baseUrl, $searchParam, '&laquo;', 'prev') ?>
                    <?= renderPageItem(max(1, $currentPage - 1), $currentPage, $baseUrl, $searchParam, '&lsaquo;', 'prev') ?>

                    <?php
                    $window = 2;
                    $start = max(1, $currentPage - $window);
                    $end = min($totalPages, $currentPage + $window);

                    if ($start > 1) {
                        echo renderPageItem(1, $currentPage, $baseUrl, $searchParam);
                        if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }

                    for ($i = $start; $i <= $end; $i++) {
                        echo renderPageItem($i, $currentPage, $baseUrl, $searchParam);
                    }

                    if ($end < $totalPages) {
                        if ($end < $totalPages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        echo renderPageItem($totalPages, $currentPage, $baseUrl, $searchParam);
                    }
                    ?>

                    <?= renderPageItem(min($totalPages, $currentPage + 1), $currentPage, $baseUrl, $searchParam, '&rsaquo;', 'next') ?>
                    <?= renderPageItem($totalPages, $currentPage, $baseUrl, $searchParam, '&raquo;', 'next') ?>
                </ul>
            </nav>
            <?php endif; ?>

            <div class="text-center text-muted small">
                Menampilkan <?= count($unitKerja) ?> dari <?= $total ?> data
            </div>

            <div class="mt-3 text-muted small">
                <i class="bi bi-info-circle"></i> 
                Data ini diambil dari tabel <code>tbl_unit_kerja</code> yang merupakan referensi read-only dari sistem kampus.
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>
