<?php $this->extend('layouts/main'); ?>
<?php $this->section('content'); ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"></h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Aset (<?= number_format($total) ?> data)</h6>
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown" id="columnDropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="columnToggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-table"></i> Kolom
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="columnToggle" style="min-width:240px;" id="columnPanel">
                        <p class="small text-muted mb-2 fw-semibold">Pilih Kolom:</p>
                        <div id="columnCheckboxes">
                            <?php foreach ($allColumns as $key => $label): ?>
                                <div class="form-check">
                                    <input class="form-check-input column-cb" type="checkbox" value="<?= esc($key) ?>" id="col_<?= esc($key) ?>" <?= in_array($key, $visibleColumns) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="col_<?= esc($key) ?>"><?= esc($label) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="invalid-feedback d-block text-danger small mb-2" id="columnError" style="display:none;"></div>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" class="btn btn-primary btn-sm flex-grow-1" id="applyColumnsBtn">Apply</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="resetColumnsBtn">Reset</button>
                        </div>
                    </div>
                </div>
                <a href="<?= base_url('/master/aset/new') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Single Aset</a>
                <a href="<?= base_url('/master/bulk-aset/new') ?>" class="btn btn-warning btn-sm"><i class="bi bi-layers"></i> Bulk Aset</a>
            </div>
        </div>
        <div class="card-body">
            <form method="get" class="mb-4" id="filterForm">
                <div class="row g-2">
                    <div class="col-md-2">
                        <select name="search_col" class="form-control form-control-sm">
                            <option value="">Semua Kolom</option>
                            <?php foreach ($searchableColumns as $key => $label): ?>
                                <option value="<?= esc($key) ?>" <?= ($searchCol ?? '') === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari..." value="<?= esc($search ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control form-control-sm">
                            <option value="">Semua Status</option>
                            <option value="draft" <?= ($status ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="aktif" <?= ($status ?? '') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="nonaktif" <?= ($status ?? '') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            <option value="hilang" <?= ($status ?? '') === 'hilang' ? 'selected' : '' ?>>Hilang</option>
                            <option value="dihapus" <?= ($status ?? '') === 'dihapus' ? 'selected' : '' ?>>Dihapus</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="input_mode" class="form-control form-control-sm">
                            <option value="">Semua Mode</option>
                            <option value="single" <?= ($inputMode ?? '') === 'single' ? 'selected' : '' ?>>Single</option>
                            <option value="bulk" <?= ($inputMode ?? '') === 'bulk' ? 'selected' : '' ?>>Bulk</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="kategori_id" class="form-control form-control-sm">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($kategoriOptions as $k): ?>
                                <option value="<?= $k['kt_id'] ?>" <?= ($kategoriFilter ?? '') == $k['kt_id'] ? 'selected' : '' ?>><?= esc($k['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-secondary btn-sm w-100" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                    <?php if (!empty($search) || !empty($status) || !empty($kategoriFilter) || !empty($searchCol)): ?>
                        <div class="col-md-12 mt-1">
                            <a href="<?= base_url('/master/aset') ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle"></i> Reset Filter</a>
                        </div>
                    <?php endif; ?>
                    <?php if ($sortCol): ?>
                        <input type="hidden" name="sort" value="<?= esc($sortCol) ?>">
                        <input type="hidden" name="dir" value="<?= esc($sortDir) ?>">
                    <?php endif; ?>
                </div>
            </form>

            <div class="table-responsive table-wrapper">
                <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <?php foreach ($visibleColumns as $col): ?>
                                <?php
                                    $isSorted = ($sortCol === $col);
                                    $nextDir = $isSorted ? ($sortDir === 'asc' ? 'desc' : 'asc') : 'asc';
                                    $arrowIcon = $isSorted ? ($sortDir === 'asc' ? ' <i class="bi bi-caret-up-fill"></i>' : ' <i class="bi bi-caret-down-fill"></i>') : ' <i class="bi bi-caret-up-fill text-muted" style="opacity:0.3"></i>';
                                    $sortParams = array_filter([
                                        'sort' => $col,
                                        'dir' => $nextDir,
                                        'q' => $search ?? null,
                                        'search_col' => $searchCol ?? null,
                                        'status' => $status ?? null,
                                        'input_mode' => $inputMode ?? null,
                                        'kategori_id' => $kategoriFilter ?? null,
                                    ], function($v) { return $v !== null && $v !== ''; });
                                    $sortUrl = base_url('/master/aset') . '?' . http_build_query($sortParams);
                                ?>
                                <th class="sortable-header"><a href="<?= esc($sortUrl) ?>" class="text-decoration-none text-dark"><?= esc($allColumns[$col] ?? $col) ?? '' ?><?= $arrowIcon ?></a></th>
                            <?php endforeach; ?>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): ?>
                            <?php foreach ($records as $row): ?>
                                <tr>
                                    <?php foreach ($visibleColumns as $col): ?>
                                        <td>
                                            <?php if ($col === 'input_mode'): ?>
                                                <?php if (($row['input_mode'] ?? 'single') === 'bulk'): ?>
                                                    <span class="badge bg-warning text-dark">Bulk</span>
                                                    <?php if (!empty($row['batch_id'])): ?>
                                                        <br><small class="text-muted" title="Batch ID"><?= esc(substr($row['batch_id'], 0, 20)) ?></small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge bg-info">Single</span>
                                                <?php endif; ?>
                                            <?php elseif ($col === 'status_aset'): ?>
                                                <?php
                                                    $badgeClass = match($row['status_aset'] ?? 'draft') {
                                                        'aktif' => 'badge-success',
                                                        'draft' => 'badge-secondary',
                                                        'nonaktif' => 'badge-warning',
                                                        'hilang' => 'badge-danger',
                                                        'dihapus' => 'badge-dark',
                                                        default => 'badge-secondary'
                                                    };
                                                ?>
                                                <span class="badge <?= $badgeClass ?>"><?= esc(ucfirst($row['status_aset'] ?? 'draft')) ?></span>
                                            <?php elseif ($col === 'nama_penanggung_jawab'): ?>
                                                <?= esc(format_penanggung_jawab([
                                                    'nama_gelar' => $row['nama_penanggung_jawab'] ?? null,
                                                    'nama_jabatan' => $row['pj_nama_jabatan'] ?? null,
                                                    'pj_nama_jabatan' => $row['pj_nama_jabatan'] ?? null,
                                                    'tgl_lahir' => $row['pj_tgl_lahir'] ?? null,
                                                    'pj_tgl_lahir' => $row['pj_tgl_lahir'] ?? null,
                                                ])) ?>
                                            <?php elseif ($col === 'nilai_perolehan'): ?>
                                                <?= esc($row['nilai_perolehan'] ? 'Rp ' . number_format((float)$row['nilai_perolehan'], 0, ',', '.') : '-') ?>
                                            <?php elseif ($col === 'tahun_perolehan'): ?>
                                                <?= esc($row['tahun_perolehan'] ?? '-') ?>
                                            <?php elseif ($col === 'nomor_aset_baru'): ?>
                                                <?= esc($row['nomor_aset_baru'] ?? '-') ?>
                                            <?php else: ?>
                                                <?= esc($row[$col] ?? '-') ?>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td>
                                        <div class="action-btn-group">
                                            <button type="button" class="icon-btn detail-btn" title="Detail" onclick="showDetail('<?= esc($row['all_id'], 'js') ?>')">
                                                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" /></svg>
                                            </button>
                                            <a href="<?= base_url('/master/aset/' . esc($row['all_id'], 'url') . '/edit') ?>" class="icon-btn edit-btn" title="Edit">
                                                <svg viewBox="0 0 24 24"><path d="M3 21h18" /><path d="M14.7 5.3l4 4L8 20H4v-4L14.7 5.3z" /><path d="M13 6l4 4" /></svg>
                                            </a>
                                            <form action="<?= base_url('/master/aset/' . esc($row['all_id'], 'url')) ?>" method="post" style="display:inline;">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="icon-btn delete-btn" title="Hapus" onclick="return confirm('Yakin nonaktifkan aset ini?')">
                                                    <svg viewBox="0 0 24 24"><path d="M3 6h18" /><path d="M8 6V4h8v2" /><path d="M6 6l1 14h10l1-14" /><path d="M10 11v6" /><path d="M14 11v6" /></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= count($visibleColumns) + 1 ?>" class="text-center text-muted py-4">
                                    <?php if (!empty($search) || !empty($status) || !empty($kategoriFilter) || !empty($inputMode)): ?>
                                        Tidak ada hasil untuk pencarian tersebut.
                                    <?php else: ?>
                                        Belum ada data aset. <a href="<?= base_url('/master/aset/new') ?>">Registrasi aset pertama</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php
            $queryParams = [];
            if (!empty($search)) $queryParams['q'] = $search;
            if (!empty($searchCol)) $queryParams['search_col'] = $searchCol;
            if (!empty($status)) $queryParams['status'] = $status;
            if (!empty($kategoriFilter)) $queryParams['kategori_id'] = $kategoriFilter;
            if (!empty($inputMode)) $queryParams['input_mode'] = $inputMode;
            if (!empty($sortCol)) $queryParams['sort'] = $sortCol;
            if (!empty($sortDir)) $queryParams['dir'] = $sortDir;
            $queryString = !empty($queryParams) ? '&' . http_build_query($queryParams) : '';
            $baseUrl = base_url('/master/aset?page=');
            $window = 2;
            $start = max(1, $currentPage - $window);
            $end = min($totalPages, $currentPage + $window);
            ?>

            <?php if ($totalPages > 1 && $total > 50): ?>
            <nav class="mt-3">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= ($currentPage === 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $baseUrl ?>1<?= $queryString ?>">&laquo;</a>
                    </li>
                    <li class="page-item <?= ($currentPage === 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $baseUrl . max(1, $currentPage - 1) . $queryString ?>">&lsaquo;</a>
                    </li>
                    <?php if ($start > 1): ?>
                        <li class="page-item"><a class="page-link" href="<?= $baseUrl ?>1<?= $queryString ?>">1</a></li>
                        <?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                    <?php endif; ?>
                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?= ($i === $currentPage) ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl . $i . $queryString ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($end < $totalPages): ?>
                        <?php if ($end < $totalPages - 1): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                        <li class="page-item"><a class="page-link" href="<?= $baseUrl . $totalPages . $queryString ?>"><?= $totalPages ?></a></li>
                    <?php endif; ?>
                    <li class="page-item <?= ($currentPage === $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $baseUrl . min($totalPages, $currentPage + 1) . $queryString ?>">&rsaquo;</a>
                    </li>
                    <li class="page-item <?= ($currentPage === $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $baseUrl . $totalPages . $queryString ?>">&raquo;</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>

            <div class="text-center text-muted small">
                Menampilkan <?= count($records) ?> dari <?= $total ?> data
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Detail Aset</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
.sortable-header { cursor: pointer; white-space: nowrap; user-select: none; }
.sortable-header a:hover { text-decoration: underline; }
.table-wrapper { overflow-x: auto; }
.table-wrapper table { min-width: 800px; }
.table-wrapper table th, .table-wrapper table td { white-space: nowrap; }
.table-wrapper table th:last-child, .table-wrapper table td:last-child { position: sticky; right: 0; background: #fff; z-index: 1; }
.table-wrapper table thead th:last-child { background: #f8f9fa; z-index: 2; }
</style>

<script>
function currentUrlWithQuery(params) {
    var url = new URL(window.location.origin + window.location.pathname);
    var existingParams = new URLSearchParams(window.location.search);
    existingParams.delete('page');
    existingParams.forEach(function(value, key) {
        url.searchParams.set(key, value);
    });
    Object.keys(params).forEach(function(key) {
        if (params[key] === null || params[key] === undefined || params[key] === '') {
            url.searchParams.delete(key);
        } else {
            url.searchParams.set(key, params[key]);
        }
    });
    return url.toString();
}

function showDetail(id) {
    var modal = new bootstrap.Modal(document.getElementById('detailModal'));
    modal.show();
    document.getElementById('detailModalBody').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';
    fetch('<?= base_url("/master/aset/") ?>' + encodeURIComponent(id))
        .then(r => r.ok ? r.json() : Promise.reject(r))
        .then(data => {
            var html = '<table class="table table-borderless">';
            html += '<tr><td class="text-muted" style="width:180px;">ID Aset</td><td class="fw-bold"><code>' + escHtml(data.all_id || '-') + '</code></td></tr>';
            html += '<tr><td class="text-muted">ID Aset Baru</td><td>' + escHtml(data.nomor_aset_baru || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">ID Aset Lama</td><td>' + escHtml(data.nomor_aset_lama || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Nama Aset</td><td class="fw-bold">' + escHtml(data.nama_aset || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Kategori</td><td>' + escHtml(data.nama_kategori || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Sub Kategori</td><td>' + escHtml(data.nama_sub_kategori || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Merk</td><td>' + escHtml(data.nama_merk || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Type</td><td>' + escHtml(data.nama_type || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Kondisi</td><td>' + escHtml(data.nama_kondisi || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Sumber Dana</td><td>' + escHtml(data.nama_sumber_dana || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Unit Kerja</td><td>' + escHtml(data.nama_unit || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Sub Unit</td><td>' + escHtml(data.nama_sub_unit || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Lokasi</td><td>' + escHtml((data.nama_gedung || '-') + ' / ' + (data.nama_lantai || '-') + ' / ' + (data.nama_ruangan || '-')) + '</td></tr>';
            html += '<tr><td class="text-muted">Penanggung Jawab</td><td>' + formatPenanggungJawab(data) + '</td></tr>';
            html += '<tr><td class="text-muted">Serial Number</td><td>' + escHtml(data.serial_number || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Tahun Perolehan</td><td>' + escHtml(data.tahun_perolehan || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Tanggal Perolehan</td><td>' + escHtml(data.tanggal_perolehan || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Nilai Perolehan</td><td>' + escHtml(data.nilai_perolehan ? 'Rp ' + Number(data.nilai_perolehan).toLocaleString('id-ID') : '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Status</td><td><span class="badge bg-' + (data.status_aset === 'aktif' ? 'success' : 'secondary') + '">' + escHtml(data.status_aset || 'draft') + '</span></td></tr>';
            html += '<tr><td class="text-muted">Spesifikasi</td><td>' + escHtml(data.spesifikasi || '-').replace(/\n/g, '<br>') + '</td></tr>';
            html += '</table>';
            document.getElementById('detailModalBody').innerHTML = html;
        })
        .catch(err => {
            document.getElementById('detailModalBody').innerHTML = '<div class="alert alert-danger">Gagal memuat: ' + escHtml(err.message || 'Unknown error') + '</div>';
        });
}
function escHtml(str) {
    if (!str) return '';
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
}

function formatTanggalIndonesia(dateStr) {
    if (!dateStr || dateStr === '0000-00-00') return '-';
    var parts = dateStr.split('-');
    if (parts.length !== 3) return dateStr;
    return parts[2] + '-' + parts[1] + '-' + parts[0];
}

function formatPenanggungJawab(data) {
    if (!data) return '-';
    var namaGelar = data.nama_penanggung_jawab || '-';
    if (!namaGelar || namaGelar === '-') return '-';
    var namaJabatan = data.pj_nama_jabatan || null;
    var tglLahir = data.pj_tgl_lahir || null;
    if (namaJabatan) {
        return escHtml(namaGelar) + ' (' + escHtml(namaJabatan) + ')';
    }
    if (tglLahir && tglLahir !== '0000-00-00') {
        return escHtml(namaGelar) + ' (' + formatTanggalIndonesia(tglLahir) + ')';
    }
    return escHtml(namaGelar);
}

function validateColumnSelection() {
    var checked = document.querySelectorAll('.column-cb:checked');
    var error = document.getElementById('columnError');
    error.style.display = 'none';
    if (checked.length < 1) {
        error.textContent = 'Pilih minimal 1 kolom.';
        error.style.display = 'block';
        return false;
    }
    return true;
}

function applyColumns() {
    if (!validateColumnSelection()) {
        return;
    }
    var checked = document.querySelectorAll('.column-cb:checked');
    var columns = Array.from(checked).map(function(el) { return el.value; });
    var form = document.createElement('form');
    form.method = 'GET';
    form.action = '<?= base_url('/master/aset/save_column_preferences') ?>';
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'columns';
    input.value = JSON.stringify(columns);
    form.appendChild(input);
    var csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = 'csrf_test_name';
    csrf.value = '<?= csrf_hash() ?>';
    form.appendChild(csrf);
    document.body.appendChild(form);
    form.submit();
}

function resetColumns() {
    var appliedColumns = <?= json_encode(array_values($visibleColumns)) ?>;
    document.querySelectorAll('.column-cb').forEach(function(cb) {
        cb.checked = appliedColumns.includes(cb.value);
    });
    document.getElementById('columnError').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    var applyBtn = document.getElementById('applyColumnsBtn');
    if (applyBtn) {
        applyBtn.addEventListener('click', applyColumns);
    }

    var resetBtn = document.getElementById('resetColumnsBtn');
    if (resetBtn) {
        resetBtn.addEventListener('click', resetColumns);
    }

    document.querySelectorAll('.sortable-header a').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var href = this.getAttribute('href');
            window.location.href = href;
        });
    });
});
</script>
<?php $this->endSection(); ?>