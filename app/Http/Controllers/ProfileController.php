<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PragmaRX\Google2FAQRCode\Google2FA;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user()->load('currentPlan');
        $stats = [
            'total_cases' => $user->cases()->count(),
            'active_cases' => $user->cases()->where('case_status', '!=', 'closed')->count(),
            'closed_cases' => $user->cases()->where('case_status', 'closed')->count(),
            'total_documents' => $user->cases()->withCount('documents')->get()->sum('documents_count'),
        ];
        
        return view('profile.index', compact('user', 'stats'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'bio' => 'nullable|string|max:1000',
        ]);

        try {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->bio = $request->bio;

            if ($request->hasFile('profile_image')) {
                // Delete old profile image if exists
                if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                    Storage::disk('public')->delete($user->profile_image);
                }

                // Store new image
                $imageName = 'profile_' . $user->id . '_' . time() . '.' . $request->profile_image->extension();
                $path = $request->profile_image->storeAs('profile-images', $imageName, 'public');
                $user->profile_image = $path;
            }

            $user->save();

            return back()->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update profile: ' . $e->getMessage())->withInput();
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed|different:current_password',
            'new_password_confirmation' => 'required|string|min:8',
        ], [
            'new_password.different' => 'New password must be different from current password.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        try {
            $user->password = Hash::make($request->new_password);
            $user->save();

            // Logout from other devices (optional)
            Auth::logoutOtherDevices($request->new_password);

            return back()->with('success', 'Password updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update password: ' . $e->getMessage())->withInput();
        }
    }

    public function enableEmail2FA(Request $request)
    {
        $user = Auth::user();

        if ($user->hasGoogle2FAEnabled()) {
            return back()->with('error', 'Please disable Google Authenticator first to enable Email 2FA.');
        }

        try {
            $user->two_factor_enabled = true;
            $user->save();

            // Generate initial verification code
            $user->generateVerificationCode();

            return back()->with('success', 'Email two-factor authentication has been enabled. Please verify your email.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to enable email 2FA: ' . $e->getMessage());
        }
    }

    public function disableEmail2FA(Request $request)
    {
        $user = Auth::user();

        try {
            $user->two_factor_enabled = false;
            $user->two_factor_secret = null;
            $user->two_factor_recovery_codes = null;
            $user->verification_code = null;
            $user->verification_code_expires_at = null;
            $user->save();

            return back()->with('success', 'Email two-factor authentication has been disabled.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to disable email 2FA: ' . $e->getMessage());
        }
    }

    public function showGoogle2FASetup()
    {
        $user = Auth::user();

        if ($user->hasTwoFactorEnabled()) {
            return back()->with('error', 'Please disable Email 2FA first to setup Google Authenticator.');
        }

        // Generate new secret if not exists
        if (!$user->google2fa_secret) {
            $user->generateGoogle2FASecret();
            $user->refresh();
        }

        $google2fa = new Google2FA();
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );

        $secret = $user->google2fa_secret;
        
        // Generate backup codes
        $backupCodes = $this->generateBackupCodes();

        return view('profile.google2fa-setup', compact('qrCodeUrl', 'secret', 'backupCodes'));
    }

    public function enableGoogle2FA(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6',
            'backup_codes' => 'nullable|array',
        ]);

        $user = Auth::user();

        if ($user->hasTwoFactorEnabled()) {
            return back()->with('error', 'Please disable Email 2FA first to enable Google Authenticator.');
        }

        // Verify the code
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->verification_code);

        if (!$valid) {
            return back()->withErrors(['verification_code' => 'Invalid verification code.'])->withInput();
        }

        try {
            $user->google2fa_enabled = true;
            
            // Store backup codes if provided
            if ($request->filled('backup_codes')) {
                $user->two_factor_recovery_codes = json_encode($request->backup_codes);
            }
            
            $user->save();

            return redirect()->route('profile.index')->with('success', 'Google Authenticator has been enabled successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to enable Google Authenticator: ' . $e->getMessage())->withInput();
        }
    }

    public function disableGoogle2FA(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.'])->withInput();
        }

        try {
            $user->google2fa_enabled = false;
            $user->google2fa_secret = null;
            $user->two_factor_recovery_codes = null;
            $user->save();

            return back()->with('success', 'Google Authenticator has been disabled.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to disable Google Authenticator: ' . $e->getMessage());
        }
    }

    public function resendVerificationCode()
    {
        $user = Auth::user();

        if (!$user->two_factor_enabled) {
            return back()->with('error', 'Email 2FA is not enabled.');
        }

        try {
            $code = $user->generateVerificationCode();
            
            // Here you would typically send the code via email
            // For now, we'll just show it (in production, remove this)
            return back()->with('success', 'Verification code sent to your email. Code: ' . $code);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to resend verification code: ' . $e->getMessage());
        }
    }

    public function verifyEmail2FA(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if (!$user->isVerificationCodeValid($request->verification_code)) {
            return back()->withErrors(['verification_code' => 'Invalid or expired verification code.'])->withInput();
        }

        try {
            $user->clearVerificationCode();
            $user->email_verified_at = now();
            $user->save();

            return back()->with('success', 'Email verified successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to verify email: ' . $e->getMessage());
        }
    }

    public function downloadBackupCodes()
    {
        $user = Auth::user();
        
        if (!$user->two_factor_recovery_codes) {
            return back()->with('error', 'No backup codes available.');
        }

        $codes = json_decode($user->two_factor_recovery_codes, true);
        $content = implode("\n", $codes);
        
        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="backup-codes-' . date('Y-m-d') . '.txt"');
    }

    public function regenerateBackupCodes()
    {
        $user = Auth::user();

        if (!$user->hasTwoFactorEnabled() && !$user->hasGoogle2FAEnabled()) {
            return back()->with('error', '2FA is not enabled.');
        }

        try {
            $backupCodes = $this->generateBackupCodes();
            $user->two_factor_recovery_codes = json_encode($backupCodes);
            $user->save();

            return back()->with('success', 'Backup codes regenerated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to regenerate backup codes: ' . $e->getMessage());
        }
    }

    private function generateBackupCodes()
    {
        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(5))); // 10-character codes
        }
        return $codes;
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'confirmation' => 'required|string|in:DELETE',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password.'])->withInput();
        }

        try {
            // Delete profile image
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // Logout user
            Auth::logout();

            // Delete user (this will cascade delete related records based on foreign key constraints)
            $user->delete();

            return redirect()->route('login')->with('success', 'Your account has been permanently deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete account: ' . $e->getMessage());
        }
    }
}