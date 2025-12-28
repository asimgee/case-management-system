<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Case_Model extends Model
{
    use HasFactory;

    protected $table = 'cases';

    protected $fillable = [
        'case_number',
        'case_title',
        'first_party_title',
        'second_party_title',
        'first_party_name',
        'first_party_contact',
        'first_party_cnic',
        'first_party_address',
        'first_party_council',
        'second_party_name',
        'second_party_contact',
        'second_party_cnic',
        'second_party_address',
        'second_party_council',
        'case_type_id',
        'case_remedy_id',
        'court_type_id',
        'court_name',
        'case_status',
        'filing_date',
        'next_date',
        'judgment_date',
        'offending_lawyer',
        'client_type',
        'next_order',
        'case_notes',
        'fir_no',
        'fir_year',
        'offence',
        'police_station',
        'other_fir_details',
        'user_id'
    ];

    protected $casts = [
        'filing_date' => 'date',
        'next_date' => 'date',
        'judgment_date' => 'date',
        'fir_year' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function caseType()
    {
        return $this->belongsTo(CaseType::class);
    }

    public function caseRemedy()
    {
        return $this->belongsTo(CaseRemedy::class);
    }

    public function courtType()
    {
        return $this->belongsTo(CourtType::class);
    }

    public function assignedLawyer()
    {
        return $this->belongsTo(Lawyer::class, 'assigned_lawyer_id');
    }

    public function additionalLawyer()
    {
        return $this->belongsTo(Lawyer::class, 'additional_lawyer_id');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'case_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'case_id');
    }

    public function hearings()
    {
        return $this->hasMany(Hearing::class, 'case_id');
    }

    /**
     * Get first party client
     */
    public function getFirstPartyAttribute()
    {
        return $this->clients()->where('type', 'first_party')->first();
    }

    /**
     * Get second party client
     */
    public function getSecondPartyAttribute()
    {
        return $this->clients()->where('type', 'second_party')->first();
    }

    /**
     * Get other parties
     */
    public function getOtherPartiesAttribute()
    {
        return $this->clients()->where('type', 'other')->get();
    }

    // Accessors
    public function getFormattedStatusAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->case_status));
    }

    public function getFormattedFilingDateAttribute()
    {
        return $this->filing_date ? $this->filing_date->format('M d, Y') : 'N/A';
    }

    public function getFormattedNextDateAttribute()
    {
        return $this->next_date ? $this->next_date->format('M d, Y') : 'N/A';
    }

    public function getFormattedJudgmentDateAttribute()
    {
        return $this->judgment_date ? $this->judgment_date->format('M d, Y') : 'N/A';
    }

    public function getCaseTypeNameAttribute()
    {
        return $this->caseType ? $this->caseType->name : 'N/A';
    }

    public function getCaseRemedyNameAttribute()
    {
        return $this->caseRemedy ? $this->caseRemedy->name : 'N/A';
    }

    public function getCourtTypeNameAttribute()
    {
        return $this->courtType ? $this->courtType->name : 'N/A';
    }

    public function getAssignedLawyerNameAttribute()
    {
        return $this->assignedLawyer ? $this->assignedLawyer->name : 'N/A';
    }

    public function getAdditionalLawyerNameAttribute()
    {
        return $this->additionalLawyer ? $this->additionalLawyer->name : 'N/A';
    }

    /**
     * Get documents by category
     */
    public function getDocumentsByCategory($category)
    {
        return $this->documents()->where('category', $category)->get();
    }

    /**
     * Check if case has documents
     */
    public function getHasDocumentsAttribute()
    {
        return $this->documents()->exists();
    }

    /**
     * Get documents count
     */
    public function getDocumentsCountAttribute()
    {
        return $this->documents()->count();
    }

    /**
     * Check if case has upcoming hearing
     */
    public function getHasUpcomingHearingAttribute()
    {
        return $this->hearings()->where('hearing_date', '>=', now())->exists();
    }

    /**
     * Get upcoming hearing
     */
    public function getUpcomingHearingAttribute()
    {
        return $this->hearings()->where('hearing_date', '>=', now())->orderBy('hearing_date')->first();
    }

    /**
     * Get past hearings
     */
    public function getPastHearingsAttribute()
    {
        return $this->hearings()->where('hearing_date', '<', now())->orderBy('hearing_date', 'desc')->get();
    }

    /**
     * Get all parties (clients) for this case
     */
    public function getAllPartiesAttribute()
    {
        return $this->clients()->get();
    }

    /**
     * Scope for active cases (pending or in_hearing)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('case_status', ['pending', 'in_hearing']);
    }

    /**
     * Scope for closed cases
     */
    public function scopeClosed($query)
    {
        return $query->where('case_status', 'closed');
    }

    /**
     * Scope for cases with upcoming hearings
     */
    public function scopeWithUpcomingHearings($query)
    {
        return $query->whereHas('hearings', function($q) {
            $q->where('hearing_date', '>=', now());
        });
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('case_number', 'like', "%{$searchTerm}%")
              ->orWhere('case_title', 'like', "%{$searchTerm}%")
              ->orWhere('first_party_name', 'like', "%{$searchTerm}%")
              ->orWhere('second_party_name', 'like', "%{$searchTerm}%")
              ->orWhere('court_name', 'like', "%{$searchTerm}%")
              ->orWhere('fir_no', 'like', "%{$searchTerm}%");
        });
    }

    /**
     * Get case age in days
     */
    public function getCaseAgeAttribute()
    {
        if (!$this->filing_date) return null;
        
        return $this->filing_date->diffInDays(now());
    }

    /**
     * Check if case is overdue (no next date set and pending for more than 30 days)
     */
    public function getIsOverdueAttribute()
    {
        if ($this->case_status === 'closed') return false;
        
        if (!$this->next_date) {
            return $this->filing_date->diffInDays(now()) > 30;
        }
        
        return false;
    }

    /**
     * Get case progress percentage (based on status and dates)
     */
    public function getProgressPercentageAttribute()
    {
        $progress = [
            'pending' => 25,
            'in_hearing' => 75,
            'closed' => 100,
        ];
        
        return $progress[$this->case_status] ?? 0;
    }

    /**
     * Get case summary for dashboard
     */
    public function getSummaryAttribute()
    {
        $summary = [
            'case_number' => $this->case_number,
            'title' => $this->case_title,
            'status' => $this->formatted_status,
            'next_date' => $this->formatted_next_date,
            'court' => $this->court_name,
            'parties' => "{$this->first_party_name} vs {$this->second_party_name}",
            'age' => $this->case_age . ' days',
            'documents_count' => $this->documents_count,
            'hearings_count' => $this->hearings()->count(),
        ];
        
        return $summary;
    }
    // Case_Model.php mein ye boot method add karein
protected static function boot()
{
    parent::boot();
    
    static::creating(function ($case) {
        if (empty($case->case_number)) {
            // Jab bhi naya case create ho, automatically case number generate ho
            $case->case_number = self::generateCaseNumber($case->user_id);
        }
    });
}

/**
 * Generate unique case number for user
 */
public static function generateCaseNumber($userId)
{
    // User ke liye latest case number dekhein
    $lastCase = self::where('user_id', $userId)
        ->where('case_number', 'LIKE', 'CASE-' . date('Y') . '-%')
        ->orderBy('id', 'desc')
        ->first();
    
    $nextNumber = 1;
    
    // Agar pehle se cases hain to number extract karein
    if ($lastCase && preg_match('/CASE-\d{4}-(\d{4})/', $lastCase->case_number, $matches)) {
        $nextNumber = (int)$matches[1] + 1;
    }
    
    return 'CASE-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
}

/**
 * Get next case number for user
 */
public static function getNextCaseNumber($userId)
{
    return self::generateCaseNumber($userId);
}
}