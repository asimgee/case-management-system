<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lawyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'license_number',
        'address',
        'is_custom',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedCases()
    {
        return $this->hasMany(Case_Model::class, 'assigned_lawyer_id');
    }

    public function additionalCases()
    {
        return $this->hasMany(Case_Model::class, 'additional_lawyer_id');
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
}