@extends('frontend.layouts.app')

@section('title', 'My Bookings — '.setting('site_name', 'Rideora'))

@push('styles')
<style>
    .tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
    .tab {
        display: inline-flex; align-items: center; gap: 8px; padding: 9px 15px; border-radius: 999px;
        border: 1px solid var(--border); background: #fff; font-size: .85rem; font-weight: 600; color: var(--muted);
    }
    .tab:hover { border-color: var(--primary); color: var(--primary); }
    .tab.active { background: var(--primary); border-color: var(--primary); color: #fff; }
    .tab .count {
        background: rgba(100, 116, 139, .14); border-radius: 999px; padding: 1px 8px; font-size: .74rem;
    }
    .tab.active .count { background: rgba(255,255,255,.25); }

    .bk-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 18px; }
    .bk-top { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; padding: 14px 20px; background: #FCFDFF; border-bottom: 1px solid var(--border); }
    .bk-code { font-weight: 700; font-size: .92rem; color: var(--dark); letter-spacing: .01em; }
    .bk-body { display: flex; gap: 18px; padding: 20px; align-items: center; flex-wrap: wrap; }
    .bk-thumb { width: 130px; height: 92px; border-radius: 12px; object-fit: cover; background: #EEF2F7; flex-shrink: 0; }
    .bk-info { flex: 1; min-width: 220px; }
    .bk-info h3 { font-size: 1.05rem; margin-bottom: 4px; }
    .bk-meta { display: grid; grid-template-columns: repeat(3, minmax(140px, 1fr)); gap: 12px; margin-top: 12px; }
    .bk-meta div span { display: block; font-size: .72rem; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); }
    .bk-meta div strong { font-size: .88rem; font-weight: 600; color: var(--dark); }
    .bk-actions { display: flex; flex-direction: column; gap: 8px; align-items: stretch; min-width: 140px; }
    .bk-total { text-align: right; }
    .bk-total strong { font-size: 1.2rem; color: var(--dark); display: block; }
    @media (max-width: 720px) {
        .bk-meta { grid-template-columns: 1fr 1fr; }
        .bk-actions { width: 100%; flex-direction: row; flex-wrap: wrap; }
        .bk-actions > * { flex: 1 1 46%; }

        /* Let the status tabs scroll sideways instead of wrapping */
        .tabs { flex-wrap: nowrap; overflow-x: auto; padding-bottom: 6px; -webkit-overflow-scrolling: touch; }
        .tab { flex-shrink: 0; }
    }

    @media (max-width: 560px) {
        .bk-body { padding: 16px; gap: 14px; }
        .bk-thumb { width: 100%; height: 170px; }
        .bk-top { padding: 13px 16px; }
        .bk-info { min-width: 0; }
    }

    @media (max-width: 420px) {
        .bk-meta { grid-template-columns: 1fr; }
        .bk-actions > * { flex: 1 1 100%; }
    }
</style>
@endpush

@section('content')
    <div class="page-header">
        <h1>My bookings</h1>
        <p>Track every rental, payment status and confirmation in one place.</p>
    </div>

    <div class="tabs">
        <a href="{{ route('bookings.index') }}" class="tab {{ ! $status ? 'active' : '' }}">
            All <span class="count">{{ $counts['all'] }}</span>
        </a>
        <a href="{{ route('bookings.index', ['status' => 'pending']) }}" class="tab {{ $status === 'pending' ? 'active' : '' }}">
            Pending <span class="count">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('bookings.index', ['status' => 'payment_submitted']) }}" class="tab {{ $status === 'payment_submitted' ? 'active' : '' }}">
            Payment submitted <span class="count">{{ $counts['payment_submitted'] }}</span>
        </a>
        <a href="{{ route('bookings.index', ['status' => 'confirmed']) }}" class="tab {{ $status === 'confirmed' ? 'active' : '' }}">
            Confirmed <span class="count">{{ $counts['confirmed'] }}</span>
        </a>
        <a href="{{ route('bookings.index', ['status' => 'completed']) }}" class="tab {{ $status === 'completed' ? 'active' : '' }}">
            Completed <span class="count">{{ $counts['completed'] }}</span>
        </a>
        <a href="{{ route('bookings.index', ['status' => 'cancelled']) }}" class="tab {{ $status === 'cancelled' ? 'active' : '' }}">
            Cancelled <span class="count">{{ $counts['cancelled'] }}</span>
        </a>
    </div>

    @forelse($bookings as $booking)
        <article class="bk-card">
            <div class="bk-top">
                <div class="flex flex-wrap" style="gap:14px; align-items:center;">
                    <span class="bk-code">{{ $booking->booking_code }}</span>
                    <span class="muted small">Booked {{ $booking->created_at->format('d M Y') }}</span>
                </div>
                <div class="flex flex-wrap" style="gap:8px;">
                    {!! status_badge($booking->statusLabel(), $booking->statusClass()) !!}
                    {!! status_badge($booking->paymentStatusLabel(), $booking->paymentStatusClass()) !!}
                </div>
            </div>

            <div class="bk-body">
                <img class="bk-thumb" src="{{ $booking->vehicle->imageUrl() }}" alt="{{ $booking->vehicle->name }}">

                <div class="bk-info">
                    <h3>{{ $booking->vehicle->name }}</h3>
                    <p class="muted small">
                        {{ $booking->vehicle->brand }} · {{ $booking->vehicle->transmission }} · {{ $booking->vehicle->seats }} seats
                    </p>

                    <div class="bk-meta">
                        <div>
                            <span>Pickup</span>
                            <strong>{{ $booking->pickup_date->format('d M Y') }} {{ $booking->pickup_time ? \Illuminate\Support\Str::substr($booking->pickup_time, 0, 5) : '' }}</strong>
                        </div>
                        <div>
                            <span>Return</span>
                            <strong>{{ $booking->return_date->format('d M Y') }} {{ $booking->return_time ? \Illuminate\Support\Str::substr($booking->return_time, 0, 5) : '' }}</strong>
                        </div>
                        <div>
                            <span>Duration</span>
                            <strong>{{ $booking->durationLabel() }}</strong>
                        </div>
                        <div>
                            <span>Pickup location</span>
                            <strong>{{ $booking->pickup_location }}</strong>
                        </div>
                        <div>
                            <span>Total</span>
                            <strong>{{ bdt($booking->total_amount) }}</strong>
                        </div>
                        <div>
                            <span>Payment</span>
                            <strong>{{ $booking->paymentStatusLabel() }}</strong>
                        </div>
                    </div>
                </div>

                <div class="bk-actions">
                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline btn-sm">View details</a>

                    @if($booking->canBePaid())
                        <a href="{{ route('bookings.payment', $booking) }}" class="btn btn-primary btn-sm">Pay now</a>
                    @endif

                    @if($booking->canBeCancelled())
                        <form method="POST" action="{{ route('bookings.cancel', $booking) }}"
                              data-confirm="Cancel booking {{ $booking->booking_code }}? This cannot be undone.">
                            @csrf
                            <button type="submit" class="btn btn-danger-soft btn-sm btn-block">Cancel booking</button>
                        </form>
                    @endif

                    @if($booking->canBeReviewed())
                        <a href="{{ route('reviews.index') }}" class="btn btn-success btn-sm">Leave review</a>
                    @endif
                </div>
            </div>
        </article>
    @empty
        <div class="card card-pad empty-state">
            <div class="icon"><i class="bi bi-car-front"></i></div>
            <h3>No bookings found</h3>
            <p>{{ $status ? 'No bookings with this status yet.' : 'You have not booked a vehicle yet.' }}</p>
            <a href="{{ route('vehicles.index') }}" class="btn btn-primary mt-16">Browse vehicles</a>
        </div>
    @endforelse

    <div class="pagination-wrap">{{ $bookings->links() }}</div>
@endsection
