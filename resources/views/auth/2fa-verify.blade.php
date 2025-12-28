<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Verification - AI Legal Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(-45deg, #0f172a, #1e1b4b, #312e81, #4f46e5);
            background-size: 400% 400%;
            animation: gradientBG 12s ease infinite;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .security-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 
                0 25px 45px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        
        .otp-input {
            width: 60px;
            height: 70px;
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: white;
            transition: all 0.3s ease;
        }
        
        .otp-input:focus {
            border-color: rgba(34, 197, 94, 0.5);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
            transform: translateY(-2px);
        }
        
        .otp-input.filled {
            border-color: rgba(34, 197, 94, 0.5);
            background: rgba(34, 197, 94, 0.1);
            animation: pulse 0.5s ease;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .countdown {
            font-variant-numeric: tabular-nums;
            animation: countdownPulse 1s infinite;
        }
        
        @keyframes countdownPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .loader {
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-top: 3px solid rgba(34, 197, 94, 0.8);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .floating-shield {
            animation: floatShield 6s ease-in-out infinite;
        }
        
        @keyframes floatShield {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        
        .ripple-effect {
            position: absolute;
            border-radius: 50%;
            background: rgba(34, 197, 94, 0.2);
            transform: scale(0);
            animation: ripple 1s linear;
        }
        
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        .verification-success {
            animation: successScale 0.5s ease-out;
        }
        
        @keyframes successScale {
            0% { transform: scale(0.8); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .digit-entered {
            animation: digitPop 0.3s ease;
        }
        
        @keyframes digitPop {
            0% { transform: scale(0.8); }
            70% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .glow-border {
            position: relative;
        }
        
        .glow-border::before {
            content: '';
            position: absolute;
            inset: -2px;
            background: linear-gradient(45deg, #10b981, #3b82f6, #8b5cf6);
            border-radius: inherit;
            z-index: -1;
            filter: blur(10px);
            opacity: 0.5;
            animation: glowRotate 3s linear infinite;
        }
        
        @keyframes glowRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .typewriter {
            overflow: hidden;
            white-space: nowrap;
            border-right: 2px solid rgba(255, 255, 255, 0.7);
            animation: typing 3.5s steps(40, end), blink-caret 0.75s step-end infinite;
        }
        
        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }
        
        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: rgba(255, 255, 255, 0.7); }
        }
        
        .slide-up {
            animation: slideUp 0.8s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive Design */
        @media (max-width: 640px) {
            .otp-input {
                width: 50px;
                height: 60px;
                font-size: 1.5rem;
            }
            
            .security-card {
                margin: 1rem;
                padding: 1.5rem !important;
            }
        }
        
        @media (max-height: 600px) {
            body {
                overflow-y: auto;
            }
            
            .verification-container {
                padding: 2rem 1rem;
            }
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(34, 197, 94, 0.5);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(34, 197, 94, 0.8);
        }
        
        /* Timer Animation */
        .timer-circle {
            stroke-dasharray: 283;
            stroke-dashoffset: 283;
            animation: timer 30s linear forwards;
            transform: rotate(-90deg);
            transform-origin: center;
        }
        
        @keyframes timer {
            to {
                stroke-dashoffset: 0;
            }
        }
    </style>
</head>
<body class="h-full">
    <!-- Animated Background Elements -->
    <div class="floating-shield absolute top-1/4 left-10 w-20 h-20 bg-gradient-to-br from-green-500/10 to-blue-500/10 rounded-full blur-xl"></div>
    <div class="floating-shield absolute bottom-1/4 right-10 w-32 h-32 bg-gradient-to-tr from-blue-500/10 to-purple-500/10 rounded-full blur-xl" style="animation-delay: -3s;"></div>
    
    <!-- Verification Container -->
    <div class="verification-container min-h-screen flex items-center justify-center p-4 relative">
        <div class="w-full max-w-md z-10">
            <!-- Security Card -->
            <div class="security-card rounded-3xl p-8 slide-up">
                <!-- Header with Animated Shield -->
                <div class="text-center mb-8">
                    <div class="relative inline-block mb-4">
                        <div class="glow-border rounded-2xl">
                            <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto shadow-2xl relative">
                                <i class="fas fa-shield-alt text-white text-3xl"></i>
                                <!-- Ripple Effect -->
                                <div class="absolute inset-0 rounded-2xl border-2 border-green-400/30 animate-ping opacity-20"></div>
                            </div>
                        </div>
                    </div>
                    
                    <h1 class="text-2xl font-bold text-white mb-2 animate__animated animate__fadeInDown">
                        @if($type === 'email')
                            <i class="fas fa-envelope mr-2 text-blue-300"></i>Email Verification
                        @else
                            <i class="fas fa-mobile-alt mr-2 text-green-300"></i>Google Authenticator
                        @endif
                    </h1>
                    
                    <p class="text-blue-100/70 mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                        @if($type === 'email')
                            Secure your account with two-factor authentication
                        @else
                            Authenticate using your 2FA app
                        @endif
                    </p>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl backdrop-blur-sm animate__animated animate__shakeX">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-400 mr-3"></i>
                            <div class="text-sm text-red-200">
                                {{ $errors->first() }}
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 rounded-xl backdrop-blur-sm verification-success">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 mr-3"></i>
                            <div class="text-sm text-green-200">
                                {{ session('success') }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- OTP Input Section -->
                <div class="mb-8 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                    <label class="block text-sm font-medium text-white/90 mb-4 text-center">
                        @if($type === 'email')
                            Enter the 6-digit code sent to your email
                        @else
                            Enter 6-digit code from your authenticator app
                        @endif
                    </label>
                    
                    <!-- OTP Inputs -->
                    <form method="POST" action="{{ route('2fa.verify.submit') }}" id="otpForm">
                        @csrf
                        
                        <div class="flex justify-center gap-3 mb-6" id="otpContainer">
                            @for($i = 1; $i <= 6; $i++)
                            <input 
                                type="text" 
                                maxlength="1" 
                                inputmode="numeric" 
                                pattern="[0-9]*" 
                                data-index="{{ $i }}"
                                class="otp-input digit-entered" 
                                required 
                                autocomplete="off"
                                onkeyup="handleOTPInput(this, event)"
                                onfocus="this.select()">
                            @endfor
                        </div>
                        
                        <!-- Hidden Input for Complete Code -->
                        <input type="hidden" name="verification_code" id="verificationCode">
                        
                        <!-- Timer -->
                        @if($type === 'email')
                        <div class="text-center mb-6">
                            <div class="inline-flex items-center gap-2">
                                <div class="relative">
                                    <svg width="40" height="40" class="countdown">
                                        <circle cx="20" cy="20" r="18" stroke="rgba(255,255,255,0.1)" stroke-width="3" fill="none"/>
                                        <circle cx="20" cy="20" r="18" stroke="rgba(34, 197, 94, 0.8)" stroke-width="3" fill="none" class="timer-circle"/>
                                    </svg>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <span id="countdown" class="text-sm font-semibold text-white">30</span>
                                    </div>
                                </div>
                                <span class="text-sm text-white/60">seconds remaining</span>
                            </div>
                        </div>
                        @endif

                        <button 
                            type="submit" 
                            id="verifyBtn"
                            class="w-full py-3 px-4 bg-gradient-to-r from-green-500 to-blue-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 relative overflow-hidden group">
                            <span class="relative z-10 flex items-center justify-center">
                                <i class="fas fa-shield-check mr-2"></i>
                                Verify & Continue
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-green-500/0 via-green-500/20 to-green-500/0 transform translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-1000"></div>
                        </button>
                    </form>
                </div>

                <!-- Resend Code -->
                @if($type === 'email')
                <div class="text-center animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                    <form method="POST" action="{{ route('2fa.resend') }}" id="resendForm">
                        @csrf
                        <p class="text-sm text-white/60 mb-2">
                            Didn't receive the code? 
                        </p>
                        <button 
                            type="submit" 
                            id="resendBtn"
                            disabled
                            class="text-sm text-blue-300 hover:text-white transition-colors hover:underline disabled:text-white/30 disabled:cursor-not-allowed group">
                            <i class="fas fa-redo mr-1 group-hover:animate-spin"></i>
                            <span id="resendText">Resend code in <span id="resendCountdown">30</span>s</span>
                        </button>
                    </form>
                </div>
                @endif

                <!-- Back to Login -->
                <div class="mt-6 text-center animate__animated animate__fadeInUp" style="animation-delay: 0.5s;">
                    <a href="{{ route('login') }}" class="text-sm text-white/60 hover:text-white transition-colors group">
                        <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                        Back to login
                    </a>
                </div>
            </div>

            <!-- Security Tips -->
            <div class="mt-6 grid grid-cols-2 gap-3">
                <div class="text-center p-3 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm slide-up" style="animation-delay: 0.6s;">
                    <i class="fas fa-clock text-yellow-400 mb-1"></i>
                    <p class="text-xs text-white/70">Code expires in 5 minutes</p>
                </div>
                <div class="text-center p-3 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm slide-up" style="animation-delay: 0.7s;">
                    <i class="fas fa-user-shield text-green-400 mb-1"></i>
                    <p class="text-xs text-white/70">Enhanced security</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // OTP Input Handling
        let currentOTP = ['', '', '', '', '', ''];
        
        function handleOTPInput(input, event) {
            const index = parseInt(input.dataset.index) - 1;
            let value = input.value;
            
            // Allow only numbers
            if (!/^\d*$/.test(value)) {
                input.value = '';
                value = '';
            }
            
            // Update current OTP
            if (value) {
                currentOTP[index] = value;
                input.classList.add('filled');
                input.classList.add('digit-entered');
                
                // Move to next input
                if (index < 5) {
                    const nextInput = document.querySelector(`[data-index="${index + 2}"]`);
                    nextInput.focus();
                }
                
                // Auto-submit when all digits are entered
                if (index === 5 && currentOTP.every(digit => digit !== '')) {
                    autoSubmitOTP();
                }
            } else {
                currentOTP[index] = '';
                input.classList.remove('filled');
            }
            
            // Handle backspace
            if (event.key === 'Backspace' && !value && index > 0) {
                const prevInput = document.querySelector(`[data-index="${index}"]`);
                prevInput.focus();
                prevInput.value = '';
                currentOTP[index - 1] = '';
                prevInput.classList.remove('filled');
            }
            
            // Update hidden input
            document.getElementById('verificationCode').value = currentOTP.join('');
        }
        
        // Auto-submit when OTP is complete
        function autoSubmitOTP() {
            const otp = currentOTP.join('');
            if (otp.length === 6 && /^\d{6}$/.test(otp)) {
                document.getElementById('verificationCode').value = otp;
                
                // Show success animation
                document.querySelectorAll('.otp-input').forEach(input => {
                    input.classList.add('border-green-500');
                });
                
                // Add ripple effect
                createRipple(document.getElementById('verifyBtn'));
                
                // Submit form after delay
                setTimeout(() => {
                    document.getElementById('verifyBtn').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Verifying...';
                    document.getElementById('verifyBtn').disabled = true;
                    document.getElementById('otpForm').submit();
                }, 500);
            }
        }
        
        // Timer functionality
        let countdown = 30;
        let timerInterval;
        
        function startTimer() {
            const countdownElement = document.getElementById('countdown');
            const resendCountdown = document.getElementById('resendCountdown');
            const resendBtn = document.getElementById('resendBtn');
            
            timerInterval = setInterval(() => {
                countdown--;
                countdownElement.textContent = countdown;
                resendCountdown.textContent = countdown;
                
                if (countdown <= 0) {
                    clearInterval(timerInterval);
                    resendBtn.disabled = false;
                    document.getElementById('resendText').textContent = 'Resend code';
                    document.querySelector('.timer-circle').style.animationPlayState = 'paused';
                }
            }, 1000);
        }
        
        // Resend code
        document.getElementById('resendForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('resendBtn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Sending...';
            btn.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                countdown = 30;
                startTimer();
                btn.innerHTML = '<i class="fas fa-redo mr-1"></i> Resend code in <span id="resendCountdown">30</span>s';
                
                // Show success message
                const successDiv = document.createElement('div');
                successDiv.className = 'mt-4 p-3 bg-green-500/10 border border-green-500/30 rounded-xl backdrop-blur-sm animate__animated animate__fadeIn';
                successDiv.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-400 mr-2"></i>
                        <div class="text-sm text-green-200">
                            New verification code sent successfully!
                        </div>
                    </div>
                `;
                document.querySelector('.security-card').insertBefore(successDiv, document.querySelector('.text-center'));
                
                setTimeout(() => {
                    successDiv.remove();
                }, 3000);
            }, 1000);
        });
        
        // Create ripple effect
        function createRipple(element) {
            const ripple = document.createElement('span');
            const rect = element.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = rect.width / 2 - size / 2;
            const y = rect.height / 2 - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                transform: scale(0);
                animation: ripple 0.6s linear;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                pointer-events: none;
            `;
            
            element.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        }
        
        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            // Focus first OTP input
            setTimeout(() => {
                document.querySelector('[data-index="1"]').focus();
            }, 500);
            
            // Start timer for email verification
            @if($type === 'email')
            startTimer();
            @endif
            
            // Add animation to OTP inputs
            const otpInputs = document.querySelectorAll('.otp-input');
            otpInputs.forEach((input, index) => {
                setTimeout(() => {
                    input.classList.add('animate__animated', 'animate__fadeInUp');
                }, index * 100);
            });
            
            // Form submission animation
            document.getElementById('otpForm').addEventListener('submit', function(e) {
                const otp = currentOTP.join('');
                if (otp.length !== 6) {
                    e.preventDefault();
                    
                    // Shake empty inputs
                    otpInputs.forEach(input => {
                        if (!input.value) {
                            input.classList.add('animate__animated', 'animate__headShake');
                            setTimeout(() => {
                                input.classList.remove('animate__animated', 'animate__headShake');
                            }, 1000);
                        }
                    });
                    
                    return false;
                }
                
                // Add loading state
                const verifyBtn = document.getElementById('verifyBtn');
                verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Verifying...';
                verifyBtn.disabled = true;
                
                // Add loader animation
                const loader = document.createElement('div');
                loader.className = 'loader mx-auto mt-4';
                this.appendChild(loader);
            });
            
            // Add hover effects
            const securityCard = document.querySelector('.security-card');
            securityCard.addEventListener('mouseenter', () => {
                securityCard.style.transform = 'translateY(-5px)';
            });
            securityCard.addEventListener('mouseleave', () => {
                securityCard.style.transform = 'translateY(0)';
            });
            
            // Add click effects to buttons
            document.querySelectorAll('button').forEach(btn => {
                btn.addEventListener('click', function() {
                    createRipple(this);
                });
            });
        });
        
        // Handle paste event for OTP
        document.addEventListener('paste', function(e) {
            const activeElement = document.activeElement;
            if (activeElement.classList.contains('otp-input')) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text');
                const digits = pastedData.replace(/\D/g, '').split('');
                
                digits.forEach((digit, index) => {
                    if (index < 6) {
                        const input = document.querySelector(`[data-index="${index + 1}"]`);
                        if (input) {
                            input.value = digit;
                            currentOTP[index] = digit;
                            input.classList.add('filled', 'digit-entered');
                        }
                    }
                });
                
                // Focus last filled input or submit
                const lastIndex = Math.min(digits.length, 5);
                const lastInput = document.querySelector(`[data-index="${lastIndex + 1}"]`);
                if (lastInput) {
                    lastInput.focus();
                }
                
                // Auto-submit if complete
                if (digits.length >= 6) {
                    autoSubmitOTP();
                }
            }
        });
        
        // Keyboard navigation for OTP
        document.addEventListener('keydown', function(e) {
            const activeElement = document.activeElement;
            if (activeElement.classList.contains('otp-input')) {
                const index = parseInt(activeElement.dataset.index) - 1;
                
                if (e.key === 'ArrowRight' && index < 5) {
                    e.preventDefault();
                    document.querySelector(`[data-index="${index + 2}"]`).focus();
                } else if (e.key === 'ArrowLeft' && index > 0) {
                    e.preventDefault();
                    document.querySelector(`[data-index="${index}"]`).focus();
                }
            }
        });
    </script>
</body>
</html>