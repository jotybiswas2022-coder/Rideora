@extends('frontend.layouts.app')

@section('title', 'Payment submitted — '.setting('site_name', 'Rideora'))

@push('styles')
<style>
    .success-hero {
        background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 40px 32px;
        text-align: center; margin-bottom: 24px;
    }
    .success-icon {
        width: 74px; height: 74px; border-radius: 50%; background: var(--success-soft); color: var(--success);
        display: inline-flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 16px;
    }
    .success-hero h1 { font-size: 1.7rem; margin-bottom: 8px; }
    .success-hero p { color: var(--muted); max-width: 40rem; margin: 0 auto; }
    .recap-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 26px; text-align: left; }
    .recap { background: var(--light); border: 1px solid var(--border); border-radius: var(--radius); padding: 14px 16px; }
    .recap span.lbl { display: block; font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); margin-bottom: 3px; }
    .recap strong { font-size: .92rem; color: var(--dark); }
    .steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
    .step-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); padding: 18px; }
    .step-card .num { font-weight: 800; color: var(--primary); font-size: 1.1rem; }
    .step-card strong { display: block; margin: 6px 0 4px; font-size: .92rem; }
    .step-card p { font-size: .82rem; color: var(--muted); }
    .proof-box { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; }
    .proof-box img { max-height: 280px; border-radius: var(--radius); border: 1px solid var(--border); }
    @media (max-width: 900px) {
        .recap-grid, .steps { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 620px) {
        .success-hero { padding: 28px 18px; }
        .success-hero h1 { font-size: 1.35rem; }
        .success-icon { width: 62px; height: 62px; font-size: 1.6rem; }
        .recap-grid, .steps { grid-template-columns: 1fr; gap: 12px; }
        .recap { padding: 13px 14px; }
        .step-card { padding: 16px; }
        .proof-box { padding: 18px; }
        .proof-box img { max-height: 260px; width: 100%; object-fit: contain; }
    }
</style>
@endpush

@section('content')
    <div class="success-hero">
        <div class="success-icon"><i class="bi bi-check-lg"></i></div>
        <h1>Payment submitted for verification</h1>
        <p>
            Thank you, {{ $payment->user->name }}. Your payment for booking
            <strong>{{ $payment->booking->booking_code }}</strong> is now <strong>pending verification</strong>.
            Our team reviews manual payments and you will be notified once it is confirmed.
        </p>

        <div class="recap-grid">
            <div class="recap">
                <span class="lbl">Booking ID</span>
                <strong>{{ $payment->booking->booking_code }}</strong>
            </div>
            <div class="recap">
                <span class="lbl">Vehicle</span>
                <strong>{{ $payment->booking->vehicle->name }}</strong>
            </div>
            <div class="recap">
                <span class="lbl">Amount submitted</span>
                <strong>{{ bdt($payment->amount) }}</strong>
            </div>
            <div class="recap">
                <span class="lbl">Payment method</span>
                <strong>{{ $payment->paymentMethod?->name ?? '—' }}</strong>
            </div>
            <div class="recap">
                <span class="lbl">Transaction ID</span>
                <strong>{{ $payment->transaction_id }}</strong>
            </div>
            <div class="recap">
                <span class="lbl">Status</span>
                <strong>{!! status_badge($payment->statusLabel(), $payment->statusClass()) !!}</strong>
            </div>
        </div>

        <div class="form-actions mt-24" style="justify-content:center;">
            <a href="{{ route('bookings.show', $payment->booking) }}" class="btn btn-primary btn-lg">View booking details</a>
            <a href="{{ route('vehicles.index') }}" class="btn btn-outline btn-lg">Browse more vehicles</a>
        </div>
    </div>

    <div class="panel" style="background:#fff; border:1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 24px;">
        <h2 style="font-size:1.05rem; margin-bottom:16px;">What happens next</h2>
        <div class="steps">
            <div class="step-card">
                <span class="num">01</span>
                <strong>Payment queued</strong>
                <p>Your submission is now in the admin verification queue.</p>
            </div>
            <div class="step-card">
                <span class="num">02</span>
                <strong>Manual review</strong>
                <p>Our team matches your transaction ID against the account statement.</p>
            </div>
            <div class="step-card">
                <span class="num">03</span>
                <strong>Decision</strong>
                <p>Approved payments confirm the booking; rejected ones can be resubmitted.</p>
            </div>
            <div class="step-card">
                <span class="num">04</span>
                <strong>Notification</strong>
                <p>You will see the outcome in your notifications and booking page.</p>
            </div>
        </div>
    </div>

    @if($payment->proofUrl())
        <div class="proof-box">
            <h2 style="font-size:1.05rem; margin-bottom:12px;">Your submitted proof</h2>
            <a href="{{ $payment->proofUrl() }}" target="_blank" rel="noopener">
                <img src="{{ $payment->proofUrl() }}" alt="Payment proof">
            </a>
        </div>
    @endif
@endsection
