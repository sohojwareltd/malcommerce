<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordSetup
{
    /**
     * Handle an incoming request.
     * Redirect users without password to password setup page
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user needs to set password (has no password and is admin or sponsor)
            if (empty($user->password) && ($user->isAdmin() || $user->isSponsor())) {
                // Allow access to password setup routes (profile edit and password update)
                $allowedRoutes = [
                    'admin.profile.edit',
                    'admin.profile.update',
                    'admin.profile.update-password',
                    'admin.profile.change-password',
                    'sponsor.profile.edit',
                    'sponsor.profile.update',
                    'sponsor.profile.update-password',
                ];
                
                $routeName = $request->route() ? $request->route()->getName() : null;
                
                if (!in_array($routeName, $allowedRoutes)) {
                    if ($user->isAdmin()) {
                        return redirect()->route('admin.profile.edit')->with('password_required', 'Please set a password to continue.');
                    } elseif ($user->isSponsor()) {
                        return redirect()->route('sponsor.profile.edit')->with('password_required', 'Please set a password to continue.');
                    }
                }
            }
        }
        
        return $next($request);
    }
}
