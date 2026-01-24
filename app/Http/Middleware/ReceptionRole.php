<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReceptionRole
{
    /**
     * Handle an incoming request.
     *
     * Usage:
     *   ->middleware('reception.role:admin,reception')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Explicitly use the reception guard
        $user = auth('reception')->user();

        // Not logged in as reception staff
        if (!$user) {
            return redirect()->route('reception.login');
        }

        // Role not allowed
        if (!in_array($user->role, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
