<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PhysicalRoom;

class PhysicalRoomSeeder extends Seeder
{
    public function run(): void
    {
        // Example: create 21 rooms numbered 101–121 (change to match your real numbering)
        for ($i = 101; $i <= 121; $i++) {
            PhysicalRoom::firstOrCreate(
                ['room_number' => (string) $i],
                [
                    'room_type_id' => null,              // TBD by default
                    'status' => 'Available',
                    'is_bookable_online' => false,
                ]
            );
        }
    }
}
