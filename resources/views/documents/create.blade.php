@extends('layouts.app')

@section('title', 'Upload Document - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-2">Upload Document</h1>
            <p class="text-gray-600 dark:text-gray-400">Upload new documents to your library</p>
        </div>
        <a href="{{ route('documents.index') }}" class="btn-secondary mt-4 lg:mt-0">
            <i class="fas fa-arrow-left mr-2"></i>Back to Documents
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Upload Form -->
        <div class="lg:col-span-2">
            <div class="card p-4 lg:p-6">
                <form id="uploadForm" method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- File Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Document File <span class="text-red-500">*</span>
                            </label>
                            <div id="dropArea" class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors cursor-pointer">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
                                <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2" id="fileName">
                                    Drag & Drop files here or click to browse
                                </p>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">
                                    Max file size: 50MB • Supported formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, JPG, PNG, ZIP
                                </p>
                                <input type="file" name="document" id="fileInput" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.zip,.rar" required>
                                <button type="button" onclick="document.getElementById('fileInput').click()" class="btn-primary">
                                    <i class="fas fa-folder-open mr-2"></i>Choose File
                                </button>
                                <div id="fileInfo" class="mt-4 hidden">
                                    <div class="flex items-center justify-center space-x-2 text-sm">
                                        <i class="fas fa-file text-blue-500"></i>
                                        <span class="font-medium text-gray-900 dark:text-white" id="selectedFileName"></span>
                                        <span class="text-gray-500" id="selectedFileSize"></span>
                                    </div>
                                </div>
                            </div>
                            @error('document')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Document Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Document Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" 
                                   class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                   placeholder="Enter document name" required>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Case Selection -->
                        <div>
                            <label for="case_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Associate with Case (Optional)
                            </label>
                            <select id="case_id" name="case_id" 
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                <option value="">Select a case (Optional)</option>
                                @foreach($cases as $case)
                                    <option value="{{ $case->id }}" {{ old('case_id') == $case->id ? 'selected' : '' }}>
                                        {{ $case->case_number }} - {{ $case->first_party_title }} vs {{ $case->second_party_title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('case_id')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select id="category" name="category" 
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" required>
                                <option value="">Select category</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description (Optional)
                            </label>
                            <textarea id="description" name="description" rows="3"
                                      class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors resize-none"
                                      placeholder="Add a description for this document">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="mb-4 sm:mb-0">
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Documents are securely stored and encrypted
                                </div>
                            </div>
                            <div class="flex space-x-3">
                                <a href="{{ route('documents.index') }}" class="btn-secondary">
                                    Cancel
                                </a>
                                <button type="submit" class="btn-primary">
                                    <i class="fas fa-upload mr-2"></i>Upload Document
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Upload Tips -->
        <div>
            <div class="card p-4 lg:p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>Upload Tips
                </h3>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Use descriptive names for easy searching</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Associate documents with cases for better organization</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Add descriptions to provide context</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-0.5 mr-2"></i>
                        <span class="text-sm text-gray-700 dark:text-gray-300">Maximum file size: 50MB per document</span>
                    </li>
                </ul>
            </div>

            <!-- Recent Uploads -->
            @if(isset($recentDocuments) && $recentDocuments->count() > 0)
                <div class="card p-4 lg:p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-history text-blue-500 mr-2"></i>Recent Uploads
                    </h3>
                    <div class="space-y-3">
                        @foreach($recentDocuments as $doc)
                            <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <div class="w-8 h-8 {{ [
                                    'red' => 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
                                    'blue' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                                    'green' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                                    'gray' => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                                ][$doc->file_color] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }} rounded-lg flex items-center justify-center mr-3">
                                    <i class="{{ $doc->file_icon }} text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $doc->name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $doc->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// File upload handling
const dropArea = document.getElementById('dropArea');
const fileInput = document.getElementById('fileInput');
const fileName = document.getElementById('fileName');
const selectedFileName = document.getElementById('selectedFileName');
const selectedFileSize = document.getElementById('selectedFileSize');
const fileInfo = document.getElementById('fileInfo');
const nameInput = document.getElementById('name');

// Prevent default drag behaviors
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, preventDefaults, false);
    document.body.addEventListener(eventName, preventDefaults, false);
});

// Highlight drop area when item is dragged over it
['dragenter', 'dragover'].forEach(eventName => {
    dropArea.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, unhighlight, false);
});

// Handle dropped files
dropArea.addEventListener('drop', handleDrop, false);

// Handle file input change
fileInput.addEventListener('change', handleFileSelect, false);

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function highlight() {
    dropArea.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
    dropArea.classList.remove('border-gray-300', 'dark:border-gray-600');
}

function unhighlight() {
    dropArea.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
    dropArea.classList.add('border-gray-300', 'dark:border-gray-600');
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    
    if (files.length > 0) {
        fileInput.files = files;
        handleFileSelect();
    }
}

function handleFileSelect() {
    const file = fileInput.files[0];
    
    if (file) {
        // Update file info display
        fileName.textContent = 'File selected';
        selectedFileName.textContent = file.name;
        selectedFileSize.textContent = `(${formatBytes(file.size)})`;
        fileInfo.classList.remove('hidden');
        
        // Auto-fill name input if empty
        if (!nameInput.value) {
            // Remove extension from filename for name field
            const nameWithoutExt = file.name.replace(/\.[^/.]+$/, "");
            nameInput.value = nameWithoutExt;
        }
        
        // Validate file size
        const maxSize = 50 * 1024 * 1024; // 50MB in bytes
        if (file.size > maxSize) {
            alert('File size exceeds 50MB limit. Please choose a smaller file.');
            fileInput.value = '';
            resetFileDisplay();
        }
        
        // Validate file type
        const allowedExtensions = ['.pdf', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.txt', '.jpg', '.jpeg', '.png', '.zip', '.rar'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        
        if (!allowedExtensions.includes(fileExtension)) {
            alert('File type not allowed. Please upload a supported file type.');
            fileInput.value = '';
            resetFileDisplay();
        }
    } else {
        resetFileDisplay();
    }
}

function resetFileDisplay() {
    fileName.textContent = 'Drag & Drop files here or click to browse';
    fileInfo.classList.add('hidden');
    fileInput.value = '';
}

function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

// Form validation
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    const file = fileInput.files[0];
    const name = nameInput.value.trim();
    const category = document.getElementById('category').value;
    
    if (!file) {
        e.preventDefault();
        alert('Please select a file to upload.');
        dropArea.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    
    if (!name) {
        e.preventDefault();
        alert('Please enter a document name.');
        nameInput.focus();
        return;
    }
    
    if (!category) {
        e.preventDefault();
        alert('Please select a category.');
        document.getElementById('category').focus();
        return;
    }
});

// Add loading state to submit button
document.getElementById('uploadForm').addEventListener('submit', function() {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
    submitBtn.disabled = true;
});
</script>
@endsection