@extends('frontend.app')

@section('content')

<div class="chatbox-contact-page">

    <!-- Floating Bubbles Background -->
    <div class="chatbox-floating-bubbles">
        <div class="chatbox-bubble"></div>
        <div class="chatbox-bubble"></div>
        <div class="chatbox-bubble"></div>
        <div class="chatbox-bubble"></div>
        <div class="chatbox-bubble"></div>
    </div>

    <!-- Success Alert -->
    @if (session('success'))
        <div class="chatbox-alert-success chatbox-show" id="successAlert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @else
        <div class="chatbox-alert-success" id="successAlert">
            <i class="bi bi-check-circle-fill"></i> Your message was sent successfully!
        </div>
    @endif

    <!-- Contact Section -->
    <section class="chatbox-contact-section" id="contact">

        <!-- Animated Message Bubbles -->
        <div class="chatbox-message-bubble">
            <i class="bi bi-chat-dots-fill"></i> Hello there!
        </div>
        <div class="chatbox-message-bubble">
            <i class="bi bi-reply-fill"></i> Let's chat!
        </div>

        <div class="chatbox-container">

            <div class="chatbox-section-header chatbox-fade-in">
                <div class="chatbox-section-subtitle">
                    <i class="bi bi-envelope-fill"></i> Contact
                </div>
                <h2 class="chatbox-section-title">Get In Touch With Us</h2>
                <p class="chatbox-section-desc">Let us know for any questions or emergency needs</p>
            </div>

            <div class="chatbox-contact-grid">

                <!-- Form -->
                <div class="chatbox-form-wrapper chatbox-fade-in">

                    <form id="contactForm" action="{{ route('contact') }}" method="POST">
                        @csrf

                        <div class="chatbox-form-group">
                            <label>Your Name</label>
                            <input type="text" name="name" placeholder="Enter your full name" required>
                        </div>

                        <div class="chatbox-form-group">
                            <label>Your Email</label>
                            <input type="email" name="email" placeholder="example@email.com" required>
                        </div>

                        <div class="chatbox-form-group">
                            <label>Message</label>
                            <textarea name="message" rows="5" placeholder="Write your message here..." required></textarea>
                        </div>

                        <button type="submit" class="chatbox-submit-btn" id="contactSubmitBtn">
                            <i class="bi bi-send-fill"></i> Send Message
                        </button>
                    </form>
                </div>

                <!-- Info -->
                <div class="chatbox-info-wrapper chatbox-fade-in">
                    <div class="chatbox-info-card">
                        <div class="chatbox-contact-icon">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div>
                            <h5>Email</h5>
                            <p>
                                {{ $account->email ?? 'N/A' }}<br>
                                {{ $account->website ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>

    <script>
        // Scroll animation (chatbox version)
        const chatboxObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('chatbox-visible');
                    }, index * 80);
                    chatboxObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.chatbox-fade-in').forEach(el => chatboxObserver.observe(el));

        // AJAX Form Submit (Fixed Laravel Version)
        document.getElementById('contactForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const form = this;
            const alertBox = document.getElementById('successAlert');
            const submitBtn = document.getElementById('contactSubmitBtn');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-send-fill"></i> Sending...';

            const formData = new FormData(form);

            fetch("{{ route('contact') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error("Request failed");
                return response.text();
            })
            .then(() => {
                alertBox.classList.add('chatbox-show');
                alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });

                form.reset();

                setTimeout(() => {
                    alertBox.classList.remove('chatbox-show');
                }, 5000);
            })
            .catch(() => {
                alertBox.innerHTML = "Failed to send message, please try again.";
                alertBox.classList.add('chatbox-show');

                setTimeout(() => {
                    alertBox.classList.remove('chatbox-show');
                }, 5000);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-send-fill"></i> Send Message';
            });
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>

    <style>
        /* Scoped to contact page — avoids conflicting with navbar / Bootstrap layout */
        .chatbox-contact-page {
            width: 100%;
            min-height: 100%;
            position: relative;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1f2937;
        }

        /* Floating Message Bubbles Animation */
        .chatbox-contact-page .chatbox-floating-bubbles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .chatbox-contact-page .chatbox-bubble {
            position: absolute;
            background: rgba(37, 99, 235, 0.05);
            border-radius: 50%;
            animation: chatbox-float-up 15s infinite ease-in-out;
        }

        .chatbox-contact-page .chatbox-bubble:nth-child(1) {
            width: 80px;
            height: 80px;
            left: 10%;
            animation-delay: 0s;
            animation-duration: 12s;
        }

        .chatbox-contact-page .chatbox-bubble:nth-child(2) {
            width: 60px;
            height: 60px;
            left: 25%;
            animation-delay: 2s;
            animation-duration: 14s;
        }

        .chatbox-contact-page .chatbox-bubble:nth-child(3) {
            width: 100px;
            height: 100px;
            left: 50%;
            animation-delay: 4s;
            animation-duration: 16s;
        }

        .chatbox-contact-page .chatbox-bubble:nth-child(4) {
            width: 70px;
            height: 70px;
            left: 75%;
            animation-delay: 6s;
            animation-duration: 13s;
        }

        .chatbox-contact-page .chatbox-bubble:nth-child(5) {
            width: 90px;
            height: 90px;
            left: 90%;
            animation-delay: 8s;
            animation-duration: 15s;
        }

        @keyframes chatbox-float-up {
            0% {
                bottom: -100px;
                opacity: 0;
                transform: translateX(0) rotate(0deg);
            }
            50% {
                opacity: 0.3;
                transform: translateX(50px) rotate(180deg);
            }
            100% {
                bottom: 100%;
                opacity: 0;
                transform: translateX(-50px) rotate(360deg);
            }
        }

        /* Alert Success */
        .chatbox-contact-page .chatbox-alert-success {
            position: fixed;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 18px 35px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
            z-index: 1060;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            transition: top 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            white-space: nowrap;
            max-width: calc(100vw - 2rem);
        }

        .chatbox-contact-page .chatbox-alert-success.chatbox-show {
            top: calc(var(--chatbox-navbar-height, 64px) + 16px);
        }

        .chatbox-contact-page .chatbox-alert-success i {
            font-size: 24px;
            flex-shrink: 0;
        }

        /* Contact Section */
        .chatbox-contact-page .chatbox-contact-section {
            position: relative;
            padding: 48px 24px;
            min-height: 100%;
            display: flex;
            align-items: center;
            z-index: 1;
            overflow: hidden;
        }

        .chatbox-contact-page .chatbox-container {
            max-width: 100%;
            margin: 0 auto;
            width: 100%;
        }

        /* Section Header */
        .chatbox-contact-page .chatbox-section-header {
            text-align: center;
            margin-bottom: 48px;
        }

        .chatbox-contact-page .chatbox-section-subtitle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #2563EB 0%, #1d4ed8 100%);
            color: white;
            padding: 10px 25px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            animation: chatbox-pulse-glow 2s infinite;
        }

        @keyframes chatbox-pulse-glow {
            0%, 100% {
                box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            }
            50% {
                box-shadow: 0 4px 25px rgba(37, 99, 235, 0.5);
            }
        }

        .chatbox-contact-page .chatbox-section-title {
            font-size: 2.25rem;
            font-weight: 800;
            margin: 0 0 15px;
            line-height: 1.2;
            background: linear-gradient(135deg, #1f2937 0%, #2563EB 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .chatbox-contact-page .chatbox-section-desc {
            font-size: 1.05rem;
            color: #6b7280;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Contact Grid */
        .chatbox-contact-page .chatbox-contact-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 40px;
            align-items: start;
        }

        /* Form Wrapper */
        .chatbox-contact-page .chatbox-form-wrapper {
            background: white;
            padding: 45px;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(37, 99, 235, 0.08);
            border: 1px solid rgba(37, 99, 235, 0.1);
            position: relative;
            overflow: hidden;
        }

        .chatbox-contact-page .chatbox-form-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2563EB 0%, #1d4ed8 100%);
        }

        .chatbox-contact-page .chatbox-form-group {
            margin-bottom: 25px;
        }

        .chatbox-contact-page .chatbox-form-group label {
            display: block;
            color: #1f2937;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 15px;
        }

        .chatbox-contact-page .chatbox-form-group input,
        .chatbox-contact-page .chatbox-form-group textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            transition: border-color 0.3s ease, background 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
            background: #f9fafb;
            color: #1f2937;
            box-sizing: border-box;
        }

        .chatbox-contact-page .chatbox-form-group input:focus,
        .chatbox-contact-page .chatbox-form-group textarea:focus {
            outline: none;
            border-color: #2563EB;
            background: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
            transform: translateY(-2px);
        }

        .chatbox-contact-page .chatbox-form-group textarea {
            resize: vertical;
            min-height: 130px;
            font-family: inherit;
        }

        .chatbox-contact-page .chatbox-submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #2563EB 0%, #1d4ed8 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .chatbox-contact-page .chatbox-submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(37, 99, 235, 0.4);
        }

        .chatbox-contact-page .chatbox-submit-btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .chatbox-contact-page .chatbox-submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Contact Info Cards */
        .chatbox-contact-page .chatbox-info-wrapper {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .chatbox-contact-page .chatbox-info-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(37, 99, 235, 0.08);
            border: 1px solid rgba(37, 99, 235, 0.1);
            display: flex;
            gap: 20px;
            align-items: flex-start;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .chatbox-contact-page .chatbox-info-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(37, 99, 235, 0.15);
        }

        .chatbox-contact-page .chatbox-contact-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #2563EB 0%, #1d4ed8 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .chatbox-contact-page .chatbox-contact-icon i {
            font-size: 28px;
            color: white;
        }

        .chatbox-contact-page .chatbox-info-card h5 {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 8px;
        }

        .chatbox-contact-page .chatbox-info-card p {
            color: #6b7280;
            line-height: 1.6;
            font-size: 15px;
            margin: 0;
        }

        /* Message Bubble Animation */
        .chatbox-contact-page .chatbox-contact-section > .chatbox-message-bubble {
            position: absolute;
            background: #2563EB;
            color: white;
            padding: 12px 20px;
            border-radius: 20px;
            border-bottom-left-radius: 4px;
            font-size: 14px;
            opacity: 0;
            animation: chatbox-message-appear 3s infinite;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .chatbox-contact-page .chatbox-contact-section > .chatbox-message-bubble:nth-child(1) {
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }

        .chatbox-contact-page .chatbox-contact-section > .chatbox-message-bubble:nth-child(2) {
            top: 30%;
            right: 5%;
            left: auto;
            animation-delay: 1.5s;
            background: #1d4ed8;
            border-bottom-left-radius: 20px;
            border-bottom-right-radius: 4px;
        }

        @keyframes chatbox-message-appear {
            0%, 100% {
                opacity: 0;
                transform: scale(0.8) translateY(20px);
            }
            10%, 90% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        /* Fade In Animation */
        .chatbox-contact-page .chatbox-fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .chatbox-contact-page .chatbox-fade-in.chatbox-visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive — align with Bootstrap md breakpoint */
        @media (max-width: 991.98px) {
            .chatbox-contact-page .chatbox-contact-grid {
                grid-template-columns: 1fr;
                gap: 28px;
            }

            .chatbox-contact-page .chatbox-section-title {
                font-size: 1.875rem;
            }

            .chatbox-contact-page .chatbox-form-wrapper {
                padding: 30px;
            }

            .chatbox-contact-page .chatbox-contact-section > .chatbox-message-bubble {
                display: none;
            }
        }

        @media (max-width: 575.98px) {
            .chatbox-contact-page .chatbox-section-title {
                font-size: 1.5rem;
            }

            .chatbox-contact-page .chatbox-form-wrapper {
                padding: 22px;
            }

            .chatbox-contact-page .chatbox-contact-section {
                padding: 32px 16px;
            }

            .chatbox-contact-page .chatbox-section-header {
                margin-bottom: 32px;
            }
        }
    </style>

@endsection