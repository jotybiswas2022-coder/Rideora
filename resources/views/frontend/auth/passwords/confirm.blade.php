@extends('frontend.app')

@section('content')
<div class="login-container">
    <div class="container">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-5">
                <div class="card login-card">

                    <div class="card-header login-header text-center">
                        {{ __('Confirm Password') }}
                    </div>

                    <div class="card-body login-body">
                        <p class="text-center mb-4">
                            {{ __('Please confirm your password before continuing.') }}
                        </p>

                        <form method="POST" action="{{ route('password.confirm') }}">
                            @csrf

                            {{-- Password --}}
                            <div class="row mb-3">
                                <label for="password"
                                       class="col-md-4 col-form-label text-md-end login-label">
                                    {{ __('Password') }}
                                </label>

                                <div class="col-md-8">
                                    <input id="password" type="password"
                                           class="form-control login-input @error('password') is-invalid @enderror"
                                           name="password" required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Submit --}}
                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn login-btn w-100">
                                        {{ __('Confirm Password') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link d-block text-center mt-2" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
body {
    overflow: hidden;
}

/* Container */
.login-container {
    height: calc(100vh - 70px);
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea, #764ba2);
}

/* Card */
.login-card {
    width: 100%;
    max-width: 520px;
    min-height: 300px;
    border-radius: 20px;
    border: none;
    box-shadow: 0 18px 40px rgba(0,0,0,0.18);
    background: #fff;
}

/* Header */
.login-header {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    color: #fff;
    padding: 22px;
    font-size: 22px;
    font-weight: 600;
    border-bottom: none;
}

/* Body */
.login-body {
    padding: 32px 36px;
}

/* Labels */
.login-label {
    font-weight: 500;
    color: #555;
}

/* Inputs */
.login-input {
    padding: 10px 14px;
    font-size: 15px;
    border-radius: 10px;
    border: 1px solid #ddd;
}

.login-input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 0.2rem rgba(99,102,241,0.25);
}

/* Button */
.login-btn {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    border: none;
    color: #fff;
    padding: 10px 28px;
    font-size: 15px;
    border-radius: 30px;
    transition: 0.3s;
}

.login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99,102,241,0.4);
}

/* Mobile */
@media (max-width: 576px) {
    body {
        overflow-y: auto;
    }

    .login-card {
        max-width: 94%;
        min-height: auto;
    }
}
</style>
@endsection
