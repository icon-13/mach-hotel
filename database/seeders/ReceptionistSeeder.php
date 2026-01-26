<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ReceptionistSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('RECEPTION_EMAIL', 'reception@machhotel.com');
        $name  = env('RECEPTION_NAME', 'Reception Desk');
        $pass  = env('RECEPTION_PASSWORD', 'Reception@12345');

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->role = 'receptionist';

            if (Schema::hasColumn('users', 'is_active')) {
                $user->is_active = true;
            }

            $user->save();
            return;
        }

        $data = [
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($pass),
            'role'     => 'reception',
        ];

        if (Schema::hasColumn('users', 'is_active')) {
            $data['is_active'] = true;
        }

        User::create($data);
    }
}
