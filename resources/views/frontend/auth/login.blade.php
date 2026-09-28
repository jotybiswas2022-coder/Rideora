<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In — Connectly</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>

<div class="cl-login-page">

    {{-- Background Orbs --}}
    <div class="cl-orb cl-orb-1"></div>
    <div class="cl-orb cl-orb-2"></div>
    <div class="cl-orb cl-orb-3"></div>
    <div class="cl-orb cl-orb-4"></div>

    {{-- Grid Overlay --}}
    <div class="cl-grid-overlay"></div>

    {{-- Floating Particles --}}
    <div class="cl-particles" id="clParticles"></div>

    {{-- Decorative Message Bubbles --}}
    <div class="cl-bubble cl-bubble-1">
        <div class="cl-bubble-inner">
            <span class="cl-bubble-dot" style="--clr:#2563EB;"></span>
            <span class="cl-bubble-dot" style="--clr:#60A5FA;"></span>
            <span class="cl-bubble-dot" style="--clr:#1E40AF;"></span>
        </div>
    </div>
    <div class="cl-bubble cl-bubble-2">
        <div class="cl-bubble-avatar"></div>
        <div class="cl-bubble-lines">
            <span class="cl-bubble-line"></span>
            <span class="cl-bubble-line" style="width:60%;"></span>
        </div>
    </div>
    <div class="cl-bubble cl-bubble-3">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z" fill="#2563EB"/>
        </svg>
        <span>Secured</span>
    </div>

    {{-- Main Content --}}
    <div class="cl-login-wrapper">

        {{-- Left: Brand Section --}}
        <div class="cl-brand-section">
            <div class="cl-brand-content">
                <div class="cl-brand-badge">Welcome Back</div>

                <a href="/" class="cl-brand-logo">
                    <div class="cl-brand-icon-wrap">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <span class="cl-brand-name">Connectly</span>
                </a>

                <p class="cl-brand-tagline">Where connections come to life</p>

                {{-- Stats --}}
                <div class="cl-brand-stats">
                    <div class="cl-stat-item">
                        <span class="cl-stat-num" id="statUsers">0</span>
                        <span class="cl-stat-label">Active Users</span>
                    </div>
                    <div class="cl-stat-divider"></div>
                    <div class="cl-stat-item">
                        <span class="cl-stat-num" id="statMessages">0</span>
                        <span class="cl-stat-label">Messages Today</span>
                    </div>
                    <div class="cl-stat-divider"></div>
                    <div class="cl-stat-item">
                        <span class="cl-stat-num" id="statUptime">0</span>
                        <span class="cl-stat-label">Uptime</span>
                    </div>
                </div>

                {{-- Feature List --}}
                <div class="cl-feature-list">
                    <div class="cl-feature-item">
                        <i class="bi bi-shield-check"></i>
                        <span>End-to-end encrypted</span>
                    </div>
                    <div class="cl-feature-item">
                        <i class="bi bi-lightning-fill"></i>
                        <span>Real-time messaging</span>
                    </div>
                    <div class="cl-feature-item">
                        <i class="bi bi-globe2"></i>
                        <span>Connect globally</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Login Card --}}
        <div class="cl-login-card-wrapper">
            <div class="cl-login-card">

                {{-- Card Header --}}
                <div class="cl-card-header">
                    <div class="cl-card-icon">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <h2 class="cl-card-title">Sign in</h2>
                    <p class="cl-card-subtitle">Welcome back! Please enter your details.</p>
                </div>

                {{-- Card Body --}}
                <div class="cl-card-body">
                    <form method="POST" action="{{ route('login') }}" id="clLoginForm" autocomplete="off">
                        @csrf

                        {{-- Validation Errors --}}
                        @if ($errors->any())
                            <input type="hidden" id="clValidationErrors" value='{{ json_encode($errors->all()) }}'>
                        @endif

                        @if (session('success'))
                            <input type="hidden" id="clSessionSuccess" value="{{ session('success') }}">
                        @endif

                        {{-- Email --}}
                        <div class="cl-input-group">
                            <label class="cl-input-label" for="email">Email</label>
                            <div class="cl-input-wrap">
                                <i class="bi bi-envelope-fill cl-input-icon"></i>
                                <input id="email" type="email"
                                       class="cl-input @error('email') cl-input-error @enderror"
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="you@example.com"
                                       required autocomplete="off" autofocus>
                                <div class="cl-input-glow"></div>
                            </div>
                            @error('email')
                                <span class="cl-error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="cl-input-group">
                            <label class="cl-input-label" for="password">Password</label>
                            <div class="cl-input-wrap">
                                <i class="bi bi-lock-fill cl-input-icon"></i>
                                <input id="password" type="password"
                                       class="cl-input @error('password') cl-input-error @enderror"
                                       name="password"
                                       placeholder="••••••••"
                                       required autocomplete="current-password">
                                <button type="button" class="cl-pw-toggle" id="clPwToggle" tabindex="-1">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                                <div class="cl-input-glow"></div>
                            </div>
                            @error('password')
                                <span class="cl-error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Options --}}
                        <div class="cl-options-row">
                            <label class="cl-checkbox">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span class="cl-checkbox-mark">
                                    <i class="bi bi-check"></i>
                                </span>
                                <span class="cl-checkbox-label">Remember me</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="cl-forgot-link">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="cl-submit-btn" id="clSubmitBtn">
                            <span class="cl-btn-text">Sign In</span>
                            <span class="cl-btn-loader" id="clBtnLoader">
                                <i class="bi bi-arrow-right"></i>
                            </span>
                            <div class="cl-btn-shine"></div>
                        </button>
                    </form>

                    {{-- Social Login --}}
                    <div class="cl-divider"><span>Or continue with</span></div>

                    <div class="cl-social-row">
                        <button type="button" class="cl-social-btn cl-social-google" onclick="Swal.fire({icon:'info',title:'Coming Soon',text:'Google login coming soon!',background:'#1e293b',color:'#f1f5f9',confirmButtonColor:'#2563EB'})">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Google
                        </button>
                        <button type="button" class="cl-social-btn cl-social-github" onclick="Swal.fire({icon:'info',title:'Coming Soon',text:'GitHub login coming soon!',background:'#1e293b',color:'#f1f5f9',confirmButtonColor:'#2563EB'})">
                            <i class="bi bi-github"></i>
                            GitHub
                        </button>
                    </div>

                    {{-- Sign Up Link --}}
                    <div class="cl-signup-row">
                        <span>Don't have an account?</span>
                        <a href="{{ route('register') }}" class="cl-signup-link">
                            Create account
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                {{-- Card Footer --}}
                <div class="cl-card-footer">
                    <i class="bi bi-lock-fill"></i>
                    <span>Your data is fully encrypted and secure.</span>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===================== PARTICLES =====================
    const particlesContainer = document.getElementById('clParticles');
    if (particlesContainer) {
        for (let i = 0; i < 35; i++) {
            const particle = document.createElement('div');
            particle.className = 'cl-particle';
            const size = Math.random() * 4 + 2;
            particle.style.cssText = `
                width:${size}px;height:${size}px;
                left:${Math.random() * 100}%;
                animation-duration:${Math.random() * 15 + 10}s;
                animation-delay:${Math.random() * 5}s;
                bottom:-10px;
                opacity:${Math.random() * 0.5 + 0.2};
            `;
            particlesContainer.appendChild(particle);
        }
    }

    // ===================== PASSWORD TOGGLE =====================
    const pwToggle = document.getElementById('clPwToggle');
    const pwInput = document.getElementById('password');
    if (pwToggle && pwInput) {
        pwToggle.addEventListener('click', function() {
            const type = pwInput.getAttribute('type') === 'password' ? 'text' : 'password';
            pwInput.setAttribute('type', type);
            this.querySelector('i').className = type === 'password' ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }

    // ===================== COUNTER ANIMATION =====================
    function animateCounters() {
        const counters = [
            { el: document.getElementById('statUsers'), target: 128, suffix: 'K+' },
            { el: document.getElementById('statMessages'), target: 50, suffix: 'M+' },
            { el: document.getElementById('statUptime'), target: 99, suffix: '.9%' },
        ];

        counters.forEach(({ el, target, suffix }) => {
            if (!el) return;
            let current = 0;
            const increment = Math.max(1, Math.floor(target / 40));
            const stepTime = Math.floor(1500 / (target / increment));

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                el.textContent = current + suffix;
            }, stepTime);
        });
    }

    // Start counters with IntersectionObserver
    const brandSection = document.querySelector('.cl-brand-content');
    if (brandSection) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.disconnect();
                }
            });
        }, { threshold: 0.3 });
        observer.observe(brandSection);
    }

    // ===================== ENTRANCE ANIMATIONS =====================
    const loginCard = document.querySelector('.cl-login-card');
    if (loginCard) {
        setTimeout(() => {
            loginCard.classList.add('cl-card-visible');
        }, 100);
    }

    const brandContent = document.querySelector('.cl-brand-content');
    if (brandContent) {
        setTimeout(() => {
            brandContent.classList.add('cl-brand-visible');
        }, 300);
    }

    // ===================== INPUT FOCUS EFFECTS =====================
    document.querySelectorAll('.cl-input-wrap').forEach(wrap => {
        const input = wrap.querySelector('.cl-input');
        const icon = wrap.querySelector('.cl-input-icon');
        if (input && icon) {
            input.addEventListener('focus', () => {
                wrap.classList.add('cl-input-focused');
                icon.style.color = '#2563EB';
                icon.style.transform = 'translateY(-50%) scale(1.15)';
            });
            input.addEventListener('blur', () => {
                wrap.classList.remove('cl-input-focused');
                icon.style.color = '#64748b';
                icon.style.transform = 'translateY(-50%) scale(1)';
            });
        }
    });

    // ===================== SUBMIT BUTTON LOADER =====================
    const loginForm = document.getElementById('clLoginForm');
    const submitBtn = document.getElementById('clSubmitBtn');
    const btnLoader = document.getElementById('clBtnLoader');
    const btnText = submitBtn?.querySelector('.cl-btn-text');

    if (loginForm && submitBtn) {
        loginForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.style.pointerEvents = 'none';
            btnText.textContent = 'Signing in...';
            btnLoader.innerHTML = '<div class="cl-spinner"></div>';

            // Safety net: re-enable button after 10s if page doesn't navigate
            setTimeout(function() {
                if (submitBtn.disabled) {
                    submitBtn.disabled = false;
                    submitBtn.style.pointerEvents = '';
                    btnText.textContent = 'Sign In';
                    btnLoader.innerHTML = '<i class="bi bi-arrow-right"></i>';
                }
            }, 10000);
        });
    }

    // ===================== SWEETALERT2 TOASTS =====================
    // Validation errors — parse JSON array from server
    const validationErrors = document.getElementById('clValidationErrors');
    if (validationErrors) {
        try {
            const errors = JSON.parse(validationErrors.value);
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: Array.isArray(errors) ? errors.join('\n') : validationErrors.value,
                background: '#1e293b',
                color: '#f1f5f9',
                confirmButtonColor: '#2563EB',
                confirmButtonText: 'Try Again',
                iconColor: '#ef4444',
            });
        } catch(e) {
            // Fallback for malformed JSON
        }
    }

    // Session success
    const sessionSuccess = document.getElementById('clSessionSuccess');
    if (sessionSuccess) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: sessionSuccess.value,
            background: '#1e293b',
            color: '#f1f5f9',
            confirmButtonColor: '#2563EB',
            timer: 3000,
            timerProgressBar: true,
            iconColor: '#22c55e',
        });
    }

    // ===================== KEYBOARD: ENTER TO SUBMIT =====================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const active = document.activeElement;
            if (active && (active.id === 'email' || active.id === 'password')) {
                e.preventDefault();
                loginForm?.requestSubmit();
            }
        }
    });

    // ===================== MOUSE GLOW ON CARD =====================
    const card = document.querySelector('.cl-login-card');
    if (card) {
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            this.style.setProperty('--mouse-x', x + '%');
            this.style.setProperty('--mouse-y', y + '%');
        });
    }
});
</script>

<style>
/* ===================== RESET & BASE ===================== */
* { margin:0; padding:0; box-sizing:border-box; }

.cl-login-page {
    --clr-primary: #2563EB;
    --clr-light: #60A5FA;
    --clr-dark: #1E40AF;
    --clr-bg: #0b1121;
    --clr-card: rgba(255,255,255,0.04);
    --clr-border: rgba(255,255,255,0.06);
    --clr-text: #f1f5f9;
    --clr-text-secondary: #94a3b8;
    --clr-input-bg: rgba(255,255,255,0.05);
    --clr-input-border: rgba(255,255,255,0.08);
    --clr-input-focus: rgba(37,99,235,0.25);
    --font: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;

    font-family: var(--font);
    background: var(--clr-bg);
    color: var(--clr-text);
    min-height: 100vh;
    min-height: 100dvh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

/* ===================== BACKGROUND ORBS ===================== */
.cl-orb {
    position: fixed;
    border-radius: 50%;
    filter: blur(80px);
    pointer-events: none;
    z-index: 0;
}
.cl-orb-1 {
    width: 600px; height: 600px;
    background: radial-gradient(circle, rgba(37,99,235,0.12), transparent 70%);
    top: -200px; left: -150px;
    animation: cl-orb-float-1 12s ease-in-out infinite;
}
.cl-orb-2 {
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(96,165,250,0.08), transparent 70%);
    bottom: -150px; right: -100px;
    animation: cl-orb-float-2 15s ease-in-out infinite;
}
.cl-orb-3 {
    width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(30,64,175,0.1), transparent 70%);
    top: 50%; right: 40%;
    animation: cl-orb-float-3 18s ease-in-out infinite;
}
.cl-orb-4 {
    width: 350px; height: 350px;
    background: radial-gradient(circle, rgba(37,99,235,0.06), transparent 70%);
    bottom: 20%; left: 30%;
    animation: cl-orb-float-1 20s ease-in-out infinite reverse;
}

@keyframes cl-orb-float-1 {
    0%,100% { transform: translate(0,0) scale(1); }
    50% { transform: translate(80px,60px) scale(1.15); }
}
@keyframes cl-orb-float-2 {
    0%,100% { transform: translate(0,0) scale(1); }
    50% { transform: translate(-60px,-80px) scale(1.1); }
}
@keyframes cl-orb-float-3 {
    0%,100% { transform: translate(0,0) scale(1); }
    50% { transform: translate(40px,-50px) scale(1.2); }
}

/* ===================== GRID OVERLAY ===================== */
.cl-grid-overlay {
    position: fixed;
    inset: 0;
    background-image:
        linear-gradient(rgba(37,99,235,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(37,99,235,0.03) 1px, transparent 1px);
    background-size: 60px 60px;
    pointer-events: none;
    z-index: 1;
    mask-image: radial-gradient(ellipse at 50% 50%, black 30%, transparent 70%);
    -webkit-mask-image: radial-gradient(ellipse at 50% 50%, black 30%, transparent 70%);
}

/* ===================== PARTICLES ===================== */
.cl-particles {
    position: fixed;
    inset: 0;
    overflow: hidden;
    pointer-events: none;
    z-index: 1;
}
.cl-particle {
    position: absolute;
    background: linear-gradient(135deg, var(--clr-primary), var(--clr-light));
    border-radius: 50%;
    animation: cl-particle-rise linear infinite;
}
@keyframes cl-particle-rise {
    0% { transform: translateY(0) rotate(0deg); opacity: 0; }
    10% { opacity: 0.5; }
    90% { opacity: 0.2; }
    100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; }
}

/* ===================== DECORATIVE BUBBLES ===================== */
.cl-bubble {
    position: fixed;
    background: rgba(255,255,255,0.03);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 16px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 2;
    pointer-events: none;
    animation: cl-bubble-float 6s ease-in-out infinite;
}
.cl-bubble-1 {
    top: 15%; left: 8%;
    animation-delay: 0s;
}
.cl-bubble-2 {
    bottom: 22%; left: 10%;
    animation-delay: 2s;
    animation-duration: 7s;
}
.cl-bubble-3 {
    top: 40%; right: 6%;
    animation-delay: 4s;
    animation-duration: 8s;
}
@keyframes cl-bubble-float {
    0%,100% { transform: translateY(0); }
    50% { transform: translateY(-12px); }
}
.cl-bubble-inner { display:flex; gap:6px; }
.cl-bubble-dot {
    width:8px;height:8px;border-radius:50%;
    background:var(--clr);
    animation: cl-bubble-dot-pulse 2s ease-in-out infinite;
}
.cl-bubble-dot:nth-child(2) { animation-delay:0.3s; }
.cl-bubble-dot:nth-child(3) { animation-delay:0.6s; }
@keyframes cl-bubble-dot-pulse {
    0%,100% { transform:scale(1); opacity:0.6; }
    50% { transform:scale(1.3); opacity:1; }
}
.cl-bubble-avatar {
    width:28px;height:28px;border-radius:50%;
    background:linear-gradient(135deg,var(--clr-primary),var(--clr-light));
    flex-shrink:0;
}
.cl-bubble-lines { display:flex; flex-direction:column; gap:5px; }
.cl-bubble-line {
    width:80px;height:5px;border-radius:3px;
    background:rgba(255,255,255,0.1);
}
.cl-bubble-3 {
    gap:8px; font-size:13px; color:var(--clr-text-secondary); font-weight:500;
}
.cl-bubble-3 svg { flex-shrink:0; }

/* ===================== MAIN LAYOUT ===================== */
.cl-login-wrapper {
    position: relative;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 60px;
    width: 100%;
    max-width: 1200px;
    padding: 40px 24px;
    min-height: 100vh;
    min-height: 100dvh;
}

/* ===================== BRAND SECTION (LEFT) ===================== */
.cl-brand-section {
    flex: 1;
    max-width: 440px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.cl-brand-content {
    opacity: 0;
    transform: translateX(-30px);
    transition: all 0.8s cubic-bezier(.16,1,.3,1);
}
.cl-brand-content.cl-brand-visible {
    opacity: 1;
    transform: translateX(0);
}
.cl-brand-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: rgba(37,99,235,0.1);
    border: 1px solid rgba(37,99,235,0.15);
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--clr-light);
    margin-bottom: 24px;
    letter-spacing: 0.3px;
}
.cl-brand-badge::before {
    content: '';
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #22c55e;
    animation: cl-pulse-dot 2s ease-in-out infinite;
}
@keyframes cl-pulse-dot {
    0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.6); }
    50% { box-shadow: 0 0 0 6px rgba(34,197,94,0); }
}
.cl-brand-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    margin-bottom: 16px;
}
.cl-brand-icon-wrap {
    width: 48px; height: 48px;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, var(--clr-primary), var(--clr-dark));
    border-radius: 14px;
    font-size: 1.5rem;
    color: #fff;
    box-shadow: 0 8px 24px rgba(37,99,235,0.3);
}
.cl-brand-name {
    font-size: 2rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--clr-light), var(--clr-primary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: -0.5px;
}
.cl-brand-tagline {
    font-size: 1.1rem;
    color: var(--clr-text-secondary);
    margin-bottom: 36px;
    line-height: 1.6;
}

/* Stats */
.cl-brand-stats {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 36px;
    padding: 20px 24px;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 14px;
}
.cl-stat-item { text-align: center; flex:1; }
.cl-stat-num {
    display: block;
    font-size: 1.6rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--clr-light), var(--clr-primary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1.2;
}
.cl-stat-label {
    font-size: 0.75rem;
    color: var(--clr-text-secondary);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 4px;
}
.cl-stat-divider {
    width: 1px; height: 40px;
    background: rgba(255,255,255,0.08);
}

/* Feature List */
.cl-feature-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.cl-feature-item {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 0.9rem;
    color: var(--clr-text-secondary);
    transition: all 0.3s ease;
    cursor: default;
}
.cl-feature-item:hover {
    color: var(--clr-text);
    transform: translateX(4px);
}
.cl-feature-item i {
    font-size: 1.1rem;
    color: var(--clr-primary);
    width: 24px;
    text-align: center;
}

/* ===================== LOGIN CARD (RIGHT) ===================== */
.cl-login-card-wrapper {
    flex: 1;
    max-width: 420px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.cl-login-card {
    --mouse-x: 50%;
    --mouse-y: 50%;
    width: 100%;
    background: var(--clr-card);
    backdrop-filter: blur(24px) saturate(180%);
    -webkit-backdrop-filter: blur(24px) saturate(180%);
    border: 1px solid var(--clr-border);
    border-radius: 24px;
    padding: 40px 36px;
    position: relative;
    overflow: hidden;
    transition: all 0.6s cubic-bezier(.16,1,.3,1);
    opacity: 0;
    transform: translateY(30px) scale(0.97);
}
.cl-login-card.cl-card-visible {
    opacity: 1;
    transform: translateY(0) scale(1);
}
.cl-login-card::before {
    content: '';
    position: absolute;
    top: var(--mouse-y);
    left: var(--mouse-x);
    transform: translate(-50%,-50%);
    width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(37,99,235,0.06), transparent 70%);
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
    transition: all 0.3s ease;
}
.cl-login-card:hover {
    border-color: rgba(37,99,235,0.12);
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    transform: translateY(-2px);
}

/* Card Header */
.cl-card-header {
    text-align: center;
    margin-bottom: 32px;
    position: relative;
    z-index: 1;
}
.cl-card-icon {
    width: 56px; height: 56px;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, rgba(37,99,235,0.15), rgba(37,99,235,0.05));
    border: 1px solid rgba(37,99,235,0.15);
    border-radius: 16px;
    margin: 0 auto 16px;
    font-size: 1.5rem;
    color: var(--clr-light);
    animation: cl-card-icon-pulse 3s ease-in-out infinite;
}
@keyframes cl-card-icon-pulse {
    0%,100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}
.cl-card-title {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--clr-text);
    margin-bottom: 8px;
    letter-spacing: -0.3px;
}
.cl-card-subtitle {
    font-size: 0.9rem;
    color: var(--clr-text-secondary);
    line-height: 1.5;
}

/* Card Body */
.cl-card-body {
    position: relative;
    z-index: 1;
}

/* ===================== INPUTS ===================== */
.cl-input-group {
    margin-bottom: 20px;
}
.cl-input-label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--clr-text);
    margin-bottom: 8px;
    letter-spacing: 0.2px;
}
.cl-input-wrap {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
}
.cl-input-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    z-index: 2;
    font-size: 1rem;
    transition: all 0.3s cubic-bezier(.16,1,.3,1);
}
.cl-input {
    width: 100%;
    padding: 14px 44px 14px 44px;
    background: var(--clr-input-bg);
    border: 1.5px solid var(--clr-input-border);
    border-radius: 12px;
    font-family: var(--font);
    font-size: 0.95rem;
    color: var(--clr-text);
    outline: none;
    transition: all 0.3s cubic-bezier(.16,1,.3,1);
    position: relative;
    z-index: 1;
}
.cl-input::placeholder {
    color: #475569;
}
.cl-input:focus {
    border-color: var(--clr-primary);
    background: rgba(37,99,235,0.05);
    box-shadow: 0 0 0 4px var(--clr-input-focus);
}
.cl-input-error {
    border-color: #ef4444 !important;
}
.cl-input-glow {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    border-radius: 12px;
    background: radial-gradient(circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(37,99,235,0.08), transparent 60%);
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 0;
}
.cl-input-focused .cl-input-glow {
    opacity: 1;
}
.cl-error-text {
    display: block;
    color: #ef4444;
    font-size: 0.8rem;
    margin-top: 6px;
    font-weight: 500;
}

/* Password Toggle */
.cl-pw-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #64748b;
    font-size: 1.1rem;
    cursor: pointer;
    padding: 4px;
    z-index: 2;
    transition: all 0.3s ease;
    border-radius: 6px;
}
.cl-pw-toggle:hover {
    color: var(--clr-text);
    background: rgba(255,255,255,0.05);
}

/* ===================== OPTIONS ROW ===================== */
.cl-options-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}
.cl-checkbox {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    user-select: none;
}
.cl-checkbox input {
    display: none;
}
.cl-checkbox-mark {
    width: 18px; height: 18px;
    border: 2px solid rgba(255,255,255,0.15);
    border-radius: 5px;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.3s ease;
    flex-shrink: 0;
}
.cl-checkbox-mark i {
    font-size: 11px;
    color: #fff;
    opacity: 0;
    transform: scale(0);
    transition: all 0.2s ease;
}
.cl-checkbox input:checked + .cl-checkbox-mark {
    background: var(--clr-primary);
    border-color: var(--clr-primary);
}
.cl-checkbox input:checked + .cl-checkbox-mark i {
    opacity: 1;
    transform: scale(1);
}
.cl-checkbox-label {
    font-size: 0.85rem;
    color: var(--clr-text-secondary);
    font-weight: 500;
}
.cl-forgot-link {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--clr-primary);
    text-decoration: none;
    transition: all 0.3s ease;
}
.cl-forgot-link:hover {
    color: var(--clr-light);
    text-decoration: underline;
}

/* ===================== SUBMIT BUTTON ===================== */
.cl-submit-btn {
    width: 100%;
    padding: 15px 24px;
    background: linear-gradient(135deg, var(--clr-primary), var(--clr-dark));
    border: none;
    border-radius: 12px;
    font-family: var(--font);
    font-size: 1rem;
    font-weight: 700;
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(.16,1,.3,1);
    box-shadow: 0 4px 16px rgba(37,99,235,0.3);
}
.cl-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(37,99,235,0.4);
}
.cl-submit-btn:active {
    transform: translateY(0);
}
.cl-submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}
.cl-btn-shine {
    position: absolute;
    top: 0; left: -100%;
    width: 60%; height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
    transform: skewX(-20deg);
    transition: left 0.8s ease;
}
.cl-submit-btn:hover .cl-btn-shine {
    left: 150%;
}
.cl-btn-loader {
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
}
.cl-submit-btn:hover .cl-btn-loader i {
    animation: cl-btn-arrow 1s ease infinite;
}
@keyframes cl-btn-arrow {
    0%,100% { transform: translateX(0); }
    50% { transform: translateX(4px); }
}
.cl-spinner {
    width: 18px; height: 18px;
    border: 2.5px solid rgba(255,255,255,0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: cl-spin 0.7s linear infinite;
}
@keyframes cl-spin {
    to { transform: rotate(360deg); }
}

/* ===================== DIVIDER ===================== */
.cl-divider {
    display: flex;
    align-items: center;
    margin: 24px 0;
}
.cl-divider::before,
.cl-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(255,255,255,0.06);
}
.cl-divider span {
    padding: 0 16px;
    font-size: 0.8rem;
    color: var(--clr-text-secondary);
    font-weight: 500;
}

/* ===================== SOCIAL BUTTONS ===================== */
.cl-social-row {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
}
.cl-social-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 16px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    font-family: var(--font);
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--clr-text-secondary);
    cursor: pointer;
    transition: all 0.3s ease;
}
.cl-social-btn:hover {
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.12);
    color: var(--clr-text);
    transform: translateY(-1px);
}
.cl-social-btn i,
.cl-social-btn svg {
    font-size: 1.2rem;
}

/* ===================== SIGN UP ROW ===================== */
.cl-signup-row {
    text-align: center;
    font-size: 0.9rem;
    color: var(--clr-text-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
}
.cl-signup-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-weight: 700;
    color: var(--clr-primary);
    text-decoration: none;
    transition: all 0.3s ease;
}
.cl-signup-link:hover {
    color: var(--clr-light);
    gap: 8px;
}
.cl-signup-link i {
    font-size: 0.85rem;
    transition: transform 0.3s ease;
}
.cl-signup-link:hover i {
    transform: translateX(3px);
}

/* ===================== CARD FOOTER ===================== */
.cl-card-footer {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,0.05);
    position: relative;
    z-index: 1;
}
.cl-card-footer i {
    font-size: 0.85rem;
    color: var(--clr-primary);
}
.cl-card-footer span {
    font-size: 0.78rem;
    color: var(--clr-text-secondary);
}

/* ===================== RESPONSIVE ===================== */
@media (max-width: 1024px) {
    .cl-login-wrapper {
        gap: 40px;
    }
    .cl-brand-section { max-width: 360px; }
    .cl-login-card-wrapper { max-width: 380px; }
    .cl-bubble { display: none; }
}
@media (max-width: 860px) {
    .cl-login-wrapper {
        flex-direction: column;
        gap: 32px;
        padding: 24px 16px;
        min-height: auto;
    }
    .cl-brand-section {
        max-width: 100%;
        width: 100%;
    }
    .cl-brand-content {
        text-align: center;
    }
    .cl-brand-logo { justify-content: center; }
    .cl-feature-list {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
    }
    .cl-brand-stats { padding: 16px; }
    .cl-stat-num { font-size: 1.3rem; }
    .cl-login-card-wrapper {
        max-width: 100%;
        width: 100%;
    }
    .cl-login-card { padding: 32px 24px; }
    .cl-card-title { font-size: 1.4rem; }
    .cl-brand-tagline { margin-bottom: 24px; }
    .cl-brand-stats { margin-bottom: 24px; }
}
@media (max-width: 480px) {
    .cl-login-card { padding: 24px 18px; border-radius: 20px; }
    .cl-social-row { flex-direction: column; }
    .cl-options-row { flex-direction: column; gap: 12px; align-items: flex-start; }
    .cl-brand-name { font-size: 1.6rem; }
    .cl-brand-icon-wrap { width: 40px; height: 40px; font-size: 1.2rem; border-radius: 12px; }
    .cl-card-icon { width: 48px; height: 48px; font-size: 1.2rem; border-radius: 14px; }
}

/* ===================== SCROLLBAR ===================== */
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: var(--clr-bg); }
::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.12); }
</style>
</body>
</html>
