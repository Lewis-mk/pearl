<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login')->with('error', 'Please log in to access this portal.');
        }

        if (!$request->user()->isActive()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account is deactivated or pending approval. Please contact administration.');
        }

        // Admin has universal portal access
        if ($request->user()->hasRole('admin')) {
            return $next($request);
        }

        if (!$request->user()->hasRole($roles)) {
            abort(403, 'Unauthorized. You do not possess the required role for this portal.');
        }

        return $next($request);
    }
}
