@extends('layouts.app')

@section('title', 'Cases Management - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Cases Management</h1>
        <button onclick="window.openCaseModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i>Add New Case
        </button>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 dark:bg-green-900/30 dark:border-green-800 dark:text-green-400 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 dark:bg-red-900/30 dark:border-red-800 dark:text-red-400 flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <form method="GET" action="{{ route('cases.index') }}" id="filterForm">
        <div class="card p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Court</label>
                    <select name="court" id="courtFilter" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Courts</option>
                        <option value="Supreme Court" {{ request('court') == 'Supreme Court' ? 'selected' : '' }}>Supreme Court</option>
                        <option value="High Court" {{ request('court') == 'High Court' ? 'selected' : '' }}>High Court</option>
                        <option value="District Court" {{ request('court') == 'District Court' ? 'selected' : '' }}>District Court</option>
                        <option value="Session Court" {{ request('court') == 'Session Court' ? 'selected' : '' }}>Session Court</option>
                        <option value="Magistrate Court" {{ request('court') == 'Magistrate Court' ? 'selected' : '' }}>Magistrate Court</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" id="statusFilter" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_hearing" {{ request('status') == 'in_hearing' ? 'selected' : '' }}>Hearing</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                    <input type="date" name="date" id="dateFilter" value="{{ request('date') }}" 
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                @if(Auth::user()->role === 'admin')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User</label>
                    <select name="user" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div class="flex items-end space-x-2">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <a href="{{ route('cases.index') }}" class="w-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors flex items-center justify-center">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!-- Cases Table -->
    <div class="card overflow-hidden">
        @if($cases->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full min-w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Case ID</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell">Court</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell">Date</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($cases as $case)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            <a href="{{ route('cases.show', $case->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                {{ $case->case_number }}
                            </a>
                        </td>
                        <td class="px-4 lg:px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($case->case_title, 30) }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $case->caseType->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden lg:table-cell">
                            {{ Str::limit($case->court_name, 20) }}
                        </td>
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ app('App\Http\Controllers\CaseController')->getStatusColor($case->case_status) }}">
                                {{ ucfirst(str_replace('_', ' ', $case->case_status)) }}
                            </span>
                        </td>
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden lg:table-cell">
                            {{ \Carbon\Carbon::parse($case->filing_date)->format('M d, Y') }}
                        </td>
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('cases.show', $case->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('cases.edit', $case->id) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300 transition-colors" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('cases.destroy', $case->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this case?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <div class="mb-4">
                <i class="fas fa-folder-open text-4xl text-gray-300 dark:text-gray-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No cases found</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Get started by creating your first case.</p>
            <button onclick="window.openCaseModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center mx-auto">
                <i class="fas fa-plus mr-2"></i>Create Your First Case
            </button>
        </div>
        @endif

        <!-- Pagination -->
        @if($cases->hasPages())
        <div class="bg-white dark:bg-gray-800 px-4 lg:px-6 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $cases->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@include('components.modals.case')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit form when filters change (optional)
        const filters = ['courtFilter', 'statusFilter', 'dateFilter'];
        filters.forEach(filterId => {
            const element = document.getElementById(filterId);
            if (element) {
                element.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            }
        });
    });

    function openCaseModal() {
        // This function is defined in the modal file
        window.openCaseModal();
    }
</script>
@endsection