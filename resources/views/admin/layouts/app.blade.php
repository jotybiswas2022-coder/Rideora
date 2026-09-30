<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ setting('site_name', 'Rideora') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.4/dist/sweetalert2.all.min.js" defer></script>

    <style>
        /* ============ Rideora admin design system ============ */
        :root {
            --primary: #2563EB;
            --primary-dark: #1D4ED8;
            --primary-soft: #EFF6FF;
            --dark: #0F172A;
            --sidebar: #111C33;
            --sidebar-hover: #1B2949;
            --light: #F8FAFC;
            --text: #1E293B;
            --muted: #64748B;
            --border: #E2E8F0;
            --success: #16A34A;
            --success-soft: #ECFDF5;
            --warning: #F59E0B;
            --warning-soft: #FFFBEB;
            --danger: #DC2626;
            --danger-soft: #FEF2F2;
            --radius-sm: 8px;
            --radius: 12px;
            --radius-lg: 16px;
            --shadow-sm: 0 1px 2px rgba(15, 23, 42, .06);
            --shadow: 0 8px 26px rgba(15, 23, 42, .08);
            --sidebar-w: 250px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            background: #F1F5F9; color: var(--text); line-height: 1.55; -webkit-font-smoothing: antialiased;
            overflow-x: hidden; overflow-wrap: break-word;
        }

        img, svg, video { max-width: 100%; height: auto; }

        a { color: var(--primary); text-decoration: none; }
        a:hover { color: var(--primary-dark); }
        img { max-width: 100%; display: block; }
        h1, h2, h3, h4 { color: var(--dark); font-weight: 700; line-height: 1.25; }

        /* ============ Icons (Bootstrap Icons) ============ */
        i[class^="bi-"], i[class*=" bi-"] { line-height: 1; }

        /* ============ Layout shell ============ */
        .admin-shell { display: flex; min-height: 100vh; }
        .admin-main { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .admin-content { padding: 26px 28px 48px; flex: 1; }

        /* ============ Topbar ============ */
        .admin-topbar {
            background: #fff; border-bottom: 1px solid var(--border); padding: 0 28px; height: 66px;
            display: flex; align-items: center; justify-content: space-between; gap: 16px; position: sticky; top: 0; z-index: 60;
        }
        .topbar-left { display: flex; align-items: center; gap: 14px; min-width: 0; }
        .topbar-left h1 { font-size: 1.05rem; }
        .topbar-left p { font-size: .78rem; color: var(--muted); }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .sidebar-toggle {
            display: none; width: 40px; height: 40px; border: 1px solid var(--border); background: #fff;
            border-radius: var(--radius-sm); cursor: pointer; font-size: 1.05rem;
        }

        .admin-bell { position: relative; }
        .admin-bell > button {
            width: 40px; height: 40px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: #fff;
            cursor: pointer; position: relative;
        }
        .admin-bell .count {
            position: absolute; top: -6px; right: -6px; min-width: 18px; height: 18px; padding: 0 4px; background: var(--danger);
            color: #fff; border-radius: 999px; font-size: .66rem; font-weight: 700; display: inline-flex;
            align-items: center; justify-content: center; border: 2px solid #fff;
        }
        .admin-bell-panel {
            position: absolute; right: 0; top: calc(100% + 10px); width: 330px; max-width: 90vw; background: #fff;
            border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow); display: none; overflow: hidden;
        }
        .admin-bell-panel.is-open { display: block; }
        .admin-bell-panel .head { padding: 12px 16px; border-bottom: 1px solid var(--border); font-size: .85rem; font-weight: 700; background: #FCFDFF; }
        .admin-bell-panel .list { max-height: 320px; overflow-y: auto; }
        .admin-bell-panel a { display: block; padding: 11px 16px; border-bottom: 1px solid #F1F5F9; color: var(--text); }
        .admin-bell-panel a:last-child { border-bottom: none; }
        .admin-bell-panel a:hover { background: var(--light); }
        .admin-bell-panel strong { font-size: .82rem; display: block; }
        .admin-bell-panel span { font-size: .76rem; color: var(--muted); }
        .admin-bell-panel .empty { padding: 26px 16px; text-align: center; color: var(--muted); font-size: .85rem; }

        .admin-user { display: flex; align-items: center; gap: 10px; padding: 6px 10px 6px 6px; border: 1px solid var(--border); border-radius: 999px; background: #fff; }
        .admin-user .avatar {
            width: 30px; height: 30px; border-radius: 50%; background: var(--primary); color: #fff; display: inline-flex;
            align-items: center; justify-content: center; font-size: .74rem; font-weight: 700; overflow: hidden;
        }
        .admin-user .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .admin-user .who { font-size: .82rem; font-weight: 600; }

        /* ============ Buttons ============ */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 7px; padding: 10px 16px;
            border-radius: var(--radius-sm); border: 1px solid transparent; font-size: .86rem; font-weight: 600;
            font-family: inherit; cursor: pointer; transition: transform .14s ease, background .14s ease, color .14s ease;
            text-decoration: none; line-height: 1.2; white-space: nowrap;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn:disabled { opacity: .55; cursor: not-allowed; transform: none; }
        .btn-primary { background: var(--primary); color: #fff; box-shadow: 0 4px 14px rgba(37, 99, 235, .22); }
        .btn-primary:hover { background: var(--primary-dark); color: #fff; }
        .btn-outline { background: #fff; border-color: var(--border); color: var(--text); }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }
        .btn-success { background: var(--success); color: #fff; }
        .btn-success:hover { background: #15803d; color: #fff; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-danger:hover { background: #b91c1c; color: #fff; }
        .btn-danger-soft { background: var(--danger-soft); color: var(--danger); }
        .btn-warning-soft { background: var(--warning-soft); color: #b45309; }
        .btn-light { background: var(--light); border-color: var(--border); color: var(--text); }
        .btn-sm { padding: 7px 12px; font-size: .79rem; }
        .btn-lg { padding: 13px 22px; font-size: .95rem; }
        .btn-block { width: 100%; }

        /* ============ Cards ============ */
        .card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); }
        .card + .card { margin-top: 22px; }
        .card-head {
            padding: 16px 22px; border-bottom: 1px solid var(--border); display: flex; align-items: center;
            justify-content: space-between; gap: 12px; flex-wrap: wrap;
        }
        .card-head h3 { font-size: 1rem; }
        .card-head p { font-size: .8rem; color: var(--muted); }
        .card-body { padding: 22px; }
        .card-pad { padding: 22px; }
        .card-foot { padding: 15px 22px; border-top: 1px solid var(--border); background: #FCFDFF; border-radius: 0 0 var(--radius-lg) var(--radius-lg); }

        /* ============ Stat cards ============ */
        .stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
        .stat {
            background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 18px;
            display: flex; flex-direction: column; gap: 4px;
        }
        .stat .lbl { font-size: .74rem; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); }
        .stat .val { font-size: 1.65rem; font-weight: 800; color: var(--dark); line-height: 1.15; }
        .stat .note { font-size: .78rem; color: var(--muted); }
        .stat.highlight { background: linear-gradient(140deg, #0F172A, #1E3A8A); border-color: transparent; }
        .stat.highlight .lbl { color: #93C5FD; }
        .stat.highlight .val { color: #fff; }
        .stat.highlight .note { color: #BFDBFE; }

        /* ============ Badges ============ */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 999px; font-size: .73rem; font-weight: 700; white-space: nowrap; }
        .badge-success { background: var(--success-soft); color: #15803d; }
        .badge-warning { background: var(--warning-soft); color: #b45309; }
        .badge-danger { background: var(--danger-soft); color: #b91c1c; }
        .badge-info { background: #E0F2FE; color: #0369A1; }
        .badge-primary { background: var(--primary-soft); color: var(--primary-dark); }
        .badge-muted { background: #F1F5F9; color: var(--muted); }

        /* ============ Forms ============ */
        .form-grid { display: grid; gap: 18px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .form-grid-3 { display: grid; gap: 18px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group.full { grid-column: 1 / -1; }
        label { font-size: .83rem; font-weight: 600; color: var(--dark); }
        .form-control {
            width: 100%; padding: 10px 13px; border: 1px solid var(--border); border-radius: var(--radius-sm);
            font-size: .9rem; font-family: inherit; color: var(--text); background: #fff;
        }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37, 99, 235, .12); }
        .form-control.is-invalid { border-color: var(--danger); }
        textarea.form-control { min-height: 110px; resize: vertical; }
        select.form-control { appearance: none; background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'><path fill='%2364748b' d='M6 8.5 1.5 4h9z'/></svg>"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 32px; }
        .form-hint { font-size: .76rem; color: var(--muted); }
        .form-hint code { background: #F1F5F9; padding: 1px 6px; border-radius: 5px; font-size: .78rem; }
        .icon-field { display: flex; align-items: center; gap: 12px; }
        .icon-preview {
            display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; flex-shrink: 0;
            border-radius: 10px; background: var(--primary-soft); color: var(--primary); font-size: 1.2rem;
        }
        .form-error { font-size: .76rem; color: var(--danger); font-weight: 600; }
        .checkbox-row { display: flex; align-items: flex-start; gap: 10px; font-size: .85rem; }
        .checkbox-row input { width: 17px; height: 17px; margin-top: 2px; accent-color: var(--primary); flex-shrink: 0; }
        .form-actions { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }

        /* ============ Alerts ============ */
        .alert { display: flex; gap: 10px; align-items: flex-start; padding: 12px 15px; border-radius: var(--radius); font-size: .86rem; border: 1px solid transparent; margin-bottom: 18px; }
        .alert-success { background: var(--success-soft); border-color: #BBF7D0; color: #14532D; }
        .alert-error { background: var(--danger-soft); border-color: #FECACA; color: #7F1D1D; }
        .alert-info { background: var(--primary-soft); border-color: #BFDBFE; color: #1E3A8A; }
        .alert-warning { background: var(--warning-soft); border-color: #FDE68A; color: #78350F; }
        .alert ul { margin: 5px 0 0 18px; }

        /* ============ Tables ============ */
        .table-wrap { overflow-x: auto; }
        table.data { width: 100%; border-collapse: collapse; font-size: .86rem; min-width: 780px; }
        table.data th {
            text-align: left; padding: 12px 16px; background: #F8FAFC; color: var(--muted); font-size: .72rem;
            text-transform: uppercase; letter-spacing: .06em; border-bottom: 1px solid var(--border); white-space: nowrap;
        }
        table.data td { padding: 13px 16px; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }
        table.data tbody tr:hover { background: #FCFDFF; }
        table.data .actions { display: flex; gap: 7px; flex-wrap: wrap; }
        .cell-media { display: flex; align-items: center; gap: 12px; }
        .cell-media img { width: 62px; height: 44px; object-fit: cover; border-radius: 8px; background: #EEF2F7; flex-shrink: 0; }
        .cell-media strong { display: block; font-size: .88rem; }
        .cell-media span { font-size: .76rem; color: var(--muted); }

        /* ============ Utilities ============ */
        .grid { display: grid; gap: 20px; }
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid-sidebar { grid-template-columns: 1.6fr 1fr; }
        .flex { display: flex; }
        .flex-between { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
        .flex-center { display: flex; align-items: center; gap: 10px; }
        .flex-wrap { flex-wrap: wrap; }
        .stack-8 { display: flex; flex-direction: column; gap: 8px; }
        .stack-16 { display: flex; flex-direction: column; gap: 16px; }
        .muted { color: var(--muted); }
        .small { font-size: .8rem; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .mt-8 { margin-top: 8px; } .mt-16 { margin-top: 16px; } .mt-24 { margin-top: 24px; }
        .mb-8 { margin-bottom: 8px; } .mb-16 { margin-bottom: 16px; } .mb-24 { margin-bottom: 24px; }
        .divider { height: 1px; background: var(--border); margin: 18px 0; }
        .empty-state { text-align: center; padding: 44px 22px; color: var(--muted); }
        .empty-state .icon { font-size: 2rem; margin-bottom: 8px; }
        .empty-state h3 { margin-bottom: 6px; font-size: 1rem; color: var(--dark); }

        /* ============ Collapsible sections (mobile only) ============ */
        .fold-btn { display: none; }

        .filter-bar { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 18px 22px; margin-bottom: 22px; }
        .filter-bar form { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; align-items: end; }

        .summary-list { display: flex; flex-direction: column; gap: 10px; }
        .summary-row { display: flex; align-items: center; justify-content: space-between; gap: 14px; font-size: .88rem; }
        .summary-row.total { padding-top: 12px; border-top: 1px dashed var(--border); font-weight: 700; font-size: 1rem; color: var(--dark); }

        .pagination-wrap { margin-top: 22px; }
        .pagination-wrap svg { width: 15px; height: 15px; }
        .pagination-wrap nav > div:first-child { display: none; }
        .pagination-wrap span[aria-current="page"] > span,
        .pagination-wrap a {
            display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 11px;
            border: 1px solid var(--border); background: #fff; color: var(--text); font-size: .82rem;
            border-radius: var(--radius-sm); margin: 0 3px;
        }
        .pagination-wrap span[aria-current="page"] > span { background: var(--primary); border-color: var(--primary); color: #fff; font-weight: 700; }
        .pagination-wrap span[aria-disabled="true"] > span { opacity: .45; }

        .bar-chart { display: flex; align-items: flex-end; gap: 14px; height: 190px; padding-top: 12px; }
        .bar-wrap { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 8px; justify-content: flex-end; height: 100%; }
        .bar { width: 100%; max-width: 54px; background: linear-gradient(180deg, #3B82F6, #2563EB); border-radius: 8px 8px 4px 4px; min-height: 4px; }
        .bar-wrap span { font-size: .74rem; color: var(--muted); }
        .bar-wrap strong { font-size: .78rem; color: var(--dark); }

        /* ============ SweetAlert2 theming ============ */
        .swal2-popup {
            border-radius: var(--radius-lg) !important; font-family: inherit !important; padding: 26px 26px 22px !important;
        }
        .swal2-title { font-size: 1.15rem !important; font-weight: 700 !important; color: var(--dark) !important; }
        .swal2-html-container { font-size: .9rem !important; color: var(--muted) !important; }
        .swal2-confirm, .swal2-cancel {
            border-radius: var(--radius-sm) !important; font-family: inherit !important; font-weight: 600 !important;
            font-size: .86rem !important; padding: 10px 20px !important; margin: 0 !important; box-shadow: none !important;
        }
        .swal2-confirm { background: var(--primary) !important; }
        .swal2-confirm.swal2-danger { background: var(--danger) !important; }
        .swal2-cancel { background: #fff !important; color: var(--text) !important; border: 1px solid var(--border) !important; }
        .swal2-icon { width: 3.2em !important; height: 3.2em !important; border-width: 4px !important; margin-bottom: 12px !important; }
        .swal2-toast { border-radius: var(--radius) !important; box-shadow: var(--shadow) !important; font-family: inherit !important; }
        .swal2-toast .swal2-title { font-size: .92rem !important; padding: 14px 18px 14px 56px !important; }
        .swal2-toast .swal2-icon { margin: 0 !important; position: absolute; left: 18px; top: 50%; transform: translateY(-50%); width: 2em !important; height: 2em !important; font-size: 1.5rem !important; border-width: 3px !important; }
        .swal2-backdrop-show { backdrop-filter: blur(2px); }

        /* ============ Responsive ============ */
        @media (max-width: 1150px) {
            .stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 1000px) {
            .grid-sidebar { grid-template-columns: 1fr; }
            .filter-bar form { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .form-grid-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 760px) {
            .admin-content { padding: 18px 16px 40px; }
            .admin-topbar { padding: 0 16px; }
            .sidebar-toggle { display: inline-flex; align-items: center; justify-content: center; }
            .form-grid, .form-grid-3, .filter-bar form { grid-template-columns: 1fr; }
            .admin-user .who { display: none; }
            .card + .card, .card + .grid, .grid + .card, .grid + .grid { margin-top: 14px; }
            .grid { gap: 14px; }
            .card-head { padding: 12px 16px; }
            .card-body, .card-pad { padding: 14px 16px; }
            .card-foot { padding: 11px 16px; }
            .card-head h3 { font-size: .95rem; }

            /* Collapsible sections keep the page short on phones */
            .card.is-folded [data-fold-body] { display: none; }
            .fold-btn {
                display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px;
                border: 1px solid var(--border); background: #fff; border-radius: var(--radius-sm);
                color: var(--muted); cursor: pointer; font-size: .8rem; flex-shrink: 0;
            }
            .fold-btn i { transition: transform .18s ease; }
            .card.is-folded .fold-btn i { transform: rotate(-90deg); }
            .card.is-folded .card-head { border-bottom-color: transparent; }

            /* Data tables become stacked cards so nothing needs sideways scrolling */
            .table-wrap { overflow-x: visible; }
            table.data { min-width: 0; width: 100%; font-size: .84rem; }
            table.data thead { display: none; }
            table.data, table.data tbody, table.data tr, table.data td { display: block; width: 100%; }
            table.data tr {
                background: #fff; border: 1px solid var(--border); border-radius: var(--radius);
                margin-bottom: 10px; padding: 4px 0; box-shadow: var(--shadow-sm);
            }
            table.data tbody tr:hover { background: #fff; }

            /* Label and value share one line instead of stacking, halving the row height */
            table.data td {
                border: none; padding: 5px 14px; font-size: .84rem;
                display: grid; grid-template-columns: minmax(64px, auto) 1fr; gap: 10px; align-items: center;
            }
            table.data td::before {
                content: attr(data-label);
                font-size: .68rem; font-weight: 700; letter-spacing: .06em; line-height: 1.3;
                text-transform: uppercase; color: var(--muted); margin-bottom: 0;
            }
            table.data td:not([data-label])::before { content: none; }
            table.data td[colspan]::before { content: none; }
            table.data tr > td:first-child { font-weight: 700; font-size: .9rem; }
            table.data tr > td:first-child::before { content: none; }
            table.data tr > td:last-child:not(:first-child) { padding-top: 7px; }
            table.data .actions { flex-wrap: wrap; }
            table.data .actions form { flex: 1 1 auto; }
            table.data .actions .btn { width: 100%; }
            table.data .cell-media img { width: 54px; height: 38px; }
        }

        @media (max-width: 620px) {
            .admin-content { padding: 16px 14px 36px; }
            .filter-bar { padding: 16px; }
            .stat-grid { gap: 10px; }
            .stat { padding: 11px 12px; gap: 1px; }
            .stat .val { font-size: 1.25rem; }
            .stat .lbl { font-size: .66rem; letter-spacing: .04em; }
            .stat .note { font-size: .7rem; line-height: 1.3; }
            .filter-bar form { gap: 12px; }
            .pagination-wrap span[aria-current="page"] > span,
            .pagination-wrap a { min-width: 34px; height: 34px; padding: 0 9px; font-size: .8rem; }
            .form-actions { flex-direction: column; align-items: stretch; }
            .form-actions .btn { width: 100%; }
            .bar-chart { gap: 6px; height: 160px; }
            .bar-wrap strong { font-size: .68rem; }
            .bar-wrap span { font-size: .64rem; }
            .bar { max-width: 40px; }
            .empty-state { padding: 26px 14px; }
        }

        @media (max-width: 420px) {
            .admin-topbar { height: 60px; }
            .topbar-right { gap: 8px; }
            .sidebar-toggle, .admin-bell > button { width: 38px; height: 38px; }
            .admin-user { padding: 4px; }
            .badge { font-size: .68rem; padding: 4px 8px; }
            .stat .val { font-size: 1.15rem; }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="admin-shell">
        @include('admin.layouts.sidebar')

        <div class="admin-main">
            @include('admin.layouts.navbar')

            <div class="admin-content">
                @include('admin.partials.flash')
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Sidebar toggle for small screens
            var toggle = document.querySelector('[data-sidebar-toggle]');
            var scrim = document.querySelector('[data-sidebar-scrim]');

            if (toggle) {
                toggle.addEventListener('click', function () {
                    document.body.classList.toggle('sidebar-open');
                });
            }

            function closeSidebar() {
                document.body.classList.remove('sidebar-open');
            }

            if (scrim) {
                scrim.addEventListener('click', closeSidebar);
            }

            // Close the mobile sidebar after navigating
            document.querySelectorAll('.admin-sidebar a, .admin-sidebar button').forEach(function (el) {
                el.addEventListener('click', function () {
                    if (window.innerWidth <= 1000) { closeSidebar(); }
                });
            });

            // Esc closes the sidebar on mobile
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') { closeSidebar(); }
            });

            // Admin notification dropdown
            var bell = document.querySelector('[data-admin-bell]');
            var panel = document.querySelector('[data-admin-bell-panel]');
            if (bell && panel) {
                bell.addEventListener('click', function (event) {
                    event.stopPropagation();
                    panel.classList.toggle('is-open');
                });
                document.addEventListener('click', function () { panel.classList.remove('is-open'); });
            }

            // Confirmation for destructive forms, via SweetAlert2
            document.querySelectorAll('form[data-confirm]').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.dataset.confirmed === '1') { return; }

                    event.preventDefault();

                    var button = form.querySelector('button[type="submit"]');
                    var method = form.querySelector('input[name="_method"]');
                    var destructive = method && method.value.toUpperCase() === 'DELETE';

                    if (typeof window.Swal === 'undefined') {
                        if (window.confirm(form.dataset.confirm)) {
                            form.dataset.confirmed = '1';
                            form.submit();
                        }
                        return;
                    }

                    window.Swal.fire({
                        title: form.dataset.confirmTitle
                            || (button ? button.textContent.trim() : 'Are you sure?'),
                        text: form.dataset.confirm,
                        icon: destructive ? 'error' : 'warning',
                        showCancelButton: true,
                        confirmButtonText: form.dataset.confirmOk
                            || (button ? button.textContent.trim() : 'Yes, continue'),
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        focusCancel: true,
                        customClass: destructive ? { confirmButton: 'swal2-danger' } : {},
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            form.dataset.confirmed = '1';
                            form.submit();
                        }
                    });
                });
            });

            // Status dropdowns that auto-submit ask first, then revert if the admin cancels
            document.querySelectorAll('form[data-confirm-select] select').forEach(function (select) {
                select.dataset.previousValue = select.value;

                select.addEventListener('change', function () {
                    var form = select.closest('form');
                    var chosen = select.options[select.selectedIndex].text.trim();
                    var message = form.dataset.confirmSelect.replace('__value__', chosen);

                    if (typeof window.Swal === 'undefined') {
                        var fallback = window.confirm(message);
                        select.value = fallback ? chosen : select.dataset.previousValue;
                        if (fallback) { form.submit(); }
                        return;
                    }

                    select.value = select.dataset.previousValue;

                    window.Swal.fire({
                        title: form.dataset.confirmTitle || 'Update status?',
                        text: message,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: form.dataset.confirmOk || 'Update',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        focusCancel: true,
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            form.dataset.confirmed = '1';
                            form.submit();
                        }
                    });
                });
            });

            // Page feedback from flash messages, rendered as SweetAlert toasts
            window.addEventListener('load', function () {
                if (typeof window.Swal === 'undefined') { return; }

                var host = document.querySelector('[data-flash-messages]');
                if (!host) { return; }

                var queue = [];

                try {
                    queue = JSON.parse(host.dataset.flashMessages || '[]');
                } catch (error) {
                    queue = [];
                }

                var toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3200,
                    timerProgressBar: true,
                    didOpen: function (element) {
                        element.addEventListener('mouseenter', Swal.stopTimer);
                        element.addEventListener('mouseleave', Swal.resumeTimer);
                    },
                });

                queue.forEach(function (item, index) {
                    setTimeout(function () {
                        toast.fire({ icon: item.icon || 'success', title: item.message });
                    }, index * 420);
                });
            });

            // Collapsible sections: start folded on phones so the page stays short
            var phones = window.matchMedia('(max-width: 760px)');

            document.querySelectorAll('[data-fold]').forEach(function (card) {
                var button = card.querySelector('[data-fold-toggle]');
                if (!button) { return; }

                function setFolded(folded) {
                    card.classList.toggle('is-folded', folded);
                    button.setAttribute('aria-expanded', folded ? 'false' : 'true');
                }

                if (phones.matches && card.hasAttribute('data-fold-mobile')) {
                    setFolded(true);
                }

                button.addEventListener('click', function () {
                    setFolded(!card.classList.contains('is-folded'));
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
