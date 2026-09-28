@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of bookings, payments and revenue')

@push('styles')
<style>
    .quick-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin: 22px 0; }
    .quick {
        background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 16px 18px;
        display: flex; align-items: center; gap: 14px; color: var(--text);
    }
    .quick:hover { border-color: var(--primary); color: var(--text); }
    .quick .ico {
        width: 42px; height: 42px; border-radius: 12px; background: var(--primary-soft); color: var(--primary);
        display: inline-flex; align-items: center; justify-content: center; font-size: 1.05rem; flex-shrink: 0;
    }
    .quick strong { display: block; font-size: .9rem; }
    .quick span { font-size: .78rem; color: var(--muted); }

    .status-legend { display: grid; gap: 10px; }
    .legend-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; font-size: .86rem; }
    .legend-row .left { display: flex; align-items: center; gap: 10px; }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .legend-track { height: 8px; border-radius: 999px; background: #F1F5F9; overflow: hidden; margin-top: 6px; }
    .legend-track span { display: block; height: 100%; border-radius: 999px; background: var(--primary); }

    @media (max-width: 1150px) { .quick-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 620px) { .quick-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
    <!-- ============ Stat cards ============ -->
    <div class="stat-grid">
        <div class="stat">
            <span class="lbl">Total Vehicles</span>
            <span class="val">{{ $stats['total_vehicles'] }}</span>
            <span class="note">{{ $stats['available_vehicles'] }} available now</span>
        </div>
        <div class="stat">
            <span class="lbl">Total Customers</span>
            <span class="val">{{ $stats['total_customers'] }}</span>
            <span class="note">Registered accounts</span>
        </div>
        <div class="stat">
            <span class="lbl">Total Bookings</span>
            <span class="val">{{ $stats['total_bookings'] }}</span>
            <span class="note">{{ $stats['vehicles_on_rent'] }} currently on rent</span>
        </div>
        <div class="stat highlight">
            <span class="lbl">Total Revenue</span>
            <span class="val">{{ bdt($stats['total_revenue']) }}</span>
            <span class="note">From verified payments only</span>
        </div>
    </div>

    <div class="stat-grid mt-16">
        <div class="stat">
            <span class="lbl">Pending Payments</span>
            <span class="val">{{ $stats['pending_payments'] }}</span>
            <span class="note">Awaiting manual verification</span>
        </div>
        <div class="stat">
            <span class="lbl">Confirmed Bookings</span>
            <span class="val">{{ $stats['confirmed_bookings'] }}</span>
            <span class="note">Confirmed or in progress</span>
        </div>
        <div class="stat">
            <span class="lbl">Completed Rentals</span>
            <span class="val">{{ $stats['completed_rentals'] }}</span>
            <span class="note">Finished successfully</span>
        </div>
        <div class="stat">
            <span class="lbl">Pending Reviews</span>
            <span class="val">{{ $stats['pending_reviews'] }}</span>
            <span class="note">Waiting for moderation</span>
        </div>
    </div>

    <!-- ============ Quick actions ============ -->
    <div class="quick-grid">
        <a href="{{ route('admin.vehicles.create') }}" class="quick">
            <span class="ico"><i class="bi bi-plus-lg"></i></span>
            <span><strong>Add vehicle</strong><span>Expand the fleet</span></span>
        </a>
        <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="quick">
            <span class="ico"><i class="bi bi-check-circle-fill"></i></span>
            <span><strong>Verify payments</strong><span>{{ $stats['pending_payments'] }} waiting</span></span>
        </a>
        <a href="{{ route('admin.bookings.index', ['status' => 'payment_submitted']) }}" class="quick">
            <span class="ico"><i class="bi bi-clipboard-check"></i></span>
            <span><strong>Review bookings</strong><span>Payment submitted queue</span></span>
        </a>
        <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="quick">
            <span class="ico"><i class="bi bi-star-fill"></i></span>
            <span><strong>Moderate reviews</strong><span>{{ $stats['pending_reviews'] }} pending</span></span>
        </a>
    </div>

    <div class="grid grid-sidebar">
        <!-- ============ Revenue chart ============ -->
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Revenue — last 6 months</h3>
                    <p>Verified payments per month</p>
                </div>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline btn-sm">All payments</a>
            </div>
            <div class="card-body">
                @php $maxRevenue = max(array_values($months) ?: [0]) ?: 1; @endphp
                <div class="bar-chart">
                    @foreach($months as $month => $total)
                        <div class="bar-wrap">
                            <strong>{{ bdt($total) }}</strong>
                            <div class="bar" style="height: {{ max(4, (int) round(($total / $maxRevenue) * 130)) }}px;"></div>
                            <span>{{ \Carbon\Carbon::parse($month.'-01')->format('M Y') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ============ Booking status breakdown ============ -->
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Booking status</h3>
                    <p>Distribution across all bookings</p>
                </div>
            </div>
            <div class="card-body">
                @php $totalBookings = array_sum($statusStats) ?: 0; @endphp
                <div class="status-legend">
                    @foreach(\App\Models\Booking::STATUSES as $status)
                        @php
                            $count = $statusStats[$status] ?? 0;
                            $percent = $totalBookings > 0 ? round(($count / $totalBookings) * 100) : 0;
                            $color = match ($status) {
                                'completed' => '#16A34A',
                                'confirmed' => '#2563EB',
                                'ongoing' => '#0EA5E9',
                                'payment_submitted' => '#F59E0B',
                                'pending' => '#A855F7',
                                default => '#DC2626',
                            };
                        @endphp

                        <div>
                            <div class="legend-row">
                                <span class="left">
                                    <span class="legend-dot" style="background: {{ $color }};"></span>
                                    {{ \Illuminate\Support\Str::headline($status) }}
                                </span>
                                <strong>{{ $count }} ({{ $percent }}%)</strong>
                            </div>
                            <div class="legend-track"><span style="width: {{ $percent }}%; background: {{ $color }};"></span></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-sidebar">
        <div>
            <!-- ============ Recent bookings ============ -->
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Recent bookings</h3>
                        <p>Latest six bookings across the platform</p>
                    </div>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline btn-sm">View all</a>
                </div>

                <div class="table-wrap">
                    <table class="data">
                        <thead>
                            <tr>
                                <th>Booking</th>
                                <th>Customer</th>
                                <th>Vehicle</th>
                                <th>Dates</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                                <tr>
                                    <td data-label="Booking"><strong>{{ $booking->booking_code }}</strong></td>
                                    <td data-label="Customer">{{ $booking->user->name }}</td>
                                    <td data-label="Vehicle">{{ $booking->vehicle->name }}</td>
                                    <td class="small" data-label="Dates">
                                        {{ $booking->pickup_date->format('d M') }} → {{ $booking->return_date->format('d M Y') }}
                                    </td>
                                    <td data-label="Total"><strong>{{ bdt($booking->total_amount) }}</strong></td>
                                    <td data-label="Status">{!! status_badge($booking->statusLabel(), $booking->statusClass()) !!}</td>
                                    <td>
                                        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-outline btn-sm">Open</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center muted" style="padding:28px;">No bookings yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ============ Top vehicles ============ -->
            <div class="card">
                <div class="card-head">
                    <div>
                        <h3>Most booked vehicles</h3>
                        <p>Ranked by total bookings</p>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="data">
                        <thead>
                            <tr><th>Vehicle</th><th>Category</th><th>Daily rate</th><th>Bookings</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @forelse($topVehicles as $vehicle)
                                <tr>
                                    <td data-label="Vehicle">
                                        <div class="cell-media">
                                            <img src="{{ $vehicle->imageUrl() }}" alt="{{ $vehicle->name }}">
                                            <span>
                                                <strong>{{ $vehicle->name }}</strong>
                                                <span>{{ $vehicle->registration_number }}</span>
                                            </span>
                                        </div>
                                    </td>
                                    <td data-label="Category">{{ $vehicle->category?->name ?? '—' }}</td>
                                    <td data-label="Daily rate">{{ bdt($vehicle->price_per_day) }}</td>
                                    <td data-label="Bookings"><strong>{{ $vehicle->bookings_count }}</strong></td>
                                    <td data-label="Status">{!! status_badge($vehicle->statusLabel(), $vehicle->statusClass()) !!}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center muted" style="padding:28px;">No vehicles yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ============ Pending payments ============ -->
        <div class="card">
            <div class="card-head">
                <div>
                    <h3>Payments to verify</h3>
                    <p>Manual verification queue</p>
                </div>
            </div>
            <div class="card-body">
                @forelse($pendingPayments as $payment)
                    <div class="flex-between mb-16" style="padding-bottom:16px; border-bottom:1px solid var(--border);">
                        <div style="min-width:0;">
                            <strong style="display:block; font-size:.9rem;">{{ $payment->booking->booking_code }}</strong>
                            <span class="muted small">{{ $payment->user->name }} · {{ $payment->paymentMethod?->name }}</span>
                            <span class="muted small" style="display:block;">TrxID: {{ $payment->transaction_id }}</span>
                        </div>
                        <div class="text-right">
                            <strong>{{ bdt($payment->amount) }}</strong>
                            <div class="mt-8">
                                <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-primary btn-sm">Verify</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="padding:30px 0;">
                        <div class="icon"><i class="bi bi-check-circle-fill"></i></div>
                        <h3>All caught up</h3>
                        <p>No payments are waiting for verification.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
