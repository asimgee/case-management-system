<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Case_Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Start query
        $query = Client::query();
        
        // Apply user restrictions
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }
        
        // Apply filters
        if ($request->has('type') && $request->type != '' && $request->type != 'all') {
            $query->where('type', $request->type);
        }
        
        if ($request->has('council_for') && $request->council_for != '' && $request->council_for != 'all') {
            $query->where('council_for', $request->council_for);
        }
        
        if ($request->has('status') && $request->status != '' && $request->status != 'all') {
            $query->where('is_active', $request->status == 'active');
        }
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('contact_number', 'like', '%' . $search . '%')
                  ->orWhere('cnic', 'like', '%' . $search . '%')
                  ->orWhere('company_name', 'like', '%' . $search . '%');
            });
        }
        
        if ($request->has('case_id') && $request->case_id != '') {
            $query->where('case_id', $request->case_id);
        }
        
        // Eager load relationships
        $query->with(['case:id,case_number,case_title', 'user:id,name']);
        
        // Sort and paginate
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $query->orderBy($sortBy, $sortOrder);
        $clients = $query->paginate(15)->withQueryString();
        
        // Get statistics
        $statsQuery = clone $query;
        $totalClients = $statsQuery->count();
        $activeClients = $statsQuery->where('is_active', true)->count();
        
        // Get unique types for filter
        $types = Client::select('type')
            ->distinct()
            ->whereNotNull('type')
            ->pluck('type');
        
        // Get unique council_for values for filter
        $councilTypes = Client::select('council_for')
            ->distinct()
            ->whereNotNull('council_for')
            ->pluck('council_for');
        
        // Get cases for filter
        if ($user->role === 'admin') {
            $cases = Case_Model::select('id', 'case_number', 'case_title')->get();
        } else {
            $cases = Case_Model::where('user_id', $user->id)
                ->select('id', 'case_number', 'case_title')
                ->get();
        }
        
        return view('clients.index', compact(
            'clients',
            'totalClients',
            'activeClients',
            'types',
            'councilTypes',
            'cases',
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
        
        // Client types
        $clientTypes = [
            'individual' => 'Individual',
            'corporate' => 'Corporate/Business',
            'government' => 'Government Agency',
            'ngo' => 'NGO/Non-Profit',
        ];
        
        // Council for options
        $councilOptions = [
            'plaintiff' => 'Plaintiff',
            'defendant' => 'Defendant',
            'complainant' => 'Complainant',
            'respondent' => 'Respondent',
            'petitioner' => 'Petitioner',
            'appellant' => 'Appellant',
            'witness' => 'Witness',
            'expert' => 'Expert Witness',
            'other' => 'Other',
        ];
        
        // Gender options
        $genderOptions = [
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other',
        ];
        
        return view('clients.create', compact(
            'cases',
            'clientTypes',
            'councilOptions',
            'genderOptions'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:clients,email',
            'contact_number' => 'nullable|string|max:20',
            'alternate_contact' => 'nullable|string|max:20',
            'cnic' => 'nullable|string|max:15|unique:clients,cnic',
            'type' => 'required|in:individual,corporate,government,ngo',
            'council_for' => 'required|in:plaintiff,defendant,complainant,respondent,petitioner,appellant,witness,expert,other',
            'address' => 'nullable|string|max:500',
            'company_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'nationality' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:255',
            'education' => 'nullable|string|max:255',
            'income_range' => 'nullable|in:low,medium,high',
            'emergency_contact' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'relationship' => 'nullable|string|max:100',
            'case_id' => 'nullable|exists:cases,id',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        
        // Check if user has access to the case
        if ($request->filled('case_id')) {
            $case = Case_Model::find($request->case_id);
            $user = Auth::user();
            
            if ($user->role !== 'admin' && $case->user_id !== $user->id) {
                return back()->with('error', 'You do not have permission to add clients to this case.')->withInput();
            }
        }

        try {
            // Handle profile picture upload
            $profilePicturePath = null;
            if ($request->hasFile('profile_picture')) {
                $imageName = 'client_' . time() . '_' . Str::random(8) . '.' . $request->profile_picture->extension();
                $path = $request->profile_picture->storeAs('client-profiles', $imageName, 'public');
                $profilePicturePath = $path;
            }

            $client = Client::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'contact_number' => $validated['contact_number'],
                'alternate_contact' => $validated['alternate_contact'],
                'cnic' => $validated['cnic'],
                'type' => $validated['type'],
                'council_for' => $validated['council_for'],
                'address' => $validated['address'],
                'company_name' => $validated['company_name'],
                'designation' => $validated['designation'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'nationality' => $validated['nationality'],
                'occupation' => $validated['occupation'],
                'education' => $validated['education'],
                'income_range' => $validated['income_range'],
                'emergency_contact' => $validated['emergency_contact'],
                'emergency_contact_name' => $validated['emergency_contact_name'],
                'relationship' => $validated['relationship'],
                'case_id' => $validated['case_id'],
                'profile_picture' => $profilePicturePath,
                'notes' => $validated['notes'],
                'is_active' => $validated['is_active'] ?? true,
                'user_id' => Auth::id()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Client created successfully!',
                    'client' => $client->load('case')
                ]);
            }

            return redirect()->route('clients.index')->with('success', 'Client created successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create client: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to create client: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Client $client)
    {
        // Check authorization
        $user = Auth::user();
        if ($user->role !== 'admin' && $client->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $client->load([
            'case' => function($query) {
                $query->with(['caseType', 'courtType', 'assignedLawyer']);
            },
            'user'
        ]);
        
        // Get client's cases if not already loaded
        if (!$client->case) {
            $cases = Case_Model::where(function($query) use ($client) {
                $query->where('first_party_name', $client->name)
                      ->orWhere('second_party_name', $client->name);
            })->with(['caseType', 'courtType'])->get();
            $client->setRelation('cases', $cases);
        }
        
        // Get documents related to client
        $documents = [];
        if ($client->case) {
            $documents = $client->case->documents()->latest()->take(10)->get();
        }
        
        // Get upcoming hearings
        $hearings = [];
        if ($client->case) {
            $hearings = $client->case->hearings()
                ->where('hearing_date', '>=', now())
                ->orderBy('hearing_date')
                ->take(5)
                ->get();
        }
        
        // Get client statistics
        $stats = [
            'total_cases' => $client->cases ? $client->cases->count() : 0,
            'active_cases' => $client->cases ? $client->cases->where('case_status', '!=', 'closed')->count() : 0,
            'total_documents' => count($documents),
            'upcoming_hearings' => count($hearings),
        ];

        return view('clients.show', compact('client', 'documents', 'hearings', 'stats'));
    }

    public function edit(Client $client)
    {
        // Check authorization
        $user = Auth::user();
        if ($user->role !== 'admin' && $client->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $client->load('case');

        // Get cases for dropdown
        if ($user->role === 'admin') {
            $cases = Case_Model::select('id', 'case_number', 'case_title')->get();
        } else {
            $cases = Case_Model::where('user_id', $user->id)
                ->select('id', 'case_number', 'case_title')
                ->get();
        }
        
        // Client types
        $clientTypes = [
            'individual' => 'Individual',
            'corporate' => 'Corporate/Business',
            'government' => 'Government Agency',
            'ngo' => 'NGO/Non-Profit',
        ];
        
        // Council for options
        $councilOptions = [
            'plaintiff' => 'Plaintiff',
            'defendant' => 'Defendant',
            'complainant' => 'Complainant',
            'respondent' => 'Respondent',
            'petitioner' => 'Petitioner',
            'appellant' => 'Appellant',
            'witness' => 'Witness',
            'expert' => 'Expert Witness',
            'other' => 'Other',
        ];
        
        // Gender options
        $genderOptions = [
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other',
        ];
        
        // Income ranges
        $incomeRanges = [
            'low' => 'Low Income',
            'medium' => 'Medium Income',
            'high' => 'High Income',
        ];

        return view('clients.edit', compact(
            'client',
            'cases',
            'clientTypes',
            'councilOptions',
            'genderOptions',
            'incomeRanges'
        ));
    }

    public function update(Request $request, Client $client)
    {
        // Check authorization
        $user = Auth::user();
        if ($user->role !== 'admin' && $client->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('clients')->ignore($client->id)
            ],
            'contact_number' => 'nullable|string|max:20',
            'alternate_contact' => 'nullable|string|max:20',
            'cnic' => [
                'nullable',
                'string',
                'max:15',
                Rule::unique('clients')->ignore($client->id)
            ],
            'type' => 'required|in:individual,corporate,government,ngo',
            'council_for' => 'required|in:plaintiff,defendant,complainant,respondent,petitioner,appellant,witness,expert,other',
            'address' => 'nullable|string|max:500',
            'company_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'nationality' => 'nullable|string|max:100',
            'occupation' => 'nullable|string|max:255',
            'education' => 'nullable|string|max:255',
            'income_range' => 'nullable|in:low,medium,high',
            'emergency_contact' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'relationship' => 'nullable|string|max:100',
            'case_id' => 'nullable|exists:cases,id',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        
        // Check if user has access to the case
        if ($request->filled('case_id') && $request->case_id != $client->case_id) {
            $case = Case_Model::find($request->case_id);
            if ($user->role !== 'admin' && $case->user_id !== $user->id) {
                return back()->with('error', 'You do not have permission to assign clients to this case.')->withInput();
            }
        }

        try {
            // Handle profile picture upload
            if ($request->hasFile('profile_picture')) {
                // Delete old profile picture if exists
                if ($client->profile_picture && Storage::disk('public')->exists($client->profile_picture)) {
                    Storage::disk('public')->delete($client->profile_picture);
                }
                
                $imageName = 'client_' . $client->id . '_' . time() . '.' . $request->profile_picture->extension();
                $path = $request->profile_picture->storeAs('client-profiles', $imageName, 'public');
                $validated['profile_picture'] = $path;
            }

            $client->update($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Client updated successfully!',
                    'client' => $client->load('case')
                ]);
            }

            return redirect()->route('clients.index')->with('success', 'Client updated successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update client: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to update client: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Client $client)
    {
        // Check authorization
        $user = Auth::user();
        if ($user->role !== 'admin' && $client->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Check if client has associated cases
            if ($client->case || $client->cases()->exists()) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete client with associated cases. Please remove client from cases first.'
                    ], 422);
                }
                return back()->with('error', 'Cannot delete client with associated cases. Please remove client from cases first.');
            }

            // Delete profile picture if exists
            if ($client->profile_picture && Storage::disk('public')->exists($client->profile_picture)) {
                Storage::disk('public')->delete($client->profile_picture);
            }

            $client->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Client deleted successfully!'
                ]);
            }

            return redirect()->route('clients.index')->with('success', 'Client deleted successfully!');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete client: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to delete client: ' . $e->getMessage());
        }
    }

    public function getClientsData(Request $request)
    {
        $user = Auth::user();
        
        $query = Client::query();
        
        // Apply user restrictions
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }
        
        // Apply search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('contact_number', 'like', '%' . $search . '%')
                  ->orWhere('cnic', 'like', '%' . $search . '%');
            });
        }
        
        // Apply type filter
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }
        
        $clients = $query->select(['id', 'name', 'email', 'contact_number', 'type', 'council_for'])
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->map(function($client) {
                return [
                    'id' => $client->id,
                    'text' => $client->name . ' (' . $client->type . ') - ' . ($client->contact_number ?: 'No phone'),
                    'name' => $client->name,
                    'email' => $client->email,
                    'contact_number' => $client->contact_number,
                    'type' => $client->type,
                    'council_for' => $client->council_for,
                ];
            });

        return response()->json($clients);
    }

    public function toggleStatus(Client $client)
    {
        // Check authorization
        $user = Auth::user();
        if ($user->role !== 'admin' && $client->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $client->is_active = !$client->is_active;
            $client->save();

            return response()->json([
                'success' => true,
                'message' => 'Client status updated successfully!',
                'is_active' => $client->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update client status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'client_ids' => 'required|array',
            'client_ids.*' => 'exists:clients,id'
        ]);
        
        $user = Auth::user();
        $processed = 0;
        $failed = 0;
        
        foreach ($request->client_ids as $clientId) {
            try {
                $client = Client::find($clientId);
                
                // Check authorization
                if ($user->role !== 'admin' && $client->user_id !== $user->id) {
                    $failed++;
                    continue;
                }
                
                switch ($request->action) {
                    case 'activate':
                        $client->is_active = true;
                        $client->save();
                        break;
                        
                    case 'deactivate':
                        $client->is_active = false;
                        $client->save();
                        break;
                        
                    case 'delete':
                        // Check if client has cases
                        if ($client->case || $client->cases()->exists()) {
                            $failed++;
                            continue;
                        }
                        
                        // Delete profile picture
                        if ($client->profile_picture && Storage::disk('public')->exists($client->profile_picture)) {
                            Storage::disk('public')->delete($client->profile_picture);
                        }
                        
                        $client->delete();
                        break;
                }
                
                $processed++;
                
            } catch (\Exception $e) {
                $failed++;
                \Log::error('Failed to process client ' . $clientId . ': ' . $e->getMessage());
            }
        }
        
        $message = "Processed {$processed} client(s) successfully.";
        if ($failed > 0) {
            $message .= " Failed to process {$failed} client(s).";
        }
        
        return redirect()->route('clients.index')
            ->with($failed > 0 ? 'warning' : 'success', $message);
    }

    public function export(Request $request)
    {
        // Implement CSV/Excel export functionality
        return back()->with('info', 'Export feature coming soon.');
    }

    public function import(Request $request)
    {
        // Implement CSV/Excel import functionality
        return back()->with('info', 'Import feature coming soon.');
    }

    public function getClientStatistics()
    {
        $user = Auth::user();
        
        $query = Client::query();
        
        // Apply user restrictions
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }
        
        $total = $query->count();
        $active = $query->where('is_active', true)->count();
        
        $byType = $query->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
        
        $byCouncil = $query->selectRaw('council_for, count(*) as count')
            ->groupBy('council_for')
            ->pluck('count', 'council_for')
            ->toArray();
        
        $recent = $query->latest()->take(5)->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'active' => $active,
                'inactive' => $total - $active,
                'by_type' => $byType,
                'by_council' => $byCouncil,
                'recent' => $recent,
            ]
        ]);
    }

    public static function getTypeColor($type)
    {
        $colors = [
            'individual' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'corporate' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'government' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
            'ngo' => 'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-400',
        ];

        return $colors[$type] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
    }

    public static function getGenderColor($gender)
    {
        $colors = [
            'male' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'female' => 'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-400',
            'other' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400'
        ];

        return $colors[$gender] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
    }
    
    public static function getCouncilColor($council)
    {
        $colors = [
            'plaintiff' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'defendant' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            'complainant' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'respondent' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
            'petitioner' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
            'appellant' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
            'witness' => 'bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-400',
            'expert' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400',
        ];

        return $colors[$council] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
    }
}