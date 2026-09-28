<div class="login-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-7 col-lg-5">
                <div class="card login-card">

                    {{-- Header --}}
                    <div class="card-header login-header text-center">
                        {{ __('Reset Password') }}
                    </div>

                    {{-- Body --}}
                    <div class="card-body login-body">

                        {{-- Success Alert --}}
                        @if (session('status'))
                            <div class="alert alert-success text-center">
                                {{ session('status') }}
                            </div>
                        @endif

                        {{-- Lock Icon --}}
                        <div class="lock-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 11c1.657 0 3 1.343 3 3v2H9v-2c0-1.657 1.343-3 3-3zm6-2V7a6 6 0 10-12 0v2"/>
                            </svg>
                        </div>

                        {{-- Instructions --}}
                        <p class="text-center instructions">
                            {{ __('Enter your email address and we will send you a password reset link.') }}
                        </p>

                        {{-- Form --}}
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            {{-- Email --}}
                            <div class="mb-4">
                                <label for="email" class="form-label login-label">
                                    {{ __('Email') }}
                                </label><br><br>

                                <input id="email" type="email"
                                       class="form-control login-input @error('email') is-invalid @enderror"
                                       name="email"
                                       placeholder="Enter your email"
                                       value="{{ old('email') }}"
                                       required autofocus>

                                @error('email')
                                    <div class="invalid-feedback d-block">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div><br><br>

                            {{-- Submit Button --}}
                            <button type="submit" class="btn login-btn w-100">
                                {{ __('Send Password Reset Link') }}
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Body */
body {
    font-family: 'Inter', sans-serif;
    background: #0f172a;
    margin: 0;
    overflow-x: hidden;
    overflow-y: hidden;
}

/* Container */
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 12px;
    background: linear-gradient(135deg, #0f172a, #1e293b);
    position: relative;
}

/* Floating bubbles */
.login-container::before,
.login-container::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    z-index: 0;
}

.login-container::before {
    top: -150px;
    left: -100px;
    width: 400px;
    height: 400px;
    background: rgba(59,130,246,0.08);
    animation: floatBubble1 8s ease-in-out infinite;
}

.login-container::after {
    bottom: -120px;
    right: -80px;
    width: 500px;
    height: 500px;
    background: rgba(99,102,241,0.06);
    animation: floatBubble2 10s ease-in-out infinite;
}

@keyframes floatBubble1 {
    0%,100% { transform: translate(0,0) scale(1); }
    50% { transform: translate(30px, 20px) scale(1.05);}
}

@keyframes floatBubble2 {
    0%,100% { transform: translate(0,0) scale(1); }
    50% { transform: translate(-40px, -30px) scale(1.08);}
}

/* Card */
.login-card {
    width: 100%;
    border-radius: 20px;
    background: rgba(30,41,59,0.85);
    backdrop-filter: blur(15px);
    border: 1px solid rgba(59,130,246,0.25);
    box-shadow: 0 20px 50px rgba(0,0,0,0.45);
    overflow: hidden;
    position: relative;
    z-index: 1;
}

/* Header */
.login-header {
    background: linear-gradient(135deg, rgba(15,23,42,0.95), rgba(26,39,68,0.9));
    color: #fff;
    padding: 22px;
    font-size: 22px;
    font-weight: 700;
    border-bottom: 1px solid rgba(59,130,246,0.25);
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
}

/* Body */
.login-body {
    padding: 32px 36px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Instructions */
.instructions {
    font-size: 14.5px;
    color: #94a3b8;
    line-height: 1.6;
}

/* Alert */
.alert-success {
    background: rgba(59,130,246,0.12);
    border: 1px solid rgba(59,130,246,0.25);
    color: #60a5fa;
    border-radius: 12px;
    font-size: 14px;
    text-align: center;
    padding: 10px 12px;
}

/* Lock Icon */
.lock-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(59,130,246,0.12);
    border: 2px solid rgba(59,130,246,0.2);
    transition: transform .3s;
}

.lock-icon:hover {
    transform: scale(1.05);
}

.lock-icon svg {
    width: 28px;
    height: 28px;
    color: #3b82f6;
}

/* Label */
.login-label {
    color: #94a3b8;
    font-weight: 500;
}

/* Input */
.login-input {
    background: rgba(15,23,42,0.85);
    color: #e2e8f0;
    border: 1px solid rgba(59,130,246,0.25);
    border-radius: 12px;
    padding: 12px 16px;
    width: 100%;
    transition: all .3s ease;
}

.login-input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
    background: rgba(12,19,34,0.95);
    outline: none;
}

/* Invalid Feedback */
.invalid-feedback strong {
    color: #f87171;
    font-size: 13px;
}

/* Button */
.login-btn {
    background: linear-gradient(135deg,#3b82f6,#2563eb);
    border: none;
    padding: 12px;
    font-weight: 600;
    border-radius: 12px;
    color: #fff;
    transition: all .3s ease;
}

.login-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 25px rgba(59,130,246,.35), 0 0 40px rgba(59,130,246,0.1);
}

/* Mobile */
@media(max-width:768px) {
    .login-body {padding: 25px 20px;}
    .login-label {text-align: left;}
}

@media(max-width:576px) {
    .login-header {font-size: 19px; padding: 18px;}
    .login-body {padding: 20px 15px;}
}
</style>