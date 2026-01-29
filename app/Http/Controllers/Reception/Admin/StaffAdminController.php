<?php

namespace App\Http\Controllers\Reception\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Support\Audit;



class StaffAdminController extends Controller
{
    public function index()
    {
        $staff = User::whereIn('role', ['admin', 'reception'])
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return view('reception.admin.staff.index', compact('staff'));
    }

    public function create()

    {
        return view('reception.admin.staff.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', Rule::in(['admin', 'reception'])],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'role'     => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()
            ->route('reception.admin.staff.index')
            ->with('success', 'Staff account created successfully.');
    }

    public function edit(User $staff)
    {
        abort_unless(in_array($staff->role, ['admin', 'reception']), 404);

        return view('reception.admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, User $staff)
    {
        abort_unless(in_array($staff->role, ['admin', 'reception']), 404);

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($staff->id)],
            'role'  => ['required', Rule::in(['admin', 'reception'])],
        ]);

        // Prevent demoting yourself from admin
        if ($request->user('reception')->id === $staff->id && $data['role'] !== 'admin') {
            return back()->withErrors(['role' => 'You cannot remove your own admin role.']);
        }

        $staff->update($data);

        Audit::log('staff.update', 'User', $staff->id, [
         'updated_fields' => array_keys($data),
         ]);


        return redirect()
            ->route('reception.admin.staff.index')
            ->with('success', 'Staff account updated.');
    }

    public function resetPassword(Request $request, User $staff)
    {
        abort_unless(in_array($staff->role, ['admin', 'reception']), 404);

        $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        $staff->update([
            'password' => Hash::make($request->password),
        ]);

        Audit::log('staff.reset_password', 'User', $staff->id, [], 'warning');


        return back()->with('success', 'Password reset successfully.');
    }

    public function toggleActive(User $staff)
    {
        abort_unless(in_array($staff->role, ['admin', 'reception']), 404);

        // Prevent deactivating yourself
        if ($staff->id === auth('reception')->id()) {
            return back()->withErrors(['error' => 'You cannot deactivate your own account.']);
        }

        $staff->update([
            'is_active' => ! $staff->is_active,
        ]);

        Audit::log('staff.toggle_active', 'User', $staff->id, [
         'is_active' => $staff->is_active,
          ], 'warning');


        return back()->with('success', 'Staff status updated.');
    }
}
