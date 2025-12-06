<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginSecurity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $key = 'login-attempts:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds."
            ]);
        }

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            RateLimiter::hit($key);
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        if (!$user->is_active) {
            return back()->withErrors(['email' => 'Your account has been deactivated.']);
        }

        $loginSecurity = $user->loginSecurity;

        if ($loginSecurity && $loginSecurity->isLocked()) {
            return back()->withErrors(['email' => 'Account temporarily locked. Please try again later.']);
        }

        if (!Hash::check($request->password, $user->password)) {
            if ($loginSecurity) {
                $loginSecurity->incrementFailedAttempts();
            }
            RateLimiter::hit($key);
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        // Reset failed attempts on successful password verification
        if ($loginSecurity) {
            $loginSecurity->resetFailedAttempts();
            $loginSecurity->update([
                'last_login_ip' => $request->ip(),
                'last_login_at' => now()
            ]);
        }

        RateLimiter::clear($key);

        // Check which 2FA method is enabled
        if ($user->hasTwoFactorEnabled()) {
            return $this->handleEmail2FA($user);
        }

        if ($user->hasGoogle2FAEnabled()) {
            return $this->handleGoogle2FA($user);
        }

        Auth::login($user, $request->remember);
        return redirect()->intended('/dashboard');
    }

    protected function handleEmail2FA(User $user)
    {
        $verificationCode = $user->generateVerificationCode();
        
        // Send verification code via email
        Mail::to($user->email)->send(new VerificationCodeMail($verificationCode));
        
        session(['2fa_user_id' => $user->id, '2fa_type' => 'email']);
        return redirect()->route('2fa.verify');
    }

    protected function handleGoogle2FA(User $user)
    {
        session(['2fa_user_id' => $user->id, '2fa_type' => 'google']);
        return redirect()->route('2fa.verify');
    }

    public function showTwoFactorVerification()
    {
        if (!session('2fa_user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('2fa_user_id'));
        $type = session('2fa_type');

        return view('auth.2fa-verify', compact('user', 'type'));
    }

    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'verification_code' => 'required'
        ]);

        $user = User::find(session('2fa_user_id'));
        $type = session('2fa_type');

        if (!$user) {
            return redirect()->route('login');
        }

        if ($type === 'email') {
            if (!$user->isVerificationCodeValid($request->verification_code)) {
                return back()->withErrors(['verification_code' => 'Invalid or expired verification code.']);
            }
            $user->clearVerificationCode();
        } elseif ($type === 'google') {
            if (!$user->verifyGoogle2FA($request->verification_code)) {
                return back()->withErrors(['verification_code' => 'Invalid Google Authenticator code.']);
            }
        } else {
            return redirect()->route('login');
        }

        Auth::login($user);
        session()->forget(['2fa_user_id', '2fa_type']);

        return redirect()->intended('/dashboard');
    }

    public function resendVerificationCode()
    {
        $user = User::find(session('2fa_user_id'));

        if ($user && session('2fa_type') === 'email') {
            $verificationCode = $user->generateVerificationCode();
            Mail::to($user->email)->send(new VerificationCodeMail($verificationCode));
            
            return back()->with('success', 'Verification code has been resent to your email.');
        }

        return redirect()->route('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}