<?php
// [file name]: app/Http/Middleware/SocialLoginMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialLoginMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->isSocialUser()) {
            // Social users cannot change password via regular form
            if ($request->is('password/*')) {
                return redirect()->route('dashboard')
                    ->with('info', 'Password management is not available for social login accounts.');
            }
        }
        
        return $next($request);
    }
}