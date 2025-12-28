<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PragmaRX\Google2FAQRCode\Google2FA;
 
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $with = ['role'];
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'google2fa_secret',
        'google2fa_enabled',
        'verification_code',
        'verification_code_expires_at',
        'profile_image',
        'phone',
        'address',
        'bio',
        'current_plan_id',
        'social_id',
        'social_provider',
        'social_avatar',
        'email_verified_at',
        'last_login_at',
        'last_login_ip',
        'settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'google2fa_secret',
        'verification_code',
        'social_id',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'verification_code_expires_at' => 'datetime',
        'last_login_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'google2fa_enabled' => 'boolean',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function loginSecurity()
    {
        return $this->hasOne(LoginSecurity::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latest();
    }

    public function currentPlan()
    {
        return $this->belongsTo(Plan::class, 'current_plan_id');
    }

    public function cases()
    {
        return $this->hasMany(Case_Model::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function hearings()
    {
        return $this->hasManyThrough(Hearing::class, Case_Model::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Attribute Accessors
    public function getIsSocialUserAttribute()
    {
        return !is_null($this->social_id);
    }

    public function getAvatarAttribute()
    {
        if ($this->profile_image) {
            return Storage::url($this->profile_image);
        }
        
        if ($this->social_avatar) {
            return $this->social_avatar;
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function getInitialsAttribute()
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

    public function getIsOnlineAttribute()
    {
        if (!$this->last_login_at) {
            return false;
        }
        
        return $this->last_login_at->diffInMinutes(now()) < 5;
    }

    public function getStatusColorAttribute()
    {
        if (!$this->is_active) {
            return 'danger';
        }
        
        if ($this->is_online) {
            return 'success';
        }
        
        return 'secondary';
    }

    public function getStatusTextAttribute()
    {
        if (!$this->is_active) {
            return 'Inactive';
        }
        
        if ($this->is_online) {
            return 'Online';
        }
        
        return 'Offline';
    }

    public function getLastLoginFormattedAttribute()
    {
        if (!$this->last_login_at) {
            return 'Never';
        }
        
        return $this->last_login_at->diffForHumans();
    }

    // Role and Permission Methods
    public function hasRole($role)
    {
        if ($this->role) {
            return $this->role->name === $role;
        }
        
        return false;
    }
    public function hasPermission($permission)
    {
        if (!$this->role) {
            return false;
        }
        
        return $this->role->hasPermission($permission);
    }

    public function isAdmin()
    
        {
        
        if (!$this->currentPlan) {
            return false;
        }
        
        if ($this->currentPlan->max_documents === 0) {
            return true;
        }
        
        return $this->documents()->count() < $this->currentPlan->max_documents;
    }

    public function getRemainingDocuments()
    {
        if (!$this->currentPlan || $this->currentPlan->max_documents === 0) {
            return 'unlimited';
        }
        
        $remaining = $this->currentPlan->max_documents - $this->documents()->count();
        return max(0, $remaining);
    }

    // Two-Factor Authentication Methods
    public function hasTwoFactorEnabled()
    {
        return $this->two_factor_enabled;
    }

    public function hasGoogle2FAEnabled()
    {
        return $this->google2fa_enabled && !empty($this->google2fa_secret);
    }

    public function hasAny2FAEnabled()
    {
        return $this->hasTwoFactorEnabled() || $this->hasGoogle2FAEnabled();
    }

    public function generateVerificationCode()
    {
        $this->verification_code = rand(100000, 999999);
        $this->verification_code_expires_at = now()->addMinutes(10);
        $this->save();

        return $this->verification_code;
    }

    public function isVerificationCodeValid($code)
    {
        if (!$this->verification_code || !$this->verification_code_expires_at) {
            return false;
        }
        
        return $this->verification_code === $code && 
               $this->verification_code_expires_at->isFuture();
    }

    public function clearVerificationCode()
    {
        $this->verification_code = null;
        $this->verification_code_expires_at = null;
        $this->save();
    }

    public function generateGoogle2FASecret()
    {
        $google2fa = new Google2FA();
        $this->google2fa_secret = $google2fa->generateSecretKey();
        $this->save();

        return $this->google2fa_secret;
    }

    public function verifyGoogle2FA($code)
    {
        if (!$this->google2fa_secret) {
            return false;
        }
        
        $google2fa = new Google2FA();
        return $google2fa->verifyKey($this->google2fa_secret, $code);
    }

    public function getGoogle2FAQRCode()
    {
        if (!$this->google2fa_secret) {
            return null;
        }
        
        $google2fa = new Google2FA();
        return $google2fa->getQRCodeUrl(
            config('app.name'),
            $this->email,
            $this->google2fa_secret
        );
    }

    public function generateBackupCodes()
    {
        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(5)));
        }
        
        $this->two_factor_recovery_codes = json_encode($codes);
        $this->save();
        
        return $codes;
    }

    public function getBackupCodes()
    {
        if (!$this->two_factor_recovery_codes) {
            return [];
        }
        
        return json_decode($this->two_factor_recovery_codes, true);
    }

    public function verifyBackupCode($code)
    {
        $codes = $this->getBackupCodes();
        $index = array_search($code, $codes);
        
        if ($index !== false) {
            unset($codes[$index]);
            $this->two_factor_recovery_codes = json_encode(array_values($codes));
            $this->save();
            return true;
        }
        
        return false;
    }

    // Login and Activity Methods
    public function recordLogin($ipAddress)
    {
        $this->last_login_at = now();
        $this->last_login_ip = $ipAddress;
        $this->save();
    }

    public function getLoginHistory($limit = 10)
    {
        // You might want to create a separate login_history table
        // For now, return empty array
        return [];
    }

    // Settings Methods
    public function getSetting($key, $default = null)
    {
        $settings = $this->settings ?? [];
        return $settings[$key] ?? $default;
    }

    public function setSetting($key, $value)
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
        $this->save();
    }

    public function removeSetting($key)
    {
        $settings = $this->settings ?? [];
        if (isset($settings[$key])) {
            unset($settings[$key]);
            $this->settings = $settings;
            $this->save();
        }
    }

    // Notification Methods
    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    public function markNotificationsAsRead()
    {
        $this->unreadNotifications()->update(['read_at' => now()]);
    }

    public function sendNotification($data)
    {
        return $this->notifications()->create([
            'type' => $data['type'] ?? 'general',
            'title' => $data['title'],
            'message' => $data['message'],
            'data' => $data['data'] ?? null,
            'read_at' => null,
        ]);
    }

    // Scope Methods
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeAdmins($query)
    {
        return $query->whereHas('role', function($q) {
            $q->whereIn('name', ['admin', 'super_admin']);
        });
    }

    public function scopeRegularUsers($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('name', 'user');
        });
    }

    public function scopeWithActiveSubscription($query)
    {
        return $query->whereHas('activeSubscription');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('email', 'like', '%' . $search . '%')
              ->orWhere('phone', 'like', '%' . $search . '%');
        });
    }

    // Statistics Methods
    public function getStatisticsAttribute()
    {
        return [
            'total_cases' => $this->cases()->count(),
            'active_cases' => $this->cases()->where('case_status', '!=', 'closed')->count(),
            'total_documents' => $this->documents()->count(),
            'total_clients' => $this->clients()->count(),
            'upcoming_hearings' => $this->hearings()->where('hearing_date', '>=', now())->count(),
        ];
    }

    public function getMonthlyCaseStats($months = 6)
    {
        $stats = [];
        $now = now();
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            
            $stats[$month->format('M Y')] = $this->cases()
                ->whereBetween('created_at', [$start, $end])
                ->count();
        }
        
        return $stats;
    }

    // Helper Methods
    public function activate()
    {
        $this->is_active = true;
        $this->save();
        return $this;
    }

    public function deactivate()
    {
        $this->is_active = false;
        $this->save();
        return $this;
    }

    public function toggleStatus()
    {
        $this->is_active = !$this->is_active;
        $this->save();
        return $this;
    }

    public function changeRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->role_id = $role->id;
            $this->save();
        }
        return $this;
    }

    public function changePassword($newPassword)
    {
        $this->password = bcrypt($newPassword);
        $this->save();
        return $this;
    }

    public function isEmailVerified()
    {
        return !is_null($this->email_verified_at);
    }

    public function markEmailAsVerified()
    {
        $this->email_verified_at = now();
        $this->save();
        return $this;
    }
}