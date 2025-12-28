<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoginSecurity;
use App\Models\Role;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class SocialLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $socialUser = Socialite::driver('google')->stateless()->user();
            return $this->handleSocialUser($socialUser, 'google');
        } catch (\Exception $e) {
            return redirect()->route('register')
                ->withErrors(['social_error' => 'Google authentication failed. Please try again.'.$e]);
        }
    }

    public function redirectToMicrosoft()
    {
        return Socialite::driver('microsoft')->stateless()->redirect();
    }

    public function handleMicrosoftCallback()
    {
        try {
            $socialUser = Socialite::driver('microsoft')->stateless()->user();
            return $this->handleSocialUser($socialUser, 'microsoft');
        } catch (\Exception $e) {
            return redirect()->route('register')
                ->withErrors(['social_error' => 'Microsoft authentication failed. Please try again.']);
        }
    }

    private function handleSocialUser($socialUser, $provider)
    {
        // Check if user already exists
        $existingUser = User::where('email', $socialUser->getEmail())->first();

        if ($existingUser) {
            // Update social ID if not set
            if (!$existingUser->social_id) {
                $existingUser->update([
                    'social_id' => $socialUser->getId(),
                    'social_provider' => $provider,
                    'social_avatar' => $socialUser->getAvatar(),
                ]);
            }

            Auth::login($existingUser);
            return redirect()->route('dashboard')
                ->with('success', 'Welcome back! You have successfully logged in.');
        }

        // Create new user
        $defaultRole = Role::getDefaultRole();
        $defaultPlan = Plan::getDefaultPlan();

        $user = User::create([
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'password' => Hash::make(Str::random(24)), // Random password for social login
            'role_id' => $defaultRole->id,
            'current_plan_id' => $defaultPlan->id,
            'social_id' => $socialUser->getId(),
            'social_provider' => $provider,
            'social_avatar' => $socialUser->getAvatar(),
            'email_verified_at' => now(), // Social emails are considered verified
            'is_active' => true
        ]);

        // Create login security record
        LoginSecurity::create([
            'user_id' => $user->id,
            'login_notifications' => true,
            'two_factor_required' => false
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Welcome to AI Legal Assistant! Your account has been created successfully.');
    }

    public function redirectToApple()
    {
        // Apple Sign In requires additional setup
        // For now, redirect to registration with message
        return redirect()->route('register')
            ->with('info', 'Apple Sign In is coming soon. Please use email or other social providers.');
    }
}