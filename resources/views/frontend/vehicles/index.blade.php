@extends('frontend.layouts.app')

@section('title', 'Browse Vehicles — '.setting('site_name', 'Rideora'))

@push('styles')
<style>
    .listing-head { background: #fff; border-bottom: 1px solid var(--border); padding: 26px 0; }
    .listing-head h1 { font-size: 1.75rem; }
    .listing-head p { color: var(--muted); font-size: .92rem; }

    .listing-layout { display: grid; grid-template-columns: 290px 1fr; gap: 26px; align-items: start; }

    .filters { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; position: sticky; top: 88px; }
    .filters h3 { font-size: 1rem; margin-bottom: 4px; }
    .filters .sub { color: var(--muted); font-size: .8rem; margin-bottom: 16px; }
    .filter-block { margin-bottom: 16px; }
    .filter-actions { display: flex; gap: 10px; margin-top: 18px; }
    .filter-toggle { display: none; }

    .results-bar { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-bottom: 18px; }
    .results-bar .count { font-size: .9rem; color: var(--muted); }
    .results-bar .count strong { color: var(--dark); }
    .sort-inline { display: flex; align-items: center; gap: 8px; }
    .sort-inline label { font-size: .82rem; color: var(--muted); }

    .active-chips { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
    .chip {
        display: inline-flex; align-items: center; gap: 7px; background: var(--primary-soft); color: var(--primary-dark);
        border-radius: 999px; padding: 5px 12px; font-size: .78rem; font-weight: 600;
    }
    .chip a { color: inherit; opacity: .75; font-weight: 700; }

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

    @media (max-width: 980px) {
        .listing-layout { grid-template-columns: 1fr; gap: 20px; }
        .filters { position: static; display: none; }
        .filters.is-open { display: block; }
        .filter-toggle { display: inline-flex; }
    }

    @media (max-width: 620px) {
        .listing-head { padding: 20px 0; }
        .listing-head h1 { font-size: 1.4rem; }
        .filters { padding: 18px; }
        .results-bar { align-items: flex-start; flex-direction: column; gap: 10px; }
        .sort-inline { width: 100%; }
        .sort-inline select { flex: 1; }
        .grid-2 { gap: 16px; }
    }

    @media (max-width: 420px) {
        .v-card-foot { flex-direction: column; align-items: stretch; gap: 10px; }
        .v-card-foot .btn { width: 100%; }
        .v-card-body { padding: 16px; }
        .filter-actions .btn { width: 100%; }
    }
</style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Browse our fleet</h1>
        <p>{{ $vehicles->total() }} {{ \Illuminate\Support\Str::plural('vehicle', $vehicles->total()) }} matched your search.</p>
    </div>

    <div class="listing-layout">
        <!-- ============ Filters ============ -->
        <aside>
            <button type="button" class="btn btn-outline btn-block filter-toggle mb-16" data-filter-toggle>
                Show search &amp; filters
            </button>

            <div class="filters" data-filter-panel>
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
                        <label for="f-min">Daily price (৳)</label>
                        <div class="flex" style="gap:10px;">
                            <input type="number" id="f-min" name="min_price" class="form-control" min="0" step="100"
                                   placeholder="Min" value="{{ $filters['min_price'] ?? '' }}">
                            <input type="number" id="f-max" name="max_price" class="form-control" min="0" step="100"
                                   placeholder="Max" value="{{ $filters['max_price'] ?? '' }}">
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
                        <button type="submit" class="btn btn-primary btn-block">Apply filters</button>
                    </div>
                    <div class="filter-actions">
                        <a href="{{ route('vehicles.index') }}" class="btn btn-light btn-block">Reset all</a>
                    </div>
                </form>
            </div>
        </aside>

        <!-- ============ Results ============ -->
        <section>
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
            @endphp

            @if($activeFilters->isNotEmpty() || filled($filters['category'] ?? null))
                <div class="active-chips">
                    @if(filled($filters['category'] ?? null))
                        <span class="chip">Category: {{ \Illuminate\Support\Str::headline($filters['category']) }}
                            <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" title="Remove">&times;</a>
                        </span>
                    @endif
                    @foreach($activeFilters as $key => $label)
                        <span class="chip">{{ $label }}: {{ \Illuminate\Support\Str::limit((string) $filters[$key], 18) }}
                            <a href="{{ request()->fullUrlWithQuery([$key => null]) }}" title="Remove">&times;</a>
                        </span>
                    @endforeach
                </div>
            @endif

            <div class="results-bar">
                <div class="count">
                    Showing <strong>{{ $vehicles->firstItem() ?? 0 }}–{{ $vehicles->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $vehicles->total() }}</strong> vehicles
                </div>
                <div class="sort-inline">
                    <label for="top-sort">Sort</label>
                    <select id="top-sort" class="form-control" style="width:auto; padding:8px 30px 8px 12px;" data-sort-select>
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
            toggle.addEventListener('click', function () {
                panel.classList.toggle('is-open');
                toggle.textContent = panel.classList.contains('is-open') ? 'Hide search & filters' : 'Show search & filters';
            });
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
