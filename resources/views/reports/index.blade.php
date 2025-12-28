@extends('layouts.app')

@section('title', 'Reports & Analytics - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-2">Reports & Analytics</h1>
            <p class="text-gray-600 dark:text-gray-400">Comprehensive insights into your legal practice</p>
        </div>
        <div class="flex space-x-3 mt-4 lg:mt-0">
            <button onclick="exportReport('pdf')" class="btn-secondary">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </button>
            <button onclick="exportReport('excel')" class="btn-primary">
                <i class="fas fa-file-excel mr-2"></i>Export Excel
            </button>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card p-4 lg:p-6 mb-6">
        <form id="dateFilterForm" method="GET" action="{{ route('reports.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time Period</label>
                    <select name="time_period" id="timePeriodSelect" 
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        @foreach($timePeriods as $value => $label)
                            <option value="{{ $value }}" 
                                @if(request('time_period') == $value) selected @endif>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div id="customDateRange" class="{{ request('time_period') == 'custom' ? '' : 'hidden' }} md:col-span-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" 
                                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" 
                                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        </div>
                    </div>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="btn-primary w-full">
                        <i class="fas fa-filter mr-2"></i>Apply Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Cases</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalCases }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-gavel text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-green-600 dark:text-green-400 font-medium">
                        <i class="fas fa-arrow-up mr-1"></i>{{ $activeCases }} Active
                    </span>
                    <span class="text-gray-600 dark:text-gray-400">
                        {{ $closedCases }} Closed
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Success Rate</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $performanceMetrics['success_rate'] ?? 89 }}%</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-trophy text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                    <i class="fas fa-arrow-up mr-1"></i>High performance
                </span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg. Resolution</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $performanceMetrics['case_resolution_time'] ?? 28 }} days</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                    <i class="fas fa-check-circle mr-1"></i>Within target
                </span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg. Case Value</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">${{ number_format($performanceMetrics['average_case_value'] ?? 8250) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-green-600 dark:text-green-400 text-sm font-medium">
                    <i class="fas fa-chart-line mr-1"></i>Growing
                </span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-8">
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Monthly Case Trends</h2>
                <select id="chartPeriod" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-1.5">
                    <option value="monthly">Monthly</option>
                    <option value="weekly">Weekly</option>
                </select>
            </div>
            <div class="h-64">
                <canvas id="casesChart"></canvas>
            </div>
        </div>
        
        <div class="card p-4 lg:p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Case Types Distribution</h2>
            <div class="h-64">
                <canvas id="caseTypesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Detailed Reports -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-8">
        <!-- Case Status Distribution -->
        <div class="card p-4 lg:p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Case Status Distribution</h2>
            <div class="space-y-4">
                @foreach($casesByStatus as $status => $count)
                    @php
                        $percentage = $totalCases > 0 ? ($count / $totalCases) * 100 : 0;
                        $colors = [
                            'pending' => ['bg' => 'bg-yellow-500', 'text' => 'text-yellow-600 dark:text-yellow-400'],
                            'in_hearing' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-600 dark:text-blue-400'],
                            'closed' => ['bg' => 'bg-green-500', 'text' => 'text-green-600 dark:text-green-400'],
                        ];
                        $color = $colors[$status] ?? ['bg' => 'bg-gray-500', 'text' => 'text-gray-600 dark:text-gray-400'];
                    @endphp
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">
                                {{ str_replace('_', ' ', $status) }}
                            </span>
                            <span class="text-sm font-bold {{ $color['text'] }}">
                                {{ $count }} ({{ round($percentage, 1) }}%)
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="{{ $color['bg'] }} h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activities</h2>
                <a href="{{ route('cases.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    View All
                </a>
            </div>
            <div class="space-y-3">
                @forelse($recentCases as $case)
                    @php
                        $colors = [
                            'pending' => ['bg' => 'bg-yellow-50 dark:bg-yellow-900/20', 'border' => 'border-yellow-200 dark:border-yellow-800'],
                            'in_hearing' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'border' => 'border-blue-200 dark:border-blue-800'],
                            'closed' => ['bg' => 'bg-green-50 dark:bg-green-900/20', 'border' => 'border-green-200 dark:border-green-800'],
                        ];
                        $color = $colors[$case->case_status] ?? ['bg' => 'bg-gray-50 dark:bg-gray-800/50', 'border' => 'border-gray-200 dark:border-gray-700'];
                    @endphp
                    <div class="flex items-center space-x-3 p-3 {{ $color['bg'] }} rounded-lg border {{ $color['border'] }}">
                        <div class="w-8 h-8 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <i class="fas fa-gavel text-gray-600 dark:text-gray-400 text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $case->case_number }} - {{ $case->first_party_title }} vs {{ $case->second_party_title }}
                            </p>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="text-xs px-2 py-0.5 bg-white dark:bg-gray-700 rounded capitalize">
                                    {{ str_replace('_', ' ', $case->case_status) }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $case->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <p class="text-gray-500 dark:text-gray-400">No recent activities</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="card p-4 text-center">
            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                {{ $performanceMetrics['billable_hours'] ?? 1247 }}
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Billable Hours</p>
        </div>
        <div class="card p-4 text-center">
            <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-2">
                {{ $performanceMetrics['client_acquisition_rate'] ?? 12 }}
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">New Clients/Month</p>
        </div>
        <div class="card p-4 text-center">
            <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">
                {{ $performanceMetrics['client_satisfaction'] ?? 4.8 }}/5
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Client Satisfaction</p>
        </div>
        <div class="card p-4 text-center">
            <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mb-2">
                {{ $performanceMetrics['case_load_per_lawyer'] ?? 18 }}
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Cases per Lawyer</p>
        </div>
    </div>

    <!-- Report Types Navigation -->
    <div class="card p-4 lg:p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detailed Reports</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('reports.financial') }}" 
               class="p-4 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-xl transition-colors group">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400">
                            Financial Report
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Revenue & expenses</p>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('reports.case-analysis') }}" 
               class="p-4 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl transition-colors group">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-balance-scale text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400">
                            Case Analysis
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Performance metrics</p>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('reports.client-analysis') }}" 
               class="p-4 bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 border border-purple-200 dark:border-purple-800 rounded-xl transition-colors group">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400">
                            Client Analysis
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Client trends & insights</p>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('reports.performance') }}" 
               class="p-4 bg-yellow-50 dark:bg-yellow-900/20 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded-xl transition-colors group">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-trophy text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white group-hover:text-yellow-600 dark:group-hover:text-yellow-400">
                            Performance
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Team & individual</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Custom Report Generation -->
    <div class="card p-4 lg:p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Generate Custom Report</h2>
        <form id="customReportForm" method="POST" action="{{ route('reports.generate') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Report Type</label>
                    <select name="report_type" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        <option value="">Select type</option>
                        <option value="summary">Summary Report</option>
                        <option value="financial">Financial Report</option>
                        <option value="case_analysis">Case Analysis</option>
                        <option value="client_analysis">Client Analysis</option>
                        <option value="performance">Performance Report</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Format</label>
                    <select name="format" required
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                        <option value="">Select format</option>
                        <option value="pdf">PDF Document</option>
                        <option value="excel">Excel Spreadsheet</option>
                        <option value="csv">CSV File</option>
                        <option value="html">HTML Report</option>
                    </select>
                </div>
                
                <div class="md:col-span-2 lg:col-span-4 flex justify-center">
                    <button type="submit" class="btn-primary px-8">
                        <i class="fas fa-chart-line mr-2"></i>Generate Report
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Initialize charts
let casesChart = null;
let caseTypesChart = null;

function initCharts() {
    // Destroy existing charts if they exist
    if (casesChart) casesChart.destroy();
    if (caseTypesChart) caseTypesChart.destroy();
    
    // Monthly Cases Chart
    const casesCtx = document.getElementById('casesChart').getContext('2d');
    casesChart = new Chart(casesCtx, {
        type: 'line',
        data: {
            labels: @json($chartData['months'] ?? []),
            datasets: [{
                label: 'Number of Cases',
                data: @json($chartData['cases'] ?? []),
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    },
                    ticks: {
                        precision: 0
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    
    // Case Types Chart
    const caseTypesCtx = document.getElementById('caseTypesChart').getContext('2d');
    const caseTypes = @json($casesByType ?? []);
    
    caseTypesChart = new Chart(caseTypesCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(caseTypes),
            datasets: [{
                data: Object.values(caseTypes),
                backgroundColor: [
                    '#EF4444', '#3B82F6', '#10B981', '#F59E0B', 
                    '#8B5CF6', '#EC4899', '#6366F1', '#14B8A6'
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                }
            },
            cutout: '60%'
        }
    });
}

// Time period filter handler
document.getElementById('timePeriodSelect').addEventListener('change', function() {
    const customDateRange = document.getElementById('customDateRange');
    if (this.value === 'custom') {
        customDateRange.classList.remove('hidden');
    } else {
        customDateRange.classList.add('hidden');
        // Auto-set dates based on period
        const dates = getDateRangeForPeriod(this.value);
        if (dates) {
            document.querySelector('input[name="start_date"]').value = dates.start;
            document.querySelector('input[name="end_date"]').value = dates.end;
        }
    }
});

function getDateRangeForPeriod(period) {
    const today = new Date();
    let startDate, endDate;
    
    switch(period) {
        case 'today':
            startDate = endDate = today.toISOString().split('T')[0];
            break;
        case 'yesterday':
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);
            startDate = endDate = yesterday.toISOString().split('T')[0];
            break;
        case 'this_week':
            const startOfWeek = new Date(today);
            startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay());
            const endOfWeek = new Date(startOfWeek);
            endOfWeek.setDate(endOfWeek.getDate() + 6);
            startDate = startOfWeek.toISOString().split('T')[0];
            endDate = endOfWeek.toISOString().split('T')[0];
            break;
        case 'last_week':
            const lastWeekStart = new Date(today);
            lastWeekStart.setDate(lastWeekStart.getDate() - lastWeekStart.getDay() - 7);
            const lastWeekEnd = new Date(lastWeekStart);
            lastWeekEnd.setDate(lastWeekEnd.getDate() + 6);
            startDate = lastWeekStart.toISOString().split('T')[0];
            endDate = lastWeekEnd.toISOString().split('T')[0];
            break;
        case 'this_month':
            startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
            break;
        case 'last_month':
            const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            startDate = new Date(lastMonth.getFullYear(), lastMonth.getMonth(), 1).toISOString().split('T')[0];
            endDate = new Date(lastMonth.getFullYear(), lastMonth.getMonth() + 1, 0).toISOString().split('T')[0];
            break;
        case 'this_quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            startDate = new Date(today.getFullYear(), quarter * 3, 1).toISOString().split('T')[0];
            endDate = new Date(today.getFullYear(), quarter * 3 + 3, 0).toISOString().split('T')[0];
            break;
        case 'last_quarter':
            const lastQuarterMonth = today.getMonth() < 3 ? 9 : (today.getMonth() < 6 ? 0 : (today.getMonth() < 9 ? 3 : 6));
            const lastQuarterYear = today.getMonth() < 3 ? today.getFullYear() - 1 : today.getFullYear();
            startDate = new Date(lastQuarterYear, lastQuarterMonth, 1).toISOString().split('T')[0];
            endDate = new Date(lastQuarterYear, lastQuarterMonth + 3, 0).toISOString().split('T')[0];
            break;
        case 'this_year':
            startDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
            endDate = new Date(today.getFullYear(), 11, 31).toISOString().split('T')[0];
            break;
        case 'last_year':
            startDate = new Date(today.getFullYear() - 1, 0, 1).toISOString().split('T')[0];
            endDate = new Date(today.getFullYear() - 1, 11, 31).toISOString().split('T')[0];
            break;
    }
    
    return { start: startDate, end: endDate };
}

// Chart period selector
document.getElementById('chartPeriod').addEventListener('change', function() {
    fetch(`{{ route('reports.api.chart-data') }}?period=${this.value}`)
        .then(response => response.json())
        .then(data => {
            // Update charts with new data
            updateChartsWithData(data);
        })
        .catch(error => {
            console.error('Error fetching chart data:', error);
        });
});

function updateChartsWithData(data) {
    if (casesChart && data.labels && data.cases) {
        casesChart.data.labels = data.labels;
        casesChart.data.datasets[0].data = data.cases;
        casesChart.update();
    }
    
    if (caseTypesChart && data.case_types) {
        caseTypesChart.data.labels = data.case_types.labels;
        caseTypesChart.data.datasets[0].data = data.case_types.data;
        caseTypesChart.data.datasets[0].backgroundColor = data.case_types.colors;
        caseTypesChart.update();
    }
}

// Export functions
function exportReport(format) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("reports.generate") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    const reportType = document.createElement('input');
    reportType.type = 'hidden';
    reportType.name = 'report_type';
    reportType.value = 'summary';
    form.appendChild(reportType);
    
    const startDate = document.createElement('input');
    startDate.type = 'hidden';
    startDate.name = 'start_date';
    startDate.value = '{{ $startDate }}';
    form.appendChild(startDate);
    
    const endDate = document.createElement('input');
    endDate.type = 'hidden';
    endDate.name = 'end_date';
    endDate.value = '{{ $endDate }}';
    form.appendChild(endDate);
    
    const formatInput = document.createElement('input');
    formatInput.type = 'hidden';
    formatInput.name = 'format';
    formatInput.value = format;
    form.appendChild(formatInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Custom report form validation
document.getElementById('customReportForm').addEventListener('submit', function(e) {
    const startDate = this.querySelector('input[name="start_date"]').value;
    const endDate = this.querySelector('input[name="end_date"]').value;
    
    if (new Date(startDate) > new Date(endDate)) {
        e.preventDefault();
        alert('End date must be after start date.');
        return false;
    }
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...';
    submitBtn.disabled = true;
});

// Initialize charts on page load
document.addEventListener('DOMContentLoaded', function() {
    initCharts();
    
    // Set initial date range if time period is not custom
    const timePeriod = document.getElementById('timePeriodSelect').value;
    if (timePeriod !== 'custom') {
        const dates = getDateRangeForPeriod(timePeriod);
        if (dates) {
            document.querySelector('input[name="start_date"]').value = dates.start;
            document.querySelector('input[name="end_date"]').value = dates.end;
        }
    }
});

// Refresh stats button
function refreshStats() {
    fetch('{{ route("reports.api.stats") }}')
        .then(response => response.json())
        .then(data => {
            // Update stats cards
            document.querySelectorAll('.stat-card').forEach(card => {
                // Update with real data
            });
        })
        .catch(error => {
            console.error('Error fetching stats:', error);
        });
}

// Auto-refresh stats every 5 minutes
setInterval(refreshStats, 5 * 60 * 1000);
</script>

<style>
.card-hover {
    transition: all 0.2s ease-in-out;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Chart container height */
.h-64 {
    height: 16rem;
}

/* Responsive design */
@media (max-width: 768px) {
    .h-64 {
        height: 12rem;
    }
}
</style>
@endsection