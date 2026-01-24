<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\RoomType;

class RoomsController extends Controller
{
    public function index()
    {
        $roomTypes = RoomType::query()
            ->where('is_active', true)
            ->where('is_bookable', true)
            ->orderBy('price_tzs')
            ->get();

        return view('rooms.index', compact('roomTypes'));
    }

    public function show(RoomType $roomType)
    {
        abort_unless($roomType->is_active, 404);
        abort_unless((bool)$roomType->is_bookable, 404);

        return view('rooms.show', compact('roomType'));
    }
}
