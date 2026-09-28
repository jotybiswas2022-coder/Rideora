@extends('frontend.layouts.app')

@section('title', 'Booking '.$booking->booking_code.' — '.setting('site_name', 'Rideora'))

@push('styles')
<style>
    .bk-layout { display: grid; grid-template-columns: 1.55fr 1fr; gap: 26px; align-items: start; }
    .panel { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; }
    .panel + .panel { margin-top: 22px; }
    .panel h2 { font-size: 1.1rem; margin-bottom: 16px; }
    .bk-hero {
        background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 22px;
    }
    .bk-hero-top { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; padding: 18px 24px; border-bottom: 1px solid var(--border); }
    .bk-hero-top h1 { font-size: 1.3rem; }
    .detail-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
    .detail {
        background: var(--light); border: 1px solid var(--border); border-radius: var(--radius); padding: 13px 15px;
    }
    .detail span.lbl { display: block; font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); margin-bottom: 3px; }
    .detail strong { font-size: .92rem; color: var(--dark); }
    .vehicle-row { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; }
    .vehicle-row img { width: 132px; height: 92px; object-fit: cover; border-radius: 12px; background: #EEF2F7; }
    .pay-item { border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; margin-bottom: 12px; }
    .pay-item:last-child { margin-bottom: 0; }
    .pay-item .top { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 10px; }
    .timeline { list-style: none; display: grid; gap: 0; position: relative; }
    .timeline li { padding: 0 0 18px 26px; position: relative; font-size: .87rem; }
    .timeline li::before {
        content: ''; position: absolute; left: 5px; top: 6px; width: 10px; height: 10px; border-radius: 50%;
        background: var(--primary); box-shadow: 0 0 0 3px var(--primary-soft);
    }
    .timeline li::after { content: ''; position: absolute; left: 9px; top: 18px; bottom: 0; width: 2px; background: var(--border); }
    .timeline li:last-child { padding-bottom: 0; }
    .timeline li:last-child::after { display: none; }
    .timeline strong { display: block; }
    .sticky-side { position: sticky; top: 88px; }
    @media (max-width: 960px) {
        .bk-layout { grid-template-columns: 1fr; gap: 18px; }
        .sticky-side { position: static; }
    }

    @media (max-width: 620px) {
        .detail-grid { grid-template-columns: 1fr; }
        .panel { padding: 18px; }
        .panel + .panel { margin-top: 16px; }
        .bk-hero-top { padding: 15px 18px; }
        .bk-hero-top h1 { font-size: 1.12rem; }
        .bk-hero .card-body { padding: 18px; }
        .vehicle-row img { width: 100%; height: 170px; }
        .pay-item { padding: 14px; }
        .panel .stack-8 .btn { width: 100%; }
    }

    @media (max-width: 420px) {
        .timeline li { padding-left: 22px; }
        .pay-item .top { flex-direction: column; align-items: flex-start; }
    }
</style>
@endpush

@section('content')
    <div class="breadcrumbs mb-16" style="font-size:.82rem; color:var(--muted);">
        <a href="{{ route('bookings.index') }}">&larr; Back to my bookings</a>
    </div>

    <div class="bk-layout">
        <div>
            <!-- ===== Header ===== -->
            <div class="bk-hero">
                <div class="bk-hero-top">
                    <div>
                        <h1>Booking {{ $booking->booking_code }}</h1>
                        <span class="muted small">Created {{ $booking->created_at->format('d M Y, g:i A') }}</span>
                    </div>
                    <div class="flex flex-wrap" style="gap:8px;">
                        {!! status_badge($booking->statusLabel(), $booking->statusClass()) !!}
                        {!! status_badge($booking->paymentStatusLabel(), $booking->paymentStatusClass()) !!}
                    </div>
                </div>

                <div class="card-body">
                    <div class="vehicle-row">
                        <img src="{{ $booking->vehicle->imageUrl() }}" alt="{{ $booking->vehicle->name }}">
                        <div style="flex:1; min-width:200px;">
                            <h2 style="font-size:1.1rem; margin-bottom:4px;">{{ $booking->vehicle->name }}</h2>
                            <p class="muted small">
                                {{ $booking->vehicle->brand }}{{ $booking->vehicle->model ? ' · '.$booking->vehicle->model : '' }} ·
                                {{ $booking->vehicle->transmission }} · {{ $booking->vehicle->fuel_type }} · {{ $booking->vehicle->seats }} seats
                            </p>
                            <a href="{{ route('vehicles.show', $booking->vehicle) }}" class="btn btn-outline btn-sm mt-8">View vehicle</a>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="detail-grid">
                        <div class="detail">
                            <span class="lbl">Pickup</span>
                            <strong>{{ $booking->pickup_date->format('D, d M Y') }}</strong>
                            <div class="muted small">{{ $booking->pickup_time ? \Illuminate\Support\Str::substr($booking->pickup_time, 0, 5) : '—' }} · {{ $booking->pickup_location }}</div>
                        </div>
                        <div class="detail">
                            <span class="lbl">Return</span>
                            <strong>{{ $booking->return_date->format('D, d M Y') }}</strong>
                            <div class="muted small">{{ $booking->return_time ? \Illuminate\Support\Str::substr($booking->return_time, 0, 5) : '—' }} · {{ $booking->dropoff_location ?: $booking->pickup_location }}</div>
                        </div>
                        <div class="detail">
                            <span class="lbl">Duration</span>
                            <strong>{{ $booking->durationLabel() }}</strong>
                            <div class="muted small">{{ $booking->rental_days }} day(s), {{ $booking->rental_hours }} hour(s)</div>
                        </div>
                        <div class="detail">
                            <span class="lbl">Registration</span>
                            <strong>{{ $booking->vehicle->registration_number }}</strong>
                            <div class="muted small">{{ $booking->vehicle->location ?: 'Flexible pickup' }}</div>
                        </div>
                    </div>

                    @if($booking->customer_note)
                        <div class="alert alert-info mt-24 mb-8">
                            <span><i class="bi bi-info-circle-fill"></i></span>
                            <div><strong>Your note:</strong> {{ $booking->customer_note }}</div>
                        </div>
                    @endif

                    @if($booking->admin_note)
                        <div class="alert alert-warning mt-16 mb-8">
                            <span><i class="bi bi-exclamation-triangle-fill"></i></span>
                            <div><strong>Note from Rideora:</strong> {{ $booking->admin_note }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ===== Payment history ===== -->
            <div class="panel">
                <h2>Payment history</h2>

                @forelse($booking->payments as $historyPayment)
                    <div class="pay-item">
                        <div class="top">
                            <div>
                                <strong>{{ $historyPayment->paymentMethod?->name ?? 'Manual payment' }}</strong>
                                <div class="muted small">Transaction ID: {{ $historyPayment->transaction_id }}</div>
                            </div>
                            <div class="text-right">
                                <strong>{{ bdt($historyPayment->amount) }}</strong>
                                <div>{!! status_badge($historyPayment->statusLabel(), $historyPayment->statusClass()) !!}</div>
                            </div>
                        </div>

                        <div class="muted small">
                            Submitted {{ $historyPayment->created_at->format('d M Y, g:i A') }}
                            @if($historyPayment->verified_at)
                                · Reviewed {{ $historyPayment->verified_at->format('d M Y, g:i A') }}
                                @if($historyPayment->verifier) by {{ $historyPayment->verifier->name }} @endif
                            @endif
                        </div>

                        @if($historyPayment->admin_note)
                            <div class="muted small mt-8"><strong>Admin note:</strong> {{ $historyPayment->admin_note }}</div>
                        @endif

                        @if($historyPayment->proofUrl())
                            <a href="{{ $historyPayment->proofUrl() }}" target="_blank" rel="noopener" class="btn btn-light btn-sm mt-8">View payment proof</a>
                        @endif
                    </div>
                @empty
                    <div class="empty-state" style="padding:28px 0;">
                        <div class="icon"><i class="bi bi-cash-stack"></i></div>
                        <h3>No payment submitted yet</h3>
                        <p>Complete the manual payment to confirm this booking.</p>
                        @if($booking->canBePaid())
                            <a href="{{ route('bookings.payment', $booking) }}" class="btn btn-primary mt-16">Go to payment page</a>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- ===== Timeline ===== -->
            <div class="panel">
                <h2>Booking timeline</h2>
                <ul class="timeline">
                    <li>
                        <strong>Booking created</strong>
                        <span class="muted small">{{ $booking->created_at->format('d M Y, g:i A') }}</span>
                    </li>
                    @foreach($booking->payments as $historyPayment)
                        <li>
                            <strong>Payment submitted — {{ $historyPayment->paymentMethod?->name }}</strong>
                            <span class="muted small">{{ $historyPayment->created_at->format('d M Y, g:i A') }}</span>
                        </li>
                        @if($historyPayment->verified_at)
                            <li>
                                <strong>Payment {{ $historyPayment->status }} by admin</strong>
                                <span class="muted small">{{ $historyPayment->verified_at->format('d M Y, g:i A') }}</span>
                            </li>
                        @endif
                    @endforeach
                    <li>
                        <strong>Current status: {{ $booking->statusLabel() }}</strong>
                        <span class="muted small">Updated {{ $booking->updated_at->diffForHumans() }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- ===== Sidebar ===== -->
        <aside class="sticky-side">
            <div class="panel">
                <h2>Price breakdown</h2>
                <div class="summary-list">
                    <div class="summary-row"><span>Base rental</span><strong>{{ bdt($booking->base_amount) }}</strong></div>
                    <div class="summary-row"><span>Security deposit</span><strong>{{ bdt($booking->security_deposit) }}</strong></div>
                    @if((float) $booking->discount > 0)
                        <div class="summary-row"><span>Discount</span><strong>-{{ bdt($booking->discount) }}</strong></div>
                    @endif
                    <div class="summary-row total"><span>Total</span><span>{{ bdt($booking->total_amount) }}</span></div>
                </div>

                <p class="muted small mt-16">
                    The security deposit is refundable after the vehicle is returned in good condition.
                </p>

                <div class="divider"></div>

                <div class="stack-8">
                    @if($booking->canBePaid())
                        <a href="{{ route('bookings.payment', $booking) }}" class="btn btn-primary btn-block">
                            {{ $booking->payment_status === \App\Models\Booking::PAYMENT_REJECTED ? 'Resubmit payment' : 'Proceed to payment' }}
                        </a>
                    @endif

                    @if($booking->payment_status === \App\Models\Booking::PAYMENT_PENDING)
                        <div class="alert alert-warning mb-8">
                            <span><i class="bi bi-hourglass-split"></i></span>
                            <div>Your payment is awaiting admin verification.</div>
                        </div>
                    @endif

                    @if($booking->canBeReviewed())
                        <a href="{{ route('reviews.index') }}" class="btn btn-success btn-block">Leave a review</a>
                    @endif

                    @if($booking->canBeCancelled())
                        <form method="POST" action="{{ route('bookings.cancel', $booking) }}"
                              data-confirm="Cancel booking {{ $booking->booking_code }}? This cannot be undone.">
                            @csrf
                            <button type="submit" class="btn btn-danger-soft btn-block">Cancel this booking</button>
                        </form>
                    @endif

                    <a href="{{ route('contact') }}" class="btn btn-outline btn-block">Need help?</a>
                </div>
            </div>

            <div class="panel">
                <h2>Booked by</h2>
                <p><strong>{{ $booking->user->name }}</strong></p>
                <p class="muted small">{{ $booking->user->email }}</p>
                <p class="muted small">{{ $booking->user->phone }}</p>
            </div>
        </aside>
    </div>
@endsection
