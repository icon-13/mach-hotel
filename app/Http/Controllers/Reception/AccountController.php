<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Auth\StatefulGuard;

class AccountController extends Controller
{
    public function editPassword()
    {
        return view('reception.account.password');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => [
                'required',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],
        ]);

        /** @var User|null $user */
        $user = Auth::guard('reception')->user();

        if (!$user) {
            return redirect()->route('reception.login');
        }

        if (!Hash::check($data['current_password'], (string) $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->withInput();
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();

        $request->session()->regenerate();

        return back()->with('success', 'Password updated successfully.');
    }
}
