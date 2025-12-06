<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory;

  protected $fillable = [
        'name',
        'email',
        'contact_number',
        'cnic',
        'type',
        'address',
        'company_name',
        'designation',
        'date_of_birth',
        'gender',
        'nationality',
        'notes',
        'user_id'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];


    /**
     * Relationship with Case
     */
    public function cases(): BelongsTo
    {
        return $this->belongsTo(Case_Model::class, 'case_id');
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
     * Relationship with User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
        return $this->cases()->where('case_status', '!=', 'closed')->exists();
    }

    /**
     * Get active cases count
     */
    public function getActiveCasesCountAttribute(): int
    {
        return $this->cases()->where('case_status', '!=', 'closed')->count();
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
}