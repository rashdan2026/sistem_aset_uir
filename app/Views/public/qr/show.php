<?php
/** @var array $record */
$nomorAset  = $record['nomor_aset_baru'] ?? $record['all_id'];
$statusBadge = match (strtolower((string) ($record['status_aset'] ?? ''))) {
    'aktif'    => ['Aktif', '#1f5f4a'],
    'draft'    => ['Draft', '#6b7280'],
    'nonaktif' => ['Nonaktif', '#b89b5e'],
    'hilang'   => ['Hilang', '#d9534f'],
    'dihapus'  => ['Dihapus', '#334155'],
    default    => [ucfirst((string) ($record['status_aset'] ?? '-')), '#6b7280'],
};
$kondisiWarna = !empty($record['kondisi_warna']) ? $record['kondisi_warna'] : '#6c757d';
$kondisiWarna = (strpos($kondisiWarna, '#') !== 0) ? '#' . ltrim($kondisiWarna, '#') : $kondisiWarna;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="theme-color" content="#1f5f4a">
<meta name="description" content="Informasi publik aset Universitas Islam Riau">
<meta name="robots" content="index, follow">
<title><?= esc($title) ?> — Universitas Islam Riau</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --bg: #f5f7f4;
    --surface: #ffffff;
    --border: #e3e8df;
    --primary: #1f5f4a;
    --primary-2: #2f7a5f;
    --primary-soft: #dcefe7;
    --accent: #b89b5e;
    --text: #1f2933;
    --muted: #6b7280;
    --shadow: 0 10px 30px rgba(31, 95, 74, 0.08);
    --shadow-sm: 0 4px 12px rgba(31, 95, 74, 0.06);
    --radius: 16px;
}
* { box-sizing: border-box; }
html, body { margin: 0; padding: 0; }
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: linear-gradient(180deg, #f8faf7 0%, #eef3ed 100%);
    color: var(--text);
    font-size: 14.5px;
    line-height: 1.55;
    min-height: 100vh;
    -webkit-font-smoothing: antialiased;
}
.container { max-width: 880px; margin: 0 auto; padding: 20px 16px 40px; }

.header {
    background: var(--surface);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
    margin-bottom: 18px;
    border: 1px solid rgba(31, 95, 74, 0.08);
}
.header-top {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 18px 22px;
    background: linear-gradient(135deg, #1f5f4a 0%, #2f7a5f 100%);
    color: #fff;
}
.header-top img { width: 52px; height: 52px; object-fit: contain; flex-shrink: 0; background: #fff; border-radius: 10px; padding: 6px; }
.header-top .org { line-height: 1.2; }
.header-top .org .name { font-weight: 800; font-size: 15px; letter-spacing: -0.01em; }
.header-top .org .sub  { font-size: 12px; opacity: 0.85; margin-top: 2px; }
.header-body { padding: 18px 22px; display: flex; justify-content: space-between; align-items: center; gap: 14px; flex-wrap: wrap; }
.header-body h1 { font-size: 18px; margin: 0; font-weight: 700; color: var(--primary); letter-spacing: -0.01em; }
.header-body .id-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.header-body .id-row code {
    background: var(--primary-soft);
    color: var(--primary);
    padding: 5px 12px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 600;
}
.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.02em;
    color: #fff;
}
.badge .dot { width: 6px; height: 6px; border-radius: 50%; background: rgba(255,255,255,0.9); }

.summary-card {
    background: var(--surface);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 22px;
    margin-bottom: 18px;
    border: 1px solid rgba(31, 95, 74, 0.08);
}
.summary-card .label { font-size: 11.5px; text-transform: uppercase; letter-spacing: 1.1px; color: var(--muted); font-weight: 700; margin-bottom: 6px; }
.summary-card .value { font-size: 20px; font-weight: 700; color: var(--text); margin: 0; line-height: 1.35; word-break: break-word; }
.summary-card .sub   { font-size: 13px; color: var(--muted); margin-top: 4px; }

.summary-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-top: 18px;
}
@media (max-width: 540px) { .summary-grid { grid-template-columns: 1fr; } }

.detail-card {
    background: var(--surface);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
    margin-bottom: 18px;
    border: 1px solid rgba(31, 95, 74, 0.08);
}
.detail-card .title {
    padding: 16px 22px;
    border-bottom: 1px solid var(--border);
    background: linear-gradient(180deg, #ffffff 0%, #fbfcfa 100%);
    font-weight: 700;
    color: var(--primary);
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.detail-card .title i { font-style: normal; font-size: 16px; }
.detail-table { width: 100%; border-collapse: collapse; }
.detail-table th, .detail-table td { padding: 13px 22px; vertical-align: top; border-bottom: 1px solid var(--border); }
.detail-table tr:last-child th, .detail-table tr:last-child td { border-bottom: none; }
.detail-table th {
    width: 38%;
    text-align: left;
    font-weight: 600;
    color: var(--muted);
    font-size: 12.5px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    background: #fafcfb;
}
.detail-table td { font-size: 14px; color: var(--text); font-weight: 500; word-break: break-word; }
.detail-table tr:hover td, .detail-table tr:hover th { background: #f8fbf7; }
.kondisi-pill { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 999px; color: #fff; font-size: 12.5px; font-weight: 700; }
.kondisi-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: rgba(255,255,255,0.9); }
.empty-val { color: #b6c0bb; font-style: italic; font-weight: 500; }

.qr-card {
    background: var(--surface);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 18px 22px;
    margin-bottom: 18px;
    display: flex;
    gap: 16px;
    align-items: center;
    border: 1px solid rgba(31, 95, 74, 0.08);
}
.qr-card img { width: 110px; height: 110px; border: 1px solid var(--border); border-radius: 12px; padding: 6px; background: #fff; flex-shrink: 0; }
.qr-card .qr-info { min-width: 0; }
.qr-card .qr-info .ttl { font-size: 12px; color: var(--muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.06em; }
.qr-card .qr-info .lnk { font-size: 12.5px; color: var(--primary); word-break: break-all; font-weight: 500; }
@media (max-width: 540px) { .qr-card { flex-direction: column; align-items: flex-start; } .qr-card img { width: 100%; max-width: 220px; height: auto; aspect-ratio: 1/1; } }

.note-card {
    background: #fdf8f0;
    border: 1px solid #f0e4ca;
    border-radius: 12px;
    padding: 14px 18px;
    font-size: 13px;
    color: #7a5f1a;
    margin-bottom: 18px;
    display: flex;
    gap: 12px;
    align-items: flex-start;
}
.note-card .ic { flex-shrink: 0; font-size: 18px; line-height: 1; }

.footer {
    margin-top: 32px;
    text-align: center;
    color: var(--muted);
    font-size: 12px;
}
.footer .line { opacity: 0.6; margin: 0 8px; }
</style>
</head>
<body>
<div class="container">

    <header class="header">
        <div class="header-top">
            <img src="<?= base_url('/logo.png') ?>" alt="Logo UIR">
            <div class="org">
                <div class="name">Universitas Islam Riau</div>
                <div class="sub">Sistem Informasi Aset — Halaman Publik</div>
            </div>
        </div>
        <div class="header-body">
            <div>
                <h1>Informasi Aset</h1>
                <div class="id-row" style="margin-top:6px;">
                    <code><?= esc($nomorAset) ?></code>
                    <span class="badge" style="background: <?= esc($statusBadge[1]) ?>;">
                        <span class="dot"></span><?= esc($statusBadge[0]) ?>
                    </span>
                </div>
            </div>
            <div style="font-size: 12px; color: var(--muted); text-align: right;">
                <div>Diperbarui: <?= !empty($record['updated_at']) ? esc(date('d/m/Y', strtotime($record['updated_at']))) : '-' ?></div>
                <?php if (!empty($record['tahun_perolehan'])): ?>
                    <div>Tahun Perolehan: <?= esc($record['tahun_perolehan']) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="summary-card">
        <div class="label">Nama Aset</div>
        <p class="value"><?= esc($record['nama_aset'] ?? '-') ?></p>
        <div style="margin-top: 14px;">
            <div class="label">Unit Kerja</div>
            <div class="value" style="font-size: 16px;"><?= esc($record['nama_unit'] ?? '-') ?></div>
        </div>
        <div class="summary-grid">
            <div>
                <div class="label">Kategori</div>
                <div class="value" style="font-size: 15px;"><?= esc($record['nama_kategori'] ?? '-') ?></div>
            </div>
            <div>
                <div class="label">Kondisi</div>
                <div style="margin-top: 4px;">
                    <?php if (!empty($record['nama_kondisi'])): ?>
                        <span class="kondisi-pill" style="background: <?= esc($kondisiWarna) ?>;">
                            <span class="dot"></span><?= esc($record['nama_kondisi']) ?>
                        </span>
                    <?php else: ?>
                        <span class="empty-val">-</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php
    $hasLocation = !empty($record['nama_gedung']) || !empty($record['nama_lantai']) || !empty($record['nama_ruangan']);
    $locationText = trim(($record['nama_gedung'] ?? '') . ' / ' . ($record['nama_lantai'] ?? '') . ' / ' . ($record['nama_ruangan'] ?? ''), ' /');
    ?>

    <section class="detail-card">
        <div class="title"><i>📋</i> Detail Aset</div>
        <table class="detail-table">
            <tr>
                <th>Nama Aset</th>
                <td><?= esc($record['nama_aset'] ?? '-') ?></td>
            </tr>
            <tr>
                <th>Unit Kerja</th>
                <td><?= esc($record['nama_unit'] ?? '-') ?></td>
            </tr>
            <tr>
                <th>Sub Unit</th>
                <td><?= !empty($record['nama_sub_unit']) ? esc($record['nama_sub_unit']) : '<span class="empty-val">-</span>' ?></td>
            </tr>
            <?php if ($hasLocation): ?>
            <tr>
                <th>Ruangan</th>
                <td><?= esc($locationText !== '' ? $locationText : '-') ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <th>Golongan</th>
                <td><?= !empty($record['nama_golongan']) ? esc($record['nama_golongan']) : '<span class="empty-val">-</span>' ?></td>
            </tr>
            <tr>
                <th>Kategori</th>
                <td><?= esc($record['nama_kategori'] ?? '-') ?></td>
            </tr>
            <tr>
                <th>Sub Kategori</th>
                <td><?= !empty($record['nama_sub_kategori']) ? esc($record['nama_sub_kategori']) : '<span class="empty-val">-</span>' ?></td>
            </tr>
            <tr>
                <th>Merk</th>
                <td><?= !empty($record['nama_merk']) ? esc($record['nama_merk']) : '<span class="empty-val">-</span>' ?></td>
            </tr>
            <tr>
                <th>Type</th>
                <td><?= !empty($record['nama_type']) ? esc($record['nama_type']) : '<span class="empty-val">-</span>' ?></td>
            </tr>
            <tr>
                <th>Kondisi</th>
                <td>
                    <?php if (!empty($record['nama_kondisi'])): ?>
                        <span class="kondisi-pill" style="background: <?= esc($kondisiWarna) ?>;">
                            <span class="dot"></span><?= esc($record['nama_kondisi']) ?>
                        </span>
                    <?php else: ?>
                        <span class="empty-val">-</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Sumber Dana</th>
                <td><?= !empty($record['nama_sumber_dana']) ? esc($record['nama_sumber_dana']) : '<span class="empty-val">-</span>' ?></td>
            </tr>
        </table>
    </section>

    <div class="qr-card">
        <img src="<?= esc($qrSrc) ?>" alt="QR Code Aset" loading="lazy" decoding="async">
        <div class="qr-info">
            <div class="ttl">Tautan Publik</div>
            <div class="lnk"><?= esc($publicUrl) ?></div>
        </div>
    </div>

    <div class="note-card">
        <span class="ic">🔒</span>
        <div>
            Halaman ini hanya menampilkan informasi umum yang aman untuk publik.
            Data sensitif seperti nilai perolehan, nomor seri, dan informasi pribadi tidak ditampilkan.
        </div>
    </div>

    <footer class="footer">
        © <?= date('Y') ?> Universitas Islam Riau
        <span class="line">•</span>
        Sistem Informasi Aset
        <span class="line">•</span>
        Halaman Publik
    </footer>

</div>
</body>
</html>
