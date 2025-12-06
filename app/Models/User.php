<?php
// [file name]: User.php - UPDATED
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PragmaRX\Google2FAQRCode\Google2FA;

class User extends Authenticatable
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
        'current_plan_id',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'google2fa_secret'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'verification_code_expires_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'google2fa_enabled' => 'boolean',
        'is_active' => 'boolean'
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
        return $this->hasOne(Subscription::class)->where('status', 'active');
    }

    public function currentPlan()
    {
        return $this->belongsTo(Plan::class, 'current_plan_id');
    }

    public function cases()
    {
        return $this->hasMany(Case_Model::class);
    }

    // Role and Permission Methods
public function hasRole($role)
{
    if ($this->relationLoaded('role') || $this->role instanceof \App\Models\Role) {
        return $this->role->name === $role;
    }

    // fallback if the DB still stores a string in 'role' column
    return $this->attributes['role_id'] === $role || $this->attributes['role'] === $role;
}


    public function hasPermission($permission)
    {
        return $this->role && in_array($permission, $this->role->permissions ?? []);
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    // Subscription Methods
    public function hasActiveSubscription()
    {
        return $this->activeSubscription && $this->activeSubscription->isActive();
    }

    public function isOnTrial()
    {
        return $this->activeSubscription && $this->activeSubscription->isOnTrial();
    }

    public function canCreateCase()
    {
        if ($this->isAdmin()) return true;
        
        if (!$this->currentPlan) return false;
        
        if ($this->currentPlan->max_cases === 0) return true;
        
        return $this->cases()->count() < $this->currentPlan->max_cases;
    }

    public function getRemainingCases()
    {
        if (!$this->currentPlan || $this->currentPlan->max_cases === 0) {
            return 'unlimited';
        }
        
        return max(0, $this->currentPlan->max_cases - $this->cases()->count());
    }

    // Existing 2FA methods remain the same...
    public function hasTwoFactorEnabled()
    {
        return $this->two_factor_enabled;
    }

    public function hasGoogle2FAEnabled()
    {
        return $this->google2fa_enabled && !empty($this->google2fa_secret);
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
        $google2fa = new Google2FA();
        return $google2fa->verifyKey($this->google2fa_secret, $code);
    }

    public function getGoogle2FAQRCode()
    {
        $google2fa = new Google2FA();
        return $google2fa->getQRCodeUrl(
            config('app.name'),
            $this->email,
            $this->google2fa_secret
        );
    }
}