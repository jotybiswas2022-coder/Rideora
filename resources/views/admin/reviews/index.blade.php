@extends('admin.layouts.app')

@section('title', 'Reviews')
@section('page-title', 'Review moderation')
@section('page-subtitle', 'Approve or reject customer feedback')

@push('styles')
<style>
    .review-card { border: 1px solid var(--border); border-radius: var(--radius); padding: 18px; margin-bottom: 14px; background: #fff; }
    .review-card:last-child { margin-bottom: 0; }
    .review-head { display: flex; gap: 14px; align-items: flex-start; flex-wrap: wrap; }
    .review-avatar {
        width: 42px; height: 42px; border-radius: 50%; background: var(--primary-soft); color: var(--primary-dark);
        display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: .82rem; flex-shrink: 0;
    }
    .review-stars { color: #F59E0B; font-size: 1rem; letter-spacing: 2px; }
    .review-quote { font-size: .89rem; margin-top: 10px; }
    .review-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 14px; }
</style>
@endpush

@section('content')
    <div class="stat-grid mb-24">
        <div class="stat">
            <span class="lbl">Pending</span>
            <span class="val">{{ $stats['pending'] }}</span>
            <span class="note">Awaiting moderation</span>
        </div>
        <div class="stat">
            <span class="lbl">Approved</span>
            <span class="val">{{ $stats['approved'] }}</span>
            <span class="note">Visible on the website</span>
        </div>
        <div class="stat">
            <span class="lbl">Rejected</span>
            <span class="val">{{ $stats['rejected'] }}</span>
            <span class="note">Hidden from customers</span>
        </div>
        <div class="stat highlight">
            <span class="lbl">Average rating</span>
            <span class="val">{{ $stats['average'] ?: '—' }}</span>
            <span class="note">Based on approved reviews</span>
        </div>
    </div>

    <div class="filter-bar">
        <form method="GET" action="{{ route('admin.reviews.index') }}">
            <div class="form-group">
                <label for="q">Search</label>
                <input type="text" id="q" name="q" class="form-control" value="{{ $filters['q'] ?? '' }}"
                       placeholder="Comment, customer or vehicle">
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="">Any status</option>
                    @foreach(\App\Models\Review::STATUSES as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="rating">Rating</label>
                <select id="rating" name="rating" class="form-control">
                    <option value="">Any rating</option>
                    @foreach([5, 4, 3, 2, 1] as $rating)
                        <option value="{{ $rating }}" @selected((string) ($filters['rating'] ?? '') === (string) $rating)>{{ $rating }} stars</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>&nbsp;</label>
                <div class="flex" style="gap:10px;">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-light">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <div class="card-head">
            <div>
                <h3>Reviews</h3>
                <p>{{ $reviews->total() }} reviews found</p>
            </div>
            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="btn btn-primary btn-sm">Pending only</a>
        </div>

        <div class="card-body">
            @forelse($reviews as $review)
                <div class="review-card">
                    <div class="review-head">
                        <span class="review-avatar">{{ $review->user?->initials() ?? 'R' }}</span>

                        <div style="flex:1; min-width:220px;">
                            <div class="flex-between" style="gap:12px;">
                                <div>
                                    <strong style="font-size:.92rem;">{{ $review->user?->name ?? 'Deleted customer' }}</strong>
                                    <span class="muted small" style="display:block;">
                                        {{ $review->vehicle?->name }} · booking {{ $review->booking?->booking_code ?? '—' }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <div class="review-stars">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                                    <span class="muted small">{{ $review->created_at->format('d M Y, g:i A') }}</span>
                                </div>
                            </div>

                            <p class="review-quote">{{ $review->comment }}</p>

                            <div class="flex-wrap flex" style="gap:8px; margin-top:10px;">
                                {!! status_badge(ucfirst($review->status), $review->statusClass()) !!}
                                <a href="{{ route('admin.vehicles.show', $review->vehicle) }}" class="btn btn-light btn-sm">Vehicle</a>
                                @if($review->booking)
                                    <a href="{{ route('admin.bookings.show', $review->booking) }}" class="btn btn-light btn-sm">Booking</a>
                                @endif
                                @if($review->user)
                                    <a href="{{ route('admin.users.show', $review->user) }}" class="btn btn-light btn-sm">Customer</a>
                                @endif
                            </div>

                            <div class="review-actions">
                                @if($review->status !== \App\Models\Review::STATUS_APPROVED)
                                    <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                @endif

                                @if($review->status !== \App\Models\Review::STATUS_REJECTED)
                                    <form method="POST" action="{{ route('admin.reviews.reject', $review) }}"
                                          data-confirm="Reject this review? It will be hidden from the website.">
                                        @csrf
                                        <button type="submit" class="btn btn-warning-soft btn-sm">Reject</button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                                      data-confirm="Delete this review permanently?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger-soft btn-sm">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state" style="padding:34px 0;">
                    <div class="icon">&#9733;</div>
                    <h3>No reviews found</h3>
                    <p>Reviews submitted by customers will appear here for moderation.</p>
                </div>
            @endforelse
        </div>

        @if($reviews->hasPages())
            <div class="card-foot pagination-wrap">{{ $reviews->links() }}</div>
        @endif
    </div>
@endsection
