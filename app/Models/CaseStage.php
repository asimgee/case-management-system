<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_id',
        'stage_name',
        'description',
        'start_date',
        'end_date',
        'status',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function case()
    {
        return $this->belongsTo(Case_Model::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}