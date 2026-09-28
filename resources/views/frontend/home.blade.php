@extends('frontend.layouts.app')

@section('title', setting('site_name', 'Rideora').' — '.setting('site_tagline', 'Your Ride, Your Way.'))

@push('styles')
<style>
    .hero {
        background:
            radial-gradient(1000px 460px at 88% -10%, rgba(37, 99, 235, .28), transparent 60%),
            linear-gradient(160deg, #0F172A 0%, #16233f 55%, #1e3a8a 100%);
        color: #fff; padding: 78px 0 96px; position: relative; overflow: hidden;
    }
    .hero::after {
        content: ''; position: absolute; inset: auto -10% -60% 40%; height: 340px;
        background: radial-gradient(closest-side, rgba(37, 99, 235, .35), transparent); pointer-events: none;
    }
    .hero-grid { display: grid; grid-template-columns: 1.05fr .95fr; gap: 46px; align-items: center; position: relative; z-index: 1; }
    .hero h1 { color: #fff; font-size: 3.05rem; letter-spacing: -.03em; margin-bottom: 14px; }
    .hero h1 span { color: #93C5FD; }
    .hero p.lead { color: #C7D2E5; font-size: 1.08rem; max-width: 30rem; margin-bottom: 26px; }
    .hero-pills { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 26px; }
    .hero-pill {
        display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; border-radius: 999px;
        background: rgba(255, 255, 255, .08); border: 1px solid rgba(255, 255, 255, .14); font-size: .82rem; color: #E2E8F0;
    }
    .hero-stats { display: flex; gap: 30px; flex-wrap: wrap; }
    .hero-stat strong { display: block; font-size: 1.5rem; color: #fff; }
    .hero-stat span { font-size: .8rem; color: #94A3B8; }

    .search-card {
        background: #fff; border-radius: var(--radius-lg); padding: 26px; box-shadow: var(--shadow-lg);
        border: 1px solid rgba(255, 255, 255, .5);
    }
    .search-card h3 { font-size: 1.15rem; margin-bottom: 4px; }
    .search-card > p { color: var(--muted); font-size: .86rem; margin-bottom: 18px; }
    .search-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .search-grid .full { grid-column: 1 / -1; }

    .how-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
    .how-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; position: relative; }
    .how-num {
        font-size: 1.7rem; font-weight: 800; color: #DBEAFE; letter-spacing: -.04em; display: block; margin-bottom: 10px;
    }
    .how-card h3 { font-size: 1rem; margin-bottom: 6px; }
    .how-card p { font-size: .87rem; color: var(--muted); }

    .cat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 18px; }
    .cat-card {
        display: flex; align-items: center; gap: 14px; padding: 20px; background: #fff; border: 1px solid var(--border);
        border-radius: var(--radius-lg); color: var(--text); transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }
    .cat-card:hover { border-color: var(--primary); color: var(--text); box-shadow: var(--shadow); transform: translateY(-2px); }
    .cat-icon {
        width: 46px; height: 46px; border-radius: 13px; background: var(--primary-soft);
        display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
        font-size: 1.25rem; line-height: 1; overflow: hidden;
    }
    .cat-body { flex: 1; min-width: 0; }
    .cat-card strong {
        display: block; font-size: .95rem; color: var(--dark);
        overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .cat-card span.muted { display: block; font-size: .8rem; }

    .why-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; }
    .why-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; }
    .why-card .ico { font-size: 1.3rem; margin-bottom: 8px; display: block; }
    .why-card h4 { font-size: .92rem; margin-bottom: 5px; }
    .why-card p { font-size: .82rem; color: var(--muted); }

    .testi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .testi-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; }
    .testi-card p { font-size: .9rem; color: var(--text); margin: 12px 0 16px; }
    .testi-who { display: flex; align-items: center; gap: 10px; }
    .testi-who strong { font-size: .88rem; display: block; }
    .testi-avatar {
        width: 38px; height: 38px; border-radius: 50%; background: var(--primary-soft); color: var(--primary);
        display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: .82rem;
    }

    .cta {
        background: linear-gradient(135deg, #2563EB, #1D4ED8); color: #fff; border-radius: var(--radius-lg);
        padding: 46px; display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap;
    }
    .cta h2 { color: #fff; font-size: 1.9rem; margin-bottom: 6px; }
    .cta p { color: #DBEAFE; }
    .cta .btn-light { background: #fff; color: var(--primary); border-color: #fff; }

    /* Vehicle cards */
    .v-card {
        background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden;
        display: flex; flex-direction: column; transition: transform .18s ease, box-shadow .18s ease;
    }
    .v-card:hover { transform: translateY(-3px); box-shadow: var(--shadow); }
    .v-card-media { position: relative; aspect-ratio: 16 / 10; background: #EEF2F7; overflow: hidden; }
    .v-card-media img { width: 100%; height: 100%; object-fit: cover; }
    .v-card-badge {
        position: absolute; top: 12px; left: 12px; background: rgba(22, 163, 74, .95); color: #fff;
        font-size: .7rem; font-weight: 700; padding: 5px 10px; border-radius: 999px;
    }
    .v-card-tag {
        position: absolute; top: 12px; right: 12px; background: rgba(15, 23, 42, .78); color: #fff;
        font-size: .7rem; font-weight: 600; padding: 5px 10px; border-radius: 999px;
    }
    .v-card-body { padding: 18px; display: flex; flex-direction: column; gap: 12px; flex: 1; }
    .v-card-title { display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; }
    .v-card-title h3 { font-size: 1.03rem; margin-bottom: 2px; }
    .v-card-rating { display: inline-flex; align-items: center; gap: 4px; font-size: .78rem; color: var(--muted); }
    .v-card-specs { list-style: none; display: flex; flex-wrap: wrap; gap: 8px; }
    .v-card-specs li {
        display: inline-flex; align-items: center; gap: 6px; background: var(--light); border: 1px solid var(--border);
        padding: 5px 10px; border-radius: 999px; font-size: .76rem; color: var(--muted);
    }
    .v-card-location { font-size: .8rem; color: var(--muted); display: flex; align-items: center; gap: 6px; }
    .v-card-foot { margin-top: auto; padding-top: 14px; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .v-card-price strong { font-size: 1.15rem; color: var(--dark); }
    .v-card-price .block { display: block; }

    @media (max-width: 1040px) {
        .why-grid { grid-template-columns: repeat(3, 1fr); }
        .hero-grid { grid-template-columns: 1fr; }
        .hero h1 { font-size: 2.4rem; }
    }
    @media (max-width: 900px) {
        .how-grid, .testi-grid, .cat-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 620px) {
        .how-grid, .testi-grid, .why-grid, .search-grid { grid-template-columns: 1fr; }
        .hero { padding: 44px 0 52px; }
        .hero h1 { font-size: 1.95rem; }
        .hero p.lead { font-size: 1rem; }
        .hero-stats { gap: 18px 26px; }
        .hero-stat strong { font-size: 1.28rem; }
        .search-card { padding: 20px; }
        .how-card, .testi-card, .why-card { padding: 18px; }
        .cta { padding: 28px 20px; }
        .cta h2 { font-size: 1.5rem; }
        .cta .btn { width: 100%; }

        /* Keep categories two per row on phones — stack the chip so names stay readable */
        .cat-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .cat-card { flex-direction: column; align-items: center; text-align: center; gap: 10px; padding: 16px 10px; height: 100%; }
        .cat-body { width: 100%; }
        .cat-card strong { white-space: normal; overflow: visible; font-size: .88rem; }
        .cat-card span.muted { font-size: .74rem; }
    }

    @media (max-width: 420px) {
        .hero h1 { font-size: 1.7rem; }
        .hero-pill { font-size: .76rem; padding: 7px 11px; }
        .v-card-foot { flex-direction: column; align-items: stretch; }
        .v-card-foot .btn { width: 100%; }
    }
</style>
@endpush

@section('full-bleed')
    <!-- ===================== HERO ===================== -->
    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div>
                    <span class="eyebrow" style="background: rgba(147,197,253,.16); color:#BFDBFE;">{{ setting('site_name', 'Rideora') }} Vehicle Rental</span>
                    <h1>Your Ride, <span>Your Way.</span></h1>
                    <p class="lead">Reliable vehicles. Flexible rentals. Simple booking.</p>

                    <div class="hero-pills">
                        <span class="hero-pill"><i class="bi bi-check-lg"></i> Verified vehicles</span>
                        <span class="hero-pill"><i class="bi bi-check-lg"></i> Transparent pricing</span>
                        <span class="hero-pill"><i class="bi bi-check-lg"></i> Manual payment verification</span>
                    </div>

                    <div class="hero-stats">
                        <div class="hero-stat"><strong>{{ $stats['vehicles'] }}+</strong><span>Vehicles ready</span></div>
                        <div class="hero-stat"><strong>{{ $stats['customers'] }}+</strong><span>Happy customers</span></div>
                        <div class="hero-stat"><strong>{{ $stats['bookings'] }}+</strong><span>Bookings confirmed</span></div>
                        <div class="hero-stat"><strong>{{ $stats['reviews'] }}+</strong><span>Customer reviews</span></div>
                    </div>
                </div>

                <div class="search-card">
                    <h3>Find your ride</h3>
                    <p>Search our fleet by location, dates and vehicle category.</p>

                    <form method="GET" action="{{ route('vehicles.index') }}">
                        <div class="search-grid">
                            <div class="form-group full">
                                <label for="hero-location">Pickup Location</label>
                                <input type="text" id="hero-location" name="location" class="form-control"
                                       value="{{ old('location') }}" placeholder="Dhaka, Chattogram, Sylhet…" list="hero-locations">
                                <datalist id="hero-locations">
                                    @foreach(\App\Models\Vehicle::query()->listable()->whereNotNull('location')->distinct()->pluck('location') as $loc)
                                        <option value="{{ $loc }}"></option>
                                    @endforeach
                                </datalist>
                            </div>

                            <div class="form-group">
                                <label for="hero-pickup">Pickup Date</label>
                                <input type="date" id="hero-pickup" name="pickup_date" class="form-control"
                                       min="{{ now()->toDateString() }}" value="{{ old('pickup_date', now()->addDay()->toDateString()) }}">
                            </div>

                            <div class="form-group">
                                <label for="hero-return">Return Date</label>
                                <input type="date" id="hero-return" name="return_date" class="form-control"
                                       min="{{ now()->toDateString() }}" value="{{ old('return_date', now()->addDays(2)->toDateString()) }}">
                            </div>

                            <div class="form-group full">
                                <label for="hero-category">Vehicle Category</label>
                                <select id="hero-category" name="category" class="form-control">
                                    <option value="">All categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->slug }}" @selected(old('category') === $category->slug)>
                                            {{ $category->name }} ({{ $category->vehicles_count }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group full">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Search Vehicles</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== FEATURED VEHICLES ===================== -->
    <section class="section">
        <div class="container">
            <div class="flex-between section-head">
                <div>
                    <span class="eyebrow">Featured Fleet</span>
                    <h2>Popular vehicles this week</h2>
                    <p>Handpicked rides maintained and verified by the Rideora team.</p>
                </div>
                <a href="{{ route('vehicles.index') }}" class="btn btn-outline">Browse all vehicles</a>
            </div>

            @if($featuredVehicles->isEmpty())
                <div class="card card-pad empty-state">
                    <div class="icon"><i class="bi bi-car-front"></i></div>
                    <h3>No vehicles published yet</h3>
                    <p>Our fleet is being prepared. Please check back shortly.</p>
                </div>
            @else
                <div class="grid grid-3">
                    @foreach($featuredVehicles as $vehicle)
                        @include('frontend.partials.vehicle-card', ['vehicle' => $vehicle])
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- ===================== CATEGORIES ===================== -->
    <section class="section-tight">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">Browse by type</span>
                <h2>Vehicle categories</h2>
                <p>From daily commutes to family trips and cargo runs.</p>
            </div>

            <div class="cat-grid">
                @foreach($categories as $category)
                    <a href="{{ route('vehicles.index', ['category' => $category->slug]) }}" class="cat-card">
                        <span class="cat-icon">{!! category_icon($category->icon) !!}</span>
                        <span class="cat-body">
                            <strong>{{ $category->name }}</strong>
                            <span class="muted">{{ $category->vehicles_count }} {{ \Illuminate\Support\Str::plural('vehicle', $category->vehicles_count) }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===================== HOW IT WORKS ===================== -->
    <section class="section">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">How it works</span>
                <h2>Rent a vehicle in four simple steps</h2>
                <p>No hidden charges, no automated gateways — a real person verifies every payment.</p>
            </div>

            <div class="how-grid">
                <div class="how-card">
                    <span class="how-num">01</span>
                    <h3>Choose Vehicle</h3>
                    <p>Browse the fleet, filter by category, seats and price, then open the vehicle you like.</p>
                </div>
                <div class="how-card">
                    <span class="how-num">02</span>
                    <h3>Book Your Ride</h3>
                    <p>Pick your rental dates and times. We instantly check availability and calculate the total.</p>
                </div>
                <div class="how-card">
                    <span class="how-num">03</span>
                    <h3>Pay Manually</h3>
                    <p>Send the total via bKash, Nagad or bank transfer and upload the payment screenshot.</p>
                </div>
                <div class="how-card">
                    <span class="how-num">04</span>
                    <h3>Get Confirmation</h3>
                    <p>Our team verifies your payment and confirms the booking. Your ride is ready.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== WHY RIDEORA ===================== -->
    <section class="section-tight">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">Why Rideora</span>
                <h2>Built around trust and clarity</h2>
            </div>

            <div class="why-grid">
                <div class="why-card">
                    <span class="ico"><i class="bi bi-lightning-charge-fill"></i></span>
                    <h4>Easy Booking</h4>
                    <p>A short form and a live price summary — nothing more.</p>
                </div>
                <div class="why-card">
                    <span class="ico"><i class="bi bi-shield-check"></i></span>
                    <h4>Verified Vehicles</h4>
                    <p>Every vehicle is inspected, documented and maintained.</p>
                </div>
                <div class="why-card">
                    <span class="ico"><i class="bi bi-cash-stack"></i></span>
                    <h4>Transparent Pricing</h4>
                    <p>Base fare, deposit and discount are shown up front.</p>
                </div>
                <div class="why-card">
                    <span class="ico"><i class="bi bi-check-circle-fill"></i></span>
                    <h4>Manual Payment Verification</h4>
                    <p>Real humans review each payment before confirmation.</p>
                </div>
                <div class="why-card">
                    <span class="ico"><i class="bi bi-tools"></i></span>
                    <h4>Reliable Service</h4>
                    <p>Support before, during and after your rental period.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===================== TESTIMONIALS ===================== -->
    @if($testimonials->isNotEmpty())
        <section class="section-tight">
            <div class="container">
                <div class="section-head">
                    <span class="eyebrow">Testimonials</span>
                    <h2>What our customers say</h2>
                </div>

                <div class="testi-grid">
                    @foreach($testimonials as $testimonial)
                        <div class="testi-card">
                            {!! star_row($testimonial->rating) !!}
                            <p>“{{ \Illuminate\Support\Str::limit($testimonial->comment, 160) }}”</p>
                            <div class="testi-who">
                                <span class="testi-avatar">{{ $testimonial->user?->initials() ?? 'R' }}</span>
                                <span>
                                    <strong>{{ $testimonial->user?->name ?? 'Rideora customer' }}</strong>
                                    <span class="muted small">{{ $testimonial->vehicle?->name }}</span>
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- ===================== CTA ===================== -->
    <section class="section-tight" style="padding-bottom: 72px;">
        <div class="container">
            <div class="cta">
                <div>
                    <h2>Ready to hit the road?</h2>
                    <p>Find your perfect ride today.</p>
                </div>
                <div class="flex flex-wrap" style="gap:12px;">
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
