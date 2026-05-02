<?php $this->extend('layouts/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"></h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Sub Unit (<?= number_format($total) ?> data)</h6>
            <a href="<?= base_url('/master/sub-units/new') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Sub Unit</a>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('/master/sub-units') ?>" class="mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <input type="text" name="q" class="form-control" value="<?= esc($params['q'] ?? '') ?>" placeholder="Cari kode, nama sub unit, atau unit kerja...">
                    </div>
                    <div class="col-md-3">
                        <select name="unit_kerja_id" class="form-select">
                            <option value="">Semua Unit Kerja</option>
                            <?php if (!empty($filterOptions['unit_kerja'])): ?>
                                <?php foreach ($filterOptions['unit_kerja'] as $uk): ?>
                                    <option value="<?= esc($uk['id_unit_kerja']) ?>" <?= ($params['unit_kerja_id'] ?? '') == $uk['id_unit_kerja'] ? 'selected' : '' ?>>
                                        <?= esc($uk['nama_unit']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="is_active" class="form-select">
                            <option value="">Status</option>
                            <option value="1" <?= ($params['is_active'] ?? '') === '1' ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= ($params['is_active'] ?? '') === '0' ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
                        <?php if (!empty(array_filter($params ?? []))): ?>
                            <a href="<?= base_url('/master/sub-units') ?>" class="btn btn-secondary">Reset</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Sub Unit</th>
                            <th>Unit Kerja</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($subUnits)): ?>
                        <?php foreach ($subUnits as $subUnit): ?>
                        <tr>
                            <td><?= esc($subUnit['kode_sub_unit']) ?></td>
                            <td><?= esc($subUnit['nama_sub_unit']) ?></td>
                            <td><?= esc($subUnit['unit_nama'] ?? '-') ?></td>
                            <td><?= esc($subUnit['jenis_sub_unit'] ?? '-') ?></td>
                            <td>
                                <?php if ($subUnit['is_active']): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btn-group">
                                    <a href="<?= base_url('/master/sub-units/' . $subUnit['su_id']) ?>" class="icon-btn detail-btn" title="Detail">
                                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" /></svg>
                                    </a>
                                    <a href="<?= base_url('/master/sub-units/' . $subUnit['su_id'] . '/edit') ?>" class="icon-btn edit-btn" title="Edit">
                                        <svg viewBox="0 0 24 24"><path d="M3 21h18" /><path d="M14.7 5.3l4 4L8 20H4v-4L14.7 5.3z" /><path d="M13 6l4 4" /></svg>
                                    </a>
                                    <form action="<?= base_url('/master/sub-units/' . $subUnit['su_id']) ?>" method="post" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="icon-btn delete-btn" title="Hapus" onclick="return confirm('Yakin hapus sub unit ini?')">
                                            <svg viewBox="0 0 24 24"><path d="M3 6h18" /><path d="M8 6V4h8v2" /><path d="M6 6l1 14h10l1-14" /><path d="M10 11v6" /><path d="M14 11v6" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Tidak ada data yang sesuai dengan pencarian.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php
            $page = $currentPage;
            $totalPages = $total > 0 ? ceil($total / $perPage) : 1;
            $baseUrl = base_url('/master/sub-units');
            $searchParam = $searchQuery;

            function pageUrl($page, $baseUrl, $searchParam) {
                return $baseUrl . '?page=' . $page . $searchParam;
            }

            function renderPageItem($page, $currentPage, $baseUrl, $searchParam, $label = null, $class = '') {
                $label = $label ?? $page;
                $isActive = ($page === $currentPage);
                $url = pageUrl($page, $baseUrl, $searchParam);
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
                Menampilkan <?= count($subUnits) ?> dari <?= $total ?> data
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>