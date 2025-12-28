<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HearingReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'hearing_id',
        'reminder_date',
        'reminder_time',
        'message',
        'sent',
        'sent_at',
        'sent_via',
        'recipients',
        'created_by'
    ];

    protected $casts = [
        'reminder_date' => 'date',
        'reminder_time' => 'datetime',
        'sent' => 'boolean',
        'sent_at' => 'datetime',
        'recipients' => 'array'
    ];

    protected $appends = [
        'formatted_reminder_date',
        'formatted_reminder_time',
        'is_past_due',
        'status_label'
    ];

    // Relationships
    public function hearing(): BelongsTo
    {
        return $this->belongsTo(Hearing::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getFormattedReminderDateAttribute(): string
    {
        return $this->reminder_date ? $this->reminder_date->format('M d, Y') : 'N/A';
    }

    public function getFormattedReminderTimeAttribute(): string
    {
        return $this->reminder_time ? $this->reminder_time->format('h:i A') : 'N/A';
    }

    public function getIsPastDueAttribute(): bool
    {
        if (!$this->reminder_date) {
            return false;
        }
        
        return $this->reminder_date->isPast() && !$this->sent;
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->sent) {
            return 'Sent';
        }
        
        if ($this->is_past_due) {
            return 'Past Due';
        }
        
        return 'Pending';
    }

    public function getStatusColorAttribute(): string
    {
        if ($this->sent) {
            return 'success';
        }
        
        if ($this->is_past_due) {
            return 'danger';
        }
        
        return 'warning';
    }

    // Methods
    public function markAsSent($via = 'email'): bool
    {
        $this->sent = true;
        $this->sent_at = now();
        $this->sent_via = $via;
        return $this->save();
    }
}