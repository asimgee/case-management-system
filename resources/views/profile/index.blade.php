@extends('layouts.app')

@section('title', 'Profile Settings - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-6">Profile Settings</h1>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 dark:text-green-400 mr-3"></i>
                <div class="text-sm text-green-600 dark:text-green-400">
                    {{ session('success') }}
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 mr-3"></i>
                <div class="text-sm text-red-600 dark:text-red-400">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Profile Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Profile Card -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Profile Information</h2>
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="flex items-center space-x-6 mb-6">
                        <div class="relative">
                            @if(auth()->user()->profile_image)
                                <img src="{{ asset('storage/profile-images/' . auth()->user()->profile_image) }}" 
                                     alt="Profile" class="w-24 h-24 rounded-2xl object-cover border-2 border-gray-200 dark:border-gray-700">
                            @else
                                <div class="w-24 h-24 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center text-white text-2xl font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <label for="profile_image" class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full shadow-lg hover:bg-blue-700 transition-colors cursor-pointer">
                                <i class="fas fa-camera text-sm"></i>
                                <input type="file" id="profile_image" name="profile_image" class="hidden" accept="image/*" onchange="previewImage(this)">
                            </label>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ auth()->user()->email }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ auth()->user()->isAdmin() ? 'Administrator' : 'User' }}
                            </p>
                            <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                <i class="fas fa-circle text-xs mr-1"></i>
                                Online
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" 
                                   class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" 
                                   class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save mr-2"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Update -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Update Password</h2>
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Password</label>
                            <input type="password" id="current_password" name="current_password" 
                                   class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                                <input type="password" id="new_password" name="new_password" 
                                       class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                @error('new_password')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" 
                                       class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                            </div>
                        </div>
                        <div class="flex justify-end pt-4">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-key mr-2"></i>Update Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="space-y-6" id="security">
            <!-- Two-Factor Authentication -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Two-Factor Authentication</h2>
                <div class="space-y-4">
                    <!-- Email 2FA -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                                <i class="fas fa-envelope text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">Email Authentication</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Receive code via email</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ auth()->user()->two_factor_enabled ? route('profile.disable-email-2fa') : route('profile.enable-email-2fa') }}">
                            @csrf
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" {{ auth()->user()->two_factor_enabled ? 'checked' : '' }} 
                                       class="sr-only peer" onchange="this.form.submit()">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            </label>
                        </form>
                    </div>

                    <!-- Google Authenticator -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                                <i class="fab fa-google text-green-600 dark:text-green-400"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">Google Authenticator</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Use authenticator app</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if(auth()->user()->google2fa_enabled)
                                <form method="POST" action="{{ route('profile.disable-google-2fa') }}">
                                    @csrf
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('profile.google2fa-setup') }}" class="btn-primary text-sm py-2 px-3">
                                    Setup
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-shield-alt text-yellow-600 dark:text-yellow-400 mt-0.5"></i>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-400">Security Tip</h4>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                Enable two-factor authentication for enhanced security.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Status -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Security Status</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Password Strength</span>
                        <span class="text-sm font-medium text-green-600 dark:text-green-400">Strong</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">2FA Enabled</span>
                        <span class="text-sm font-medium {{ (auth()->user()->two_factor_enabled || auth()->user()->google2fa_enabled) ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ (auth()->user()->two_factor_enabled || auth()->user()->google2fa_enabled) ? 'Yes' : 'No' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Last Login</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            @if(auth()->user()->loginSecurity && auth()->user()->loginSecurity->last_login_at)
                                {{ auth()->user()->loginSecurity->last_login_at->diffForHumans() }}
                            @else
                                Never
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Account Created</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ auth()->user()->created_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
                <div class="space-y-2">
                    <a href="{{ route('profile.index') }}" class="flex items-center space-x-3 p-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-user-edit text-blue-500"></i>
                        <span>Edit Profile</span>
                    </a>
                    <a href="{{ route('profile.index') }}#security" class="flex items-center space-x-3 p-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-shield-alt text-green-500"></i>
                        <span>Security Settings</span>
                    </a>
                    <a href="{{ route('settings') }}" class="flex items-center space-x-3 p-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-cogs text-purple-500"></i>
                        <span>App Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Create a preview image
            const preview = document.createElement('img');
            preview.src = e.target.result;
            preview.className = 'w-24 h-24 rounded-2xl object-cover border-2 border-gray-200 dark:border-gray-700';
            
            // Replace the existing image or initial
            const parent = input.closest('.relative');
            const existingImage = parent.querySelector('img, div');
            if (existingImage) {
                parent.replaceChild(preview, existingImage);
            }
            
            // Re-add the camera button
            const cameraLabel = document.createElement('label');
            cameraLabel.className = 'absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full shadow-lg hover:bg-blue-700 transition-colors cursor-pointer';
            cameraLabel.innerHTML = '<i class="fas fa-camera text-sm"></i>';
            
            // Create new file input
            const newInput = input.cloneNode(true);
            newInput.onchange = function() { previewImage(this); };
            cameraLabel.appendChild(newInput);
            parent.appendChild(cameraLabel);
            
            // Remove the old input
            input.remove();
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection