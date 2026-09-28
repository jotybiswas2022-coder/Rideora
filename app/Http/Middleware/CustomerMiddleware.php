<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Customer area: any signed in user with an active account.
     *
     * Administrators are allowed through as well so they can rent and pay for a
     * vehicle like any other customer. Own-booking ownership rules still apply
     * through the booking policies.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Please sign in to continue.');
        }

        if (Auth::user()->status !== 'active') {
            Auth::logout();

            return redirect()->route('login')->with('error', 'Your account has been deactivated. Please contact support.');
        }

        return $next($request);
    }
}
