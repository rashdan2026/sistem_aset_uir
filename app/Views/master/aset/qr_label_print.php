<?php
/** @var array $labels */
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= esc($title) ?></title>
<style>
    @page {
        size: 70mm 50mm;
        margin: 0;
    }
    * { box-sizing: border-box; }
    html, body {
        margin: 0;
        padding: 0;
        background: #f0f0f0;
        font-family: Arial, Helvetica, sans-serif;
        color: #000;
    }
    body { padding: 8px; }

    .toolbar {
        max-width: 920px;
        margin: 0 auto 12px;
        padding: 12px 16px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .toolbar .ttl { font-weight: 700; font-size: 14px; color: #1f5f4a; }
    .toolbar .meta { font-size: 12px; color: #6b7280; }
    .toolbar .btns { display: flex; gap: 8px; }
    .btn {
        display: inline-block;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: 1px solid transparent;
    }
    .btn-primary {
        background: linear-gradient(135deg, #1f5f4a 0%, #2f7a5f 100%);
        color: #fff !important;
        box-shadow: 0 3px 10px rgba(31, 95, 74, 0.20);
    }
    .btn-outline {
        background: #fff;
        color: #1f5f4a;
        border-color: #1f5f4a;
    }

    .sheet {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
    }

    /* ============ LABEL 70mm x 50mm ============ */
    .label {
        width: 70mm;
        height: 50mm;
        padding: 2.2mm 3mm;
        background: #fff;
        border: 1px dashed #c8d3cc;
        display: flex;
        flex-direction: column;
        page-break-after: always;
        break-after: page;
        page-break-inside: avoid;
        break-inside: avoid;
        overflow: hidden;
    }
    .label:last-child { page-break-after: auto; break-after: auto; }

    /* Header: logo + identitas institusi */
    .label-header {
        display: flex;
        align-items: center;
        gap: 2mm;
        flex-shrink: 0;
    }
    .label-logo {
        width: 9mm;
        height: 9mm;
        object-fit: contain;
        flex-shrink: 0;
    }
    .label-title { line-height: 1.05; min-width: 0; }
    .label-title .ylpi {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 4.6pt;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: #000;
        margin-bottom: 0.4mm;
        white-space: nowrap;
    }
    .label-title .uir {
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 9.5pt;
        font-weight: 700;
        color: #000;
        line-height: 1;
        white-space: nowrap;
    }

    .label-divider {
        border: none;
        border-top: 0.6mm solid #000;
        margin: 1.2mm 0 1.2mm 0;
        flex-shrink: 0;
    }

    /* Body: info kiri, QR kanan */
    .label-body {
        flex: 1;
        display: flex;
        gap: 2.5mm;
        align-items: stretch;
        overflow: hidden;
        min-height: 0;
    }

    .label-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    /* Nama aset: prioritas utama */
    .aset-name {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 8.5pt;
        font-weight: 800;
        color: #000;
        line-height: 1.2;
        word-break: break-word;
        overflow-wrap: anywhere;
    }

    .info-divider {
        border: none;
        border-top: 0.3mm solid #000;
        margin: 1.1mm 0;
        flex-shrink: 0;
    }

    /* Unit kerja & lokasi: ringkas, lebih kecil dari nama aset */
    .info-line {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 6pt;
        color: #000;
        line-height: 1.25;
        word-break: break-word;
        overflow-wrap: anywhere;
        margin-bottom: 0.6mm;
    }
    .info-line .info-label {
        font-weight: 800;
        letter-spacing: 0.05em;
    }
    .info-line .info-value { font-weight: 400; }

    /* QR Code: 21mm + quiet zone */
    .label-qr-wrap {
        width: 24mm;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .label-qr {
        width: 21mm;
        height: 21mm;
        object-fit: contain;
        display: block;
        image-rendering: pixelated;
        image-rendering: -moz-crisp-edges;
    }

    @media print {
        html, body { background: #fff !important; padding: 0 !important; }
        .toolbar { display: none !important; }
        .sheet { gap: 0; }
        .label {
            border: none !important;
            margin: 0 !important;
        }
        .label-logo {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
</head>
<body>

<div class="toolbar">
    <div>
        <div class="ttl">Cetak Label QR Aset (70mm × 50mm)</div>
        <div class="meta"><?= count($labels) ?> label • Tampilan pratinjau • Klik "Cetak" untuk mencetak</div>
    </div>
    <div class="btns">
        <a href="<?= base_url('/master/aset') ?>" class="btn btn-outline">← Kembali</a>
        <button type="button" class="btn btn-primary" onclick="window.print()">🖨 Cetak</button>
    </div>
</div>

<div class="sheet">
    <?php foreach ($labels as $lbl): ?>
        <div class="label">
            <div class="label-header">
                <img class="label-logo" src="<?= base_url('/logo-bw.png') ?>" alt="Logo Universitas Islam Riau">
                <div class="label-title">
                    <div class="ylpi">YAYASAN LEMBAGA PENDIDIKAN ISLAM RIAU</div>
                    <div class="uir">UNIVERSITAS ISLAM RIAU</div>
                </div>
            </div>
            <hr class="label-divider">
            <div class="label-body">
                <div class="label-info">
                    <div class="aset-name"><?= esc($lbl['asetText']) ?></div>
                    <hr class="info-divider">
                    <div class="info-line">
                        <span class="info-label">UNIT KERJA:</span>
                        <span class="info-value"><?= esc($lbl['unitKerjaText']) ?></span>
                    </div>
                    <div class="info-line">
                        <span class="info-label">LOKASI:</span>
                        <span class="info-value"><?= esc($lbl['lokasiText']) ?></span>
                    </div>
                </div>
                <div class="label-qr-wrap">
                    <img class="label-qr" src="<?= esc($lbl['qrSrc']) ?>" alt="QR Code">
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
