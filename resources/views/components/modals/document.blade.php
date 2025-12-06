<!-- Document Upload Modal -->
<div id="documentUploadModal" class="modal">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl mx-4 fixed-height-450 scrollable">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Upload Document</h2>
                <button onclick="closeDocumentUploadModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <form onsubmit="submitDocument(event)" class="space-y-4">
                <div>
                    <label for="documentFile" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select File</label>
                    <input type="file" id="documentFile" required class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label for="documentName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document Name</label>
                    <input type="text" id="documentName" required class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label for="documentCategory" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                    <select id="documentCategory" required class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                        <option value="">Select Category</option>
                        <option value="legal">Legal Documents</option>
                        <option value="evidence">Evidence</option>
                        <option value="court">Court Documents</option>
                        <option value="statements">Statements</option>
                    </select>
                </div>
                <div>
                    <label for="documentDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea id="documentDescription" rows="3" class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeDocumentUploadModal()" class="btn-secondary">
                        Cancel
                    </button>
                    <button type="button" onclick="resetDocumentForm()" class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 px-4 py-2 rounded-xl hover:bg-yellow-200 dark:hover:bg-yellow-900/50 transition-colors">
                        Reset
                    </button>
                    <button type="submit" class="btn-primary">
                        Upload Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>