<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_number',
        'cnic',
        'council_for',
        'address',
        'email',
        'date_of_birth',
        'gender',
        'occupation',
        'company',
        'notes',
        'type',
        'case_id',
        'user_id',
        'is_active',
        'profile_picture',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with Case
     */
    public function case(): BelongsTo
    {
        return $this->belongsTo(Case_Model::class, 'case_id');
    }

    /**
     * Relationship with User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with multiple cases (if client appears in multiple cases)
     */
    public function cases(): HasMany
    {
        return $this->hasMany(Case_Model::class, function($query) {
            $query->where('first_party_name', $this->name)
                  ->orWhere('second_party_name', $this->name);
        });
    }

    /**
     * Relationship with documents
     */
    public function documents()
    {
        return $this->hasMany(Document::class, function($query) {
            $query->whereHas('case', function($q) {
                $q->where('first_party_name', $this->name)
                  ->orWhere('second_party_name', $this->name);
            });
        });
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }
        
        return substr($initials, 0, 2);
    }

    /**
     * Get client age
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) return null;
        
        return $this->date_of_birth->age;
    }

    /**
     * Get formatted date of birth
     */
    public function getFormattedDobAttribute(): ?string
    {
        if (!$this->date_of_birth) return null;
        
        return $this->date_of_birth->format('M d, Y');
    }

    /**
     * Get client type label
     */
    public function getTypeLabelAttribute(): string
    {
        $labels = [
            'first_party' => 'First Party',
            'second_party' => 'Second Party',
            'other' => 'Other Party',
        ];
        
        return $labels[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    /**
     * Get council for label
     */
    public function getCouncilLabelAttribute(): string
    {
        $labels = [
            'plaintiff' => 'Plaintiff',
            'defendant' => 'Defendant',
            'complainant' => 'Complainant',
            'respondent' => 'Respondent',
            'petitioner' => 'Petitioner',
            'appellant' => 'Appellant',
            'other' => 'Other',
        ];
        
        return $labels[$this->council_for] ?? ucfirst($this->council_for);
    }

    /**
     * Scope for first party clients
     */
    public function scopeFirstParty($query)
    {
        return $query->where('type', 'first_party');
    }

    /**
     * Scope for second party clients
     */
    public function scopeSecondParty($query)
    {
        return $query->where('type', 'second_party');
    }

    /**
     * Scope for other parties
     */
    public function scopeOtherParties($query)
    {
        return $query->where('type', 'other');
    }

    /**
     * Format CNIC for display
     */
    public function getFormattedCnicAttribute(): ?string
    {
        if (!$this->cnic) return null;
        
        $cnic = preg_replace('/[^0-9]/', '', $this->cnic);
        if (strlen($cnic) === 13) {
            return substr($cnic, 0, 5) . '-' . substr($cnic, 5, 7) . '-' . substr($cnic, 12, 1);
        }
        
        return $this->cnic;
    }

    public function getHasActiveCasesAttribute(): bool
    {
        if ($this->case) {
            return $this->case->case_status !== 'closed';
        }
        
        return false;
    }

    /**
     * Get active cases count
     */
    public function getActiveCasesCountAttribute(): int
    {
        if ($this->case) {
            return $this->case->case_status !== 'closed' ? 1 : 0;
        }
        
        return 0;
    }

    /**
     * Check if client is plaintiff
     */
    public function isPlaintiff(): bool
    {
        return in_array($this->council_for, ['plaintiff', 'complainant', 'petitioner', 'appellant']);
    }

    /**
     * Check if client is defendant
     */
    public function isDefendant(): bool
    {
        return in_array($this->council_for, ['defendant', 'respondent']);
    }

    /**
     * Get client status color
     */
    public function getStatusColorAttribute(): string
    {
        if ($this->is_active) {
            return 'success';
        }
        
        return 'secondary';
    }

    /**
     * Get client status label
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->is_active) {
            return 'Active';
        }
        
        return 'Inactive';
    }

    /**
     * Scope for active clients
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive clients
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('name', 'like', "%{$searchTerm}%")
              ->orWhere('cnic', 'like', "%{$searchTerm}%")
              ->orWhere('contact_number', 'like', "%{$searchTerm}%")
              ->orWhere('email', 'like', "%{$searchTerm}%")
              ->orWhere('address', 'like', "%{$searchTerm}%");
        });
    }

    /**
     * Get client profile picture URL
     */
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return Storage::url($this->profile_picture);
        }
        
        // Return default avatar based on initials
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get client contact information
     */
    public function getContactInfoAttribute(): array
    {
        return [
            'phone' => $this->contact_number,
            'email' => $this->email,
            'address' => $this->address,
            'cnic' => $this->formatted_cnic,
        ];
    }

    /**
     * Get client statistics
     */
    public function getStatsAttribute(): array
    {
        return [
            'active_cases' => $this->active_cases_count,
            'total_cases' => $this->case ? 1 : 0,
            'total_documents' => $this->documents->count(),
        ];
    }

    /**
     * Toggle client active status
     */
    public function toggleStatus(): bool
    {
        $this->is_active = !$this->is_active;
        return $this->save();
    }

    /**
     * Get client's full information
     */
    public function getFullInfoAttribute(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'initials' => $this->initials,
            'contact_info' => $this->contact_info,
            'type' => $this->type_label,
            'council' => $this->council_label,
            'status' => $this->status_label,
            'age' => $this->age,
            'dob' => $this->formatted_dob,
            'gender' => $this->gender,
            'occupation' => $this->occupation,
            'company' => $this->company,
            'notes' => $this->notes,
            'profile_picture' => $this->profile_picture_url,
            'stats' => $this->stats,
            'created_at' => $this->created_at->format('M d, Y'),
            'updated_at' => $this->updated_at->format('M d, Y'),
        ];
    }
}