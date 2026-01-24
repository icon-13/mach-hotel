<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

use function GuzzleHttp\json_encode;

class RoomSeeder extends Seeder
{
  public function run(): void
  {
    $rooms = [
      [
        'name' => 'Deluxe Queen',
        'slug' => 'deluxe-queen',
        'type' => 'Deluxe',
        'capacity' => 2,
        'price_per_night' => 120000,
        'short_description' => 'Spacious comfort with premium feel.',
        'amenities' => json_encode(['Wi-Fi','A/C','TV']),
      ],
      [
        'name' => 'Executive Suite',
        'slug' => 'executive-suite',
        'type' => 'Suite',
        'capacity' => 2,
        'price_per_night' => 180000,
        'short_description' => 'More space, more luxury — top tier.',
        'amenities' => json_encode(['Wi-Fi','A/C','TV','Lounge']),
      ],
      [
        'name' => 'Standard Double',
        'slug' => 'standard-double',
        'type' => 'Standard',
        'capacity' => 2,
        'price_per_night' => 90000,
        'short_description' => 'Clean, cozy, best value.',
        'amenities' => json_encode(['Wi-Fi','TV']),

      ],
    ];

    foreach ($rooms as $r) {
      Room::updateOrCreate(['slug' => $r['slug']], $r);
    }
  }
}
