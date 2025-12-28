<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HearingExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'hearing_id',
        'description',
        'amount',
        'category',
        'date',
        'paid_by',
        'payment_method',
        'receipt_number',
        'receipt_path',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date'
    ];

    protected $appends = [
        'formatted_date',
        'formatted_amount',
        'category_label'
    ];

    // Relationships
    public function hearing(): BelongsTo
    {
        return $this->belongsTo(Hearing::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getFormattedDateAttribute(): string
    {
        return $this->date ? $this->date->format('M d, Y') : 'N/A';
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'PKR ' . number_format($this->amount, 2);
    }

    public function getCategoryLabelAttribute(): string
    {
        $categories = [
            'travel' => 'Travel',
            'accommodation' => 'Accommodation',
            'food' => 'Food & Beverages',
            'court_fees' => 'Court Fees',
            'documentation' => 'Documentation',
            'witness_fees' => 'Witness Fees',
            'expert_fees' => 'Expert Fees',
            'miscellaneous' => 'Miscellaneous',
            'other' => 'Other'
        ];
        
        return $categories[$this->category] ?? ucfirst($this->category);
    }

    public function getCategoryColorAttribute(): string
    {
        $colors = [
            'travel' => 'blue',
            'accommodation' => 'green',
            'food' => 'yellow',
            'court_fees' => 'red',
            'documentation' => 'purple',
            'witness_fees' => 'pink',
            'expert_fees' => 'indigo',
            'miscellaneous' => 'gray'
        ];
        
        return $colors[$this->category] ?? 'secondary';
    }
}