@extends('frontend.layouts.app')

@section('title', 'Login — '.setting('site_name', 'Rideora'))

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
    .demo-box { margin-top: 20px; background: var(--light); border: 1px dashed var(--border); border-radius: var(--radius); padding: 14px; font-size: .8rem; color: var(--muted); }
    .demo-box strong { color: var(--dark); }
    @media (max-width: 900px) {
        .auth-layout { grid-template-columns: 1fr; gap: 22px; }
        .auth-side { display: none; }
    }

    @media (max-width: 620px) {
        .auth-card { padding: 24px 18px; }
        .auth-card h1 { font-size: 1.35rem; }
        .auth-card .btn-lg { width: 100%; }
        .auth-card .flex-between { flex-direction: column; align-items: flex-start; gap: 10px; }
        .demo-box { font-size: .76rem; }
    }
</style>
@endpush

@section('content')
    <div class="auth-layout">
        <div class="auth-card">
            <h1>Welcome back</h1>
            <p>Sign in to manage your bookings and payments.</p>

            <form method="POST" action="{{ route('login.store') }}" class="stack-16">
                @csrf

                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" required autofocus autocomplete="username">
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           required autocomplete="current-password">
                    @error('password')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="flex-between">
                    <label class="checkbox-row" for="remember">
                        <input type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                        <span>Keep me signed in</span>
                    </label>
                    <a href="{{ route('contact') }}" class="small">Need help?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">Sign in</button>
            </form>

            <div class="auth-foot">
                New to {{ setting('site_name', 'Rideora') }}?
                <a href="{{ route('register') }}">Create an account</a>
            </div>

            <div class="demo-box">
                <strong>Admin demo:</strong> admin@rideora.test / admin12345<br>
                <strong>Customer demo:</strong> rakib@example.com / password123
            </div>
        </div>

        <div class="auth-side">
            <h2>Your Ride, Your Way.</h2>
            <p>Sign in to book vehicles, upload your manual payment and follow the verification status in real time.</p>
            <ul>
                <li><span>&#10003;</span> Track booking and payment status</li>
                <li><span>&#10003;</span> Re-book your favourite vehicles</li>
                <li><span>&#10003;</span> Review completed rentals</li>
                <li><span>&#10003;</span> Notifications for every update</li>
            </ul>
        </div>
    </div>
@endsection
