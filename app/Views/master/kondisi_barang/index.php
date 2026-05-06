<?php $this->extend('layouts/main'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"></h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Kondisi Barang (<?= number_format($total) ?> data)</h6>
            <a href="<?= base_url('/master/kondisi-barang/new') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Kondisi</a>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('/master/kondisi-barang') ?>" class="mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <input type="text" name="q" class="form-control" value="<?= esc($params['q'] ?? '') ?>" placeholder="Cari kode atau nama kondisi...">
                    </div>
                    <div class="col-md-3">
                        <select name="is_active" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="1" <?= ($params['is_active'] ?? '') === '1' ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= ($params['is_active'] ?? '') === '0' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Cari</button>
                        <?php if (!empty(array_filter($params ?? []))): ?>
                            <a href="<?= base_url('/master/kondisi-barang') ?>" class="btn btn-secondary">Reset</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Level</th>
                            <th>Available for Use</th>
                            <th>Status</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): ?>
                            <?php $no = (($currentPage - 1) * $perPage) + 1; foreach ($records as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($row['kode_kondisi']) ?></td>
                                    <td><?= esc($row['nama_kondisi']) ?></td>
                                    <td><?= esc($row['level_kondisi']) ?></td>
                                    <td><?= $row['is_available_for_use'] ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>' ?></td>
                                    <td><?= $row['is_active'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' ?></td>
                                    <td>
                                        <div class="action-btn-group">
                                            <a href="<?= base_url('/master/kondisi-barang/' . $row['kd_id'] . '/edit') ?>" class="icon-btn edit-btn" title="Edit">
                                                <svg viewBox="0 0 24 24"><path d="M3 21h18" /><path d="M14.7 5.3l4 4L8 20H4v-4L14.7 5.3z" /><path d="M13 6l4 4" /></svg>
                                            </a>
                                            <form action="<?= base_url('/master/kondisi-barang/' . $row['kd_id']) ?>" method="post" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="icon-btn delete-btn" title="Hapus" onclick="return confirm('Yakin nonaktifkan?')">
                                                    <svg viewBox="0 0 24 24"><path d="M3 6h18" /><path d="M8 6V4h8v2" /><path d="M6 6l1 14h10l1-14" /><path d="M10 11v6" /><path d="M14 11v6" /></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php
            $totalPages = $total > 0 ? ceil($total / $perPage) : 1;
            $baseUrl = base_url('/master/kondisi-barang');
            $searchParam = $searchQuery;

            function pageUrlKondisi($page, $baseUrl, $searchParam) {
                return $baseUrl . '?page=' . $page . $searchParam;
            }

            function renderPageItemKondisi($page, $currentPage, $baseUrl, $searchParam, $label = null, $class = '') {
                $label = $label ?? $page;
                $isActive = ($page === $currentPage);
                $url = pageUrlKondisi($page, $baseUrl, $searchParam);
                return '<li class="page-item ' . ($isActive ? 'active' : '') . ($class ? ' ' . $class : '') . '">'
                     . '<a class="page-link" href="' . $url . '">' . $label . '</a></li>';
            }
            ?>

            <?php if ($totalPages > 1): ?>
            <nav class="mt-3">
                <ul class="pagination justify-content-center">
                    <?= renderPageItemKondisi(1, $currentPage, $baseUrl, $searchParam, '&laquo;', 'prev') ?>
                    <?= renderPageItemKondisi(max(1, $currentPage - 1), $currentPage, $baseUrl, $searchParam, '&lsaquo;', 'prev') ?>

                    <?php
                    $window = 2;
                    $start = max(1, $currentPage - $window);
                    $end = min($totalPages, $currentPage + $window);

                    if ($start > 1) {
                        echo renderPageItemKondisi(1, $currentPage, $baseUrl, $searchParam);
                        if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }

                    for ($i = $start; $i <= $end; $i++) {
                        echo renderPageItemKondisi($i, $currentPage, $baseUrl, $searchParam);
                    }

                    if ($end < $totalPages) {
                        if ($end < $totalPages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        echo renderPageItemKondisi($totalPages, $currentPage, $baseUrl, $searchParam);
                    }
                    ?>

                    <?= renderPageItemKondisi(min($totalPages, $currentPage + 1), $currentPage, $baseUrl, $searchParam, '&rsaquo;', 'next') ?>
                    <?= renderPageItemKondisi($totalPages, $currentPage, $baseUrl, $searchParam, '&raquo;', 'next') ?>
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