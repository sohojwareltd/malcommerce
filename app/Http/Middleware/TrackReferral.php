<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackReferral
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for referral code in query string
        if ($request->has('ref')) {
            $referralCode = $request->query('ref');
            // Store in session for 30 days
            session(['referral_code' => $referralCode]);
            // Also set a cookie for persistence
            cookie()->queue('referral_code', $referralCode, 60 * 24 * 30); // 30 days
        } elseif ($request->cookie('referral_code') && !session()->has('referral_code')) {
            // Restore from cookie if session doesn't have it
            session(['referral_code' => $request->cookie('referral_code')]);
        }
        
        // Store product parameter if present (for redirect after login)
        if ($request->has('product')) {
            session(['login_redirect_product' => $request->query('product')]);
        }
        
        return $next($request);
    }
}
