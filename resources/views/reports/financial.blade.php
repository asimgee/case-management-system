@extends('layouts.app')

@section('title', 'Financial Report - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-2">Financial Report</h1>
            <p class="text-gray-600 dark:text-gray-400">Revenue, expenses, and profitability analysis</p>
        </div>
        <div class="flex space-x-3 mt-4 lg:mt-0">
            <a href="{{ route('reports.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Reports
            </a>
            <button onclick="exportFinancialPDF()" class="btn-primary">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </button>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card p-4 lg:p-6 mb-6">
        <form id="dateFilterForm" method="GET" action="{{ route('reports.financial') }}">
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
                    <a href="{{ route('reports.financial') }}" class="btn-secondary">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Financial Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total Revenue</h3>
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600 dark:text-green-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                ${{ number_format($revenueData['total_revenue']) }}
            </div>
            <div class="flex items-center text-sm">
                <span class="text-green-600 dark:text-green-400 font-medium mr-2">
                    <i class="fas fa-arrow-up mr-1"></i>{{ $revenueData['revenue_growth'] }}%
                </span>
                <span class="text-gray-600 dark:text-gray-400">growth from last period</span>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Monthly Avg:</span>
                    <span class="font-medium text-gray-900 dark:text-white">${{ number_format($revenueData['monthly_revenue']) }}</span>
                </div>
                <div class="flex justify-between text-sm mt-2">
                    <span class="text-gray-600 dark:text-gray-400">Yearly Projection:</span>
                    <span class="font-medium text-gray-900 dark:text-white">${{ number_format($revenueData['yearly_revenue']) }}</span>
                </div>
            </div>
        </div>

        <!-- Total Expenses -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total Expenses</h3>
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-red-600 dark:text-red-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                ${{ number_format($expenseData['total_expenses']) }}
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Monthly average: ${{ number_format($expenseData['monthly_expenses']) }}
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="space-y-2">
                    @php
                        $expenseTotal = array_sum($expenseData['expense_breakdown']);
                    @endphp
                    @foreach($expenseData['expense_breakdown'] as $category => $amount)
                        @php
                            $percentage = $expenseTotal > 0 ? ($amount / $expenseTotal) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600 dark:text-gray-400 capitalize">{{ $category }}</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    ${{ number_format($amount) }} ({{ round($percentage, 1) }}%)
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Net Profit -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Net Profit</h3>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                ${{ number_format($profitData['net_profit']) }}
            </div>
            <div class="flex items-center text-sm mb-4">
                <span class="text-green-600 dark:text-green-400 font-medium">
                    Profit Margin: {{ round($profitData['profit_margin'], 1) }}%
                </span>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Monthly Profit:</span>
                    <span class="font-medium text-green-600 dark:text-green-400">
                        ${{ number_format($profitData['monthly_profit']) }}
                    </span>
                </div>
                <div class="flex justify-between text-sm mt-2">
                    <span class="text-gray-600 dark:text-gray-400">Average Case Value:</span>
                    <span class="font-medium text-gray-900 dark:text-white">
                        ${{ number_format($revenueData['average_case_value']) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Trend Chart -->
    <div class="card p-4 lg:p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Revenue Trend</h2>
        <div class="h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Top Revenue Cases -->
    <div class="card p-4 lg:p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Top Revenue Cases</h2>
            <span class="text-sm text-gray-600 dark:text-gray-400">
                {{ count($revenueData['top_revenue_cases']) }} cases
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Case Details
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Client
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Revenue
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Contribution
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($revenueData['top_revenue_cases'] as $index => $case)
                        @php
                            $percentage = ($case['revenue'] / $revenueData['total_revenue']) * 100;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-4 lg:px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                                        <span class="text-blue-600 dark:text-blue-400 font-medium text-sm">
                                            {{ $index + 1 }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ $case['case_number'] }}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            High-value case
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 lg:px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">
                                    {{ $case['client'] }}
                                </div>
                            </td>
                            <td class="px-4 lg:px-6 py-4">
                                <div class="font-bold text-green-600 dark:text-green-400">
                                    ${{ number_format($case['revenue']) }}
                                </div>
                            </td>
                            <td class="px-4 lg:px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-3">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ round($percentage, 1) }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Financial Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                {{ round($profitData['profit_margin'], 1) }}%
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Profit Margin</p>
        </div>
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-2">
                {{ $revenueData['revenue_growth'] }}%
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Revenue Growth</p>
        </div>
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400 mb-2">
                1:{{
                    $revenueData['total_revenue'] > 0 ? 
                    round($expenseData['total_expenses'] / $revenueData['total_revenue'], 2) : 
                    0
                }}
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Expense Ratio</p>
        </div>
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mb-2">
                ${{ number_format($revenueData['average_case_value']) }}
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Avg Case Value</p>
        </div>
    </div>

    <!-- Expense Breakdown Chart -->
    <div class="card p-4 lg:p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Expense Breakdown</h2>
        <div class="h-64">
            <canvas id="expenseChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize charts
let revenueChart = null;
let expenseChart = null;

function initCharts() {
    // Destroy existing charts if they exist
    if (revenueChart) revenueChart.destroy();
    if (expenseChart) expenseChart.destroy();
    
    // Revenue Trend Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Revenue',
                data: [45000, 52000, 48000, 61000, 55000, 72000, 68000, 75000, 70000, 82000, 78000, 90000],
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Expenses',
                data: [28000, 32000, 30000, 35000, 33000, 40000, 38000, 42000, 40000, 45000, 43000, 48000],
                borderColor: '#EF4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
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
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
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
    
    // Expense Breakdown Chart
    const expenseCtx = document.getElementById('expenseChart').getContext('2d');
    const expenseData = @json($expenseData['expense_breakdown']);
    
    expenseChart = new Chart(expenseCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(expenseData).map(key => key.charAt(0).toUpperCase() + key.slice(1)),
            datasets: [{
                data: Object.values(expenseData),
                backgroundColor: [
                    '#3B82F6', '#EF4444', '#10B981', '#F59E0B', 
                    '#8B5CF6', '#6366F1'
                ],
                borderWidth: 0,
                hoverOffset: 10
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
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = Object.values(expenseData).reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `${context.label}: $${context.parsed.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '65%'
        }
    });
}

// Export function
function exportFinancialPDF() {
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
    reportType.value = 'financial';
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

// Initialize charts on page load
document.addEventListener('DOMContentLoaded', function() {
    initCharts();
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