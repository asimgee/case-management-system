<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Case_Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with(['cases']);

        // Apply filters
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('contact_number', 'like', '%' . $search . '%')
                  ->orWhere('cnic', 'like', '%' . $search . '%');
            });
        }

        $clients = $query->latest()->paginate(12);

        return view('client.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clients,email',
            'contact_number' => 'nullable|string|max:20',
            'cnic' => 'nullable|string|max:15|unique:clients,cnic',
            'type' => 'required|in:individual,corporate',
            'address' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'nationality' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ]);

        try {
            $client = Client::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'contact_number' => $validated['contact_number'],
                'cnic' => $validated['cnic'],
                'type' => $validated['type'],
                'address' => $validated['address'],
                'company_name' => $validated['company_name'],
                'designation' => $validated['designation'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'nationality' => $validated['nationality'],
                'notes' => $validated['notes'],
                'user_id' => Auth::id()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Client created successfully!',
                    'client' => $client
                ]); 
            }

            return redirect()->route('client.index')->with('success', 'Client created successfully!');

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
        $client->load(['cases' => function($query) {
            $query->latest()->with(['caseType', 'courtType']);
        }]);

        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clients,email,' . $client->id,
            'contact_number' => 'nullable|string|max:20',
            'cnic' => 'nullable|string|max:15|unique:clients,cnic,' . $client->id,
            'type' => 'required|in:individual,corporate',
            'address' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'nationality' => 'nullable|string|max:100',
            'notes' => 'nullable|string'
        ]);

        try {
            $client->update($validated);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Client updated successfully!',
                    'client' => $client
                ]);
            }

            return redirect()->route('client.index')->with('success', 'Client updated successfully!');

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
        try {
            // Check if client has cases
            if ($client->cases()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete client with associated cases. Please reassign or delete cases first.'
                ], 422);
            }

            $client->delete();

            return response()->json([
                'success' => true,
                'message' => 'Client deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete client: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getClientsData(Request $request)
    {
        $clients = Client::select(['id', 'name', 'email', 'contact_number', 'type'])
            ->when($request->has('search'), function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%');
            })
            ->limit(10)
            ->get();

        return response()->json($clients);
    }

    public static function getTypeColor($type)
    {
        $colors = [
            'individual' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'corporate' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
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
}