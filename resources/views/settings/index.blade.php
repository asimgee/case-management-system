@extends('layouts.app')

@section('title', 'Settings - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-6">Application Settings</h1>

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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Application Settings -->
        <div class="lg:col-span-2 space-y-6">
            <!-- General Settings -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">General Settings</h2>
                <form class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="firm_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Law Firm Name</label>
                            <input type="text" id="firm_name" value="{{ auth()->user()->name }} Law Firm" class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Timezone</label>
                            <select id="timezone" class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                <option value="UTC" selected>UTC</option>
                                <option value="EST">Eastern Time (EST)</option>
                                <option value="PST">Pacific Time (PST)</option>
                                <option value="CST">Central Time (CST)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="date_format" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date Format</label>
                            <select id="date_format" class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                <option value="Y-m-d" selected>YYYY-MM-DD</option>
                                <option value="m/d/Y">MM/DD/YYYY</option>
                                <option value="d/m/Y">DD/MM/YYYY</option>
                            </select>
                        </div>
                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Language</label>
                            <select id="language" class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                                <option value="en" selected>English</option>
                                <option value="es">Spanish</option>
                                <option value="fr">French</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="button" onclick="saveGeneralSettings()" class="btn-primary">
                            <i class="fas fa-save mr-2"></i>Save Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notification Settings -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Notification Settings</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-xl">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">Case Updates</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Get notified about case status changes</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-xl">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">Hearing Reminders</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Reminders for upcoming hearings</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-xl">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">Document Uploads</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Notifications for new document uploads</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-xl">
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">Client Messages</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Notifications for client communications</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preferences Sidebar -->
        <div class="space-y-6">
            <!-- Theme & Preferences -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Theme & Preferences</h2>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Theme</label>
                        <div class="grid grid-cols-2 gap-3">
                            <button onclick="setTheme('light')" class="flex flex-col items-center p-4 border-2 {{ request()->cookie('theme', 'light') === 'light' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600' }} rounded-xl hover:border-blue-500 transition-colors">
                                <div class="w-full h-20 bg-white border border-gray-300 rounded-lg mb-2"></div>
                                <span class="text-sm font-medium">Light</span>
                            </button>
                            <button onclick="setTheme('dark')" class="flex flex-col items-center p-4 border-2 {{ request()->cookie('theme') === 'dark' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600' }} rounded-xl hover:border-blue-500 transition-colors">
                                <div class="w-full h-20 bg-gray-900 border border-gray-700 rounded-lg mb-2"></div>
                                <span class="text-sm font-medium">Dark</span>
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Email Notifications</label>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Case Updates</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Security Alerts</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Newsletter</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Data Management</label>
                        <div class="space-y-3">
                            <button onclick="createBackup()" class="w-full btn-primary">
                                <i class="fas fa-database mr-2"></i>Create Backup
                            </button>
                            <button onclick="restoreBackup()" class="w-full btn-secondary">
                                <i class="fas fa-undo mr-2"></i>Restore Backup
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">System Information</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">App Version</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">v2.1.0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Last Update</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ now()->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">User Since</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Account Type</span>
                        <span class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Professional' }}</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button onclick="clearCache()" class="w-full btn-secondary">
                        <i class="fas fa-broom mr-2"></i>Clear Cache
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function saveGeneralSettings() {
        showToast('Settings saved successfully!', 'success');
    }

    function clearCache() {
        showToast('Cache cleared successfully!', 'success');
    }

    function createBackup() {
        showToast('Backup created successfully!', 'success');
    }

    function restoreBackup() {
        showToast('Backup restored successfully!', 'success');
    }

    // Set theme with cookie
    function setTheme(theme) {
        document.documentElement.classList.toggle('dark', theme === 'dark');
        
        // Set cookie for 30 days
        document.cookie = `theme=${theme}; path=/; max-age=${30 * 24 * 60 * 60}; SameSite=Lax`;
        
        showToast(`Theme changed to ${theme} mode`, 'success');
    }
</script>
@endpush