<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Hearing extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_id',
        'hearing_date',
        'hearing_time',
        'location',
        'judge',
        'type',
        'purpose',
        'outcome',
        'next_hearing_date',
        'next_hearing_time',
        'next_hearing_location',
        'next_hearing_judge',
        'notes',
        'status',
        'duration_minutes',
        'attendance_notes',
        'adjournment_reason',
        'adjournment_requested_by',
        'court_order_number',
        'court_order_details',
        'witnesses_present',
        'evidence_presented',
        'arguments_made',
        'decisions_taken',
        'compliance_required',
        'compliance_deadline',
        'follow_up_actions',
        'reminder_sent',
        'rescheduled_from',
        'user_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'hearing_date' => 'date',
        'hearing_time' => 'datetime',
        'next_hearing_date' => 'date',
        'next_hearing_time' => 'datetime',
        'duration_minutes' => 'integer',
        'compliance_deadline' => 'date',
        'reminder_sent' => 'boolean',
        'witnesses_present' => 'array',
        'evidence_presented' => 'array',
        'arguments_made' => 'array',
        'decisions_taken' => 'array',
        'follow_up_actions' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $appends = [
        'formatted_hearing_date',
        'formatted_hearing_time',
        'formatted_next_hearing_date',
        'formatted_next_hearing_time',
        'hearing_datetime',
        'next_hearing_datetime',
        'is_upcoming',
        'is_past',
        'is_today',
        'hearing_status',
        'hearing_type_label',
        'duration_formatted'
    ];

    // Relationships
    public function case(): BelongsTo
    {
        return $this->belongsTo(Case_Model::class, 'case_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'hearing_id');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(HearingReminder::class);
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(HearingAttendee::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(HearingExpense::class);
    }

    public function notesHistory(): HasMany
    {
        return $this->hasMany(HearingNote::class)->orderBy('created_at', 'desc');
    }

    public function rescheduledFromHearing(): BelongsTo
    {
        return $this->belongsTo(Hearing::class, 'rescheduled_from');
    }

    public function rescheduledToHearing(): HasMany
    {
        return $this->hasMany(Hearing::class, 'rescheduled_from');
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('hearing_date', '>=', today())
                     ->orderBy('hearing_date')
                     ->orderBy('hearing_time');
    }

    public function scopePast($query)
    {
        return $query->where('hearing_date', '<', today())
                     ->orderBy('hearing_date', 'desc')
                     ->orderBy('hearing_time', 'desc');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('hearing_date', today())
                     ->orderBy('hearing_time');
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('hearing_date', [today(), today()->addDays(7)])
                     ->orderBy('hearing_date')
                     ->orderBy('hearing_time');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('hearing_date', now()->month)
                     ->whereYear('hearing_date', now()->year)
                     ->orderBy('hearing_date')
                     ->orderBy('hearing_time');
    }

    public function scopeByCase($query, $caseId)
    {
        return $query->where('case_id', $caseId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByJudge($query, $judgeName)
    {
        return $query->where('judge', 'like', "%{$judgeName}%");
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', 'like', "%{$location}%");
    }

    public function scopeScheduled($query)
    {
        return $query->whereIn('status', ['scheduled', 'rescheduled']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeAdjourned($query)
    {
        return $query->where('status', 'adjourned');
    }

    public function scopeWithReminders($query)
    {
        return $query->whereHas('reminders');
    }

    public function scopeWithoutReminders($query)
    {
        return $query->whereDoesntHave('reminders');
    }

    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('purpose', 'like', "%{$searchTerm}%")
              ->orWhere('outcome', 'like', "%{$searchTerm}%")
              ->orWhere('judge', 'like', "%{$searchTerm}%")
              ->orWhere('location', 'like', "%{$searchTerm}%")
              ->orWhere('notes', 'like', "%{$searchTerm}%")
              ->orWhereHas('case', function($caseQuery) use ($searchTerm) {
                  $caseQuery->where('case_number', 'like', "%{$searchTerm}%")
                           ->orWhere('case_title', 'like', "%{$searchTerm}%");
              });
        });
    }

    // Accessors
    public function getFormattedHearingDateAttribute(): string
    {
        return $this->hearing_date ? $this->hearing_date->format('M d, Y') : 'N/A';
    }

    public function getFormattedHearingTimeAttribute(): string
    {
        return $this->hearing_time ? $this->hearing_time->format('h:i A') : 'N/A';
    }

    public function getFormattedNextHearingDateAttribute(): ?string
    {
        return $this->next_hearing_date ? $this->next_hearing_date->format('M d, Y') : null;
    }

    public function getFormattedNextHearingTimeAttribute(): ?string
    {
        return $this->next_hearing_time ? $this->next_hearing_time->format('h:i A') : null;
    }

    public function getHearingDatetimeAttribute(): ?string
    {
        if (!$this->hearing_date || !$this->hearing_time) {
            return null;
        }
        
        $date = $this->hearing_date->format('Y-m-d');
        $time = $this->hearing_time->format('H:i:s');
        
        return "{$date} {$time}";
    }

    public function getNextHearingDatetimeAttribute(): ?string
    {
        if (!$this->next_hearing_date || !$this->next_hearing_time) {
            return null;
        }
        
        $date = $this->next_hearing_date->format('Y-m-d');
        $time = $this->next_hearing_time->format('H:i:s');
        
        return "{$date} {$time}";
    }

    public function getIsUpcomingAttribute(): bool
    {
        if (!$this->hearing_datetime) {
            return false;
        }
        
        $hearingDateTime = Carbon::parse($this->hearing_datetime);
        return $hearingDateTime->isFuture();
    }

    public function getIsPastAttribute(): bool
    {
        if (!$this->hearing_datetime) {
            return false;
        }
        
        $hearingDateTime = Carbon::parse($this->hearing_datetime);
        return $hearingDateTime->isPast();
    }

    public function getIsTodayAttribute(): bool
    {
        if (!$this->hearing_date) {
            return false;
        }
        
        return $this->hearing_date->isToday();
    }

    public function getHearingStatusAttribute(): string
    {
        if ($this->status) {
            return ucfirst($this->status);
        }
        
        if ($this->is_upcoming) {
            return 'Upcoming';
        }
        
        if ($this->is_past) {
            return 'Completed';
        }
        
        return 'Unknown';
    }

    public function getHearingTypeLabelAttribute(): string
    {
        $types = [
            'initial' => 'Initial Hearing',
            'regular' => 'Regular Hearing',
            'final' => 'Final Hearing',
            'appeal' => 'Appeal Hearing',
            'review' => 'Review Hearing',
            'motion' => 'Motion Hearing',
            'settlement' => 'Settlement Hearing',
            'pre_trial' => 'Pre-trial Conference',
            'case_management' => 'Case Management',
            'evidence' => 'Evidence Hearing',
            'witness' => 'Witness Hearing',
            'sentencing' => 'Sentencing Hearing',
            'bail' => 'Bail Hearing',
            'interim' => 'Interim Hearing',
            'other' => 'Other'
        ];
        
        return $types[$this->type] ?? ucfirst($this->type ?? 'Regular');
    }

    public function getDurationFormattedAttribute(): string
    {
        if (!$this->duration_minutes) {
            return 'N/A';
        }
        
        if ($this->duration_minutes < 60) {
            return $this->duration_minutes . ' minutes';
        }
        
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        if ($minutes > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        
        return $hours . ' hours';
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            'scheduled' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            'adjourned' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'rescheduled' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
        ];
        
        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
    }

    public function getTypeColorAttribute(): string
    {
        $colors = [
            'initial' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
            'final' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            'appeal' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
            'motion' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
            'settlement' => 'bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-400',
            'bail' => 'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-400',
        ];
        
        return $colors[$this->type] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400';
    }

    public function getCaseNumberAttribute(): ?string
    {
        return $this->case ? $this->case->case_number : null;
    }

    public function getCaseTitleAttribute(): ?string
    {
        return $this->case ? $this->case->case_title : null;
    }

    public function getUserNameAttribute(): ?string
    {
        return $this->user ? $this->user->name : null;
    }

    public function getDaysUntilHearingAttribute(): ?int
    {
        if (!$this->hearing_date) {
            return null;
        }
        
        return $this->hearing_date->diffInDays(today());
    }

    public function getIsUrgentAttribute(): bool
    {
        if (!$this->hearing_date) {
            return false;
        }
        
        return $this->hearing_date->diffInDays(today()) <= 2;
    }

    public function getHasNextHearingAttribute(): bool
    {
        return !is_null($this->next_hearing_date);
    }

    public function getWitnessesListAttribute(): array
    {
        if (!$this->witnesses_present || !is_array($this->witnesses_present)) {
            return [];
        }
        
        return $this->witnesses_present;
    }

    public function getEvidenceListAttribute(): array
    {
        if (!$this->evidence_presented || !is_array($this->evidence_presented)) {
            return [];
        }
        
        return $this->evidence_presented;
    }

    public function getArgumentsListAttribute(): array
    {
        if (!$this->arguments_made || !is_array($this->arguments_made)) {
            return [];
        }
        
        return $this->arguments_made;
    }

    public function getDecisionsListAttribute(): array
    {
        if (!$this->decisions_taken || !is_array($this->decisions_taken)) {
            return [];
        }
        
        return $this->decisions_taken;
    }

    public function getFollowUpActionsListAttribute(): array
    {
        if (!$this->follow_up_actions || !is_array($this->follow_up_actions)) {
            return [];
        }
        
        return $this->follow_up_actions;
    }

    // Mutators
    public function setHearingDateAttribute($value)
    {
        $this->attributes['hearing_date'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function setHearingTimeAttribute($value)
    {
        $this->attributes['hearing_time'] = $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    public function setNextHearingDateAttribute($value)
    {
        $this->attributes['next_hearing_date'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function setNextHearingTimeAttribute($value)
    {
        $this->attributes['next_hearing_time'] = $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    public function setWitnessesPresentAttribute($value)
    {
        $this->attributes['witnesses_present'] = $value ? json_encode((array)$value) : null;
    }

    public function setEvidencePresentedAttribute($value)
    {
        $this->attributes['evidence_presented'] = $value ? json_encode((array)$value) : null;
    }

    public function setArgumentsMadeAttribute($value)
    {
        $this->attributes['arguments_made'] = $value ? json_encode((array)$value) : null;
    }

    public function setDecisionsTakenAttribute($value)
    {
        $this->attributes['decisions_taken'] = $value ? json_encode((array)$value) : null;
    }

    public function setFollowUpActionsAttribute($value)
    {
        $this->attributes['follow_up_actions'] = $value ? json_encode((array)$value) : null;
    }

    // Business Logic Methods
    public function markAsCompleted(): bool
    {
        $this->status = 'completed';
        return $this->save();
    }

    public function markAsCancelled(string $reason = null): bool
    {
        $this->status = 'cancelled';
        if ($reason) {
            $this->notes .= "\nCancellation Reason: {$reason}";
        }
        return $this->save();
    }

    public function adjourn(string $reason, string $requestedBy = null): bool
    {
        $this->status = 'adjourned';
        $this->adjournment_reason = $reason;
        $this->adjournment_requested_by = $requestedBy;
        return $this->save();
    }

    public function reschedule($newDate, $newTime = null, $newLocation = null): Hearing
    {
        $rescheduledHearing = $this->replicate();
        $rescheduledHearing->hearing_date = $newDate;
        $rescheduledHearing->hearing_time = $newTime;
        $rescheduledHearing->location = $newLocation ?? $this->location;
        $rescheduledHearing->status = 'rescheduled';
        $rescheduledHearing->rescheduled_from = $this->id;
        $rescheduledHearing->save();
        
        $this->status = 'cancelled';
        $this->save();
        
        return $rescheduledHearing;
    }

    public function addReminder($daysBefore, $message = null): HearingReminder
    {
        $reminderDate = Carbon::parse($this->hearing_datetime)->subDays($daysBefore);
        
        return HearingReminder::create([
            'hearing_id' => $this->id,
            'reminder_date' => $reminderDate,
            'message' => $message ?? "Reminder: Hearing scheduled for {$this->formatted_hearing_date} at {$this->formatted_hearing_time}",
            'sent' => false
        ]);
    }

    public function addAttendee($name, $role, $contact = null): HearingAttendee
    {
        return HearingAttendee::create([
            'hearing_id' => $this->id,
            'name' => $name,
            'role' => $role,
            'contact' => $contact,
            'attended' => false
        ]);
    }

    public function addExpense($description, $amount, $category = 'other'): HearingExpense
    {
        return HearingExpense::create([
            'hearing_id' => $this->id,
            'description' => $description,
            'amount' => $amount,
            'category' => $category,
            'date' => today()
        ]);
    }

    public function addNote($content, $userId): HearingNote
    {
        return HearingNote::create([
            'hearing_id' => $this->id,
            'content' => $content,
            'created_by' => $userId
        ]);
    }

    public function sendReminder(): bool
    {
        // Implement reminder sending logic (email, SMS, etc.)
        // This is a placeholder - implement based on your notification system
        
        $this->reminder_sent = true;
        return $this->save();
    }

    public function calculateExpensesTotal(): float
    {
        return $this->expenses()->sum('amount');
    }

    public function getAttendeesCount(): int
    {
        return $this->attendees()->count();
    }

    public function getAttendeesByRole($role): array
    {
        return $this->attendees()->where('role', $role)->pluck('name')->toArray();
    }

    // Validation Rules
    public static function validationRules($id = null): array
    {
        return [
            'case_id' => 'required|exists:cases,id',
            'hearing_date' => 'required|date|after_or_equal:today',
            'hearing_time' => 'required|date_format:H:i',
            'location' => 'required|string|max:255',
            'judge' => 'nullable|string|max:255',
            'type' => 'required|in:initial,regular,final,appeal,review,motion,settlement,pre_trial,case_management,evidence,witness,sentencing,bail,interim,other',
            'purpose' => 'required|string|max:1000',
            'outcome' => 'nullable|string|max:2000',
            'next_hearing_date' => 'nullable|date|after:hearing_date',
            'next_hearing_time' => 'nullable|date_format:H:i|required_with:next_hearing_date',
            'next_hearing_location' => 'nullable|string|max:255|required_with:next_hearing_date',
            'next_hearing_judge' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:5000',
            'status' => 'required|in:scheduled,completed,cancelled,adjourned,rescheduled',
            'duration_minutes' => 'nullable|integer|min:1|max:480',
            'attendance_notes' => 'nullable|string|max:2000',
            'adjournment_reason' => 'nullable|string|max:1000|required_if:status,adjourned',
            'adjournment_requested_by' => 'nullable|string|max:255',
            'court_order_number' => 'nullable|string|max:100',
            'court_order_details' => 'nullable|string|max:2000',
            'witnesses_present' => 'nullable|array',
            'witnesses_present.*' => 'string|max:255',
            'evidence_presented' => 'nullable|array',
            'evidence_presented.*' => 'string|max:500',
            'arguments_made' => 'nullable|array',
            'arguments_made.*' => 'string|max:1000',
            'decisions_taken' => 'nullable|array',
            'decisions_taken.*' => 'string|max:1000',
            'compliance_required' => 'nullable|string|max:2000',
            'compliance_deadline' => 'nullable|date|after:hearing_date',
            'follow_up_actions' => 'nullable|array',
            'follow_up_actions.*' => 'string|max:500',
            'user_id' => 'required|exists:users,id',
        ];
    }

    // Static Methods
    public static function getStatusOptions(): array
    {
        return [
            'scheduled' => 'Scheduled',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'adjourned' => 'Adjourned',
            'rescheduled' => 'Rescheduled',
        ];
    }

    public static function getTypeOptions(): array
    {
        return [
            'initial' => 'Initial Hearing',
            'regular' => 'Regular Hearing',
            'final' => 'Final Hearing',
            'appeal' => 'Appeal Hearing',
            'review' => 'Review Hearing',
            'motion' => 'Motion Hearing',
            'settlement' => 'Settlement Hearing',
            'pre_trial' => 'Pre-trial Conference',
            'case_management' => 'Case Management',
            'evidence' => 'Evidence Hearing',
            'witness' => 'Witness Hearing',
            'sentencing' => 'Sentencing Hearing',
            'bail' => 'Bail Hearing',
            'interim' => 'Interim Hearing',
            'other' => 'Other',
        ];
    }

    public static function getUpcomingHearingsCount($userId = null): int
    {
        $query = self::upcoming();
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        return $query->count();
    }

    public static function getTodaysHearings($userId = null)
    {
        $query = self::today()->with(['case', 'user']);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        return $query->get();
    }

    public static function getHearingStatistics($userId = null): array
    {
        $query = self::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        return [
            'total' => $query->count(),
            'scheduled' => $query->clone()->where('status', 'scheduled')->count(),
            'completed' => $query->clone()->where('status', 'completed')->count(),
            'cancelled' => $query->clone()->where('status', 'cancelled')->count(),
            'adjourned' => $query->clone()->where('status', 'adjourned')->count(),
            'upcoming' => $query->clone()->upcoming()->count(),
            'past' => $query->clone()->past()->count(),
            'today' => $query->clone()->today()->count(),
        ];
    }

    // Boot Method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hearing) {
            if (auth()->check()) {
                $hearing->created_by = auth()->id();
            }
            
            if (!$hearing->status) {
                $hearing->status = 'scheduled';
            }
            
            if (!$hearing->user_id && $hearing->case) {
                $hearing->user_id = $hearing->case->user_id;
            }
        });

        static::updating(function ($hearing) {
            if (auth()->check()) {
                $hearing->updated_by = auth()->id();
            }
        });

        static::deleting(function ($hearing) {
            if (auth()->check()) {
                $hearing->deleted_by = auth()->id();
                $hearing->save();
            }
            
            // Delete related records
            $hearing->reminders()->delete();
            $hearing->attendees()->delete();
            $hearing->expenses()->delete();
            $hearing->notesHistory()->delete();
            $hearing->documents()->delete();
        });
    }
}