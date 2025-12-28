<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginSecurity extends Model
{
    use HasFactory;
    protected $table = 'login_security';
    protected $fillable = [
        'user_id',
        'login_notifications',
        'two_factor_required',
        'failed_login_attempts',
        'locked_until',
        'last_login_ip',
        'last_login_at'
    ];

    protected $casts = [
        'login_notifications' => 'boolean',
        'two_factor_required' => 'boolean',
        'locked_until' => 'datetime',
        'last_login_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isLocked()
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    public function incrementFailedAttempts()
    {
        $this->failed_login_attempts++;
        
        if ($this->failed_login_attempts >= 5) {
            $this->locked_until = now()->addMinutes(30);
        }
        
        $this->save();
    }

    public function resetFailedAttempts()
    {
        $this->failed_login_attempts = 0;
        $this->locked_until = null;
        $this->save();
    }
}