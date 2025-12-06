<?php
// [file name]: RegisterController.php - UPDATED
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
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
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
            'is_active' => true
        ]);

        // Create login security record
        LoginSecurity::create([
            'user_id' => $user->id,
            'login_notifications' => true,
            'two_factor_required' => false
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

        return redirect()->route('dashboard')->with('success', 'Welcome to AI Legal Assistant! Your account has been created successfully.');
    }
}