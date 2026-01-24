<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\RoomType;

class HomeController extends Controller
{
    public function index()
    {
        $roomTypes = RoomType::query()
            ->where('is_active', true)
            ->where('is_bookable', true)
            ->orderBy('price_tzs')
            ->get();

        return view('home', compact('roomTypes'));
    }
}
