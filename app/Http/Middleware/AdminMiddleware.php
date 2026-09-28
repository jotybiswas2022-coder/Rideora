<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Allow only authenticated administrators through.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Please sign in to continue.');
        }

        if (! Auth::user()->isAdmin()) {
            return redirect()
                ->route('customer.dashboard')
                ->with('error', 'You do not have permission to access the admin panel.');
        }

        if (Auth::user()->status !== 'active') {
            Auth::logout();

            return redirect()->route('login')->with('error', 'Your account has been deactivated.');
        }

        return $next($request);
    }
}
