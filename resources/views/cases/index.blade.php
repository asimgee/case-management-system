@extends('layouts.app')

@section('title', 'Cases Management - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 mobile-space-y-4">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Cases Management</h1>
        <button onclick="openCaseModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            Add New Case
        </button>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 dark:bg-green-900/30 dark:border-green-800 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 dark:bg-red-900/30 dark:border-red-800 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="card p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <select id="courtFilter" class="border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Courts</option>
                <option value="Supreme Court">Supreme Court</option>
                <option value="High Court">High Court</option>
                <option value="District Court">District Court</option>
                <option value="Session Court">Session Court</option>
                <option value="Magistrate Court">Magistrate Court</option>
            </select>
            <select id="statusFilter" class="border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Status</option>
                <option value="open">Open</option>
                <option value="pending">Pending</option>
                <option value="hearing">Hearing</option>
                <option value="closed">Closed</option>
            </select>
            <input type="date" id="dateFilter" class="border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button class="btn-secondary" onclick="exportCases()">
                <i class="fas fa-download mr-2"></i>Export
            </button>
        </div>
    </div>

    <!-- Cases Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Case ID</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mobile-hidden">Court</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mobile-hidden">Date</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="casesTableBody">
                    @forelse($cases as $case)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $case->case_number }} {{-- Fixed: Changed from case_number ?? 'N/A' to case_number --}}
                        </td>
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $case->case_title }}
                        </td>
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 mobile-hidden">
                            {{ $case->court_name }}
                        </td>
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ app('App\Http\Controllers\CaseController')->getStatusColor($case->case_status) }}">
                                {{ ucfirst($case->case_status) }}
                            </span>
                        </td>
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 mobile-hidden">
                            {{ \Carbon\Carbon::parse($case->filing_date)->format('M d, Y') }}
                        </td>
                        <td class="px-4 lg:px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('cases.show', $case->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3 transition-colors" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('cases.edit', $case->id) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300 mr-3 transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('cases.destroy', $case->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this case?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 lg:px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-folder-open text-4xl mb-4 text-gray-300 dark:text-gray-600"></i>
                                <p class="text-lg font-medium mb-2">No cases found</p>
                                <p class="text-sm">Get started by creating your first case.</p>
                                <button onclick="openCaseModal()" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    Create Your First Case
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($cases->hasPages())
        <div class="bg-white dark:bg-gray-800 px-4 lg:px-6 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $cases->links() }}
        </div>
        @endif
    </div>
</div>


<script>
    // Filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const courtFilter = document.getElementById('courtFilter');
        const statusFilter = document.getElementById('statusFilter');
        const dateFilter = document.getElementById('dateFilter');
        
        function applyFilters() {
            const courtValue = courtFilter.value;
            const statusValue = statusFilter.value;
            const dateValue = dateFilter.value;
            
            // You can implement AJAX filtering here or form submission
            console.log('Filters applied:', { court: courtValue, status: statusValue, date: dateValue });
        }
        
        courtFilter.addEventListener('change', applyFilters);
        statusFilter.addEventListener('change', applyFilters);
        dateFilter.addEventListener('input', applyFilters);
    });

    function exportCases() {
        // Implement export functionality
        alert('Export functionality will be implemented here');
    }
</script>
@endsection