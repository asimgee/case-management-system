<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - AI Legal Assistant</title>
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
            background: linear-gradient(-45deg, #1e3a8a, #3730a3, #5b21b6, #7c3aed);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .floating-element {
            animation: float 8s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(3deg); }
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        
        .input-field {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(99, 102, 241, 0.5);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        
        .password-strength {
            height: 6px;
            border-radius: 3px;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 2px;
            background: linear-gradient(90deg, #ef4444, #f59e0b, #10b981, #059669);
            background-size: 400% 100%;
        }
        
        .strength-weak { background-position: 0% 0; }
        .strength-moderate { background-position: 33% 0; }
        .strength-good { background-position: 66% 0; }
        .strength-strong { background-position: 100% 0; }
        
        .social-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .social-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.2);
        }
        
        .plan-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .plan-card:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-5px);
            border-color: rgba(99, 102, 241, 0.3);
        }
        
        .plan-card.selected {
            background: rgba(99, 102, 241, 0.2);
            border-color: rgba(99, 102, 241, 0.5);
        }
        
        .pulse-ring {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .checkmark {
            animation: checkmark 0.5s ease-in-out;
        }
        
        @keyframes checkmark {
            0% { transform: scale(0); }
            70% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .slide-up {
            animation: slideUp 0.6s ease-out;
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
        
        .bounce-in {
            animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
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
        
        .shimmer {
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.1),
                transparent
            );
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .register-container {
                padding: 1rem;
            }
            
            .glass-card {
                margin: 1rem;
                padding: 1.5rem !important;
            }
            
            .social-login-btns {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .social-btn {
                width: 100%;
            }
        }
        
        @media (max-height: 700px) {
            .register-container {
                padding-top: 2rem;
                padding-bottom: 2rem;
            }
            
            .glass-card {
                max-height: 90vh;
                overflow-y: auto;
            }
            
            .glass-card::-webkit-scrollbar {
                width: 6px;
            }
            
            .glass-card::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.1);
                border-radius: 3px;
            }
            
            .glass-card::-webkit-scrollbar-thumb {
                background: rgba(99, 102, 241, 0.5);
                border-radius: 3px;
            }
        }
        
        /* Custom checkbox */
        .custom-checkbox {
            position: relative;
            cursor: pointer;
        }
        
        .custom-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }
        
        .checkmark-box {
            position: relative;
            height: 20px;
            width: 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .custom-checkbox input:checked ~ .checkmark-box {
            background: rgba(99, 102, 241, 0.3);
            border-color: rgba(99, 102, 241, 0.5);
        }
        
        .custom-checkbox input:checked ~ .checkmark-box::after {
            content: "✓";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
            animation: checkmark 0.3s ease;
        }
        
        /* Step indicator */
        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2rem;
            gap: 1rem;
        }
        
        .step {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .step.active {
            background: rgba(99, 102, 241, 0.8);
            transform: scale(1.2);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
        }
        
        .step.completed {
            background: rgba(34, 197, 94, 0.8);
        }
        
        .step.completed::after {
            content: "✓";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 8px;
            font-weight: bold;
        }
        
        /* Loading spinner */
        .spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="h-full">
    <!-- Background Elements -->
    <div class="floating-element absolute top-5 left-5 w-20 h-20 bg-gradient-to-br from-blue-500/10 to-purple-500/10 rounded-full blur-xl"></div>
    <div class="floating-element absolute bottom-5 right-5 w-32 h-32 bg-gradient-to-tr from-purple-500/10 to-pink-500/10 rounded-full blur-xl" style="animation-delay: -4s;"></div>
    <div class="floating-element absolute top-1/3 right-1/3 w-16 h-16 bg-gradient-to-br from-indigo-500/10 to-blue-500/10 rounded-full blur-xl" style="animation-delay: -2s;"></div>
    <div class="floating-element absolute bottom-1/3 left-1/4 w-24 h-24 bg-gradient-to-tr from-pink-500/10 to-rose-500/10 rounded-full blur-xl" style="animation-delay: -6s;"></div>

    <div class="register-container min-h-screen flex items-center justify-center p-4 relative">
        <div class="w-full max-w-2xl z-10">
            <!-- Step Indicator -->
            <div class="step-indicator mb-8">
                <div class="step active"></div>
                <div class="step"></div>
                <div class="step"></div>
            </div>

            <!-- Main Registration Card -->
            <div class="glass-card rounded-3xl p-8 slide-up">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="relative inline-block mb-4">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl blur-lg opacity-50 animate-pulse"></div>
                        <div class="pulse-ring absolute inset-0 border-2 border-blue-400/30 rounded-2xl"></div>
                        <div class="relative w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto shadow-2xl">
                            <i class="fas fa-balance-scale text-white text-2xl"></i>
                        </div>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2 animate__animated animate__fadeInDown">
                        Join AI Legal Assistant
                    </h1>
                    <p class="text-blue-100/70 typewriter">
                        Empower your legal practice with AI
                    </p>
                </div>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl backdrop-blur-sm animate__animated animate__shakeX">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-400 text-lg mr-3"></i>
                            <div class="text-sm text-red-200">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('social_error'))
                    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl backdrop-blur-sm">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                            <div class="text-sm text-red-200">
                                {{ session('social_error') }}
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="mb-6 p-4 bg-blue-500/10 border border-blue-500/30 rounded-xl backdrop-blur-sm">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-400 mr-3"></i>
                            <div class="text-sm text-blue-200">
                                {{ session('info') }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Social Login -->
                <div class="mb-8">
                    <div class="social-login-btns flex gap-3 mb-6">
                        <a href="{{ route('auth.google') }}" class="social-btn flex-1 flex items-center justify-center gap-3 p-3 rounded-xl text-white hover:text-white transition-all duration-300 bounce-in" style="animation-delay: 0.1s;">
                            <i class="fab fa-google text-red-400"></i>
                            <span class="text-sm font-medium">Google</span>
                        </a>
                        <a href="{{ route('auth.microsoft') }}" class="social-btn flex-1 flex items-center justify-center gap-3 p-3 rounded-xl text-white hover:text-white transition-all duration-300 bounce-in" style="animation-delay: 0.2s;">
                            <i class="fab fa-microsoft text-blue-400"></i>
                            <span class="text-sm font-medium">Microsoft</span>
                        </a>
                        <a href="{{ route('auth.apple') }}" class="social-btn flex-1 flex items-center justify-center gap-3 p-3 rounded-xl text-white hover:text-white transition-all duration-300 bounce-in" style="animation-delay: 0.3s;">
                            <i class="fab fa-apple text-gray-200"></i>
                            <span class="text-sm font-medium">Apple</span>
                        </a>
                    </div>
                    
                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-white/10"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-transparent text-white/40">
                                Or sign up with email
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Registration Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-6" id="registrationForm">
                    @csrf
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Name Field -->
                        <div class="space-y-2 animate__animated animate__fadeInLeft" style="animation-delay: 0.2s;">
                            <label for="name" class="block text-sm font-medium text-white/90">
                                <i class="fas fa-user mr-2 text-blue-300"></i>Full Name
                            </label>
                            <div class="relative group">
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    value="{{ old('name') }}" 
                                    required 
                                    autofocus
                                    class="input-field w-full px-4 py-3 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all duration-300"
                                    placeholder="John Doe"
                                    oninput="validateName(this)">
                                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fas fa-check text-green-400 hidden" id="nameValid"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Email Field -->
                        <div class="space-y-2 animate__animated animate__fadeInRight" style="animation-delay: 0.3s;">
                            <label for="email" class="block text-sm font-medium text-white/90">
                                <i class="fas fa-envelope mr-2 text-blue-300"></i>Email Address
                            </label>
                            <div class="relative group">
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    class="input-field w-full px-4 py-3 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all duration-300"
                                    placeholder="legal@example.com"
                                    oninput="validateEmail(this)">
                                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fas fa-check text-green-400 hidden" id="emailValid"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-2 animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                        <label for="password" class="block text-sm font-medium text-white/90">
                            <i class="fas fa-lock mr-2 text-blue-300"></i>Password
                        </label>
                        <div class="relative group">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required 
                                class="input-field w-full px-4 py-3 pr-10 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all duration-300"
                                placeholder="••••••••"
                                onkeyup="checkPasswordStrength(this.value)">
                            <button type="button" 
                                    onclick="togglePasswordVisibility('password')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white/50 hover:text-white transition-colors">
                                <i class="fas fa-eye" id="passwordEyeIcon"></i>
                            </button>
                        </div>
                        
                        <!-- Password Strength Meter -->
                        <div class="mt-2">
                            <div class="password-strength strength-weak" id="passwordStrength"></div>
                            <div class="flex justify-between mt-1">
                                <div class="text-xs text-white/50" id="passwordStrengthText">
                                    Password strength: Weak
                                </div>
                                <div class="text-xs text-white/50" id="passwordRequirements">
                                    Min. 8 characters
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-2 animate__animated animate__fadeInUp" style="animation-delay: 0.5s;">
                        <label for="password_confirmation" class="block text-sm font-medium text-white/90">
                            <i class="fas fa-lock mr-2 text-blue-300"></i>Confirm Password
                        </label>
                        <div class="relative group">
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                required 
                                class="input-field w-full px-4 py-3 pr-10 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all duration-300"
                                placeholder="••••••••"
                                oninput="validatePasswordMatch()">
                            <button type="button" 
                                    onclick="togglePasswordVisibility('password_confirmation')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white/50 hover:text-white transition-colors">
                                <i class="fas fa-eye" id="confirmPasswordEyeIcon"></i>
                            </button>
                        </div>
                        <div class="text-xs text-red-400 hidden" id="passwordMatchError">
                            <i class="fas fa-times mr-1"></i>Passwords don't match
                        </div>
                    </div>

                    <!-- Plan Selection -->
                    @if(isset($plans) && $plans->count() > 0)
                    <div class="animate__animated animate__fadeInUp" style="animation-delay: 0.6s;">
                        <label class="block text-sm font-medium text-white/90 mb-3">
                            <i class="fas fa-crown mr-2 text-yellow-300"></i>Select Your Plan
                        </label>
                        <div class="grid md:grid-cols-3 gap-4">
                            @foreach($plans as $plan)
                            <label class="plan-card cursor-pointer rounded-xl p-4 relative">
                                <input type="radio" name="plan_id" value="{{ $plan->id }}" 
                                       class="absolute opacity-0" 
                                       {{ $plan->is_default ? 'checked' : '' }}>
                                <div class="text-center">
                                    <div class="w-10 h-10 mx-auto mb-2 rounded-lg bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center">
                                        <i class="fas {{ $plan->icon ?? 'fa-star' }} text-white"></i>
                                    </div>
                                    <h3 class="font-semibold text-white mb-1">{{ $plan->name }}</h3>
                                    <p class="text-2xl font-bold text-white mb-2">
                                        {{ $plan->price == 0 ? 'Free' : '$' . $plan->price }}
                                        <span class="text-sm text-white/50">/month</span>
                                    </p>
                                   @php
    $features = is_string($plan->features) ? json_decode($plan->features, true) : $plan->features;
@endphp

<div class="text-xs text-white/70 space-y-1">
    @foreach($features as $feature)
        <div class="flex items-center">
            <i class="fas fa-check text-green-400 mr-2 text-xs"></i>
            {{ trim($feature) }}
        </div>
    @endforeach
</div>

                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Terms and Conditions -->
                    <div class="animate__animated animate__fadeInUp" style="animation-delay: 0.7s;">
                        <label class="custom-checkbox flex items-start cursor-pointer group">
                            <input type="checkbox" id="terms" name="terms" required class="mr-3">
                            <span class="checkmark-box"></span>
                            <span class="ml-3 text-sm text-white/70 group-hover:text-white transition-colors">
                                I agree to the 
                                <a href="#" class="text-blue-300 hover:text-white hover:underline transition-colors">Terms of Service</a> 
                                and 
                                <a href="#" class="text-blue-300 hover:text-white hover:underline transition-colors">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 animate__animated animate__fadeInUp" style="animation-delay: 0.8s;">
                        <button 
                            type="submit" 
                            id="submitBtn"
                            class="w-full py-3 px-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 relative overflow-hidden group">
                            <span class="relative z-10 flex items-center justify-center">
                                <i class="fas fa-user-plus mr-2"></i>
                                Create Account
                            </span>
                            <div class="absolute inset-0 shimmer"></div>
                        </button>
                    </div>
                </form>

                <!-- Login Link -->
                <div class="mt-8 text-center animate__animated animate__fadeIn" style="animation-delay: 1s;">
                    <p class="text-sm text-white/60">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="text-blue-300 hover:text-white font-semibold transition-colors group">
                            Sign in here 
                            <i class="fas fa-arrow-right ml-1 transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </p>
                </div>
            </div>

            <!-- Features Banner -->
            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-3 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm">
                    <i class="fas fa-shield-alt text-green-400 text-lg mb-1"></i>
                    <p class="text-xs text-white/70">Secure & Encrypted</p>
                </div>
                <div class="text-center p-3 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm">
                    <i class="fas fa-bolt text-yellow-400 text-lg mb-1"></i>
                    <p class="text-xs text-white/70">Fast Processing</p>
                </div>
                <div class="text-center p-3 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm">
                    <i class="fas fa-headset text-blue-400 text-lg mb-1"></i>
                    <p class="text-xs text-white/70">24/7 Support</p>
                </div>
                <div class="text-center p-3 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm">
                    <i class="fas fa-chart-line text-purple-400 text-lg mb-1"></i>
                    <p class="text-xs text-white/70">AI Powered</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            let strengthText = 'Weak';
            let strengthClass = 'strength-weak';
            
            // Criteria checks
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/\d/)) strength++;
            if (password.match(/[^a-zA-Z\d]/)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    strengthText = 'Weak';
                    strengthClass = 'strength-weak';
                    break;
                case 2:
                    strengthText = 'Moderate';
                    strengthClass = 'strength-moderate';
                    break;
                case 3:
                    strengthText = 'Good';
                    strengthClass = 'strength-good';
                    break;
                case 4:
                    strengthText = 'Strong';
                    strengthClass = 'strength-strong';
                    break;
            }
            
            const strengthBar = document.getElementById('passwordStrength');
            const strengthTextElement = document.getElementById('passwordStrengthText');
            
            if (strengthBar && strengthTextElement) {
                strengthBar.className = 'password-strength ' + strengthClass;
                strengthTextElement.textContent = `Password strength: ${strengthText}`;
                
                // Color coding
                if (strength >= 3) {
                    strengthTextElement.className = 'text-xs text-green-400';
                } else if (strength === 2) {
                    strengthTextElement.className = 'text-xs text-yellow-400';
                } else {
                    strengthTextElement.className = 'text-xs text-red-400';
                }
            }
            
            validatePasswordMatch();
        }
        
        // Toggle password visibility
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(fieldId + 'EyeIcon');
            
            if (field.type === 'password') {
                field.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
        
        // Validate password match
        function validatePasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            const errorElement = document.getElementById('passwordMatchError');
            
            if (confirmPassword && password !== confirmPassword) {
                errorElement.classList.remove('hidden');
                return false;
            } else {
                errorElement.classList.add('hidden');
                return true;
            }
        }
        
        // Validate email
        function validateEmail(input) {
            const email = input.value;
            const isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            const icon = document.getElementById('emailValid');
            
            if (email && isValid) {
                icon.classList.remove('hidden');
                input.classList.add('border-green-500/50');
                input.classList.remove('border-red-500/50');
            } else if (email) {
                icon.classList.add('hidden');
                input.classList.add('border-red-500/50');
                input.classList.remove('border-green-500/50');
            } else {
                icon.classList.add('hidden');
                input.classList.remove('border-green-500/50', 'border-red-500/50');
            }
        }
        
        // Validate name
        function validateName(input) {
            const name = input.value.trim();
            const isValid = name.length >= 2;
            const icon = document.getElementById('nameValid');
            
            if (name && isValid) {
                icon.classList.remove('hidden');
                input.classList.add('border-green-500/50');
                input.classList.remove('border-red-500/50');
            } else if (name) {
                icon.classList.add('hidden');
                input.classList.add('border-red-500/50');
                input.classList.remove('border-green-500/50');
            } else {
                icon.classList.add('hidden');
                input.classList.remove('border-green-500/50', 'border-red-500/50');
            }
        }
        
        // Plan selection animation
        document.querySelectorAll('.plan-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.plan-card').forEach(c => {
                    c.classList.remove('selected');
                });
                this.classList.add('selected');
                
                // Add bounce animation
                this.style.animation = 'none';
                setTimeout(() => {
                    this.style.animation = 'bounceIn 0.5s';
                }, 10);
            });
        });
        
        // Form submission
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            if (!validatePasswordMatch()) {
                e.preventDefault();
                document.getElementById('passwordMatchError').classList.remove('hidden');
                document.getElementById('passwordMatchError').classList.add('animate__animated', 'animate__headShake');
                setTimeout(() => {
                    document.getElementById('passwordMatchError').classList.remove('animate__animated', 'animate__headShake');
                }, 1000);
                return false;
            }
            
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating Account...';
            submitBtn.disabled = true;
            
            // Add loading animation to form
            this.classList.add('opacity-50');
        });
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effects
            document.querySelectorAll('.input-field').forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('scale-105');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('scale-105');
                });
            });
            
            // Animate form elements sequentially
            const formElements = document.querySelectorAll('.animate__animated');
            formElements.forEach((el, index) => {
                setTimeout(() => {
                    el.classList.add('animate__fadeInUp');
                }, index * 100);
            });
            
            // Auto-focus name field
            document.getElementById('name').focus();
        });
        
        // Responsive adjustments
        function adjustLayout() {
            const width = window.innerWidth;
            const container = document.querySelector('.register-container');
            
            if (width < 768) {
                container.classList.add('overflow-y-auto');
                document.querySelector('.glass-card').classList.add('max-h-[90vh]');
            } else {
                container.classList.remove('overflow-y-auto');
                document.querySelector('.glass-card').classList.remove('max-h-[90vh]');
            }
        }
        
        window.addEventListener('resize', adjustLayout);
        window.addEventListener('load', adjustLayout);
    </script>
</body>
</html>