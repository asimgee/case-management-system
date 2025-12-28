<?php
// [file name]: HearingController.php

namespace App\Http\Controllers;

use App\Models\Case_Model;
use App\Models\Hearing;
use App\Models\HearingAttendee;
use App\Models\HearingExpense;
use App\Models\HearingReminder;
use App\Models\User;
use App\Models\Lawyer;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class HearingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of hearings.
     */
    public function index(Request $request)
    {
        $query = Hearing::with(['case', 'user', 'creator'])
            ->orderBy('hearing_date', 'asc')
            ->orderBy('hearing_time', 'asc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('hearing_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('hearing_date', '<=', $request->date_to);
        }

        if ($request->filled('judge')) {
            $query->where('judge', 'like', '%' . $request->judge . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('purpose', 'like', '%' . $search . '%')
                  ->orWhere('location', 'like', '%' . $search . '%')
                  ->orWhereHas('case', function($caseQuery) use ($search) {
                      $caseQuery->where('case_number', 'like', '%' . $search . '%')
                               ->orWhere('case_title', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by user if not admin
        if (Auth::user()->role !== 'admin') {
            $query->where('user_id', Auth::id());
        } elseif ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $hearings = $query->paginate(20);

        $statusOptions = Hearing::getStatusOptions();
        $typeOptions = Hearing::getTypeOptions();

        return view('hearings.index', compact('hearings', 'statusOptions', 'typeOptions'));
    }

    /**
     * Show the form for creating a new hearing.
     */
    public function create(Request $request)
    {
        $caseId = $request->case_id;
        $case = null;
        
        if ($caseId) {
            $case = Case_Model::where('id', $caseId)
                ->where(function($query) {
                    if (Auth::user()->role !== 'admin') {
                        $query->where('user_id', Auth::id());
                    }
                })
                ->firstOrFail();
        }

        $cases = Case_Model::query();
        if (Auth::user()->role !== 'admin') {
            $cases->where('user_id', Auth::id());
        }
        $cases = $cases->get();

        $users = Auth::user()->role === 'admin' ? User::all() : collect([Auth::user()]);

        $typeOptions = Hearing::getTypeOptions();
        $statusOptions = Hearing::getStatusOptions();

        return view('hearings.create', compact('cases', 'case', 'users', 'typeOptions', 'statusOptions'));
    }

    /**
     * Store a newly created hearing in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Hearing::validationRules());

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Check case access
            $case = Case_Model::findOrFail($request->case_id);
            if (Auth::user()->role !== 'admin' && $case->user_id !== Auth::id()) {
                abort(403, 'Unauthorized action.');
            }

            $hearing = Hearing::create([
                'case_id' => $request->case_id,
                'hearing_date' => $request->hearing_date,
                'hearing_time' => $request->hearing_time,
                'location' => $request->location,
                'judge' => $request->judge,
                'type' => $request->type,
                'purpose' => $request->purpose,
                'outcome' => $request->outcome,
                'next_hearing_date' => $request->next_hearing_date,
                'next_hearing_time' => $request->next_hearing_time,
                'next_hearing_location' => $request->next_hearing_location,
                'next_hearing_judge' => $request->next_hearing_judge,
                'notes' => $request->notes,
                'status' => $request->status,
                'duration_minutes' => $request->duration_minutes,
                'attendance_notes' => $request->attendance_notes,
                'adjournment_reason' => $request->adjournment_reason,
                'adjournment_requested_by' => $request->adjournment_requested_by,
                'court_order_number' => $request->court_order_number,
                'court_order_details' => $request->court_order_details,
                'witnesses_present' => $request->witnesses_present,
                'evidence_presented' => $request->evidence_presented,
                'arguments_made' => $request->arguments_made,
                'decisions_taken' => $request->decisions_taken,
                'compliance_required' => $request->compliance_required,
                'compliance_deadline' => $request->compliance_deadline,
                'follow_up_actions' => $request->follow_up_actions,
                'user_id' => $request->user_id,
            ]);

            // Add attendees if provided
            if ($request->has('attendees')) {
                foreach ($request->attendees as $attendeeData) {
                    if (!empty($attendeeData['name'])) {
                        $hearing->addAttendee(
                            $attendeeData['name'],
                            $attendeeData['role'] ?? 'other',
                            $attendeeData['contact'] ?? null
                        );
                    }
                }
            }

            // Add expenses if provided
            if ($request->has('expenses')) {
                foreach ($request->expenses as $expenseData) {
                    if (!empty($expenseData['description']) && !empty($expenseData['amount'])) {
                        $hearing->addExpense(
                            $expenseData['description'],
                            $expenseData['amount'],
                            $expenseData['category'] ?? 'other'
                        );
                    }
                }
            }

            // Add reminders if provided
            if ($request->has('reminders')) {
                foreach ($request->reminders as $reminderData) {
                    if (!empty($reminderData['days_before'])) {
                        $hearing->addReminder(
                            $reminderData['days_before'],
                            $reminderData['message'] ?? null
                        );
                    }
                }
            }

            DB::commit();

            return redirect()->route('hearings.show', $hearing->id)
                ->with('success', 'Hearing created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create hearing: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified hearing.
     */
    public function show(Hearing $hearing)
    {
        // Authorization
        if (Auth::user()->role !== 'admin' && $hearing->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $hearing->load([
            'case', 
            'user', 
            'creator', 
            'attendees', 
            'expenses', 
            'reminders', 
            'notesHistory.creator',
            'documents'
        ]);

        $statistics = [
            'total_expenses' => $hearing->calculateExpensesTotal(),
            'total_attendees' => $hearing->getAttendeesCount(),
            'upcoming_reminders' => $hearing->reminders()->where('sent', false)->count(),
        ];

        return view('hearings.show', compact('hearing', 'statistics'));
    }

    /**
     * Show the form for editing the specified hearing.
     */
    public function edit(Hearing $hearing)
    {
        // Authorization
        if (Auth::user()->role !== 'admin' && $hearing->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $cases = Case_Model::query();
        if (Auth::user()->role !== 'admin') {
            $cases->where('user_id', Auth::id());
        }
        $cases = $cases->get();

        $users = Auth::user()->role === 'admin' ? User::all() : collect([Auth::user()]);

        $typeOptions = Hearing::getTypeOptions();
        $statusOptions = Hearing::getStatusOptions();

        $hearing->load(['attendees', 'expenses', 'reminders']);

        return view('hearings.edit', compact('hearing', 'cases', 'users', 'typeOptions', 'statusOptions'));
    }

    /**
     * Update the specified hearing in storage.
     */
    public function update(Request $request, Hearing $hearing)
    {
        // Authorization
        if (Auth::user()->role !== 'admin' && $hearing->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $rules = Hearing::validationRules($hearing->id);
        $rules['case_id'] = 'required|exists:cases,id';

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Check case access if changing case
            if ($request->case_id != $hearing->case_id) {
                $case = Case_Model::findOrFail($request->case_id);
                if (Auth::user()->role !== 'admin' && $case->user_id !== Auth::id()) {
                    abort(403, 'Unauthorized action.');
                }
            }

            $hearing->update([
                'case_id' => $request->case_id,
                'hearing_date' => $request->hearing_date,
                'hearing_time' => $request->hearing_time,
                'location' => $request->location,
                'judge' => $request->judge,
                'type' => $request->type,
                'purpose' => $request->purpose,
                'outcome' => $request->outcome,
                'next_hearing_date' => $request->next_hearing_date,
                'next_hearing_time' => $request->next_hearing_time,
                'next_hearing_location' => $request->next_hearing_location,
                'next_hearing_judge' => $request->next_hearing_judge,
                'notes' => $request->notes,
                'status' => $request->status,
                'duration_minutes' => $request->duration_minutes,
                'attendance_notes' => $request->attendance_notes,
                'adjournment_reason' => $request->adjournment_reason,
                'adjournment_requested_by' => $request->adjournment_requested_by,
                'court_order_number' => $request->court_order_number,
                'court_order_details' => $request->court_order_details,
                'witnesses_present' => $request->witnesses_present,
                'evidence_presented' => $request->evidence_presented,
                'arguments_made' => $request->arguments_made,
                'decisions_taken' => $request->decisions_taken,
                'compliance_required' => $request->compliance_required,
                'compliance_deadline' => $request->compliance_deadline,
                'follow_up_actions' => $request->follow_up_actions,
                'user_id' => $request->user_id,
            ]);

            // Update attendees
            $hearing->attendees()->delete();
            if ($request->has('attendees')) {
                foreach ($request->attendees as $attendeeData) {
                    if (!empty($attendeeData['name'])) {
                        $hearing->addAttendee(
                            $attendeeData['name'],
                            $attendeeData['role'] ?? 'other',
                            $attendeeData['contact'] ?? null
                        );
                    }
                }
            }

            // Update expenses
            $hearing->expenses()->delete();
            if ($request->has('expenses')) {
                foreach ($request->expenses as $expenseData) {
                    if (!empty($expenseData['description']) && !empty($expenseData['amount'])) {
                        $hearing->addExpense(
                            $expenseData['description'],
                            $expenseData['amount'],
                            $expenseData['category'] ?? 'other'
                        );
                    }
                }
            }

            // Update reminders
            $hearing->reminders()->delete();
            if ($request->has('reminders')) {
                foreach ($request->reminders as $reminderData) {
                    if (!empty($reminderData['days_before'])) {
                        $hearing->addReminder(
                            $reminderData['days_before'],
                            $reminderData['message'] ?? null
                        );
                    }
                }
            }

            DB::commit();

            return redirect()->route('hearings.show', $hearing->id)
                ->with('success', 'Hearing updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update hearing: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified hearing from storage.
     */
    public function destroy(Hearing $hearing)
    {
        // Authorization
        if (Auth::user()->role !== 'admin' && $hearing->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        DB::beginTransaction();

        try {
            $caseId = $hearing->case_id;
            $hearing->delete();

            DB::commit();

            return redirect()->route('cases.show', $caseId)
                ->with('success', 'Hearing deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete hearing: ' . $e->getMessage());
        }
    }

    /**
     * Mark hearing as completed.
     */
    public function markAsCompleted(Hearing $hearing)
    {
        // Authorization
        if (Auth::user()->role !== 'admin' && $hearing->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $hearing->markAsCompleted();
            return redirect()->back()->with('success', 'Hearing marked as completed!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to mark hearing as completed: ' . $e->getMessage());
        }
    }

    /**
     * Mark hearing as cancelled.
     */
    public function markAsCancelled(Request $request, Hearing $hearing)
    {
        // Authorization
        if (Auth::user()->role !== 'admin' && $hearing->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $hearing->markAsCancelled($request->reason);
            return redirect()->back()->with('success', 'Hearing marked as cancelled!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to mark hearing as cancelled: ' . $e->getMessage());
        }
    }

    /**
     * Adjourn hearing.
     */
    public function adjourn(Request $request, Hearing $hearing)
    {
        // Authorization
        if (Auth::user()->role !== 'admin' && $hearing->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
            'requested_by' => 'nullable|string|max:255'
        ]);

        try {
            $hearing->adjourn($request->reason, $request->requested_by);
            return redirect()->back()->with('success', 'Hearing adjourned!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to adjourn hearing: ' . $e->getMessage());
        }
    }

    /**
     * Reschedule hearing.
     */
    public function reschedule(Request $request, Hearing $hearing)
    {
        // Authorization
        if (Auth::user()->role !== 'admin' && $hearing->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'new_date' => 'required|date|after_or_equal:today',
            'new_time' => 'required|date_format:H:i',
            'new_location' => 'nullable|string|max:255'
        ]);

        try {
            $rescheduledHearing = $hearing->reschedule(
                $request->new_date,
                $request->new_time,
                $request->new_location
            );
            
            return redirect()->route('hearings.show', $rescheduledHearing->id)
                ->with('success', 'Hearing rescheduled successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to reschedule hearing: ' . $e->getMessage());
        }
    }

    /**
     * Add a note to hearing.
     */
    public function addNote(Request $request, Hearing $hearing)
    {
        // Authorization
        if (Auth::user()->role !== 'admin' && $hearing->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'content' => 'required|string|max:5000'
        ]);

        try {
            $hearing->addNote($request->content, Auth::id());
            return redirect()->back()->with('success', 'Note added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add note: ' . $e->getMessage());
        }
    }

    /**
     * Send reminder for hearing.
     */
    public function sendReminder(Hearing $hearing)
    {
        // Authorization
        if (Auth::user()->role !== 'admin' && $hearing->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $hearing->sendReminder();
            return redirect()->back()->with('success', 'Reminder sent successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send reminder: ' . $e->getMessage());
        }
    }

    /**
     * Get hearing statistics.
     */
    public function getStatistics()
    {
        $userId = Auth::id();
        $isAdmin = Auth::user()->role === 'admin';
        
        $stats = Hearing::getHearingStatistics($isAdmin ? null : $userId);
        
        return response()->json($stats);
    }

    /**
     * Get today's hearings.
     */
    public function getTodaysHearings()
    {
        $userId = Auth::id();
        $isAdmin = Auth::user()->role === 'admin';
        
        $hearings = Hearing::getTodaysHearings($isAdmin ? null : $userId);
        
        return response()->json($hearings);
    }

    /**
     * Get upcoming hearings.
     */
    public function getUpcomingHearings()
    {
        $userId = Auth::id();
        $isAdmin = Auth::user()->role === 'admin';
        
        $hearings = Hearing::upcoming()
            ->when(!$isAdmin, function($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->with(['case', 'user'])
            ->take(10)
            ->get();
        
        return response()->json($hearings);
    }

    /**
     * Create first hearing when case is created.
     */
    public function createFirstHearingForCase(Case_Model $case, Request $request = null)
    {
        // Set default hearing date (7 days from filing date)
        $hearingDate = $request->next_date ?? $case->filing_date->addDays(7);
        
        // Create first hearing
        $hearing = Hearing::create([
            'case_id' => $case->id,
            'hearing_date' => $hearingDate,
            'hearing_time' => '09:00:00',
            'location' => $case->court_name,
            'judge' => 'To be assigned',
            'type' => 'initial',
            'purpose' => 'First hearing for case filing',
            'status' => 'scheduled',
            'user_id' => $case->user_id,
        ]);

        // Add attendees (lawyers and clients)
        if ($case->assignedLawyer) {
            $hearing->addAttendee(
                $case->assignedLawyer->name,
                'lawyer',
                $case->assignedLawyer->phone
            );
        }

        // Add first party client
        $firstParty = $case->clients()->where('type', 'first_party')->first();
        if ($firstParty) {
            $hearing->addAttendee(
                $firstParty->name,
                'client',
                $firstParty->contact_number
            );
        }

        // Add second party client
        $secondParty = $case->clients()->where('type', 'second_party')->first();
        if ($secondParty) {
            $hearing->addAttendee(
                $secondParty->name,
                'client',
                $secondParty->contact_number
            );
        }

        // Add reminder for 3 days before hearing
        $hearing->addReminder(3, 'First hearing reminder for case: ' . $case->case_number);

        return $hearing;
    }

    /**
 * Display calendar view for hearings.
 */
public function calendar(Request $request)
{
    // Get year and month from request or use current
    $year = $request->get('year', now()->year);
    $month = $request->get('month', now()->month);
    
    // Validate year and month
    if ($year < 1900 || $year > 2100) $year = now()->year;
    if ($month < 1 || $month > 12) $month = now()->month;
    
    // Create Carbon date
    $date = Carbon::createFromDate($year, $month, 1);
    $monthName = $date->format('F');
    $daysInMonth = $date->daysInMonth;
    $firstDayOfWeek = $date->dayOfWeek; // 0 = Sunday, 6 = Saturday
    
    // Get hearings for this month
    $startDate = $date->copy()->startOfMonth();
    $endDate = $date->copy()->endOfMonth();
    
    $hearingsQuery = Hearing::whereBetween('hearing_date', [$startDate, $endDate])
        ->with(['case'])
        ->orderBy('hearing_date')
        ->orderBy('hearing_time');
    
    // Filter by user if not admin
    if (Auth::user()->role !== 'admin') {
        $hearingsQuery->where('user_id', Auth::id());
    }
    
    $hearings = $hearingsQuery->get();
    
    // Group hearings by date
    $hearingsByDate = [];
    $totalHearings = 0;
    
    foreach ($hearings as $hearing) {
        $dateStr = $hearing->hearing_date->toDateString();
        if (!isset($hearingsByDate[$dateStr])) {
            $hearingsByDate[$dateStr] = [];
        }
        $hearingsByDate[$dateStr][] = $hearing;
        $totalHearings++;
    }
    
    // Get upcoming hearings for this week
    $upcomingHearings = Hearing::upcoming()
        ->with(['case'])
        ->when(Auth::user()->role !== 'admin', function($q) {
            $q->where('user_id', Auth::id());
        })
        ->whereBetween('hearing_date', [now(), now()->addDays(7)])
        ->orderBy('hearing_date')
        ->orderBy('hearing_time')
        ->take(6)
        ->get();
    
    return view('hearings.calendar', compact(
        'year',
        'month',
        'monthName',
        'daysInMonth',
        'firstDayOfWeek',
        'hearingsByDate',
        'totalHearings',
        'upcomingHearings'
    ));
}

/**
 * Display hearing details for modal.
 */
public function modalView(Hearing $hearing)
{
    // Authorization
    if (Auth::user()->role !== 'admin' && $hearing->user_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }
    
    $hearing->load(['case', 'creator', 'updater', 'attendees', 'reminders']);
    
    return view('hearings.modal-view', compact('hearing'));
}
}