@extends('frontend.layouts.app')

@section('title', 'Book '.$vehicle->name.' — '.setting('site_name', 'Rideora'))

@push('styles')
<style>
    .book-layout { display: grid; grid-template-columns: 1.5fr 1fr; gap: 26px; align-items: start; }
    .panel { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 26px; }
    .panel h2 { font-size: 1.15rem; margin-bottom: 6px; }
    .panel > p.sub { color: var(--muted); font-size: .88rem; margin-bottom: 20px; }
    .vehicle-strip { display: flex; gap: 16px; align-items: center; flex-wrap: wrap; background: var(--light); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; margin-bottom: 22px; }
    .vehicle-strip img { width: 120px; height: 84px; object-fit: cover; border-radius: 10px; background: #EEF2F7; }
    .sticky-side { position: sticky; top: 88px; }
    .quote-state { font-size: .85rem; padding: 11px 13px; border-radius: var(--radius); margin-bottom: 14px; }
    .quote-state.ok { background: var(--success-soft); border: 1px solid #BBF7D0; color: #14532D; }
    .quote-state.no { background: var(--danger-soft); border: 1px solid #FECACA; color: #7F1D1D; }
    .quote-state.idle { background: var(--light); border: 1px dashed var(--border); color: var(--muted); }
    @media (max-width: 960px) {
        .book-layout { grid-template-columns: 1fr; gap: 18px; }
        .sticky-side { position: static; }
    }

    @media (max-width: 620px) {
        .panel { padding: 18px; }
        .vehicle-strip { padding: 14px; gap: 12px; }
        .vehicle-strip img { width: 100%; height: 170px; }
        .form-grid { gap: 14px; }
    }
</style>
@endpush

@section('content')
    <div class="breadcrumbs mb-16" style="font-size:.82rem; color:var(--muted);">
        <a href="{{ route('vehicles.show', $vehicle) }}">&larr; Back to vehicle details</a>
    </div>

    <div class="page-header">
        <h1>Book {{ $vehicle->name }}</h1>
        <p>Choose your rental window. Availability and the total are calculated on our servers.</p>
    </div>

    <div class="book-layout">
        <div class="panel">
            <div class="vehicle-strip">
                <img src="{{ $vehicle->imageUrl() }}" alt="{{ $vehicle->name }}">
                <div style="flex:1; min-width:200px;">
                    <strong style="font-size:1.05rem;">{{ $vehicle->name }}</strong>
                    <p class="muted small">
                        {{ $vehicle->brand }}{{ $vehicle->model ? ' · '.$vehicle->model : '' }} ·
                        {{ $vehicle->transmission }} · {{ $vehicle->fuel_type }} · {{ $vehicle->seats }} seats
                    </p>
                    <p class="small mt-8">
                        {{ bdt($vehicle->price_per_day) }} / day · {{ bdt($vehicle->price_per_hour) }} / hour ·
                        {{ bdt($vehicle->security_deposit) }} deposit
                    </p>
                </div>
                {!! status_badge($vehicle->statusLabel(), $vehicle->statusClass()) !!}
            </div>

            <h2>Rental details</h2>
            <p class="sub">All fields marked required must be filled in before submitting.</p>

            @if(! $available)
                <div class="alert alert-warning">
                    <span><i class="bi bi-exclamation-triangle-fill"></i></span>
                    <div>This vehicle is already booked for part of the period you selected. Choose different dates to continue.</div>
                </div>
            @endif

            <form method="POST" action="{{ route('bookings.store') }}" id="booking-form">
                @csrf
                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                <div class="form-grid">
                    <div class="form-group full">
                        <label for="pickup_location">Pickup location <span style="color:var(--danger)">*</span></label>
                        <input type="text" id="pickup_location" name="pickup_location"
                               class="form-control @error('pickup_location') is-invalid @enderror"
                               value="{{ old('pickup_location', $vehicle->location) }}" required
                               placeholder="Where should we hand over the keys?">
                        @error('pickup_location')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group full">
                        <label for="dropoff_location">Drop-off location</label>
                        <input type="text" id="dropoff_location" name="dropoff_location"
                               class="form-control @error('dropoff_location') is-invalid @enderror"
                               value="{{ old('dropoff_location') }}" placeholder="Leave empty to return to the pickup location">
                        @error('dropoff_location')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="pickup_date">Pickup date <span style="color:var(--danger)">*</span></label>
                        <input type="date" id="pickup_date" name="pickup_date"
                               class="form-control @error('pickup_date') is-invalid @enderror"
                               min="{{ now()->toDateString() }}" value="{{ old('pickup_date', $pickupDate) }}" required>
                        @error('pickup_date')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="pickup_time">Pickup time <span style="color:var(--danger)">*</span></label>
                        <input type="time" id="pickup_time" name="pickup_time"
                               class="form-control @error('pickup_time') is-invalid @enderror"
                               value="{{ old('pickup_time', $pickupTime) }}" required>
                        @error('pickup_time')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="return_date">Return date <span style="color:var(--danger)">*</span></label>
                        <input type="date" id="return_date" name="return_date"
                               class="form-control @error('return_date') is-invalid @enderror"
                               min="{{ now()->toDateString() }}" value="{{ old('return_date', $returnDate) }}" required>
                        @error('return_date')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="return_time">Return time <span style="color:var(--danger)">*</span></label>
                        <input type="time" id="return_time" name="return_time"
                               class="form-control @error('return_time') is-invalid @enderror"
                               value="{{ old('return_time', $returnTime) }}" required>
                        @error('return_time')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group full">
                        <label for="customer_note">Note for our team</label>
                        <textarea id="customer_note" name="customer_note" class="form-control"
                                  placeholder="Anything we should know? Driver requirement, extra helmet, delivery request…">{{ old('customer_note') }}</textarea>
                        @error('customer_note')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="alert alert-info mt-24">
                    <span><i class="bi bi-info-circle-fill"></i></span>
                    <div>
                        After submitting you will be taken to the manual payment page where you can pay by bKash, Nagad or bank transfer.
                        The total is recalculated on the server, so it always matches your selected dates.
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg" id="submit-booking">Create booking</button>
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-light btn-lg">Cancel</a>
                </div>
            </form>
        </div>

        <aside class="sticky-side">
            <div class="panel">
                <h2>Live price summary</h2>
                <p class="sub">Updates as you change the rental window.</p>

                <div class="quote-state idle" id="quote-state">Checking availability…</div>

                <div class="summary-list" id="quote-summary">
                    <div class="summary-row"><span>Daily rate</span><strong>{{ bdt($vehicle->price_per_day) }}</strong></div>
                    <div class="summary-row"><span>Hourly rate</span><strong>{{ bdt($vehicle->price_per_hour) }}</strong></div>
                    <div class="summary-row"><span>Security deposit</span><strong>{{ bdt($vehicle->security_deposit) }}</strong></div>
                </div>

                <div id="quote-dynamic"></div>

                @if($quote)
                    <div class="divider"></div>
                    <div class="summary-list">
                        <div class="summary-row"><span>Duration</span><strong>{{ $quote['rental_days'] }} day(s), {{ $quote['rental_hours'] }} hour(s)</strong></div>
                        <div class="summary-row"><span>Base rental</span><strong>{{ bdt($quote['base_amount']) }}</strong></div>
                        <div class="summary-row"><span>Security deposit</span><strong>{{ bdt($quote['security_deposit']) }}</strong></div>
                        @if($quote['discount'] > 0)
                            <div class="summary-row"><span>Discount ({{ $quote['discount_percent'] }}%)</span><strong>-{{ bdt($quote['discount']) }}</strong></div>
                        @endif
                        <div class="summary-row total"><span>Total payable</span><span>{{ bdt($quote['total_amount']) }}</span></div>
                    </div>
                @endif

                <p class="muted small mt-16">
                    Discounts apply automatically for longer rentals (5% from 7 days, 10% from 14 days, 15% from 30 days).
                </p>
            </div>
        </aside>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('booking-form');
        var pickupDate = document.getElementById('pickup_date');
        var pickupTime = document.getElementById('pickup_time');
        var returnDate = document.getElementById('return_date');
        var returnTime = document.getElementById('return_time');
        var stateBox = document.getElementById('quote-state');
        var dynamic = document.getElementById('quote-dynamic');
        var submitBtn = document.getElementById('submit-booking');
        var availabilityUrl = '{{ route('vehicles.availability', $vehicle) }}';

        function setState(message, kind) {
            stateBox.className = 'quote-state ' + kind;
            stateBox.textContent = message;
        }

        function syncDates() {
            if (!pickupDate || !returnDate) { return; }
            returnDate.min = pickupDate.value || '{{ now()->toDateString() }}';
            if (returnDate.value && pickupDate.value && returnDate.value < pickupDate.value) {
                returnDate.value = pickupDate.value;
            }
        }

        function refreshQuote() {
            if (!pickupDate.value || !returnDate.value || !pickupTime.value || !returnTime.value) {
                setState('Fill in all dates and times to see the total.', 'idle');
                dynamic.innerHTML = '';
                return;
            }

            var params = new URLSearchParams({
                pickup_date: pickupDate.value,
                pickup_time: pickupTime.value,
                return_date: returnDate.value,
                return_time: returnTime.value
            });

            setState('Checking availability…', 'idle');

            fetch(availabilityUrl + '?' + params.toString(), { headers: { 'Accept': 'application/json' } })
                .then(function (response) { return response.json().then(function (body) { return { ok: response.ok, body: body }; }); })
                .then(function (payload) {
                    var body = payload.body;

                    if (!payload.ok) {
                        var message = body.message || (body.errors ? Object.values(body.errors)[0][0] : 'These dates are not valid.');
                        setState(message, 'no');
                        dynamic.innerHTML = '';
                        submitBtn.disabled = true;
                        return;
                    }

                    var quote = body.quote;
                    setState(body.message, body.available ? 'ok' : 'no');
                    submitBtn.disabled = !body.available;

                    dynamic.innerHTML =
                        '<div class="divider"></div>' +
                        '<div class="summary-list">' +
                            '<div class="summary-row"><span>Duration</span><strong>' + quote.duration_label + '</strong></div>' +
                            '<div class="summary-row"><span>Base rental</span><strong>' + quote.base_amount_formatted + '</strong></div>' +
                            '<div class="summary-row"><span>Security deposit</span><strong>' + quote.security_deposit_formatted + '</strong></div>' +
                            (quote.discount > 0
                                ? '<div class="summary-row"><span>Discount (' + quote.discount_percent + '%)</span><strong>-' + quote.discount_formatted + '</strong></div>'
                                : '') +
                            '<div class="summary-row total"><span>Total payable</span><span>' + quote.total_amount_formatted + '</span></div>' +
                        '</div>';
                })
                .catch(function () {
                    setState('Could not check availability. Please try again.', 'no');
                });
        }

        [pickupDate, pickupTime, returnDate, returnTime].forEach(function (el) {
            if (!el) { return; }
            el.addEventListener('change', function () {
                syncDates();
                refreshQuote();
            });
        });

        syncDates();
        refreshQuote();
    });
</script>
@endpush
