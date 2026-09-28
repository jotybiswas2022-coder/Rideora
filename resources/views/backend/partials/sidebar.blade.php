@php
use Illuminate\Support\Str;
@endphp

<!-- Top Navbar -->
<nav class="top-navbar">
    <div class="top-nav-inner">
        <a class="top-nav-brand" href="/admin">
            <i class="bi bi-speedometer2"></i>
            <span>Admin Dashboard</span>
        </a>

        <button class="nav-toggler" type="button" onclick="document.getElementById('navbarTopNav').classList.toggle('show')">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="top-nav-links" id="navbarTopNav">
            <a class="nav-link-custom {{ request()->is('/') ? 'active' : '' }}" href="/">
                <i class="bi bi-house-door"></i> Home
            </a>

            @auth
                @if(auth()->user()->is_admin == 1)
                    <a class="nav-link-custom {{ Str::startsWith(request()->path(), 'admin') ? 'active' : '' }}" href="/admin">
                        <i class="bi bi-speedometer2"></i> Admin Panel
                    </a>
                @endif
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="nav-link-btn logout-btn">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            @else
                <a class="nav-link-custom {{ request()->is('login') ? 'active' : '' }}" href="/login">
                    <i class="bi bi-person-circle"></i> Login
                </a>
                <a class="nav-link-custom signup-link" href="/register">
                    <i class="bi bi-person-plus"></i> Signup
                </a>
            @endauth
        </div>
    </div>
</nav>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-layout-sidebar"></i>
        <span>Navigation</span>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="{{ url('/admin/contact') }}"
               class="{{ request()->is('admin/contact') ? 'active' : '' }}">
                <i class="bi bi-envelope-fill"></i>
                <span>Contact</span>
            </a>
        </li>
    </ul>
    <div class="sidebar-footer">
        <span class="sidebar-version">Connectly v1.0</span>
    </div>
</aside>

<style>
/* ─── Top Navbar (Dark Theme) ─── */
.top-navbar {
    background: linear-gradient(135deg, #0f172a 0%, #131e3a 100%);
    border-bottom: 1px solid rgba(255,255,255,0.06);
    padding: 10px 24px;
    position: sticky;
    top: 0;
    z-index: 100;
}
.top-nav-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 100%;
    gap: 16px;
}
.top-nav-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #f1f5f9;
    font-weight: 700;
    font-size: 1rem;
    text-decoration: none;
    letter-spacing: -0.3px;
}
.top-nav-brand i {
    font-size: 1.3rem;
    color: #60A5FA;
}
.nav-toggler {
    display: none;
    background: transparent;
    border: none;
    color: #94a3b8;
    font-size: 1.3rem;
    padding: 4px;
    cursor: pointer;
}
.nav-toggler:focus { outline: none; }

.top-nav-links {
    display: flex;
    align-items: center;
    gap: 4px;
}
.nav-link-custom {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    color: #94a3b8;
    font-weight: 500;
    font-size: 0.85rem;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.2s ease;
}
.nav-link-custom:hover {
    color: #60A5FA;
    background: rgba(37,99,235,0.08);
}
.nav-link-custom.active {
    color: #60A5FA;
    background: rgba(37,99,235,0.12);
}
.nav-link-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    color: #f87171;
    font-weight: 600;
    font-size: 0.85rem;
    background: transparent;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: inherit;
}
.nav-link-btn:hover {
    background: rgba(248,113,113,0.1);
    color: #ef4444;
}
.signup-link {
    background: linear-gradient(135deg, #2563EB, #1E40AF);
    color: #fff !important;
    padding: 7px 18px;
}
.signup-link:hover {
    background: linear-gradient(135deg, #1E40AF, #1e3a8a);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37,99,235,0.3);
}

/* ─── Sidebar (Dark Theme) ─── */
.sidebar {
    width: 250px;
    min-width: 250px;
    background: linear-gradient(180deg, #0a0f1e 0%, #0f172a 100%);
    border-right: 1px solid rgba(255,255,255,0.05);
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    max-height: calc(100vh - 57px);
}
.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 20px 20px 16px;
    color: #64748b;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}
.sidebar-brand i {
    font-size: 0.9rem;
    color: #475569;
}
.sidebar-menu {
    list-style: none;
    padding: 12px 10px;
    margin: 0;
    flex: 1;
}
.sidebar-menu li { margin-bottom: 2px; }
.sidebar-menu a {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #94a3b8;
    padding: 10px 14px;
    font-weight: 500;
    font-size: 0.88rem;
    border-radius: 10px;
    transition: all 0.2s ease;
    text-decoration: none;
}
.sidebar-menu a i {
    font-size: 1.05rem;
    width: 20px;
    text-align: center;
    flex-shrink: 0;
}
.sidebar-menu a:hover {
    background: rgba(37,99,235,0.1);
    color: #60A5FA;
}
.sidebar-menu a.active {
    background: linear-gradient(135deg, rgba(37,99,235,0.15), rgba(37,99,235,0.05));
    color: #60A5FA;
    border: 1px solid rgba(37,99,235,0.12);
    box-shadow: 0 2px 8px rgba(37,99,235,0.08);
}
.sidebar-footer {
    padding: 14px 20px;
    border-top: 1px solid rgba(255,255,255,0.04);
}
.sidebar-version {
    font-size: 0.65rem;
    color: rgba(148,163,184,0.35);
    letter-spacing: 1px;
    text-transform: uppercase;
}

/* ─── Scrollbar ─── */
.sidebar::-webkit-scrollbar { width: 4px; }
.sidebar::-webkit-scrollbar-track { background: transparent; }
.sidebar::-webkit-scrollbar-thumb { background: rgba(148,163,184,0.12); border-radius: 10px; }

/* ─── Responsive ─── */
@media (max-width: 768px) {
    .nav-toggler { display: block; }
    .top-nav-links {
        display: none;
        flex-direction: column;
        align-items: stretch;
        padding: 8px 0;
        gap: 2px;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #0f172a;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        z-index: 99;
    }
    .top-nav-links.show {
        display: flex;
    }
    .sidebar { width: 100%; min-width: 100%; max-height: none; flex-direction: row; flex-wrap: wrap; }
    .sidebar-brand { display: none; }
    .sidebar-menu { display: flex; flex-wrap: wrap; padding: 8px; gap: 4px; }
    .sidebar-menu li { margin-bottom: 0; }
    .sidebar-menu a { font-size: 0.82rem; padding: 8px 12px; }
    .sidebar-footer { display: none; }
}
</style>
