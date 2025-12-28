<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AI Legal Assistant</title>
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
            background: linear-gradient(-45deg, #667eea, #764ba2, #4f46e5, #7e22ce);
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
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.1);
        }
        
        .dark .glass-card {
            background: rgba(15, 23, 42, 0.3);
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        .input-glow:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
        }
        
        .btn-shimmer {
            position: relative;
            overflow: hidden;
        }
        
        .btn-shimmer::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                rgba(255,255,255,0) 30%,
                rgba(255,255,255,0.1) 50%,
                rgba(255,255,255,0) 70%
            );
            transform: rotate(30deg);
            transition: all 0.6s ease;
        }
        
        .btn-shimmer:hover::after {
            left: 100%;
        }
        
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            pointer-events: none;
        }
        
        .ripple {
            animation: ripple 2s linear infinite;
        }
        
        @keyframes ripple {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }
        
        .slide-in {
            animation: slideIn 0.8s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .typewriter {
            overflow: hidden;
            border-right: 2px solid #fff;
            white-space: nowrap;
            animation: typing 3.5s steps(40, end), blink-caret 0.75s step-end infinite;
        }
        
        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }
        
        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: #fff; }
        }
        
        /* Responsive adjustments */
        @media (max-width: 640px) {
            .login-container {
                padding: 1rem;
            }
            .glass-card {
                padding: 1.5rem !important;
            }
        }
        
        @media (max-height: 600px) {
            body {
                overflow-y: auto;
            }
            .login-container {
                padding-top: 2rem;
                padding-bottom: 2rem;
            }
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.5);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.8);
        }
    </style>
</head>
<body class="h-full">
    <!-- Animated Background Particles -->
    <div id="particles-container"></div>
    
    <!-- Floating Elements -->
    <div class="floating-element absolute top-10 left-10 w-24 h-24 bg-gradient-to-br from-blue-400/20 to-purple-400/20 rounded-full blur-xl"></div>
    <div class="floating-element absolute bottom-10 right-10 w-32 h-32 bg-gradient-to-tr from-purple-400/20 to-pink-400/20 rounded-full blur-xl" style="animation-delay: -3s;"></div>
    <div class="floating-element absolute top-1/4 right-1/4 w-16 h-16 bg-gradient-to-br from-indigo-400/20 to-blue-400/20 rounded-full blur-xl" style="animation-delay: -2s;"></div>
    
    <div class="login-container min-h-screen flex items-center justify-center p-4 relative">
        <div class="w-full max-w-md z-10">
            <!-- Logo with Ripple Effect -->
            <div class="text-center mb-8 relative">
                <div class="relative inline-block">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl blur-lg opacity-50 animate-pulse"></div>
                    <div class="ripple absolute inset-0 border-2 border-blue-400/30 rounded-2xl"></div>
                    <div class="relative w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-2xl transform hover:scale-105 transition-transform duration-300">
                        <i class="fas fa-balance-scale text-white text-3xl"></i>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-white mt-4 mb-2 tracking-tight slide-in">AI Legal Assistant</h1>
                <p class="text-blue-100/80 font-light slide-in" style="animation-delay: 0.2s;">Secure Access to Legal Intelligence</p>
            </div>

            <!-- Login Card -->
            <div class="glass-card rounded-3xl p-8 slide-in" style="animation-delay: 0.4s;">
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-xl backdrop-blur-sm transform transition-all duration-500 hover:scale-[1.02]">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-red-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-red-200">
                                    {{ $errors->first() }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 rounded-xl backdrop-blur-sm animate__animated animate__fadeIn">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-green-200">
                                    {{ session('status') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-medium text-white/90">
                            <i class="fas fa-envelope mr-2 text-blue-300"></i>Email Address
                        </label>
                        <div class="relative group">
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl blur opacity-20 group-hover:opacity-40 transition duration-300"></div>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus
                                class="relative w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 text-white placeholder-white/50 transition-all duration-300 input-glow"
                                placeholder="legal@example.com">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="block text-sm font-medium text-white/90">
                            <i class="fas fa-lock mr-2 text-blue-300"></i>Password
                        </label>
                        <div class="relative group">
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl blur opacity-20 group-hover:opacity-40 transition duration-300"></div>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required 
                                class="relative w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/50 text-white placeholder-white/50 transition-all duration-300 input-glow"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white/50 hover:text-white transition-colors">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer group">
                            <div class="relative">
                                <input 
                                    type="checkbox" 
                                    name="remember" 
                                    class="sr-only peer">
                                <div class="w-5 h-5 bg-white/10 border border-white/20 rounded peer-checked:bg-blue-500 peer-checked:border-blue-500 transition-all duration-300 group-hover:border-blue-400"></div>
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                            </div>
                            <span class="ml-3 text-sm text-white/70 group-hover:text-white transition-colors">Remember me</span>
                        </label>
                        
                        <a href="#" class="text-sm text-blue-300 hover:text-white transition-colors hover:underline decoration-blue-300/50 group">
                            <i class="fas fa-key mr-1 group-hover:animate-pulse"></i>Forgot password?
                        </a>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full py-3 px-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 btn-shimmer">
                        <i class="fas fa-sign-in-alt mr-2 animate-pulse"></i>Sign In
                    </button>
                </form>

                <!-- Social Login -->
                <div class="mt-8 pt-6 border-t border-white/10">
                    <p class="text-center text-sm text-white/60 mb-4">Or continue with</p>
                    <div class="grid grid-cols-3 gap-3">
                        <a href="{{ route('auth.google') }}" class="p-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-center transition-all duration-300 hover:scale-105 group">
                            <i class="fab fa-google text-red-400 group-hover:text-red-300"></i>
                        </a>
                        <a href="{{ route('auth.microsoft') }}" class="p-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-center transition-all duration-300 hover:scale-105 group">
                            <i class="fab fa-microsoft text-blue-400 group-hover:text-blue-300"></i>
                        </a>
                        <a href="{{ route('auth.apple') }}" class="p-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl text-center transition-all duration-300 hover:scale-105 group">
                            <i class="fab fa-apple text-white group-hover:text-gray-200"></i>
                        </a>
                    </div>
                </div>

                <!-- Register Link -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-white/60">
                        New to AI Legal Assistant? 
                        <a href="{{ route('register') }}" class="text-blue-300 hover:text-white font-semibold transition-colors group">
                            Create account 
                            <i class="fas fa-arrow-right ml-1 transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </p>
                </div>
            </div>

            <!-- Security Badge -->
            <div class="mt-6 text-center slide-in" style="animation-delay: 0.6s;">
                <div class="inline-flex items-center space-x-2 text-xs text-white/40">
                    <i class="fas fa-shield-alt text-green-400 animate-pulse"></i>
                    <span>Enterprise-grade security • 256-bit encryption • GDPR compliant</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Check system preference for dark mode
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // Create animated particles
        function createParticles() {
            const container = document.getElementById('particles-container');
            const colors = ['rgba(99, 102, 241, 0.3)', 'rgba(139, 92, 246, 0.3)', 'rgba(168, 85, 247, 0.3)'];
            
            for (let i = 0; i < 15; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                const size = Math.random() * 100 + 50;
                const posX = Math.random() * 100;
                const posY = Math.random() * 100;
                const color = colors[Math.floor(Math.random() * colors.length)];
                const duration = Math.random() * 10 + 10;
                const delay = Math.random() * 5;
                
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${posX}%`;
                particle.style.top = `${posY}%`;
                particle.style.background = color;
                particle.style.animation = `float ${duration}s ease-in-out infinite`;
                particle.style.animationDelay = `${delay}s`;
                
                container.appendChild(particle);
            }
        }

        // Input focus effects
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('scale-105');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('scale-105');
            });
        });

        // Form submission animation
        document.querySelector('form').addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Signing in...';
            button.disabled = true;
        });

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
            
            // Add hover effects to cards
            const cards = document.querySelectorAll('.glass-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-5px)';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0)';
                });
            });
            
            // Add ripple effect to buttons
            document.querySelectorAll('button, a').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
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
                    
                    this.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 600);
                });
            });
        });

        // Responsive adjustments
        function adjustLayout() {
            const width = window.innerWidth;
            const height = window.innerHeight;
            
            if (height < 600) {
                document.querySelector('.login-container').classList.add('py-8');
            }
            
            if (width < 640) {
                document.querySelectorAll('.glass-card').forEach(card => {
                    card.classList.add('p-6');
                });
            }
        }

        window.addEventListener('resize', adjustLayout);
        window.addEventListener('load', adjustLayout);
    </script>
</body>
</html>