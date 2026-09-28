@extends('frontend.layouts.app')

@section('title', 'My Reviews — '.setting('site_name', 'Rideora'))

@push('styles')
<style>
    .review-layout { display: grid; grid-template-columns: 1fr 1.25fr; gap: 26px; align-items: start; }
    .panel { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; }
    .panel + .panel { margin-top: 22px; }
    .panel h2 { font-size: 1.08rem; margin-bottom: 6px; }
    .panel p.sub { color: var(--muted); font-size: .86rem; margin-bottom: 18px; }

    .pending-item { border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; margin-bottom: 16px; }
    .pending-item:last-child { margin-bottom: 0; }
    .pending-head { display: flex; gap: 14px; align-items: center; margin-bottom: 14px; flex-wrap: wrap; }
    .pending-head img { width: 96px; height: 68px; object-fit: cover; border-radius: 10px; background: #EEF2F7; }
    .star-picker { display: flex; gap: 6px; }
    .star-picker input { position: absolute; opacity: 0; pointer-events: none; }
    .star-picker label { font-size: 1.5rem; color: #CBD5E1; cursor: pointer; line-height: 1; }
    .star-picker input:checked ~ label,
    .star-picker label:hover,
    .star-picker label:hover ~ label { color: #F59E0B; }
    .star-picker input:checked + label { color: #F59E0B; }

    .review-card { border: 1px solid var(--border); border-radius: var(--radius); padding: 18px; margin-bottom: 14px; }
    .review-card:last-child { margin-bottom: 0; }
    .review-top { display: flex; align-items: center; gap: 14px; margin-bottom: 10px; flex-wrap: wrap; }
    .review-top img { width: 84px; height: 60px; object-fit: cover; border-radius: 10px; background: #EEF2F7; }
    @media (max-width: 940px) {
        .review-layout { grid-template-columns: 1fr; gap: 18px; }
    }

    @media (max-width: 620px) {
        .panel { padding: 18px; }
        .panel + .panel { margin-top: 16px; }
        .pending-item, .review-card { padding: 14px; }
        .pending-head img { width: 100%; height: 150px; }
        .review-top img { width: 100%; height: 150px; }
        .form-group .btn { width: 100%; }
    }

    @media (max-width: 420px) {
        .star-picker label { font-size: 1.35rem; }
    }
</style>
@endpush

@section('content')
    <div class="page-header">
        <h1>My reviews</h1>
        <p>Share your experience after a completed rental and manage the reviews you have submitted.</p>
    </div>

    <div class="review-layout">
        <div>
            <div class="panel">
                <h2>Awaiting your review</h2>
                <p class="sub">Reviews can only be written after a rental is completed.</p>

                @forelse($reviewableBookings as $booking)
                    <div class="pending-item">
                        <div class="pending-head">
                            <img src="{{ $booking->vehicle->imageUrl() }}" alt="{{ $booking->vehicle->name }}">
                            <div style="flex:1; min-width:180px;">
                                <strong>{{ $booking->vehicle->name }}</strong>
                                <p class="muted small">{{ $booking->booking_code }}</p>
                                <p class="muted small">
                                    {{ $booking->pickup_date->format('d M Y') }} → {{ $booking->return_date->format('d M Y') }}
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('reviews.store') }}" class="stack-16">
                            @csrf
                            <input type="hidden" name="booking_id" value="{{ $booking->id }}">

                            <div class="form-group">
                                <label>Your rating</label>
                                <div class="star-picker">
                                    @foreach([5, 4, 3, 2, 1] as $rating)
                                        <input type="radio" id="star-{{ $booking->id }}-{{ $rating }}"
                                               name="rating" value="{{ $rating }}" {{ $rating === 5 ? 'checked' : '' }} required>
                                        <label for="star-{{ $booking->id }}-{{ $rating }}" title="{{ $rating }} star"><i class="bi bi-star-fill"></i></label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="comment-{{ $booking->id }}">Your review</label>
                                <textarea id="comment-{{ $booking->id }}" name="comment" class="form-control" required
                                          placeholder="How was the vehicle, the pickup and our service?">{{ old('comment') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit review</button>
                        </form>
                    </div>
                @empty
                    <div class="empty-state" style="padding:26px 0;">
                        <div class="icon"><i class="bi bi-star-fill"></i></div>
                        <p>Nothing to review right now. Reviews unlock once a rental is completed.</p>
                        <a href="{{ route('bookings.index') }}" class="btn btn-outline mt-16">View my bookings</a>
                    </div>
                @endforelse
            </div>
        </div>

        <div>
            <div class="panel">
                <h2>Reviews you submitted</h2>
                <p class="sub">Reviews appear on the vehicle page once our team approves them.</p>

                @forelse($reviews as $review)
                    <div class="review-card">
                        <div class="review-top">
                            <img src="{{ $review->vehicle->imageUrl() }}" alt="{{ $review->vehicle->name }}">
                            <div style="flex:1; min-width:160px;">
                                <a href="{{ route('vehicles.show', $review->vehicle) }}"><strong>{{ $review->vehicle->name }}</strong></a>
                                <div>{!! star_row($review->rating) !!}</div>
                                <span class="muted small">
                                    {{ $review->booking?->booking_code }} · {{ $review->created_at->format('d M Y') }}
                                </span>
                            </div>
                            {!! status_badge(ucfirst($review->status), $review->statusClass()) !!}
                        </div>
                        <p class="small">{{ $review->comment }}</p>
                    </div>
                @empty
                    <div class="empty-state" style="padding:26px 0;">
                        <div class="icon"><i class="bi bi-pencil-square"></i></div>
                        <p>You have not submitted any reviews yet.</p>
                    </div>
                @endforelse

                <div class="pagination-wrap">{{ $reviews->links() }}</div>
            </div>
        </div>
    </div>
@endsection
