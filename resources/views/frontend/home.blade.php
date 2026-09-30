@extends('frontend.layouts.app')

@section('title', setting('site_name', 'Rideora').' — '.setting('site_tagline', 'Your Ride, Your Way.'))

@push('styles')
<script>document.documentElement.classList.add('js');</script>
<style>
    /* ================================================================
       Rideora — home page
       Motion, elevation and rhythm tokens live in the shared layout.
       ================================================================ */

    /* ---------------- Scroll reveal ---------------- */
    .js [data-reveal] {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity var(--dur-slow) var(--ease-out), transform var(--dur-slow) var(--ease-out);
        transition-delay: var(--reveal-delay, 0s);
    }
    .js [data-reveal].is-in { opacity: 1; transform: none; }

    /* ---------------- Section rhythm ---------------- */
    .sec { padding: 76px 0; }
    .sec-last { padding-top: 0; }
    .sec-head {
        display: flex; align-items: flex-end; justify-content: space-between;
        gap: 20px; flex-wrap: wrap; margin-bottom: 32px;
    }
    .sec-head .section-head { margin-bottom: 0; max-width: 660px; }
    .sec-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .sec-alt { background: #fff; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }

    /* ---------------- Hero ---------------- */
    .hero {
        position: relative;
        color: #fff;
        padding: 84px 0 92px;
        overflow: hidden;
        isolation: isolate;
        background:
            radial-gradient(1100px 520px at 88% -12%, rgba(37, 99, 235, .38), transparent 62%),
            radial-gradient(760px 420px at 4% 108%, rgba(14, 165, 233, .22), transparent 60%),
            linear-gradient(158deg, #0B1220 0%, #131F38 48%, #1B3570 100%);
    }
    /* Faint blueprint grid, faded out towards the bottom */
    .hero::before {
        content: ''; position: absolute; inset: 0; z-index: 0; pointer-events: none;
        background-image:
            linear-gradient(rgba(255, 255, 255, .05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, .05) 1px, transparent 1px);
        background-size: 58px 58px;
        -webkit-mask-image: radial-gradient(120% 78% at 50% 0%, #000 8%, transparent 72%);
        mask-image: radial-gradient(120% 78% at 50% 0%, #000 8%, transparent 72%);
    }
    /* Soft glow anchoring the search card */
    .hero::after {
        content: ''; position: absolute; z-index: 0; pointer-events: none;
        right: -6%; top: 6%; width: 620px; height: 620px; border-radius: 50%;
        background: radial-gradient(closest-side, rgba(59, 130, 246, .3), transparent);
    }
    .hero > .container { position: relative; z-index: 1; }

    .hero-grid { display: grid; grid-template-columns: 1.02fr .98fr; gap: 52px; align-items: center; }
    .hero .eyebrow {
        background: rgba(147, 197, 253, .14); color: #BFDBFE; border: 1px solid rgba(147, 197, 253, .22);
    }
    .hero h1 { color: #fff; font-size: clamp(2.2rem, 5.2vw, 3.35rem); letter-spacing: -.035em; line-height: 1.08; margin-bottom: 16px; }
    .hero h1 .grad { color: #93C5FD; }
    @supports ((-webkit-background-clip: text) or (background-clip: text)) {
        .hero h1 .grad {
            background: linear-gradient(100deg, #93C5FD 0%, #DBEAFE 42%, #60A5FA 100%);
            -webkit-background-clip: text; background-clip: text; color: transparent;
        }
    }
    .hero p.lead { color: #C3D0E4; font-size: 1.1rem; max-width: 32rem; margin-bottom: 26px; }

    .hero-pills { display: flex; gap: 9px; flex-wrap: wrap; margin-bottom: 32px; }
    .hero-pill {
        display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; border-radius: 999px;
        background: rgba(255, 255, 255, .07); border: 1px solid rgba(255, 255, 255, .13);
        font-size: .82rem; font-weight: 500; color: #DCE6F5;
        transition: background var(--dur-fast) ease, border-color var(--dur-fast) ease;
    }
    .hero-pill:hover { background: rgba(255, 255, 255, .12); border-color: rgba(255, 255, 255, .24); }
    .hero-pill i { color: #6EE7B7; font-size: .9rem; }

    .hero-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px 22px; }
    .hero-stat { padding-left: 14px; border-left: 2px solid rgba(147, 197, 253, .35); }
    .hero-stat strong { display: block; font-size: 1.6rem; font-weight: 800; color: #fff; line-height: 1.15; font-variant-numeric: tabular-nums; }
    .hero-stat span { font-size: .78rem; color: #93A4BE; line-height: 1.35; display: block; margin-top: 2px; }

    .hero-cue {
        position: absolute; left: 50%; bottom: 20px; transform: translateX(-50%);
        color: rgba(255, 255, 255, .34); font-size: 1.1rem; z-index: 1; pointer-events: none;
    }
    @media (prefers-reduced-motion: no-preference) {
        .hero-cue { animation: cue-bob 2.4s ease-in-out infinite; }
        @keyframes cue-bob { 0%, 100% { transform: translate(-50%, 0); } 50% { transform: translate(-50%, 7px); } }
    }

    /* ---------------- Hero search card ---------------- */
    .search-card {
        background: #fff; border-radius: 22px; padding: 26px; color: var(--text);
        border: 1px solid rgba(255, 255, 255, .5);
        box-shadow: 0 30px 70px -18px rgba(2, 8, 23, .55), 0 0 0 1px rgba(15, 23, 42, .04);
    }
    .search-head { display: flex; align-items: center; gap: 12px; margin-bottom: 6px; }
    .search-head .ico {
        width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
        background: var(--primary-soft); color: var(--primary);
        display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem;
    }
    .search-card h3 { font-size: 1.12rem; line-height: 1.25; }
    .search-card > p { color: var(--muted); font-size: .86rem; margin-bottom: 20px; }
    .search-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .search-grid .full { grid-column: 1 / -1; }
    .search-grid .btn-lg { min-height: 50px; }

    .field { position: relative; display: flex; align-items: center; }
    .field > i {
        position: absolute; left: 14px; font-size: .95rem; color: #94A3B8;
        pointer-events: none; transition: color var(--dur-fast) ease; z-index: 1;
    }
    .field .form-control { padding-left: 40px; }
    .field:focus-within > i { color: var(--primary); }
    .search-note {
        display: flex; align-items: flex-start; gap: 8px; margin-top: 14px;
        font-size: .78rem; color: var(--muted); line-height: 1.45;
    }
    .search-note i { color: var(--success); flex-shrink: 0; margin-top: 1px; }

    /* ---------------- Marquee (featured + categories) ---------------- */
    .fleet-toggle {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        height: 44px; padding: 0 16px; border-radius: 999px; flex-shrink: 0;
        border: 1px solid var(--border); background: #fff; color: var(--text);
        font-family: inherit; font-size: .84rem; font-weight: 600; cursor: pointer;
        transition: border-color var(--dur-fast) ease, color var(--dur-fast) ease, box-shadow var(--dur-fast) ease;
    }
    .fleet-toggle:hover { border-color: var(--primary); color: var(--primary); box-shadow: var(--shadow-sm); }
    .fleet-toggle[aria-pressed="true"] { border-color: var(--primary); color: var(--primary); background: var(--primary-soft); }

    .fleet-marquee {
        position: relative;
        /* Room for the hover lift + shadow — without this `overflow: hidden` crops the card tops */
        padding: 12px 0;
        overflow: hidden;
        /* Fade the edges so cards enter and leave instead of clipping hard */
        -webkit-mask-image: linear-gradient(90deg, transparent 0, #000 3.5%, #000 96.5%, transparent 100%);
        mask-image: linear-gradient(90deg, transparent 0, #000 3.5%, #000 96.5%, transparent 100%);
    }
    .fleet-track { display: flex; width: max-content; animation: fleet-scroll 48s linear infinite; will-change: transform; }
    .fleet-marquee:hover .fleet-track,
    .fleet-track:focus-within,
    .fleet-track.is-paused { animation-play-state: paused; }
    .fleet-group { display: flex; gap: 22px; padding-right: 22px; }
    .fleet-group .v-card { width: 290px; flex: 0 0 290px; }
    .fleet-group .cat-card { width: 270px; flex: 0 0 270px; }
    /* -50% is exactly one group (cards + gaps + trailing space), so the loop is seamless */
    @keyframes fleet-scroll {
        from { transform: translate3d(0, 0, 0); }
        to   { transform: translate3d(-50%, 0, 0); }
    }

    /* ---------------- Categories ---------------- */
    .cat-card {
        display: flex; align-items: center; gap: 14px; padding: 20px; background: #fff;
        border: 1px solid var(--border); border-radius: var(--radius-lg); color: var(--text); height: 100%;
        transition: border-color var(--dur) var(--ease-out), box-shadow var(--dur) var(--ease-out), transform var(--dur) var(--ease-out);
    }
    .cat-card:hover { border-color: var(--primary); color: var(--text); box-shadow: var(--shadow); transform: translateY(-3px); }
    .cat-icon {
        width: 48px; height: 48px; border-radius: 14px; background: var(--primary-soft); color: var(--primary);
        display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
        font-size: 1.3rem; line-height: 1; overflow: hidden;
        transition: background var(--dur) var(--ease-out), color var(--dur) var(--ease-out);
    }
    .cat-card:hover .cat-icon { background: var(--primary); color: #fff; }
    .cat-body { flex: 1; min-width: 0; }
    .cat-card strong {
        display: block; font-size: .95rem; color: var(--dark);
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .cat-card span.muted { display: block; font-size: .8rem; }
    .cat-go { color: var(--primary); opacity: 0; transform: translateX(-6px); transition: opacity var(--dur) var(--ease-out), transform var(--dur) var(--ease-out); flex-shrink: 0; }
    .cat-card:hover .cat-go, .cat-card:focus-visible .cat-go { opacity: 1; transform: none; }

    /* ---------------- How it works ---------------- */
    .how-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 32px; position: relative; }
    .how-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; position: relative; z-index: 1; height: 100%; }
    .how-num {
        display: inline-flex; align-items: center; justify-content: center;
        width: 44px; height: 44px; border-radius: 13px; background: var(--primary-soft); color: var(--primary);
        font-size: .95rem; font-weight: 800; letter-spacing: .02em; margin-bottom: 16px;
    }
    .how-card h3 { font-size: 1.02rem; margin-bottom: 7px; }
    .how-card p { font-size: .87rem; color: var(--muted); }
    .how-body { min-width: 0; }
    .how-card .step-tag {
        display: inline-flex; align-items: center; gap: 6px; margin-top: 14px;
        font-size: .74rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: var(--primary);
    }
    /* Dashed connector, only visible in the gaps between cards */
    @media (min-width: 901px) {
        .how-grid::before {
            content: ''; position: absolute; top: 46px; left: 12.5%; right: 12.5%; height: 2px; z-index: 0;
            background-image: linear-gradient(90deg, var(--border) 55%, transparent 0);
            background-size: 11px 2px;
        }
    }

    /* ---------------- Why Rideora ---------------- */
    .why-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; }
    .why-card {
        display: flex; gap: 14px; align-items: flex-start; background: #fff;
        border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 22px; height: 100%;
        transition: border-color var(--dur) var(--ease-out), box-shadow var(--dur) var(--ease-out), transform var(--dur) var(--ease-out);
    }
    .why-card:hover { border-color: #CBD5E1; box-shadow: var(--shadow); transform: translateY(-3px); }
    .why-ico {
        width: 44px; height: 44px; border-radius: 13px; background: var(--primary-soft); color: var(--primary);
        display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;
    }
    .why-card h3 { font-size: .97rem; margin-bottom: 5px; }
    .why-card p { font-size: .84rem; color: var(--muted); }

    /* ---------------- Testimonials ---------------- */
    .testi-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; }
    .testi-card {
        background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 26px;
        position: relative; height: 100%; display: flex; flex-direction: column; margin: 0;
    }
    .testi-card blockquote { margin: 0; }
    .testi-quote {
        position: absolute; top: 20px; right: 24px; font-size: 3.4rem; line-height: 1;
        color: var(--primary-soft); font-weight: 800; pointer-events: none; user-select: none;
    }
    .testi-card p { font-size: .92rem; color: var(--text); margin: 12px 0 18px; position: relative; }
    .testi-foot { margin-top: auto; display: flex; align-items: center; gap: 11px; padding-top: 16px; border-top: 1px solid var(--border); }
    .testi-who { display: flex; align-items: center; gap: 11px; min-width: 0; }
    .testi-who > span:last-child { min-width: 0; }
    .testi-who strong { font-size: .88rem; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .testi-avatar {
        width: 40px; height: 40px; border-radius: 50%; background: var(--primary-soft); color: var(--primary);
        display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: .84rem; flex-shrink: 0;
    }
    .testi-verified { display: inline-flex; align-items: center; gap: 5px; font-size: .72rem; font-weight: 700; color: var(--success); margin-left: auto; flex-shrink: 0; }

    /* ---------------- CTA ---------------- */
    .cta {
        position: relative; overflow: hidden;
        background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 55%, #1E3A8A 100%);
        color: #fff; border-radius: 24px; padding: 48px;
        display: flex; align-items: center; justify-content: space-between; gap: 28px; flex-wrap: wrap;
    }
    .cta::before {
        content: ''; position: absolute; right: -80px; top: -110px; width: 420px; height: 420px; border-radius: 50%;
        background: radial-gradient(closest-side, rgba(255, 255, 255, .16), transparent); pointer-events: none;
    }
    .cta > * { position: relative; z-index: 1; }
    .cta h2 { color: #fff; font-size: 2rem; letter-spacing: -.02em; margin-bottom: 8px; }
    .cta p { color: #DBEAFE; }
    .cta .btn-light { background: #fff; color: var(--primary-dark); border-color: #fff; box-shadow: 0 10px 26px -8px rgba(2, 8, 23, .45); }
    .cta .btn-light:hover { background: #EFF6FF; color: var(--primary-dark); }
    .cta .btn-dark { background: rgba(8, 15, 33, .82); color: #fff; border-color: rgba(255, 255, 255, .18); }
    .cta .btn-dark:hover { background: rgba(8, 15, 33, .95); color: #fff; }
    .cta-points { list-style: none; display: flex; gap: 18px; flex-wrap: wrap; margin-top: 20px; }
    .cta-points li { display: inline-flex; align-items: center; gap: 7px; font-size: .84rem; color: #DBEAFE; }
    .cta-points i { color: #6EE7B7; }

    /* ================================================================
       Responsive
       ================================================================ */
    @media (max-width: 1040px) {
        .hero-grid { grid-template-columns: 1fr; gap: 40px; }
        .hero { padding: 64px 0 76px; }
        .sec { padding: 60px 0; }
    }

    @media (max-width: 900px) {
        .how-grid, .testi-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .why-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 620px) {
        .hero { padding: 44px 0 58px; }
        .hero p.lead { font-size: 1rem; }
        .hero-pills { margin-bottom: 26px; gap: 8px; }
        .hero-pill { font-size: .78rem; padding: 7px 12px; }
        /* 2 x 2 keeps the numbers on one line instead of a ragged wrap */
        .hero-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 18px; }
        .hero-stat strong { font-size: 1.32rem; }
        .hero-cue { display: none; }

        .sec { padding: 44px 0; }
        .sec-head { margin-bottom: 24px; }
        .sec-actions { width: 100%; }
        .sec-actions .btn { flex: 1 1 auto; justify-content: center; }
        .fleet-toggle { padding: 0 14px; }
        .fleet-toggle [data-fleet-toggle-label] { display: none; }

        .search-card { padding: 20px; border-radius: 18px; }
        .search-grid { grid-template-columns: 1fr; }

        /* Steps collapse into compact rows — the number sits beside the text
           instead of above it, which roughly halves the section height. */
        .how-grid { grid-template-columns: 1fr; gap: 12px; }
        .how-card { display: flex; align-items: flex-start; gap: 14px; padding: 16px; }
        .how-num { width: 38px; height: 38px; border-radius: 11px; font-size: .84rem; margin-bottom: 0; flex-shrink: 0; }
        .how-body { flex: 1; }
        .how-card h3 { font-size: .95rem; margin-bottom: 4px; }
        .how-card p { font-size: .82rem; }
        .step-tag { display: none; }

        .why-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .why-card { flex-direction: column; gap: 10px; padding: 16px 14px; }
        .why-ico { width: 38px; height: 38px; border-radius: 11px; font-size: 1.05rem; }
        .why-card h3 { font-size: .9rem; margin-bottom: 4px; }
        .why-card p { font-size: .8rem; }

        .testi-grid { grid-template-columns: 1fr; gap: 16px; }
        .testi-card { padding: 20px; }

        .cta { padding: 30px 22px; border-radius: 18px; gap: 22px; }
        .cta h2 { font-size: 1.5rem; }
        .cta-points { gap: 10px 16px; }
        .cta-points li { font-size: .8rem; }

        .fleet-group { gap: 14px; padding-right: 14px; }
        .fleet-marquee { padding: 8px 0; }
        .fleet-group .v-card { width: 240px; flex: 0 0 240px; }
        .fleet-group .cat-card { width: 156px; flex: 0 0 156px; }

        /* Stack the category chip so long names stay readable */
        .fleet-group .cat-card { flex-direction: column; align-items: center; text-align: center; gap: 10px; padding: 16px 10px; }
        .cat-body { width: 100%; }
        .cat-card strong { white-space: normal; overflow: visible; font-size: .86rem; }
        .cat-card span.muted { font-size: .74rem; }
        .cat-go { display: none; }
    }

    @media (max-width: 420px) {
        .hero h1 { letter-spacing: -.03em; }
        .hero-pill { font-size: .74rem; padding: 7px 11px; }
        .hero-stat strong { font-size: 1.22rem; }
        .fleet-group .cat-card { width: 144px; flex: 0 0 144px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .js [data-reveal] { opacity: 1; transform: none; transition: none; }
        .hero-cue { display: none; }
        .fleet-track { animation: none; }
        .fleet-marquee { overflow-x: auto; -webkit-mask-image: none; mask-image: none; }
        .fleet-group[aria-hidden="true"] { display: none; }
    }
</style>
@endpush

@section('full-bleed')
    <!-- ===================== HERO ===================== -->
    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div>
                    <span class="eyebrow"><i class="bi bi-lightning-charge-fill" aria-hidden="true"></i> {{ setting('site_name', 'Rideora') }} Vehicle Rental</span>
                    <h1>Your Ride, <span class="grad">Your Way.</span></h1>
                    <p class="lead">Reliable vehicles. Flexible rentals. Simple booking — with a real person checking every payment before you pick up the keys.</p>

                    <div class="hero-pills">
                        <span class="hero-pill"><i class="bi bi-check-lg" aria-hidden="true"></i> Verified vehicles</span>
                        <span class="hero-pill"><i class="bi bi-check-lg" aria-hidden="true"></i> Transparent pricing</span>
                        <span class="hero-pill"><i class="bi bi-check-lg" aria-hidden="true"></i> Manual payment verification</span>
                    </div>

                    <div class="hero-stats">
                        <div class="hero-stat"><strong>{{ number_format($stats['vehicles']) }}+</strong><span>Vehicles ready</span></div>
                        <div class="hero-stat"><strong>{{ number_format($stats['customers']) }}+</strong><span>Happy customers</span></div>
                        <div class="hero-stat"><strong>{{ number_format($stats['bookings']) }}+</strong><span>Bookings confirmed</span></div>
                        <div class="hero-stat"><strong>{{ number_format($stats['reviews']) }}+</strong><span>Customer reviews</span></div>
                    </div>
                </div>

                <div class="search-card">
                    <div class="search-head">
                        <span class="ico" aria-hidden="true"><i class="bi bi-search"></i></span>
                        <h3>Find your ride</h3>
                    </div>
                    <p>Search the fleet by location, dates and vehicle type.</p>

                    <form method="GET" action="{{ route('vehicles.index') }}">
                        <div class="search-grid">
                            <div class="form-group full">
                                <label for="hero-location">Pickup location</label>
                                <div class="field">
                                    <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                    <input type="text" id="hero-location" name="location" class="form-control"
                                           value="{{ old('location') }}" placeholder="Dhaka, Chattogram, Sylhet…"
                                           list="hero-locations" autocomplete="address-level2">
                                </div>
                                <datalist id="hero-locations">
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc }}"></option>
                                    @endforeach
                                </datalist>
                            </div>

                            <div class="form-group">
                                <label for="hero-pickup">Pickup date</label>
                                <div class="field">
                                    <i class="bi bi-calendar3" aria-hidden="true"></i>
                                    <input type="date" id="hero-pickup" name="pickup_date" class="form-control"
                                           min="{{ now()->toDateString() }}" value="{{ old('pickup_date', now()->addDay()->toDateString()) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="hero-return">Return date</label>
                                <div class="field">
                                    <i class="bi bi-calendar-check" aria-hidden="true"></i>
                                    <input type="date" id="hero-return" name="return_date" class="form-control"
                                           min="{{ now()->toDateString() }}" value="{{ old('return_date', now()->addDays(2)->toDateString()) }}">
                                </div>
                            </div>

                            <div class="form-group full">
                                <label for="hero-category">Vehicle type</label>
                                <div class="field">
                                    <i class="bi bi-car-front" aria-hidden="true"></i>
                                    <select id="hero-category" name="category" class="form-control">
                                        <option value="">All categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->slug }}" @selected(old('category') === $category->slug)>
                                                {{ $category->name }} ({{ $category->vehicles_count }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group full">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">
                                    Search Vehicles<i class="bi bi-arrow-right" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <p class="search-note">
                            <i class="bi bi-shield-check" aria-hidden="true"></i>
                            <span>No booking fee. You only pay after our team confirms your payment.</span>
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <span class="hero-cue" aria-hidden="true"><i class="bi bi-chevron-double-down"></i></span>
    </section>

    <!-- ===================== FEATURED VEHICLES ===================== -->
    <section class="sec">
        <div class="container">
            <div class="sec-head" data-reveal>
                <div class="section-head">
                    <span class="eyebrow">Featured fleet</span>
                    <h2>Popular vehicles this week</h2>
                    <p>Handpicked rides, inspected and maintained by the Rideora team.</p>
                </div>
                <div class="sec-actions">
                    <button type="button" class="fleet-toggle" data-fleet-toggle="fleet"
                            aria-pressed="false" aria-label="Pause the scrolling vehicle list">
                        <i class="bi bi-pause-fill" aria-hidden="true"></i>
                        <span data-fleet-toggle-label aria-hidden="true">Pause</span>
                    </button>
                    <a href="{{ route('vehicles.index') }}" class="btn btn-outline">
                        Browse all vehicles<i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            @if($featuredVehicles->isEmpty())
                <div class="card card-pad empty-state">
                    <div class="icon"><i class="bi bi-car-front"></i></div>
                    <h3>No vehicles published yet</h3>
                    <p>Our fleet is being prepared. Please check back shortly.</p>
                </div>
            @else
                <div class="fleet-marquee" data-fleet="fleet" data-reveal>
                    <div class="fleet-track" data-fleet-track>
                        @foreach([0, 1] as $copy)
                            <div class="fleet-group"{!! $copy ? ' aria-hidden="true"' : '' !!}>
                                @foreach($featuredVehicles as $vehicle)
                                    @include('frontend.partials.vehicle-card', ['vehicle' => $vehicle])
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- ===================== CATEGORIES ===================== -->
    <section class="sec sec-alt">
        <div class="container">
            <div class="sec-head" data-reveal>
                <div class="section-head">
                    <span class="eyebrow">Browse by type</span>
                    <h2>Vehicle categories</h2>
                    <p>From daily commutes to family trips and cargo runs.</p>
                </div>
                <div class="sec-actions">
                    <button type="button" class="fleet-toggle" data-fleet-toggle="cats"
                            aria-pressed="false" aria-label="Pause the scrolling category list">
                        <i class="bi bi-pause-fill" aria-hidden="true"></i>
                        <span data-fleet-toggle-label aria-hidden="true">Pause</span>
                    </button>
                </div>
            </div>

            @if($categories->isEmpty())
                <div class="card card-pad empty-state">
                    <div class="icon"><i class="bi bi-grid"></i></div>
                    <h3>No categories yet</h3>
                    <p>Categories will appear here as soon as they are added.</p>
                </div>
            @else
                <div class="fleet-marquee" data-fleet="cats" data-reveal>
                    <div class="fleet-track" data-fleet-track>
                        @foreach([0, 1] as $copy)
                            <div class="fleet-group"{!! $copy ? ' aria-hidden="true"' : '' !!}>
                                @foreach($categories as $category)
                                    <a href="{{ route('vehicles.index', ['category' => $category->slug]) }}" class="cat-card">
                                        <span class="cat-icon">{!! category_icon($category->icon) !!}</span>
                                        <span class="cat-body">
                                            <strong>{{ $category->name }}</strong>
                                            <span class="muted">{{ $category->vehicles_count }} {{ \Illuminate\Support\Str::plural('vehicle', $category->vehicles_count) }}</span>
                                        </span>
                                        <i class="bi bi-arrow-right cat-go" aria-hidden="true"></i>
                                    </a>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- ===================== HOW IT WORKS ===================== -->
    <section class="sec">
        <div class="container">
            <div class="section-head" data-reveal>
                <span class="eyebrow">How it works</span>
                <h2>Rent a vehicle in four simple steps</h2>
                <p>No hidden charges, no automated gateways — a real person verifies every payment.</p>
            </div>

            <div class="how-grid">
                @foreach([
                    ['01', 'Choose vehicle', 'Browse the fleet, filter by category, seats and price, then open the vehicle you like.', 'Start browsing'],
                    ['02', 'Book your ride', 'Pick your rental dates and times. We instantly check availability and calculate the total.', 'Check availability'],
                    ['03', 'Pay manually', 'Send the total via bKash, Nagad or bank transfer and upload the payment screenshot.', 'Payment methods'],
                    ['04', 'Get confirmation', 'Our team verifies your payment and confirms the booking. Your ride is ready.', 'Collect keys'],
                ] as $index => $step)
                    <div class="how-card" data-reveal style="--reveal-delay: {{ $index * 70 }}ms">
                        <span class="how-num" aria-hidden="true">{{ $step[0] }}</span>
                        <div class="how-body">
                            <h3>{{ $step[1] }}</h3>
                            <p>{{ $step[2] }}</p>
                            <span class="step-tag"><i class="bi bi-arrow-right" aria-hidden="true"></i>{{ $step[3] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===================== WHY RIDEORA ===================== -->
    <section class="sec sec-alt">
        <div class="container">
            <div class="section-head" data-reveal>
                <span class="eyebrow">Why Rideora</span>
                <h2>Built around trust and clarity</h2>
                <p>The things customers actually care about when they hand over their money.</p>
            </div>

            <div class="why-grid">
                @foreach([
                    ['lightning-charge-fill', 'Easy booking', 'A short form and a live price summary — nothing more.'],
                    ['shield-check', 'Verified vehicles', 'Every vehicle is inspected, documented and maintained.'],
                    ['cash-stack', 'Transparent pricing', 'Base fare, deposit and discount are shown up front.'],
                    ['check-circle-fill', 'Manual verification', 'Real humans review each payment before confirmation.'],
                    ['tools', 'Serviced fleet', 'Scheduled maintenance between every single rental.'],
                    ['headset', 'Real support', 'A person answers before, during and after your rental.'],
                ] as $index => $why)
                    <div class="why-card" data-reveal style="--reveal-delay: {{ ($index % 3) * 70 }}ms">
                        <span class="why-ico" aria-hidden="true"><i class="bi bi-{{ $why[0] }}"></i></span>
                        <div>
                            <h3>{{ $why[1] }}</h3>
                            <p>{{ $why[2] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===================== TESTIMONIALS ===================== -->
    @if($testimonials->isNotEmpty())
        <section class="sec">
            <div class="container">
                <div class="section-head" data-reveal>
                    <span class="eyebrow">Testimonials</span>
                    <h2>What our customers say</h2>
                </div>

                <div class="testi-grid">
                    @foreach($testimonials as $testimonial)
                        <figure class="testi-card" data-reveal>
                            <span class="testi-quote" aria-hidden="true">&ldquo;</span>
                            {!! star_row($testimonial->rating) !!}
                            <blockquote><p>{{ \Illuminate\Support\Str::limit($testimonial->comment, 160) }}</p></blockquote>
                            <figcaption class="testi-foot">
                                <span class="testi-who">
                                    <span class="testi-avatar">{{ $testimonial->user?->initials() ?? 'R' }}</span>
                                    <span>
                                        <strong>{{ $testimonial->user?->name ?? 'Rideora customer' }}</strong>
                                        <span class="muted small">{{ $testimonial->vehicle?->name }}</span>
                                    </span>
                                </span>
                                <span class="testi-verified"><i class="bi bi-patch-check-fill" aria-hidden="true"></i>Verified</span>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- ===================== CTA ===================== -->
    <section class="sec sec-last">
        <div class="container">
            <div class="cta" data-reveal>
                <div>
                    <h2>Ready to hit the road?</h2>
                    <p>Find your perfect ride today — it takes about two minutes.</p>
                    <ul class="cta-points">
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>No booking fee</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Free cancellation up to 24h</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i>Inspected vehicles only</li>
                    </ul>
                </div>
                <div class="sec-actions">
                    <a href="{{ route('vehicles.index') }}" class="btn btn-light btn-lg">Browse Vehicles</a>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-dark btn-lg">Create Free Account</a>
                    @endguest
                    @auth
                        @unless(auth()->user()->isAdmin())
                            <a href="{{ route('bookings.index') }}" class="btn btn-dark btn-lg">My Bookings</a>
                        @endunless
                    @endauth
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        /* ---------- Scroll reveal ---------- */
        var revealables = document.querySelectorAll('[data-reveal]');
        if (reduced || !('IntersectionObserver' in window)) {
            revealables.forEach(function (el) { el.classList.add('is-in'); });
        } else {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) { return; }
                    entry.target.classList.add('is-in');
                    observer.unobserve(entry.target);
                });
            }, { rootMargin: '0px 0px -6% 0px', threshold: 0.06 });
            revealables.forEach(function (el) { observer.observe(el); });
        }

        /* ---------- Fleet marquees: pause control + touch safety ---------- */
        document.querySelectorAll('[data-fleet]').forEach(function (root) {
            var track = root.querySelector('[data-fleet-track]');
            if (!track) { return; }

            var toggle = document.querySelector('[data-fleet-toggle="' + root.getAttribute('data-fleet') + '"]');
            var manual = false;
            var resumeTimer = null;

            function setPaused(on) {
                track.classList.toggle('is-paused', on);
            }

            function syncToggle() {
                if (!toggle) { return; }
                var paused = track.classList.contains('is-paused');
                var label = toggle.querySelector('[data-fleet-toggle-label]');
                var icon = toggle.querySelector('i');
                toggle.setAttribute('aria-pressed', paused ? 'true' : 'false');
                toggle.setAttribute('aria-label', (paused ? 'Resume' : 'Pause') + ' the scrolling list');
                if (label) { label.textContent = paused ? 'Play' : 'Pause'; }
                if (icon) { icon.className = paused ? 'bi bi-play-fill' : 'bi bi-pause-fill'; }
            }

            if (toggle) {
                if (reduced) {
                    // The stylesheet already turns the strip into a plain scroll area.
                    toggle.hidden = true;
                } else {
                    toggle.addEventListener('click', function () {
                        manual = !track.classList.contains('is-paused');
                        setPaused(manual);
                        syncToggle();
                    });
                }
            }

            // Cards must never slide out from under a finger mid-tap.
            root.addEventListener('touchstart', function () {
                if (manual || reduced) { return; }
                setPaused(true);
                clearTimeout(resumeTimer);
                resumeTimer = setTimeout(function () {
                    if (!manual) { setPaused(false); }
                }, 5000);
            }, { passive: true });
        });
    });
</script>
@endpush
