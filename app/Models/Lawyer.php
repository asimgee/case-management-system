<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lawyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'license_number',
        'address',
        'specialization',
        'qualification',
        'experience_years',
        'bar_association',
        'bar_number',
        'court_admission_date',
        'is_active',
        'is_custom',
        'user_id',
        'profile_picture',
        'notes',
    ];

    protected $casts = [
        'experience_years' => 'integer',
        'court_admission_date' => 'date',
        'is_active' => 'boolean',
        'is_custom' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedCases(): HasMany
    {
        return $this->hasMany(Case_Model::class, 'assigned_lawyer_id');
    }

    public function additionalCases(): HasMany
    {
        return $this->hasMany(Case_Model::class, 'additional_lawyer_id');
    }

    /**
     * Get all cases assigned to this lawyer
     */
    public function getAllCasesAttribute()
    {
        return $this->assignedCases->merge($this->additionalCases);
    }

    /**
     * Get active cases
     */
    public function getActiveCasesAttribute()
    {
        return $this->getAllCasesAttribute()->where('case_status', '!=', 'closed');
    }

    /**
     * Get closed cases
     */
    public function getClosedCasesAttribute()
    {
        return $this->getAllCasesAttribute()->where('case_status', 'closed');
    }

    public function scopeCustom($query, $userId = null)
    {
        $query->where('is_custom', true);
        if ($userId) {
            $query->where('user_id', $userId);
        }
        return $query;
    }

    public function scopeStandard($query)
    {
        return $query->where('is_custom', false);
    }

    /**
     * Scope for active lawyers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive lawyers
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
              ->orWhere('email', 'like', "%{$searchTerm}%")
              ->orWhere('phone', 'like', "%{$searchTerm}%")
              ->orWhere('license_number', 'like', "%{$searchTerm}%")
              ->orWhere('specialization', 'like', "%{$searchTerm}%")
              ->orWhere('bar_number', 'like', "%{$searchTerm}%");
        });
    }

    /**
     * Get lawyer's initials
     */
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
     * Get lawyer's full address
     */
    public function getFullAddressAttribute(): ?string
    {
        return $this->address;
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute(): ?string
    {
        if (!$this->phone) return null;
        
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        if (strlen($phone) === 10) {
            return '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6, 4);
        }
        
        return $this->phone;
    }

    /**
     * Get formatted admission date
     */
    public function getFormattedAdmissionDateAttribute(): ?string
    {
        if (!$this->court_admission_date) return null;
        
        return $this->court_admission_date->format('M d, Y');
    }

    /**
     * Get lawyer's status color
     */
    public function getStatusColorAttribute(): string
    {
        if ($this->is_active) {
            return 'success';
        }
        
        return 'secondary';
    }

    /**
     * Get lawyer's status label
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->is_active) {
            return 'Active';
        }
        
        return 'Inactive';
    }

    /**
     * Get lawyer's type label
     */
    public function getTypeLabelAttribute(): string
    {
        if ($this->is_custom) {
            return 'Custom';
        }
        
        return 'Standard';
    }

    /**
     * Get lawyer's profile picture URL
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
     * Get lawyer's contact information
     */
    public function getContactInfoAttribute(): array
    {
        return [
            'phone' => $this->formatted_phone,
            'email' => $this->email,
            'address' => $this->full_address,
        ];
    }

    /**
     * Get lawyer's professional information
     */
    public function getProfessionalInfoAttribute(): array
    {
        return [
            'license_number' => $this->license_number,
            'specialization' => $this->specialization,
            'qualification' => $this->qualification,
            'experience' => $this->experience_years . ' years',
            'bar_association' => $this->bar_association,
            'bar_number' => $this->bar_number,
            'admission_date' => $this->formatted_admission_date,
        ];
    }

    /**
     * Get lawyer's statistics
     */
    public function getStatsAttribute(): array
    {
        $allCases = $this->getAllCasesAttribute();
        
        return [
            'total_cases' => $allCases->count(),
            'active_cases' => $allCases->where('case_status', '!=', 'closed')->count(),
            'closed_cases' => $allCases->where('case_status', 'closed')->count(),
            'assigned_cases' => $this->assignedCases->count(),
            'additional_cases' => $this->additionalCases->count(),
        ];
    }

    /**
     * Toggle lawyer active status
     */
    public function toggleStatus(): bool
    {
        $this->is_active = !$this->is_active;
        return $this->save();
    }

    /**
     * Check if lawyer is available (not overloaded with cases)
     */
    public function getIsAvailableAttribute(): bool
    {
        $activeCasesCount = $this->active_cases->count();
        
        // Consider lawyer available if they have less than 20 active cases
        return $activeCasesCount < 20;
    }

    /**
     * Get lawyer's availability status
     */
    public function getAvailabilityStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }
        
        if ($this->is_available) {
            return 'Available';
        }
        
        return 'Busy';
    }

    /**
     * Get lawyer's availability color
     */
    public function getAvailabilityColorAttribute(): string
    {
        if (!$this->is_active) {
            return 'secondary';
        }
        
        if ($this->is_available) {
            return 'success';
        }
        
        return 'warning';
    }

    /**
     * Get lawyer's full information
     */
    public function getFullInfoAttribute(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'initials' => $this->initials,
            'contact_info' => $this->contact_info,
            'professional_info' => $this->professional_info,
            'type' => $this->type_label,
            'status' => $this->status_label,
            'availability' => $this->availability_status,
            'is_custom' => $this->is_custom,
            'user_id' => $this->user_id,
            'profile_picture' => $this->profile_picture_url,
            'stats' => $this->stats,
            'notes' => $this->notes,
            'created_at' => $this->created_at->format('M d, Y'),
            'updated_at' => $this->updated_at->format('M d, Y'),
        ];
    }
}