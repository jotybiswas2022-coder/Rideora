@extends('frontend.layouts.app')

@section('title', 'Browse Vehicles — '.setting('site_name', 'Rideora'))

@push('styles')
<style>
    .listing-layout { display: grid; grid-template-columns: 292px 1fr; gap: 26px; align-items: start; }

    .filters { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; position: sticky; top: 88px; }
    .filters h3 { font-size: 1rem; margin-bottom: 4px; }
    .filters .sub { color: var(--muted); font-size: .8rem; margin-bottom: 16px; }
    .filter-block { margin-bottom: 15px; }
    .filter-actions { display: flex; gap: 10px; margin-top: 18px; }
    .filter-actions .btn { flex: 1; justify-content: center; }

    /* Mobile disclosure — hidden on desktop, collapsible below 980px */
    .filter-toggle { display: none; }
    .filter-count {
        display: inline-flex; align-items: center; justify-content: center; min-width: 22px; height: 22px;
        padding: 0 6px; border-radius: 999px; background: var(--primary); color: #fff;
        font-size: .72rem; font-weight: 700; font-variant-numeric: tabular-nums;
    }
    .filter-toggle .bi-sliders { transition: transform var(--dur, .24s) var(--ease-out, ease); }
    .filter-toggle[aria-expanded="true"] { border-color: var(--primary); color: var(--primary); background: var(--primary-soft); }
    .filter-toggle[aria-expanded="true"] .bi-sliders { transform: rotate(90deg); }

    .results-bar { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-bottom: 18px; }
    .results-bar .count { font-size: .9rem; color: var(--muted); }
    .results-bar .count strong { color: var(--dark); font-variant-numeric: tabular-nums; }
    .sort-inline { display: flex; align-items: center; gap: 8px; }
    .sort-inline label { font-size: .82rem; color: var(--muted); }
    .sort-inline .form-control { width: auto; min-width: 168px; padding-top: 9px; padding-bottom: 9px; }

    .active-chips { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-bottom: 16px; }
    .chip {
        display: inline-flex; align-items: center; gap: 6px; background: var(--primary-soft); color: var(--primary-dark);
        border-radius: 999px; padding: 4px 6px 4px 12px; font-size: .78rem; font-weight: 600;
    }
    .chip a {
        color: inherit; opacity: .7; font-weight: 700; display: inline-flex; align-items: center; justify-content: center;
        width: 26px; height: 26px; border-radius: 50%; font-size: 1rem; line-height: 1;
    }
    .chip a:hover { opacity: 1; background: rgba(37, 99, 235, .12); }
    .chips-clear { font-size: .78rem; font-weight: 700; margin-left: 2px; }

    .price-pair { display: flex; gap: 10px; }

    @media (max-width: 980px) {
        .listing-layout { grid-template-columns: 1fr; gap: 18px; }
        .filters { position: static; display: none; }
        .filters.is-open { display: block; }
        .filter-toggle { display: inline-flex; align-items: center; justify-content: center; gap: 9px; }
    }

    @media (max-width: 620px) {
        .filters { padding: 18px; }
        .filter-actions { flex-direction: column-reverse; }
        .results-bar { align-items: stretch; flex-direction: column; gap: 12px; }
        .sort-inline { width: 100%; }
        .sort-inline label { display: none; }
        .sort-inline .form-control { flex: 1; min-width: 0; }
        .chip { padding-left: 11px; }
        .chip a { width: 32px; height: 32px; }
        .grid-2 { gap: 16px; }
    }

    @media (max-width: 420px) {
        .v-card-body { padding: 16px; }
    }
</style>
@endpush

@section('content')
    @php
        $activeFilters = collect([
            'q' => 'Keyword',
            'location' => 'Location',
            'brand' => 'Brand',
            'fuel_type' => 'Fuel',
            'transmission' => 'Transmission',
            'seats' => 'Seats',
            'min_price' => 'Min price',
            'max_price' => 'Max price',
            'pickup_date' => 'Pickup',
            'return_date' => 'Return',
        ])->filter(fn ($label, $key) => filled($filters[$key] ?? null));

        $categoryFilter = $filters['category'] ?? null;
        $activeFilterCount = $activeFilters->count() + (filled($categoryFilter) ? 1 : 0);
    @endphp

    <div class="page-header">
        <h1>Browse our fleet</h1>
        <p>{{ $vehicles->total() }} {{ \Illuminate\Support\Str::plural('vehicle', $vehicles->total()) }} matched your search.</p>
    </div>

    <div class="listing-layout">
        <!-- ============ Filters ============ -->
        <aside>
            <button type="button" class="btn btn-outline btn-block filter-toggle mb-16"
                    data-filter-toggle aria-expanded="false" aria-controls="filter-panel">
                <i class="bi bi-sliders" aria-hidden="true"></i>
                <span data-filter-toggle-label>Search &amp; filters</span>
                @if($activeFilterCount > 0)
                    <span class="filter-count">{{ $activeFilterCount }}</span>
                @endif
            </button>

            <div class="filters" id="filter-panel" data-filter-panel>
                <h3>Search &amp; filter</h3>
                <p class="sub">Narrow the list to find your perfect ride.</p>

                <form method="GET" action="{{ route('vehicles.index') }}">
                    <div class="filter-block form-group">
                        <label for="f-q">Keyword</label>
                        <input type="text" id="f-q" name="q" class="form-control" value="{{ $filters['q'] ?? '' }}"
                               placeholder="Name, brand or model">
                    </div>

                    <div class="filter-block form-group">
                        <label for="f-location">Pickup location</label>
                        <input type="text" id="f-location" name="location" class="form-control" list="filter-locations"
                               value="{{ $filters['location'] ?? '' }}" placeholder="Any location">
                        <datalist id="filter-locations">
                            @foreach($locations as $loc)
                                <option value="{{ $loc }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    <div class="filter-block form-group">
                        <label for="f-pickup">Pickup date</label>
                        <input type="date" id="f-pickup" name="pickup_date" class="form-control"
                               min="{{ now()->toDateString() }}" value="{{ $filters['pickup_date'] ?? '' }}">
                    </div>

                    <div class="filter-block form-group">
                        <label for="f-return">Return date</label>
                        <input type="date" id="f-return" name="return_date" class="form-control"
                               min="{{ now()->toDateString() }}" value="{{ $filters['return_date'] ?? '' }}">
                    </div>

                    <div class="filter-block form-group">
                        <label for="f-category">Category</label>
                        <select id="f-category" name="category" class="form-control">
                            <option value="">All categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" @selected(($filters['category'] ?? '') === $category->slug)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-block form-group">
                        <label for="f-brand">Brand</label>
                        <select id="f-brand" name="brand" class="form-control">
                            <option value="">All brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand }}" @selected(($filters['brand'] ?? '') === $brand)>{{ $brand }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-block form-group">
                        <label for="f-fuel">Fuel type</label>
                        <select id="f-fuel" name="fuel_type" class="form-control">
                            <option value="">Any fuel</option>
                            @foreach(\App\Models\Vehicle::FUEL_TYPES as $fuel)
                                <option value="{{ $fuel }}" @selected(($filters['fuel_type'] ?? '') === $fuel)>{{ $fuel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-block form-group">
                        <label for="f-transmission">Transmission</label>
                        <select id="f-transmission" name="transmission" class="form-control">
                            <option value="">Any transmission</option>
                            @foreach(\App\Models\Vehicle::TRANSMISSIONS as $transmission)
                                <option value="{{ $transmission }}" @selected(($filters['transmission'] ?? '') === $transmission)>{{ $transmission }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-block form-group">
                        <label for="f-seats">Minimum seats</label>
                        <select id="f-seats" name="seats" class="form-control">
                            <option value="">Any</option>
                            @foreach([2, 4, 5, 7, 8, 12] as $seatOption)
                                <option value="{{ $seatOption }}" @selected((string) ($filters['seats'] ?? '') === (string) $seatOption)>
                                    {{ $seatOption }}+ seats
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-block form-group">
                        <label for="f-min">Daily price range</label>
                        <div class="price-pair">
                            <input type="number" id="f-min" name="min_price" class="form-control" min="0" step="100" inputmode="numeric"
                                   aria-label="Minimum daily price" placeholder="Min" value="{{ $filters['min_price'] ?? '' }}">
                            <input type="number" id="f-max" name="max_price" class="form-control" min="0" step="100" inputmode="numeric"
                                   aria-label="Maximum daily price" placeholder="Max" value="{{ $filters['max_price'] ?? '' }}">
                        </div>
                    </div>

                    <div class="filter-block form-group">
                        <label for="f-sort">Sort by</label>
                        <select id="f-sort" name="sort" class="form-control" data-auto-submit>
                            @foreach(['newest' => 'Newest first', 'price_asc' => 'Price: low to high', 'price_desc' => 'Price: high to low', 'name_asc' => 'Name: A to Z', 'rating' => 'Top rated'] as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['sort'] ?? 'newest') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Apply filters</button>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-light">Reset all</a>
                    </div>
                </form>
            </div>
        </aside>

        <!-- ============ Results ============ -->
        <section>
            @if($activeFilterCount > 0)
                <div class="active-chips">
                    @if(filled($categoryFilter))
                        <span class="chip">Category: {{ \Illuminate\Support\Str::headline($categoryFilter) }}
                            <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" aria-label="Remove the category filter">&times;</a>
                        </span>
                    @endif
                    @foreach($activeFilters as $key => $label)
                        <span class="chip">{{ $label }}: {{ \Illuminate\Support\Str::limit((string) $filters[$key], 18) }}
                            <a href="{{ request()->fullUrlWithQuery([$key => null]) }}" aria-label="Remove the {{ strtolower($label) }} filter">&times;</a>
                        </span>
                    @endforeach
                    <a href="{{ route('vehicles.index') }}" class="chips-clear">Clear all</a>
                </div>
            @endif

            <div class="results-bar">
                <p class="count" aria-live="polite">
                    Showing <strong>{{ $vehicles->firstItem() ?? 0 }}–{{ $vehicles->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $vehicles->total() }}</strong> {{ \Illuminate\Support\Str::plural('vehicle', $vehicles->total()) }}
                </p>
                <div class="sort-inline">
                    <label for="top-sort">Sort</label>
                    <select id="top-sort" class="form-control" data-sort-select>
                        @foreach(['newest' => 'Newest first', 'price_asc' => 'Price: low to high', 'price_desc' => 'Price: high to low', 'name_asc' => 'Name: A to Z', 'rating' => 'Top rated'] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['sort'] ?? 'newest') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($vehicles->isEmpty())
                <div class="card card-pad empty-state">
                    <div class="icon"><i class="bi bi-search"></i></div>
                    <h3>No vehicles found</h3>
                    <p>Try widening your dates, price range or removing a filter.</p>
                    <a href="{{ route('vehicles.index') }}" class="btn btn-primary mt-16">Clear all filters</a>
                </div>
            @else
                <div class="grid grid-2">
                    @foreach($vehicles as $vehicle)
                        @include('frontend.partials.vehicle-card', ['vehicle' => $vehicle])
                    @endforeach
                </div>

                <div class="pagination-wrap">{{ $vehicles->links() }}</div>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toggle = document.querySelector('[data-filter-toggle]');
        var panel = document.querySelector('[data-filter-panel]');
        if (toggle && panel) {
            var toggleLabel = toggle.querySelector('[data-filter-toggle-label]');
            var syncToggle = function () {
                var open = panel.classList.contains('is-open');
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (toggleLabel) { toggleLabel.textContent = open ? 'Hide search & filters' : 'Search & filters'; }
            };
            toggle.addEventListener('click', function () {
                panel.classList.toggle('is-open');
                syncToggle();
                if (panel.classList.contains('is-open')) {
                    var first = panel.querySelector('input, select, button');
                    if (first) { first.focus({ preventScroll: true }); }
                }
            });
            // Filters are already applied, so start expanded on mobile.
            if (panel.classList.contains('is-open')) { syncToggle(); }
        }

        // Sorting from the results bar submits the filter form.
        var sortSelect = document.querySelector('[data-sort-select]');
        if (sortSelect) {
            sortSelect.addEventListener('change', function () {
                var url = new URL(window.location.href);
                url.searchParams.set('sort', sortSelect.value);
                window.location.href = url.toString();
            });
        }
        document.querySelectorAll('[data-auto-submit]').forEach(function (el) {
            el.addEventListener('change', function () {
                if (el.form) { el.form.submit(); }
            });
        });

        // Keep the return date after the pickup date.
        var pickup = document.getElementById('f-pickup');
        var ret = document.getElementById('f-return');
        if (pickup && ret) {
            var sync = function () {
                ret.min = pickup.value || '{{ now()->toDateString() }}';
                if (ret.value && pickup.value && ret.value < pickup.value) {
                    ret.value = pickup.value;
                }
            };
            pickup.addEventListener('change', sync);
            sync();
        }
    });
</script>
@endpush
