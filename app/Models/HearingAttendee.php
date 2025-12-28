<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HearingAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'hearing_id',
        'name',
        'role',
        'contact',
        'organization',
        'designation',
        'attended',
        'arrival_time',
        'departure_time',
        'notes',
        'signature_path'
    ];

    protected $casts = [
        'attended' => 'boolean',
        'arrival_time' => 'datetime',
        'departure_time' => 'datetime'
    ];

    protected $appends = [
        'role_label',
        'attendance_status',
        'duration_minutes'
    ];

    // Relationships
    public function hearing(): BelongsTo
    {
        return $this->belongsTo(Hearing::class);
    }

    // Accessors
    public function getRoleLabelAttribute(): string
    {
        $roles = [
            'lawyer' => 'Lawyer',
            'client' => 'Client',
            'witness' => 'Witness',
            'judge' => 'Judge',
            'court_staff' => 'Court Staff',
            'expert' => 'Expert Witness',
            'translator' => 'Translator',
            'observer' => 'Observer',
            'other' => 'Other'
        ];
        
        return $roles[$this->role] ?? ucfirst($this->role);
    }

    public function getAttendanceStatusAttribute(): string
    {
        if ($this->attended) {
            return 'Present';
        }
        
        return 'Absent';
    }

    public function getDurationMinutesAttribute(): ?int
    {
        if (!$this->arrival_time || !$this->departure_time) {
            return null;
        }
        
        return $this->arrival_time->diffInMinutes($this->departure_time);
    }

    public function getRoleColorAttribute(): string
    {
        $colors = [
            'lawyer' => 'primary',
            'client' => 'success',
            'witness' => 'info',
            'judge' => 'warning',
            'court_staff' => 'secondary',
            'expert' => 'purple',
            'translator' => 'teal',
            'observer' => 'gray'
        ];
        
        return $colors[$this->role] ?? 'secondary';
    }

    // Methods
    public function markAttendance(bool $attended, $arrivalTime = null, $departureTime = null): bool
    {
        $this->attended = $attended;
        
        if ($attended) {
            $this->arrival_time = $arrivalTime ?? now();
            $this->departure_time = $departureTime;
        } else {
            $this->arrival_time = null;
            $this->departure_time = null;
        }
        
        return $this->save();
    }
}