<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Verification - AI Legal Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .verification-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .dark .verification-card {
            background: rgba(15, 23, 42, 0.95);
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        .code-input {
            width: 100%;
            height: 60px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            background: white;
        }
        
        .dark .code-input {
            background: #1f2937;
            border-color: #4b5563;
            color: white;
        }
        
        .code-input:focus {
            border-color: #3b82f6;
            ring: 2px;
        }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4">
    <div class="verification-card rounded-2xl shadow-2xl w-full max-w-md p-8">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-shield-alt text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                @if($type === 'email')
                    Email Verification
                @else
                    Google Authenticator
                @endif
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                @if($type === 'email')
                    Enter the 6-digit code sent to your email
                @else
                    Enter the code from Google Authenticator app
                @endif
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 mr-3"></i>
                    <div class="text-sm text-red-600 dark:text-red-400">
                        {{ $errors->first() }}
                    </div>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 mr-3"></i>
                    <div class="text-sm text-green-600 dark:text-green-400">
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('2fa.verify.submit') }}" class="space-y-6">
            @csrf
            
            <div class="text-center">
                <label for="verification_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                    @if($type === 'email')
                        Enter 6-digit code
                    @else
                        Enter Google Authenticator code
                    @endif
                </label>
                <input type="text" 
                       name="verification_code" 
                       maxlength="6" 
                       pattern="[0-9]*" 
                       inputmode="numeric" 
                       class="code-input" 
                       required 
                       autofocus
                       placeholder="@if($type === 'email') 123456 @else 000000 @endif">
                
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                    @if($type === 'email')
                        We've sent a verification code to your email address.
                    @else
                        Open your Google Authenticator app to get the verification code.
                    @endif
                </p>
            </div>

            <button 
                type="submit" 
                class="w-full bg-gradient-to-r from-green-500 to-blue-600 text-white py-3 px-4 rounded-xl font-medium hover:from-green-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all shadow-lg hover:shadow-xl">
                <i class="fas fa-check-circle mr-2"></i>Verify Code
            </button>
        </form>

        @if($type === 'email')
        <div class="mt-6 text-center">
            <form method="POST" action="{{ route('2fa.resend') }}">
                @csrf
                <button type="submit" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300 transition-colors">
                    <i class="fas fa-redo mr-1"></i>Resend verification code
                </button>
            </form>
        </div>
        @endif

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i>Back to login
            </a>
        </div>
    </div>

    <script>
        // Auto-submit when 6 digits are entered
        document.querySelector('input[name="verification_code"]').addEventListener('input', function(e) {
            if (this.value.length === 6) {
                this.form.submit();
            }
        });

        // Check system preference for dark mode
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</body>
</html>