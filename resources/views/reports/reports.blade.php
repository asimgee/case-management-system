@extends('layouts.app')

@section('title', 'Reports & Analytics - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 mobile-space-y-4">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Reports & Analytics</h1>
        <div class="flex space-x-3 mobile-full-width mobile-justify-center">
            <button onclick="exportPDF()" class="btn-secondary mobile-full-width">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </button>
            <button onclick="exportExcel()" class="btn-primary mobile-full-width">
                <i class="fas fa-file-excel mr-2"></i>Export Excel
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
        <div class="stat-card card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">$247,500</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                    <i class="fas fa-arrow-up mr-1"></i>15% from last month
                </span>
            </div>
        </div>

        <div class="stat-card card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Billable Hours</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">1,247</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-blue-600 dark:text-blue-400 text-sm font-medium">
                    <i class="fas fa-arrow-up mr-1"></i>8% from last month
                </span>
            </div>
        </div>

        <div class="stat-card card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Success Rate</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">89%</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-trophy text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                    <i class="fas fa-arrow-up mr-1"></i>3% from last month
                </span>
            </div>
        </div>

        <div class="stat-card card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Client Satisfaction</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">4.8/5</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-star text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 dark:text-green-400 text-sm font-medium">Excellent rating</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-8">
        <div class="card p-4 lg:p-6 fixed-height-400">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Revenue</h2>
            <div class="chart-container mobile-chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        <div class="card p-4 lg:p-6 fixed-height-400">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Case Types Distribution</h2>
            <div class="chart-container mobile-chart-container">
                <canvas id="caseTypesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Detailed Reports -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <!-- Performance Metrics -->
        <div class="card p-4 lg:p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Metrics</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Case Resolution Time</span>
                    <span class="text-sm font-bold text-green-600 dark:text-green-400">28 days</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Client Acquisition Rate</span>
                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">12 new/month</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Average Case Value</span>
                    <span class="text-sm font-bold text-purple-600 dark:text-purple-400">$8,250</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Case Load per Lawyer</span>
                    <span class="text-sm font-bold text-yellow-600 dark:text-yellow-400">18 cases</span>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="card p-4 lg:p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activities</h2>
            <div class="space-y-3">
                <div class="flex items-center space-x-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-plus text-blue-600 dark:text-blue-400 text-xs"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">New case registered</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">2 hours ago</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600 dark:text-green-400 text-xs"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Case #2024-015 closed</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">5 hours ago</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                    <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar text-purple-600 dark:text-purple-400 text-xs"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Invoice generated</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">1 day ago</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-plus text-yellow-600 dark:text-yellow-400 text-xs"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">New client onboarded</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">2 days ago</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Generation Options -->
    <div class="card p-4 lg:p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Generate Custom Report</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <select class="border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                <option>Select Time Period</option>
                <option>Last 7 Days</option>
                <option>Last 30 Days</option>
                <option>Last Quarter</option>
                <option>Last Year</option>
            </select>
            <select class="border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                <option>Select Report Type</option>
                <option>Financial Report</option>
                <option>Case Performance</option>
                <option>Client Analytics</option>
                <option>Team Performance</option>
            </select>
            <select class="border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                <option>Select Format</option>
                <option>PDF Document</option>
                <option>Excel Spreadsheet</option>
                <option>CSV File</option>
                <option>HTML Report</option>
            </select>
            <button onclick="generateCustomReport()" class="btn-primary">
                <i class="fas fa-chart-line mr-2"></i>Generate
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function generateCustomReport() {
        showToast('Custom report generation started...', 'info');
        setTimeout(() => {
            showToast('Custom report generated successfully!', 'success');
        }, 2000);
    }
</script>
@endpush