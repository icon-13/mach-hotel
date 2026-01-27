<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ReceptionAuthController extends Controller
{
    /**
     * Show reception login form
     */
    public function showLogin(Request $request)
    {
        // If already logged in, go to correct landing
        if (Auth::guard('reception')->check()) {
            $u = Auth::guard('reception')->user();

            return ($u && $u->role === 'admin')
                ? redirect()->route('reception.admin.rooms.index')
                : redirect()->route('reception.bookings.index');
        }

        // ✅ Prevent old intended redirects (like /dashboard) hijacking staff login
        $request->session()->forget('url.intended');

        // ✅ IMPORTANT: Refresh CSRF token so login form always has a valid token
        // This kills “419 after inactivity / old tab” issues.
        $request->session()->regenerateToken();

        return view('reception.auth.login');
    }

    /**
     * Handle reception login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::guard('reception')->attempt($request->only('email', 'password'), $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid email or password.',
            ]);
        }

        $user = Auth::guard('reception')->user();

        // Allow only staff roles into reception
        if (!$user || !in_array($user->role, ['admin', 'reception'], true)) {
            Auth::guard('reception')->logout();

            throw ValidationException::withMessages([
                'email' => 'This account is not allowed to access Reception.',
            ]);
        }

        // Block inactive staff accounts
        if (!$user->is_active) {
            Auth::guard('reception')->logout();

            throw ValidationException::withMessages([
                'email' => 'This account is inactive. Contact administrator.',
            ]);
        }

        // ✅ SECURITY: new session id after login
        $request->session()->regenerate();

        // ✅ CRITICAL: regenerate CSRF token after session regenerate
        // Prevents the “logged in but 419 appears” from stale token / double submit.
        $request->session()->regenerateToken();

        // ✅ Role landing
        $target = ($user->role === 'admin')
            ? route('reception.admin.rooms.index')
            : route('reception.bookings.index');

        /**
         * ✅ Only respect "intended" if it points INSIDE /reception
         */
        $intended = session()->pull('url.intended');
        if ($intended && str_starts_with($intended, url('/reception'))) {
            return redirect()->to($intended);
        }

        return redirect()->to($target);
    }

    /**
     * Logout reception staff (POST)
     */
    public function logout(Request $request)
    {
        Auth::guard('reception')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('reception.login')->with('success', 'Logged out successfully.');
    }

    /**
     * Logout via GET (fallback only; don't link it in UI)
     */
    public function logoutGet(Request $request)
    {
        if (Auth::guard('reception')->check()) {
            Auth::guard('reception')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('reception.login')->with('success', 'Logged out successfully.');
    }
}
