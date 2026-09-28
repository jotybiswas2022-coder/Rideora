<style>
    /* ============ Admin sidebar ============ */
    .admin-sidebar {
        width: var(--sidebar-w); background: var(--sidebar); color: #CBD5E1; flex-shrink: 0;
        display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh;
    }
    .sidebar-brand {
        display: flex; align-items: center; gap: 10px; padding: 20px 20px; color: #fff; font-weight: 800; font-size: 1.15rem;
        border-bottom: 1px solid rgba(255, 255, 255, .07);
    }
    .sidebar-brand:hover { color: #fff; }
    .sidebar-brand .mark {
        width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #2563EB, #1D4ED8);
        display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .sidebar-brand .tag { display: block; font-size: .68rem; font-weight: 600; color: #93C5FD; letter-spacing: .08em; text-transform: uppercase; }

    .sidebar-nav { padding: 14px 12px; overflow-y: auto; flex: 1; }
    .sidebar-section { font-size: .68rem; text-transform: uppercase; letter-spacing: .1em; color: #64748B; padding: 14px 12px 7px; }
    .sidebar-link {
        display: flex; align-items: center; gap: 11px; padding: 10px 12px; border-radius: var(--radius-sm);
        color: #CBD5E1; font-size: .88rem; font-weight: 500; margin-bottom: 2px;
    }
    .sidebar-link:hover { background: var(--sidebar-hover); color: #fff; }
    .sidebar-link.active { background: var(--primary); color: #fff; box-shadow: 0 6px 16px rgba(37, 99, 235, .3); }
    .sidebar-link .ico { width: 20px; text-align: center; font-size: 1rem; flex-shrink: 0; }
    .sidebar-link .pill {
        margin-left: auto; background: var(--danger); color: #fff; font-size: .68rem; font-weight: 700;
        padding: 1px 7px; border-radius: 999px;
    }
    .sidebar-link.active .pill { background: rgba(255, 255, 255, .28); }

    .sidebar-foot { padding: 14px 12px 18px; border-top: 1px solid rgba(255, 255, 255, .07); }
    .sidebar-foot form button {
        display: flex; align-items: center; gap: 11px; width: 100%; padding: 10px 12px; border: none; background: none;
        color: #FCA5A5; font-size: .88rem; font-weight: 600; font-family: inherit; cursor: pointer; border-radius: var(--radius-sm);
        text-align: left;
    }
    .sidebar-foot form button:hover { background: rgba(220, 38, 38, .14); }

    .sidebar-scrim { display: none; }

    @media (max-width: 1000px) {
        .admin-sidebar {
            position: fixed; left: 0; top: 0; bottom: 0; z-index: 120; transform: translateX(-100%);
            transition: transform .22s ease;
        }
        body.sidebar-open .admin-sidebar { transform: translateX(0); }
        body.sidebar-open .sidebar-scrim { display: block; position: fixed; inset: 0; background: rgba(15, 23, 42, .45); z-index: 110; }
    }
</style>

<div class="sidebar-scrim" data-sidebar-scrim></div>

<aside class="admin-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
        <span class="mark">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 17h14M5 17a2 2 0 1 0 4 0m6 0a2 2 0 1 0 4 0M4 17V9l2-4h12l2 4v8"/>
            </svg>
        </span>
        <span>
            {{ setting('site_name', 'Rideora') }}
            <span class="tag">Admin panel</span>
        </span>
    </a>

    @php
        $pendingPaymentsCount = \App\Models\Payment::where('status', \App\Models\Payment::STATUS_PENDING)->count();
        $pendingReviewsCount = \App\Models\Review::where('status', \App\Models\Review::STATUS_PENDING)->count();
    @endphp

    <nav class="sidebar-nav">
        <div class="sidebar-section">Overview</div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="ico">&#128200;</span> Dashboard
        </a>

        <div class="sidebar-section">Fleet</div>
        <a href="{{ route('admin.vehicles.index') }}" class="sidebar-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}">
            <span class="ico">&#128663;</span> Vehicles
        </a>
        <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <span class="ico">&#127991;</span> Categories
        </a>

        <div class="sidebar-section">Rentals</div>
        <a href="{{ route('admin.bookings.index') }}" class="sidebar-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
            <span class="ico">&#128203;</span> Bookings
        </a>
        <a href="{{ route('admin.payments.index') }}" class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <span class="ico">&#2547;</span> Payments
            @if($pendingPaymentsCount > 0)
                <span class="pill">{{ $pendingPaymentsCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.payment-methods.index') }}" class="sidebar-link {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">
            <span class="ico">&#128179;</span> Payment Methods
        </a>

        <div class="sidebar-section">People</div>
        <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <span class="ico">&#128101;</span> Customers
        </a>
        <a href="{{ route('admin.reviews.index') }}" class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
            <span class="ico">&#9733;</span> Reviews
            @if($pendingReviewsCount > 0)
                <span class="pill">{{ $pendingReviewsCount }}</span>
            @endif
        </a>

        <div class="sidebar-section">System</div>
        <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <span class="ico">&#9881;</span> Website Settings
        </a>
        <a href="{{ route('home') }}" class="sidebar-link" target="_blank" rel="noopener">
            <span class="ico">&#127760;</span> View website
        </a>
    </nav>

    <div class="sidebar-foot">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"><span class="ico">&#128682;</span> Logout</button>
        </form>
    </div>
</aside>
