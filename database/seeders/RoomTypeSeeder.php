<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        $hasCode   = Schema::hasColumn('room_types', 'code');
        $hasUsd    = Schema::hasColumn('room_types', 'price_usd');
        $hasQuota  = Schema::hasColumn('room_types', 'online_quota');

        // Price column can be either price_tzs OR price_per_night (your DB is clearly NOT price_per_night)
        $tzsColumn = Schema::hasColumn('room_types', 'price_tzs')
            ? 'price_tzs'
            : (Schema::hasColumn('room_types', 'price_per_night') ? 'price_per_night' : null);

        if (!$tzsColumn) {
            throw new \RuntimeException("No TZS price column found. Expected 'price_tzs' or 'price_per_night' in room_types.");
        }

        // DDR
        $ddr = [
            'name' => 'Deluxe Double Room (DDR)',
            'slug' => 'ddr',
            $tzsColumn => 40000, // TZS
            'capacity' => 2,
            'amenities' => ['Wi-Fi', 'A/C', 'TV'],
            'description' => 'Deluxe double room with premium comfort.',
            'is_active' => true,
        ];

        if ($hasCode)  $ddr['code'] = 'DDR';
        if ($hasUsd)   $ddr['price_usd'] = 25.00;
        if ($hasQuota) $ddr['online_quota'] = 4;

        RoomType::updateOrCreate(['slug' => 'ddr'], $ddr);

        // DSR
        $dsr = [
            'name' => 'Deluxe Single Room (DSR)',
            'slug' => 'dsr',
            $tzsColumn => 35000, // TZS
            'capacity' => 1,
            'amenities' => ['Wi-Fi', 'A/C', 'TV'],
            'description' => 'Deluxe single room — clean, quiet, comfortable.',
            'is_active' => true,
        ];

        if ($hasCode)  $dsr['code'] = 'DSR';
        if ($hasUsd)   $dsr['price_usd'] = 20.00;
        if ($hasQuota) $dsr['online_quota'] = 8;

        RoomType::updateOrCreate(['slug' => 'dsr'], $dsr);
    }
}
