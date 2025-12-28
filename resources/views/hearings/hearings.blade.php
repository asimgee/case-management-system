@extends('layouts.app')

@section('title', 'Hearing Calendar - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 mobile-space-y-4">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Hearing Calendar</h1>
        <div class="flex space-x-3 mobile-full-width mobile-justify-center">
            <button class="btn-secondary mobile-full-width">
                Monthly View
            </button>
            <button class="btn-primary mobile-full-width">
                Weekly View
            </button>
        </div>
    </div>

    <!-- Calendar Section -->
    <div class="card p-4 lg:p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">January 2024</h2>
            <div class="flex space-x-2">
                <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-7 gap-2 lg:gap-4 mb-4">
            <div class="text-center font-semibold text-gray-700 dark:text-gray-300 py-2">Sun</div>
            <div class="text-center font-semibold text-gray-700 dark:text-gray-300 py-2">Mon</div>
            <div class="text-center font-semibold text-gray-700 dark:text-gray-300 py-2">Tue</div>
            <div class="text-center font-semibold text-gray-700 dark:text-gray-300 py-2">Wed</div>
            <div class="text-center font-semibold text-gray-700 dark:text-gray-300 py-2">Thu</div>
            <div class="text-center font-semibold text-gray-700 dark:text-gray-300 py-2">Fri</div>
            <div class="text-center font-semibold text-gray-700 dark:text-gray-300 py-2">Sat</div>
        </div>
        
        <div class="grid grid-cols-7 gap-2 lg:gap-4">
            @for($i = 1; $i <= 31; $i++)
                <div class="h-16 lg:h-20 border border-gray-200 dark:border-gray-700 rounded-lg p-2 text-sm transition-colors hover:bg-gray-50 dark:hover:bg-gray-800 {{ in_array($i, [3, 5, 12, 18, 25]) ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' : '' }}">
                    <div class="font-medium {{ in_array($i, [3, 5, 12, 18, 25]) ? 'text-blue-600 dark:text-blue-400' : '' }}">{{ $i }}</div>
                    @if($i == 3)
                        <div class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 rounded px-1 mt-1 truncate">2PM Hearing</div>
                    @endif
                    @if($i == 5)
                        <div class="text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-400 rounded px-1 mt-1 truncate">10AM Court</div>
                    @endif
                    @if($i == 12)
                        <div class="text-xs bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 rounded px-1 mt-1 truncate">3PM Trial</div>
                    @endif
                    @if($i == 18)
                        <div class="text-xs bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400 rounded px-1 mt-1 truncate">11AM Motion</div>
                    @endif
                    @if($i == 25)
                        <div class="text-xs bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 rounded px-1 mt-1 truncate">9AM Settlement</div>
                    @endif
                </div>
            @endfor
        </div>
    </div>

    <!-- Upcoming Hearings -->
    <div class="card p-4 lg:p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Upcoming Hearings</h2>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-gavel text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Smith vs. Johnson</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Case #2024-001</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                            <i class="fas fa-clock mr-1"></i>Tomorrow, 2:00 PM
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Supreme Court</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Room 304</p>
                    <button class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs transition-colors">
                        View Details
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl border border-purple-200 dark:border-purple-800">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-balance-scale text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Corporate Merger Case</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Case #2024-002</p>
                        <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">
                            <i class="fas fa-clock mr-1"></i>Jan 5, 10:00 AM
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">High Court</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Room 215</p>
                    <button class="mt-2 bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded-lg text-xs transition-colors">
                        View Details
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-home text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Family Custody Dispute</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Case #2024-003</p>
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                            <i class="fas fa-clock mr-1"></i>Jan 12, 3:00 PM
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">District Court</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Room 102</p>
                    <button class="mt-2 bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg text-xs transition-colors">
                        View Details
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl border border-yellow-200 dark:border-yellow-800">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-contract text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-900 dark:text-white">Contract Dispute</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Case #2024-004</p>
                        <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                            <i class="fas fa-clock mr-1"></i>Jan 18, 11:00 AM
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Civil Court</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Room 408</p>
                    <button class="mt-2 bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded-lg text-xs transition-colors">
                        View Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection