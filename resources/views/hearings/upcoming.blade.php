@extends('layouts.app')

@section('title', 'Upcoming Hearings - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-2">Upcoming Hearings</h1>
            <p class="text-gray-600 dark:text-gray-400">All scheduled hearings for your cases</p>
        </div>
        <div class="flex space-x-3 mt-4 lg:mt-0">
            <a href="{{ route('hearings.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
            <a href="{{ route('hearings.calendar') }}" class="btn-primary">
                <i class="fas fa-calendar-alt mr-2"></i> Calendar View
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-4 lg:p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex-1">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchInput" placeholder="Search by case number, party name, or court..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <select id="dateFilter" class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    <option value="all">All Dates</option>
                    <option value="today">Today</option>
                    <option value="tomorrow">Tomorrow</option>
                    <option value="this_week">This Week</option>
                    <option value="next_week">Next Week</option>
                    <option value="this_month">This Month</option>
                </select>
                <select id="statusFilter" class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_hearing">In Hearing</option>
                    <option value="overdue">Overdue</option>
                </select>
                <button onclick="resetFilters()" class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl text-gray-700 dark:text-gray-300 transition-colors">
                    <i class="fas fa-redo mr-2"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Upcoming</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $upcomingHearings->total() }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-gavel text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">This Week</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        {{ \App\Models\Case_Model::whereNotNull('next_date')
                            ->whereBetween('next_date', [now()->startOfWeek(), now()->endOfWeek()])
                            ->where('case_status', '!=', 'closed')
                            ->when(Auth::user()->role !== 'admin', function($q) {
                                return $q->where('user_id', Auth::id());
                            })
                            ->count() }}
                    </h3>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-week text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Overdue</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        {{ \App\Models\Case_Model::whereNotNull('next_date')
                            ->whereDate('next_date', '<', now()->toDateString())
                            ->where('case_status', '!=', 'closed')
                            ->when(Auth::user()->role !== 'admin', function($q) {
                                return $q->where('user_id', Auth::id());
                            })
                            ->count() }}
                    </h3>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Avg. Days to Hear</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        @php
                            $avgDays = \App\Models\Case_Model::whereNotNull('next_date')
                                ->where('next_date', '>=', now()->toDateString())
                                ->where('case_status', '!=', 'closed')
                                ->when(Auth::user()->role !== 'admin', function($q) {
                                    return $q->where('user_id', Auth::id());
                                })
                                ->selectRaw('AVG(DATEDIFF(next_date, CURDATE())) as avg_days')
                                ->first()->avg_days ?? 0;
                        @endphp
                        {{ round($avgDays) }}
                    </h3>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Hearings Table -->
    <div class="card p-0 overflow-hidden">
        <!-- Table Header -->
        <div class="p-4 lg:p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Hearing List</h2>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ $upcomingHearings->firstItem() ?? 0 }}-{{ $upcomingHearings->lastItem() ?? 0 }} of {{ $upcomingHearings->total() }} hearings
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            <div class="flex items-center space-x-1">
                                <span>Case Details</span>
                                <button onclick="sortTable('case')" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-sort"></i>
                                </button>
                            </div>
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            <div class="flex items-center space-x-1">
                                <span>Hearing Date</span>
                                <button onclick="sortTable('date')" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-sort"></i>
                                </button>
                            </div>
                        </th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Court & Lawyer</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($upcomingHearings as $case)
                        @php
                            $hearingDate = \Carbon\Carbon::parse($case->next_date);
                            $isToday = $hearingDate->isToday();
                            $isTomorrow = $hearingDate->isTomorrow();
                            $isOverdue = $hearingDate->isPast() && !$hearingDate->isToday();
                            $daysDiff = $hearingDate->diffInDays(now());
                            
                            // Determine status color
                            if ($isOverdue) {
                                $statusColor = 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
                                $statusText = 'Overdue';
                            } elseif ($isToday) {
                                $statusColor = 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
                                $statusText = 'Today';
                            } elseif ($isTomorrow) {
                                $statusColor = 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
                                $statusText = 'Tomorrow';
                            } elseif ($daysDiff <= 7) {
                                $statusColor = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
                                $statusText = 'This Week';
                            } else {
                                $statusColor = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
                                $statusText = 'Upcoming';
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <!-- Case Details -->
                            <td class="px-4 lg:px-6 py-4">
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-balance-scale text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('cases.show', $case->id) }}" class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            {{ $case->first_party_title }} vs {{ $case->second_party_title }}
                                        </a>
                                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="font-mono">#{{ $case->case_number }}</span>
                                                @if($case->caseType)
                                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-xs">
                                                        {{ $case->caseType->name }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="mt-1 text-xs">
                                                <span class="font-medium">Client:</span> {{ $case->first_party_name }}
                                                @if($case->first_party_contact)
                                                    <span class="text-gray-500 mx-2">•</span>
                                                    {{ $case->first_party_contact }}
                                                @endif
                                            </div>
                                            <div class="mt-1 text-xs">
                                                <span class="font-medium">Opponent:</span> {{ $case->second_party_name }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Hearing Date -->
                            <td class="px-4 lg:px-6 py-4">
                                <div class="space-y-1">
                                    <div class="font-medium text-gray-900 dark:text-white">
                                        {{ $hearingDate->format('D, M d, Y') }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $hearingDate->format('h:i A') }}
                                    </div>
                                    <div class="text-xs {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-500' }}">
                                        @if($isToday)
                                            <i class="fas fa-exclamation-circle mr-1"></i> Today
                                        @elseif($isTomorrow)
                                            <i class="fas fa-calendar-alt mr-1"></i> Tomorrow
                                        @elseif($isOverdue)
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Overdue by {{ $daysDiff }} day{{ $daysDiff > 1 ? 's' : '' }}
                                        @else
                                            <i class="fas fa-calendar mr-1"></i> In {{ $daysDiff }} day{{ $daysDiff > 1 ? 's' : '' }}
                                        @endif
                                    </div>
                                    @if($case->next_order)
                                        <div class="text-xs text-gray-500 mt-2" title="{{ $case->next_order }}">
                                            <i class="fas fa-sticky-note mr-1"></i>
                                            {{ Str::limit($case->next_order, 40) }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Court & Lawyer -->
                            <td class="px-4 lg:px-6 py-4">
                                <div class="space-y-2">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white text-sm">
                                            <i class="fas fa-landmark mr-2 text-gray-400"></i>
                                            {{ $case->court_name }}
                                        </div>
                                        @if($case->courtType)
                                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                                {{ $case->courtType->name }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                                        @if($case->assignedLawyer)
                                            <div class="flex items-center">
                                                <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mr-2">
                                                    <i class="fas fa-user-tie text-blue-600 dark:text-blue-400 text-xs"></i>
                                                </div>
                                                <span class="text-sm text-gray-900 dark:text-white">
                                                    {{ $case->assignedLawyer->name }}
                                                </span>
                                            </div>
                                            @if($case->assignedLawyer->phone)
                                                <div class="text-xs text-gray-600 dark:text-gray-400 ml-8 mt-1">
                                                    {{ $case->assignedLawyer->phone }}
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-sm text-gray-500 italic">
                                                No lawyer assigned
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-4 lg:px-6 py-4">
                                <div class="space-y-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                        {{ $statusText }}
                                    </span>
                                    <div class="text-xs">
                                        <span class="{{ $case->case_status === 'in_hearing' ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400' }}">
                                            {{ ucfirst(str_replace('_', ' ', $case->case_status)) }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Filed: {{ \Carbon\Carbon::parse($case->filing_date)->format('M d, Y') }}
                                    </div>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 lg:px-6 py-4">
                                <div class="flex flex-col space-y-2">
                                    <a href="{{ route('cases.show', $case->id) }}" 
                                       class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg transition-colors">
                                        <i class="fas fa-eye mr-1.5"></i> View
                                    </a>
                                    <a href="{{ route('cases.edit', $case->id) }}" 
                                       class="inline-flex items-center justify-center px-3 py-1.5 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-300 text-xs rounded-lg transition-colors">
                                        <i class="fas fa-edit mr-1.5"></i> Edit
                                    </a>
                                    <button onclick="showRescheduleModal({{ $case->id }}, '{{ $case->next_date }}')"
                                            class="inline-flex items-center justify-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs rounded-lg transition-colors">
                                        <i class="fas fa-calendar-plus mr-1.5"></i> Reschedule
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 lg:px-6 py-12 text-center">
                                <div class="w-20 h-20 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-calendar-times text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No upcoming hearings</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-6">You have no scheduled hearings.</p>
                                <a href="{{ route('cases.create') }}" class="btn-primary">
                                    <i class="fas fa-plus mr-2"></i> Add New Case
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($upcomingHearings->hasPages())
            <div class="p-4 lg:p-6 border-t border-gray-200 dark:border-gray-700">
                {{ $upcomingHearings->links() }}
            </div>
        @endif
    </div>

    <!-- Export Section -->
    <div class="mt-6 flex justify-between items-center">
        <div class="text-sm text-gray-600 dark:text-gray-400">
            <i class="fas fa-info-circle mr-2"></i>
            Export hearing list for better tracking
        </div>
        <div class="flex space-x-3">
            <button onclick="exportToCSV()" class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm transition-colors">
                <i class="fas fa-file-csv mr-2"></i> Export CSV
            </button>
            <button onclick="printHearingList()" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm transition-colors">
                <i class="fas fa-print mr-2"></i> Print List
            </button>
        </div>
    </div>
</div>

<!-- Reschedule Modal -->
<div id="rescheduleModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Reschedule Hearing</h3>
        </div>
        <div class="p-6">
            <form id="rescheduleForm">
                @csrf
                <input type="hidden" id="rescheduleCaseId">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Hearing Date</label>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-gray-900 dark:text-white font-medium" id="currentHearingDate"></p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" id="currentHearingInfo"></p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Hearing Date *</label>
                    <input type="date" id="newHearingDate" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" required>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason for Rescheduling (Optional)</label>
                    <textarea id="rescheduleReason" rows="3" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors resize-none" placeholder="Enter reason for rescheduling..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRescheduleModal()" class="px-4 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors">
                        Reschedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Filter functionality
document.getElementById('searchInput').addEventListener('input', function() {
    filterHearings();
});

document.getElementById('dateFilter').addEventListener('change', function() {
    filterHearings();
});

document.getElementById('statusFilter').addEventListener('change', function() {
    filterHearings();
});

function filterHearings() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const dateFilter = document.getElementById('dateFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    const rows = document.querySelectorAll('tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        if (row.cells.length < 5) return; // Skip empty row
        
        const caseNumber = row.cells[0].querySelector('.font-mono').textContent.toLowerCase();
        const partyNames = row.cells[0].querySelector('a').textContent.toLowerCase();
        const courtName = row.cells[2].querySelector('.font-medium').textContent.toLowerCase();
        const status = row.cells[3].querySelector('.inline-flex').textContent.toLowerCase();
        const caseStatus = row.cells[3].querySelector('.text-xs span').textContent.toLowerCase();
        const hearingDate = new Date(row.cells[1].querySelector('.font-medium').textContent);
        const today = new Date();
        
        // Date filter
        let dateMatch = true;
        if (dateFilter !== 'all') {
            switch(dateFilter) {
                case 'today':
                    dateMatch = hearingDate.toDateString() === today.toDateString();
                    break;
                case 'tomorrow':
                    const tomorrow = new Date(today);
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    dateMatch = hearingDate.toDateString() === tomorrow.toDateString();
                    break;
                case 'this_week':
                    const startOfWeek = new Date(today);
                    startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay());
                    const endOfWeek = new Date(startOfWeek);
                    endOfWeek.setDate(endOfWeek.getDate() + 6);
                    dateMatch = hearingDate >= startOfWeek && hearingDate <= endOfWeek;
                    break;
                case 'next_week':
                    const nextWeekStart = new Date(today);
                    nextWeekStart.setDate(nextWeekStart.getDate() + (7 - nextWeekStart.getDay()));
                    const nextWeekEnd = new Date(nextWeekStart);
                    nextWeekEnd.setDate(nextWeekEnd.getDate() + 6);
                    dateMatch = hearingDate >= nextWeekStart && hearingDate <= nextWeekEnd;
                    break;
                case 'this_month':
                    dateMatch = hearingDate.getMonth() === today.getMonth() && 
                                hearingDate.getFullYear() === today.getFullYear();
                    break;
            }
        }
        
        // Status filter
        let statusMatch = true;
        if (statusFilter !== 'all') {
            if (statusFilter === 'overdue') {
                statusMatch = status.includes('overdue');
            } else if (statusFilter === 'pending') {
                statusMatch = caseStatus.includes('pending');
            } else if (statusFilter === 'in_hearing') {
                statusMatch = caseStatus.includes('in hearing');
            }
        }
        
        // Search filter
        const searchMatch = !searchTerm || 
            caseNumber.includes(searchTerm) || 
            partyNames.includes(searchTerm) || 
            courtName.includes(searchTerm);
        
        // Show/hide row
        if (dateMatch && statusMatch && searchMatch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show/hide empty message
    const emptyRow = document.querySelector('tbody tr:last-child');
    if (emptyRow && emptyRow.cells.length === 1) {
        emptyRow.style.display = visibleCount === 0 ? '' : 'none';
    }
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('dateFilter').value = 'all';
    document.getElementById('statusFilter').value = 'all';
    filterHearings();
}

function sortTable(column) {
    // This is a simple client-side sort
    // For large datasets, implement server-side sorting
    alert('Sorting feature would be implemented with server-side pagination');
}

// Reschedule Modal Functions
function showRescheduleModal(caseId, currentDate) {
    const date = new Date(currentDate);
    document.getElementById('rescheduleCaseId').value = caseId;
    document.getElementById('currentHearingDate').textContent = date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    
    const daysDiff = Math.floor((date - new Date()) / (1000 * 60 * 60 * 24));
    let infoText = '';
    if (daysDiff < 0) {
        infoText = `Overdue by ${Math.abs(daysDiff)} day${Math.abs(daysDiff) > 1 ? 's' : ''}`;
    } else if (daysDiff === 0) {
        infoText = 'Scheduled for today';
    } else {
        infoText = `In ${daysDiff} day${daysDiff > 1 ? 's' : ''}`;
    }
    document.getElementById('currentHearingInfo').textContent = infoText;
    
    // Set minimum date for rescheduling
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('newHearingDate').min = tomorrow.toISOString().split('T')[0];
    document.getElementById('newHearingDate').value = '';
    document.getElementById('rescheduleReason').value = '';
    
    document.getElementById('rescheduleModal').classList.remove('hidden');
}

function closeRescheduleModal() {
    document.getElementById('rescheduleModal').classList.add('hidden');
}

document.getElementById('rescheduleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const caseId = document.getElementById('rescheduleCaseId').value;
    const newDate = document.getElementById('newHearingDate').value;
    const reason = document.getElementById('rescheduleReason').value;
    
    if (!newDate) {
        alert('Please select a new hearing date');
        return;
    }
    
    // Send AJAX request
    fetch(`/hearings/${caseId}/update-next-date`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            next_date: newDate,
            next_order: reason || 'Hearing rescheduled'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Hearing rescheduled successfully!');
            closeRescheduleModal();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});

// Export Functions
function exportToCSV() {
    // This would generate and download a CSV file
    alert('CSV export feature would generate a file with all hearing data');
    // Implementation would involve collecting data and creating a CSV download
}

function printHearingList() {
    const printContent = document.querySelector('.card').outerHTML;
    const originalContent = document.body.innerHTML;
    
    document.body.innerHTML = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Upcoming Hearings - Print</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f5f5f5; }
                h1 { color: #333; }
                .print-header { margin-bottom: 20px; }
                .print-footer { margin-top: 30px; text-align: center; color: #666; }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h1>Upcoming Hearings Report</h1>
                <p>Generated on: ${new Date().toLocaleDateString()} ${new Date().toLocaleTimeString()}</p>
                <p>Total Hearings: {{ $upcomingHearings->total() }}</p>
            </div>
            ${printContent}
            <div class="print-footer">
                <p>--- End of Report ---</p>
            </div>
        </body>
        </html>
    `;
    
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
}

// Close modals when clicking outside
document.getElementById('rescheduleModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRescheduleModal();
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRescheduleModal();
    }
});

// Initialize filters on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set today's date as default for date pickers
    const today = new Date().toISOString().split('T')[0];
    document.querySelectorAll('input[type="date"]').forEach(input => {
        if (!input.value) {
            input.value = today;
        }
    });
});
</script>

<style>
/* Custom styles for better UX */
@media print {
    .no-print {
        display: none !important;
    }
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Better table responsiveness */
@media (max-width: 768px) {
    table {
        display: block;
        overflow-x: auto;
    }
}
</style>
@endsection