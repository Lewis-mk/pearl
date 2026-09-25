<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user()) {
            return redirect()->route('login')->with('error', 'Please log in to continue.');
        }

        if (!$request->user()->isActive()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account is inactive or pending.');
        }

        if (!$request->user()->hasPermission($permission)) {
            abort(403, "Forbidden. You do not hold the required permission: '{$permission}'.");
        }

        return $next($request);
    }
}
