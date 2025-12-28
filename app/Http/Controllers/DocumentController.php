<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Case_Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Start query
        $query = Document::query();
        
        // Apply user restrictions
        if ($user->role !== 'admin') {
            $query->where(function($q) use ($user) {
                $q->where('uploaded_by', $user->id)
                  ->orWhereHas('case', function($caseQuery) use ($user) {
                      $caseQuery->where('user_id', $user->id);
                  });
            });
        }
        
        // Eager load relationships
        $query->with(['case', 'uploader']);
        
        // Apply filters
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('category') && $request->category != 'all' && $request->category != '') {
            $query->where('category', $request->category);
        }
        
        if ($request->has('case_id') && $request->case_id != '') {
            $query->where('case_id', $request->case_id);
        }
        
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->has('type') && $request->type != 'all') {
            switch ($request->type) {
                case 'with_case':
                    $query->whereNotNull('case_id');
                    break;
                case 'without_case':
                    $query->whereNull('case_id');
                    break;
            }
        }
        
        // Get unique categories for filter
        $categoriesQuery = clone $query;
        $categories = $categoriesQuery->select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->filter()
            ->values();
        
        // Get user's cases for filter
        if ($user->role === 'admin') {
            $cases = Case_Model::select('id', 'case_number', 'case_title')->get();
        } else {
            $cases = Case_Model::where('user_id', $user->id)
                ->select('id', 'case_number', 'case_title')
                ->get();
        }
        
        // Sort and paginate
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $query->orderBy($sortBy, $sortOrder);
        $documents = $query->paginate(15)->withQueryString();
        
        // Get statistics
        $statsQuery = clone $query;
        $totalDocuments = $statsQuery->count();
        $totalSize = $statsQuery->sum('size');
        $recentDocuments = Document::whereIn('id', $statsQuery->pluck('id'))
            ->latest()
            ->take(5)
            ->get();
        
        return view('documents.index', compact(
            'documents',
            'categories',
            'cases',
            'totalDocuments',
            'totalSize',
            'recentDocuments',
            'sortBy',
            'sortOrder'
        ));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Get cases for dropdown
        if ($user->role === 'admin') {
            $cases = Case_Model::select('id', 'case_number', 'case_title')->get();
        } else {
            $cases = Case_Model::where('user_id', $user->id)
                ->select('id', 'case_number', 'case_title')
                ->get();
        }
        
        // Document categories
        $categories = [
            'pleading' => 'Pleading',
            'evidence' => 'Evidence',
            'affidavit' => 'Affidavit',
            'motion' => 'Motion',
            'order' => 'Order',
            'judgment' => 'Judgment',
            'decree' => 'Decree',
            'notice' => 'Notice',
            'summons' => 'Summons',
            'warrant' => 'Warrant',
            'contract' => 'Contract',
            'agreement' => 'Agreement',
            'deed' => 'Deed',
            'will' => 'Will',
            'power_of_attorney' => 'Power of Attorney',
            'correspondence' => 'Correspondence',
            'email' => 'Email',
            'letter' => 'Letter',
            'research' => 'Legal Research',
            'memo' => 'Memo',
            'brief' => 'Brief',
            'opinion' => 'Legal Opinion',
            'report' => 'Report',
            'invoice' => 'Invoice',
            'receipt' => 'Receipt',
            'fir' => 'FIR',
            'charge_sheet' => 'Charge Sheet',
            'bail_application' => 'Bail Application',
            'other' => 'Other'
        ];
        
        // Allowed file types
        $allowedTypes = [
            'pdf' => 'PDF Documents',
            'doc' => 'Word Documents (DOC)',
            'docx' => 'Word Documents (DOCX)',
            'xls' => 'Excel Files (XLS)',
            'xlsx' => 'Excel Files (XLSX)',
            'ppt' => 'PowerPoint (PPT)',
            'pptx' => 'PowerPoint (PPTX)',
            'txt' => 'Text Files',
            'jpg' => 'JPEG Images',
            'jpeg' => 'JPEG Images',
            'png' => 'PNG Images',
            'gif' => 'GIF Images',
            'zip' => 'ZIP Archives',
            'rar' => 'RAR Archives',
        ];
        
        return view('documents.create', compact('cases', 'categories', 'allowedTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'case_id' => 'nullable|exists:cases,id',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string|max:2000',
            'tags' => 'nullable|string|max:500',
            'document' => 'required|file|max:51200', // 50MB max
            'access_level' => 'nullable|in:public,private,confidential',
        ]);
        
        // Check if user has access to the case
        if ($request->filled('case_id')) {
            $case = Case_Model::find($request->case_id);
            $user = Auth::user();
            
            if ($user->role !== 'admin' && $case->user_id !== $user->id) {
                return back()->with('error', 'You do not have permission to upload documents for this case.')->withInput();
            }
        }
        
        try {
            $file = $request->file('document');
            
            // Validate file type
            $allowedMimes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'text/plain',
                'image/jpeg',
                'image/png',
                'image/gif',
                'application/zip',
                'application/x-rar-compressed',
            ];
            
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                return back()->with('error', 'File type not allowed.')->withInput();
            }
            
            // Generate unique filename
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileName = 'doc_' . time() . '_' . Str::random(8) . '.' . $extension;
            
            // Store file
            $path = $file->storeAs('documents', $fileName, 'public');
            
            // Parse tags
            $tags = $request->filled('tags') ? array_map('trim', explode(',', $request->tags)) : [];
            
            // Create document record
            $document = Document::create([
                'case_id' => $request->case_id,
                'name' => $request->name,
                'file_name' => $fileName,
                'original_name' => $originalName,
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => $extension,
                'category' => $request->category,
                'description' => $request->description,
                'tags' => $tags,
                'access_level' => $request->access_level ?? 'private',
                'uploaded_by' => Auth::id(),
                'metadata' => [
                    'original_name' => $originalName,
                    'uploaded_by' => Auth::user()->name,
                    'uploaded_at' => now()->toDateTimeString(),
                    'file_size' => $this->formatBytes($file->getSize()),
                    'ip_address' => $request->ip(),
                ]
            ]);
            
            return redirect()->route('documents.index')
                ->with('success', 'Document uploaded successfully!');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to upload document: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Document $document)
    {
        // Check authorization
        $user = Auth::user();
        if (!$document->canAccess($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        $document->load(['case', 'uploader']);
        
        // Get related documents
        $relatedDocuments = Document::where('id', '!=', $document->id)
            ->where(function($query) use ($document) {
                $query->where('case_id', $document->case_id)
                      ->orWhere('category', $document->category)
                      ->orWhere('uploaded_by', $document->uploaded_by);
            })
            ->latest()
            ->take(5)
            ->get();
        
        // Get document statistics
        $stats = [
            'total_views' => 0, // You can add a view counter in the future
            'download_count' => 0, // You can add a download counter
            'last_accessed' => $document->updated_at->format('M d, Y H:i'),
        ];
        
        return view('documents.show', compact('document', 'relatedDocuments', 'stats'));
    }

    public function edit(Document $document)
    {
        // Check authorization
        $user = Auth::user();
        if ($user->role !== 'admin' && $document->uploaded_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $document->load(['case']);
        
        // Get cases for dropdown
        if ($user->role === 'admin') {
            $cases = Case_Model::select('id', 'case_number', 'case_title')->get();
        } else {
            $cases = Case_Model::where('user_id', $user->id)
                ->select('id', 'case_number', 'case_title')
                ->get();
        }
        
        // Document categories
        $categories = [
            'pleading' => 'Pleading',
            'evidence' => 'Evidence',
            'affidavit' => 'Affidavit',
            'motion' => 'Motion',
            'order' => 'Order',
            'judgment' => 'Judgment',
            'decree' => 'Decree',
            'notice' => 'Notice',
            'summons' => 'Summons',
            'warrant' => 'Warrant',
            'contract' => 'Contract',
            'agreement' => 'Agreement',
            'deed' => 'Deed',
            'will' => 'Will',
            'power_of_attorney' => 'Power of Attorney',
            'correspondence' => 'Correspondence',
            'email' => 'Email',
            'letter' => 'Letter',
            'research' => 'Legal Research',
            'memo' => 'Memo',
            'brief' => 'Brief',
            'opinion' => 'Legal Opinion',
            'report' => 'Report',
            'invoice' => 'Invoice',
            'receipt' => 'Receipt',
            'fir' => 'FIR',
            'charge_sheet' => 'Charge Sheet',
            'bail_application' => 'Bail Application',
            'other' => 'Other'
        ];
        
        // Access levels
        $accessLevels = [
            'public' => 'Public (Anyone can view)',
            'private' => 'Private (Only you and case owner)',
            'confidential' => 'Confidential (Only you)',
        ];
        
        // Prepare tags for display
        $tags = $document->tags ? implode(', ', $document->tags) : '';
        
        return view('documents.edit', compact('document', 'cases', 'categories', 'accessLevels', 'tags'));
    }

    public function update(Request $request, Document $document)
    {
        // Check authorization
        $user = Auth::user();
        if ($user->role !== 'admin' && $document->uploaded_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'case_id' => 'nullable|exists:cases,id',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string|max:2000',
            'tags' => 'nullable|string|max:500',
            'access_level' => 'required|in:public,private,confidential',
            'new_document' => 'nullable|file|max:51200', // Optional new file upload
        ]);
        
        // Check if user has access to the case
        if ($request->filled('case_id') && $request->case_id != $document->case_id) {
            $case = Case_Model::find($request->case_id);
            if ($user->role !== 'admin' && $case->user_id !== $user->id) {
                return back()->with('error', 'You do not have permission to assign documents to this case.')->withInput();
            }
        }
        
        try {
            // Parse tags
            $tags = $request->filled('tags') ? array_map('trim', explode(',', $request->tags)) : [];
            
            // Handle file update if new file is uploaded
            if ($request->hasFile('new_document')) {
                $file = $request->file('new_document');
                
                // Validate file type
                $allowedMimes = [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'text/plain',
                    'image/jpeg',
                    'image/png',
                    'image/gif',
                    'application/zip',
                    'application/x-rar-compressed',
                ];
                
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    return back()->with('error', 'File type not allowed.')->withInput();
                }
                
                // Delete old file
                if (Storage::disk('public')->exists($document->path)) {
                    Storage::disk('public')->delete($document->path);
                }
                
                // Generate new filename
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = 'doc_' . time() . '_' . Str::random(8) . '.' . $extension;
                
                // Store new file
                $path = $file->storeAs('documents', $fileName, 'public');
                
                // Create new version record
                $newDocument = $document->createNewVersion([
                    'path' => $path,
                    'file_name' => $fileName,
                    'original_name' => $originalName,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'extension' => $extension,
                    'name' => $request->name,
                    'description' => $request->description,
                ]);
                
                // Update current document
                $document->update([
                    'name' => $request->name,
                    'case_id' => $request->case_id,
                    'category' => $request->category,
                    'description' => $request->description,
                    'tags' => $tags,
                    'access_level' => $request->access_level,
                    'file_name' => $fileName,
                    'original_name' => $originalName,
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'extension' => $extension,
                ]);
            } else {
                // Just update metadata
                $document->update([
                    'name' => $request->name,
                    'case_id' => $request->case_id,
                    'category' => $request->category,
                    'description' => $request->description,
                    'tags' => $tags,
                    'access_level' => $request->access_level,
                ]);
            }
            
            return redirect()->route('documents.index')
                ->with('success', 'Document updated successfully!');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update document: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Document $document)
    {
        // Check authorization
        $user = Auth::user();
        if ($user->role !== 'admin' && $document->uploaded_by !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($document->path)) {
                Storage::disk('public')->delete($document->path);
            }
            
            // Delete record
            $document->delete();
            
            return redirect()->route('documents.index')
                ->with('success', 'Document deleted successfully!');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete document: ' . $e->getMessage());
        }
    }

    public function download(Document $document)
    {
        // Check authorization
        $user = Auth::user();
        if (!$document->canAccess($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        if (!Storage::disk('public')->exists($document->path)) {
            return back()->with('error', 'File not found.');
        }
        
        // Increment download count (you can add this field to documents table)
        // $document->increment('download_count');
        
        return Storage::disk('public')->download($document->path, $document->original_name);
    }

    public function preview(Document $document)
    {
        // Check authorization
        $user = Auth::user();
        if (!$document->canAccess($user)) {
            abort(403, 'Unauthorized action.');
        }
        
        if (!Storage::disk('public')->exists($document->path)) {
            return back()->with('error', 'File not found.');
        }
        
        $filePath = Storage::disk('public')->path($document->path);
        $mimeType = $document->mime_type;
        
        // Check if file is viewable in browser
        $viewableTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'text/plain',
            'text/html',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];
        
        if (in_array($mimeType, $viewableTypes)) {
            // For images, serve directly
            if (str_starts_with($mimeType, 'image/')) {
                return response()->file($filePath);
            }
            
            // For PDFs and documents
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $document->original_name . '"'
            ]);
        }
        
        // For non-viewable files, offer download
        return Storage::disk('public')->download($document->path, $document->original_name);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array|min:1',
            'document_ids.*' => 'exists:documents,id'
        ]);
        
        $user = Auth::user();
        $deletedCount = 0;
        $failedCount = 0;
        
        foreach ($request->document_ids as $documentId) {
            $document = Document::find($documentId);
            
            // Check authorization
            if ($user->role !== 'admin' && $document->uploaded_by !== $user->id) {
                $failedCount++;
                continue;
            }
            
            try {
                // Delete file from storage
                if (Storage::disk('public')->exists($document->path)) {
                    Storage::disk('public')->delete($document->path);
                }
                
                // Delete record
                $document->delete();
                $deletedCount++;
                
            } catch (\Exception $e) {
                $failedCount++;
                \Log::error('Failed to delete document ' . $documentId . ': ' . $e->getMessage());
            }
        }
        
        $message = $deletedCount . ' document(s) deleted successfully.';
        if ($failedCount > 0) {
            $message .= ' Failed to delete ' . $failedCount . ' document(s).';
        }
        
        return redirect()->route('documents.index')
            ->with($failedCount > 0 ? 'warning' : 'success', $message);
    }

    public function bulkDownload(Request $request)
    {
        $request->validate([
            'document_ids' => 'required|array|min:1',
            'document_ids.*' => 'exists:documents,id'
        ]);
        
        $user = Auth::user();
        $documents = Document::whereIn('id', $request->document_ids)->get();
        $downloadableDocs = [];
        
        foreach ($documents as $document) {
            // Check authorization
            if ($document->canAccess($user) && Storage::disk('public')->exists($document->path)) {
                $downloadableDocs[] = $document;
            }
        }
        
        if (empty($downloadableDocs)) {
            return back()->with('error', 'No documents available for download.');
        }
        
        if (count($downloadableDocs) === 1) {
            $document = $downloadableDocs[0];
            return Storage::disk('public')->download($document->path, $document->original_name);
        }
        
        // For multiple files, create a zip archive
        $zipFileName = 'documents_' . time() . '.zip';
        $zipPath = storage_path('app/public/temp/' . $zipFileName);
        
        // Ensure temp directory exists
        $tempDir = storage_path('app/public/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($downloadableDocs as $document) {
                $filePath = Storage::disk('public')->path($document->path);
                $safeName = preg_replace('/[^A-Za-z0-9\.\_\-]/', '_', $document->original_name);
                $zip->addFile($filePath, $safeName);
            }
            $zip->close();
        } else {
            return back()->with('error', 'Failed to create zip archive.');
        }
        
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function getDocumentStats()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            $query = Document::query();
        } else {
            $query = Document::where('uploaded_by', $user->id)
                ->orWhereHas('case', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
        }
        
        $total = $query->count();
        $totalSize = $query->sum('size');
        
        $byCategory = $query->selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->pluck('count', 'category')
            ->toArray();
        
        $recent = $query->latest()->take(5)->get(['id', 'name', 'category', 'created_at', 'size']);
        
        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'total_size' => $this->formatBytes($totalSize),
                'total_size_bytes' => $totalSize,
                'by_category' => $byCategory,
                'recent' => $recent,
                'storage_usage' => $this->calculateStorageUsage($totalSize),
            ]
        ]);
    }

    public function searchDocuments(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);
        
        $user = Auth::user();
        $query = Document::query();
        
        // Apply user restrictions
        if ($user->role !== 'admin') {
            $query->where(function($q) use ($user) {
                $q->where('uploaded_by', $user->id)
                  ->orWhereHas('case', function($caseQuery) use ($user) {
                      $caseQuery->where('user_id', $user->id);
                  });
            });
        }
        
        $search = $request->query;
        $documents = $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhereJsonContains('tags', $search)
                  ->orWhereHas('case', function($caseQuery) use ($search) {
                      $caseQuery->where('case_number', 'like', "%{$search}%")
                               ->orWhere('case_title', 'like', "%{$search}%");
                  });
            })
            ->with(['case:id,case_number,case_title'])
            ->latest()
            ->paginate(10);
        
        return view('documents.search', compact('documents', 'search'));
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    private function calculateStorageUsage($totalSize)
    {
        $maxStorage = 10737418240; // 10GB in bytes
        
        $percentage = ($totalSize / $maxStorage) * 100;
        $remaining = $maxStorage - $totalSize;
        
        return [
            'used' => $this->formatBytes($totalSize),
            'total' => $this->formatBytes($maxStorage),
            'remaining' => $this->formatBytes($remaining),
            'percentage' => round($percentage, 2),
            'is_near_limit' => $percentage > 80,
        ];
    }
}