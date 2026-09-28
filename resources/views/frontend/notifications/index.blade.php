@extends('frontend.layouts.app')

@section('title', 'Notifications — '.setting('site_name', 'Rideora'))

@push('styles')
<style>
    .notif-head { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-bottom: 20px; }
    .notif-list { display: grid; gap: 12px; }
    .notif {
        display: flex; gap: 14px; padding: 18px; background: #fff; border: 1px solid var(--border);
        border-radius: var(--radius-lg); align-items: flex-start;
    }
    .notif.unread { border-left: 4px solid var(--primary); background: #FBFDFF; }
    .notif-ico {
        width: 42px; height: 42px; border-radius: 12px; background: var(--primary-soft); color: var(--primary);
        display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.05rem;
    }
    .notif-body { flex: 1; min-width: 0; }
    .notif-body h3 { font-size: .96rem; margin-bottom: 3px; }
    .notif-body p { font-size: .87rem; color: var(--muted); }
    .notif-meta { display: flex; align-items: center; gap: 10px; margin-top: 8px; font-size: .78rem; color: #94a3b8; }
    .notif-actions { display: flex; flex-direction: column; gap: 8px; align-items: flex-end; }
    @media (max-width: 640px) {
        .notif { flex-wrap: wrap; padding: 15px; gap: 12px; }
        .notif-actions { align-items: stretch; width: 100%; flex-direction: row; }
        .notif-actions form { flex: 1 1 46%; }
        .notif-actions .btn { width: 100%; }
        .notif-head { flex-direction: column; align-items: stretch; }
        .notif-head .btn { width: 100%; }
    }

    @media (max-width: 420px) {
        .notif-actions { flex-direction: column; }
        .notif-actions form { flex: 1 1 100%; }
    }
</style>
@endpush

@section('content')
    <div class="notif-head">
        <div>
            <h1 style="font-size:1.5rem;">Notifications</h1>
            <p class="muted small">{{ $notifications->total() }} total · {{ auth()->user()->unreadNotificationsCount() }} unread</p>
        </div>

        @if(auth()->user()->unreadNotificationsCount() > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="btn btn-outline">Mark all as read</button>
            </form>
        @endif
    </div>

    @forelse($notifications as $notification)
        <div class="notif {{ $notification->is_read ? '' : 'unread' }}">
            <span class="notif-ico">{!! $notification->icon() !!}</span>

            <div class="notif-body">
                <h3>{{ $notification->title }}</h3>
                <p>{{ $notification->message }}</p>
                <div class="notif-meta">
                    <span>{{ $notification->created_at->format('d M Y, g:i A') }}</span>
                    <span>&middot;</span>
                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                    @unless($notification->is_read)
                        <span>&middot;</span>
                        <span style="color: var(--primary); font-weight: 700;">Unread</span>
                    @endunless
                </div>
            </div>

            <div class="notif-actions">
                @if($notification->link)
                    <form method="POST" action="{{ route('notifications.read', $notification) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">Open</button>
                    </form>
                @endif

                @unless($notification->is_read)
                    <form method="POST" action="{{ route('notifications.read', $notification) }}">
                        @csrf
                        <button type="submit" class="btn btn-light btn-sm">Mark read</button>
                    </form>
                @endunless
            </div>
        </div>
    @empty
        <div class="card card-pad empty-state">
            <div class="icon">&#128276;</div>
            <h3>No notifications yet</h3>
            <p>Booking and payment updates will appear here.</p>
            <a href="{{ route('vehicles.index') }}" class="btn btn-primary mt-16">Browse vehicles</a>
        </div>
    @endforelse

    <div class="pagination-wrap">{{ $notifications->links() }}</div>
@endsection
