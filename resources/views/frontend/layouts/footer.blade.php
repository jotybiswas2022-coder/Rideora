<style>
    /* ============ Rideora footer ============ */
    .site-footer { background: var(--dark); color: #CBD5E1; margin-top: auto; }
    .site-footer .top { padding: 52px 0 30px; display: grid; grid-template-columns: 1.4fr 1fr 1fr 1.2fr; gap: 34px; }
    .site-footer h4 { color: #fff; font-size: .95rem; margin-bottom: 14px; }
    .site-footer p { font-size: .86rem; color: #94A3B8; }
    .site-footer a { color: #CBD5E1; font-size: .87rem; }
    .site-footer a:hover { color: #fff; }
    .footer-brand { display: inline-flex; align-items: center; gap: 10px; color: #fff; font-weight: 800; font-size: 1.2rem; margin-bottom: 12px; }
    .footer-brand .brand-mark { box-shadow: none; }
    .footer-links { list-style: none; display: flex; flex-direction: column; gap: 9px; }
    .footer-contact { list-style: none; display: flex; flex-direction: column; gap: 10px; font-size: .86rem; }
    .footer-contact li { display: flex; gap: 9px; align-items: flex-start; }
    .socials { display: flex; gap: 10px; margin-top: 16px; }
    .socials a {
        width: 36px; height: 36px; border-radius: 10px; background: rgba(255, 255, 255, .08);
        display: inline-flex; align-items: center; justify-content: center; font-size: .78rem; font-weight: 700; color: #E2E8F0;
    }
    .socials a:hover { background: var(--primary); color: #fff; }
    .site-footer .bottom {
        border-top: 1px solid rgba(255, 255, 255, .1); padding: 18px 0; display: flex;
        align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; font-size: .82rem; color: #94A3B8;
    }
    @media (max-width: 900px) {
        .site-footer .top { grid-template-columns: 1fr 1fr; gap: 26px; padding: 40px 0 24px; }
    }

    @media (max-width: 620px) {
        .site-footer .top { grid-template-columns: 1fr; gap: 26px; padding: 34px 0 22px; }
        .footer-brand { font-size: 1.1rem; }
        .site-footer .bottom { flex-direction: column; align-items: flex-start; text-align: left; gap: 8px; }
    }
</style>

<footer class="site-footer">
    <div class="container">
        <div class="top">
            <div>
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
                    @if(setting('facebook_url'))<a href="{{ setting('facebook_url') }}" target="_blank" rel="noopener">FB</a>@endif
                    @if(setting('instagram_url'))<a href="{{ setting('instagram_url') }}" target="_blank" rel="noopener">IG</a>@endif
                    @if(setting('twitter_url'))<a href="{{ setting('twitter_url') }}" target="_blank" rel="noopener">X</a>@endif
                    @if(setting('youtube_url'))<a href="{{ setting('youtube_url') }}" target="_blank" rel="noopener">YT</a>@endif
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

            <div>
                <h4>Contact</h4>
                <ul class="footer-contact">
                    <li><span>&#128205;</span><span>{{ setting('office_address', 'Dhaka, Bangladesh') }}</span></li>
                    <li><span>&#9742;</span><span>{{ setting('support_phone', '+880 1700-000000') }}</span></li>
                    <li><span>&#9993;</span><span>{{ setting('support_email', 'support@rideora.test') }}</span></li>
                </ul>
            </div>
        </div>

        <div class="bottom">
            <span>&copy; {{ date('Y') }} {{ setting('site_name', 'Rideora') }}. All rights reserved.</span>
            <span>Manual payment verification &middot; bKash &middot; Nagad &middot; Bank Transfer</span>
        </div>
    </div>
</footer>
