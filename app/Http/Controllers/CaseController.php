<?php

namespace App\Http\Controllers;

use App\Models\Case_Model;
use App\Models\Client;
use App\Models\Document;
use App\Models\CaseType;
use App\Models\CaseRemedy;
use App\Models\CourtType;
use App\Models\Lawyer;
use App\Models\User;
// use App\Models\Hearing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;

class CaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role === 'admin') {
            $query = Case_Model::with(['user', 'caseType', 'caseRemedy', 'courtType', 'assignedLawyer', 'additionalLawyer', 'documents', 'clients']);
        } else {
            $query = Case_Model::where('user_id', Auth::id())
                ->with(['caseType', 'caseRemedy', 'courtType', 'assignedLawyer', 'additionalLawyer', 'documents', 'clients']);
        }
        
        // Apply filters
        if ($request->has('court') && $request->court != '') {
            $query->where('court_name', 'like', '%' . $request->court . '%');
        }
        
        if ($request->has('status') && $request->status != '') {
            $query->where('case_status', $request->status);
        }
        
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('filing_date', $request->date);
        }

        if ($request->has('case_type') && $request->case_type != '') {
            $query->where('case_type_id', $request->case_type);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('case_number', 'like', "%{$search}%")
                  ->orWhere('case_title', 'like', "%{$search}%")
                  ->orWhere('first_party_name', 'like', "%{$search}%")
                  ->orWhere('second_party_name', 'like', "%{$search}%");
            });
        }

        if (Auth::user()->role === 'admin' && $request->has('user') && $request->user != '') {
            $query->where('user_id', $request->user);
        }
        
        $cases = $query->latest()->paginate(10);
        $users = Auth::user()->role === 'admin' ? User::all() : collect();
        $caseTypes = CaseType::where('is_custom', false)
            ->orWhere(function($query) {
                $query->where('is_custom', true)
                      ->where('user_id', Auth::id());
            })->get();

        $courtTypes = CourtType::where('is_custom', false)
            ->orWhere(function($query) {
                $query->where('is_custom', true)
                      ->where('user_id', Auth::id());
            })->get();

        $lawyers = Lawyer::where('is_custom', false)
            ->orWhere(function($query) {
                $query->where('is_custom', true)
                      ->where('user_id', Auth::id());
            })->get();

        $remedies = CaseRemedy::where('is_custom', false)
            ->orWhere(function($query) {
                $query->where('is_custom', true)
                      ->where('user_id', Auth::id());
            })->get();

        return view('cases.index', compact('cases', 'users', 'caseTypes', 'courtTypes', 'lawyers', 'remedies'));
    }

    public function create()
    {
        $caseTypes = CaseType::where('is_custom', false)
            ->orWhere(function($query) {
                $query->where('is_custom', true)
                      ->where('user_id', Auth::id());
            })->get();

        $courtTypes = CourtType::where('is_custom', false)
            ->orWhere(function($query) {
                $query->where('is_custom', true)
                      ->where('user_id', Auth::id());
            })->get();

        $lawyers = Lawyer::where('is_custom', false)
            ->orWhere(function($query) {
                $query->where('is_custom', true)
                      ->where('user_id', Auth::id());
            })->get();

        $remedies = CaseRemedy::where('is_custom', false)
            ->orWhere(function($query) {
                $query->where('is_custom', true)
                      ->where('user_id', Auth::id());
            })->get();

        return view('cases.create', compact('caseTypes', 'courtTypes', 'lawyers', 'remedies'));
    }

    public function getRemediesByType($caseTypeId)
    {
        $caseType = CaseType::find($caseTypeId);
        if (!$caseType) {
            return response()->json([]);
        }

        $remedies = CaseRemedy::where('is_custom', false)
            ->orWhere(function($query) {
                $query->where('is_custom', true)
                      ->where('user_id', Auth::id());
            })
            ->when($caseType->slug, function($query) use ($caseType) {
                // Map case type to remedy category
                $categoryMap = [
                    'civil' => 'civil',
                    'criminal' => 'criminal',
                    'family' => 'family',
                    'commercial' => 'civil',
                    'constitutional' => 'civil',
                    'corporate' => 'civil'
                ];
                
                $category = $categoryMap[$caseType->slug] ?? 'civil';
                return $query->where('category', $category);
            })
            ->get();

        return response()->json($remedies);
    }

    public function storeCaseType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:case_types,name'
        ]);

        $caseType = CaseType::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_custom' => true,
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'caseType' => $caseType,
            'message' => 'Case type created successfully!'
        ]);
    }

    public function storeCaseRemedy(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:case_remedies,name',
            'category' => 'required|string|max:50'
        ]);

        $caseRemedy = CaseRemedy::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'category' => $request->category,
            'description' => $request->description,
            'is_custom' => true,
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'caseRemedy' => $caseRemedy,
            'message' => 'Case remedy created successfully!'
        ]);
    }

    public function storeCourtType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:court_types,name'
        ]);

        $courtType = CourtType::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_custom' => true,
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'courtType' => $courtType,
            'message' => 'Court type created successfully!'
        ]);
    }

    public function storeLawyer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:lawyers,email',
            'phone' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50'
        ]);

        $lawyer = Lawyer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'license_number' => $request->license_number,
            'address' => $request->address,
            'specialization' => $request->specialization,
            'is_custom' => true,
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'lawyer' => $lawyer,
            'message' => 'Lawyer created successfully!'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_party_title' => 'required|string|max:255',
            'second_party_title' => 'required|string|max:255',
            'first_party_name' => 'required|string|max:255',
            'first_party_contact' => 'nullable|string|max:20',
            'first_party_cnic' => 'nullable|string|max:15',
            'first_party_address' => 'nullable|string',
            'second_party_name' => 'required|string|max:255',
            'second_party_contact' => 'nullable|string|max:20',
            'second_party_cnic' => 'nullable|string|max:15',
            'second_party_address' => 'nullable|string',
            'case_type_id' => 'required|exists:case_types,id',
            'case_remedy_id' => 'required|exists:case_remedies,id',
            'court_type_id' => 'required|exists:court_types,id',
            'court_name' => 'required|string|max:255',
            'case_status' => 'required|in:pending,in_hearing,closed',
            'filing_date' => 'required|date',
            'next_date' => 'nullable|date',
            'judgment_date' => 'nullable|date',
           'client_type' => 'required|in:1,2',
            'offending_lawyer' => 'nullable|string|max:255',
            'next_order' => 'nullable|string',
            'remarks' => 'nullable|string',
            'fir_no' => 'nullable|string|max:50',
            'fir_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'offence' => 'nullable|string|max:255',
            'police_station' => 'nullable|string|max:255',
            'other_fir_details' => 'nullable|string',
            'documents.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,txt',
            'document_category' => 'nullable|string|max:50',
            'document_description' => 'nullable|string',
        ]);

        // Start database transaction
        DB::beginTransaction();

        try {
            // Generate case number
            $caseCount = Case_Model::where('user_id', Auth::id())->count() + 1;
            $caseNumber = Case_Model::generateCaseNumber(Auth::id());

            // Create case
            $case = Case_Model::create([
                'case_number' => $caseNumber,
                'case_title' => $validated['first_party_title'] . ' vs ' . $validated['second_party_title'],
                'first_party_title' => $validated['first_party_title'],
                'second_party_title' => $validated['second_party_title'],
                'first_party_name' => $validated['first_party_name'],
                'first_party_contact' => $validated['first_party_contact'],
                'first_party_cnic' => $validated['first_party_cnic'],
                'first_party_address' => $validated['first_party_address'],
                'first_party_council' => null,
                'second_party_name' => $validated['second_party_name'],
                'second_party_contact' => $validated['second_party_contact'],
                'second_party_cnic' => $validated['second_party_cnic'],
                'second_party_address' => $validated['second_party_address'],
                'second_party_council' => null,
                'case_type_id' => $validated['case_type_id'],
                'client_type' => $validated['client_type'],
                'case_remedy_id' => $validated['case_remedy_id'],
                'court_type_id' => $validated['court_type_id'],
                'court_name' => $validated['court_name'],
                'case_status' => $validated['case_status'],
                'filing_date' => $validated['filing_date'],
                'next_date' => $validated['next_date'],
                'judgment_date' => $validated['judgment_date'],
                'offending_lawyer' => $validated['offending_lawyer'] ?? null,
                'next_order' => $validated['next_order'] ?? null,
                'case_notes' => $validated['remarks'] ?? null,
                'fir_no' => $validated['fir_no'] ?? null,
                'fir_year' => $validated['fir_year'] ?? null,
                'offence' => $validated['offence'] ?? null,
                'police_station' => $validated['police_station'] ?? null,
                'other_fir_details' => $validated['other_fir_details'] ?? null,
                'user_id' => Auth::id()
            ]);

            // Create First Party Client
            Client::create([
                'name' => $validated['first_party_name'],
                'contact_number' => $validated['first_party_contact'],
                'cnic' => $validated['first_party_cnic'],
                'council_for' => 'plaintiff',
                'address' => $validated['first_party_address'],
                'type' => 'first_party',
                'case_id' => $case->id,
                'user_id' => Auth::id()
            ]);

            // Create Second Party Client
            Client::create([
                'name' => $validated['second_party_name'],
                'contact_number' => $validated['second_party_contact'],
                'cnic' => $validated['second_party_cnic'],
                'council_for' => 'defendant',
                'address' => $validated['second_party_address'],
                'type' => 'second_party',
                'case_id' => $case->id,
                'user_id' => Auth::id()
            ]);

            // Create Other Parties as Clients
            if ($request->has('other_parties')) {
                foreach ($request->other_parties as $otherParty) {
                    if (!empty($otherParty['name'])) {
                        Client::create([
                            'name' => $otherParty['name'],
                            'contact_number' => $otherParty['contact'] ?? null,
                            'address' => $otherParty['address'] ?? null,
                            'council_for' => $otherParty['role'] ?? 'other',
                            'cnic' => null,
                            'type' => 'other',
                            'case_id' => $case->id,
                            'user_id' => Auth::id()
                        ]);
                    }
                }
            }

            // Handle document uploads and store in documents table
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $originalName = $document->getClientOriginalName();
                    $fileName = time() . '_' . Str::random(10) . '_' . $originalName;
                    $path = $document->storeAs('case-documents', $fileName, 'public');
                    
                    Document::create([
                        'case_id' => $case->id,
                        'name' => $originalName,
                        'original_name' => $originalName,
                        'file_name' => $fileName,
                        'path' => $path,
                        'size' => $document->getSize(),
                        'mime_type' => $document->getClientMimeType(),
                        'extension' => $document->getClientOriginalExtension(),
                        'category' => $request->document_category ?? 'other',
                        'description' => $request->document_description ?? null,
                        'uploaded_by' => Auth::id(),
                        'metadata' => [
                            'uploaded_at' => now()->toDateTimeString(),
                            'uploaded_by' => Auth::user()->name,
                        ]
                    ]);
                }
            }
            $hearingController = new HearingController();
            $hearingController->createFirstHearingForCase($case, $request);
            DB::commit();

            return redirect()->route('cases.index')->with('success', 'Case created successfully! Case Number: ' . $caseNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create case: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Case_Model $case)
    {
        // Authorization
        if (Auth::user()->role !== 'admin' && $case->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $case->load([
            'caseType', 
            'caseRemedy', 
            'courtType', 
            'assignedLawyer', 
            'additionalLawyer', 
            'clients', 
            'documents.uploader',
            'hearings'
        ]);
        
        // Get case statistics
        $caseStats = [
            'total_documents' => $case->documents()->count(),
            'total_hearings'  => $case->hearings()->count(),
            'total_clients'   => $case->clients()->count(),
            'upcoming_hearing'=> $case->hearings()
                                        ->whereDate('hearing_date', '>=', now())
                                        ->orderBy('hearing_date')
                                        ->first(),
        ];
        
        
        return view('cases.show', compact('case', 'caseStats'));
    }

    public function edit(Case_Model $case)
    {
        // Authorization
        if (Auth::user()->role !== 'admin' && $case->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $caseTypes = CaseType::where('is_custom', false)
            ->orWhere(function($query) {
                $query->where('is_custom', true)
                      ->where('user_id', Auth::id());
            })->get();

        $courtTypes = CourtType::where('is_custom', false)
            ->orWhere(function($query) {
                $query->where('is_custom', true)
                      ->where('user_id', Auth::id());
            })->get();

        $lawyers = Lawyer::where('is_custom', false)
            ->orWhere(function($query) {
                $query->where('is_custom', true)
                      ->where('user_id', Auth::id());
            })->get();

        $remedies = CaseRemedy::where('is_custom', false)
            ->orWhere(function($query) {
                $query->where('is_custom', true)
                      ->where('user_id', Auth::id());
            })->get();

        // Load existing other parties from clients
        $otherParties = $case->clients()->where('type', 'other')->get();
        $documents = $case->documents;
        $hearings = $case->hearings()->orderBy('hearing_date', 'desc')->get();

        return view('cases.edit', compact('case', 'caseTypes', 'courtTypes', 'lawyers', 'remedies', 'otherParties', 'documents', 'hearings'));
    }

    public function update(Request $request, Case_Model $case)
    {
        // Authorization
        if (Auth::user()->role !== 'admin' && $case->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'first_party_title' => 'required|string|max:255',
            'second_party_title' => 'required|string|max:255',
            'first_party_name' => 'required|string|max:255',
            'first_party_contact' => 'nullable|string|max:20',
            'first_party_cnic' => 'nullable|string|max:15',
            'first_party_address' => 'nullable|string',
            'second_party_name' => 'required|string|max:255',
            'second_party_contact' => 'nullable|string|max:20',
            'second_party_cnic' => 'nullable|string|max:15',
            'second_party_address' => 'nullable|string',
            'case_type_id' => 'required|exists:case_types,id',
            'case_remedy_id' => 'required|exists:case_remedies,id',
            'court_type_id' => 'required|exists:court_types,id',
            'court_name' => 'required|string|max:255',
            'case_status' => 'required|in:pending,in_hearing,closed',
            'filing_date' => 'required|date',
            'next_date' => 'nullable|date',
            'judgment_date' => 'nullable|date',
           'client_type' => 'nullable|in:1,2',
            'offending_lawyer' => 'nullable|string|max:255',
            'next_order' => 'nullable|string',
            'remarks' => 'nullable|string',
            'fir_no' => 'nullable|string|max:50',
            'fir_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'offence' => 'nullable|string|max:255',
            'police_station' => 'nullable|string|max:255',
            'other_fir_details' => 'nullable|string',
            'documents.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,txt',
            'document_category' => 'nullable|string|max:50',
            'document_description' => 'nullable|string',
        ]);

        // Start database transaction
        DB::beginTransaction();

        try {
            $case->update([
                'case_title' => $validated['first_party_title'] . ' vs ' . $validated['second_party_title'],
                'first_party_title' => $validated['first_party_title'],
                'second_party_title' => $validated['second_party_title'],
                'first_party_name' => $validated['first_party_name'],
                'first_party_contact' => $validated['first_party_contact'],
                'first_party_cnic' => $validated['first_party_cnic'],
                'first_party_address' => $validated['first_party_address'],
                'second_party_name' => $validated['second_party_name'],
                'second_party_contact' => $validated['second_party_contact'],
                'second_party_cnic' => $validated['second_party_cnic'],
                'second_party_address' => $validated['second_party_address'],
                'case_type_id' => $validated['case_type_id'],
                'case_remedy_id' => $validated['case_remedy_id'],
                'court_type_id' => $validated['court_type_id'],
                'court_name' => $validated['court_name'],
                'case_status' => $validated['case_status'],
                'filing_date' => $validated['filing_date'],
                'next_date' => $validated['next_date'],
                'judgment_date' => $validated['judgment_date'],
                'client_type' => $validated['client_type'],
                'offending_lawyer' => $validated['offending_lawyer'] ?? null,
                'next_order' => $validated['next_order'] ?? null,
                'case_notes' => $validated['remarks'] ?? null,
                'fir_no' => $validated['fir_no'] ?? null,
                'fir_year' => $validated['fir_year'] ?? null,
                'offence' => $validated['offence'] ?? null,
                'police_station' => $validated['police_station'] ?? null,
                'other_fir_details' => $validated['other_fir_details'] ?? null,
            ]);

            // Update First Party Client
            $firstPartyClient = $case->clients()->where('type', 'first_party')->first();
            if ($firstPartyClient) {
                $firstPartyClient->update([
                    'name' => $validated['first_party_name'],
                    'contact_number' => $validated['first_party_contact'],
                    'cnic' => $validated['first_party_cnic'],
                    'address' => $validated['first_party_address'],
                ]);
            } else {
                // Create if doesn't exist
                Client::create([
                    'name' => $validated['first_party_name'],
                    'contact_number' => $validated['first_party_contact'],
                    'cnic' => $validated['first_party_cnic'],
                    'council_for' => 'plaintiff',
                    'address' => $validated['first_party_address'],
                    'type' => 'first_party',
                    'case_id' => $case->id,
                    'user_id' => Auth::id()
                ]);
            }
    
            // Update Second Party Client
            $secondPartyClient = $case->clients()->where('type', 'second_party')->first();
            if ($secondPartyClient) {
                $secondPartyClient->update([
                    'name' => $validated['second_party_name'],
                    'contact_number' => $validated['second_party_contact'],
                    'cnic' => $validated['second_party_cnic'],
                    'address' => $validated['second_party_address'],
                ]);
            } else {
                // Create if doesn't exist
                Client::create([
                    'name' => $validated['second_party_name'],
                    'contact_number' => $validated['second_party_contact'],
                    'cnic' => $validated['second_party_cnic'],
                    'council_for' => 'defendant',
                    'address' => $validated['second_party_address'],
                    'type' => 'second_party',
                    'case_id' => $case->id,
                    'user_id' => Auth::id()
                ]);
            }
    
            // Update Other Parties
            $case->clients()->where('type', 'other')->delete();
            if ($request->has('other_parties')) {
                foreach ($request->other_parties as $otherParty) {
                    if (!empty($otherParty['name'])) {
                        Client::create([
                            'name' => $otherParty['name'],
                            'contact_number' => $otherParty['contact'] ?? null,
                            'address' => $otherParty['address'] ?? null,
                            'council_for' => $otherParty['role'] ?? 'other',
                            'type' => 'other',
                            'case_id' => $case->id,
                            'user_id' => Auth::id()
                        ]);
                    }
                }
            }

            // Handle new document uploads
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $originalName = $document->getClientOriginalName();
                    $fileName = time() . '_' . Str::random(10) . '_' . $originalName;
                    $path = $document->storeAs('case-documents', $fileName, 'public');
                    
                    Document::create([
                        'case_id' => $case->id,
                        'name' => $originalName,
                        'original_name' => $originalName,
                        'file_name' => $fileName,
                        'path' => $path,
                        'size' => $document->getSize(),
                        'mime_type' => $document->getClientMimeType(),
                        'extension' => $document->getClientOriginalExtension(),
                        'category' => $request->document_category ?? 'other',
                        'description' => $request->document_description ?? null,
                        'uploaded_by' => Auth::id(),
                        'metadata' => [
                            'uploaded_at' => now()->toDateTimeString(),
                            'uploaded_by' => Auth::user()->name,
                        ]
                    ]);
                }
            }
            if ($request->has('next_date') && $request->next_date != $case->next_date) {
                $upcomingHearing = $case->getUpcomingHearingAttribute();
                if ($upcomingHearing) {
                    $upcomingHearing->update([
                        'hearing_date' => $request->next_date,
                    ]);
                } else {
                    // Create new hearing if none exists
                    $hearingController = new HearingController();
                    $hearingController->createFirstHearingForCase($case, $request);
                }
            }
            
            DB::commit();

            return redirect()->route('cases.index')->with('success', 'Case updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update case: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Case_Model $case)
    {
        // Authorization
        if (Auth::user()->role !== 'admin' && $case->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        DB::beginTransaction();

        try {
            // Delete associated documents from storage and database
            foreach ($case->documents as $document) {
                Storage::disk('public')->delete($document->path);
                $document->delete();
            }
            
            // Delete associated clients
            $case->clients()->delete();
            
            // Delete associated hearings
            $case->hearings()->delete();
            
            // Delete the case
            $case->delete();

            DB::commit();

            return redirect()->route('cases.index')->with('success', 'Case deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete case: ' . $e->getMessage());
        }
    }

    public function downloadDocumentByIndex(Case_Model $case, $index)
    {
        // Authorization
        if (Auth::user()->role !== 'admin' && $case->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $documents = $case->documents;
        if (isset($documents[$index])) {
            $document = $documents[$index];
            return $this->downloadDocumentFile($document);
        }

        return back()->with('error', 'Document not found.');
    }

    public function removeDocumentByIndex(Case_Model $case, $index)
    {
        // Authorization
        if (Auth::user()->role !== 'admin' && $case->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $documents = $case->documents;
        if (isset($documents[$index])) {
            $document = $documents[$index];
            return $this->deleteDocument($document);
        }

        return back()->with('error', 'Document not found.');
    }

    public function downloadDocument(Document $document)
    {
        // Authorization
        $case = $document->case;
        if (Auth::user()->role !== 'admin' && $case->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return $this->downloadDocumentFile($document);
    }

    public function removeDocument(Document $document)
    {
        // Authorization
        $case = $document->case;
        if (Auth::user()->role !== 'admin' && $case->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return $this->deleteDocument($document);
    }

    private function downloadDocumentFile(Document $document)
    {
        if (Storage::disk('public')->exists($document->path)) {
            return Storage::disk('public')->download($document->path, $document->original_name ?? $document->name);
        }

        return back()->with('error', 'Document not found on server.');
    }

    private function deleteDocument(Document $document)
    {
        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($document->path)) {
                Storage::disk('public')->delete($document->path);
            }
            
            // Delete record from database
            $document->delete();

            return back()->with('success', 'Document removed successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove document: ' . $e->getMessage());
        }
    }

    public static function getStatusColor($status)
    {
        $colors = [
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'in_hearing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400'
        ];

        return $colors[$status] ?? $colors['pending'];
    }

    public function getCaseStatistics()
    {
        $userId = Auth::id();
        $isAdmin = Auth::user()->role === 'admin';
        
        $query = $isAdmin ? Case_Model::query() : Case_Model::where('user_id', $userId);
        
        $stats = [
            'total' => $query->count(),
            'pending' => $query->where('case_status', 'pending')->count(),
            'in_hearing' => $query->where('case_status', 'in_hearing')->count(),
            'closed' => $query->where('case_status', 'closed')->count(),
            'recent' => $query->latest()->take(5)->get(),
        ];
        
        return response()->json($stats);
    }
}