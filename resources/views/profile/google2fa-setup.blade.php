@extends('layouts.app')

@section('title', 'Setup Google Authenticator - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-6">Setup Google Authenticator</h1>

        <div class="card p-6">
            <div class="space-y-6">
                <!-- Step 1: Download App -->
                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">
                        1
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Download Google Authenticator</h3>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            Install Google Authenticator on your mobile device from the App Store or Google Play Store.
                        </p>
                        <div class="flex space-x-4 mt-3">
                            <a href="#" class="flex items-center space-x-2 text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300">
                                <i class="fab fa-apple"></i>
                                <span>App Store</span>
                            </a>
                            <a href="#" class="flex items-center space-x-2 text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300">
                                <i class="fab fa-google-play"></i>
                                <span>Google Play</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Scan QR Code -->
                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">
                        2
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Scan QR Code</h3>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            Open Google Authenticator and scan the QR code below.
                        </p>
                        <div class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 inline-block">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $qrCodeUrl }}" 
                                 alt="QR Code" class="w-48 h-48">
                        </div>
                    </div>
                </div>

                <!-- Step 3: Manual Setup -->
                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">
                        3
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Manual Setup (Optional)</h3>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            If you can't scan the QR code, enter this secret key manually:
                        </p>
                        <div class="mt-3 p-3 bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <code class="text-sm font-mono text-gray-900 dark:text-white">{{ $secret }}</code>
                            <button onclick="copyToClipboard('{{ $secret }}')" class="ml-2 text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Verify -->
                <div class="flex items-start space-x-4">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">
                        4
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Enter Verification Code</h3>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            Enter the 6-digit code from Google Authenticator to verify setup.
                        </p>
                        <form method="POST" action="{{ route('profile.enable-google-2fa') }}" class="mt-4">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="verification_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Verification Code
                                    </label>
                                    <input type="text" 
                                           name="verification_code" 
                                           maxlength="6" 
                                           pattern="[0-9]*" 
                                           inputmode="numeric"
                                           class="w-full max-w-xs border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-center text-lg font-mono"
                                           required 
                                           autofocus
                                           placeholder="000000">
                                </div>
                                <div class="flex space-x-3">
                                    <button type="submit" class="btn-primary">
                                        <i class="fas fa-check-circle mr-2"></i>Verify & Enable
                                    </button>
                                    <a href="{{ route('profile.index') }}" class="btn-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('Secret key copied to clipboard!', 'success');
    }, function(err) {
        showToast('Failed to copy secret key.', 'error');
    });
}

// Auto-submit when 6 digits are entered
document.querySelector('input[name="verification_code"]').addEventListener('input', function(e) {
    if (this.value.length === 6) {
        this.form.submit();
    }
});
</script>
@endsection