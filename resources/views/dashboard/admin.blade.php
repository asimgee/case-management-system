@extends('layouts.app')

@section('title', 'Admin Dashboard - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <!-- Admin Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 mobile-space-y-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">System overview and analytics</p>
        </div>
        <div class="flex space-x-3 mobile-full-width mobile-justify-center">
            <button onclick="window.location='{{ route('admin.backup') }}'" class="btn-primary mobile-full-width">
                <i class="fas fa-database mr-2"></i>Backup System
            </button>
            <button onclick="window.location='{{ route('admin.settings') }}'" class="btn-secondary mobile-full-width">
                <i class="fas fa-cog mr-2"></i>Settings
            </button>
        </div>
    </div>

    <!-- System Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
        <!-- Total Users -->
        <div class="stat-card card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_users'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                @if($stats['new_users_this_month'] > 0)
                <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                    <i class="fas fa-arrow-up mr-1"></i>{{ $stats['new_users_this_month'] }} this month
                </span>
                @else
                <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">No new users this month</span>
                @endif
            </div>
        </div>

        <!-- Total Cases -->
        <div class="stat-card card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Cases</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_cases'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-gavel text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                @if($stats['new_cases_this_month'] > 0)
                <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                    <i class="fas fa-arrow-up mr-1"></i>{{ $stats['new_cases_this_month'] }} new
                </span>
                @else
                <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">No new cases this month</span>
                @endif
            </div>
        </div>

        <!-- Active Cases -->
        <div class="stat-card card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Cases</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_cases'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-yellow-600 dark:text-yellow-400 text-sm font-medium">
                    {{ round(($stats['active_cases'] / max($stats['total_cases'], 1)) * 100, 1) }}% of total
                </span>
            </div>
        </div>

        <!-- Active Subscriptions -->
        <div class="stat-card card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Subscriptions</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_subscriptions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-credit-card text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-purple-600 dark:text-purple-400 text-sm font-medium">
                    {{ round(($stats['active_subscriptions'] / max($stats['total_users'], 1)) * 100, 1) }}% conversion
                </span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Monthly Cases Chart -->
        <div class="card p-4 lg:p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Monthly Cases</h2>
            <div class="h-64">
                <canvas id="monthlyCasesChart"></canvas>
            </div>
        </div>

        <!-- User Growth Chart -->
        <div class="card p-4 lg:p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">User Growth</h2>
            <div class="h-64">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Data Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Users -->
        <div class="card p-4 lg:p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Users</h2>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                    View all
                </a>
            </div>
            <div class="space-y-4">
                @forelse($recentUsers as $user)
                <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                        <div class="flex items-center space-x-3 mt-1">
                            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 rounded-full">
                                {{ ucfirst($user->role) }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $user->email }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            Joined {{ $user->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-users text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-600 dark:text-gray-400">No users yet</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Cases -->
        <div class="card p-4 lg:p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Cases</h2>
                <a href="{{ route('cases.index') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                    View all
                </a>
            </div>
            <div class="space-y-4">
                @forelse($recentCases as $case)
                <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-gavel text-green-600 dark:text-green-400"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $case->case_title }}</p>
                        <div class="flex items-center space-x-3 mt-1">
                            <span class="text-xs px-2 py-1 rounded-full 
                                {{ $case->case_status == 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 
                                   ($case->case_status == 'closed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400') }}">
                                {{ ucfirst(str_replace('_', ' ', $case->case_status)) }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $case->user->name ?? 'Unknown' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            Created {{ $case->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-folder-open text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-600 dark:text-gray-400">No cases yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="card p-4 lg:p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">System Health</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Storage -->
            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-medium text-gray-900 dark:text-white">Storage</h3>
                    <span class="text-xs px-2 py-1 rounded-full 
                        {{ $systemHealth['storage']['status'] == 'healthy' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 
                           ($systemHealth['storage']['status'] == 'warning' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                        {{ ucfirst($systemHealth['storage']['status']) }}
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Used:</span>
                        <span class="font-medium">{{ $systemHealth['storage']['used'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Free:</span>
                        <span class="font-medium">{{ $systemHealth['storage']['free'] }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $systemHealth['storage']['percentage'] }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Database -->
            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-medium text-gray-900 dark:text-white">Database</h3>
                    <span class="text-xs px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded-full">
                        {{ ucfirst($systemHealth['database']['status']) }}
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Size:</span>
                        <span class="font-medium">{{ $systemHealth['database']['size'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Tables:</span>
                        <span class="font-medium">{{ $systemHealth['database']['tables'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Application -->
            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-medium text-gray-900 dark:text-white">Application</h3>
                    <span class="text-xs px-2 py-1 
                        {{ $systemHealth['application']['status'] == 'production' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' }} rounded-full">
                        {{ ucfirst($systemHealth['application']['status']) }}
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Version:</span>
                        <span class="font-medium">{{ $systemHealth['application']['version'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Environment:</span>
                        <span class="font-medium">{{ $systemHealth['application']['environment'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Debug Mode:</span>
                        <span class="font-medium {{ $systemHealth['application']['debug_mode'] ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400' }}">
                            {{ $systemHealth['application']['debug_mode'] ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card p-4 lg:p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Recent Activity</h2>
        <div class="space-y-4">
            @forelse($recentActivity as $activity)
            <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <div class="w-10 h-10 rounded-full flex items-center justify-center
                    {{ $activity['color'] == 'blue' ? 'bg-blue-100 dark:bg-blue-900/30' : 
                       ($activity['color'] == 'green' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-purple-100 dark:bg-purple-900/30') }}">
                    <i class="{{ $activity['icon'] }} 
                        {{ $activity['color'] == 'blue' ? 'text-blue-600 dark:text-blue-400' : 
                           ($activity['color'] == 'green' ? 'text-green-600 dark:text-green-400' : 'text-purple-600 dark:text-purple-400') }}">
                    </i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity['title'] }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $activity['description'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $activity['time'] }}</p>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <i class="fas fa-history text-gray-400 text-3xl mb-3"></i>
                <p class="text-gray-600 dark:text-gray-400">No recent activity</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Cases Chart
        const monthlyCtx = document.getElementById('monthlyCasesChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: @json($monthlyCases['labels']),
                datasets: [{
                    label: 'Cases',
                    data: @json($monthlyCases['data']),
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // User Growth Chart
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(userGrowthCtx, {
            type: 'bar',
            data: {
                labels: @json($userGrowth['labels']),
                datasets: [{
                    label: 'New Users',
                    data: @json($userGrowth['data']),
                    backgroundColor: 'rgba(16, 185, 129, 0.5)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection