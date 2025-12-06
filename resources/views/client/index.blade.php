@extends('layouts.app')

@section('title', 'Clients - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 mobile-space-y-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Clients</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your clients and their information</p>
        </div>
        <button onclick="openClientModal()" class="btn-primary mobile-full-width">
            <i class="fas fa-plus mr-2"></i>Add New Client
        </button>
    </div>

    <!-- Filters -->
    <div class="card p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-col sm:flex-row gap-4 flex-1">
                <div class="flex-1">
                    <input type="text" id="searchInput" placeholder="Search clients..." 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                           onkeyup="filterClients()">
                </div>
                <select id="typeFilter" onchange="filterClients()" 
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">All Types</option>
                    <option value="individual">Individual</option>
                    <option value="corporate">Corporate</option>
                </select>
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Total: {{ $clients->total() }} clients
            </div>
        </div>
    </div>

    <!-- Clients Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-6" id="clientsGrid">
        @forelse($clients as $client)
        <div class="card p-6 card-hover client-card" data-type="{{ $client->type }}" data-name="{{ strtolower($client->name) }}">
            <div class="flex items-center space-x-4 mb-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-semibold 
                    {{ $client->type === 'corporate' ? 'bg-green-500' : 'bg-blue-500' }}">
                    {{ $client->initials }}
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $client->name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ App\Http\Controllers\ClientController::getTypeColor($client->type) }}">
                            {{ ucfirst($client->type) }}
                        </span>
                    </p>
                </div>
            </div>
            
            <div class="space-y-2 mb-4">
                @if($client->contact_number)
                <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                    <i class="fas fa-phone mr-2 w-4"></i>
                    <span class="truncate">{{ $client->contact_number }}</span>
                </p>
                @endif
                
                @if($client->email)
                <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                    <i class="fas fa-envelope mr-2 w-4"></i>
                    <span class="truncate">{{ $client->email }}</span>
                </p>
                @endif
                
                <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                    <i class="fas fa-folder mr-2 w-4"></i>
                    {{ $client->active_cases_count }} Active Cases
                </p>

                @if($client->company_name)
                <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                    <i class="fas fa-building mr-2 w-4"></i>
                    <span class="truncate">{{ $client->company_name }}</span>
                </p>
                @endif
            </div>

            <div class="flex space-x-2">
                <button onclick="openClientModal({{ $client->id }})" 
                        class="flex-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 py-2 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors text-sm font-medium">
                    <i class="fas fa-edit mr-1"></i>Edit
                </button>
                <a href="{{ route('clients.show', $client) }}" 
                   class="flex-1 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 py-2 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/50 transition-colors text-sm font-medium text-center">
                    <i class="fas fa-eye mr-1"></i>View
                </a>
                <button onclick="deleteClient({{ $client->id }}, '{{ addslashes($client->name) }}')" 
                        class="flex-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 py-2 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors text-sm font-medium">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="card p-12 text-center">
                <i class="fas fa-users text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Clients Found</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Get started by adding your first client.</p>
                <button onclick="openClientModal()" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Add New Client
                </button>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($clients->hasPages())
    <div class="mt-6">
        {{ $clients->links() }}
    </div>
    @endif
</div>

<!-- Include Client Modal -->
  @include('components.modals.client')

<script>
function filterClients() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const typeFilter = document.getElementById('typeFilter').value;
    const clientCards = document.querySelectorAll('.client-card');
    
    clientCards.forEach(card => {
        const clientName = card.getAttribute('data-name');
        const clientType = card.getAttribute('data-type');
        
        const matchesSearch = clientName.includes(searchTerm);
        const matchesType = !typeFilter || clientType === typeFilter;
        
        if (matchesSearch && matchesType) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
@endsection