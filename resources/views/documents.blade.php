@extends('layouts.app')

@section('title', 'Document Library - AI Legal Assistant')

@section('content')
<div class="fade-in">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 mobile-space-y-4">
        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Document Library</h1>
        <button onclick="openDocumentUploadModal()" class="btn-primary mobile-full-width">
            <i class="fas fa-upload mr-2"></i>Upload Document
        </button>
    </div>

    <!-- Upload Area -->
    <div class="card border-2 border-dashed border-gray-300 dark:border-gray-600 p-8 text-center mb-6 hover:border-blue-400 dark:hover:border-blue-500 transition-colors cursor-pointer" onclick="openDocumentUploadModal()">
        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 dark:text-gray-500 mb-4"></i>
        <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">Drag & Drop files here</p>
        <p class="text-gray-500 dark:text-gray-400 mb-4">or click to browse</p>
        <button class="btn-primary">
            <i class="fas fa-folder-open mr-2"></i>Choose Files
        </button>
    </div>

    <!-- Document Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
        <div class="card p-4 card-hover">
            <div class="flex items-center justify-center w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl mb-3">
                <i class="fas fa-file-pdf text-red-600 dark:text-red-400 text-xl"></i>
            </div>
            <h3 class="font-medium text-gray-900 dark:text-white mb-1">Contract_Agreement.pdf</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Legal Documents</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Jan 15, 2024</p>
            <div class="flex space-x-2">
                <button class="flex-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 py-1 px-2 rounded text-xs hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                    <i class="fas fa-download mr-1"></i>Download
                </button>
                <button class="flex-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 py-1 px-2 rounded text-xs hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </div>
        </div>

        <div class="card p-4 card-hover">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl mb-3">
                <i class="fas fa-file-word text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <h3 class="font-medium text-gray-900 dark:text-white mb-1">Case_Evidence.docx</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Evidence</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Jan 12, 2024</p>
            <div class="flex space-x-2">
                <button class="flex-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 py-1 px-2 rounded text-xs hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                    <i class="fas fa-download mr-1"></i>Download
                </button>
                <button class="flex-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 py-1 px-2 rounded text-xs hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </div>
        </div>

        <div class="card p-4 card-hover">
            <div class="flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl mb-3">
                <i class="fas fa-file-pdf text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <h3 class="font-medium text-gray-900 dark:text-white mb-1">Court_Filing.pdf</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Court Documents</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Jan 10, 2024</p>
            <div class="flex space-x-2">
                <button class="flex-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 py-1 px-2 rounded text-xs hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                    <i class="fas fa-download mr-1"></i>Download
                </button>
                <button class="flex-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 py-1 px-2 rounded text-xs hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </div>
        </div>

        <div class="card p-4 card-hover">
            <div class="flex items-center justify-center w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl mb-3">
                <i class="fas fa-file-word text-purple-600 dark:text-purple-400 text-xl"></i>
            </div>
            <h3 class="font-medium text-gray-900 dark:text-white mb-1">Client_Statement.docx</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Statements</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Jan 8, 2024</p>
            <div class="flex space-x-2">
                <button class="flex-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 py-1 px-2 rounded text-xs hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                    <i class="fas fa-download mr-1"></i>Download
                </button>
                <button class="flex-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 py-1 px-2 rounded text-xs hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </div>
        </div>

        <div class="card p-4 card-hover">
            <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl mb-3">
                <i class="fas fa-file-excel text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <h3 class="font-medium text-gray-900 dark:text-white mb-1">Financial_Records.xlsx</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Financial</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Jan 5, 2024</p>
            <div class="flex space-x-2">
                <button class="flex-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 py-1 px-2 rounded text-xs hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                    <i class="fas fa-download mr-1"></i>Download
                </button>
                <button class="flex-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 py-1 px-2 rounded text-xs hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </div>
        </div>

        <div class="card p-4 card-hover">
            <div class="flex items-center justify-center w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl mb-3">
                <i class="fas fa-file-powerpoint text-indigo-600 dark:text-indigo-400 text-xl"></i>
            </div>
            <h3 class="font-medium text-gray-900 dark:text-white mb-1">Case_Presentation.pptx</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Presentations</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Jan 3, 2024</p>
            <div class="flex space-x-2">
                <button class="flex-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 py-1 px-2 rounded text-xs hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                    <i class="fas fa-download mr-1"></i>Download
                </button>
                <button class="flex-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 py-1 px-2 rounded text-xs hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </div>
        </div>

        <div class="card p-4 card-hover">
            <div class="flex items-center justify-center w-12 h-12 bg-pink-100 dark:bg-pink-900/30 rounded-xl mb-3">
                <i class="fas fa-file-image text-pink-600 dark:text-pink-400 text-xl"></i>
            </div>
            <h3 class="font-medium text-gray-900 dark:text-white mb-1">Evidence_Photos.zip</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Evidence</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Dec 28, 2023</p>
            <div class="flex space-x-2">
                <button class="flex-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 py-1 px-2 rounded text-xs hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                    <i class="fas fa-download mr-1"></i>Download
                </button>
                <button class="flex-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 py-1 px-2 rounded text-xs hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </div>
        </div>

        <div class="card p-4 card-hover">
            <div class="flex items-center justify-center w-12 h-12 bg-teal-100 dark:bg-teal-900/30 rounded-xl mb-3">
                <i class="fas fa-file-contract text-teal-600 dark:text-teal-400 text-xl"></i>
            </div>
            <h3 class="font-medium text-gray-900 dark:text-white mb-1">Settlement_Agreement.pdf</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Legal Documents</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Dec 25, 2023</p>
            <div class="flex space-x-2">
                <button class="flex-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 py-1 px-2 rounded text-xs hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                    <i class="fas fa-download mr-1"></i>Download
                </button>
                <button class="flex-1 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 py-1 px-2 rounded text-xs hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>
@endsection