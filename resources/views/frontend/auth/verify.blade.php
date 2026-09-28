<div class="login-container">
    <div class="container">
        <div class="row justify-content-center w-100">
            <div class="col-md-6 col-lg-5">
                <div class="card login-card">
                    <div class="card-header login-header text-center">
                        {{ __('Verify Your Email Address') }}
                    </div>

                    <div class="card-body login-body">

                        @if (session('resent'))
                            <div class="alert alert-success">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                        @endif

                        <p class="mb-4">
                            {{ __('Before proceeding, please check your email for a verification link.') }}
                        </p>
                        <p class="mb-3">
                            {{ __('If you did not receive the email') }},
                        </p>

                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="btn login-btn w-100">
                                {{ __('Click here to request another') }}
                            </button>
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
    text-align: center;
    font-size: 15px;
    color: #555;
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
    margin-top: 10px;
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
