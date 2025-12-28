<?php
// [file name]: app/Http/Controllers/Auth/RegisterController.php - UPDATED FINAL VERSION
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoginSecurity;
use App\Models\Role;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $plans = Plan::where('is_active', true)->get();
        return view('auth.register', compact('plans'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'plan_id' => 'nullable|exists:plans,id',
            'terms' => 'required'
        ], [
            'terms.required' => 'You must accept the terms and conditions.',
            'email.unique' => 'This email is already registered. Try signing in instead.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check for social login conflicts
        $existingSocialUser = User::where('email', $request->email)
            ->whereNotNull('social_id')
            ->first();

        if ($existingSocialUser) {
            $provider = ucfirst($existingSocialUser->social_provider);
            return redirect()->back()
                ->withErrors([
                    'email' => "This email is already registered via $provider. Please sign in using $provider."
                ])
                ->withInput();
        }

        // Get default role and plan
        $defaultRole = Role::getDefaultRole();
        $selectedPlan = $request->plan_id ? Plan::find($request->plan_id) : Plan::getDefaultPlan();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $defaultRole->id,
            'current_plan_id' => $selectedPlan->id,
            'email_verified_at' => $selectedPlan->requires_email_verification ? null : now(),
            'is_active' => true
        ]);

        // Create login security record
        LoginSecurity::create([
            'user_id' => $user->id,
            'login_notifications' => true,
            'two_factor_required' => false,
            'social_login_enabled' => true
        ]);

        // Create subscription record for non-free plans
        if (!$selectedPlan->isFree()) {
            \App\Models\Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $selectedPlan->id,
                'status' => 'pending',
                'trial_ends_at' => $selectedPlan->trial_days > 0 ? now()->addDays($selectedPlan->trial_days) : null
            ]);
        }

        Auth::login($user);

        // Send email verification if needed
        if ($selectedPlan->requires_email_verification && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            return redirect()->route('verification.notice')
                ->with('success', 'Welcome! Please verify your email address.');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Welcome to AI Legal Assistant! Your account has been created successfully.');
    }
}