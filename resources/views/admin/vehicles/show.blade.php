@extends('admin.layouts.app')

@section('title', $vehicle->name)
@section('page-title', $vehicle->name)
@section('page-subtitle', $vehicle->brand.' · '.$vehicle->registration_number)

@push('styles')
<style>
    .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 14px; }
    .gallery-grid figure { border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; background: #fff; }
    .gallery-grid img { width: 100%; height: 130px; object-fit: cover; }
    .gallery-grid figcaption { padding: 9px 11px; font-size: .76rem; display: flex; justify-content: space-between; gap: 8px; align-items: center; }
    .spec-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
    .spec { background: var(--light); border: 1px solid var(--border); border-radius: var(--radius); padding: 13px 15px; }
    .spec span.lbl { display: block; font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); margin-bottom: 3px; }
    .spec strong { font-size: .9rem; color: var(--dark); }
    @media (max-width: 760px) { .spec-grid { grid-template-columns: 1fr 1fr; } }
</style>
@endpush

@section('content')
    <div class="flex-between mb-24">
        <div class="flex-center">
            {!! status_badge($vehicle->statusLabel(), $vehicle->statusClass()) !!}
            {!! status_badge($vehicle->category?->name ?? 'Uncategorised', 'badge-primary') !!}
            <span class="muted small">{{ $vehicle->bookings_count }} bookings · {{ $vehicle->reviews_count }} reviews</span>
        </div>
        <div class="flex" style="gap:8px;">
            <a href="{{ route('vehicles.show', $vehicle) }}" target="_blank" rel="noopener" class="btn btn-light btn-sm">View on site</a>
            <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-primary btn-sm">Edit vehicle</a>
        </div>
    </div>

    <div class="grid grid-sidebar">
        <div>
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Images</h3>
                        <p>{{ $vehicle->images->count() }} uploaded</p>
                    </div>
                </div>
                <div class="card-body">
                    @if($vehicle->images->isEmpty())
                        <div class="empty-state" style="padding:26px 0;">
                            <div class="icon"><i class="bi bi-camera"></i></div>
                            <p>No images uploaded for this vehicle yet.</p>
                            <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-primary mt-16">Upload images</a>
                        </div>
                    @else
                        <div class="gallery-grid">
                            @foreach($vehicle->images as $image)
                                <figure>
                                    <img src="{{ $image->url() }}" alt="{{ $vehicle->name }}">
                                    <figcaption>
                                        @if($image->is_primary)
                                            {!! status_badge('Primary', 'badge-success') !!}
                                        @else
                                            <span class="muted">Gallery image</span>
                                            <form method="POST" action="{{ route('admin.vehicles.image.primary', [$vehicle, $image]) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-light btn-sm">Set primary</button>
                                            </form>
                                        @endif
                                    </figcaption>
                                </figure>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Recent bookings for this vehicle</h3>
                        <p>Last eight rentals</p>
                    </div>
                    <a href="{{ route('admin.bookings.index', ['q' => $vehicle->registration_number]) }}" class="btn btn-outline btn-sm">All bookings</a>
                </div>

                <div class="table-wrap">
                    <table class="data">
                        <thead>
                            <tr><th>Booking</th><th>Customer</th><th>Dates</th><th>Total</th><th>Status</th><th></th></tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                                <tr>
                                    <td data-label="Booking"><strong>{{ $booking->booking_code }}</strong></td>
                                    <td data-label="Customer">{{ $booking->user->name }}</td>
                                    <td class="small" data-label="Dates">{{ $booking->pickup_date->format('d M Y') }} → {{ $booking->return_date->format('d M Y') }}</td>
                                    <td data-label="Total">{{ bdt($booking->total_amount) }}</td>
                                    <td data-label="Status">{!! status_badge($booking->statusLabel(), $booking->statusClass()) !!}</td>
                                    <td><a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-light btn-sm">Open</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center muted" style="padding:26px;">No bookings for this vehicle yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Description</h3>
                    </div>
                </div>
                <div class="card-body">
                    <p class="small">{{ $vehicle->description ?: 'No description provided.' }}</p>
                </div>
            </div>
        </div>

        <aside>
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Specifications</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="spec-grid">
                        <div class="spec"><span class="lbl">Type</span><strong>{{ $vehicle->vehicle_type }}</strong></div>
                        <div class="spec"><span class="lbl">Fuel</span><strong>{{ $vehicle->fuel_type }}</strong></div>
                        <div class="spec"><span class="lbl">Gearbox</span><strong>{{ $vehicle->transmission }}</strong></div>
                        <div class="spec"><span class="lbl">Seats</span><strong>{{ $vehicle->seats }}</strong></div>
                        <div class="spec"><span class="lbl">Location</span><strong>{{ $vehicle->location ?: '—' }}</strong></div>
                        <div class="spec"><span class="lbl">Model</span><strong>{{ $vehicle->model ?: '—' }}</strong></div>
                    </div>

                    <div class="divider"></div>

                    <div class="summary-list">
                        <div class="summary-row"><span>Price per day</span><strong>{{ bdt($vehicle->price_per_day) }}</strong></div>
                        <div class="summary-row"><span>Price per hour</span><strong>{{ bdt($vehicle->price_per_hour) }}</strong></div>
                        <div class="summary-row total"><span>Security deposit</span><span>{{ bdt($vehicle->security_deposit) }}</span></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Update status</h3>
                        <p>Availability and maintenance flags</p>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.vehicles.status', $vehicle) }}" class="stack-8">
                        @csrf
                        <select name="status" class="form-control">
                            @foreach(\App\Models\Vehicle::STATUSES as $status)
                                <option value="{{ $status }}" @selected($vehicle->status === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-block">Save status</button>
                    </form>

                    <div class="divider"></div>

                    <p class="muted small">Created {{ $vehicle->created_at->format('d M Y, g:i A') }}</p>
                    <p class="muted small">Last updated {{ $vehicle->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </aside>
    </div>
@endsection
