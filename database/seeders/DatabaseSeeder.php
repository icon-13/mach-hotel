<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ✅ Quota-based room types only (DDR & DSR)
        $this->call(RoomTypeSeeder::class);
        $this->call(RoomSeeder::class);
        $this->call(StaffAdminSeeder::class);
        $this->call(PhysicalRoomSeeder::class);



        // ✅ Optional test user (IDEMPOTENT: won't duplicate)
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'), // optional, but good
            ]
        );
    }
}
