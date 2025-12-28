@extends('layouts.app')

@section('title', 'Dashboard - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <!-- Welcome Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 mobile-space-y-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                Welcome back, {{ Auth::user()->name }}!
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your legal cases, clients, and documents efficiently</p>
        </div>
        <div class="flex space-x-3 mobile-full-width mobile-justify-center">
            <button onclick="openCaseModal()" class="btn-primary mobile-full-width">
                <i class="fas fa-plus mr-2"></i>Add New Case
            </button>
            <button onclick="generateReport()" class="btn-secondary mobile-full-width">
                <i class="fas fa-chart-bar mr-2"></i>Generate Report
            </button>
        </div>
    </div>

    <!-- Subscription Info -->
    @if($subscriptionInfo)
    <div class="card p-4 lg:p-6 mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Subscription Status</h3>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    Plan: <span class="font-medium">{{ $subscriptionInfo['plan_name'] }}</span> • 
                    Status: <span class="font-medium {{ $subscriptionInfo['status'] == 'active' ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                        {{ ucfirst($subscriptionInfo['status']) }}
                    </span>
                </p>
                @if($subscriptionInfo['is_trial'] && $subscriptionInfo['trial_ends_at'])
                <p class="text-sm text-yellow-600 dark:text-yellow-400 mt-2">
                    <i class="fas fa-clock mr-1"></i>Trial ends: {{ $subscriptionInfo['trial_ends_at'] }}
                </p>
                @endif
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ $subscriptionInfo['remaining_cases'] }}
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Cases remaining</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
        <!-- Total Cases -->
        <div class="stat-card card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Cases</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $caseStats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-folder text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                @if($caseStats['new_this_month'] > 0)
                <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                    <i class="fas fa-arrow-up mr-1"></i>{{ $caseStats['new_this_month'] }} this month
                </span>
                @else
                <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">No new cases this month</span>
                @endif
            </div>
        </div>

        <!-- Pending Cases -->
        <div class="stat-card card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Cases</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $caseStats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                @if($caseStats['pending'] > 0)
                <span class="text-yellow-600 dark:text-yellow-400 text-sm font-medium">Requires attention</span>
                @else
                <span class="text-green-600 dark:text-green-400 text-sm font-medium">All cases up to date</span>
                @endif
            </div>
        </div>

        <!-- Upcoming Hearings -->
        <div class="stat-card card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Upcoming Hearings</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $upcomingHearings->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                @if($upcomingHearings->count() > 0)
                @php
                    $nextHearing = $upcomingHearings->first();
                @endphp
                <span class="text-purple-600 dark:text-purple-400 text-sm font-medium">
                    Next: {{ $nextHearing->hearing_date->format('M d') }}
                </span>
                @else
                <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">No upcoming hearings</span>
                @endif
            </div>
        </div>

        <!-- Clients -->
        <div class="stat-card card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Clients</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $clientStats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-teal-100 dark:bg-teal-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-teal-600 dark:text-teal-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                @if($clientStats['new_this_month'] > 0)
                <span class="text-teal-600 dark:text-teal-400 text-sm font-medium">
                    <i class="fas fa-plus mr-1"></i>{{ $clientStats['new_this_month'] }} new this month
                </span>
                @else
                <span class="text-gray-600 dark:text-gray-400 text-sm font-medium">No new clients this month</span>
                @endif
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

        <!-- Case Distribution Chart -->
        <div class="card p-4 lg:p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Case Status Distribution</h2>
            <div class="h-64">
                <canvas id="caseDistributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Data Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
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
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-folder text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $case->case_title }}</p>
                        <div class="flex items-center space-x-3 mt-1">
                            <span class="text-xs px-2 py-1 rounded-full {{ $case->case_status == 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : ($case->case_status == 'closed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400') }}">
                                {{ ucfirst(str_replace('_', ' ', $case->case_status)) }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $case->caseType->name ?? 'N/A' }}
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

        <!-- Upcoming Hearings -->
        <div class="card p-4 lg:p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Upcoming Hearings</h2>
                <a href="{{ route('hearings.index') }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
                    View all
                </a>
            </div>
            <div class="space-y-4">
                @forelse($upcomingHearings as $hearing)
                <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-day text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $hearing->case->case_title ?? 'N/A' }}
                        </p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs font-medium text-gray-900 dark:text-white">
                                <i class="far fa-clock mr-1"></i>
                                {{ $hearing->hearing_date->format('M d, Y - h:i A') }}
                            </span>
                            @if($hearing->hearing_date->isToday())
                            <span class="text-xs px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 rounded-full">
                                Today
                            </span>
                            @elseif($hearing->hearing_date->isTomorrow())
                            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 rounded-full">
                                Tomorrow
                            </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            {{ $hearing->venue ?? 'Venue not specified' }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-calendar-alt text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-600 dark:text-gray-400">No upcoming hearings</p>
                </div>
                @endforelse
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
            type: 'bar',
            data: {
                labels: @json($monthlyCases['labels']),
                datasets: [{
                    label: 'Cases',
                    data: @json($monthlyCases['data']),
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
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

        // Case Distribution Chart
        const distributionCtx = document.getElementById('caseDistributionChart').getContext('2d');
        new Chart(distributionCtx, {
            type: 'doughnut',
            data: {
                labels: @json($caseDistribution['labels']),
                datasets: [{
                    data: @json($caseDistribution['data']),
                    backgroundColor: @json($caseDistribution['colors']),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection