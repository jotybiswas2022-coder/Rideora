<style>
    /* ============ Rideora navbar ============ */
    .site-header {
        --nav-h: 70px;
        position: sticky; top: 0; z-index: 900; background: rgba(255, 255, 255, .94);
        backdrop-filter: blur(12px); border-bottom: 1px solid var(--border);
    }
    .site-header .inner { display: flex; align-items: center; justify-content: space-between; gap: 12px; height: var(--nav-h); }
    .brand { display: inline-flex; align-items: center; gap: 10px; font-weight: 800; font-size: 1.28rem; color: var(--dark); letter-spacing: -.02em; }
    .brand:hover { color: var(--dark); }
    .brand-mark {
        width: 38px; height: 38px; border-radius: 11px; background: linear-gradient(135deg, #2563EB, #1D4ED8);
        display: inline-flex; align-items: center; justify-content: center; color: #fff; font-size: 1rem;
        box-shadow: 0 6px 16px rgba(37, 99, 235, .3);
    }
    .nav-links { display: flex; align-items: center; gap: 4px; list-style: none; }
    .nav-links a {
        display: inline-block; padding: 9px 14px; border-radius: var(--radius-sm); color: var(--muted);
        font-size: .9rem; font-weight: 600;
    }
    .nav-links a:hover { background: var(--primary-soft); color: var(--primary); }
    .nav-links a.active { color: var(--primary); background: var(--primary-soft); }
    .nav-right { display: flex; align-items: center; gap: 10px; }
    .nav-toggle {
        display: none; width: 42px; height: 42px; border: 1px solid var(--border); background: #fff;
        border-radius: var(--radius-sm); cursor: pointer; font-size: 1.1rem; color: var(--dark); align-items: center; justify-content: center;
    }

    /* Notification bell */
    .bell-wrap { position: relative; }
    .bell {
        position: relative; width: 42px; height: 42px; border-radius: var(--radius-sm); border: 1px solid var(--border);
        background: #fff; cursor: pointer; font-size: 1.05rem; display: inline-flex; align-items: center; justify-content: center;
    }
    .bell:hover { border-color: var(--primary); }
    .bell-count {
        position: absolute; top: -6px; right: -6px; min-width: 19px; height: 19px; padding: 0 5px;
        background: var(--danger); color: #fff; border-radius: 999px; font-size: .68rem; font-weight: 700;
        display: inline-flex; align-items: center; justify-content: center; border: 2px solid #fff;
    }
    .bell-panel {
        position: absolute; right: 0; top: calc(100% + 12px); width: 340px; max-width: 92vw; background: #fff;
        border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);
        display: none; overflow: hidden;
    }
    .bell-panel.is-open { display: block; }
    .bell-panel-head { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-bottom: 1px solid var(--border); background: #fcfdff; }
    .bell-panel-head strong { font-size: .88rem; }
    .bell-panel-head button { background: none; border: none; color: var(--primary); font-size: .76rem; font-weight: 600; cursor: pointer; font-family: inherit; }
    .bell-list { max-height: 330px; overflow-y: auto; }
    .bell-item { display: flex; gap: 10px; padding: 12px 16px; border-bottom: 1px solid #F1F5F9; text-decoration: none; color: var(--text); }
    .bell-item:last-child { border-bottom: none; }
    .bell-item:hover { background: #f8fafc; }
    .bell-item.unread { background: var(--primary-soft); }
    .bell-item .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--primary); margin-top: 7px; flex-shrink: 0; }
    .bell-item .body { flex: 1; min-width: 0; }
    .bell-item .title { font-size: .83rem; font-weight: 700; color: var(--dark); }
    .bell-item .msg { font-size: .79rem; color: var(--muted); }
    .bell-item .time { font-size: .7rem; color: #94a3b8; margin-top: 3px; }
    .bell-empty { padding: 28px 16px; text-align: center; color: var(--muted); font-size: .85rem; }
    .bell-foot { display: block; text-align: center; padding: 11px; border-top: 1px solid var(--border); font-size: .82rem; font-weight: 600; background: #fcfdff; }

    /* Account menu */
    .account { position: relative; }
    .account-btn {
        display: inline-flex; align-items: center; gap: 8px; padding: 7px 12px 7px 8px; border-radius: 999px;
        border: 1px solid var(--border); background: #fff; cursor: pointer; font-family: inherit; font-size: .88rem; font-weight: 600; color: var(--dark);
    }
    .account-btn:hover { border-color: var(--primary); }
    .avatar {
        width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, #2563EB, #1D4ED8); color: #fff;
        display: inline-flex; align-items: center; justify-content: center; font-size: .78rem; font-weight: 700; overflow: hidden; flex-shrink: 0;
    }
    .avatar img { width: 100%; height: 100%; object-fit: cover; }
    .account-menu {
        position: absolute; right: 0; top: calc(100% + 10px); min-width: 210px; background: #fff; border: 1px solid var(--border);
        border-radius: var(--radius); box-shadow: var(--shadow-lg); padding: 6px; display: none;
    }
    .account-menu.is-open { display: block; }
    .account-menu a, .account-menu button {
        display: flex; align-items: center; gap: 9px; width: 100%; padding: 9px 12px; border: none; background: none;
        text-align: left; font-size: .87rem; color: var(--text); font-family: inherit; cursor: pointer; border-radius: var(--radius-sm);
        text-decoration: none;
    }
    .account-menu a:hover, .account-menu button:hover { background: var(--light); color: var(--primary); }
    .account-menu .sep { height: 1px; background: var(--border); margin: 5px 0; }
    .account-menu .danger { color: var(--danger); }
    .account-menu .danger:hover { background: var(--danger-soft); color: var(--danger); }

    /* ============ Responsive ============ */
    @media (max-width: 900px) {
        .nav-toggle { display: inline-flex; }
        .nav-links {
            position: absolute; left: 0; right: 0; top: var(--nav-h); flex-direction: column; align-items: stretch; gap: 2px;
            background: #fff; border-bottom: 1px solid var(--border); padding: 12px 16px 18px; box-shadow: var(--shadow);
            display: none; max-height: calc(100vh - var(--nav-h)); overflow-y: auto;
        }
        .nav-links.is-open { display: flex; }
        .nav-links a { padding: 13px 14px; font-size: .95rem; }
        .nav-right .btn span.label { display: none; }
    }

    @media (max-width: 560px) {
        .site-header { --nav-h: 62px; }
        .brand { font-size: 1.16rem; gap: 8px; }
        .brand-mark { width: 34px; height: 34px; border-radius: 10px; }
        .brand-mark svg { width: 17px; height: 17px; }
        .nav-right { gap: 8px; }
        .nav-right .btn { padding: 9px 13px; font-size: .84rem; }
        .bell, .nav-toggle { width: 40px; height: 40px; }
        .account-btn { padding: 5px 9px 5px 5px; font-size: .84rem; }
        .avatar { width: 28px; height: 28px; }

        /* Dropdowns span the viewport instead of overflowing it */
        .bell-panel {
            position: fixed; left: 12px; right: 12px; top: calc(var(--nav-h) + 8px); width: auto; max-width: none;
            max-height: calc(100vh - var(--nav-h) - 24px);
        }
        .account-menu { min-width: 0; width: min(260px, calc(100vw - 24px)); }
    }

    @media (max-width: 380px) {
        .brand span:last-child { font-size: 1.02rem; }
        .nav-right .btn { padding: 8px 11px; font-size: .8rem; }
    }
</style>

<header class="site-header">
    <div class="container inner">
        <a href="{{ route('home') }}" class="brand">
            <span class="brand-mark" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 17h14M5 17a2 2 0 1 0 4 0m6 0a2 2 0 1 0 4 0M4 17V9l2-4h12l2 4v8"/>
                </svg>
            </span>
            <span>{{ setting('site_name', 'Rideora') }}</span>
        </a>

        <nav>
            <ul class="nav-links" data-nav-menu>
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                <li><a href="{{ route('vehicles.index') }}" class="{{ request()->routeIs('vehicles.*') ? 'active' : '' }}">Vehicles</a></li>
                <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a></li>
                <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a></li>

                @auth
                    <li><a href="{{ route('customer.dashboard') }}" class="{{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                    <li><a href="{{ route('bookings.index') }}" class="{{ request()->routeIs('bookings.*') ? 'active' : '' }}">My Bookings</a></li>
                @endauth

                <li>
                    <a href="{{ auth()->user()?->isAdmin() ? route('admin.dashboard') : route('login') }}"
                       class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">Admin Panel</a>
                </li>
            </ul>
        </nav>

        <div class="nav-right">
            @auth
                <div class="bell-wrap">
                    <button type="button" class="bell" data-bell-toggle aria-label="Notifications">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>
                        </svg>
                        <span class="bell-count" data-bell-count hidden>0</span>
                    </button>
                    <div class="bell-panel" data-bell-panel>
                        <div class="bell-panel-head">
                            <strong>Notifications</strong>
                            <button type="button" data-bell-read-all hidden>Mark all read</button>
                        </div>
                        <div class="bell-list" data-bell-list>
                            <div class="bell-empty">Loading…</div>
                        </div>
                        <a href="{{ route('notifications.index') }}" class="bell-foot">View all notifications</a>
                    </div>
                </div>

                <div class="account">
                    <button type="button" class="account-btn" data-account-toggle>
                        <span class="avatar">
                            @if(auth()->user()->avatar_path)
                                <img src="{{ \App\Models\VehicleImage::publicUrl(auth()->user()->avatar_path) }}" alt="{{ auth()->user()->name }}">
                            @else
                                {{ auth()->user()->initials() }}
                            @endif
                        </span>
                        <span>{{ \Illuminate\Support\Str::limit(auth()->user()->name, 14) }}</span>
                    </button>
                    <div class="account-menu" data-account-menu>
                        <a href="{{ route('customer.dashboard') }}">Dashboard</a>
                        <a href="{{ route('bookings.index') }}">My Bookings</a>
                        <a href="{{ route('reviews.index') }}">My Reviews</a>
                        <a href="{{ route('profile.index') }}">Profile</a>
                        <a href="{{ route('notifications.index') }}">Notifications</a>
                        @if(auth()->user()->isAdmin())
                            <div class="sep"></div>
                            <a href="{{ route('admin.dashboard') }}">Admin Panel</a>
                        @endif
                        <div class="sep"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="danger">Sign out</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">Login</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
            @endauth

            <button type="button" class="nav-toggle" data-nav-toggle aria-expanded="false" aria-label="Toggle navigation"><i class="bi bi-list"></i></button>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function closeAll() {
            document.querySelectorAll('[data-bell-panel], [data-account-menu]').forEach(function (el) {
                el.classList.remove('is-open');
            });
        }

        var accountToggle = document.querySelector('[data-account-toggle]');
        if (accountToggle) {
            accountToggle.addEventListener('click', function (event) {
                event.stopPropagation();
                var menu = document.querySelector('[data-account-menu]');
                var isOpen = menu.classList.contains('is-open');
                closeAll();
                if (!isOpen) { menu.classList.add('is-open'); }
            });
        }

        var bellToggle = document.querySelector('[data-bell-toggle]');
        var bellList = document.querySelector('[data-bell-list]');
        var bellCount = document.querySelector('[data-bell-count]');
        var readAll = document.querySelector('[data-bell-read-all]');
        var unread = 0;

        function renderBell(data) {
            unread = data.unread;
            if (unread > 0) {
                bellCount.textContent = unread > 9 ? '9+' : unread;
                bellCount.hidden = false;
            } else {
                bellCount.hidden = true;
            }

            if (readAll) { readAll.hidden = unread === 0; }

            if (!data.items.length) {
                bellList.innerHTML = '<div class="bell-empty">You have no notifications yet.</div>';
                return;
            }

            bellList.innerHTML = data.items.map(function (item) {
                var url = item.link || '#';
                return '<a class="bell-item ' + (item.is_read ? '' : 'unread') + '" href="' + url + '" data-notif-id="' + item.id + '">' +
                    (item.is_read ? '' : '<span class="dot"></span>') +
                    '<span class="body">' +
                    '<span class="title">' + item.title + '</span>' +
                    '<span class="msg">' + item.message + '</span>' +
                    '<span class="time">' + item.time + '</span>' +
                    '</span></a>';
            }).join('');
        }

        function loadBell() {
            if (!bellList) { return Promise.resolve(); }
            return fetch('{{ route('notifications.dropdown') }}', { headers: { 'Accept': 'application/json' } })
                .then(function (response) { return response.json(); })
                .then(renderBell)
                .catch(function () {
                    bellList.innerHTML = '<div class="bell-empty">Could not load notifications.</div>';
                });
        }

        if (bellToggle) {
            loadBell();

            bellToggle.addEventListener('click', function (event) {
                event.stopPropagation();
                var panel = document.querySelector('[data-bell-panel]');
                var isOpen = panel.classList.contains('is-open');
                closeAll();
                if (!isOpen) {
                    panel.classList.add('is-open');
                    loadBell();
                }
            });
        }

        if (readAll) {
            readAll.addEventListener('click', function () {
                readAll.disabled = true;
                fetch('{{ route('notifications.read-all') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    }
                }).then(loadBell).catch(function () {
                    readAll.disabled = false;
                });
            });
        }

        if (bellList) {
            bellList.addEventListener('click', function (event) {
                var item = event.target.closest('[data-notif-id]');
                if (!item) { return; }
                event.preventDefault();
                var id = item.getAttribute('data-notif-id');
                fetch('{{ url('/notifications') }}/' + id + '/read', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    }
                }).then(function () {
                    window.location.href = item.getAttribute('href');
                });
            });
        }

        // Close on outside clicks only, otherwise clicking a control inside the
        // panel (e.g. "Mark all read") would shut the panel on the same click.
        document.addEventListener('click', function (event) {
            if (event.target.closest('[data-bell-panel], [data-account-menu], [data-bell-toggle], [data-account-toggle]')) {
                return;
            }
            closeAll();
        });
    });
</script>
