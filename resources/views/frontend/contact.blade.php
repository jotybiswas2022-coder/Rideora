@extends('frontend.layouts.app')

@section('title', 'Contact — '.setting('site_name', 'Rideora'))

@push('styles')
<style>
    .contact-layout { display: grid; grid-template-columns: 1fr 1.15fr; gap: 28px; align-items: start; }
    .contact-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 28px; }
    .contact-list { list-style: none; display: grid; gap: 18px; margin-top: 10px; }
    .contact-list li { display: flex; gap: 14px; align-items: flex-start; }
    .contact-ico {
        width: 42px; height: 42px; border-radius: 12px; background: var(--primary-soft); color: var(--primary);
        display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1rem;
    }
    .contact-list strong { display: block; font-size: .9rem; }
    .contact-list span.muted { font-size: .85rem; }
    .contact-side {
        background: linear-gradient(150deg, #0F172A, #1E3A8A); color: #fff; border-radius: var(--radius-lg); padding: 28px;
    }
    .contact-side h2 { color: #fff; font-size: 1.3rem; margin-bottom: 10px; }
    .contact-side p { color: #C7D2E5; font-size: .9rem; }
    .contact-side ul { list-style: none; margin-top: 18px; display: grid; gap: 12px; font-size: .87rem; color: #E2E8F0; }
    .contact-side li { display: flex; gap: 10px; }
    .contact-side .hours { margin-top: 22px; padding-top: 18px; border-top: 1px solid rgba(255,255,255,.15); font-size: .85rem; color: #BFDBFE; }

    @media (max-width: 900px) {
        .contact-layout { grid-template-columns: 1fr; gap: 18px; }
    }

    @media (max-width: 620px) {
        .contact-card, .contact-side { padding: 20px; }
        .contact-list li { gap: 12px; }
        .contact-ico { width: 38px; height: 38px; }
    }
</style>
@endpush

@section('content')
    <div class="page-header">
        <h1>Talk to the Rideora team</h1>
        <p>Questions about a booking, a vehicle or a payment? Send a message and we will get back to you within one business day.</p>
    </div>

    <div class="contact-layout">
        <div class="contact-card">
            <h2 style="font-size:1.1rem; margin-bottom:12px;">Contact information</h2>

            <ul class="contact-list">
                <li>
                    <span class="contact-ico"><i class="bi bi-geo-alt"></i></span>
                    <span>
                        <strong>Office</strong>
                        <span class="muted">{{ setting('office_address', 'Dhaka, Bangladesh') }}</span>
                    </span>
                </li>
                <li>
                    <span class="contact-ico"><i class="bi bi-telephone"></i></span>
                    <span>
                        <strong>Phone</strong>
                        <a href="tel:{{ preg_replace('/\s+/', '', setting('support_phone', '')) }}">{{ setting('support_phone', '+880 1700-000000') }}</a>
                    </span>
                </li>
                <li>
                    <span class="contact-ico"><i class="bi bi-envelope-fill"></i></span>
                    <span>
                        <strong>Email</strong>
                        <a href="mailto:{{ setting('support_email', 'support@rideora.test') }}">{{ setting('support_email', 'support@rideora.test') }}</a>
                    </span>
                </li>
                <li>
                    <span class="contact-ico"><i class="bi bi-credit-card-2-front"></i></span>
                    <span>
                        <strong>Payments</strong>
                        <span class="muted">bKash, Nagad and bank transfer — verified manually by our team.</span>
                    </span>
                </li>
            </ul>
        </div>

        <div class="contact-side">
            <h2>Send us a message</h2>
            <p>Fill in the form and our support team will reply by email. For urgent booking changes, please call us.</p>

            <ul>
                <li><span><i class="bi bi-check-lg"></i></span> Booking changes and cancellations</li>
                <li><span><i class="bi bi-check-lg"></i></span> Payment verification follow-ups</li>
                <li><span><i class="bi bi-check-lg"></i></span> Long term or corporate rentals</li>
                <li><span><i class="bi bi-check-lg"></i></span> Chauffeur and out-of-city requests</li>
            </ul>

            <div class="hours">
                Support hours: Saturday – Thursday, 9:00 AM – 8:00 PM (GMT+6)
            </div>
        </div>
    </div>

    <div class="contact-card mt-24">
        <h2 style="font-size:1.1rem; margin-bottom:16px;">Enquiry form</h2>

        <form method="POST" action="{{ route('contact.send') }}" class="stack-16">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Full name</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', auth()->user()->name ?? '') }}" required>
                    @error('name')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', auth()->user()->email ?? '') }}" required>
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="phone">Phone (optional)</label>
                    <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', auth()->user()->phone ?? '') }}" placeholder="+880 17xx-xxxxxx">
                    @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" class="form-control @error('subject') is-invalid @enderror"
                           value="{{ old('subject') }}" required>
                    @error('subject')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group full">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" class="form-control @error('message') is-invalid @enderror"
                              required placeholder="Tell us what you need…">{{ old('message') }}</textarea>
                    @error('message')<span class="form-error">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">Send message</button>
                <span class="muted small">We usually reply within one business day.</span>
            </div>
        </form>
    </div>
@endsection
