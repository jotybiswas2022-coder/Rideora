@extends('admin.layouts.app')

@section('title', 'Manage Vehicles')
@section('page-title', 'Vehicles')
@section('page-subtitle', $vehicles->total().' vehicles in the fleet')

@push('styles')
<style>
    /* Vehicle list — page specific */
    .mini-select { padding: 6px 26px 6px 9px; font-size: .76rem; margin-top: 8px; }
    .cell-specs { font-size: .8rem; color: var(--muted); line-height: 1.5; }
    .cell-price strong { display: block; }
    .cell-price span { font-size: .78rem; color: var(--muted); display: block; }
</style>
@endpush

@section('content')
    <!-- ============ Filters ============ -->
    <div class="filter-bar">
        <form method="GET" action="{{ route('admin.vehicles.index') }}">
            <div class="form-group">
                <label for="q">Search</label>
                <input type="text" id="q" name="q" class="form-control" value="{{ $filters['q'] ?? '' }}"
                       placeholder="Name, brand or registration">
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" class="form-control">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) ($filters['category'] ?? '') === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="">Any status</option>
                    @foreach(\App\Models\Vehicle::STATUSES as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="sort">Sort</label>
                <div class="flex" style="gap:10px;">
                    <select id="sort" name="sort" class="form-control">
                        @foreach(['newest' => 'Newest', 'price_asc' => 'Price ↑', 'price_desc' => 'Price ↓', 'name_asc' => 'Name A-Z'] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['sort'] ?? 'newest') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-head">
            <div>
                <h3>Fleet list</h3>
                <p>Update pricing, availability and images per vehicle</p>
            </div>
            <div class="flex" style="gap:10px;">
                <a href="{{ route('admin.vehicles.index') }}" class="btn btn-light btn-sm">Reset</a>
                <a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary btn-sm">+ Add vehicle</a>
            </div>
        </div>

        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>Category</th>
                        <th>Specs</th>
                        <th>Pricing</th>
                        <th>Bookings</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $vehicle)
                        <tr>
                            <td data-label="Vehicle">
                                <div class="cell-media">
                                    <img src="{{ $vehicle->imageUrl() }}" alt="{{ $vehicle->name }}">
                                    <span>
                                        <strong>{{ $vehicle->name }}</strong>
                                        <span>{{ $vehicle->brand }} · {{ $vehicle->registration_number }}</span>
                                    </span>
                                </div>
                            </td>
                            <td data-label="Category">{{ $vehicle->category?->name ?? '—' }}</td>
                            <td data-label="Specs">
                                <div class="cell-specs">
                                    {{ $vehicle->transmission }}<br>
                                    {{ $vehicle->fuel_type }} · {{ $vehicle->seats }} seats
                                </div>
                            </td>
                            <td data-label="Pricing">
                                <div class="cell-price">
                                    <strong>{{ bdt($vehicle->price_per_day) }} / day</strong>
                                    <span>{{ bdt($vehicle->price_per_hour) }} / hour</span>
                                    <span>Deposit {{ bdt($vehicle->security_deposit) }}</span>
                                </div>
                            </td>
                            <td data-label="Bookings"><strong>{{ $vehicle->bookings_count }}</strong></td>
                            <td data-label="Status">
                                {!! status_badge($vehicle->statusLabel(), $vehicle->statusClass()) !!}

                                <form method="POST" action="{{ route('admin.vehicles.status', $vehicle) }}" class="mt-8">
                                    @csrf
                                    <select name="status" class="form-control mini-select" onchange="this.form.submit()">
                                        @foreach(\App\Models\Vehicle::STATUSES as $status)
                                            <option value="{{ $status }}" @selected($vehicle->status === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td data-label="Actions">
                                <div class="actions">
                                    <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="btn btn-light btn-sm">View</a>
                                    <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-outline btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}"
                                          data-confirm="Delete {{ $vehicle->name }}? This also removes its images.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger-soft btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center muted" style="padding:34px;">
                                No vehicles matched your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vehicles->hasPages())
            <div class="card-foot pagination-wrap">{{ $vehicles->links() }}</div>
        @endif
    </div>
@endsection
