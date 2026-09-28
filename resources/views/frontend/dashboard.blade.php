@extends('frontend.layouts.app')

@section('title', 'Dashboard — '.setting('site_name', 'Rideora'))

@push('styles')
<style>
    .dash-head {
        background: linear-gradient(140deg, #0F172A, #1E3A8A); color: #fff; border-radius: var(--radius-lg);
        padding: 30px 32px; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;
    }
    .dash-head h1 { color: #fff; font-size: 1.55rem; margin-bottom: 5px; }
    .dash-head p { color: #C7D2E5; font-size: .9rem; }
    .stat-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin: 24px 0; }
    .stat-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 18px; }
    .stat-card .lbl { font-size: .74rem; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); }
    .stat-card .val { font-size: 1.7rem; font-weight: 800; color: var(--dark); line-height: 1.2; margin-top: 6px; }
    .stat-card .note { font-size: .78rem; color: var(--muted); }
    .booking-row { display: flex; align-items: center; gap: 16px; padding: 15px 0; border-bottom: 1px solid var(--border); }
    .booking-row:last-child { border-bottom: none; }
    .booking-thumb { width: 78px; height: 56px; border-radius: 10px; object-fit: cover; background: #EEF2F7; flex-shrink: 0; }
    .booking-main { flex: 1; min-width: 0; }
    .booking-main strong { display: block; font-size: .95rem; }
    .booking-main .meta { font-size: .8rem; color: var(--muted); }
    .booking-side { text-align: right; display: grid; gap: 6px; justify-items: end; }
    .notice-item { display: flex; gap: 12px; padding: 13px 0; border-bottom: 1px solid var(--border); }
    .notice-item:last-child { border-bottom: none; }
    .notice-ico {
        width: 36px; height: 36px; border-radius: 10px; background: var(--primary-soft); color: var(--primary);
        display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .notice-item strong { font-size: .88rem; display: block; }
    .notice-item p { font-size: .82rem; color: var(--muted); }

    .dash-grid { display: grid; grid-template-columns: 1.4fr 1fr; gap: 22px; }

    @media (max-width: 1050px) {
        .stat-grid { grid-template-columns: repeat(3, 1fr); }
        .dash-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 700px) {
        .stat-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .stat-card { padding: 15px; }
        .stat-card .val { font-size: 1.45rem; }
        .dash-head { padding: 22px; }
        .dash-head h1 { font-size: 1.3rem; }
        .dash-head .btn { width: 100%; }
        .booking-row { flex-wrap: wrap; gap: 12px; }
        .booking-thumb { width: 64px; height: 46px; }
        .booking-main { flex: 1 1 55%; }
        .booking-side { width: 100%; text-align: left; justify-items: start; }
        .booking-side .btn { width: 100%; }
    }

    @media (max-width: 420px) {
        .stat-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
    <div class="dash-head">
        <div>
            <h1>Hello, {{ auth()->user()->name }}</h1>
            <p>Here is a quick look at your rentals, payments and notifications.</p>
        </div>
        <div class="flex flex-wrap" style="gap:10px;">
            <a href="{{ route('vehicles.index') }}" class="btn btn-lg" style="background:#fff; color: var(--primary);">Book a vehicle</a>
            <a href="{{ route('bookings.index') }}" class="btn btn-outline btn-lg" style="background:transparent; color:#fff; border-color: rgba(255,255,255,.4);">All bookings</a>
        </div>
    </div>

    <div class="stat-grid">
        <div class="stat-card">
            <span class="lbl">Total bookings</span>
            <div class="val">{{ $stats['total'] }}</div>
            <span class="note">Lifetime rentals</span>
        </div>
        <div class="stat-card">
            <span class="lbl">Pending payments</span>
            <div class="val">{{ $stats['pending_payments'] }}</div>
            <span class="note">Awaiting payment or verification</span>
        </div>
        <div class="stat-card">
            <span class="lbl">Confirmed</span>
            <div class="val">{{ $stats['confirmed'] }}</div>
            <span class="note">Ready or in progress</span>
        </div>
        <div class="stat-card">
            <span class="lbl">Completed</span>
            <div class="val">{{ $stats['completed'] }}</div>
            <span class="note">Finished rentals</span>
        </div>
        <div class="stat-card">
            <span class="lbl">Cancelled</span>
            <div class="val">{{ $stats['cancelled'] }}</div>
            <span class="note">Paid total: {{ bdt($stats['spent']) }}</span>
        </div>
    </div>

    <div class="dash-grid">
        <!-- ===== Recent bookings ===== -->
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Recent bookings</h3>
                    <p>Your latest five rentals</p>
                </div>
                <a href="{{ route('bookings.index') }}" class="btn btn-outline btn-sm">View all</a>
            </div>

            <div class="card-body">
                @forelse($recentBookings as $booking)
                    <div class="booking-row">
                        <img class="booking-thumb" src="{{ $booking->vehicle->imageUrl() }}" alt="{{ $booking->vehicle->name }}">
                        <div class="booking-main">
                            <strong>{{ $booking->vehicle->name }}</strong>
                            <div class="meta">
                                {{ $booking->booking_code }} ·
                                {{ $booking->pickup_date->format('d M Y') }} → {{ $booking->return_date->format('d M Y') }}
                            </div>
                            <div class="flex flex-wrap mt-8" style="gap:6px;">
                                {!! status_badge($booking->statusLabel(), $booking->statusClass()) !!}
                                {!! status_badge($booking->paymentStatusLabel(), $booking->paymentStatusClass()) !!}
                            </div>
                        </div>
                        <div class="booking-side">
                            <strong>{{ bdt($booking->total_amount) }}</strong>
                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline btn-sm">Details</a>
                            @if($booking->canBePaid())
                                <a href="{{ route('bookings.payment', $booking) }}" class="btn btn-primary btn-sm">Pay now</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="icon">&#128663;</div>
                        <h3>No bookings yet</h3>
                        <p>Browse the fleet and book your first ride.</p>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-primary mt-16">Browse vehicles</a>
                    </div>
                @endforelse
            </div>
        </div>

        <div>
            <!-- ===== Review prompts ===== -->
            @if($reviewableBookings->isNotEmpty())
                <div class="card mb-16">
                    <div class="card-head">
                        <div>
                            <h3>Share your feedback</h3>
                            <p>Completed rentals waiting for a review</p>
                        </div>
                    </div>
                    <div class="card-body">
                        @foreach($reviewableBookings as $booking)
                            <div class="booking-row">
                                <img class="booking-thumb" src="{{ $booking->vehicle->imageUrl() }}" alt="{{ $booking->vehicle->name }}">
                                <div class="booking-main">
                                    <strong>{{ $booking->vehicle->name }}</strong>
                                    <div class="meta">Completed {{ $booking->updated_at->diffForHumans() }}</div>
                                </div>
                                <a href="{{ route('reviews.index') }}" class="btn btn-primary btn-sm">Review</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- ===== Notifications ===== -->
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Notifications</h3>
                        <p>Latest updates on your account</p>
                    </div>
                    <a href="{{ route('notifications.index') }}" class="btn btn-outline btn-sm">All</a>
                </div>
                <div class="card-body">
                    @forelse($unreadNotifications as $notification)
                        <div class="notice-item">
                            <span class="notice-ico">{!! $notification->icon() !!}</span>
                            <div>
                                <strong>{{ $notification->title }}</strong>
                                <p>{{ \Illuminate\Support\Str::limit($notification->message, 90) }}</p>
                                <span class="muted small">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state" style="padding: 24px 0;">
                            <div class="icon">&#128276;</div>
                            <p>No new notifications.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
