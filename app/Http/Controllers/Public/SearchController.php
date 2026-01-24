<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request, AvailabilityService $availability)
    {
        $roomTypes = collect();

        if ($request->filled(['check_in','check_out'])) {
            $request->validate([  
                'check_in'  => ['required','date','after_or_equal:today'],
                'check_out' => ['required','date','after:check_in'],
                'guests'    => ['nullable','integer','min:1','max:10'],
            ]);

            $roomTypes = $availability->availableRoomTypes(
                $request->check_in,
                $request->check_out,
                (int) $request->get('guests', 1)
            );
        }

        return view('search', [
            'roomTypes' => $roomTypes,
            'filters'   => $request->all(),
        ]);
    }
}
