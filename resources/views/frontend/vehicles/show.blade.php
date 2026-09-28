@extends('frontend.layouts.app')

@section('title', $vehicle->name.' — '.setting('site_name', 'Rideora'))

@push('styles')
<style>
    .breadcrumbs { font-size: .82rem; color: var(--muted); margin-bottom: 14px; display: flex; gap: 7px; flex-wrap: wrap; }
    .breadcrumbs a { color: var(--muted); }
    .breadcrumbs a:hover { color: var(--primary); }

    .detail-layout { display: grid; grid-template-columns: 1.6fr 1fr; gap: 28px; align-items: start; }

    .gallery { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; }
    .gallery-main { position: relative; aspect-ratio: 16 / 10; background: #EEF2F7; }
    .gallery-main img { width: 100%; height: 100%; object-fit: cover; }
    .gallery-badges { position: absolute; top: 14px; left: 14px; display: flex; gap: 8px; }
    .gallery-thumbs { display: flex; gap: 10px; padding: 14px; overflow-x: auto; }
    .gallery-thumbs button {
        width: 92px; height: 66px; flex-shrink: 0; border-radius: 10px; overflow: hidden; padding: 0; cursor: pointer;
        border: 2px solid var(--border); background: #fff;
    }
    .gallery-thumbs button.active { border-color: var(--primary); }
    .gallery-thumbs img { width: 100%; height: 100%; object-fit: cover; }

    .panel { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 22px; }
    .panel + .panel { margin-top: 22px; }
    .panel h2 { font-size: 1.15rem; margin-bottom: 14px; }
    .panel h3 { font-size: 1rem; margin-bottom: 10px; }

    .spec-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
    .spec {
        background: var(--light); border: 1px solid var(--border); border-radius: var(--radius); padding: 13px 14px;
    }
    .spec span.lbl { display: block; font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); margin-bottom: 3px; }
    .spec strong { font-size: .92rem; color: var(--dark); }

    .price-tag { display: flex; align-items: baseline; gap: 8px; }
    .price-tag strong { font-size: 2rem; color: var(--dark); letter-spacing: -.02em; }
    .price-tag span { color: var(--muted); font-size: .9rem; }

    .availability-result { margin-top: 14px; font-size: .86rem; }
    .avail-ok { background: var(--success-soft); border: 1px solid #BBF7D0; color: #14532D; padding: 11px 13px; border-radius: var(--radius); }
    .avail-no { background: var(--danger-soft); border: 1px solid #FECACA; color: #7F1D1D; padding: 11px 13px; border-radius: var(--radius); }

    .quote-box { margin-top: 14px; background: var(--light); border: 1px dashed var(--border); border-radius: var(--radius); padding: 14px; }

    .review { padding: 18px 0; border-bottom: 1px solid var(--border); }
    .review:last-child { border-bottom: none; }
    .review-head { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
    .review-avatar {
        width: 40px; height: 40px; border-radius: 50%; background: var(--primary-soft); color: var(--primary);
        display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: .85rem; flex-shrink: 0;
    }
    .review-body { font-size: .9rem; color: var(--text); }
    .rating-summary { display: flex; align-items: center; gap: 16px; padding-bottom: 14px; border-bottom: 1px solid var(--border); margin-bottom: 6px; }
    .rating-big { font-size: 2.4rem; font-weight: 800; color: var(--dark); line-height: 1; }

    .related-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
    .related-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
    .related-card img { aspect-ratio: 16/10; object-fit: cover; width: 100%; }
    .related-card .body { padding: 14px; }
    .related-card h4 { font-size: .92rem; margin-bottom: 4px; }

    .sticky-side { position: sticky; top: 88px; }

    @media (max-width: 980px) {
        .detail-layout { grid-template-columns: 1fr; gap: 20px; }
        .sticky-side { position: static; }
        .spec-grid, .related-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 620px) {
        .spec-grid, .related-grid { grid-template-columns: 1fr; }
        .panel { padding: 18px; }
        .panel + .panel, .panel.mt-24 { margin-top: 16px !important; }
        .gallery-thumbs { padding: 12px; gap: 8px; }
        .gallery-thumbs button { width: 76px; height: 56px; }
        .price-tag strong { font-size: 1.7rem; }
        .rating-summary { flex-wrap: wrap; gap: 12px; }
        .review-head { gap: 10px; }
        .review-avatar { width: 36px; height: 36px; font-size: .78rem; }
        .panel h2 { font-size: 1.05rem; }
    }

    @media (max-width: 420px) {
        .gallery-badges { flex-direction: column; gap: 6px; }
        .breadcrumbs { font-size: .78rem; }
    }
</style>
@endpush

@section('content')
    <div class="breadcrumbs">
        <a href="{{ route('home') }}">Home</a> <span>/</span>
        <a href="{{ route('vehicles.index') }}">Vehicles</a> <span>/</span>
        @if($vehicle->category)
            <a href="{{ route('vehicles.index', ['category' => $vehicle->category->slug]) }}">{{ $vehicle->category->name }}</a> <span>/</span>
        @endif
        <span>{{ $vehicle->name }}</span>
    </div>

    <div class="detail-layout">
        <!-- ================== LEFT ================== -->
        <div>
            <div class="gallery">
                @php $images = $vehicle->images; @endphp
                <div class="gallery-main">
                    <img id="gallery-main-image" src="{{ $vehicle->imageUrl() }}" alt="{{ $vehicle->name }}">
                    <div class="gallery-badges">
                        {!! status_badge($vehicle->statusLabel(), $vehicle->statusClass()) !!}
                        @if($vehicle->category)
                            {!! status_badge($vehicle->category->name, 'badge-primary') !!}
                        @endif
                    </div>
                </div>

                @if($images->count() > 1)
                    <div class="gallery-thumbs" data-gallery>
                        @foreach($images as $index => $image)
                            <button type="button" class="{{ $index === 0 ? 'active' : '' }}"
                                    data-gallery-thumb data-src="{{ $image->url() }}">
                                <img src="{{ $image->url() }}" alt="{{ $vehicle->name }} photo {{ $index + 1 }}">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="panel mt-24">
                <h2>About this vehicle</h2>
                <p>{{ $vehicle->description ?: 'This vehicle has no description yet. Contact our team for more details.' }}</p>

                <div class="divider"></div>

                <h3>Specifications</h3>
                <div class="spec-grid">
                    <div class="spec"><span class="lbl">Brand</span><strong>{{ $vehicle->brand }}</strong></div>
                    <div class="spec"><span class="lbl">Model</span><strong>{{ $vehicle->model ?: '—' }}</strong></div>
                    <div class="spec"><span class="lbl">Type</span><strong>{{ $vehicle->vehicle_type }}</strong></div>
                    <div class="spec"><span class="lbl">Seats</span><strong>{{ $vehicle->seats }} seats</strong></div>
                    <div class="spec"><span class="lbl">Transmission</span><strong>{{ $vehicle->transmission }}</strong></div>
                    <div class="spec"><span class="lbl">Fuel</span><strong>{{ $vehicle->fuel_type }}</strong></div>
                    <div class="spec"><span class="lbl">Registration</span><strong>{{ $vehicle->registration_number }}</strong></div>
                    <div class="spec"><span class="lbl">Location</span><strong>{{ $vehicle->location ?: 'Flexible' }}</strong></div>
                    <div class="spec"><span class="lbl">Deposit</span><strong>{{ bdt($vehicle->security_deposit) }}</strong></div>
                </div>
            </div>

            <!-- ================== REVIEWS ================== -->
            <div class="panel mt-24" id="reviews">
                <h2>Customer reviews</h2>

                @php
                    $averageRating = $vehicle->averageRating();
                    $reviewCount = $vehicle->reviewsCount();
                @endphp

                @if($reviewCount > 0)
                    <div class="rating-summary">
                        <div>
                            <div class="rating-big">{{ number_format($averageRating, 1) }}</div>
                            <span class="muted small">out of 5</span>
                        </div>
                        <div>
                            {!! star_row($averageRating) !!}
                            <div class="muted small">{{ $reviewCount }} verified {{ \Illuminate\Support\Str::plural('review', $reviewCount) }}</div>
                        </div>
                    </div>
                @endif

                @forelse($reviews as $review)
                    <div class="review">
                        <div class="review-head">
                            <span class="review-avatar">{{ $review->user?->initials() ?? 'R' }}</span>
                            <div>
                                <strong>{{ $review->user?->name ?? 'Rideora customer' }}</strong>
                                <div>{!! star_row($review->rating) !!} <span class="muted small">{{ $review->created_at->format('d M Y') }}</span></div>
                            </div>
                        </div>
                        <p class="review-body">{{ $review->comment }}</p>
                    </div>
                @empty
                    <div class="empty-state" style="padding:28px 0;">
                        <div class="icon">&#9733;</div>
                        <h3>No reviews yet</h3>
                        <p>Be the first to rent this vehicle and share your experience.</p>
                    </div>
                @endforelse

                @auth
                    @unless(auth()->user()->isAdmin())
                        @if($reviewableBooking)
                            <div class="divider"></div>
                            <h3>Write a review for your completed rental</h3>
                            <form method="POST" action="{{ route('reviews.store') }}" class="stack-16">
                                @csrf
                                <input type="hidden" name="booking_id" value="{{ $reviewableBooking->id }}">

                                <div class="form-group">
                                    <label for="inline-rating">Rating</label>
                                    <select id="inline-rating" name="rating" class="form-control" required>
                                        @foreach([5, 4, 3, 2, 1] as $rating)
                                            <option value="{{ $rating }}" @selected(old('rating') == $rating)>{{ $rating }} star{{ $rating > 1 ? 's' : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="inline-comment">Your experience</label>
                                    <textarea id="inline-comment" name="comment" class="form-control" required
                                              placeholder="How was the car, the pickup process and the service?">{{ old('comment') }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">Submit review</button>
                            </form>
                        @endif
                    @endunless
                @endauth
            </div>

            <!-- ================== RELATED ================== -->
            @if($relatedVehicles->isNotEmpty())
                <div class="panel mt-24">
                    <h2>Similar vehicles</h2>
                    <div class="related-grid">
                        @foreach($relatedVehicles as $related)
                            <a href="{{ route('vehicles.show', $related) }}" class="related-card">
                                <img src="{{ $related->imageUrl() }}" alt="{{ $related->name }}">
                                <div class="body">
                                    <h4>{{ $related->name }}</h4>
                                    <p class="muted small">{{ bdt($related->price_per_day) }} / day</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- ================== RIGHT (BOOKING) ================== -->
        <aside class="sticky-side">
            <div class="panel">
                <h2>{{ $vehicle->name }}</h2>
                <p class="muted small mb-16">{{ $vehicle->brand }}{{ $vehicle->model ? ' · '.$vehicle->model : '' }} · {{ $vehicle->seats }} seats</p>

                <div class="price-tag">
                    <strong>{{ bdt($vehicle->price_per_day) }}</strong>
                    <span>/ day</span>
                </div>
                <p class="muted small">{{ bdt($vehicle->price_per_hour) }} per hour · {{ bdt($vehicle->security_deposit) }} refundable deposit</p>

                <div class="divider"></div>

                <h3>Check availability</h3>
                <form id="availability-form" data-availability-url="{{ route('vehicles.availability', $vehicle) }}">
                    <div class="form-group mb-8">
                        <label for="pickup_date">Pickup date</label>
                        <input type="date" id="pickup_date" name="pickup_date" class="form-control"
                               min="{{ now()->toDateString() }}" value="{{ $requestedPickup ?: $pickupDate }}" required>
                    </div>
                    <div class="form-group mb-8">
                        <label for="pickup_time">Pickup time</label>
                        <input type="time" id="pickup_time" name="pickup_time" class="form-control"
                               value="{{ $pickupTime ?? '10:00' }}" required>
                    </div>
                    <div class="form-group mb-8">
                        <label for="return_date">Return date</label>
                        <input type="date" id="return_date" name="return_date" class="form-control"
                               min="{{ now()->toDateString() }}" value="{{ $requestedReturn ?: $returnDate }}" required>
                    </div>
                    <div class="form-group mb-8">
                        <label for="return_time">Return time</label>
                        <input type="time" id="return_time" name="return_time" class="form-control"
                               value="{{ $returnTime ?? '10:00' }}" required>
                    </div>

                    <button type="submit" class="btn btn-outline btn-block" id="availability-btn">Check availability</button>
                </form>

                <div class="availability-result" id="availability-result">
                    @if(! is_null($isAvailableForRequest))
                        <div class="{{ $isAvailableForRequest ? 'avail-ok' : 'avail-no' }}">
                            {{ $isAvailableForRequest ? 'This vehicle is available for the selected dates.' : 'This vehicle is already booked during part of the selected period.' }}
                        </div>
                    @endif
                </div>

                <div id="quote-box"></div>

                @if($vehicle->status === \App\Models\Vehicle::STATUS_AVAILABLE)
                    <a href="{{ route('bookings.create', $vehicle) }}"
                       class="btn btn-primary btn-block btn-lg mt-16" id="book-now-btn">
                        Book this vehicle
                    </a>
                @else
                    <div class="alert alert-warning mt-16 mb-8">
                        This vehicle is currently {{ $vehicle->status }} and cannot be booked online.
                    </div>
                @endif

                <p class="muted small mt-16 text-center">
                    Manual payment verification · bKash, Nagad or bank transfer
                </p>
            </div>

            @php $blocked = $vehicle->blockedRanges(); @endphp
            @if(! empty($blocked))
                <div class="panel mt-16">
                    <h3>Already booked</h3>
                    <ul class="stack-8 small muted">
                        @foreach(array_slice($blocked, 0, 5) as $range)
                            <li>&#128197; {{ \Carbon\Carbon::parse($range['from'])->format('d M Y') }}
                                &rarr; {{ \Carbon\Carbon::parse($range['to'])->format('d M Y') }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </aside>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ---------- Gallery ----------
        var main = document.getElementById('gallery-main-image');
        document.querySelectorAll('[data-gallery-thumb]').forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                main.src = thumb.dataset.src;
                document.querySelectorAll('[data-gallery-thumb]').forEach(function (el) { el.classList.remove('active'); });
                thumb.classList.add('active');
            });
        });

        // ---------- Date validation ----------
        var pickupDate = document.getElementById('pickup_date');
        var returnDate = document.getElementById('return_date');
        var pickupTime = document.getElementById('pickup_time');
        var returnTime = document.getElementById('return_time');

        if (pickupDate && returnDate) {
            var syncDates = function () {
                returnDate.min = pickupDate.value || '{{ now()->toDateString() }}';
                if (returnDate.value && pickupDate.value && returnDate.value < pickupDate.value) {
                    returnDate.value = pickupDate.value;
                }
            };
            pickupDate.addEventListener('change', syncDates);
            syncDates();
        }

        // ---------- Availability + live quote ----------
        var form = document.getElementById('availability-form');
        var resultBox = document.getElementById('availability-result');
        var quoteBox = document.getElementById('quote-box');
        var bookBtn = document.getElementById('book-now-btn');
        var submitBtn = document.getElementById('availability-btn');

        function showMessage(html, ok) {
            resultBox.innerHTML = '<div class="' + (ok ? 'avail-ok' : 'avail-no') + '">' + html + '</div>';
        }

        function renderQuote(quote, available) {
            if (!quote) { quoteBox.innerHTML = ''; return; }

            quoteBox.innerHTML =
                '<div class="quote-box">' +
                    '<div class="summary-list">' +
                        '<div class="summary-row"><span>Duration</span><strong>' + quote.duration_label + '</strong></div>' +
                        '<div class="summary-row"><span>Base rental</span><strong>' + quote.base_amount_formatted + '</strong></div>' +
                        '<div class="summary-row"><span>Security deposit</span><strong>' + quote.security_deposit_formatted + '</strong></div>' +
                        (quote.discount > 0 ? '<div class="summary-row"><span>Discount (' + quote.discount_percent + '%)</span><strong>-' + quote.discount_formatted + '</strong></div>' : '') +
                        '<div class="summary-row total"><span>Total</span><span>' + quote.total_amount_formatted + '</span></div>' +
                    '</div>' +
                    (available
                        ? '<a class="btn btn-primary btn-block mt-16" href="{{ route('bookings.create', $vehicle) }}?pickup_date=' + pickupDate.value +
                          '&pickup_time=' + pickupTime.value + '&return_date=' + returnDate.value + '&return_time=' + returnTime.value + '">Continue to booking</a>'
                        : '') +
                '</div>';
        }

        function refresh() {
            if (!form) { return; }

            var data = new FormData(form);
            submitBtn.disabled = true;
            submitBtn.textContent = 'Checking…';

            fetch(form.dataset.availabilityUrl + '?' + new URLSearchParams(data).toString(), {
                headers: { 'Accept': 'application/json' }
            })
                .then(function (response) { return response.json().then(function (body) { return { ok: response.ok, body: body }; }); })
                .then(function (payload) {
                    var body = payload.body;

                    if (!payload.ok) {
                        var message = body.message || (body.errors ? Object.values(body.errors)[0][0] : 'Please check the selected dates and times.');
                        showMessage(message, false);
                        quoteBox.innerHTML = '';
                        if (bookBtn) { bookBtn.classList.add('disabled'); }
                        return;
                    }

                    showMessage(body.message, body.available);
                    renderQuote(body.quote, body.available);

                    if (bookBtn) {
                        if (body.available) {
                            bookBtn.classList.remove('disabled');
                            bookBtn.style.pointerEvents = 'auto';
                        } else {
                            bookBtn.classList.add('disabled');
                            bookBtn.style.pointerEvents = 'none';
                        }
                    }
                })
                .catch(function () {
                    showMessage('Could not check availability right now. Please try again.', false);
                })
                .finally(function () {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Check availability';
                });
        }

        if (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                refresh();
            });
        }
    });
</script>
@endpush
