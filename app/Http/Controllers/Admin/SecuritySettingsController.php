<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginSecurity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SecuritySettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $loginSecurity = $user->loginSecurity ?? new LoginSecurity();
        
        return view('admin.security-settings', compact('user', 'loginSecurity'));
    }

    public function updateSecuritySettings(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
            'login_notifications' => 'boolean',
            'two_factor_required' => 'boolean'
        ]);

        // Update password if provided
        if ($request->new_password) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();
        }

        // Update login security settings
        $loginSecurity = $user->loginSecurity ?? new LoginSecurity();
        $loginSecurity->user_id = $user->id;
        $loginSecurity->login_notifications = $request->login_notifications ?? false;
        $loginSecurity->two_factor_required = $request->two_factor_required ?? false;
        $loginSecurity->save();

        // Update 2FA setting
        $user->two_factor_enabled = $request->two_factor_enabled ?? false;
        $user->save();

        return back()->with('success', 'Security settings updated successfully.');
    }

    public function enableTwoFactor(Request $request)
    {
        $user = Auth::user();
        
        $user->two_factor_enabled = true;
        $user->save();

        return back()->with('success', 'Two-factor authentication has been enabled.');
    }

    public function disableTwoFactor(Request $request)
    {
        $user = Auth::user();
        
        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        return back()->with('success', 'Two-factor authentication has been disabled.');
    }
}