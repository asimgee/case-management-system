@extends('layouts.app')

@section('title', 'Dashboard - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 mobile-space-y-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Welcome to AI Legal Assistant</h1>
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

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 lg:gap-6 mb-8">
        <div class="stat-card card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Cases</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">247</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-folder text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                    <i class="fas fa-arrow-up mr-1"></i>12% from last month
                </span>
            </div>
        </div>

        <div class="stat-card card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Cases</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">89</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-yellow-600 dark:text-yellow-400 text-sm font-medium">Requires attention</span>
            </div>
        </div>

        <div class="stat-card card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Closed Cases</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">158</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                    <i class="fas fa-arrow-up mr-1"></i>8% success rate
                </span>
            </div>
        </div>

        <div class="stat-card card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Upcoming Hearings</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">23</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-purple-600 dark:text-purple-400 text-sm font-medium">Next: Tomorrow 2PM</span>
            </div>
        </div>

        <div class="stat-card card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Documents</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">1,247</p>
                </div>
                <div class="w-12 h-12 bg-teal-100 dark:bg-teal-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-alt text-teal-600 dark:text-teal-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-teal-600 dark:text-teal-400 text-sm font-medium">
                    <i class="fas fa-plus mr-1"></i>45 this week
                </span>
            </div>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="card p-4 lg:p-6 mb-8 fixed-height-400">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Case Analytics</h2>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 h-80">
            <div class="chart-container mobile-chart-container">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-4">Cases per Month</h3>
                <canvas id="barChart"></canvas>
            </div>
            <div class="chart-container mobile-chart-container">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-4">Case Status Overview</h3>
                <canvas id="pieChart"></canvas>
            </div>
            <div class="chart-container mobile-chart-container">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-4">Hearing Trends</h3>
                <canvas id="lineChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card p-4 lg:p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Recent Activity</h2>
        <div class="space-y-4">
            <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-folder text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">New case filed: Smith vs. Johnson</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">2 hours ago</p>
                </div>
            </div>
            <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-file-upload text-green-600 dark:text-green-400"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Document uploaded for Case #2024-001</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">4 hours ago</p>
                </div>
            </div>
            <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-day text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Hearing scheduled for tomorrow at 2:00 PM</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">6 hours ago</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection