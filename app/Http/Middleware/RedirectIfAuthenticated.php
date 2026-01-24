<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // If no guards passed, Laravel often means "web"
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {

            if (Auth::guard($guard)->check()) {

                /**
                 * ✅ If staff is logged in (reception guard), send to correct landing
                 */
                if ($guard === 'reception') {
                    $u = Auth::guard('reception')->user();

                    if ($u && $u->role === 'admin') {
                        return redirect()->route('reception.admin.rooms.index');
                    }

                    return redirect()->route('reception.bookings.index');
                }

                /**
                 * ✅ If normal web user is logged in, DO NOT go to /dashboard
                 * Because you redirect /dashboard -> / anyway.
                 */
                return redirect()->route('home');
            }
        }

        return $next($request);
    }
}
