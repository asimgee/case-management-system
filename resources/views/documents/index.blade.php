@extends('layouts.app')

@section('title', 'Document Library - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-2">Document Library</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage all your legal documents in one place</p>
        </div>
        <div class="flex space-x-3 mt-4 lg:mt-0">
            <a href="{{ route('documents.create') }}" class="btn-primary">
                <i class="fas fa-upload mr-2"></i>Upload Document
            </a>
            <button onclick="showBulkActions()" class="btn-secondary">
                <i class="fas fa-tasks mr-2"></i>Bulk Actions
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Documents</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalDocuments }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-alt text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Size</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        @php
                            function formatBytes($bytes, $precision = 2) {
                                $units = ['B', 'KB', 'MB', 'GB', 'TB'];
                                $bytes = max($bytes, 0);
                                $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                                $pow = min($pow, count($units) - 1);
                                $bytes /= pow(1024, $pow);
                                return round($bytes, $precision) . ' ' . $units[$pow];
                            }
                        @endphp
                        {{ formatBytes($totalSize) }}
                    </h3>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-database text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Recent Uploads</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $recentDocuments->count() }}</h3>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-history text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Categories</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $categories->count() }}</h3>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                    <i class="fas fa-folder text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card p-4 lg:p-6 mb-6">
        <form id="filterForm" method="GET" action="{{ route('documents.index') }}">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search documents..." 
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                    <select name="category" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="all">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Case</label>
                    <select name="case_id" class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">All Cases</option>
                        @foreach($cases as $case)
                            <option value="{{ $case->id }}" {{ request('case_id') == $case->id ? 'selected' : '' }}>
                                {{ $case->case_number }} - {{ $case->first_party_title }} vs {{ $case->second_party_title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end space-x-3">
                    <button type="submit" class="btn-primary flex-1">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('documents.index') }}" class="btn-secondary">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Upload Area -->
    <div class="card border-2 border-dashed border-gray-300 dark:border-gray-600 p-8 text-center mb-6 hover:border-blue-400 dark:hover:border-blue-500 transition-colors cursor-pointer"
         onclick="window.location.href='{{ route('documents.create') }}'">
        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
        <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">Drag & Drop files here</p>
        <p class="text-gray-500 dark:text-gray-400 mb-4">or click to browse</p>
        <a href="{{ route('documents.create') }}" class="btn-primary inline-block">
            <i class="fas fa-folder-open mr-2"></i>Upload Documents
        </a>
    </div>

    <!-- Document Grid -->
    @if($documents->count() > 0)
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    All Documents ({{ $documents->total() }})
                </h2>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Showing {{ $documents->firstItem() }}-{{ $documents->lastItem() }} of {{ $documents->total() }}
                    </span>
                    <select id="sortBy" class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-3 py-1.5">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="name_asc">Name (A-Z)</option>
                        <option value="name_desc">Name (Z-A)</option>
                        <option value="size_asc">Size (Smallest)</option>
                        <option value="size_desc">Size (Largest)</option>
                    </select>
                </div>
            </div>

            <!-- Bulk Actions Bar -->
            <div id="bulkActionsBar" class="hidden bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="selectAllCheckbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="selectAllCheckbox" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">
                                <span id="selectedCount">0</span> selected
                            </label>
                        </div>
                        <button onclick="downloadSelected()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                            <i class="fas fa-download mr-2"></i>Download Selected
                        </button>
                        <button onclick="deleteSelected()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition-colors">
                            <i class="fas fa-trash mr-2"></i>Delete Selected
                        </button>
                    </div>
                    <button onclick="hideBulkActions()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-6">
                @foreach($documents as $document)
                    @php
                        $colorClass = [
                            'red' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
                            'blue' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                            'green' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                            'yellow' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400',
                            'purple' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
                            'pink' => 'bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400',
                            'gray' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                        ][$document->file_color] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
                    @endphp
                    
                    <div class="card p-4 card-hover group relative" data-document-id="{{ $document->id }}">
                        <!-- Checkbox for bulk selection -->
                        <div class="absolute top-3 right-3 hidden group-hover:block">
                            <input type="checkbox" class="document-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                   data-id="{{ $document->id }}">
                        </div>
                        
                        <div class="flex items-center justify-center w-12 h-12 {{ $colorClass }} rounded-xl mb-3">
                            <i class="{{ $document->file_icon }} text-xl"></i>
                        </div>
                        
                        <h3 class="font-medium text-gray-900 dark:text-white mb-1 truncate" title="{{ $document->name }}">
                            {{ $document->name }}
                        </h3>
                        
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                            <span class="inline-block px-2 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-xs">
                                {{ ucfirst($document->category) }}
                            </span>
                        </p>
                        
                        <div class="text-xs text-gray-400 dark:text-gray-500 mb-3 space-y-1">
                            <div class="flex items-center">
                                <i class="fas fa-file mr-1"></i>
                                {{ strtoupper($document->extension) }} • {{ $document->formatted_size }}
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $document->created_at->format('M d, Y') }}
                            </div>
                            @if($document->case)
                                <div class="flex items-center truncate" title="{{ $document->case->case_number }}">
                                    <i class="fas fa-gavel mr-1"></i>
                                    {{ $document->case->case_number }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="flex space-x-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('documents.preview', $document) }}" 
                               class="flex-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 py-1.5 px-2 rounded-lg text-xs hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors flex items-center justify-center"
                               target="_blank">
                                <i class="fas fa-eye mr-1.5"></i> View
                            </a>
                            <a href="{{ route('documents.download', $document) }}" 
                               class="flex-1 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 py-1.5 px-2 rounded-lg text-xs hover:bg-green-100 dark:hover:bg-green-900/50 transition-colors flex items-center justify-center">
                                <i class="fas fa-download mr-1.5"></i> Download
                            </a>
                            <button onclick="showDeleteModal({{ $document->id }})"
                                    class="flex-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 py-1.5 px-2 rounded-lg text-xs hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors flex items-center justify-center">
                                <i class="fas fa-trash mr-1.5"></i> Delete
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($documents->hasPages())
                <div class="mt-6">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    @else
        <!-- Empty State -->
        <div class="card p-8 text-center">
            <div class="w-20 h-20 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-folder-open text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">No documents found</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                @if(request()->hasAny(['search', 'category', 'case_id']))
                    No documents match your current filters. Try adjusting your search criteria.
                @else
                    You haven't uploaded any documents yet. Start by uploading your first document.
                @endif
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('documents.create') }}" class="btn-primary">
                    <i class="fas fa-upload mr-2"></i>Upload Your First Document
                </a>
                @if(request()->hasAny(['search', 'category', 'case_id']))
                    <a href="{{ route('documents.index') }}" class="btn-secondary">
                        <i class="fas fa-redo mr-2"></i>Clear Filters
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Delete Document</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-700 dark:text-gray-300 mb-6">
                Are you sure you want to delete this document? This action cannot be undone.
            </p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeleteModal()" 
                            class="px-4 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-colors">
                        Delete Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div id="bulkDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Delete Selected Documents</h3>
        </div>
        <div class="p-6">
            <p class="text-gray-700 dark:text-gray-300 mb-6">
                Are you sure you want to delete <span id="bulkDeleteCount" class="font-semibold">0</span> selected documents? This action cannot be undone.
            </p>
            <form id="bulkDeleteForm" method="POST" action="{{ route('documents.bulk-delete') }}">
                @csrf
                <div id="bulkDeleteInputs"></div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeBulkDeleteModal()" 
                            class="px-4 py-2.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-colors">
                        Delete Selected
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Document selection for bulk actions
let selectedDocuments = new Set();

function showBulkActions() {
    document.getElementById('bulkActionsBar').classList.remove('hidden');
}

function hideBulkActions() {
    document.getElementById('bulkActionsBar').classList.add('hidden');
    selectedDocuments.clear();
    updateSelectedCount();
    document.querySelectorAll('.document-checkbox').forEach(cb => cb.checked = false);
}

// Handle document checkbox changes
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('document-checkbox')) {
        const documentId = e.target.dataset.id;
        
        if (e.target.checked) {
            selectedDocuments.add(documentId);
        } else {
            selectedDocuments.delete(documentId);
        }
        
        updateSelectedCount();
        
        // Show/hide bulk actions bar
        if (selectedDocuments.size > 0) {
            document.getElementById('bulkActionsBar').classList.remove('hidden');
        } else {
            document.getElementById('bulkActionsBar').classList.add('hidden');
        }
    }
    
    // Handle select all checkbox
    if (e.target.id === 'selectAllCheckbox') {
        const checkboxes = document.querySelectorAll('.document-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = e.target.checked;
            const documentId = cb.dataset.id;
            
            if (e.target.checked) {
                selectedDocuments.add(documentId);
            } else {
                selectedDocuments.delete(documentId);
            }
        });
        
        updateSelectedCount();
        
        if (selectedDocuments.size > 0) {
            document.getElementById('bulkActionsBar').classList.remove('hidden');
        } else {
            document.getElementById('bulkActionsBar').classList.add('hidden');
        }
    }
});

function updateSelectedCount() {
    document.getElementById('selectedCount').textContent = selectedDocuments.size;
}

// Delete single document
function showDeleteModal(documentId) {
    const form = document.getElementById('deleteForm');
    form.action = `/documents/${documentId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Bulk delete
function deleteSelected() {
    if (selectedDocuments.size === 0) {
        alert('Please select documents to delete.');
        return;
    }
    
    const form = document.getElementById('bulkDeleteForm');
    const inputsContainer = document.getElementById('bulkDeleteInputs');
    inputsContainer.innerHTML = '';
    
    selectedDocuments.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'document_ids[]';
        input.value = id;
        inputsContainer.appendChild(input);
    });
    
    document.getElementById('bulkDeleteCount').textContent = selectedDocuments.size;
    document.getElementById('bulkDeleteModal').classList.remove('hidden');
}

function closeBulkDeleteModal() {
    document.getElementById('bulkDeleteModal').classList.add('hidden');
}

// Bulk download
function downloadSelected() {
    if (selectedDocuments.size === 0) {
        alert('Please select documents to download.');
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("documents.bulk-download") }}';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    selectedDocuments.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'document_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Sort functionality
document.getElementById('sortBy').addEventListener('change', function(e) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', e.target.value);
    window.location.href = url.toString();
});

// Set current sort value
const urlParams = new URLSearchParams(window.location.search);
const sortParam = urlParams.get('sort') || 'newest';
document.getElementById('sortBy').value = sortParam;

// Close modals when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

document.getElementById('bulkDeleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeBulkDeleteModal();
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
        closeBulkDeleteModal();
    }
});
</script>

<style>
.card-hover {
    transition: all 0.2s ease-in-out;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.document-checkbox {
    transition: all 0.2s;
}

.group:hover .document-checkbox {
    display: block !important;
}

/* Custom scrollbar for document grid */
.grid::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.grid::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.grid::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.grid::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.dark .grid::-webkit-scrollbar-track {
    background: #374151;
}

.dark .grid::-webkit-scrollbar-thumb {
    background: #6b7280;
}

.dark .grid::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>
@endsection