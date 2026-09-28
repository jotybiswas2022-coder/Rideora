@php
    $adminNotifications = auth()->user()->notifications()->take(6)->get();
    $adminUnread = auth()->user()->unreadNotificationsCount();
@endphp

<style>
    /* Admin topbar — page specific */
    .admin-topbar .topbar-left > div { min-width: 0; }
    .admin-topbar .topbar-left p { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 46vw; }

    @media (max-width: 620px) {
        .admin-topbar .topbar-left p { display: none; }
        .admin-topbar h1 { font-size: .98rem; }
    }
</style>

<header class="admin-topbar">
    <div class="topbar-left">
        <button type="button" class="sidebar-toggle" data-sidebar-toggle aria-label="Toggle sidebar">&#9776;</button>
        <div>
            <h1>@yield('page-title', 'Dashboard')</h1>
            <p>@yield('page-subtitle', 'Manage the Rideora rental platform')</p>
        </div>
    </div>

    <div class="topbar-right">
        <div class="admin-bell">
            <button type="button" data-admin-bell aria-label="Notifications">
                <span>&#128276;</span>
                @if($adminUnread > 0)
                    <span class="count">{{ $adminUnread > 9 ? '9+' : $adminUnread }}</span>
                @endif
            </button>

            <div class="admin-bell-panel" data-admin-bell-panel>
                <div class="head">Recent notifications</div>
                <div class="list">
                    @forelse($adminNotifications as $notification)
                        <a href="{{ $notification->link ?? route('admin.dashboard') }}">
                            <strong>{{ $notification->title }}</strong>
                            <span>{{ \Illuminate\Support\Str::limit($notification->message, 70) }}</span>
                            <span class="muted" style="display:block; font-size:.72rem;">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </a>
                    @empty
                        <div class="empty">No notifications yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="admin-user">
            <span class="avatar">
                @if(auth()->user()->avatar_path)
                    <img src="{{ \App\Models\VehicleImage::publicUrl(auth()->user()->avatar_path) }}" alt="{{ auth()->user()->name }}">
                @else
                    {{ auth()->user()->initials() }}
                @endif
            </span>
            <span class="who">{{ auth()->user()->name }}</span>
        </div>
    </div>
</header>
