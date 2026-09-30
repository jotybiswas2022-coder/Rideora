<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ setting('site_name', 'Rideora') }} — {{ setting('site_tagline') }} Reliable vehicles, flexible rentals, simple booking.">
    <title>@yield('title', setting('site_name', 'Rideora').' — '.setting('site_tagline', 'Your Ride, Your Way.'))</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* ============ Rideora — shared design system ============ */
        :root {
            --primary: #2563EB;
            --primary-dark: #1D4ED8;
            --primary-soft: #EFF6FF;
            --dark: #0F172A;
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
            --radius-lg: 18px;
            --shadow-sm: 0 1px 2px rgba(15, 23, 42, .06);
            --shadow: 0 6px 24px rgba(15, 23, 42, .07);
            --shadow-lg: 0 18px 40px rgba(15, 23, 42, .12);
            --container: 1180px;
            --ease-out: cubic-bezier(.22, .61, .36, 1);
            --ease-spring: cubic-bezier(.34, 1.4, .64, 1);
            --dur-fast: .16s;
            --dur: .24s;
            --dur-slow: .55s;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* The header is sticky, so keep anchored/scrolled targets clear of it. */
        html { scroll-padding-top: 92px; -webkit-text-size-adjust: 100%; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            background: var(--light);
            color: var(--text);
            line-height: 1.55;
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            overflow-wrap: break-word;
        }

        /* Keep images and media from blowing out narrow layouts */
        img, svg, video { max-width: 100%; height: auto; }
        table { border-collapse: collapse; }

        a { color: var(--primary); text-decoration: none; }
        a:hover { color: var(--primary-dark); }
        img { max-width: 100%; display: block; }
        h1, h2, h3, h4 { color: var(--dark); line-height: 1.25; font-weight: 700; }

    /* ============ Icons (Bootstrap Icons) ============ */
    i[class^="bi-"], i[class*=" bi-"] { line-height: 1; }
    .ico, .notif-ico, .contact-ico, .v-card-specs li span, .v-card-location span { line-height: 1; display: inline-flex; }

        .container { width: 100%; max-width: var(--container); margin: 0 auto; padding: 0 20px; }
        .page-shell { flex: 1; padding: 32px 0 64px; }
        /* Full-bleed pages (e.g. the home hero) sit flush against the sticky navbar. */
        .page-shell.is-flush { padding-top: 0; }
        .section { padding: 56px 0; }
        .section-tight { padding: 32px 0; }

        .section-head { max-width: 640px; margin-bottom: 32px; }
        .section-head h2 { font-size: 1.85rem; margin-bottom: 8px; }
        .section-head p { color: var(--muted); }
        .eyebrow {
            display: inline-block; font-size: .72rem; font-weight: 700; letter-spacing: .12em;
            text-transform: uppercase; color: var(--primary); background: var(--primary-soft);
            padding: 4px 10px; border-radius: 999px; margin-bottom: 10px;
        }

        /* ============ Buttons ============ */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 11px 20px; border-radius: var(--radius-sm); border: 1px solid transparent;
            font-size: .92rem; font-weight: 600; font-family: inherit; cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease, color .15s ease;
            text-decoration: none; line-height: 1.2; white-space: nowrap;
        }
        .btn:hover { transform: translateY(-1px); }
        .btn:disabled, .btn.disabled { opacity: .55; cursor: not-allowed; transform: none; }
        .btn-primary { background: var(--primary); color: #fff; box-shadow: 0 6px 16px rgba(37, 99, 235, .22); }
        .btn-primary:hover { background: var(--primary-dark); color: #fff; }
        .btn-dark { background: var(--dark); color: #fff; }
        .btn-dark:hover { background: #1e293b; color: #fff; }
        .btn-outline { background: #fff; border-color: var(--border); color: var(--text); }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }
        .btn-success { background: var(--success); color: #fff; }
        .btn-success:hover { background: #15803d; color: #fff; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-danger:hover { background: #b91c1c; color: #fff; }
        .btn-danger-soft { background: var(--danger-soft); color: var(--danger); }
        .btn-light { background: var(--light); color: var(--text); border-color: var(--border); }
        .btn-sm { padding: 7px 13px; font-size: .82rem; }
        .btn-lg { padding: 14px 26px; font-size: 1rem; }
        .btn-block { width: 100%; }

        /* ============ Cards ============ */
        .card {
            background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
        }
        .card-pad { padding: 24px; }
        .card-head { padding: 18px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .card-head h3 { font-size: 1.05rem; }
        .card-head p { color: var(--muted); font-size: .85rem; }
        .card-body { padding: 24px; }
        .card-foot { padding: 16px 24px; border-top: 1px solid var(--border); background: #fcfdff; border-radius: 0 0 var(--radius-lg) var(--radius-lg); }

        /* ============ Vehicle card (shared by home + listing) ============ */
        .v-card {
            position: relative;
            background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden;
            display: flex; flex-direction: column; height: 100%;
            transition: transform .22s var(--ease-out, ease), box-shadow .22s var(--ease-out, ease), border-color .22s ease;
        }
        .v-card:hover { transform: translateY(-4px); border-color: #CBD5E1; box-shadow: var(--shadow-lg); }
        .v-card:focus-within { border-color: var(--primary); }
        @supports selector(:has(*)) {
            .v-card:has(a:focus-visible) { outline: 2px solid var(--primary); outline-offset: 2px; }
        }
        /* The title link covers the whole card, so the image and specs stay one tap target */
        .v-card-title h3 a::after { content: ''; position: absolute; inset: 0; z-index: 1; }
        .v-card-cta { position: relative; z-index: 2; }

        .v-card-media { position: relative; aspect-ratio: 16 / 10; background: #EEF2F7; overflow: hidden; display: block; }
        .v-card-media img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s var(--ease-out, ease); }
        .v-card:hover .v-card-media img { transform: scale(1.06); }
        .v-card-badge {
            position: absolute; top: 12px; left: 12px; color: #fff;
            font-size: .7rem; font-weight: 700; padding: 5px 11px; border-radius: 999px; letter-spacing: .02em;
            display: inline-flex; align-items: center; gap: 5px;
        }
        .v-card-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
        .v-card-badge.is-open { background: rgba(22, 163, 74, .95); }
        .v-card-badge.is-busy { background: rgba(180, 83, 9, .95); }
        .v-card-tag {
            position: absolute; top: 12px; right: 12px; background: rgba(15, 23, 42, .74); color: #fff;
            font-size: .7rem; font-weight: 600; padding: 5px 11px; border-radius: 999px; backdrop-filter: blur(6px);
        }
        .v-card-zoom {
            position: absolute; right: 12px; bottom: 12px; width: 34px; height: 34px; border-radius: 50%;
            background: rgba(255, 255, 255, .94); color: var(--primary); display: inline-flex; align-items: center;
            justify-content: center; font-size: .9rem; box-shadow: var(--shadow-sm);
            opacity: 0; transform: translateY(6px); transition: opacity .22s ease, transform .22s ease;
        }
        .v-card:hover .v-card-zoom, .v-card:focus-within .v-card-zoom { opacity: 1; transform: none; }

        .v-card-body { padding: 18px; display: flex; flex-direction: column; gap: 11px; flex: 1; }
        .v-card-title { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
        .v-card-title h3 { font-size: 1.03rem; line-height: 1.3; }
        .v-card-title h3 a { color: var(--dark); }
        .v-card-title h3 a:hover { color: var(--primary); }
        .v-card-sub { font-size: .82rem; color: var(--muted); margin-top: -6px; }
        .v-card-rating { display: inline-flex; align-items: center; gap: 4px; font-size: .78rem; color: var(--muted); flex-shrink: 0; }
        .v-card-rating .num { font-weight: 700; color: var(--text); font-variant-numeric: tabular-nums; }
        .v-card-specs { list-style: none; display: flex; flex-wrap: wrap; gap: 7px; }
        .v-card-specs li {
            display: inline-flex; align-items: center; gap: 6px; background: var(--light); border: 1px solid var(--border);
            padding: 5px 10px; border-radius: 999px; font-size: .76rem; color: var(--muted); white-space: nowrap;
        }
        .v-card-location { font-size: .8rem; color: var(--muted); display: flex; align-items: center; gap: 6px; }
        .v-card-foot {
            margin-top: auto; padding-top: 14px; border-top: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
        }
        .v-card-price strong { font-size: 1.15rem; color: var(--dark); font-variant-numeric: tabular-nums; }
        .v-card-price .per { font-size: .78rem; color: var(--muted); }
        .v-card-price .alt { display: block; font-size: .76rem; color: var(--muted); margin-top: 2px; font-variant-numeric: tabular-nums; }

        /* ============ Badges ============ */
        .badge {
            display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 999px;
            font-size: .74rem; font-weight: 700; letter-spacing: .01em; white-space: nowrap;
        }
        .badge-success { background: var(--success-soft); color: #15803d; }
        .badge-warning { background: var(--warning-soft); color: #b45309; }
        .badge-danger { background: var(--danger-soft); color: #b91c1c; }
        .badge-info { background: #E0F2FE; color: #0369A1; }
        .badge-primary { background: var(--primary-soft); color: var(--primary-dark); }
        .badge-muted { background: #F1F5F9; color: var(--muted); }

        .stars { color: #F59E0B; letter-spacing: 1px; font-size: .95rem; }

        /* ============ Forms ============ */
        .form-grid { display: grid; gap: 18px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .form-grid-3 { display: grid; gap: 18px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group.full { grid-column: 1 / -1; }
        label { font-size: .85rem; font-weight: 600; color: var(--dark); }
        .form-control {
            width: 100%; padding: 11px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm);
            font-size: .92rem; font-family: inherit; color: var(--text); background: #fff;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37, 99, 235, .12); }
        .form-control.is-invalid { border-color: var(--danger); }
        textarea.form-control { min-height: 110px; resize: vertical; }
        select.form-control { appearance: none; background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'><path fill='%2364748b' d='M6 8.5 1.5 4h9z'/></svg>"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 34px; }
        .form-hint { font-size: .78rem; color: var(--muted); }
        .form-error { font-size: .78rem; color: var(--danger); font-weight: 600; }
        /* iOS Safari zooms the whole viewport when focusing any input under 16px */
        @media (pointer: coarse) {
            .form-control { font-size: 16px; }
        }
        .checkbox-row { display: flex; align-items: flex-start; gap: 10px; font-size: .85rem; color: var(--text); }
        .checkbox-row input { width: 17px; height: 17px; margin-top: 2px; accent-color: var(--primary); flex-shrink: 0; }
        .form-actions { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }

        /* ============ Alerts ============ */
        .alert {
            display: flex; gap: 10px; align-items: flex-start; padding: 13px 16px; border-radius: var(--radius);
            font-size: .88rem; border: 1px solid transparent; margin-bottom: 18px;
        }
        .alert-success { background: var(--success-soft); border-color: #BBF7D0; color: #14532D; }
        .alert-error { background: var(--danger-soft); border-color: #FECACA; color: #7F1D1D; }
        .alert-info { background: var(--primary-soft); border-color: #BFDBFE; color: #1E3A8A; }
        .alert-warning { background: var(--warning-soft); border-color: #FDE68A; color: #78350F; }
        .alert ul { margin: 6px 0 0 18px; }

        /* ============ Tables ============ */
        .table-wrap { overflow-x: auto; }
        table.data { width: 100%; border-collapse: collapse; font-size: .88rem; min-width: 720px; }
        table.data th {
            text-align: left; padding: 12px 16px; background: #F8FAFC; color: var(--muted);
            font-size: .74rem; text-transform: uppercase; letter-spacing: .06em; border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        table.data td { padding: 13px 16px; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }
        table.data tbody tr:hover { background: #FCFDFF; }
        table.data .row-actions { display: flex; gap: 8px; flex-wrap: wrap; }

        /* ============ Layout helpers ============ */
        .grid { display: grid; gap: 22px; }
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .flex { display: flex; }
        .flex-between { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
        .flex-center { display: flex; align-items: center; gap: 10px; }
        .flex-wrap { flex-wrap: wrap; }
        .stack-8 { display: flex; flex-direction: column; gap: 8px; }
        .stack-16 { display: flex; flex-direction: column; gap: 16px; }
        .muted { color: var(--muted); }
        .small { font-size: .82rem; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .mt-8 { margin-top: 8px; } .mt-16 { margin-top: 16px; } .mt-24 { margin-top: 24px; } .mt-32 { margin-top: 32px; }
        .mb-8 { margin-bottom: 8px; } .mb-16 { margin-bottom: 16px; } .mb-24 { margin-bottom: 24px; }
        .divider { height: 1px; background: var(--border); margin: 18px 0; }

        /* Visually hidden but announced by screen readers */
        .sr-only {
            position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
            overflow: hidden; clip: rect(0 0 0 0); clip-path: inset(50%); white-space: nowrap; border: 0;
        }

        /* Keyboard-only focus ring — never removed, only scoped to :focus-visible */
        :focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
            border-radius: 4px;
        }
        .btn:focus-visible, .form-control:focus-visible { outline-offset: 3px; }

        .page-header { margin-bottom: 26px; }
        .page-header h1 { font-size: 1.6rem; margin-bottom: 4px; }
        .page-header p { color: var(--muted); font-size: .92rem; }

        .empty-state { text-align: center; padding: 48px 24px; color: var(--muted); }
        .empty-state .icon { font-size: 2.2rem; margin-bottom: 10px; }
        .empty-state h3 { margin-bottom: 6px; color: var(--dark); font-size: 1.05rem; }

        /* ============ Pagination ============ */
        .pagination-wrap { margin-top: 26px; display: flex; justify-content: center; }
        .pagination-wrap svg { width: 16px; height: 16px; }
        .pagination-wrap nav > div:first-child { display: none; }
        .pagination-wrap span[aria-current="page"] > span,
        .pagination-wrap a {
            display: inline-flex; align-items: center; justify-content: center; min-width: 38px; height: 38px;
            padding: 0 12px; border: 1px solid var(--border); background: #fff; color: var(--text);
            font-size: .85rem; border-radius: var(--radius-sm); margin: 0 3px;
        }
        .pagination-wrap a:hover { border-color: var(--primary); color: var(--primary); }
        .pagination-wrap span[aria-current="page"] > span { background: var(--primary); border-color: var(--primary); color: #fff; font-weight: 700; }
        .pagination-wrap span[aria-disabled="true"] > span { opacity: .45; }

        .summary-list { display: flex; flex-direction: column; gap: 10px; }
        .summary-row { display: flex; align-items: center; justify-content: space-between; gap: 14px; font-size: .9rem; }
        .summary-row.total { padding-top: 12px; border-top: 1px dashed var(--border); font-weight: 700; font-size: 1.05rem; color: var(--dark); }

        /* ============ Responsive ============ */
        @media (max-width: 980px) {
            .grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .grid-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .form-grid-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 720px) {
            .grid-2, .grid-3, .grid-4, .form-grid, .form-grid-3 { grid-template-columns: 1fr; }
            .section { padding: 40px 0; }
            .section-tight { padding: 26px 0; }
            .page-shell { padding: 22px 0 48px; }
            .page-shell.is-flush { padding-top: 0; }
            .container { padding: 0 16px; }
        }

        /* Phones */
        @media (max-width: 620px) {
            .card-pad, .card-body { padding: 18px; }
            .card-head { padding: 15px 18px; }
            .card-foot { padding: 14px 18px; }
            .page-header { margin-bottom: 20px; }
            .page-header h1 { font-size: 1.32rem; }
            .section-head h2 { font-size: 1.45rem; }
            .section-head { margin-bottom: 24px; }
            .btn { padding: 10px 16px; font-size: .88rem; }
            .btn-lg { padding: 12px 20px; font-size: .94rem; }
            /* Keep every control at least a 44px tap target on touch screens */
            .btn-sm { padding: 11px 14px; font-size: .8rem; min-height: 44px; }
            /* 16px stops iOS Safari from zoom-jumping the viewport on focus */
            .form-control { font-size: 16px; padding: 12px 14px; min-height: 46px; }
            select.form-control { padding-right: 36px; }
            label { font-size: .82rem; }
            .checkbox-row { min-height: 44px; align-items: center; }
            .checkbox-row input { width: 20px; height: 20px; margin-top: 0; }
            .alert { padding: 11px 13px; font-size: .85rem; }
            .summary-row { font-size: .86rem; }
            .summary-row.total { font-size: .98rem; }
            .empty-state { padding: 34px 18px; }
            .pagination-wrap span[aria-current="page"] > span,
            .pagination-wrap a { min-width: 40px; height: 40px; padding: 0 9px; font-size: .8rem; }
            .form-actions { flex-direction: column; align-items: stretch; }
            .form-actions .btn { width: 100%; }
            .flex-between { gap: 10px; }
            .v-card-foot { flex-direction: column; align-items: stretch; gap: 10px; }
            .v-card-foot .btn { width: 100%; }
        }

        /* Small phones */
        @media (max-width: 420px) {
            .container { padding: 0 14px; }
            h1, h2, h3 { overflow-wrap: anywhere; }
            .btn { padding: 11px 13px; font-size: .85rem; }
            .badge { font-size: .7rem; padding: 4px 9px; }
            .form-control { padding: 12px 12px; font-size: 16px; }
        }
    </style>

    @stack('styles')
</head>
<body>
    @include('frontend.layouts.navbar')

    <main class="page-shell{{ trim((string) $__env->yieldContent('full-bleed')) !== '' ? ' is-flush' : '' }}">
        @if(trim((string) $__env->yieldContent('full-bleed')) !== '')
            @include('frontend.partials.flash', ['__bleed' => true])
            @yield('full-bleed')
        @else
            <div class="container">
                @include('frontend.partials.flash')
                @yield('content')
            </div>
        @endif
    </main>

    @include('frontend.layouts.footer')

    <script>
        // Mobile navigation toggle
        document.addEventListener('DOMContentLoaded', function () {
            var toggler = document.querySelector('[data-nav-toggle]');
            var menu = document.querySelector('[data-nav-menu]');
            if (toggler && menu) {
                toggler.addEventListener('click', function () {
                    menu.classList.toggle('is-open');
                    toggler.setAttribute('aria-expanded', menu.classList.contains('is-open') ? 'true' : 'false');
                });
            }

            // Generic confirmation prompt for destructive forms
            document.querySelectorAll('form[data-confirm]').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!window.confirm(form.dataset.confirm)) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
