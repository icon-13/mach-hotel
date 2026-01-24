<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Room extends Model
{
    // ✅ DB enum values (match EXACTLY)
    public const STATUS_AVAILABLE   = 'Available';
    public const STATUS_BOOKED      = 'Booked';
    public const STATUS_OCCUPIED    = 'Occupied';
    public const STATUS_MAINTENANCE = 'Maintenance'; // we’ll add in DB (migration below)

    protected $fillable = [
        'room_type_id',
        'room_number',
        'floor',

        // ✅ required in your DB
        'name',
        'slug',
        'capacity',
        'price_per_night',

        // other DB fields
        'type',
        'status',
        'short_description',
        'amenities',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'amenities' => 'array',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * ✅ Normalize statuses coming from your app (lowercase -> DB enum)
     */
    public static function normalizeStatus(?string $status): string
    {
        $s = strtolower(trim((string) $status));

        return match ($s) {
            'available'    => self::STATUS_AVAILABLE,
            'booked'       => self::STATUS_BOOKED,
            'occupied'     => self::STATUS_OCCUPIED,
            'maintenance'  => self::STATUS_MAINTENANCE,
            default        => self::STATUS_AVAILABLE,
        };
    }

    public function setStatusAttribute($value): void
    {
        $this->attributes['status'] = self::normalizeStatus((string) $value);
    }

    /**
     * ✅ Rooms available for a given stay (date-overlap safe)
     * Assumption:
     * - Rooms must be ACTIVE
     * - Status must be Available (NOT Maintenance)
     * - No overlapping booking that blocks inventory (confirmed, checked_in)
     */
    public function scopeAvailableForStay($query, int $roomTypeId, Carbon $checkIn, Carbon $checkOut, ?int $excludeBookingId = null)
    {
        return $query
            ->where('room_type_id', $roomTypeId)
            ->where('is_active', 1)
            ->where('status', self::STATUS_AVAILABLE)
            ->whereDoesntHave('bookings', function ($b) use ($checkIn, $checkOut, $excludeBookingId) {

                $b->whereIn('status', ['confirmed', 'checked_in'])
                  ->whereNotNull('room_id')
                  ->when($excludeBookingId, fn($q) => $q->where('id', '!=', $excludeBookingId))
                  ->where('check_in', '<', $checkOut)
                  ->where('check_out', '>', $checkIn);
            });
    }
}
