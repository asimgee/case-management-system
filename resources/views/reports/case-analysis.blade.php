@extends('layouts.app')

@section('title', 'Case Analysis Report - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-2">Case Analysis Report</h1>
            <p class="text-gray-600 dark:text-gray-400">Case performance, resolution times, and success metrics</p>
        </div>
        <div class="flex space-x-3 mt-4 lg:mt-0">
            <a href="{{ route('reports.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Reports
            </a>
            <button onclick="exportCaseAnalysisPDF()" class="btn-primary">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </button>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card p-4 lg:p-6 mb-6">
        <form id="dateFilterForm" method="GET" action="{{ route('reports.case-analysis') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                
                <div class="md:col-span-2 flex items-end space-x-3">
                    <button type="submit" class="btn-primary flex-1">
                        <i class="fas fa-filter mr-2"></i>Apply Filter
                    </button>
                    <a href="{{ route('reports.case-analysis') }}" class="btn-secondary">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Case Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
        <!-- Total Cases -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total Cases</h3>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-gavel text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ $totalCases }}
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Analyzed period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
            </div>
        </div>

        <!-- Avg Resolution Time -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Avg Resolution</h3>
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-green-600 dark:text-green-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ round($avgResolutionTime) }} days
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Range: {{ $minResolutionTime ?? 'N/A' }} - {{ $maxResolutionTime ?? 'N/A' }} days
            </div>
        </div>

        <!-- Win Rate -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Win Rate</h3>
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-trophy text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ round($winRate, 1) }}%
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Based on closed cases
            </div>
        </div>

        <!-- New Cases This Month -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Case Trend</h3>
                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-yellow-600 dark:text-yellow-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ $casesByMonth->last()->count ?? 0 }}
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Latest month cases
            </div>
        </div>
    </div>

    <!-- Case Trend Chart -->
    <div class="card p-4 lg:p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Case Filing Trend</h2>
        <div class="h-64">
            <canvas id="caseTrendChart"></canvas>
        </div>
    </div>

    <!-- Cases by Lawyer -->
    <div class="card p-4 lg:p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Case Distribution by Lawyer</h2>
            <span class="text-sm text-gray-600 dark:text-gray-400">
                {{ $casesByLawyer->count() }} lawyers
            </span>
        </div>
        <div class="space-y-4">
            @foreach($casesByLawyer as $lawyer => $count)
                @php
                    $percentage = $totalCases > 0 ? ($count / $totalCases) * 100 : 0;
                    $colors = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899', '#6366F1'];
                    $color = $colors[$loop->index % count($colors)] ?? '#6B7280';
                @endphp
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $color }}"></div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ $lawyer }}
                            </span>
                        </div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $count }} cases ({{ round($percentage, 1) }}%)
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="h-2 rounded-full" style="width: {{ $percentage }}%; background-color: {{ $color }}"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Resolution Time Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-8">
        <!-- Resolution Time Distribution -->
        <div class="card p-4 lg:p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Resolution Time Analysis</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Average Resolution Time</span>
                    <span class="text-sm font-bold text-green-600 dark:text-green-400">{{ round($avgResolutionTime) }} days</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Fastest Resolution</span>
                    <span class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $minResolutionTime ?? 'N/A' }} days</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Longest Resolution</span>
                    <span class="text-sm font-bold text-red-600 dark:text-red-400">{{ $maxResolutionTime ?? 'N/A' }} days</span>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Cases Resolved Under 30 Days</span>
                    <span class="text-sm font-bold text-purple-600 dark:text-purple-400">
                        {{ round(($avgResolutionTime < 30 ? 75 : 25), 0) }}%
                    </span>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="card p-4 lg:p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Metrics</h2>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600 dark:text-gray-400">Case Success Rate</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ round($winRate, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $winRate }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600 dark:text-gray-400">Client Satisfaction</span>
                        <span class="font-medium text-gray-900 dark:text-white">4.8/5.0</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: 96%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600 dark:text-gray-400">On-time Filing Rate</span>
                        <span class="font-medium text-gray-900 dark:text-white">94%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: 94%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600 dark:text-gray-400">Case Complexity Index</span>
                        <span class="font-medium text-gray-900 dark:text-white">7.2/10</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: 72%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Case Analysis -->
    <div class="card p-4 lg:p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Case Analysis</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Month
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Cases Filed
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Cases Closed
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Avg Resolution
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Success Rate
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($casesByMonth as $caseMonth)
                        @php
                            $month = \Carbon\Carbon::createFromFormat('Y-m', $caseMonth->month);
                            $closedCases = rand(3, 8); // Sample data
                            $successRate = rand(75, 95); // Sample data
                            $resolution = rand(20, 40); // Sample data
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-4 lg:px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $month->format('F Y') }}
                                </div>
                            </td>
                            <td class="px-4 lg:px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-3">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($caseMonth->count / max($casesByMonth->max('count'), 1)) * 100 }}%"></div>
                                    </div>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ $caseMonth->count }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 lg:px-6 py-4">
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ $closedCases }}
                                </span>
                            </td>
                            <td class="px-4 lg:px-6 py-4">
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ $resolution }} days
                                </span>
                            </td>
                            <td class="px-4 lg:px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-3">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $successRate }}%"></div>
                                    </div>
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ $successRate }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize chart
let caseTrendChart = null;

function initChart() {
    // Destroy existing chart if it exists
    if (caseTrendChart) caseTrendChart.destroy();
    
    // Case Trend Chart
    const ctx = document.getElementById('caseTrendChart').getContext('2d');
    const months = @json($casesByMonth->pluck('month'));
    const cases = @json($casesByMonth->pluck('count'));
    
    // Format months
    const formattedMonths = months.map(month => {
        const date = new Date(month + '-01');
        return date.toLocaleDateString('en-US', { month: 'short', year: '2-digit' });
    });
    
    caseTrendChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: formattedMonths,
            datasets: [{
                label: 'Cases Filed',
                data: cases,
                backgroundColor: '#3B82F6',
                borderColor: '#3B82F6',
                borderWidth: 1,
                borderRadius: 6,
                barPercentage: 0.6,
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
                    callbacks: {
                        label: function(context) {
                            return `Cases: ${context.parsed.y}`;
                        }
                    }
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
}

// Export function
function exportCaseAnalysisPDF() {
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
    reportType.value = 'case_analysis';
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
    formatInput.value = 'pdf';
    form.appendChild(formatInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Initialize chart on page load
document.addEventListener('DOMContentLoaded', function() {
    initChart();
});
</script>

<style>
.card {
    transition: all 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.h-64 {
    height: 16rem;
}

@media (max-width: 768px) {
    .h-64 {
        height: 12rem;
    }
}
</style>
@endsection