@extends('backend.app')

@section('content')

<div class="db-page">

    {{-- BG Orbs --}}
    <div class="db-orb db-orb-1"></div>
    <div class="db-orb db-orb-2"></div>
    <div class="db-orb db-orb-3"></div>
    <div class="db-orb db-orb-4"></div>

    {{-- Particles --}}
    <div class="db-particles" id="dbParticles"></div>

    {{-- Dashboard Header --}}

    {{-- Stat Cards --}}


    {{-- Recent Messages --}}
    <div class="db-messages">
        <div class="db-messages-header">
            <div class="db-messages-header-left">
                <div class="db-messages-icon"><i class="bi bi-chat-dots"></i></div>
                <div>
                    <h2 class="db-messages-title">Recent Messages</h2>
                    <p class="db-messages-sub">Latest inquiries from the contact form.</p>
                </div>
            </div>
            <a href="{{ route('contact.index') }}" class="db-messages-link">
                View all <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        @if($contacts->isEmpty())
            <div class="db-empty">
                <div class="db-empty-icon"><i class="bi bi-inbox"></i></div>
                <h3 class="db-empty-title">No messages yet</h3>
                <p class="db-empty-desc">Messages from the contact form will appear here.</p>
            </div>
        @else
            <div class="db-table-wrap">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th class="db-th db-th-name">Name</th>
                            <th class="db-th db-th-email">Email</th>
                            <th class="db-th db-th-msg">Message</th>
                            <th class="db-th db-th-date">Date</th>
                            <th class="db-th db-th-time">Time</th>
                            <th class="db-th db-th-action">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contacts as $index => $contact)
                            <tr class="db-tr">
                                <td class="db-td db-td-name">
                                    <div class="db-avatar">
                                        {{ strtoupper(substr($contact->name, 0, 1)) }}
                                    </div>
                                    <span>{{ $contact->name }}</span>
                                </td>
                                <td class="db-td db-td-email">
                                    <a href="mailto:{{ $contact->email }}" class="db-email-link">{{ $contact->email }}</a>
                                </td>
                                <td class="db-td db-td-msg">
                                    <button class="db-view-btn" data-bs-toggle="modal" data-bs-target="#msgModal{{ $contact->id }}">
                                        <i class="bi bi-eye"></i> View
                                    </button>

                                    {{-- Message Modal --}}
                                    <div class="modal fade" id="msgModal{{ $contact->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="db-modal-content">
                                                <div class="db-modal-header">
                                                    <div class="db-modal-avatar" style="background:linear-gradient(135deg,#2563EB,#1E40AF);">
                                                        {{ strtoupper(substr($contact->name, 0, 1)) }}
                                                    </div>
                                                    <div class="db-modal-info">
                                                        <h5 class="db-modal-name">{{ $contact->name }}</h5>
                                                        <span class="db-modal-email">{{ $contact->email }}</span>
                                                    </div>
                                                    <button type="button" class="db-modal-close" data-bs-dismiss="modal">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </div>
                                                <div class="db-modal-body">
                                                    <div class="db-modal-meta">
                                                        <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($contact->created_at)->timezone('Asia/Dhaka')->format('d M Y') }}</span>
                                                        <span><i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($contact->created_at)->timezone('Asia/Dhaka')->format('h:i A') }}</span>
                                                    </div>
                                                    <div class="db-modal-divider"></div>
                                                    <p class="db-modal-text">{{ $contact->message }}</p>
                                                </div>
                                                <div class="db-modal-footer">
                                                    <button type="button" class="db-btn-secondary" data-bs-dismiss="modal">
                                                        Close
                                                    </button>
                                                    <a href="mailto:{{ $contact->email }}" class="db-btn-primary">
                                                        <i class="bi bi-reply-fill"></i> Reply
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="db-td db-td-date">
                                    {{ \Carbon\Carbon::parse($contact->created_at)->timezone('Asia/Dhaka')->format('d M Y') }}
                                </td>
                                <td class="db-td db-td-time">
                                    {{ \Carbon\Carbon::parse($contact->created_at)->timezone('Asia/Dhaka')->format('h:i A') }}
                                </td>
                                <td class="db-td db-td-action">
                                    <button class="db-action-btn" data-bs-toggle="modal" data-bs-target="#msgModal{{ $contact->id }}" title="View message">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

<style>
/* ===== RESET (scoped) ===== */
.db-page {
    --clr-primary: #2563EB;
    --clr-light: #60A5FA;
    --clr-dark: #1E40AF;
    --clr-bg: #0f172a;
    --clr-card: rgba(255,255,255,0.04);
    --clr-border: rgba(255,255,255,0.06);
    --clr-text: #f1f5f9;
    --clr-muted: #94a3b8;
    --clr-hover: rgba(37,99,235,0.08);
    --font: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;

    font-family: var(--font);
    color: var(--clr-text);
    -webkit-font-smoothing: antialiased;
    position: relative;
    background: var(--clr-bg);
    min-height: calc(100vh - 80px);
    padding: 28px 24px;
    overflow: hidden;
}

/* ===== ORBS ===== */
.db-orb {
    position: fixed; border-radius: 50%; filter: blur(80px); pointer-events: none; z-index: 0;
}
.db-orb-1 {
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(37,99,235,0.1), transparent 70%);
    top: -200px; right: -100px;
    animation: dbo1 14s ease-in-out infinite;
}
.db-orb-2 {
    width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(96,165,250,0.07), transparent 70%);
    bottom: -150px; left: -80px;
    animation: dbo2 16s ease-in-out infinite;
}
.db-orb-3 {
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(30,64,175,0.08), transparent 70%);
    top: 30%; left: 60%;
    animation: dbo3 18s ease-in-out infinite;
}
.db-orb-4 {
    width: 250px; height: 250px;
    background: radial-gradient(circle, rgba(37,99,235,0.05), transparent 70%);
    bottom: 20%; right: 30%;
    animation: dbo1 22s ease-in-out infinite reverse;
}
@keyframes dbo1 { 0%,100% { transform:translate(0,0) scale(1); } 50% { transform:translate(60px,40px) scale(1.1); } }
@keyframes dbo2 { 0%,100% { transform:translate(0,0) scale(1); } 50% { transform:translate(-40px,-60px) scale(1.08); } }
@keyframes dbo3 { 0%,100% { transform:translate(0,0) scale(1); } 50% { transform:translate(25px,-35px) scale(1.12); } }

/* ===== PARTICLES ===== */
.db-particles { position:fixed; inset:0; overflow:hidden; pointer-events:none; z-index:1; }
.db-p {
    position:absolute;
    background:linear-gradient(135deg,var(--clr-primary),var(--clr-light));
    border-radius:50%;
    animation:dbr linear infinite;
}
@keyframes dbr {
    0% { transform:translateY(0) rotate(0deg); opacity:0; }
    10% { opacity:0.35; }
    90% { opacity:0.1; }
    100% { transform:translateY(-100vh) rotate(360deg); opacity:0; }
}

/* ===== ANIMATIONS ===== */
@keyframes fadeUp {
    from { opacity:0; transform:translateY(24px); }
    to { opacity:1; transform:translateY(0); }
}
@keyframes fadeIn {
    from { opacity:0; }
    to { opacity:1; }
}
.db-header.animate-in {
    animation:fadeUp 0.8s cubic-bezier(.16,1,.3,1) forwards;
}

/* ===== HEADER ===== */
.db-header {
    position:relative; z-index:5;
    background:linear-gradient(135deg, rgba(37,99,235,0.08), rgba(30,64,175,0.04));
    border:1px solid rgba(37,99,235,0.1);
    border-radius:20px; padding:28px 32px; margin-bottom:24px;
    overflow:hidden;
}
.db-header-bg {
    position:absolute; inset:0;
    background:
        radial-gradient(ellipse at 20% 50%, rgba(37,99,235,0.1), transparent 60%),
        radial-gradient(ellipse at 80% 20%, rgba(96,165,250,0.06), transparent 50%);
    pointer-events:none;
}
.db-header-glow {
    position:absolute; bottom:0; left:0; right:0; height:1px;
    background:linear-gradient(90deg,transparent,rgba(37,99,235,0.2),transparent);
}
.db-header-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

/* ── Admin Logo ── */
.db-admin-logo {
    position: relative;
    flex-shrink: 0;
}
.db-admin-logo-img {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(37,99,235,0.3);
    box-shadow: 0 4px 16px rgba(37,99,235,0.2);
    transition: all 0.3s ease;
}
.db-admin-logo-img:hover {
    border-color: rgba(96,165,250,0.6);
    box-shadow: 0 6px 24px rgba(37,99,235,0.3);
    transform: scale(1.05);
}
.db-admin-logo-fallback {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--clr-primary), var(--clr-dark));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    color: #fff;
    border: 2px solid rgba(37,99,235,0.3);
    box-shadow: 0 4px 16px rgba(37,99,235,0.2);
    transition: all 0.3s ease;
}
.db-admin-logo-fallback:hover {
    transform: scale(1.05);
    border-color: rgba(96,165,250,0.6);
}
.db-admin-logo-dot {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 14px;
    height: 14px;
    background: #22c55e;
    border: 2.5px solid var(--clr-bg);
    border-radius: 50%;
    animation: dbPulse 2s ease-in-out infinite;
}

.db-header-content {
    position:relative; z-index:1;
    display:flex; align-items:center; justify-content:space-between; gap:24px; flex-wrap:wrap;
}
.db-greeting {
    display:inline-flex; align-items:center; gap:7px;
    font-size:0.8rem; font-weight:600; color:var(--clr-light);
    margin-bottom:8px; letter-spacing:0.3px;
}
.db-greeting-dot {
    width:6px; height:6px; border-radius:50%; background:#22c55e;
    animation:dbPulse 2s ease-in-out infinite;
}
@keyframes dbPulse { 0%,100% { box-shadow:0 0 0 0 rgba(34,197,94,0.5); } 50% { box-shadow:0 0 0 5px rgba(34,197,94,0); } }
.db-header-title {
    font-size:1.6rem; font-weight:800; color:var(--clr-text);
    margin-bottom:4px; letter-spacing:-0.3px;
}
.db-header-name {
    background:linear-gradient(135deg,var(--clr-light),var(--clr-primary));
    -webkit-background-clip:text; -webkit-text-fill-color:transparent;
    background-clip:text;
}
.db-header-sub { font-size:0.88rem; color:var(--clr-muted); }
.db-header-right { display:flex; gap:16px; }
.db-header-stat {
    text-align:center; padding:10px 20px;
    background:rgba(255,255,255,0.03);
    border:1px solid rgba(255,255,255,0.05);
    border-radius:12px; min-width:90px;
}
.db-header-stat-num {
    display:block; font-size:1.3rem; font-weight:800; color:var(--clr-light);
    line-height:1.2;
}
.db-header-stat-label {
    display:block; font-size:0.7rem; color:var(--clr-muted); font-weight:500;
    text-transform:uppercase; letter-spacing:0.5px;
}

/* ===== STAT CARDS ===== */
.db-stats {
    display:grid; grid-template-columns:repeat(3,1fr); gap:16px;
    margin-bottom:24px; position:relative; z-index:5;
}
.db-stat-card {
    display:flex; align-items:center; gap:14px;
    background:var(--clr-card); backdrop-filter:blur(16px) saturate(180%);
    -webkit-backdrop-filter:blur(16px) saturate(180%);
    border:1px solid var(--clr-border);
    border-radius:16px; padding:20px 22px;
    transition:all 0.4s cubic-bezier(.16,1,.3,1); cursor:default;
    position:relative; overflow:hidden;
}
.db-stat-card:hover {
    transform:translateY(-4px);
    border-color:var(--accent);
    box-shadow:0 12px 40px rgba(0,0,0,0.2);
}
.db-stat-card::before {
    content:''; position:absolute; top:0; left:0;
    width:4px; height:100%;
    background:var(--accent);
    border-radius:0 2px 2px 0;
}
.db-stat-icon {
    width:48px; height:48px; display:flex; align-items:center; justify-content:center;
    background:var(--accent-bg);
    border-radius:12px; font-size:1.3rem; color:var(--accent); flex-shrink:0;
}
.db-stat-info { flex:1; }
.db-stat-value {
    display:block; font-size:1.5rem; font-weight:800; color:var(--clr-text);
    line-height:1.2; letter-spacing:-0.5px;
}
.db-stat-label {
    display:block; font-size:0.78rem; color:var(--clr-muted); font-weight:500; margin-top:2px;
}
.db-stat-trend {
    display:flex; align-items:center; gap:2px;
    font-size:0.75rem; font-weight:600;
    padding:4px 10px; border-radius:6px;
}
.db-stat-trend.up { background:rgba(16,185,129,0.1); color:#10b981; }
.db-stat-trend i { font-size:1rem; }

/* ===== MESSAGES SECTION ===== */
.db-messages {
    position:relative; z-index:5;
    background:var(--clr-card); backdrop-filter:blur(16px) saturate(180%);
    -webkit-backdrop-filter:blur(16px) saturate(180%);
    border:1px solid var(--clr-border);
    border-radius:20px; overflow:hidden;
    transition:all 0.4s cubic-bezier(.16,1,.3,1);
}
.db-messages:hover { border-color:rgba(37,99,235,0.1); }

.db-messages-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:22px 24px; gap:16px;
    border-bottom:1px solid rgba(255,255,255,0.04);
}
.db-messages-header-left { display:flex; align-items:center; gap:14px; }
.db-messages-icon {
    width:42px; height:42px; display:flex; align-items:center; justify-content:center;
    background:rgba(37,99,235,0.1);
    border:1px solid rgba(37,99,235,0.12);
    border-radius:10px; font-size:1.15rem; color:var(--clr-light);
}
.db-messages-title { font-size:1.1rem; font-weight:700; color:var(--clr-text); margin:0; }
.db-messages-sub { font-size:0.78rem; color:var(--clr-muted); margin:2px 0 0; }
.db-messages-link {
    display:inline-flex; align-items:center; gap:5px;
    font-size:0.82rem; font-weight:600; color:var(--clr-primary);
    text-decoration:none; transition:all 0.3s ease;
    padding:6px 14px; border-radius:8px;
    background:rgba(37,99,235,0.06);
}
.db-messages-link:hover { color:var(--clr-light); gap:8px; background:rgba(37,99,235,0.1); }
.db-messages-link i { transition:transform 0.3s ease; }
.db-messages-link:hover i { transform:translateX(3px); }

/* ===== TABLE ===== */
.db-table-wrap { overflow-x:auto; }
.db-table {
    width:100%; border-collapse:collapse; font-size:0.85rem;
}
.db-th {
    text-align:left; padding:14px 18px; font-weight:600; font-size:0.75rem;
    color:var(--clr-muted); text-transform:uppercase; letter-spacing:0.5px;
    background:rgba(255,255,255,0.02);
    border-bottom:1px solid rgba(255,255,255,0.04);
    white-space:nowrap;
}
.db-tr {
    transition:background 0.3s ease;
}
.db-tr:hover { background:var(--clr-hover); }
.db-td {
    padding:14px 18px; color:var(--clr-text); vertical-align:middle;
    border-bottom:1px solid rgba(255,255,255,0.03);
}
.db-td-name { display:flex; align-items:center; gap:10px; }
.db-avatar {
    width:34px; height:34px; border-radius:50%;
    background:linear-gradient(135deg,var(--clr-primary),var(--clr-dark));
    display:flex; align-items:center; justify-content:center;
    font-size:0.75rem; font-weight:700; color:#fff; flex-shrink:0;
}
.db-email-link { color:var(--clr-primary); text-decoration:none; font-weight:500; transition:color 0.3s; }
.db-email-link:hover { color:var(--clr-light); text-decoration:underline; }

.db-view-btn {
    display:inline-flex; align-items:center; gap:5px;
    padding:5px 13px; font-size:0.78rem; font-weight:600;
    background:rgba(37,99,235,0.08);
    border:1px solid rgba(37,99,235,0.12);
    border-radius:8px; color:var(--clr-light);
    cursor:pointer; transition:all 0.3s ease; font-family:var(--font);
}
.db-view-btn:hover { background:rgba(37,99,235,0.15); transform:translateY(-1px); }

.db-action-btn {
    width:34px; height:34px; display:flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,0.04);
    border:1px solid rgba(255,255,255,0.06);
    border-radius:8px; color:var(--clr-muted);
    cursor:pointer; transition:all 0.3s ease; font-size:0.9rem;
}
.db-action-btn:hover { background:var(--clr-hover); color:var(--clr-light); }

/* ===== EMPTY STATE ===== */
.db-empty {
    text-align:center; padding:60px 20px;
}
.db-empty-icon {
    font-size:2.5rem; color:rgba(255,255,255,0.06); margin-bottom:12px;
}
.db-empty-title { font-size:1.05rem; font-weight:700; color:var(--clr-text); margin-bottom:6px; }
.db-empty-desc { font-size:0.85rem; color:var(--clr-muted); }

/* ===== MODAL ===== */
.db-modal-content {
    background:#1e293b;
    border:1px solid rgba(255,255,255,0.06);
    border-radius:20px; overflow:hidden; box-shadow:0 24px 80px rgba(0,0,0,0.5);
}
.db-modal-header {
    display:flex; align-items:center; gap:14px;
    padding:20px 24px;
    border-bottom:1px solid rgba(255,255,255,0.04);
}
.db-modal-avatar {
    width:44px; height:44px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:1rem; font-weight:700; color:#fff;
}
.db-modal-info { flex:1; }
.db-modal-name { font-size:1rem; font-weight:700; color:var(--clr-text); margin:0; }
.db-modal-email { font-size:0.8rem; color:var(--clr-muted); }
.db-modal-close {
    width:32px; height:32px; display:flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,0.04); border:none; border-radius:8px;
    color:var(--clr-muted); cursor:pointer; transition:all 0.3s ease; font-size:0.8rem;
}
.db-modal-close:hover { background:rgba(255,255,255,0.08); color:var(--clr-text); }

.db-modal-body { padding:24px; }
.db-modal-meta {
    display:flex; gap:20px; margin-bottom:14px; font-size:0.8rem; color:var(--clr-muted);
}
.db-modal-meta i { margin-right:5px; color:var(--clr-primary); }
.db-modal-divider { height:1px; background:rgba(255,255,255,0.04); margin-bottom:16px; }
.db-modal-text {
    font-size:0.92rem; line-height:1.7; color:var(--clr-text); margin:0;
}

.db-modal-footer {
    display:flex; justify-content:flex-end; gap:10px;
    padding:16px 24px;
    border-top:1px solid rgba(255,255,255,0.04);
}
.db-btn-secondary {
    padding:9px 20px; font-size:0.82rem; font-weight:600;
    background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.06);
    border-radius:10px; color:var(--clr-muted); cursor:pointer;
    font-family:var(--font); transition:all 0.3s ease;
}
.db-btn-secondary:hover { background:rgba(255,255,255,0.08); color:var(--clr-text); }
.db-btn-primary {
    display:inline-flex; align-items:center; gap:6px;
    padding:9px 20px; font-size:0.82rem; font-weight:600;
    background:linear-gradient(135deg,var(--clr-primary),var(--clr-dark));
    border:none; border-radius:10px; color:#fff; cursor:pointer;
    text-decoration:none; font-family:var(--font);
    transition:all 0.3s ease; box-shadow:0 4px 12px rgba(37,99,235,0.25);
}
.db-btn-primary:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(37,99,235,0.35); }

/* ===== RESPONSIVE ===== */
@media (max-width: 992px) {
    .db-page { padding:20px 16px; }
    .db-stats { grid-template-columns:repeat(2,1fr); }
    .db-header-content { flex-direction:column; align-items:flex-start; }
    .db-header-right { width:100%; justify-content:space-around; }
    .db-header-stat { min-width:0; flex:1; }
    .db-th-email, .db-td-email { display:none; }
}
@media (max-width: 640px) {
    .db-page { padding:16px 12px; }
    .db-header { padding:20px 18px; border-radius:16px; }
    .db-header-title { font-size:1.25rem; }
    .db-header-right { gap:8px; }
    .db-header-stat { padding:8px 12px; }
    .db-header-stat-num { font-size:1.1rem; }
    .db-stats { grid-template-columns:1fr; gap:12px; }
    .db-messages-header { flex-direction:column; align-items:flex-start; }
    .db-th-date, .db-td-date { display:none; }
    .db-th-time, .db-td-time { display:none; }
    .db-messages { border-radius:14px; }
    .db-stat-card { padding:16px 18px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===================== PARTICLES =====================
    const pc = document.getElementById('dbParticles');
    if (pc) {
        for (let i = 0; i < 25; i++) {
            const p = document.createElement('div');
            p.className = 'db-p';
            const s = Math.random() * 3 + 2;
            p.style.cssText = `
                width:${s}px;height:${s}px;
                left:${Math.random() * 100}%;
                animation-duration:${Math.random() * 14 + 10}s;
                animation-delay:${Math.random() * 5}s;
                bottom:-10px;
                opacity:${Math.random() * 0.35 + 0.1};
            `;
            pc.appendChild(p);
        }
    }

    // ===================== STAGGER ANIMATION =====================
    const cards = document.querySelectorAll('.db-stat-card');
    cards.forEach((el, i) => {
        el.style.animation = `fadeUp 0.6s cubic-bezier(.16,1,.3,1) ${0.2 + i * 0.1}s forwards`;
        el.style.opacity = '0';
    });

    const tableRows = document.querySelectorAll('.db-tr');
    tableRows.forEach((el, i) => {
        el.style.animation = `fadeIn 0.4s ease ${0.4 + i * 0.06}s forwards`;
        el.style.opacity = '0';
    });
});
</script>

@endsection
