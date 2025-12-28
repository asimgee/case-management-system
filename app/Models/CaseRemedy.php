<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseRemedy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'is_custom',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cases()
    {
        return $this->hasMany(Case_Model::class);
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

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}