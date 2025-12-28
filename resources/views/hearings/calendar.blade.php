@extends('layouts.app')

@section('title', 'Hearing Calendar - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 mobile-space-y-4">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Hearing Calendar</h1>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('hearings.index') }}" class="btn-secondary">
                <i class="fas fa-list mr-2"></i> List View
            </a>
            <a href="{{ route('hearings.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i> Schedule Hearing
            </a>
            @if(request()->has('year') || request()->has('month'))
                <a href="{{ route('hearings.calendar') }}" class="btn-secondary">
                    <i class="fas fa-calendar-day mr-2"></i> Current Month
                </a>
            @endif
        </div>
    </div>

    <!-- Calendar Navigation -->
    <div class="card p-4 lg:p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 gap-4">
            <div class="flex items-center space-x-4">
                <button onclick="changeMonth(-1)" 
                        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ $monthName }} {{ $year }}
                </h2>
                <button onclick="changeMonth(1)" 
                        class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="goToToday()" class="btn-primary">
                    <i class="fas fa-calendar-day mr-2"></i> Today
                </button>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $totalHearings }} hearings this month
                </div>
            </div>
        </div>

        <!-- Calendar Days Header -->
        <div class="grid grid-cols-7 gap-1 lg:gap-2 mb-4">
            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                <div class="text-center font-semibold text-gray-700 dark:text-gray-300 py-2 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    {{ $day }}
                </div>
            @endforeach
        </div>
        
        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 gap-1 lg:gap-2">
            <!-- Empty days for the first week -->
            @for($i = 0; $i < $firstDayOfWeek; $i++)
                <div class="min-h-24 lg:min-h-32 border border-gray-200 dark:border-gray-700 rounded-lg p-1 lg:p-2"></div>
            @endfor
            
            <!-- Days of the month -->
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $isToday = $dateStr === now()->toDateString();
                    $hasHearings = isset($hearingsByDate[$dateStr]);
                    $dayHearings = $hasHearings ? $hearingsByDate[$dateStr] : [];
                @endphp
                <div class="min-h-24 lg:min-h-32 border border-gray-200 dark:border-gray-700 rounded-lg p-1 lg:p-2 
                          {{ $isToday ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' : '' }}
                          {{ $hasHearings ? 'border-green-200 dark:border-green-800' : '' }}
                          hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    
                    <!-- Day Number -->
                    <div class="flex justify-between items-start mb-1">
                        <div class="font-medium {{ $isToday ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-white' }} 
                                  {{ $hasHearings ? 'font-bold' : '' }}">
                            {{ $day }}
                        </div>
                        @if($hasHearings)
                            <span class="text-xs bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 rounded-full px-2 py-0.5">
                                {{ count($dayHearings) }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Hearing entries for this day -->
                    @if($hasHearings)
                        <div class="space-y-1 overflow-y-auto max-h-20 lg:max-h-24">
                            @foreach($dayHearings as $hearing)
                                <div class="text-xs p-1 rounded cursor-pointer truncate hover:opacity-90 transition-opacity 
                                          {{ $hearing->status === 'completed' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400' : 
                                             ($hearing->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : 
                                             ($hearing->status === 'adjourned' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : 
                                             'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400')) }}"
                                     onclick="showHearingDetails({{ $hearing->id }})"
                                     title="{{ $hearing->case->case_number ?? 'N/A' }} - {{ $hearing->purpose }}">
                                    <div class="flex items-center">
                                        <i class="fas fa-clock text-xs mr-1"></i>
                                        <span>{{ \Carbon\Carbon::parse($hearing->hearing_time)->format('h:i A') }}</span>
                                    </div>
                                    <div class="font-medium truncate">
                                        {{ $hearing->case->first_party_title ?? 'N/A' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endfor
        </div>
    </div>

    <!-- Legend -->
    <div class="card p-4 lg:p-6 mb-6">
        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-4">Legend</h3>
        <div class="flex flex-wrap gap-3">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Scheduled</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-yellow-100 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 rounded"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Adjourned</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Cancelled</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Completed</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-blue-100 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded"></div>
                <span class="text-sm text-gray-600 dark:text-gray-400">Today</span>
            </div>
        </div>
    </div>

    <!-- Upcoming Hearings Summary -->
    @if(count($upcomingHearings) > 0)
        <div class="card p-4 lg:p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Upcoming This Week</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($upcomingHearings as $hearing)
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white">
                                    {{ $hearing->case->case_number ?? 'N/A' }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    {{ $hearing->case->case_title ?? 'N/A' }}
                                </p>
                            </div>
                            <span class="badge {{ $hearing->status_color }}">
                                {{ $hearing->hearing_status }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt mr-2 w-4"></i>
                                <span>{{ $hearing->formatted_hearing_date }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock mr-2 w-4"></i>
                                <span>{{ $hearing->formatted_hearing_time }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-2 w-4"></i>
                                <span class="truncate">{{ $hearing->location }}</span>
                            </div>
                            @if($hearing->judge)
                                <div class="flex items-center">
                                    <i class="fas fa-gavel mr-2 w-4"></i>
                                    <span>{{ $hearing->judge }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="mt-4 flex space-x-2">
                            <a href="{{ route('hearings.show', $hearing->id) }}" 
                               class="flex-1 text-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded-lg transition-colors">
                                <i class="fas fa-eye mr-1"></i> View
                            </a>
                            @if($hearing->status === 'scheduled')
                                <a href="{{ route('hearings.edit', $hearing->id) }}" 
                                   class="flex-1 text-center px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white text-xs rounded-lg transition-colors">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Hearing Details Modal -->
<div id="hearingDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl max-h-[80vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white" id="hearingTitle"></h3>
                <button onclick="closeHearingDetails()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="p-6" id="hearingDetailsContent">
            <!-- Content will be loaded via AJAX -->
            <div class="flex justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>
        </div>
        <div class="p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
            <button onclick="closeHearingDetails()" class="btn-secondary">
                Close
            </button>
            <a href="#" id="viewHearingBtn" class="btn-primary">
                View Full Details
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Calendar Navigation
function changeMonth(delta) {
    const url = new URL(window.location.href);
    let year = parseInt(url.searchParams.get('year') || new Date().getFullYear());
    let month = parseInt(url.searchParams.get('month') || new Date().getMonth() + 1);
    
    month += delta;
    if (month > 12) {
        year++;
        month = 1;
    } else if (month < 1) {
        year--;
        month = 12;
    }
    
    url.searchParams.set('year', year);
    url.searchParams.set('month', month);
    window.location.href = url.toString();
}

function goToToday() {
    const today = new Date();
    const url = new URL(window.location.href);
    url.searchParams.set('year', today.getFullYear());
    url.searchParams.set('month', today.getMonth() + 1);
    window.location.href = url.toString();
}

// Hearing Details Modal
function showHearingDetails(hearingId) {
    const modal = document.getElementById('hearingDetailsModal');
    const title = document.getElementById('hearingTitle');
    const content = document.getElementById('hearingDetailsContent');
    const viewBtn = document.getElementById('viewHearingBtn');
    
    // Show modal with loading
    modal.classList.remove('hidden');
    content.innerHTML = `
        <div class="flex justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>
    `;
    
    // Fetch hearing details
    fetch(`/hearings/${hearingId}/modal`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(html => {
            content.innerHTML = html;
            viewBtn.href = `/hearings/${hearingId}`;
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-4"></i>
                    <p class="text-red-600 dark:text-red-400">Failed to load hearing details.</p>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-2">Please try again later.</p>
                </div>
            `;
        });
}

function closeHearingDetails() {
    document.getElementById('hearingDetailsModal').classList.add('hidden');
}

// Close modal when clicking outside or pressing Escape
document.getElementById('hearingDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) closeHearingDetails();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeHearingDetails();
});

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowLeft') changeMonth(-1);
    if (e.key === 'ArrowRight') changeMonth(1);
});
</script>
@endpush

<style>
/* Calendar styling */
@media (max-width: 640px) {
    .calendar-day {
        min-height: 60px;
        font-size: 0.75rem;
        padding: 0.25rem;
    }
}

/* Scrollbar styling for calendar days */
.calendar-day::-webkit-scrollbar {
    width: 3px;
}

.calendar-day::-webkit-scrollbar-track {
    background: transparent;
}

.calendar-day::-webkit-scrollbar-thumb {
    background: rgba(156, 163, 175, 0.5);
    border-radius: 3px;
}

.calendar-day::-webkit-scrollbar-thumb:hover {
    background: rgba(156, 163, 175, 0.7);
}

/* Animation for modal */
@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#hearingDetailsModal > div {
    animation: modalFadeIn 0.3s ease-out;
}
</style>
@endsection