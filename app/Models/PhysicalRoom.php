<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicalRoom extends Model
{
    protected $fillable = [
        'room_number',
        'room_type_id',
        'status',
        'is_bookable_online',
        'notes',
    ];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
