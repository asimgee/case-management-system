<div class="space-y-6">
    <!-- Basic Information -->
    <div>
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Hearing Information</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Case Number</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    {{ $hearing->case->case_number ?? 'N/A' }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Case Title</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    {{ $hearing->case->case_title ?? 'N/A' }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Date & Time</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    {{ $hearing->formatted_hearing_date }} at {{ $hearing->formatted_hearing_time }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Location</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $hearing->location }}</p>
            </div>
        </div>
    </div>
    
    <!-- Hearing Details -->
    <div>
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Hearing Details</h4>
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Type</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $hearing->type_color }}">
                    {{ $hearing->hearing_type_label }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Status</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $hearing->status_color }}">
                    {{ $hearing->hearing_status }}
                </span>
            </div>
            @if($hearing->judge)
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Judge</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $hearing->judge }}</p>
            </div>
            @endif
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Purpose</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $hearing->purpose }}</p>
            </div>
            @if($hearing->duration_minutes)
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Duration</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $hearing->duration_formatted }}</p>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Next Hearing (if any) -->
    @if($hearing->has_next_hearing)
    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Next Hearing</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Date & Time</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    {{ $hearing->formatted_next_hearing_date }} at {{ $hearing->formatted_next_hearing_time }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Location</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $hearing->next_hearing_location }}</p>
            </div>
            @if($hearing->next_hearing_judge)
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Judge</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $hearing->next_hearing_judge }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif
    
    <!-- Attendees -->
    @if($hearing->attendees->count() > 0)
    <div>
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Attendees ({{ $hearing->attendees->count() }})</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($hearing->attendees as $attendee)
            <div class="flex items-center space-x-3 p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="w-8 h-8 rounded-full flex items-center justify-center 
                          {{ $attendee->role === 'lawyer' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 
                             ($attendee->role === 'client' ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400' : 
                             ($attendee->role === 'judge' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400' : 
                             'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400')) }}">
                    <i class="fas fa-user text-xs"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $attendee->name }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $attendee->role_label }}</p>
                    @if($attendee->contact)
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">{{ $attendee->contact }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Notes -->
    @if($hearing->notes)
    <div>
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Notes</h4>
        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $hearing->notes }}</p>
        </div>
    </div>
    @endif
</div>