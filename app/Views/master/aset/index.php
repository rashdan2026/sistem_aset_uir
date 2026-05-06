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
            <a href="<?= base_url('/master/aset/new') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Registrasi Aset Baru</a>
        </div>
        <div class="card-body">
            <form method="get" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="q" class="form-control" placeholder="Cari nama, ID, nomor aset..." value="<?= esc($search ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="draft" <?= ($status ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="aktif" <?= ($status ?? '') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="nonaktif" <?= ($status ?? '') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            <option value="hilang" <?= ($status ?? '') === 'hilang' ? 'selected' : '' ?>>Hilang</option>
                            <option value="dihapus" <?= ($status ?? '') === 'dihapus' ? 'selected' : '' ?>>Dihapus</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="kategori_id" class="form-control">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($kategoriOptions as $k): ?>
                                <option value="<?= $k['kt_id'] ?>" <?= ($kategoriFilter ?? '') == $k['kt_id'] ? 'selected' : '' ?>><?= esc($k['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-secondary" type="submit">Filter</button>
                        <?php if (!empty($search) || !empty($status) || !empty($kategoriFilter)): ?>
                            <a href="<?= base_url('/master/aset') ?>" class="btn btn-outline-danger">Reset</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No Aset Baru</th>
                            <th>Nama Aset</th>
                            <th>Kategori</th>
                            <th>Sub Kategori</th>
                            <th>Kondisi</th>
                            <th>Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): ?>
                            <?php foreach ($records as $row): ?>
                                <tr>
                                    <td><?= esc($row['nomor_aset_baru'] ?? '-') ?></td>
                                    <td><?= esc($row['nama_aset']) ?></td>
                                    <td><?= esc($row['nama_kategori'] ?? '-') ?></td>
                                    <td><?= esc($row['nama_sub_kategori'] ?? '-') ?></td>
                                    <td><?= esc($row['nama_kondisi'] ?? '-') ?></td>
                                    <td>
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
                                    </td>
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
                                <td colspan="7" class="text-center text-muted py-4">
                                    <?php if (!empty($search) || !empty($status) || !empty($kategoriFilter)): ?>
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
            $baseUrl = base_url('/master/aset?page=');
            $searchParam = '';
            if (!empty($search)) $searchParam .= '&q=' . urlencode($search);
            if (!empty($status)) $searchParam .= '&status=' . urlencode($status);
            if (!empty($kategoriFilter)) $searchParam .= '&kategori_id=' . urlencode($kategoriFilter);
            $window = 2;
            $start = max(1, $currentPage - $window);
            $end = min($totalPages, $currentPage + $window);
            ?>

            <?php if ($totalPages > 1): ?>
            <nav class="mt-3">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= ($currentPage === 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $baseUrl ?>1<?= $searchParam ?>">&laquo;</a>
                    </li>
                    <li class="page-item <?= ($currentPage === 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $baseUrl . max(1, $currentPage - 1) . $searchParam ?>">&lsaquo;</a>
                    </li>
                    <?php if ($start > 1): ?>
                        <li class="page-item"><a class="page-link" href="<?= $baseUrl ?>1<?= $searchParam ?>">1</a></li>
                        <?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                    <?php endif; ?>
                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?= ($i === $currentPage) ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $baseUrl . $i . $searchParam ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($end < $totalPages): ?>
                        <?php if ($end < $totalPages - 1): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?>
                        <li class="page-item"><a class="page-link" href="<?= $baseUrl . $totalPages . $searchParam ?>"><?= $totalPages ?></a></li>
                    <?php endif; ?>
                    <li class="page-item <?= ($currentPage === $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $baseUrl . min($totalPages, $currentPage + 1) . $searchParam ?>">&rsaquo;</a>
                    </li>
                    <li class="page-item <?= ($currentPage === $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $baseUrl . $totalPages . $searchParam ?>">&raquo;</a>
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

<script>
function showDetail(id) {
    var modal = new bootstrap.Modal(document.getElementById('detailModal'));
    modal.show();
    document.getElementById('detailModalBody').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';
    fetch('<?= base_url("/master/aset/") ?>' + encodeURIComponent(id))
        .then(r => r.ok ? r.json() : Promise.reject(r))
        .then(data => {
            var html = '<table class="table table-borderless">';
            html += '<tr><td class="text-muted" style="width:180px;">ID Aset</td><td class="fw-bold"><code>' + escHtml(data.all_id || '-') + '</code></td></tr>';
            html += '<tr><td class="text-muted">No Aset Baru</td><td>' + escHtml(data.nomor_aset_baru || '-') + '</td></tr>';
            html += '<tr><td class="text-muted">No Aset Lama</td><td>' + escHtml(data.nomor_aset_lama || '-') + '</td></tr>';
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
            html += '<tr><td class="text-muted">Penanggung Jawab</td><td>' + escHtml(data.nama_penanggung_jawab || '-') + '</td></tr>';
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
</script>
<?php $this->endSection(); ?>