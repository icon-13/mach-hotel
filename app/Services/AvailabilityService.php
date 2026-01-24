<?php

namespace App\Services;

use App\Models\RoomType;
use Illuminate\Support\Collection;

class AvailabilityService
{
    public function availableRoomTypes(string $checkIn, string $checkOut, int $guests = 1): Collection
    {
        return RoomType::query()
            ->where('is_active', true)
            ->where('capacity', '>=', $guests)
            ->where('online_quota', '>', 0)
            ->withCount(['bookings as overlapping_bookings_count' => function ($q) use ($checkIn, $checkOut) {
                $q->whereIn('status', ['Confirmed', 'CheckedIn'])
                  ->where('check_in', '<', $checkOut)
                  ->where('check_out', '>', $checkIn);
            }])
            ->get()
            ->filter(fn($type) => (int)$type->overlapping_bookings_count < (int)$type->online_quota)
            ->sortBy('price_tzs')
            ->values();
    }

    public function hasAvailability(int $roomTypeId, string $checkIn, string $checkOut): bool
    {
        $type = RoomType::whereKey($roomTypeId)->where('is_active', true)->first();
        if (!$type || (int)$type->online_quota <= 0) return false;

        $overlaps = $type->bookings()
            ->whereIn('status', ['Confirmed','CheckedIn'])
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->count();

        return $overlaps < (int)$type->online_quota;
    }
}
