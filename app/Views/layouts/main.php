<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Aset Universitas Islam Riau">
    <title><?= esc($title ?? 'Sistem Informasi Aset UIR') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --bg: #f5f7f4;
            --surface: #ffffff;
            --surface-2: #f9fbf8;
            --border: #e3e8df;
            --primary: #1f5f4a;
            --primary-2: #2f7a5f;
            --primary-soft: #dcefe7;
            --accent: #b89b5e;
            --text: #1f2933;
            --muted: #6b7280;
            --sidebar-bg: linear-gradient(180deg, #e4f0e7 0%, #dbeadf 100%);
            --sidebar-hover: rgba(255,255,255,0.55);
            --shadow: 0 10px 30px rgba(31,95,74,0.08);
            --shadow-soft: 0 6px 18px rgba(31,95,74,0.06);
            --radius: 18px;
            --radius-sm: 12px;
            --danger: #d9534f;
            --danger-light: #fdf2f2;
            --info: #5ca8b8;
            --info-light: #edf7f9;
            --warning: #b89b5e;
            --warning-light: #fdf8f0;
            --g-primary: var(--primary);
            --g-primary-hover: var(--primary-2);
            --g-primary-light: var(--primary-soft);
            --g-primary-soft: var(--primary-soft);
            --uir-green: var(--primary);
            --uir-green-dark: #174837;
            --uir-green-soft: var(--primary-soft);
            --uir-navy: var(--primary);
            --uir-gold: var(--accent);
            --uir-bg: var(--bg);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(180deg, #f8faf7 0%, #f3f6f2 100%);
            color: var(--text);
            font-size: 14px;
            line-height: 1.6;
        }

        /* ── Navbar ── */
        .navbar-uir {
            background: rgba(255,255,255,0.82) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            height: 64px;
            box-shadow: 0 2px 12px rgba(31,95,74,0.03);
            z-index: 1030;
        }
        .navbar-uir .navbar-brand {
            font-size: 16px;
            font-weight: 800;
            color: var(--primary) !important;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-uir .navbar-brand::before {
            content: '';
            display: none;
        }
        .navbar-uir .navbar-brand-img {
            height: 36px;
            width: auto;
            flex-shrink: 0;
        }
        .navbar-uir .navbar-brand i { display: none; }
        .navbar-uir .nav-link {
            color: #234438 !important;
            font-size: 13.5px;
            font-weight: 600;
            padding: 8px 14px !important;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        .navbar-uir .nav-link:hover { background: rgba(31,95,74,0.06); color: var(--primary) !important; }
        .navbar-uir .btn-outline-light {
            border: 1.5px solid var(--primary);
            color: var(--primary);
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            background: transparent;
        }
        .navbar-uir .btn-outline-light:hover { background: var(--primary); color: #fff; }
        .navbar-uir .dropdown-menu {
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow);
            padding: 8px;
            font-size: 13px;
            margin-top: 8px;
        }
        .navbar-uir .dropdown-item {
            border-radius: 10px;
            padding: 9px 14px;
            color: var(--muted);
            font-weight: 500;
            transition: all 0.15s;
        }
        .navbar-uir .dropdown-item:hover { background: var(--primary-soft); color: var(--primary); }
        .navbar-uir .dropdown-divider { margin: 6px 10px; border-color: var(--border); }

        /* ── Sidebar ── */
        .sidebar {
            background: var(--sidebar-bg) !important;
            box-shadow: none !important;
            min-height: calc(100vh - 64px);
            padding: 18px 12px;
            border-right: 1px solid rgba(31,95,74,0.08);
        }
        .sidebar .position-sticky { padding-top: 0 !important; }
        .sidebar .nav.flex-column { gap: 2px; }
        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            margin: 3px 6px;
            border-radius: 14px;
            color: #234438 !important;
            font-size: 13.5px;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.55);
            transform: translateX(2px);
            color: #0D1F12 !important;
        }
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-2) 100%) !important;
            color: #fff !important;
            font-weight: 700;
            box-shadow: var(--shadow-soft);
            transform: none;
        }
        .sidebar .nav-link i {
            font-size: 16px;
            flex-shrink: 0;
            opacity: 0.7;
            width: 20px;
            text-align: center;
        }
        .sidebar .nav-link.active i { opacity: 1; }
        .sidebar .nav-header {
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: rgba(31,95,74,0.55) !important;
            padding: 10px 14px 4px;
            margin-top: 10px;
        }

        /* ── Cards ── */
        .card {
            border: 1px solid rgba(31,95,74,0.07);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(6px);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(180deg, #ffffff 0%, #fbfcfa 100%);
            border-bottom: 1px solid var(--border);
            padding: 16px 22px;
        }
        .card-header h6 {
            font-size: 14px;
            font-weight: 700;
            color: var(--primary-2);
            letter-spacing: -0.01em;
        }
        .card-body { padding: 20px 22px 24px; }

        /* ── Tables ── */
        .table { margin-bottom: 0; }
        .table-bordered { border: 1px solid var(--border) !important; border-radius: 16px; overflow: hidden; }
        .table-bordered > thead th {
            border-color: transparent !important;
            background: #f3f7f4;
            color: #6a7b73;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.7px;
            padding: 14px 16px;
            white-space: nowrap;
            text-transform: uppercase;
            border-bottom: 1px solid var(--border) !important;
        }
        .table-bordered > tbody td {
            border-color: #edf1ec !important;
            padding: 14px 16px;
            vertical-align: middle;
            font-size: 13.5px;
            color: #334155;
            transition: background-color 0.22s ease;
        }
        .table-bordered > tbody tr:nth-child(even) td { background-color: #fcfdfc; }
        .table-bordered > tbody tr:hover td { background-color: #d4ede0 !important; }

        /* ── Badges ── */
        .badge {
            font-weight: 700;
            font-size: 11.5px;
            padding: 5px 12px;
            border-radius: 10px;
            letter-spacing: 0.01em;
        }
        .badge-success, .badge.bg-success, .badge-primary, .badge.bg-primary {
            background: linear-gradient(135deg, #8fc2b0 0%, #6fa890 100%) !important;
            color: #fff;
            box-shadow: 0 4px 10px rgba(111,168,144,0.22);
        }
        .badge-danger, .badge.bg-danger { background: var(--danger-light) !important; color: var(--danger); }
        .badge-warning, .badge.bg-warning { background: var(--warning-light) !important; color: #7a5f1a; }
        .badge-info, .badge.bg-info { background: var(--info-light) !important; color: #367d8a; }
        .badge-secondary, .badge.bg-secondary { background: #f3f4f6 !important; color: var(--muted); }
        .badge-dark, .badge.bg-dark { background: #334155 !important; color: #fff; }

        /* ── Alerts ── */
        .alert {
            border: none;
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            font-weight: 600;
        }
        .alert-success { background: var(--primary-soft); color: #1f5f4a; }
        .alert-danger { background: var(--danger-light); color: var(--danger); }
        .alert-info { background: var(--info-light); color: #367d8a; }

        /* ── Buttons ── */
        .btn {
            font-weight: 600;
            font-size: 13px;
            border-radius: 10px;
            transition: all 0.2s ease;
            padding: 8px 18px;
            border: 1.5px solid transparent;
        }
        .btn-sm { padding: 6px 14px; font-size: 12.5px; border-radius: 8px; }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-2) 100%);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 4px 12px rgba(31,95,74,0.18);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #174837 0%, var(--primary) 100%);
            box-shadow: 0 6px 16px rgba(31,95,74,0.28);
            transform: translateY(-1px);
            color: #fff;
        }
        .btn-uir {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-2) 100%);
            border-color: transparent;
            color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(31,95,74,0.18);
        }
        .btn-uir:hover { box-shadow: 0 6px 16px rgba(31,95,74,0.28); transform: translateY(-1px); color: #fff; }
        .btn-outline-secondary {
            border-color: var(--border);
            color: var(--muted);
        }
        .btn-outline-secondary:hover { background: #f9fafb; color: var(--text); border-color: #c0c7be; }
        .btn-outline-danger {
            border-color: var(--danger);
            color: var(--danger);
        }
        .btn-outline-danger:hover { background: var(--danger); color: #fff; }
        .btn-danger { background: var(--danger); border-color: var(--danger); }
        .btn-danger:hover { background: #c9302c; border-color: #c9302c; }
        .btn-info { background: var(--info); border-color: var(--info); color: #fff; }
        .btn-info:hover { background: #4a949f; border-color: #4a949f; color: #fff; }
        .btn-warning { background: var(--warning); border-color: var(--warning); color: #fff; }
        .btn-warning:hover { background: #a88b4e; border-color: #a88b4e; color: #fff; }
        .btn-success {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-2) 100%);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 4px 12px rgba(31,95,74,0.18);
        }
        .btn-success:hover { box-shadow: 0 6px 16px rgba(31,95,74,0.28); transform: translateY(-1px); color: #fff; }

        /* ── Icon Action Buttons ── */
        .action-btn-group { display: flex; gap: 6px; align-items: center; }
        .icon-btn {
            width: 34px; height: 34px;
            border: none;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 0;
            flex-shrink: 0;
        }
        .icon-btn:hover { transform: translateY(-1px); }
        .icon-btn.edit-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-2) 100%);
            box-shadow: 0 4px 10px rgba(31,95,74,0.18);
        }
        .icon-btn.edit-btn:hover { box-shadow: 0 6px 14px rgba(31,95,74,0.30); }
        .icon-btn.delete-btn { background: var(--danger); }
        .icon-btn.delete-btn:hover { box-shadow: 0 4px 10px rgba(217,83,79,0.30); }
        .icon-btn.detail-btn {
            background: #9dc8d9;
            box-shadow: 0 4px 10px rgba(157,200,217,0.25);
        }
        .icon-btn.detail-btn:hover { box-shadow: 0 6px 14px rgba(157,200,217,0.40); }
        .icon-btn svg { width: 17px; height: 17px; stroke: #fff; stroke-width: 2; fill: none; }

        /* ── Forms ── */
        .form-control {
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13.5px;
            padding: 10px 14px;
            color: var(--text);
            transition: all 0.2s;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(31,95,74,0.10), 0 2px 8px rgba(0,0,0,0.04);
        }
        .form-control::placeholder { color: var(--muted); }
        .form-select {
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13.5px;
            padding: 10px 14px;
            color: var(--text);
            background: #fff;
        }
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(31,95,74,0.10), 0 2px 8px rgba(0,0,0,0.04);
        }

        /* ── Input Group ── */
        .input-group .btn { border-radius: 10px; font-size: 13px; padding: 9px 16px; }
        .input-group .form-control:not(:last-child) { border-radius: 10px 0 0 10px; }
        .input-group .btn:last-child { border-radius: 0 10px 10px 0; }
        .input-group .btn:nth-child(2):not(:last-child) { border-radius: 0; }

        /* ── Pagination ── */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .pagination .page-link {
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            border: 1px solid #d9e4dc;
            background: #ffffff;
            color: #5b6b63;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(31,95,74,0.04);
            margin: 0;
        }
        .pagination .page-link:hover {
            background: #edf7f1;
            border-color: #b8d1c2;
            color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(31,95,74,0.06);
        }
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-2) 100%);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 6px 16px rgba(31,95,74,0.16);
        }
        .pagination .page-item.active .page-link:hover {
            transform: none;
            box-shadow: 0 6px 16px rgba(31,95,74,0.16);
        }
        .pagination .page-item.disabled .page-link {
            background: #f4f7f5;
            color: #b3beb7;
            border-color: #e3e8df;
            cursor: not-allowed;
            box-shadow: none;
        }
        .pagination .page-item.disabled .page-link:hover {
            transform: none;
            background: #f4f7f5;
            color: #b3beb7;
            box-shadow: none;
        }
        .pagination .page-item.prev .page-link,
        .pagination .page-item.next .page-link {
            background: #ffffff;
            border: 1px solid #d9e4dc;
            color: #5b6b63;
        }
        .pagination .page-item.prev .page-link:hover,
        .pagination .page-item.next .page-link:hover {
            background: #edf7f1;
            border-color: #b8d1c2;
            color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(31,95,74,0.06);
        }
        .pagination .page-item.prev .page-link i,
        .pagination .page-item.next .page-link i {
            color: inherit;
        }
        .pagination .page-item.prev.disabled .page-link,
        .pagination .page-item.next.disabled .page-link {
            background: #f4f7f5;
            color: #b3beb7;
            border-color: #e3e8df;
            cursor: not-allowed;
            box-shadow: none;
        }
        .pagination .page-item.prev.disabled .page-link:hover,
        .pagination .page-item.next.disabled .page-link:hover {
            transform: none;
            background: #f4f7f5;
            color: #b3beb7;
            box-shadow: none;
        }
        .pagination-info {
            text-align: center;
            margin-top: 14px;
            color: #6b7280;
            font-size: 13px;
            font-weight: 500;
        }

        /* ── Modal ── */
        .modal-content {
            border: 1px solid rgba(31,95,74,0.08);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }
        .modal-header {
            border-bottom: 1px solid var(--border);
            padding: 16px 22px;
        }
        .modal-header.bg-primary { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-2) 100%) !important; }
        .modal-header.bg-success { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-2) 100%) !important; }
        .modal-footer {
            border-top: 1px solid var(--border);
            padding: 14px 22px;
        }

        /* ── Main Content ── */
        main { padding-top: 12px !important; }
        main h1, main .h3 {
            font-size: 24px;
            font-weight: 800;
            color: #20362f;
            letter-spacing: -0.4px;
        }
        .text-muted { color: var(--muted) !important; }
        code {
            background: var(--primary-soft);
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 12.5px;
            color: var(--primary);
            font-weight: 600;
        }

        /* ── Misc ── */
        .bg-uir { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-2) 100%) !important; }
        .text-uir { color: var(--primary) !important; }
        .text-primary { color: var(--primary) !important; }
        .text-success { color: #2f7a5f !important; }
        .text-info { color: #367d8a !important; }
        .text-warning { color: #7a5f1a !important; }
        .border-primary { border-color: var(--primary) !important; }
        .border-success { border-color: #6fa890 !important; }
        .border-info { border-color: #9dc8d9 !important; }
        .border-warning { border-color: var(--accent) !important; }
        .shadow { box-shadow: var(--shadow) !important; }
        .shadow-sm { box-shadow: var(--shadow-soft) !important; }
        .flash-messages { position: fixed; top: 76px; right: 20px; z-index: 9999; min-width: 300px; }
        .spinner-border.text-primary { color: var(--primary) !important; }
        .spinner-border.text-success { color: var(--primary-2) !important; }
        hr, .dropdown-divider { border-color: var(--border); }

        @media (max-width: 768px) {
            .navbar-uir { padding: 0 14px; }
            main { padding: 0 14px !important; }
            .card-body { padding: 14px; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light navbar-uir">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('/') ?>">
                <img src="<?= base_url('/logo.png') ?>" alt="Logo UIR" class="navbar-brand-img">
                <span class="ms-2">Sistem Aset UIR</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php if (session()->has('user_id')): ?>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/admin/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Master Data</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('/master/unit-kerja') ?>">Unit Kerja</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('/master/penanggung-jawab') ?>">Penanggung Jawab</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('/master/sub-units') ?>">Sub Unit</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('/master/gedung') ?>">Gedung</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('/master/lantai') ?>">Lantai</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('/master/ruangan') ?>">Ruangan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('/master/kategori') ?>">Kategori</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('/master/sub-kategori') ?>">Sub Kategori</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('/master/golongan') ?>">Golongan</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('/master/merk') ?>">Merk</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('/master/type') ?>">Type</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('/master/kondisi-barang') ?>">Kondisi Barang</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('/master/sumber-dana') ?>">Sumber Dana</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('/master/aset') ?>">Registrasi Aset</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= esc(session('full_name')) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= base_url('/auth/change-password') ?>">Ubah Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="document.getElementById('logout-form').submit(); return false;">Keluar</a></li>
                        </ul>
                    </li>
                </ul>
                <form id="logout-form" action="<?= base_url('/auth/logout') ?>" method="post" style="display:none;"><?= csrf_field() ?></form>
                <?php else: ?>
                <a class="btn btn-outline-light" href="<?= base_url('/auth/login') ?>">Masuk</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <?php if (session()->has('user_id')): ?>
            <nav class="col-md-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/admin/dashboard') ?>">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-header mt-3 mb-1 small text-muted">REFERENSI</li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/master/unit-kerja') ?>">
                                <i class="bi bi-buildings"></i> Unit Kerja
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/master/penanggung-jawab') ?>">
                                <i class="bi bi-person-badge"></i> Penanggung Jawab
                            </a>
                        </li>
                        <li class="nav-header mt-3 mb-1 small text-muted">LOKASI</li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/master/sub-units') ?>">
                                <i class="bi bi-house-door"></i> Sub Unit
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/master/gedung') ?>">
                                <i class="bi bi-building"></i> Gedung
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/master/lantai') ?>">
                                <i class="bi bi-signpost"></i> Lantai
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/master/ruangan') ?>">
                                <i class="bi bi-door-open"></i> Ruangan
                            </a>
                        </li>
                        <li class="nav-header mt-3 mb-1 small text-muted">KLASIFIKASI ASET</li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/master/kategori') ?>">
                                <i class="bi bi-tag"></i> Kategori
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/master/sub-kategori') ?>">
                                <i class="bi bi-tags"></i> Sub Kategori
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/master/golongan') ?>">
                                <i class="bi bi-collection"></i> Golongan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/master/merk') ?>">
                                <i class="bi bi-badge-ad"></i> Merk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/master/type') ?>">
                                <i class="bi bi-speedometer"></i> Type
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/master/kondisi-barang') ?>">
                                <i class="bi bi-exclamation-triangle"></i> Kondisi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/master/sumber-dana') ?>">
                                <i class="bi bi-cash-stack"></i> Sumber Dana
                            </a>
                        </li>
                        <li class="nav-header mt-3 mb-1 small text-muted">REGISTRASI ASET</li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('/master/aset') ?>">
                                <i class="bi bi-box-seam"></i> Registrasi Aset
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <?php endif; ?>
            
            <main class="<?= session()->has('user_id') ? 'col-md-10 ms-sm-auto' : '' ?> px-md-4">
                <?php if(session()->has('error') || session()->has('success')): ?>
                <div class="flash-messages">
                    <?php if(session()->has('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= esc(session('error')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    <?php if(session()->has('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= esc(session('success')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Set CSRF token for all AJAX requests
        $(document).ready(function() {
            $.ajaxSetup({
                beforeSend: function(xhr, settings) {
                    var csrfField = document.querySelector('input[name="<?= csrf_token() ?>"]');
                    if (csrfField) {
                        var csrfValue = csrfField.value;
                        if (!/^(GET|HEAD|OPTIONS)$/i.test(settings.type) && !settings.crossDomain) {
                            xhr.setRequestHeader('X-CSRF-TOKEN', csrfValue);
                        }
                    }
                }
            });
        });

        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>