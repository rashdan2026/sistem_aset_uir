<?php $this->extend('layouts/main'); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Referensi Penanggung Jawab</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Penanggung Jawab (<?= number_format($total) ?> data)</h6>
        </div>
        <div class="card-body">
            <form method="get" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control"
                                   placeholder="Cari nama, NPK, email..."
                                   value="<?= esc($search ?? '') ?>">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i> Cari
                            </button>
                            <?php if (!empty($search)): ?>
                                <a href="<?= base_url('/master/penanggung-jawab') ?>" class="btn btn-outline-danger">
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
                            <th width="70">NPK</th>
                            <th>Nama Lengkap</th>
                            <th width="130">Unit Kerja</th>
                            <th width="100">Kategori</th>
                            <th width="80">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($penanggungJawab)): ?>
                        <?php foreach ($penanggungJawab as $pj): ?>
                        <tr>
                            <td><?= esc($pj['npk'] ?? '-') ?></td>
                            <td><?= esc($pj['nama_gelar']) ?></td>
                            <td><?= esc($pj['unit_kerja'] ?? '-') ?></td>
                            <td><?= esc($pj['kategori'] ?? '-') ?></td>
                            <td>
                                <div class="action-btn-group">
                                    <button type="button" class="icon-btn detail-btn" title="Detail"
                                            onclick="showDetail('<?= addcslashes($pj['id_kpe'], "'") ?>')">
                                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <?php if (!empty($search)): ?>
                                    <i class="bi bi-search" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                                    Tidak ada hasil untuk pencarian "<?= esc($search) ?>"
                                <?php else: ?>
                                    <i class="bi bi-inbox" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                                    Belum ada data penanggung jawab.
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
            $baseUrl = base_url('/master/penanggung-jawab?page=');

            function pageUrl($page, $baseUrl, $searchParam) {
                return $baseUrl . $page . $searchParam;
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
                Menampilkan <?= count($penanggungJawab) ?> dari <?= $total ?> data
            </div>

            <div class="mt-3 text-muted small">
                <i class="bi bi-info-circle"></i>
                Data ini diambil dari tabel <code>ylpi_karyawan</code> yang merupakan referensi read-only dari sistem kampus.
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="bi bi-person-badge me-2"></i>Detail Penanggung Jawab
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function showDetail(idKpe) {
    var modalBody = document.getElementById('detailModalBody');
    modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted">Memuat data...</p></div>';

    var modalEl = document.getElementById('detailModal');
    var modal = new bootstrap.Modal(modalEl);
    modal.show();

    var url = '<?= base_url("/master/penanggung-jawab/") ?>' + encodeURIComponent(idKpe);
    console.log('Fetching:', url);

    fetch(url)
        .then(function(response) {
            console.log('Response status:', response.status);
            if (!response.ok) throw new Error('Data tidak ditemukan (status: ' + response.status + ')');
            return response.json();
        })
        .then(function(data) {
            console.log('Data received:', data);
            var html = '<table class="table table-borderless">';
            html += '<tr><td class="text-muted" style="width:160px;">ID KPE</td><td class="fw-bold">' + escHtml(data.id_kpe || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">NPK</td><td class="fw-bold">' + escHtml(data.npk || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Nama Lengkap</td><td class="fw-bold fs-5">' + escHtml(data.nama_gelar || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Jenis Kelamin</td><td>' + escHtml(data.jenkel || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Kategori</td><td><span class="badge bg-secondary">' + escHtml(data.kategori || '-') + '</span></td></tr>';
            html += '<tr><td class="text-muted">Unit Kerja</td><td>' + escHtml(data.unit_kerja || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">Email</td><td>' + escHtml(data.email || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">No. HP</td><td>' + escHtml(data.no_hp1 || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">No. WA</td><td>' + escHtml(data.no_wa || '-') + '</td></tr>';
            html += '</table>';
            modalBody.innerHTML = html;
        })
        .catch(function(err) {
            console.error('Error:', err);
            modalBody.innerHTML = '<div class="alert alert-danger">Gagal memuat data: ' + escHtml(err.message) + '</div>';
        });
}

function escHtml(str) {
    if (str == null) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
</script>
<?php $this->endSection() ?>