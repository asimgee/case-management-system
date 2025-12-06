<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($user->profile_image) {
                Storage::delete('public/profile-images/' . $user->profile_image);
            }

            $imageName = time() . '.' . $request->profile_image->extension();
            $request->profile_image->storeAs('public/profile-images', $imageName);
            $user->profile_image = $imageName;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed'
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    public function enableEmail2FA(Request $request)
    {
        $user = Auth::user();
        $user->two_factor_enabled = true;
        $user->save();

        return back()->with('success', 'Email two-factor authentication has been enabled.');
    }

    public function disableEmail2FA(Request $request)
    {
        $user = Auth::user();
        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        return back()->with('success', 'Email two-factor authentication has been disabled.');
    }

    public function showGoogle2FASetup()
    {
        $user = Auth::user();
        
        if (!$user->google2fa_secret) {
            $user->generateGoogle2FASecret();
        }

        $qrCodeUrl = $user->getGoogle2FAQRCode();
        $secret = $user->google2fa_secret;

        return view('profile.google2fa-setup', compact('qrCodeUrl', 'secret'));
    }

    public function enableGoogle2FA(Request $request)
    {
        $request->validate([
            'verification_code' => 'required'
        ]);

        $user = Auth::user();

        if (!$user->verifyGoogle2FA($request->verification_code)) {
            return back()->withErrors(['verification_code' => 'Invalid verification code.']);
        }

        $user->google2fa_enabled = true;
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Google Authenticator has been enabled.');
    }

    public function disableGoogle2FA(Request $request)
    {
        $user = Auth::user();
        $user->google2fa_enabled = false;
        $user->google2fa_secret = null;
        $user->save();

        return back()->with('success', 'Google Authenticator has been disabled.');
    }
}