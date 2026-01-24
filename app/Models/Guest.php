<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = [
        'full_name',
        'phone',
        'email',
    ];

    // Optional: if you ever return Guest as JSON, include "name" too
    protected $appends = ['name'];

    public function bookings()
    {
        return $this->hasMany(\App\Models\Booking::class);
    }

    // Backward compatibility: old code can still use $guest->name
    public function getNameAttribute(): string
    {
        return (string) ($this->full_name ?? '');
    }
}
