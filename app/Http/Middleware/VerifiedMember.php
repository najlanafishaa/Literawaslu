<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifiedMember
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && in_array($user->role, ['user', 'member'])) {
            $member = $user->member;
            if ($member && !$member->is_verified) {
                // Allow logout and the unverified page itself
                if ($request->routeIs('unverified') || $request->routeIs('logout')) {
                    return $next($request);
                }
                return redirect()->route('unverified');
            }
        }

        // If verified member or admin tries to visit /unverified, send them to dashboard
        if ($request->routeIs('unverified')) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
