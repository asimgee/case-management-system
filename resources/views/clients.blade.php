@extends('layouts.app')

@section('title', 'Clients - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 mobile-space-y-4">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Clients</h1>
        <button onclick="openClientModal()" class="btn-primary mobile-full-width">
            <i class="fas fa-plus mr-2"></i>Add New Client
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <div class="card p-6 card-hover">
            <div class="flex items-center space-x-4 mb-4">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Ccircle cx='24' cy='24' r='24' fill='%23EF4444'/%3E%3Ctext x='24' y='30' text-anchor='middle' fill='white' font-family='Arial' font-size='18' font-weight='bold'%3EJS%3C/text%3E%3C/svg%3E" alt="Client" class="w-12 h-12 rounded-full">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">John Smith</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Individual Client</p>
                </div>
            </div>
            <div class="space-y-2 mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-phone mr-2"></i>+1 (555) 123-4567
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-envelope mr-2"></i>john.smith@email.com
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-folder mr-2"></i>3 Active Cases
                </p>
            </div>
            <button class="w-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 py-2 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                <i class="fas fa-user mr-2"></i>View Profile
            </button>
        </div>

        <div class="card p-6 card-hover">
            <div class="flex items-center space-x-4 mb-4">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Ccircle cx='24' cy='24' r='24' fill='%2310B981'/%3E%3Ctext x='24' y='30' text-anchor='middle' fill='white' font-family='Arial' font-size='18' font-weight='bold'%3EMJ%3C/text%3E%3C/svg%3E" alt="Client" class="w-12 h-12 rounded-full">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Mary Johnson</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Corporate Client</p>
                </div>
            </div>
            <div class="space-y-2 mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-phone mr-2"></i>+1 (555) 987-6543
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-envelope mr-2"></i>mary.johnson@corp.com
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-folder mr-2"></i>7 Active Cases
                </p>
            </div>
            <button class="w-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 py-2 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                <i class="fas fa-user mr-2"></i>View Profile
            </button>
        </div>

        <div class="card p-6 card-hover">
            <div class="flex items-center space-x-4 mb-4">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Ccircle cx='24' cy='24' r='24' fill='%23F59E0B'/%3E%3Ctext x='24' y='30' text-anchor='middle' fill='white' font-family='Arial' font-size='18' font-weight='bold'%3ERW%3C/text%3E%3C/svg%3E" alt="Client" class="w-12 h-12 rounded-full">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Robert Wilson</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Individual Client</p>
                </div>
            </div>
            <div class="space-y-2 mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-phone mr-2"></i>+1 (555) 456-7890
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-envelope mr-2"></i>robert.wilson@email.com
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-folder mr-2"></i>2 Active Cases
                </p>
            </div>
            <button class="w-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 py-2 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                <i class="fas fa-user mr-2"></i>View Profile
            </button>
        </div>

        <div class="card p-6 card-hover">
            <div class="flex items-center space-x-4 mb-4">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Ccircle cx='24' cy='24' r='24' fill='%238B5CF6'/%3E%3Ctext x='24' y='30' text-anchor='middle' fill='white' font-family='Arial' font-size='18' font-weight='bold'%3EAS%3C/text%3E%3C/svg%3E" alt="Client" class="w-12 h-12 rounded-full">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Alice Brown</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Individual Client</p>
                </div>
            </div>
            <div class="space-y-2 mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-phone mr-2"></i>+1 (555) 234-5678
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-envelope mr-2"></i>alice.brown@email.com
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-folder mr-2"></i>1 Active Case
                </p>
            </div>
            <button class="w-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 py-2 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                <i class="fas fa-user mr-2"></i>View Profile
            </button>
        </div>

        <div class="card p-6 card-hover">
            <div class="flex items-center space-x-4 mb-4">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Ccircle cx='24' cy='24' r='24' fill='%230EA5E9'/%3E%3Ctext x='24' y='30' text-anchor='middle' fill='white' font-family='Arial' font-size='18' font-weight='bold'%3EDC%3C/text%3E%3C/svg%3E" alt="Client" class="w-12 h-12 rounded-full">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">David Clark</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Corporate Client</p>
                </div>
            </div>
            <div class="space-y-2 mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-phone mr-2"></i>+1 (555) 345-6789
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-envelope mr-2"></i>david.clark@corp.com
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-folder mr-2"></i>5 Active Cases
                </p>
            </div>
            <button class="w-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 py-2 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                <i class="fas fa-user mr-2"></i>View Profile
            </button>
        </div>

        <div class="card p-6 card-hover">
            <div class="flex items-center space-x-4 mb-4">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='48' height='48' viewBox='0 0 48 48'%3E%3Ccircle cx='24' cy='24' r='24' fill='%23EC4899'/%3E%3Ctext x='24' y='30' text-anchor='middle' fill='white' font-family='Arial' font-size='18' font-weight='bold'%3ESM%3C/text%3E%3C/svg%3E" alt="Client" class="w-12 h-12 rounded-full">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sarah Miller</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Individual Client</p>
                </div>
            </div>
            <div class="space-y-2 mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-phone mr-2"></i>+1 (555) 567-8901
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-envelope mr-2"></i>sarah.miller@email.com
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-folder mr-2"></i>4 Active Cases
                </p>
            </div>
            <button class="w-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 py-2 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                <i class="fas fa-user mr-2"></i>View Profile
            </button>
        </div>
    </div>
</div>
@endsection