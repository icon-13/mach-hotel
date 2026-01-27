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
        $typeId  = $request->query('room_type_id'); // numeric id OR 'tbd'
        $status  = $request->query('status');       // Available/Booked/Occupied/OutOfService

        $roomsQuery = PhysicalRoom::query()
            ->with('roomType')
            ->orderBy('room_number');

        // Filter by type
        if ($typeId !== null && $typeId !== '') {
            if ($typeId === 'tbd') {
                $roomsQuery->whereNull('room_type_id');
            } else {
                $roomsQuery->where('room_type_id', (int) $typeId);
            }
        }

        // Filter by status
        if ($status !== null && $status !== '') {
            $roomsQuery->where('status', $status);
        }

        return view('reception.admin.rooms.index', [
            'rooms'        => $roomsQuery->get(),
            'roomTypes'    => RoomType::orderBy('name')->get(),
            'selectedType' => $typeId,
            'selectedStatus' => $status,
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
         * ✅ BUSINESS RULE (locked):
         * Online booking is controlled by RoomType (online_quota + is_active),
         * NOT by physical rooms.
         *
         * TBD rooms = room_type_id NULL → hidden online automatically.
         */

        $physicalRoom->update($data);

        return redirect()
            ->route('reception.admin.rooms.index', request()->only(['room_type_id','status']))
            ->with('success', 'Room updated successfully.');
    }

    public function bulkAssign(Request $request)
    {
        $data = $request->validate([
            'room_type_id' => ['nullable', 'exists:room_types,id'], // null = TBD
            'room_numbers' => ['required', 'string'],              // "101,102,A1,B2"
            'status'       => ['nullable', 'in:Available,Booked,Occupied,OutOfService'], // optional bulk status
        ]);

        $numbers = collect(explode(',', $data['room_numbers']))
            ->map(fn ($n) => trim($n))
            ->filter()
            ->values();

        $payload = [
            'room_type_id' => $data['room_type_id'] ?? null,
        ];

        // Optional: bulk status change (useful for the “9 rooms maintenance” setup)
        if (!empty($data['status'])) {
            $payload['status'] = $data['status'];
        }

        PhysicalRoom::whereIn('room_number', $numbers)->update($payload);

        return back()->with('success', 'Bulk update saved.');
    }
}
