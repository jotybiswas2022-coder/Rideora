@extends('frontend.layouts.app')

@section('title', 'Create Account — '.setting('site_name', 'Rideora'))

@push('styles')
<style>
    .auth-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 34px; align-items: center; max-width: 1080px; margin: 10px auto 0; }
    .auth-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 34px; box-shadow: var(--shadow); }
    .auth-card h1 { font-size: 1.6rem; margin-bottom: 6px; }
    .auth-card > p { color: var(--muted); font-size: .9rem; margin-bottom: 22px; }
    .auth-side {
        background: linear-gradient(155deg, #0F172A, #1E3A8A); color: #fff; border-radius: var(--radius-lg);
        padding: 40px; min-height: 420px; display: flex; flex-direction: column; justify-content: center;
    }
    .auth-side h2 { color: #fff; font-size: 1.6rem; margin-bottom: 10px; }
    .auth-side p { color: #C7D2E5; font-size: .93rem; }
    .auth-side ul { list-style: none; margin-top: 22px; display: grid; gap: 12px; font-size: .89rem; color: #E2E8F0; }
    .auth-side li { display: flex; gap: 10px; }
    .auth-foot { margin-top: 20px; text-align: center; font-size: .88rem; color: var(--muted); }
    .pw-meter { height: 6px; border-radius: 999px; background: var(--border); overflow: hidden; margin-top: 6px; }
    .pw-meter span { display: block; height: 100%; width: 0; background: var(--danger); transition: width .2s ease, background .2s ease; }
    @media (max-width: 900px) {
        .auth-layout { grid-template-columns: 1fr; gap: 22px; }
        .auth-side { display: none; }
    }

    @media (max-width: 620px) {
        .auth-card { padding: 24px 18px; }
        .auth-card h1 { font-size: 1.35rem; }
        .auth-card .btn-lg { width: 100%; }
    }
</style>
@endpush

@section('content')
    <div class="auth-layout">
        <div class="auth-card">
            <h1>Create your account</h1>
            <p>Register once, then book any vehicle in the fleet.</p>

            <form method="POST" action="{{ route('register.store') }}" class="stack-16">
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Full name</label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required autofocus>
                        @error('name')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone number</label>
                        <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone') }}" placeholder="+880 17xx-xxxxxx" required>
                        @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" class="form-control @error('city') is-invalid @enderror"
                               value="{{ old('city') }}" placeholder="Dhaka">
                        @error('city')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group full">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror"
                               value="{{ old('address') }}" placeholder="House, road, area">
                        @error('address')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                               required data-pw-strength autocomplete="new-password">
                        <div class="pw-meter"><span data-pw-bar></span></div>
                        <span class="form-hint">At least 8 characters with letters and numbers.</span>
                        @error('password')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required
                               autocomplete="new-password">
                    </div>
                </div>

                <label class="checkbox-row" for="terms">
                    <input type="checkbox" id="terms" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} required>
                    <span>I agree to the rental terms, the security deposit policy and Rideora's manual payment verification process.</span>
                </label>
                @error('terms')<span class="form-error">{{ $message }}</span>@enderror

                <button type="submit" class="btn btn-primary btn-lg btn-block">Create account</button>
            </form>

            <div class="auth-foot">
                Already registered? <a href="{{ route('login') }}">Sign in instead</a>
            </div>
        </div>

        <div class="auth-side">
            <h2>Start renting in minutes</h2>
            <p>One account lets you book any vehicle, submit manual payments and track verification status.</p>
            <ul>
                <li><span><i class="bi bi-check-lg"></i></span> Free to register, pay only when you book</li>
                <li><span><i class="bi bi-check-lg"></i></span> Transparent daily and hourly pricing</li>
                <li><span><i class="bi bi-check-lg"></i></span> Manual bKash, Nagad and bank payments</li>
                <li><span><i class="bi bi-check-lg"></i></span> Booking history and notifications</li>
            </ul>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.querySelector('[data-pw-strength]');
        var bar = document.querySelector('[data-pw-bar]');
        if (!input || !bar) { return; }

        input.addEventListener('input', function () {
            var value = input.value;
            var score = 0;
            if (value.length >= 8) { score++; }
            if (/[a-z]/.test(value) && /[A-Z]/.test(value)) { score++; }
            if (/[0-9]/.test(value)) { score++; }
            if (/[^A-Za-z0-9]/.test(value)) { score++; }

            var widths = ['0%', '30%', '55%', '80%', '100%'];
            var colors = ['var(--danger)', 'var(--danger)', 'var(--warning)', 'var(--primary)', 'var(--success)'];
            bar.style.width = widths[score];
            bar.style.background = colors[score];
        });
    });
</script>
@endpush
