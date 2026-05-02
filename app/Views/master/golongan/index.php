<?php $this->extend('layouts/main'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"></h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Golongan (<?= number_format($total) ?> data)</h6>
            <a href="<?= base_url('/master/golongan/new') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Golongan</a>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('/master/golongan') ?>" class="mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <input type="text" name="q" class="form-control" value="<?= esc($params['q'] ?? '') ?>" placeholder="Cari kode atau nama golongan...">
                    </div>
                    <div class="col-md-3">
                        <select name="kelompok" class="form-select">
                            <option value="">Semua Kelompok</option>
                            <?php foreach ($filterOptions['kelompok'] as $opt): ?>
                                <option value="<?= esc($opt['value']) ?>" <?= ($params['kelompok'] ?? '') == $opt['value'] ? 'selected' : '' ?>><?= esc($opt['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
                        <?php if (!empty(array_filter($params ?? []))): ?>
                            <a href="<?= base_url('/master/golongan') ?>" class="btn btn-secondary">Reset</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Golongan</th>
                            <th>Kelompok</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): ?>
                        <?php foreach ($records as $r): ?>
                        <tr>
                            <td><?= esc($r['kode_golongan']) ?></td>
                            <td><?= esc($r['nama_golongan']) ?></td>
                            <td><?= esc($r['kelompok'] ?? '-') ?></td>
                            <td><?= esc($r['keterangan'] ?? '-') ?></td>
                            <td>
                                <?php if ($r['is_active']): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btn-group">
                                    <a href="<?= base_url('/master/golongan/' . $r['gl_id'] . '/edit') ?>" class="icon-btn edit-btn" title="Edit">
                                        <svg viewBox="0 0 24 24"><path d="M3 21h18" /><path d="M14.7 5.3l4 4L8 20H4v-4L14.7 5.3z" /><path d="M13 6l4 4" /></svg>
                                    </a>
                                    <form action="<?= base_url('/master/golongan/' . $r['gl_id']) ?>" method="post" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="icon-btn delete-btn" title="Hapus" onclick="return confirm('Yakin hapus golongan ini?')">
                                            <svg viewBox="0 0 24 24"><path d="M3 6h18" /><path d="M8 6V4h8v2" /><path d="M6 6l1 14h10l1-14" /><path d="M10 11v6" /><path d="M14 11v6" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data golongan.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php
            $totalPages = $total > 0 ? ceil($total / $perPage) : 1;
            $baseUrl = base_url('/master/golongan');
            $searchParam = $searchQuery;

            function pageUrlGolongan($page, $baseUrl, $searchParam) {
                return $baseUrl . '?page=' . $page . $searchParam;
            }

            function renderPageItemGolongan($page, $currentPage, $baseUrl, $searchParam, $label = null, $class = '') {
                $label = $label ?? $page;
                $isActive = ($page === $currentPage);
                $url = pageUrlGolongan($page, $baseUrl, $searchParam);
                return '<li class="page-item ' . ($isActive ? 'active' : '') . ($class ? ' ' . $class : '') . '">'
                     . '<a class="page-link" href="' . $url . '">' . $label . '</a></li>';
            }
            ?>

            <?php if ($totalPages > 1): ?>
            <nav class="mt-3">
                <ul class="pagination justify-content-center">
                    <?= renderPageItemGolongan(1, $currentPage, $baseUrl, $searchParam, '&laquo;', 'prev') ?>
                    <?= renderPageItemGolongan(max(1, $currentPage - 1), $currentPage, $baseUrl, $searchParam, '&lsaquo;', 'prev') ?>

                    <?php
                    $window = 2;
                    $start = max(1, $currentPage - $window);
                    $end = min($totalPages, $currentPage + $window);

                    if ($start > 1) {
                        echo renderPageItemGolongan(1, $currentPage, $baseUrl, $searchParam);
                        if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }

                    for ($i = $start; $i <= $end; $i++) {
                        echo renderPageItemGolongan($i, $currentPage, $baseUrl, $searchParam);
                    }

                    if ($end < $totalPages) {
                        if ($end < $totalPages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        echo renderPageItemGolongan($totalPages, $currentPage, $baseUrl, $searchParam);
                    }
                    ?>

                    <?= renderPageItemGolongan(min($totalPages, $currentPage + 1), $currentPage, $baseUrl, $searchParam, '&rsaquo;', 'next') ?>
                    <?= renderPageItemGolongan($totalPages, $currentPage, $baseUrl, $searchParam, '&raquo;', 'next') ?>
                </ul>
            </nav>
            <?php endif; ?>

            <div class="text-center text-muted small">
                Menampilkan <?= count($records) ?> dari <?= $total ?> data
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>