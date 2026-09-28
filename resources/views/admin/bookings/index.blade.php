@extends('admin.layouts.app')

@section('title', 'Bookings')
@section('page-title', 'Bookings')
@section('page-subtitle', $bookings->total().' bookings matched')

@push('styles')
<style>
    /* Bookings list — page specific */
    .stat-link { color: inherit; text-decoration: none; }
    .stat-link:hover { border-color: var(--primary); color: inherit; }
    .stat-link:hover .note { color: var(--primary); }
    .mini-select { padding: 6px 26px 6px 9px; font-size: .78rem; }
    .cell-double strong { display: block; font-size: .86rem; }
    .cell-double span { font-size: .78rem; color: var(--muted); display: block; }
</style>
@endpush

@section('content')
    <!-- ============ Status summary ============ -->
    <div class="stat-grid mb-24">
        @foreach(\App\Models\Booking::STATUSES as $status)
            <a href="{{ route('admin.bookings.index', ['status' => $status]) }}" class="stat stat-link">
                <span class="lbl">{{ \Illuminate\Support\Str::headline($status) }}</span>
                <span class="val">{{ $statusCounts[$status] ?? 0 }}</span>
                <span class="note">Click to filter</span>
            </a>
        @endforeach
    </div>

    <!-- ============ Filters ============ -->
    <div class="filter-bar">
        <form method="GET" action="{{ route('admin.bookings.index') }}">
            <div class="form-group">
                <label for="q">Search</label>
                <input type="text" id="q" name="q" class="form-control" value="{{ $filters['q'] ?? '' }}"
                       placeholder="Booking code, customer, vehicle">
            </div>

            <div class="form-group">
                <label for="status">Booking status</label>
                <select id="status" name="status" class="form-control">
                    <option value="">Any status</option>
                    @foreach(\App\Models\Booking::STATUSES as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ \Illuminate\Support\Str::headline($status) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="payment_status">Payment status</label>
                <select id="payment_status" name="payment_status" class="form-control">
                    <option value="">Any payment</option>
                    @foreach(\App\Models\Booking::PAYMENT_STATUSES as $paymentStatus)
                        <option value="{{ $paymentStatus }}" @selected(($filters['payment_status'] ?? '') === $paymentStatus)>
                            {{ \Illuminate\Support\Str::headline($paymentStatus) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="from">Pickup from</label>
                <input type="date" id="from" name="from" class="form-control" value="{{ $filters['from'] ?? '' }}">
            </div>

            <div class="form-group">
                <label for="to">Pickup to</label>
                <input type="date" id="to" name="to" class="form-control" value="{{ $filters['to'] ?? '' }}">
            </div>

            <div class="form-group">
                <label>&nbsp;</label>
                <div class="flex" style="gap:10px;">
                    <button type="submit" class="btn btn-primary">Apply</button>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-light">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-head">
            <div>
                <h3>All bookings</h3>
                <p>Change statuses inline or open a booking for full details</p>
            </div>
        </div>

        <div class="table-wrap">
            <table class="data">
                <thead>
                    <tr>
                        <th>Booking</th>
                        <th>Customer</th>
                        <th>Vehicle</th>
                        <th>Rental window</th>
                        <th>Total</th>
                        <th>Booking status</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>
                                <strong>{{ $booking->booking_code }}</strong>
                                <span class="muted small" style="display:block;">{{ $booking->created_at->format('d M Y') }}</span>
                            </td>
                            <td>
                                <div class="cell-double">
                                    <strong>{{ $booking->user->name }}</strong>
                                    <span>{{ $booking->user->phone ?: '—' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="cell-double">
                                    <strong>{{ $booking->vehicle->name }}</strong>
                                    <span>{{ $booking->vehicle->registration_number }}</span>
                                </div>
                            </td>
                            <td class="small">
                                {{ $booking->pickup_date->format('d M Y') }}<br>
                                → {{ $booking->return_date->format('d M Y') }}<br>
                                <span class="muted">{{ $booking->durationLabel() }}</span>
                            </td>
                            <td>
                                <strong>{{ bdt($booking->total_amount) }}</strong>
                                <span class="muted small" style="display:block;">{{ $booking->paymentStatusLabel() }}</span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.bookings.status', $booking) }}">
                                    @csrf
                                    <select name="booking_status" class="form-control mini-select" onchange="this.form.submit()">
                                        @foreach(\App\Models\Booking::STATUSES as $status)
                                            <option value="{{ $status }}" @selected($booking->booking_status === $status)>
                                                {{ \Illuminate\Support\Str::headline($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td>{!! status_badge($booking->paymentStatusLabel(), $booking->paymentStatusClass()) !!}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-outline btn-sm">Open</a>
                                    @if(! in_array($booking->booking_status, \App\Models\Booking::BLOCKING_STATUSES, true))
                                        <form method="POST" action="{{ route('admin.bookings.destroy', $booking) }}"
                                              data-confirm="Delete booking {{ $booking->booking_code }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger-soft btn-sm">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center muted" style="padding:34px;">No bookings matched these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="card-foot pagination-wrap">{{ $bookings->links() }}</div>
        @endif
    </div>
@endsection
