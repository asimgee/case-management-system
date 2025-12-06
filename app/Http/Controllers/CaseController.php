<?php

namespace App\Http\Controllers;

use App\Models\Case_Model;
use App\Models\Client;
use App\Models\CaseType;
use App\Models\CaseRemedy;
use App\Models\CourtType;
use App\Models\Lawyer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CaseController extends Controller
{
    public function index(Request $request)
    {
        // Check if user is admin
        if (Auth::user()->role === 'admin') {
            $query = Case_Model::with(['user', 'caseType', 'caseRemedy', 'courtType', 'assignedLawyer']);
        } else {
            $query = Case_Model::where('user_id', Auth::id())
                ->with(['caseType', 'caseRemedy', 'courtType', 'assignedLawyer']);
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
        return view('cases.index', compact('cases', 'users','caseTypes', 'courtTypes', 'lawyers'));
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

        return view('cases.create', compact('caseTypes', 'courtTypes', 'lawyers'));
    }

    public function getRemediesByType($caseType)
    {
        $remedies = CaseRemedy::where('is_custom', false)
            ->orWhere(function($query) {
                $query->where('is_custom', true)
                      ->where('user_id', Auth::id());
            })
            ->when($caseType, function($query) use ($caseType) {
                // Map case type to remedy category
                $categoryMap = [
                    'civil' => 'civil',
                    'criminal' => 'criminal',
                    'family' => 'family',
                    'commercial' => 'civil',
                    'constitutional' => 'civil',
                    'corporate' => 'civil'
                ];
                
                $category = $categoryMap[$caseType] ?? 'civil';
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
            'is_custom' => true,
            'user_id' => Auth::id()
        ]);

        return response()->json($caseType);
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
            'is_custom' => true,
            'user_id' => Auth::id()
        ]);

        return response()->json($caseRemedy);
    }

    public function storeCourtType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:court_types,name'
        ]);

        $courtType = CourtType::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'is_custom' => true,
            'user_id' => Auth::id()
        ]);

        return response()->json($courtType);
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
            'is_custom' => true,
            'user_id' => Auth::id()
        ]);

        return response()->json($lawyer);
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
            'first_party_council' => 'nullable|string|max:255',
            'second_party_name' => 'required|string|max:255',
            'second_party_contact' => 'nullable|string|max:20',
            'second_party_cnic' => 'nullable|string|max:15',
            'second_party_address' => 'nullable|string',
            'second_party_council' => 'nullable|string|max:255',
            'case_type_id' => 'required|exists:case_types,id',
            'case_remedy_id' => 'required|exists:case_remedies,id',
            'court_type_id' => 'required|exists:court_types,id',
            'court_name' => 'required|string|max:255',
            'case_status' => 'required|in:pending,in_hearing,closed',
            'filing_date' => 'required|date',
            'next_date' => 'nullable|date',
            'judgment_date' => 'nullable|date',
            'assigned_lawyer_id' => 'nullable|exists:lawyers,id',
            'additional_lawyer_id' => 'nullable|exists:lawyers,id',
            'offending_lawyer' => 'nullable|string|max:255',
            'opponent_council' => 'nullable|string|max:255',
            'power_of_attorney' => 'nullable|string|max:255',
            'next_order' => 'nullable|string',
            'remarks' => 'nullable|string',
            'case_notes' => 'nullable|string',
            'fir_no' => 'nullable|string|max:50',
            'fir_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'offence' => 'nullable|string|max:255',
            'police_station' => 'nullable|string|max:255',
            'other_fir_details' => 'nullable|string',
            'documents.*' => 'nullable|file|max:10240',
        ]);

        // Handle document uploads
        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $fileName = time() . '_' . Str::random(10) . '.' . $document->getClientOriginalExtension();
                $path = $document->storeAs('case-documents', $fileName, 'public');
                $documents[] = [
                    'name' => $document->getClientOriginalName(),
                    'path' => $path,
                    'size' => $document->getSize(),
                    'type' => $document->getClientMimeType(),
                    'category' => $request->document_category ?? 'other'
                ];
            }
        }

        // Handle other parties
        $otherParties = [];
        if ($request->has('other_parties')) {
            $otherParties = $request->other_parties;
        }

        try {
            // Generate case number
            $caseCount = Case_Model::where('user_id', Auth::id())->count() + 1;
            $caseNumber = 'CASE-' . date('Y') . '-' . str_pad($caseCount, 4, '0', STR_PAD_LEFT);

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
                'first_party_council' => $validated['first_party_council'],
                'second_party_name' => $validated['second_party_name'],
                'second_party_contact' => $validated['second_party_contact'],
                'second_party_cnic' => $validated['second_party_cnic'],
                'second_party_address' => $validated['second_party_address'],
                'second_party_council' => $validated['second_party_council'],
                'case_type_id' => $validated['case_type_id'],
                'case_remedy_id' => $validated['case_remedy_id'],
                'court_type_id' => $validated['court_type_id'],
                'court_name' => $validated['court_name'],
                'case_status' => $validated['case_status'],
                'filing_date' => $validated['filing_date'],
                'next_date' => $validated['next_date'],
                'judgment_date' => $validated['judgment_date'],
                'assigned_lawyer_id' => $validated['assigned_lawyer_id'],
                'additional_lawyer_id' => $validated['additional_lawyer_id'],
                'offending_lawyer' => $validated['offending_lawyer'],
                'opponent_council' => $validated['opponent_council'],
                'power_of_attorney' => $validated['power_of_attorney'],
                'next_order' => $validated['next_order'],
                'remarks' => $validated['remarks'],
                'case_notes' => $validated['case_notes'],
                'fir_no' => $validated['fir_no'],
                'fir_year' => $validated['fir_year'],
                'offence' => $validated['offence'],
                'police_station' => $validated['police_station'],
                'other_fir_details' => $validated['other_fir_details'],
                'documents' => $documents,
                'other_parties' => $otherParties,
                'user_id' => Auth::id()
            ]);
            // Save First Party as Client
            Client::create([
                'name' => $validated['first_party_name'],
                'contact_number' => $validated['first_party_contact'],
                'cnic' => $validated['first_party_cnic'],
                'council_for' => $validated['first_party_council'],
                'address' => $validated['first_party_address'],
                'type' => 'first_party',
                'case_id' => $case->id,
                'user_id' => Auth::id()
            ]);
    
            // Save Second Party as Client
            Client::create([
                'name' => $validated['second_party_name'],
                'contact_number' => $validated['second_party_contact'],
                'cnic' => $validated['second_party_cnic'],
                'council_for' => $validated['second_party_council'],
                'address' => $validated['second_party_address'],
                'type' => 'second_party',
                'case_id' => $case->id,
                'user_id' => Auth::id()
            ]);
    
            // Save Other Parties as Clients
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


            return redirect()->route('cases.index')->with('success', 'Case created successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create case: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Case_Model $case)
    {
        // Admin can view any case, regular users can only view their own
        if (Auth::user()->role !== 'admin' && $case->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        return view('cases.show', compact('case'));
    }

    public function edit(Case_Model $case)
    {
        // Admin can edit any case, regular users can only edit their own
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

        return view('cases.edit', compact('case', 'caseTypes', 'courtTypes', 'lawyers'));
    }

    public function update(Request $request, Case_Model $case)
    {
        // Admin can update any case, regular users can only update their own
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
            'first_party_council' => 'nullable|string|max:255',
            'second_party_name' => 'required|string|max:255',
            'second_party_contact' => 'nullable|string|max:20',
            'second_party_cnic' => 'nullable|string|max:15',
            'second_party_address' => 'nullable|string',
            'second_party_council' => 'nullable|string|max:255',
            'case_type_id' => 'required|exists:case_types,id',
            'case_remedy_id' => 'required|exists:case_remedies,id',
            'court_type_id' => 'required|exists:court_types,id',
            'court_name' => 'required|string|max:255',
            'case_status' => 'required|in:pending,in_hearing,closed',
            'filing_date' => 'required|date',
            'next_date' => 'nullable|date',
            'judgment_date' => 'nullable|date',
            'assigned_lawyer_id' => 'nullable|exists:lawyers,id',
            'additional_lawyer_id' => 'nullable|exists:lawyers,id',
            'offending_lawyer' => 'nullable|string|max:255',
            'opponent_council' => 'nullable|string|max:255',
            'power_of_attorney' => 'nullable|string|max:255',
            'next_order' => 'nullable|string',
            'remarks' => 'nullable|string',
            'case_notes' => 'nullable|string',
            'fir_no' => 'nullable|string|max:50',
            'fir_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'offence' => 'nullable|string|max:255',
            'police_station' => 'nullable|string|max:255',
            'other_fir_details' => 'nullable|string',
            'documents.*' => 'nullable|file|max:10240',
        ]);

        // Handle document uploads
        $existingDocuments = $case->documents ?? [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $fileName = time() . '_' . Str::random(10) . '.' . $document->getClientOriginalExtension();
                $path = $document->storeAs('case-documents', $fileName, 'public');
                $existingDocuments[] = [
                    'name' => $document->getClientOriginalName(),
                    'path' => $path,
                    'size' => $document->getSize(),
                    'type' => $document->getClientMimeType(),
                    'category' => $request->document_category ?? 'other'
                ];
            }
        }

        // Handle other parties
        $otherParties = [];
        if ($request->has('other_parties')) {
            $otherParties = $request->other_parties;
        }

        try {
            $case->update([
                'case_title' => $validated['first_party_title'] . ' vs ' . $validated['second_party_title'],
                'first_party_title' => $validated['first_party_title'],
                'second_party_title' => $validated['second_party_title'],
                'first_party_name' => $validated['first_party_name'],
                'first_party_contact' => $validated['first_party_contact'],
                'first_party_cnic' => $validated['first_party_cnic'],
                'first_party_address' => $validated['first_party_address'],
                'first_party_council' => $validated['first_party_council'],
                'second_party_name' => $validated['second_party_name'],
                'second_party_contact' => $validated['second_party_contact'],
                'second_party_cnic' => $validated['second_party_cnic'],
                'second_party_address' => $validated['second_party_address'],
                'second_party_council' => $validated['second_party_council'],
                'case_type_id' => $validated['case_type_id'],
                'case_remedy_id' => $validated['case_remedy_id'],
                'court_type_id' => $validated['court_type_id'],
                'court_name' => $validated['court_name'],
                'case_status' => $validated['case_status'],
                'filing_date' => $validated['filing_date'],
                'next_date' => $validated['next_date'],
                'judgment_date' => $validated['judgment_date'],
                'assigned_lawyer_id' => $validated['assigned_lawyer_id'],
                'additional_lawyer_id' => $validated['additional_lawyer_id'],
                'offending_lawyer' => $validated['offending_lawyer'],
                'opponent_council' => $validated['opponent_council'],
                'power_of_attorney' => $validated['power_of_attorney'],
                'next_order' => $validated['next_order'],
                'remarks' => $validated['remarks'],
                'case_notes' => $validated['case_notes'],
                'fir_no' => $validated['fir_no'],
                'fir_year' => $validated['fir_year'],
                'offence' => $validated['offence'],
                'police_station' => $validated['police_station'],
                'other_fir_details' => $validated['other_fir_details'],
                'documents' => $existingDocuments,
                'other_parties' => $otherParties,
            ]);

            return redirect()->route('cases.index')->with('success', 'Case updated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update case: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Case_Model $case)
    {
        // Admin can delete any case, regular users can only delete their own
        if (Auth::user()->role !== 'admin' && $case->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Delete associated documents from storage
            if ($case->documents) {
                foreach ($case->documents as $document) {
                    Storage::disk('public')->delete($document['path']);
                }
            }

            $case->delete();

            return redirect()->route('cases.index')->with('success', 'Case deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete case: ' . $e->getMessage());
        }
    }

    public function downloadDocument(Case_Model $case, $documentIndex)
    {
        // Admin can download any document, regular users can only download their own
        if (Auth::user()->role !== 'admin' && $case->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $documents = $case->documents;
        if (isset($documents[$documentIndex])) {
            $document = $documents[$documentIndex];
            return Storage::disk('public')->download($document['path'], $document['name']);
        }

        return back()->with('error', 'Document not found.');
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
}