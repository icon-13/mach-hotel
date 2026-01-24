<?php

namespace App\Http\Controllers\Reception\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhysicalRoom;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomAdminController extends Controller
{
    public function index(Request $request)
    {
        $typeId = $request->query('room_type_id');

        $roomsQuery = PhysicalRoom::query()
            ->with('roomType')
            ->orderBy('room_number');

        if ($typeId !== null && $typeId !== '') {
            if ($typeId === 'tbd') {
                $roomsQuery->whereNull('room_type_id');
            } else {
                $roomsQuery->where('room_type_id', (int) $typeId);
            }
        }

        return view('reception.admin.rooms.index', [
            'rooms'        => $roomsQuery->get(),
            'roomTypes'    => RoomType::orderBy('name')->get(),
            'selectedType' => $typeId,
        ]);
    }

    public function edit(PhysicalRoom $physicalRoom)
    {
        return view('reception.admin.rooms.edit', [
            'room'      => $physicalRoom->load('roomType'),
            'roomTypes' => RoomType::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, PhysicalRoom $physicalRoom)
    {
        $data = $request->validate([
            'room_number'  => ['required', 'string', 'max:50', 'unique:physical_rooms,room_number,' . $physicalRoom->id],
            'room_type_id' => ['nullable', 'exists:room_types,id'],
            'status'       => ['required', 'in:Available,Booked,Occupied,OutOfService'],
            'notes'        => ['nullable', 'string', 'max:1000'],
        ]);

        /**
         * ✅ IMPORTANT BUSINESS RULE:
         * Online bookability is controlled by RoomType (is_bookable + quotas),
         * NOT by physical rooms. So we don't store "is_bookable_online" here.
         *
         * TBD rooms = room_type_id NULL. They stay hidden online automatically.
         */

        $physicalRoom->update($data);

        return redirect()
            ->route('reception.admin.rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    public function bulkAssign(Request $request)
    {
        $data = $request->validate([
            'room_type_id'  => ['nullable', 'exists:room_types,id'],
            'room_numbers'  => ['required', 'string'], // comma-separated like "101,102,103" OR "A1,A2,B1"
        ]);

        $numbers = collect(explode(',', $data['room_numbers']))
            ->map(fn ($n) => trim($n))
            ->filter()
            ->values();

        PhysicalRoom::whereIn('room_number', $numbers)->update([
            'room_type_id' => $data['room_type_id'] ?? null, // null = TBD
        ]);

        return back()->with('success', 'Bulk assignment saved.');
    }
}
