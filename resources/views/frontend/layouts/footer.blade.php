<style>
    /* ============ Rideora footer ============ */
    .site-footer {
        background: var(--dark); color: #CBD5E1; margin-top: auto;
        border-top: 1px solid rgba(255, 255, 255, .08);
    }
    .site-footer .top { padding: 52px 0 30px; display: grid; grid-template-columns: 1.4fr 1fr 1fr 1.2fr; gap: 34px; }
    .site-footer h4 {
        color: #fff; font-size: .78rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; margin-bottom: 14px;
    }
    .site-footer p { font-size: .86rem; color: #94A3B8; line-height: 1.6; }
    .site-footer a { color: #CBD5E1; font-size: .87rem; transition: color var(--dur-fast, .16s) ease; }
    .site-footer a:hover { color: #fff; }

    .footer-brand { display: inline-flex; align-items: center; gap: 10px; color: #fff; font-weight: 800; font-size: 1.2rem; margin-bottom: 12px; }
    .footer-brand .brand-mark { box-shadow: none; }
    .footer-links { list-style: none; display: flex; flex-direction: column; gap: 2px; }
    .footer-links a { display: inline-flex; align-items: center; min-height: 34px; }

    .footer-contact { list-style: none; display: flex; flex-direction: column; gap: 12px; font-size: .86rem; }
    .footer-contact li { display: flex; gap: 10px; align-items: flex-start; color: #94A3B8; line-height: 1.5; }
    .footer-contact .ico {
        width: 30px; height: 30px; border-radius: 9px; background: rgba(255, 255, 255, .07); color: #93C5FD;
        display: inline-flex; align-items: center; justify-content: center; font-size: .8rem; flex-shrink: 0;
    }
    .footer-contact a { color: #CBD5E1; }

    .socials { display: flex; gap: 9px; margin-top: 18px; }
    .socials a {
        width: 40px; height: 40px; border-radius: 11px; background: rgba(255, 255, 255, .08); color: #E2E8F0;
        display: inline-flex; align-items: center; justify-content: center; font-size: 1rem;
        transition: background var(--dur-fast, .16s) ease, color var(--dur-fast, .16s) ease, transform var(--dur-fast, .16s) ease;
    }
    .socials a:hover { background: var(--primary); color: #fff; transform: translateY(-2px); }

    .site-footer .bottom {
        border-top: 1px solid rgba(255, 255, 255, .1); padding: 18px 0; display: flex;
        align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
        font-size: .82rem; color: #94A3B8;
    }
    .site-footer .bottom .pay { display: inline-flex; align-items: center; gap: 7px; }
    .site-footer .bottom .pay i { color: #6EE7B7; }

    /* ============ Tablet: 2 x 2 ============ */
    @media (max-width: 900px) {
        .site-footer .top { grid-template-columns: 1fr 1fr; gap: 26px 30px; padding: 40px 0 24px; }
    }

    /* ============ Phone: brand full width, then two tight columns ============ */
    @media (max-width: 620px) {
        .site-footer .top { grid-template-columns: 1fr 1fr; gap: 20px 18px; padding: 32px 0 20px; }
        .footer-about, .footer-contact-col { grid-column: 1 / -1; }
        .footer-brand { font-size: 1.1rem; margin-bottom: 8px; }
        .site-footer p { font-size: .82rem; }
        .site-footer h4 { margin-bottom: 8px; }
        .footer-links a { min-height: 40px; font-size: .85rem; }
        .footer-contact { gap: 8px; }
        .footer-contact .ico { width: 28px; height: 28px; }
        .socials { margin-top: 14px; }
        .socials a { width: 44px; height: 44px; }
        .site-footer .bottom {
            flex-direction: column; align-items: flex-start; text-align: left; gap: 6px;
            padding: 14px 0; font-size: .78rem;
        }
    }
</style>

<footer class="site-footer">
    <div class="container">
        <div class="top">
            <div class="footer-about">
                <span class="footer-brand">
                    <span class="brand-mark" aria-hidden="true">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 17h14M5 17a2 2 0 1 0 4 0m6 0a2 2 0 1 0 4 0M4 17V9l2-4h12l2 4v8"/>
                        </svg>
                    </span>
                    <span>{{ setting('site_name', 'Rideora') }}</span>
                </span>
                <p>{{ setting('site_tagline', 'Your Ride, Your Way.') }} Reliable vehicles, flexible rentals and simple booking with manual payment verification.</p>
                <div class="socials">
                    @foreach([
                        ['facebook_url', 'facebook', 'Facebook'],
                        ['instagram_url', 'instagram', 'Instagram'],
                        ['twitter_url', 'twitter-x', 'X (Twitter)'],
                        ['youtube_url', 'youtube', 'YouTube'],
                    ] as [$key, $icon, $label])
                        @if(setting($key))
                            <a href="{{ setting($key) }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $label }}">
                                <i class="bi bi-{{ $icon }}" aria-hidden="true"></i>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div>
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('vehicles.index') }}">Browse Vehicles</a></li>
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>

            <div>
                <h4>Account</h4>
                <ul class="footer-links">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <li><a href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                        @else
                            <li><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                            <li><a href="{{ route('bookings.index') }}">My Bookings</a></li>
                            <li><a href="{{ route('profile.index') }}">Profile</a></li>
                        @endif
                    @else
                        <li><a href="{{ route('login') }}">Login</a></li>
                        <li><a href="{{ route('register') }}">Create Account</a></li>
                    @endauth
                    <li><a href="{{ route('vehicles.index') }}">Rental Fleet</a></li>
                </ul>
            </div>

            <div class="footer-contact-col">
                <h4>Contact</h4>
                <ul class="footer-contact">
                    <li>
                        <span class="ico" aria-hidden="true"><i class="bi bi-geo-alt-fill"></i></span>
                        <span>{{ setting('office_address', 'Dhaka, Bangladesh') }}</span>
                    </li>
                    <li>
                        <span class="ico" aria-hidden="true"><i class="bi bi-telephone-fill"></i></span>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', (string) setting('support_phone', '+880 1700-000000')) }}">{{ setting('support_phone', '+880 1700-000000') }}</a>
                    </li>
                    <li>
                        <span class="ico" aria-hidden="true"><i class="bi bi-envelope-fill"></i></span>
                        <a href="mailto:{{ setting('support_email', 'support@rideora.test') }}">{{ setting('support_email', 'support@rideora.test') }}</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="bottom">
            <span>&copy; {{ date('Y') }} {{ setting('site_name', 'Rideora') }}. All rights reserved.</span>
            <span class="pay"><i class="bi bi-shield-check" aria-hidden="true"></i>Manual payment verification &middot; bKash &middot; Nagad &middot; Bank Transfer</span>
        </div>
    </div>
</footer>
