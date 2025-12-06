<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - AI Legal Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .register-container {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
        }
        
        .register-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-height: 95vh;
            overflow-y: auto;
        }
        
        .dark .register-card {
            background: rgba(15, 23, 42, 0.98);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .password-strength {
            height: 3px;
            border-radius: 2px;
            transition: all 0.3s ease;
            margin-top: 2px;
        }

        .strength-weak { background-color: #ef4444; width: 25%; }
        .strength-moderate { background-color: #f59e0b; width: 50%; }
        .strength-good { background-color: #10b981; width: 75%; }
        .strength-strong { background-color: #059669; width: 100%; }

        .social-login-btn {
            transition: all 0.2s ease;
            border: 1px solid #e5e7eb;
            font-size: 0.875rem;
            padding: 10px 12px;
        }

        .social-login-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dark .social-login-btn {
            border-color: #374151;
            background: #1f2937;
        }

        .form-input {
            padding: 10px 12px;
            font-size: 0.875rem;
            border-radius: 10px;
        }

        .form-label {
            font-size: 0.875rem;
            margin-bottom: 6px;
        }

        .compact-space > * + * {
            margin-top: 12px;
        }

        .compact-space-sm > * + * {
            margin-top: 8px;
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .register-card::-webkit-scrollbar {
            display: none;
        }
        
        /* Hide scrollbar for IE, Edge and Firefox */
        .register-card {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }

        @media (max-height: 700px) {
            .register-container {
                transform: scale(0.9);
                transform-origin: center;
            }
        }

        @media (max-height: 600px) {
            .register-container {
                transform: scale(0.8);
                transform-origin: center;
                max-height: 95vh;
                overflow: hidden;
            }
        }
    </style>
</head>
<body class="h-full">
    <div class="register-container">
        <div class="register-card p-5">
            <!-- Header -->
            <div class="text-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mx-auto mb-2 shadow-md">
                    <i class="fas fa-balance-scale text-white text-lg"></i>
                </div>
                <h1 class="text-lg font-bold text-gray-900 dark:text-white">Create Account</h1>
                <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">Join thousands of legal professionals</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-2 mb-3">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 mr-2 text-sm"></i>
                        <div class="text-xs text-red-600 dark:text-red-400">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Social Login Buttons -->
            <div class="compact-space-sm mb-3">
                <a href="{{ route('auth.google') }}" class="social-login-btn w-full flex items-center justify-center rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-medium no-underline">
                    <i class="fab fa-google text-red-500 mr-2 text-sm"></i>
                    <span class="text-xs">Sign up with Google</span>
                </a>
                
                <a href="{{ route('auth.microsoft') }}" class="social-login-btn w-full flex items-center justify-center rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-medium no-underline">
                    <i class="fab fa-microsoft text-blue-500 mr-2 text-sm"></i>
                    <span class="text-xs">Sign up with Microsoft</span>
                </a>
                
                <a href="{{ route('auth.apple') }}" class="social-login-btn w-full flex items-center justify-center rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-medium no-underline">
                    <i class="fab fa-apple text-gray-800 dark:text-white mr-2 text-sm"></i>
                    <span class="text-xs">Sign up with Apple</span>
                </a>
            </div>

            <!-- Divider -->
            <div class="relative my-3">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">Or sign up with email</span>
                </div>
            </div>

            <!-- Registration Form -->
            <form method="POST" action="{{ route('register') }}" class="compact-space">
                @csrf
                
                <div>
                    <label for="name" class="form-label block text-gray-700 dark:text-gray-300">
                        Full Name
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required 
                        autofocus
                        class="form-input w-full border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white transition-colors"
                        placeholder="Enter your full name">
                </div>

                <div>
                    <label for="email" class="form-label block text-gray-700 dark:text-gray-300">
                        Email Address
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        class="form-input w-full border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white transition-colors"
                        placeholder="Enter your email">
                </div>

                <div>
                    <label for="password" class="form-label block text-gray-700 dark:text-gray-300">
                        Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            class="form-input w-full pr-10 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white transition-colors"
                            placeholder="Create password"
                            onkeyup="checkPasswordStrength(this.value)">
                        <button type="button" 
                                onclick="togglePasswordVisibility('password')"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 text-sm">
                            <i class="fas fa-eye" id="passwordEyeIcon"></i>
                        </button>
                    </div>
                    
                    <!-- Password Strength Meter -->
                    <div class="mt-1">
                        <div class="password-strength strength-weak" id="passwordStrength"></div>
                        <div class="text-xs text-gray-500 dark:text-gray-400" id="passwordStrengthText">
                            Password strength: Weak
                        </div>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="form-label block text-gray-700 dark:text-gray-300">
                        Confirm Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            required 
                            class="form-input w-full pr-10 border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white transition-colors"
                            placeholder="Confirm password">
                        <button type="button" 
                                onclick="togglePasswordVisibility('password_confirmation')"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 text-sm">
                            <i class="fas fa-eye" id="confirmPasswordEyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="terms" 
                        name="terms" 
                        required
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 w-3.5 h-3.5">
                    <label for="terms" class="ml-2 text-xs text-gray-600 dark:text-gray-400">
                        I agree to the 
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300">Terms</a>
                        and 
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300">Privacy Policy</a>
                    </label>
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-2.5 px-4 rounded-lg font-medium hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-md hover:shadow-lg text-sm">
                    <i class="fas fa-user-plus mr-1.5"></i>Create Account
                </button>
            </form>

            <div class="mt-4 text-center">
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 font-medium">
                        Sign in here
                    </a>
                </p>
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

        function checkPasswordStrength(password) {
            let strength = 0;
            let strengthText = 'Weak';
            let strengthClass = 'strength-weak';

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
                strengthTextElement.textContent = 'Password strength: ' + strengthText;
                
                // Change text color based on strength
                if (strength >= 3) {
                    strengthTextElement.className = 'text-xs text-green-600 dark:text-green-400';
                } else if (strength === 2) {
                    strengthTextElement.className = 'text-xs text-yellow-600 dark:text-yellow-400';
                } else {
                    strengthTextElement.className = 'text-xs text-red-600 dark:text-red-400';
                }
            }
        }

        // Initialize password strength
        document.addEventListener('DOMContentLoaded', function() {
            checkPasswordStrength('');
        });

        // Auto-scale for very small screens
        function adjustScale() {
            const container = document.querySelector('.register-container');
            const viewportHeight = window.innerHeight;
            
            if (viewportHeight < 600) {
                container.style.transform = 'scale(0.75)';
            } else if (viewportHeight < 700) {
                container.style.transform = 'scale(0.85)';
            } else {
                container.style.transform = 'scale(1)';
            }
        }

        window.addEventListener('resize', adjustScale);
        adjustScale(); // Initial call
    </script>
</body>
</html>