<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#1f5f4a">
<meta name="robots" content="noindex">
<title><?= esc($title) ?> — Universitas Islam Riau</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root { --primary: #1f5f4a; --primary-2: #2f7a5f; --muted: #6b7280; --text: #1f2933; --warning: #b89b5e; --warning-light: #fdf8f0; }
* { box-sizing: border-box; }
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: linear-gradient(180deg, #f8faf7 0%, #eef3ed 100%);
    color: var(--text);
    margin: 0; padding: 0;
    min-height: 100vh;
    display: flex; align-items: center; justify-content: center;
}
.card-outer { padding: 22px; max-width: 460px; width: 100%; }
.card {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(31,95,74,0.08);
    border: 1px solid rgba(31,95,74,0.08);
    text-align: center;
    padding: 36px 28px 28px;
}
.logo { width: 72px; height: 72px; object-fit: contain; background: #fff; border-radius: 14px; padding: 6px; box-shadow: 0 4px 14px rgba(31,95,74,0.10); margin-bottom: 16px; }
.icon-circle {
    width: 78px; height: 78px;
    margin: 0 auto 16px;
    border-radius: 50%;
    background: var(--warning-light);
    color: var(--warning);
    display: flex; align-items: center; justify-content: center;
    font-size: 34px; font-weight: 800;
}
h1 { font-size: 19px; color: var(--primary); margin: 0 0 10px; font-weight: 700; letter-spacing: -0.01em; }
p { font-size: 14px; color: var(--muted); margin: 0 0 18px; line-height: 1.6; }
.footer { margin-top: 20px; font-size: 12px; color: var(--muted); }
.btn {
    display: inline-block;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-2) 100%);
    color: #fff !important;
    padding: 10px 22px;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    font-size: 13.5px;
    box-shadow: 0 4px 12px rgba(31,95,74,0.18);
}
</style>
</head>
<body>
<div class="card-outer">
    <div class="card">
        <img src="<?= base_url('/logo.png') ?>" alt="Logo UIR" class="logo">
        <div class="icon-circle">!</div>
        <h1>Aset Tidak Tersedia untuk Publik</h1>
        <p><?= esc($message ?? 'Aset ini tidak dapat ditampilkan kepada publik untuk saat ini.') ?></p>
        <a class="btn" href="<?= base_url('/') ?>">Ke Beranda</a>
        <div class="footer">© <?= date('Y') ?> Universitas Islam Riau — Sistem Informasi Aset</div>
    </div>
</div>
</body>
</html>
