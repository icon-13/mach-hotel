<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    // ✅ match DB enum values exactly (UPPER_SNAKE_CASE)
    public const STATUS_CONFIRMED   = 'CONFIRMED';
    public const STATUS_CHECKED_IN  = 'CHECKED_IN';
    public const STATUS_CHECKED_OUT = 'CHECKED_OUT';
    public const STATUS_CANCELLED   = 'CANCELLED';

    protected $fillable = [
        // ✅ NEW (physical room assignment)
        'physical_room_id',

        // ✅ keep old for backward compatibility (until you fully switch)
        'room_id',

        'guest_name',
        'guest_phone',
        'guest_email',


        'room_type_id',
        'guest_id',
        'check_in',
        'check_out',
        'total_amount',
        'code',
        'status',
        'special_requests',
    ];

    protected $casts = [
        'check_in'  => 'date',
        'check_out' => 'date',
    ];

    /**
     * ✅ Always save status in DB enum style (UPPER_SNAKE_CASE)
     * Accepts: confirmed, CONFIRMED, "checked in", "checked-in", checked_in, etc.
     */
    public function setStatusAttribute($value): void
    {
        $v = strtoupper(trim((string) $value));
        $v = str_replace([' ', '-'], '_', $v);

        $allowed = [
            self::STATUS_CONFIRMED,
            self::STATUS_CHECKED_IN,
            self::STATUS_CHECKED_OUT,
            self::STATUS_CANCELLED,
        ];

        $this->attributes['status'] = in_array($v, $allowed, true)
            ? $v
            : self::STATUS_CONFIRMED;
    }

    /**
     * ✅ NEW: Physical room relation (21 rooms system)
     */
    public function physicalRoom()
    {
        return $this->belongsTo(\App\Models\PhysicalRoom::class);
    }

    /**
     * ⚠️ Legacy relation (older "rooms" table)
     * Keep for now so existing views don't crash.
     */
    public function room()
    {
        return $this->belongsTo(\App\Models\Room::class);
    }

    public function guest()
    {
        return $this->belongsTo(\App\Models\Guest::class);
    }

    public function roomType()
    {
        return $this->belongsTo(\App\Models\RoomType::class, 'room_type_id');
    }
}
