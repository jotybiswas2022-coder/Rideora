@extends('frontend.layouts.app')

@section('title', 'My Profile — '.setting('site_name', 'Rideora'))

@push('styles')
<style>
    .profile-layout { display: grid; grid-template-columns: 1fr 1.6fr; gap: 26px; align-items: start; }
    .panel { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 26px; }
    .panel + .panel { margin-top: 22px; }
    .profile-card { text-align: center; }
    .profile-avatar {
        width: 92px; height: 92px; border-radius: 50%; margin: 0 auto 14px; overflow: hidden;
        background: linear-gradient(135deg, #2563EB, #1D4ED8); color: #fff; display: flex; align-items: center;
        justify-content: center; font-size: 1.9rem; font-weight: 700;
    }
    .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .profile-card h1 { font-size: 1.25rem; margin-bottom: 4px; }
    .profile-card .email { color: var(--muted); font-size: .88rem; }
    .profile-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-top: 22px; }
    .profile-stat { background: var(--light); border: 1px solid var(--border); border-radius: var(--radius); padding: 12px; }
    .profile-stat strong { display: block; font-size: 1.2rem; color: var(--dark); }
    .profile-stat span { font-size: .74rem; color: var(--muted); text-transform: uppercase; letter-spacing: .05em; }
    .detail-list { display: grid; gap: 14px; }
    .detail-row { display: flex; justify-content: space-between; gap: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border); font-size: .9rem; }
    .detail-row:last-child { border-bottom: none; padding-bottom: 0; }
    .detail-row span { color: var(--muted); }
    .detail-row strong { color: var(--dark); text-align: right; }
    @media (max-width: 900px) {
        .profile-layout { grid-template-columns: 1fr; gap: 18px; }
    }

    @media (max-width: 620px) {
        .panel { padding: 18px; }
        .panel + .panel { margin-top: 16px; }
        .profile-avatar { width: 78px; height: 78px; font-size: 1.6rem; }
        .profile-stats { gap: 10px; }
        .profile-stat { padding: 11px; }
        .profile-stat strong { font-size: 1.05rem; }
        .profile-card .stack-8 .btn { width: 100%; }
    }

    @media (max-width: 480px) {
        .detail-row { flex-direction: column; align-items: flex-start; gap: 3px; }
        .detail-row strong { text-align: left; }
        .profile-stats { grid-template-columns: 1fr 1fr; }
    }
</style>
@endpush

@section('content')
    <div class="page-header">
        <h1>My profile</h1>
        <p>Your account information and rental history summary.</p>
    </div>

    <div class="profile-layout">
        <div>
            <div class="panel profile-card">
                <div class="profile-avatar">
                    @if($user->avatar_path)
                        <img src="{{ \App\Models\VehicleImage::publicUrl($user->avatar_path) }}" alt="{{ $user->name }}">
                    @else
                        {{ $user->initials() }}
                    @endif
                </div>
                <h1>{{ $user->name }}</h1>
                <p class="email">{{ $user->email }}</p>
                <div class="flex-center" style="justify-content:center; margin-top:10px;">
                    {!! status_badge(ucfirst($user->status), $user->status === 'active' ? 'badge-success' : 'badge-danger') !!}
                    {!! status_badge('Customer', 'badge-primary') !!}
                </div>

                <div class="profile-stats">
                    <div class="profile-stat"><strong>{{ $stats['bookings'] }}</strong><span>Bookings</span></div>
                    <div class="profile-stat"><strong>{{ $stats['completed'] }}</strong><span>Completed</span></div>
                    <div class="profile-stat"><strong>{{ $stats['reviews'] }}</strong><span>Reviews</span></div>
                    <div class="profile-stat"><strong>{{ bdt($stats['spent']) }}</strong><span>Total paid</span></div>
                </div>

                <div class="stack-8 mt-24">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-block">Edit profile</a>
                    <a href="{{ route('bookings.index') }}" class="btn btn-outline btn-block">My bookings</a>
                </div>
            </div>

            <div class="panel">
                <h2 style="font-size:1.05rem; margin-bottom:16px;">Account information</h2>
                <div class="detail-list">
                    <div class="detail-row"><span>Full name</span><strong>{{ $user->name }}</strong></div>
                    <div class="detail-row"><span>Email</span><strong>{{ $user->email }}</strong></div>
                    <div class="detail-row"><span>Phone</span><strong>{{ $user->phone ?: 'Not provided' }}</strong></div>
                    <div class="detail-row"><span>City</span><strong>{{ $user->city ?: 'Not provided' }}</strong></div>
                    <div class="detail-row"><span>Address</span><strong>{{ $user->address ?: 'Not provided' }}</strong></div>
                    <div class="detail-row"><span>Driving licence</span><strong>{{ $user->driving_license_no ?: 'Not provided' }}</strong></div>
                    <div class="detail-row"><span>Member since</span><strong>{{ $user->created_at->format('d M Y') }}</strong></div>
                </div>
            </div>
        </div>

        <div>
            <div class="panel">
                <h2 style="font-size:1.05rem; margin-bottom:8px;">Recent activity</h2>
                <p class="muted small mb-16">Your five most recent bookings.</p>

                @forelse($user->bookings()->with('vehicle')->latest()->take(5)->get() as $booking)
                    <div class="detail-row">
                        <span>
                            <strong style="display:block; color:var(--dark);">{{ $booking->vehicle->name }}</strong>
                            {{ $booking->booking_code }} · {{ $booking->pickup_date->format('d M Y') }}
                        </span>
                        <strong>{!! status_badge($booking->statusLabel(), $booking->statusClass()) !!}</strong>
                    </div>
                @empty
                    <div class="empty-state" style="padding:26px 0;">
                        <div class="icon"><i class="bi bi-car-front"></i></div>
                        <p>No bookings yet.</p>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-primary mt-16">Browse vehicles</a>
                    </div>
                @endforelse
            </div>

            <div class="panel">
                <h2 style="font-size:1.05rem; margin-bottom:16px;">Security</h2>
                <p class="muted small">
                    Passwords are hashed with bcrypt and never stored in plain text. Change your password from the
                    <a href="{{ route('profile.edit') }}">edit profile</a> page whenever you need to.
                </p>
                <div class="divider"></div>
                <p class="muted small">
                    Need to close your account? <a href="{{ route('contact') }}">Contact our team</a> and we will help.
                </p>
            </div>
        </div>
    </div>
@endsection
