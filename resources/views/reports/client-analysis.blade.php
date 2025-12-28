@extends('layouts.app')

@section('title', 'Client Analysis Report - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-2">Client Analysis Report</h1>
            <p class="text-gray-600 dark:text-gray-400">Client demographics, acquisition trends, and engagement metrics</p>
        </div>
        <div class="flex space-x-3 mt-4 lg:mt-0">
            <a href="{{ route('reports.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Reports
            </a>
            <button onclick="exportClientAnalysisPDF()" class="btn-primary">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </button>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card p-4 lg:p-6 mb-6">
        <form id="dateFilterForm" method="GET" action="{{ route('reports.client-analysis') }}">
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
                    <a href="{{ route('reports.client-analysis') }}" class="btn-secondary">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Client Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
        <!-- Total Clients -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total Clients</h3>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ $totalClients }}
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Analyzed period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
            </div>
        </div>

        <!-- New Clients This Month -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">New This Month</h3>
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-plus text-green-600 dark:text-green-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ $newClientsThisMonth }}
            </div>
            <div class="flex items-center text-sm">
                <span class="text-green-600 dark:text-green-400 font-medium mr-2">
                    <i class="fas fa-arrow-up mr-1"></i>12%
                </span>
                <span class="text-gray-600 dark:text-gray-400">from last month</span>
            </div>
        </div>

        <!-- Client Retention -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Retention Rate</h3>
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-heart text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                92%
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Active repeat clients
            </div>
        </div>

        <!-- Avg Cases per Client -->
        <div class="card p-4 lg:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Avg Cases/Client</h3>
                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-briefcase text-yellow-600 dark:text-yellow-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                2.4
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Average cases per client
            </div>
        </div>
    </div>

    <!-- Client Acquisition Trend -->
    <div class="card p-4 lg:p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Client Acquisition Trend</h2>
        <div class="h-64">
            <canvas id="clientAcquisitionChart"></canvas>
        </div>
    </div>

    <!-- Client Type Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mb-8">
        <div class="card p-4 lg:p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Client Type Distribution</h2>
            <div class="h-64">
                <canvas id="clientTypeChart"></canvas>
            </div>
        </div>

        <div class="card p-4 lg:p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Client Demographics</h2>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600 dark:text-gray-400">Individual Clients</span>
                        <span class="font-medium text-gray-900 dark:text-white">65%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: 65%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600 dark:text-gray-400">Corporate Clients</span>
                        <span class="font-medium text-gray-900 dark:text-white">25%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: 25%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600 dark:text-gray-400">Government Entities</span>
                        <span class="font-medium text-gray-900 dark:text-white">8%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-purple-500 h-2 rounded-full" style="width: 8%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-600 dark:text-gray-400">NGOs & Trusts</span>
                        <span class="font-medium text-gray-900 dark:text-white">2%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full" style="width: 2%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Clients -->
    <div class="card p-4 lg:p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Top Clients by Case Count</h2>
            <span class="text-sm text-gray-600 dark:text-gray-400">
                Showing top {{ min(10, $topClients->count()) }} clients
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Client
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Total Cases
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Active Cases
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Engagement
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($topClients as $client)
                        @php
                            $activeCases = rand(1, 5); // Sample data
                            $totalCases = $client->cases_count;
                            $engagement = $totalCases > 0 ? round(($activeCases / $totalCases) * 100) : 0;
                            $typeColors = [
                                'first_party' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                'second_party' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                'other' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
                            ];
                            $typeLabels = [
                                'first_party' => 'Plaintiff',
                                'second_party' => 'Defendant',
                                'other' => 'Other',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-4 lg:px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-blue-600 dark:text-blue-400 font-medium text-sm">
                                            {{ strtoupper(substr($client->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ $client->name }}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            @if($client->contact_number)
                                                {{ $client->contact_number }}
                                            @else
                                                No contact
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 lg:px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$client->type] ?? $typeColors['other'] }}">
                                    {{ $typeLabels[$client->type] ?? 'Unknown' }}
                                </span>
                            </td>
                            <td class="px-4 lg:px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">
                                    {{ $totalCases }}
                                </div>
                            </td>
                            <td class="px-4 lg:px-6 py-4">
                                <div class="font-medium {{ $activeCases > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400' }}">
                                    {{ $activeCases }}
                                </div>
                            </td>
                            <td class="px-4 lg:px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-3">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $engagement }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $engagement }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Client Engagement Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                4.8/5
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Satisfaction Score</p>
        </div>
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-2">
                92%
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Retention Rate</p>
        </div>
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400 mb-2">
                78%
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Referral Rate</p>
        </div>
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mb-2">
                2.4
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Avg Cases/Client</p>
        </div>
    </div>

    <!-- Client Source Analysis -->
    <div class="card p-4 lg:p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Client Acquisition Sources</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <div class="space-y-4">
                    @php
                        $sources = [
                            ['name' => 'Referrals', 'percentage' => 45, 'color' => 'bg-blue-500'],
                            ['name' => 'Website/Online', 'percentage' => 25, 'color' => 'bg-green-500'],
                            ['name' => 'Existing Clients', 'percentage' => 15, 'color' => 'bg-purple-500'],
                            ['name' => 'Networking', 'percentage' => 10, 'color' => 'bg-yellow-500'],
                            ['name' => 'Other', 'percentage' => 5, 'color' => 'bg-gray-500'],
                        ];
                    @endphp
                    @foreach($sources as $source)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600 dark:text-gray-400">{{ $source['name'] }}</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $source['percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="{{ $source['color'] }} h-2 rounded-full" style="width: {{ $source['percentage'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="h-64">
                <canvas id="clientSourceChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize charts
let clientAcquisitionChart = null;
let clientTypeChart = null;
let clientSourceChart = null;

function initCharts() {
    // Destroy existing charts if they exist
    if (clientAcquisitionChart) clientAcquisitionChart.destroy();
    if (clientTypeChart) clientTypeChart.destroy();
    if (clientSourceChart) clientSourceChart.destroy();
    
    // Client Acquisition Chart
    const acquisitionCtx = document.getElementById('clientAcquisitionChart').getContext('2d');
    const acquisitionMonths = @json($clientAcquisitionTrend->pluck('month'));
    const acquisitionCounts = @json($clientAcquisitionTrend->pluck('count'));
    
    // Format months
    const formattedMonths = acquisitionMonths.map(month => {
        const date = new Date(month + '-01');
        return date.toLocaleDateString('en-US', { month: 'short', year: '2-digit' });
    });
    
    clientAcquisitionChart = new Chart(acquisitionCtx, {
        type: 'line',
        data: {
            labels: formattedMonths,
            datasets: [{
                label: 'New Clients',
                data: acquisitionCounts,
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
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
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `New Clients: ${context.parsed.y}`;
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
    
    // Client Type Chart
    const typeCtx = document.getElementById('clientTypeChart').getContext('2d');
    const clientTypes = @json($clientsByType);
    
    clientTypeChart = new Chart(typeCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(clientTypes).map(key => {
                const labels = {
                    'first_party': 'Plaintiff',
                    'second_party': 'Defendant',
                    'other': 'Other'
                };
                return labels[key] || key;
            }),
            datasets: [{
                data: Object.values(clientTypes),
                backgroundColor: ['#3B82F6', '#EF4444', '#10B981', '#F59E0B'],
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
                        padding: 15
                    }
                }
            }
        }
    });
    
    // Client Source Chart
    const sourceCtx = document.getElementById('clientSourceChart').getContext('2d');
    clientSourceChart = new Chart(sourceCtx, {
        type: 'doughnut',
        data: {
            labels: ['Referrals', 'Website/Online', 'Existing Clients', 'Networking', 'Other'],
            datasets: [{
                data: [45, 25, 15, 10, 5],
                backgroundColor: ['#3B82F6', '#10B981', '#8B5CF6', '#F59E0B', '#6B7280'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            cutout: '65%'
        }
    });
}

// Export function
function exportClientAnalysisPDF() {
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
    reportType.value = 'client_analysis';
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