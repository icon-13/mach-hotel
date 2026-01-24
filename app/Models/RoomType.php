<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $fillable = [
        'code','name','slug','price_tzs','price_usd','capacity','online_quota',
        'amenities','description','is_active'
    ];

    protected $casts = [
        'amenities' => 'array',
        'is_active' => 'boolean',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // ✅ this was missing
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
 
    public function physicalRooms()
{
    return $this->hasMany(\App\Models\PhysicalRoom::class);
}

}
