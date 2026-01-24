<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StaffAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'jimmymjema@gmail.com';

        // Prevent duplicates (safe to re-run)
        $admin = User::where('email', $email)->first();

        if ($admin) {
            // Ensure role is admin (in case user existed)
            if ($admin->role !== 'admin') {
                $admin->role = 'admin';
                $admin->save();
            }

            return;
        }

        User::create([
            'name'     => 'jimmy',
            'email'    => $email,
            'password' => Hash::make('12345678'),
            'role'     => 'admin',
        ]);
    }
}
