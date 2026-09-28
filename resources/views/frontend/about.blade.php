@extends('frontend.layouts.app')

@section('title', 'About — '.setting('site_name', 'Rideora'))

@push('styles')
<style>
    .about-hero {
        background: linear-gradient(150deg, #0F172A, #1E3A8A); color: #fff; border-radius: var(--radius-lg);
        padding: 52px 44px; margin-bottom: 38px;
    }
    .about-hero h1 { color: #fff; font-size: 2.3rem; margin-bottom: 12px; }
    .about-hero p { color: #C7D2E5; max-width: 46rem; font-size: 1.02rem; }
    .about-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 30px; }
    .about-stat { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.14); border-radius: var(--radius); padding: 18px; }
    .about-stat strong { display: block; font-size: 1.7rem; color: #fff; }
    .about-stat span { font-size: .82rem; color: #93C5FD; }

    .value-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .value-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; }
    .value-card .ico { font-size: 1.4rem; display: block; margin-bottom: 10px; }
    .value-card h3 { font-size: 1rem; margin-bottom: 6px; }
    .value-card p { font-size: .88rem; color: var(--muted); }

    .steps-list { list-style: none; counter-reset: step; display: grid; gap: 14px; }
    .steps-list li {
        counter-increment: step; background: #fff; border: 1px solid var(--border); border-radius: var(--radius);
        padding: 18px 18px 18px 62px; position: relative; font-size: .9rem;
    }
    .steps-list li::before {
        content: counter(step, decimal-leading-zero); position: absolute; left: 18px; top: 18px; font-weight: 800;
        color: var(--primary); font-size: 1.1rem;
    }

    .about-cta {
        background: linear-gradient(135deg, #2563EB, #1D4ED8); color: #fff; border-radius: var(--radius-lg);
        padding: 38px; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;
    }
    .about-cta h2 { color: #fff; font-size: 1.6rem; margin-bottom: 6px; }
    .about-cta p { color: #DBEAFE; }
    .about-cta .btn { background: #fff; color: var(--primary); }

    @media (max-width: 820px) {
        .value-grid { grid-template-columns: 1fr; gap: 16px; }
        .about-stats { grid-template-columns: 1fr; gap: 12px; }
        .about-hero { padding: 30px 22px; }
        .about-hero h1 { font-size: 1.72rem; }
        .about-hero p { font-size: .96rem; }
        .about-stat strong { font-size: 1.45rem; }
    }

    @media (max-width: 620px) {
        .value-card { padding: 18px; }
        .steps-list li { padding: 16px 16px 16px 54px; font-size: .87rem; }
        .steps-list li::before { left: 16px; top: 16px; font-size: 1rem; }
        .about-hero .eyebrow { font-size: .68rem; }
        .about-cta { padding: 26px 20px; }
        .about-cta h2 { font-size: 1.4rem; }
        .about-cta .btn { width: 100%; }
    }
</style>
@endpush

@section('content')
    <section class="about-hero">
        <span class="eyebrow" style="background: rgba(147,197,253,.16); color:#BFDBFE;">About {{ setting('site_name', 'Rideora') }}</span>
        <h1>Rentals made simple, honest and reliable</h1>
        <p>{{ setting('about_content', 'Rideora is a vehicle rental platform built to make renting a car, bike or microbus simple and transparent.') }}</p>

        <div class="about-stats">
            <div class="about-stat"><strong>{{ $stats['vehicles'] }}</strong><span>Vehicles in the fleet</span></div>
            <div class="about-stat"><strong>{{ $stats['customers'] }}</strong><span>Registered customers</span></div>
            <div class="about-stat"><strong>{{ $stats['completed'] }}</strong><span>Completed rentals</span></div>
        </div>
    </section>

    <section class="mb-24">
        <div class="section-head">
            <span class="eyebrow">What we stand for</span>
            <h2>Why people choose Rideora</h2>
        </div>

        <div class="value-grid">
            <div class="value-card">
                <span class="ico">&#128737;</span>
                <h3>Verified vehicles only</h3>
                <p>Every vehicle in the fleet is registered, inspected and maintained on a schedule before it is listed.</p>
            </div>
            <div class="value-card">
                <span class="ico">&#128176;</span>
                <h3>Transparent pricing</h3>
                <p>Your quote shows the base rental, discount and refundable security deposit before you confirm anything.</p>
            </div>
            <div class="value-card">
                <span class="ico">&#9989;</span>
                <h3>Real payment verification</h3>
                <p>There is no automated gateway. A member of our team checks every bKash, Nagad and bank transfer manually.</p>
            </div>
            <div class="value-card">
                <span class="ico">&#9200;</span>
                <h3>Fast turnaround</h3>
                <p>Payments are reviewed through the day and confirmed bookings are ready for pickup on schedule.</p>
            </div>
            <div class="value-card">
                <span class="ico">&#128506;</span>
                <h3>Multiple pickup points</h3>
                <p>Collect your vehicle in Dhaka, Chattogram or Sylhet — and tell us if you need a different arrangement.</p>
            </div>
            <div class="value-card">
                <span class="ico">&#128222;</span>
                <h3>Support that answers</h3>
                <p>Our team is reachable by phone, email and the contact form before, during and after your rental.</p>
            </div>
        </div>
    </section>

    <section class="mb-24">
        <div class="section-head">
            <span class="eyebrow">How renting works</span>
            <h2>From search to confirmation</h2>
        </div>

        <ol class="steps-list">
            <li>Search the fleet by location, rental dates and vehicle category to see what fits your plan.</li>
            <li>Open the vehicle details page to review specifications, pricing and live availability.</li>
            <li>Create the booking with your pickup and return dates — the total is calculated on our servers, never in the browser.</li>
            <li>Send the total by bKash, Nagad or bank transfer and upload the transaction ID with a screenshot.</li>
            <li>Our team verifies the payment, your booking is confirmed and the vehicle is reserved for your dates.</li>
            <li>After the rental is complete, share a review to help other customers.</li>
        </ol>
    </section>

    <div class="about-cta">
        <div>
            <h2>Ready to hit the road?</h2>
            <p>Find your perfect ride today.</p>
        </div>
        <a href="{{ route('vehicles.index') }}" class="btn btn-lg">Browse vehicles</a>
    </div>
@endsection
