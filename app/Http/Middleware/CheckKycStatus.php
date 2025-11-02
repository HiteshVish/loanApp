<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckKycStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Admin bypass - admins don't need KYC
        if ($user->isAdmin()) {
            return $next($request);
        }

        // If KYC not submitted, redirect to KYC form
        if (!$user->hasSubmittedKyc()) {
            if (!$request->routeIs('kyc.*') && !$request->routeIs('logout')) {
                return redirect()->route('kyc.create');
            }
        }

        // If KYC pending/rejected, show status page (block access to dashboard)
        if (!$user->isKycApproved()) {
            if (!$request->routeIs('kyc.*') && !$request->routeIs('logout') && !$request->routeIs('account.*')) {
                return redirect()->route('kyc.status');
            }
        }

        return $next($request);
    }
}
