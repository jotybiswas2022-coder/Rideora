<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account — Connectly</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>

<div class="cr-page">

    {{-- Background Orbs --}}
    <div class="cr-orb cr-orb-1"></div>
    <div class="cr-orb cr-orb-2"></div>
    <div class="cr-orb cr-orb-3"></div>
    <div class="cr-orb cr-orb-4"></div>

    {{-- Grid Overlay --}}
    <div class="cr-grid"></div>

    {{-- Floating Particles --}}
    <div class="cr-particles" id="crParticles"></div>

    {{-- Decorative Bubbles --}}
    <div class="cr-bubble cr-bubble-1">
        <div class="cr-bubble-inner">
            <span class="cr-b-dot" style="--clr:#2563EB;"></span>
            <span class="cr-b-dot" style="--clr:#60A5FA;"></span>
            <span class="cr-b-dot" style="--clr:#1E40AF;"></span>
        </div>
    </div>
    <div class="cr-bubble cr-bubble-2">
        <div class="cr-b-avatar"></div>
        <div class="cr-b-lines">
            <span class="cr-b-line"></span>
            <span class="cr-b-line" style="width:55%;"></span>
        </div>
    </div>
    <div class="cr-bubble cr-bubble-3">
        <i class="bi bi-lightning-fill" style="color:#2563EB;font-size:16px;"></i>
        <span>Fast & secure</span>
    </div>

    {{-- Main Layout --}}
    <div class="cr-wrapper">

        {{-- Left: Brand Section --}}
        <div class="cr-brand">
            <div class="cr-brand-inner">
                <div class="cr-badge">Join Connectly</div>

                <a href="/" class="cr-logo">
                    <div class="cr-logo-icon"><i class="bi bi-diagram-3-fill"></i></div>
                    <span class="cr-logo-text">Connectly</span>
                </a>

                <p class="cr-tagline">Start connecting with people who matter.</p>

                {{-- Benefits --}}
                <div class="cr-benefits">
                    <div class="cr-benefit">
                        <div class="cr-benefit-icon"><i class="bi bi-chat-dots-fill"></i></div>
                        <div class="cr-benefit-info">
                            <span class="cr-benefit-title">Real-time Chat</span>
                            <span class="cr-benefit-desc">Instant messaging with friends</span>
                        </div>
                    </div>
                    <div class="cr-benefit">
                        <div class="cr-benefit-icon"><i class="bi bi-shield-check"></i></div>
                        <div class="cr-benefit-info">
                            <span class="cr-benefit-title">Private & Secure</span>
                            <span class="cr-benefit-desc">End-to-end encrypted always</span>
                        </div>
                    </div>
                    <div class="cr-benefit">
                        <div class="cr-benefit-icon"><i class="bi bi-people-fill"></i></div>
                        <div class="cr-benefit-info">
                            <span class="cr-benefit-title">Community</span>
                            <span class="cr-benefit-desc">Share posts, react & comment</span>
                        </div>
                    </div>
                </div>

                {{-- Testimonial --}}
                <div class="cr-testimonial">
                    <div class="cr-test-avatars">
                        <div class="cr-test-av" style="background:#2563EB;">A</div>
                        <div class="cr-test-av" style="background:#60A5FA;margin-left:-12px;">S</div>
                        <div class="cr-test-av" style="background:#1E40AF;margin-left:-12px;">R</div>
                    </div>
                    <div class="cr-test-text">
                        <span>Join <strong>10,000+</strong> active users already connecting!</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Register Card --}}
        <div class="cr-card-wrap">
            <div class="cr-card">

                {{-- Card Header --}}
                <div class="cr-card-hd">
                    <div class="cr-card-ico">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <h2 class="cr-card-title">Create Account</h2>
                    <p class="cr-card-sub">Fill in your details to get started.</p>
                </div>

                {{-- Card Body --}}
                <div class="cr-card-body">
                    <form method="POST" action="{{ route('register') }}" id="crForm" autocomplete="off" novalidate>
                        @csrf

                        {{-- Validation Errors --}}
                        @if ($errors->any())
                            <input type="hidden" id="crErrors" value='{{ json_encode($errors->all()) }}'>
                        @endif

                        @if (session('success'))
                            <input type="hidden" id="crSuccess" value="{{ session('success') }}">
                        @endif

                        {{-- Name --}}
                        <div class="cr-fg">
                            <label class="cr-label" for="crName">Full Name</label>
                            <div class="cr-iw">
                                <i class="bi bi-person-fill cr-ico"></i>
                                <input id="crName" type="text"
                                       class="cr-input @error('name') cr-err @enderror"
                                       name="name" value="{{ old('name') }}"
                                       placeholder="John Doe"
                                       required autocomplete="off" autofocus>
                                <div class="cr-glow"></div>
                            </div>
                            @error('name')<span class="cr-err-txt">{{ $message }}</span>@enderror
                        </div>

                        {{-- Email --}}
                        <div class="cr-fg">
                            <label class="cr-label" for="crEmail">Email</label>
                            <div class="cr-iw">
                                <i class="bi bi-envelope-fill cr-ico"></i>
                                <input id="crEmail" type="email"
                                       class="cr-input @error('email') cr-err @enderror"
                                       name="email" value="{{ old('email') }}"
                                       placeholder="you@example.com"
                                       required autocomplete="off">
                                <div class="cr-glow"></div>
                            </div>
                            @error('email')<span class="cr-err-txt">{{ $message }}</span>@enderror
                        </div>

                        {{-- Password --}}
                        <div class="cr-fg">
                            <label class="cr-label" for="crPass">Password</label>
                            <div class="cr-iw">
                                <i class="bi bi-lock-fill cr-ico"></i>
                                <input id="crPass" type="password"
                                       class="cr-input @error('password') cr-err @enderror"
                                       name="password"
                                       placeholder="Create a strong password"
                                       required autocomplete="off">
                                <button type="button" class="cr-pw-tog" id="crPwTog1" tabindex="-1">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                                <div class="cr-glow"></div>
                            </div>
                            @error('password')<span class="cr-err-txt">{{ $message }}</span>@enderror

                            {{-- Password Strength --}}
                            <div class="cr-ps">
                                <div class="cr-ps-dots" id="crPsDots">
                                    <span class="cr-ps-d"></span>
                                    <span class="cr-ps-d"></span>
                                    <span class="cr-ps-d"></span>
                                    <span class="cr-ps-d"></span>
                                    <span class="cr-ps-d"></span>
                                </div>
                                <span class="cr-ps-txt" id="crPsTxt">
                                    <i class="bi bi-info-circle"></i> Use 8+ chars, uppercase, number & symbol
                                </span>
                            </div>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="cr-fg">
                            <label class="cr-label" for="crPassConfirm">Confirm Password</label>
                            <div class="cr-iw">
                                <i class="bi bi-shield-check cr-ico"></i>
                                <input id="crPassConfirm" type="password"
                                       class="cr-input"
                                       name="password_confirmation"
                                       placeholder="Repeat your password"
                                       required autocomplete="off">
                                <button type="button" class="cr-pw-tog" id="crPwTog2" tabindex="-1">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                                <div class="cr-glow"></div>
                            </div>

                            {{-- Match Indicator --}}
                            <div class="cr-match" id="crMatch">
                                <div class="cr-match-ico"><i class="bi bi-check-circle-fill"></i></div>
                                <span class="cr-match-txt">Passwords match</span>
                            </div>
                        </div>

                        {{-- Terms --}}
                        <div class="cr-terms">
                            <label class="cr-cb">
                                <input type="checkbox" name="terms" id="crTerms" required>
                                <span class="cr-cb-mark"><i class="bi bi-check"></i></span>
                                <span class="cr-cb-label">
                                    I agree to the <a href="#" class="cr-link-inline">Terms of Service</a> & <a href="#" class="cr-link-inline">Privacy Policy</a>
                                </span>
                            </label>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="cr-btn" id="crBtn">
                            <span class="cr-btn-txt">Create Account</span>
                            <span class="cr-btn-ldr" id="crBtnLdr"><i class="bi bi-arrow-right"></i></span>
                            <div class="cr-btn-shine"></div>
                        </button>
                    </form>

                    {{-- Divider --}}
                    <div class="cr-div"><span>Or sign up with</span></div>

                    {{-- Social --}}
                    <div class="cr-social">
                        <button type="button" class="cr-soc cr-soc-g" onclick="Swal.fire({icon:'info',title:'Coming Soon',text:'Google signup coming soon!',background:'#1e293b',color:'#f1f5f9',confirmButtonColor:'#2563EB'})">
                            <svg viewBox="0 0 24 24" width="18" height="18">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Google
                        </button>
                        <button type="button" class="cr-soc cr-soc-gh" onclick="Swal.fire({icon:'info',title:'Coming Soon',text:'GitHub signup coming soon!',background:'#1e293b',color:'#f1f5f9',confirmButtonColor:'#2563EB'})">
                            <i class="bi bi-github"></i>
                            GitHub
                        </button>
                    </div>

                    {{-- Login Link --}}
                    <div class="cr-login-row">
                        <span>Already have an account?</span>
                        <a href="{{ route('login') }}" class="cr-login-link">
                            Sign in <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                {{-- Card Footer --}}
                <div class="cr-card-ft">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span>Your information is protected with 256-bit encryption.</span>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ===================== PARTICLES =====================
    const pc = document.getElementById('crParticles');
    if (pc) {
        for (let i = 0; i < 30; i++) {
            const p = document.createElement('div');
            p.className = 'cr-p';
            const s = Math.random() * 4 + 2;
            p.style.cssText = `
                width:${s}px;height:${s}px;
                left:${Math.random() * 100}%;
                animation-duration:${Math.random() * 15 + 12}s;
                animation-delay:${Math.random() * 6}s;
                bottom:-10px;
                opacity:${Math.random() * 0.4 + 0.15};
            `;
            pc.appendChild(p);
        }
    }

    // ===================== PASSWORD TOGGLE =====================
    function setupPwTog(btnId, inputId) {
        const btn = document.getElementById(btnId);
        const inp = document.getElementById(inputId);
        if (btn && inp) {
            btn.addEventListener('click', function() {
                const t = inp.getAttribute('type') === 'password' ? 'text' : 'password';
                inp.setAttribute('type', t);
                this.querySelector('i').className = t === 'password' ? 'bi bi-eye-slash' : 'bi bi-eye';
            });
        }
    }
    setupPwTog('crPwTog1', 'crPass');
    setupPwTog('crPwTog2', 'crPassConfirm');

    // ===================== PASSWORD STRENGTH =====================
    const passInput = document.getElementById('crPass');
    const confirmInput = document.getElementById('crPassConfirm');
    const psDots = document.getElementById('crPsDots');
    const psTxt = document.getElementById('crPsTxt');
    const matchEl = document.getElementById('crMatch');

    function calcStrength(pw) {
        let score = 0;
        if (pw.length >= 8) score++;
        if (pw.length >= 12) score++;
        if (/[A-Z]/.test(pw)) score++;
        if (/[0-9]/.test(pw)) score++;
        if (/[^A-Za-z0-9]/.test(pw)) score++;

        let cls = 'cr-weak', txt = 'Weak — try adding more characters';
        if (score >= 4) { cls = 'cr-strong'; txt = 'Strong password!'; }
        else if (score >= 2) { cls = 'cr-medium'; txt = 'Getting better, add a symbol or number'; }

        return { score, cls, txt };
    }

    function updateStrength() {
        const pw = passInput.value;
        const dots = psDots.querySelectorAll('.cr-ps-d');

        // Empty field — reset to default hint
        if (pw.length === 0) {
            dots.forEach(d => d.className = 'cr-ps-d');
            psTxt.innerHTML = '<i class="bi bi-info-circle"></i> Use 8+ chars, uppercase, number & symbol';
            psTxt.className = 'cr-ps-txt';
            checkMatch();
            return;
        }

        const { score, cls, txt } = calcStrength(pw);

        dots.forEach((d, i) => {
            d.className = 'cr-ps-d';
            if (i < score) d.classList.add(cls);
        });

        psTxt.innerHTML = `<i class="bi ${score >= 4 ? 'bi-check-circle-fill' : 'bi-info-circle'}"></i> ${txt}`;
        psTxt.className = 'cr-ps-txt ' + cls;
        checkMatch();
    }

    function checkMatch() {
        if (!confirmInput.value) { matchEl.classList.remove('cr-match-show'); return; }
        matchEl.classList.add('cr-match-show');
        if (passInput.value === confirmInput.value && passInput.value) {
            matchEl.classList.add('cr-match-ok');
            matchEl.querySelector('.cr-match-txt').textContent = '✓ Passwords match';
            matchEl.querySelector('.cr-match-ico i').className = 'bi bi-check-circle-fill';
        } else {
            matchEl.classList.remove('cr-match-ok');
            matchEl.querySelector('.cr-match-txt').textContent = 'Passwords do not match';
            matchEl.querySelector('.cr-match-ico i').className = 'bi bi-exclamation-circle-fill';
        }
    }

    passInput.addEventListener('input', updateStrength);
    confirmInput.addEventListener('input', checkMatch);

    // ===================== ENTRANCE ANIMATIONS =====================
    const card = document.querySelector('.cr-card');
    const brand = document.querySelector('.cr-brand-inner');
    setTimeout(() => { if (card) card.classList.add('cr-card-vis'); }, 100);
    setTimeout(() => { if (brand) brand.classList.add('cr-brand-vis'); }, 300);

    // ===================== INPUT FOCUS =====================
    document.querySelectorAll('.cr-iw').forEach(w => {
        const inp = w.querySelector('.cr-input');
        const ico = w.querySelector('.cr-ico');
        if (inp && ico) {
            inp.addEventListener('focus', () => {
                w.classList.add('cr-iw-focus');
                ico.style.color = '#2563EB';
                ico.style.transform = 'translateY(-50%) scale(1.15)';
            });
            inp.addEventListener('blur', () => {
                w.classList.remove('cr-iw-focus');
                ico.style.color = '#64748b';
                ico.style.transform = 'translateY(-50%) scale(1)';
            });
        }
    });

    // ===================== SUBMIT =====================
    const form = document.getElementById('crForm');
    const btn = document.getElementById('crBtn');
    const btnLdr = document.getElementById('crBtnLdr');
    const btnTxt = btn?.querySelector('.cr-btn-txt');

    if (form && btn) {
        form.addEventListener('submit', function(e) {
            // Client-side password match check
            if (passInput.value !== confirmInput.value) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Passwords Don\'t Match',
                    text: 'Please make sure your passwords match before submitting.',
                    background: '#1e293b',
                    color: '#f1f5f9',
                    confirmButtonColor: '#2563EB',
                    confirmButtonText: 'Fix it',
                    iconColor: '#ef4444',
                });
                return;
            }

            // Client-side strength check
            if (passInput.value.length > 0 && calcStrength(passInput.value).score < 1) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Weak Password',
                    text: 'Please use at least 8 characters for a stronger password.',
                    background: '#1e293b',
                    color: '#f1f5f9',
                    confirmButtonColor: '#2563EB',
                    confirmButtonText: 'OK',
                    iconColor: '#f59e0b',
                });
                return;
            }

            // Terms checkbox check
            const termsCheck = document.getElementById('crTerms');
            if (termsCheck && !termsCheck.checked) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Terms & Conditions',
                    text: 'Please agree to the Terms of Service and Privacy Policy to continue.',
                    background: '#1e293b',
                    color: '#f1f5f9',
                    confirmButtonColor: '#2563EB',
                    confirmButtonText: 'I agree',
                    iconColor: '#f59e0b',
                });
                return;
            }

            // Loading state
            btn.disabled = true;
            btn.style.pointerEvents = 'none';
            btnTxt.textContent = 'Creating account...';
            btnLdr.innerHTML = '<div class="cr-spin"></div>';

            // Safety timeout
            setTimeout(function() {
                if (btn.disabled) {
                    btn.disabled = false;
                    btn.style.pointerEvents = '';
                    btnTxt.textContent = 'Create Account';
                    btnLdr.innerHTML = '<i class="bi bi-arrow-right"></i>';
                }
            }, 10000);
        });
    }

    // ===================== SWEETALERT2 =====================
    const errInput = document.getElementById('crErrors');
    if (errInput) {
        try {
            const errs = JSON.parse(errInput.value);
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: Array.isArray(errs) ? errs.join('\n') : errInput.value,
                background: '#1e293b',
                color: '#f1f5f9',
                confirmButtonColor: '#2563EB',
                confirmButtonText: 'Try Again',
                iconColor: '#ef4444',
            });
        } catch(e) {}
    }

    const sucInput = document.getElementById('crSuccess');
    if (sucInput) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: sucInput.value,
            background: '#1e293b',
            color: '#f1f5f9',
            confirmButtonColor: '#2563EB',
            timer: 3000,
            timerProgressBar: true,
            iconColor: '#22c55e',
        });
    }

    // ===================== KEYBOARD =====================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const a = document.activeElement;
            if (a && (a.id === 'crName' || a.id === 'crEmail' || a.id === 'crPass' || a.id === 'crPassConfirm')) {
                e.preventDefault();
                form?.requestSubmit();
            }
        }
    });

    // ===================== MOUSE GLOW =====================
    const c = document.querySelector('.cr-card');
    if (c) {
        c.addEventListener('mousemove', function(e) {
            const r = this.getBoundingClientRect();
            this.style.setProperty('--mx', ((e.clientX - r.left) / r.width * 100) + '%');
            this.style.setProperty('--my', ((e.clientY - r.top) / r.height * 100) + '%');
        });
    }
});
</script>

<style>
/* ===== RESET ===== */
* { margin:0; padding:0; box-sizing:border-box; }

.cr-page {
    --clr-primary: #2563EB;
    --clr-light: #60A5FA;
    --clr-dark: #1E40AF;
    --clr-bg: #0b1121;
    --clr-card: rgba(255,255,255,0.04);
    --clr-border: rgba(255,255,255,0.06);
    --clr-text: #f1f5f9;
    --clr-muted: #94a3b8;
    --clr-input-bg: rgba(255,255,255,0.05);
    --clr-input-bd: rgba(255,255,255,0.08);
    --clr-focus: rgba(37,99,235,0.25);
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

/* ===== ORBS ===== */
.cr-orb {
    position: fixed; border-radius: 50%; filter: blur(80px); pointer-events: none; z-index: 0;
}
.cr-orb-1 {
    width: 550px; height: 550px;
    background: radial-gradient(circle, rgba(37,99,235,0.12), transparent 70%);
    top: -180px; left: -120px;
    animation: cr-o1 14s ease-in-out infinite;
}
.cr-orb-2 {
    width: 450px; height: 450px;
    background: radial-gradient(circle, rgba(96,165,250,0.08), transparent 70%);
    bottom: -120px; right: -80px;
    animation: cr-o2 16s ease-in-out infinite;
}
.cr-orb-3 {
    width: 350px; height: 350px;
    background: radial-gradient(circle, rgba(30,64,175,0.1), transparent 70%);
    top: 40%; left: 50%;
    animation: cr-o3 18s ease-in-out infinite;
}
.cr-orb-4 {
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(37,99,235,0.06), transparent 70%);
    bottom: 15%; left: 20%;
    animation: cr-o1 22s ease-in-out infinite reverse;
}
@keyframes cr-o1 { 0%,100% { transform:translate(0,0) scale(1); } 50% { transform:translate(70px,50px) scale(1.12); } }
@keyframes cr-o2 { 0%,100% { transform:translate(0,0) scale(1); } 50% { transform:translate(-50px,-70px) scale(1.08); } }
@keyframes cr-o3 { 0%,100% { transform:translate(0,0) scale(1); } 50% { transform:translate(30px,-40px) scale(1.15); } }

/* ===== GRID ===== */
.cr-grid {
    position: fixed; inset: 0;
    background-image:
        linear-gradient(rgba(37,99,235,0.025) 1px, transparent 1px),
        linear-gradient(90deg, rgba(37,99,235,0.025) 1px, transparent 1px);
    background-size: 50px 50px;
    pointer-events: none; z-index: 1;
    mask-image: radial-gradient(ellipse at 50% 50%, black 25%, transparent 70%);
    -webkit-mask-image: radial-gradient(ellipse at 50% 50%, black 25%, transparent 70%);
}

/* ===== PARTICLES ===== */
.cr-particles { position:fixed; inset:0; overflow:hidden; pointer-events:none; z-index:1; }
.cr-p {
    position:absolute;
    background:linear-gradient(135deg,var(--clr-primary),var(--clr-light));
    border-radius:50%;
    animation:cr-rise linear infinite;
}
@keyframes cr-rise {
    0% { transform:translateY(0) rotate(0deg); opacity:0; }
    10% { opacity:0.4; }
    90% { opacity:0.15; }
    100% { transform:translateY(-100vh) rotate(360deg); opacity:0; }
}

/* ===== BUBBLES ===== */
.cr-bubble {
    position:fixed; z-index:2; pointer-events:none;
    background:rgba(255,255,255,0.03);
    backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px);
    border:1px solid rgba(255,255,255,0.06);
    border-radius:14px; padding:10px 14px;
    display:flex; align-items:center; gap:8px;
    animation:cr-bf 7s ease-in-out infinite;
}
.cr-bubble-1 { top:18%; left:6%; animation-delay:0s; }
.cr-bubble-2 { bottom:25%; left:8%; animation-delay:2.5s; animation-duration:8s; }
.cr-bubble-3 { top:45%; right:5%; animation-delay:5s; font-size:13px; color:var(--clr-muted); font-weight:500; }
@keyframes cr-bf { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-10px); } }
.cr-bubble-inner { display:flex; gap:5px; }
.cr-b-dot { width:7px; height:7px; border-radius:50%; background:var(--clr); animation:cr-bdp 2s ease-in-out infinite; }
.cr-b-dot:nth-child(2) { animation-delay:0.3s; }
.cr-b-dot:nth-child(3) { animation-delay:0.6s; }
@keyframes cr-bdp { 0%,100% { transform:scale(1); opacity:0.5; } 50% { transform:scale(1.3); opacity:1; } }
.cr-b-avatar { width:26px; height:26px; border-radius:50%; background:linear-gradient(135deg,var(--clr-primary),var(--clr-light)); flex-shrink:0; }
.cr-b-lines { display:flex; flex-direction:column; gap:4px; }
.cr-b-line { width:75px; height:5px; border-radius:3px; background:rgba(255,255,255,0.08); }

/* ===== WRAPPER ===== */
.cr-wrapper {
    position:relative; z-index:10;
    display:flex; align-items:center; justify-content:center;
    gap:50px; width:100%; max-width:1100px;
    padding:32px 20px; min-height:100vh; min-height:100dvh;
}

/* ===== BRAND ===== */
.cr-brand { flex:1; max-width:420px; display:flex; align-items:center; justify-content:center; }
.cr-brand-inner {
    opacity:0; transform:translateX(-30px);
    transition:all 0.8s cubic-bezier(.16,1,.3,1);
}
.cr-brand-inner.cr-brand-vis { opacity:1; transform:translateX(0); }

.cr-badge {
    display:inline-flex; align-items:center; gap:6px;
    padding:5px 12px; background:rgba(37,99,235,0.1);
    border:1px solid rgba(37,99,235,0.15); border-radius:999px;
    font-size:0.78rem; font-weight:600; color:var(--clr-light);
    margin-bottom:20px; letter-spacing:0.3px;
}
.cr-badge::before {
    content:''; width:5px; height:5px; border-radius:50%;
    background:#22c55e;
    animation:cr-pulse 2s ease-in-out infinite;
}
@keyframes cr-pulse { 0%,100% { box-shadow:0 0 0 0 rgba(34,197,94,0.6); } 50% { box-shadow:0 0 0 5px rgba(34,197,94,0); } }

.cr-logo {
    display:flex; align-items:center; gap:10px;
    text-decoration:none; margin-bottom:12px;
}
.cr-logo-icon {
    width:44px; height:44px; display:flex; align-items:center; justify-content:center;
    background:linear-gradient(135deg,var(--clr-primary),var(--clr-dark));
    border-radius:12px; font-size:1.35rem; color:#fff;
    box-shadow:0 6px 20px rgba(37,99,235,0.3);
}
.cr-logo-text {
    font-size:1.8rem; font-weight:800;
    background:linear-gradient(135deg,var(--clr-light),var(--clr-primary));
    -webkit-background-clip:text; -webkit-text-fill-color:transparent;
    background-clip:text; letter-spacing:-0.5px;
}
.cr-tagline {
    font-size:1.05rem; color:var(--clr-muted); margin-bottom:28px; line-height:1.6;
}

/* Benefits */
.cr-benefits { display:flex; flex-direction:column; gap:14px; margin-bottom:28px; }
.cr-benefit {
    display:flex; align-items:center; gap:14px;
    padding:12px 16px;
    background:rgba(255,255,255,0.03);
    border:1px solid rgba(255,255,255,0.05);
    border-radius:12px; transition:all 0.3s ease; cursor:default;
}
.cr-benefit:hover {
    background:rgba(255,255,255,0.06);
    transform:translateX(4px);
}
.cr-benefit-icon {
    width:38px; height:38px; display:flex; align-items:center; justify-content:center;
    background:linear-gradient(135deg,rgba(37,99,235,0.15),rgba(37,99,235,0.05));
    border-radius:10px; font-size:1.15rem; color:var(--clr-light); flex-shrink:0;
}
.cr-benefit-info { display:flex; flex-direction:column; gap:2px; }
.cr-benefit-title { font-size:0.85rem; font-weight:600; color:var(--clr-text); }
.cr-benefit-desc { font-size:0.78rem; color:var(--clr-muted); }

/* Testimonial */
.cr-testimonial {
    display:flex; align-items:center; gap:12px;
    padding:14px 16px; background:rgba(255,255,255,0.03);
    border:1px solid rgba(255,255,255,0.05); border-radius:12px;
}
.cr-test-avatars { display:flex; flex-shrink:0; }
.cr-test-av {
    width:32px; height:32px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:0.75rem; font-weight:700; color:#fff;
    border:2px solid var(--clr-bg);
}
.cr-test-text { font-size:0.82rem; color:var(--clr-muted); line-height:1.4; }
.cr-test-text strong { color:var(--clr-light); }

/* ===== CARD ===== */
.cr-card-wrap { flex:1; max-width:440px; display:flex; align-items:center; justify-content:center; }
.cr-card {
    --mx:50%; --my:50%;
    width:100%;
    background:var(--clr-card);
    backdrop-filter:blur(24px) saturate(180%);
    -webkit-backdrop-filter:blur(24px) saturate(180%);
    border:1px solid var(--clr-border); border-radius:24px;
    padding:36px 32px; position:relative; overflow:hidden;
    transition:all 0.6s cubic-bezier(.16,1,.3,1);
    opacity:0; transform:translateY(30px) scale(0.97);
}
.cr-card.cr-card-vis { opacity:1; transform:translateY(0) scale(1); }
.cr-card::before {
    content:''; position:absolute;
    top:var(--my); left:var(--mx);
    transform:translate(-50%,-50%);
    width:350px; height:350px;
    background:radial-gradient(circle,rgba(37,99,235,0.06),transparent 70%);
    border-radius:50%; pointer-events:none; z-index:0;
    transition:all 0.3s ease;
}
.cr-card:hover {
    border-color:rgba(37,99,235,0.12);
    box-shadow:0 20px 60px rgba(0,0,0,0.3);
    transform:translateY(-2px);
}

/* Header */
.cr-card-hd { text-align:center; margin-bottom:28px; position:relative; z-index:1; }
.cr-card-ico {
    width:52px; height:52px; display:flex; align-items:center; justify-content:center;
    background:linear-gradient(135deg,rgba(37,99,235,0.15),rgba(37,99,235,0.05));
    border:1px solid rgba(37,99,235,0.15); border-radius:16px;
    margin:0 auto 14px; font-size:1.4rem; color:var(--clr-light);
    animation:cr-ico-pulse 3s ease-in-out infinite;
}
@keyframes cr-ico-pulse { 0%,100% { transform:scale(1); } 50% { transform:scale(1.05); } }
.cr-card-title { font-size:1.5rem; font-weight:800; color:var(--clr-text); margin-bottom:6px; letter-spacing:-0.3px; }
.cr-card-sub { font-size:0.88rem; color:var(--clr-muted); }

/* Body */
.cr-card-body { position:relative; z-index:1; }

/* ===== FORM ===== */
.cr-fg { margin-bottom:18px; }
.cr-label {
    display:block; font-size:0.82rem; font-weight:600;
    color:var(--clr-text); margin-bottom:7px; letter-spacing:0.2px;
}
.cr-iw { position:relative; border-radius:12px; overflow:hidden; }
.cr-ico {
    position:absolute; left:14px; top:50%;
    transform:translateY(-50%); color:#64748b;
    z-index:2; font-size:1rem;
    transition:all 0.3s cubic-bezier(.16,1,.3,1);
}
.cr-input {
    width:100%; padding:13px 42px 13px 44px;
    background:var(--clr-input-bg); border:1.5px solid var(--clr-input-bd);
    border-radius:12px; font-family:var(--font); font-size:0.92rem;
    color:var(--clr-text); outline:none;
    transition:all 0.3s cubic-bezier(.16,1,.3,1);
    position:relative; z-index:1;
}
.cr-input::placeholder { color:#475569; }
.cr-input:focus { border-color:var(--clr-primary); background:rgba(37,99,235,0.05); box-shadow:0 0 0 4px var(--clr-focus); }
.cr-err { border-color:#ef4444 !important; }
.cr-glow {
    position:absolute; inset:0; border-radius:12px;
    background:radial-gradient(circle at var(--mx,50%) var(--my,50%),rgba(37,99,235,0.08),transparent 60%);
    pointer-events:none; opacity:0; transition:opacity 0.3s ease; z-index:0;
}
.cr-iw-focus .cr-glow { opacity:1; }
.cr-err-txt { display:block; color:#ef4444; font-size:0.78rem; margin-top:5px; font-weight:500; }

/* Password Toggle */
.cr-pw-tog {
    position:absolute; right:10px; top:50%; transform:translateY(-50%);
    background:none; border:none; color:#64748b; font-size:1.05rem;
    cursor:pointer; padding:4px; z-index:2;
    transition:all 0.3s ease; border-radius:6px;
}
.cr-pw-tog:hover { color:var(--clr-text); background:rgba(255,255,255,0.05); }

/* Password Strength */
.cr-ps {
    margin-top:10px; padding:10px 12px;
    background:rgba(255,255,255,0.03);
    border-radius:8px; border:1px solid rgba(255,255,255,0.04);
}
.cr-ps-dots { display:flex; gap:5px; margin-bottom:7px; }
.cr-ps-d {
    flex:1; height:4px; background:rgba(255,255,255,0.08);
    border-radius:2px; transition:all 0.3s ease;
}
.cr-ps-d.cr-weak { background:#ef4444; }
.cr-ps-d.cr-medium { background:#f59e0b; }
.cr-ps-d.cr-strong { background:#10b981; }
.cr-ps-txt { font-size:0.75rem; display:flex; align-items:center; gap:5px; }
.cr-ps-txt.cr-weak { color:#ef4444; }
.cr-ps-txt.cr-medium { color:#f59e0b; }
.cr-ps-txt.cr-strong { color:#10b981; }

/* Password Match */
.cr-match {
    margin-top:8px; padding:8px 12px;
    border-radius:8px; font-size:0.8rem;
    background:rgba(239,68,68,0.1); color:#ef4444;
    border-left:3px solid #ef4444;
    display:none; align-items:center; gap:7px;
}
.cr-match.cr-match-show { display:flex; animation:cr-ms 0.3s ease-out; }
.cr-match.cr-match-ok { background:rgba(16,185,129,0.1); color:#10b981; border-left-color:#10b981; }
.cr-match-ico { display:flex; font-size:0.95rem; }
@keyframes cr-ms { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }

/* Terms */
.cr-terms { margin-bottom:20px; }
.cr-cb {
    display:inline-flex; align-items:center; gap:8px; cursor:pointer; user-select:none;
}
.cr-cb input { display:none; }
.cr-cb-mark {
    width:18px; height:18px; border:2px solid rgba(255,255,255,0.15);
    border-radius:5px; display:flex; align-items:center; justify-content:center;
    transition:all 0.3s ease; flex-shrink:0;
}
.cr-cb-mark i { font-size:11px; color:#fff; opacity:0; transform:scale(0); transition:all 0.2s ease; }
.cr-cb input:checked + .cr-cb-mark { background:var(--clr-primary); border-color:var(--clr-primary); }
.cr-cb input:checked + .cr-cb-mark i { opacity:1; transform:scale(1); }
.cr-cb-label { font-size:0.82rem; color:var(--clr-muted); font-weight:500; }
.cr-link-inline { color:var(--clr-primary); text-decoration:none; font-weight:600; transition:color 0.3s; }
.cr-link-inline:hover { color:var(--clr-light); text-decoration:underline; }

/* Submit Button */
.cr-btn {
    width:100%; padding:14px 20px;
    background:linear-gradient(135deg,var(--clr-primary),var(--clr-dark));
    border:none; border-radius:12px;
    font-family:var(--font); font-size:0.95rem; font-weight:700;
    color:#fff; cursor:pointer;
    display:flex; align-items:center; justify-content:center; gap:8px;
    position:relative; overflow:hidden;
    transition:all 0.4s cubic-bezier(.16,1,.3,1);
    box-shadow:0 4px 16px rgba(37,99,235,0.3);
}
.cr-btn:hover { transform:translateY(-2px); box-shadow:0 8px 28px rgba(37,99,235,0.4); }
.cr-btn:active { transform:translateY(0); }
.cr-btn:disabled { opacity:0.7; cursor:not-allowed; transform:none; }
.cr-btn-shine {
    position:absolute; top:0; left:-100%;
    width:60%; height:100%;
    background:linear-gradient(90deg,transparent,rgba(255,255,255,0.15),transparent);
    transform:skewX(-20deg); transition:left 0.8s ease;
}
.cr-btn:hover .cr-btn-shine { left:150%; }
.cr-btn-ldr { font-size:1.05rem; display:flex; align-items:center; }
.cr-btn:hover .cr-btn-ldr i { animation:cr-ba 1s ease infinite; }
@keyframes cr-ba { 0%,100% { transform:translateX(0); } 50% { transform:translateX(4px); } }
.cr-spin {
    width:16px; height:16px;
    border:2.5px solid rgba(255,255,255,0.3);
    border-top-color:#fff; border-radius:50%;
    animation:cr-sp 0.7s linear infinite;
}
@keyframes cr-sp { to { transform:rotate(360deg); } }

/* Divider */
.cr-div { display:flex; align-items:center; margin:22px 0; }
.cr-div::before, .cr-div::after { content:''; flex:1; height:1px; background:rgba(255,255,255,0.06); }
.cr-div span { padding:0 14px; font-size:0.78rem; color:var(--clr-muted); font-weight:500; }

/* Social */
.cr-social { display:flex; gap:10px; margin-bottom:22px; }
.cr-soc {
    flex:1; display:flex; align-items:center; justify-content:center; gap:7px;
    padding:11px 14px; background:rgba(255,255,255,0.04);
    border:1px solid rgba(255,255,255,0.08); border-radius:12px;
    font-family:var(--font); font-size:0.82rem; font-weight:600;
    color:var(--clr-muted); cursor:pointer;
    transition:all 0.3s ease;
}
.cr-soc:hover { background:rgba(255,255,255,0.08); border-color:rgba(255,255,255,0.12); color:var(--clr-text); transform:translateY(-1px); }

/* Login Link */
.cr-login-row { text-align:center; font-size:0.88rem; color:var(--clr-muted); display:flex; align-items:center; justify-content:center; gap:5px; flex-wrap:wrap; }
.cr-login-link { display:inline-flex; align-items:center; gap:4px; font-weight:700; color:var(--clr-primary); text-decoration:none; transition:all 0.3s ease; }
.cr-login-link:hover { color:var(--clr-light); gap:7px; }
.cr-login-link i { font-size:0.82rem; transition:transform 0.3s ease; }
.cr-login-link:hover i { transform:translateX(3px); }

/* Card Footer */
.cr-card-ft {
    display:flex; align-items:center; justify-content:center; gap:7px;
    margin-top:22px; padding-top:18px; border-top:1px solid rgba(255,255,255,0.05);
    position:relative; z-index:1;
}
.cr-card-ft i { font-size:0.82rem; color:var(--clr-primary); }
.cr-card-ft span { font-size:0.75rem; color:var(--clr-muted); }

/* ===== RESPONSIVE ===== */
@media (max-width: 968px) {
    .cr-wrapper { gap:36px; }
    .cr-brand { max-width:340px; }
    .cr-card-wrap { max-width:400px; }
    .cr-bubble { display:none; }
}
@media (max-width: 820px) {
    .cr-wrapper { flex-direction:column; gap:28px; padding:20px 16px; min-height:auto; }
    .cr-brand { max-width:100%; width:100%; }
    .cr-brand-inner { text-align:center; }
    .cr-logo { justify-content:center; }
    .cr-benefits { max-width:400px; margin-left:auto; margin-right:auto; }
    .cr-testimonial { justify-content:center; max-width:400px; margin:0 auto; }
    .cr-card-wrap { max-width:100%; width:100%; max-width:460px; }
    .cr-card { padding:28px 22px; }
    .cr-card-title { font-size:1.3rem; }
}
@media (max-width: 480px) {
    .cr-card { padding:22px 16px; border-radius:18px; }
    .cr-social { flex-direction:column; }
    .cr-logo-text { font-size:1.5rem; }
    .cr-logo-icon { width:38px; height:38px; font-size:1.1rem; border-radius:10px; }
    .cr-card-ico { width:44px; height:44px; font-size:1.2rem; border-radius:12px; }
    .cr-card-title { font-size:1.2rem; }
    .cr-brand-inner { text-align:center; }
    .cr-badge { margin-left:auto; margin-right:auto; }
}

/* ===== SCROLLBAR ===== */
::-webkit-scrollbar { width:6px; }
::-webkit-scrollbar-track { background:var(--clr-bg); }
::-webkit-scrollbar-thumb { background:rgba(255,255,255,0.08); border-radius:3px; }
::-webkit-scrollbar-thumb:hover { background:rgba(255,255,255,0.12); }
</style>
</body>
</html>
