<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\RoomType;
use App\Models\PhysicalRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * PhysicalRooms.status enum in DB: Available / Booked / Occupied / OutOfService
     */
    private const ROOM_AVAILABLE = 'Available';
    private const ROOM_BOOKED    = 'Booked';
    private const ROOM_OCCUPIED  = 'Occupied';
    private const ROOM_OOS       = 'OutOfService';

    /**
     * Booking statuses (support both old lowercase + new UPPER_SNAKE_CASE)
     */
    private const ST_CONFIRMED   = 'CONFIRMED';
    private const ST_CHECKED_IN  = 'CHECKED_IN';
    private const ST_CHECKED_OUT = 'CHECKED_OUT';
    private const ST_CANCELLED   = 'CANCELLED';

    private const ST_LEG_CONFIRMED   = 'confirmed';
    private const ST_LEG_CHECKED_IN  = 'checked_in';
    private const ST_LEG_CHECKED_OUT = 'checked_out';
    private const ST_LEG_CANCELLED   = 'cancelled';

    private function statusSet(string $canonicalUpperSnake): array
    {
        // Return both variants so queries work regardless of what is stored.
        return match ($canonicalUpperSnake) {
            self::ST_CONFIRMED   => [self::ST_CONFIRMED,   self::ST_LEG_CONFIRMED],
            self::ST_CHECKED_IN  => [self::ST_CHECKED_IN,  self::ST_LEG_CHECKED_IN],
            self::ST_CHECKED_OUT => [self::ST_CHECKED_OUT, self::ST_LEG_CHECKED_OUT],
            self::ST_CANCELLED   => [self::ST_CANCELLED,   self::ST_LEG_CANCELLED],
            default => [$canonicalUpperSnake],
        };
    }

    private function normalizeRoomStatusFilter(?string $status): ?string
    {
        if (!$status) return null;

        $s = strtolower(trim($status));

        return match ($s) {
            'available' => self::ROOM_AVAILABLE,
            'booked' => self::ROOM_BOOKED,
            'occupied' => self::ROOM_OCCUPIED,
            'outofservice', 'out_of_service', 'oos', 'maintenance' => self::ROOM_OOS,
            default => in_array($status, [self::ROOM_AVAILABLE, self::ROOM_BOOKED, self::ROOM_OCCUPIED, self::ROOM_OOS], true)
                ? $status
                : null,
        };
    }

    /**
     * Overlap rule:
     * (existing.check_in < new.check_out) AND (existing.check_out > new.check_in)
     */
    private function physicalRoomAvailableForStay(
        int $physicalRoomId,
        Carbon $checkIn,
        Carbon $checkOut,
        ?int $excludeBookingId = null
    ): bool {
        $q = Booking::query()
            ->whereNotNull('physical_room_id')
            ->where('physical_room_id', $physicalRoomId)
            ->whereIn('status', array_merge(
                $this->statusSet(self::ST_CONFIRMED),
                $this->statusSet(self::ST_CHECKED_IN)
            ))
            ->whereDate('check_in', '<', $checkOut->toDateString())
            ->whereDate('check_out', '>', $checkIn->toDateString());

        if ($excludeBookingId) {
            $q->where('id', '!=', $excludeBookingId);
        }

        return !$q->exists();
    }

    public function index(Request $request)
    {
        $date       = $request->get('date') ?: now()->toDateString();
        $view       = $request->get('view', 'today'); // today | upcoming | recent

        $status     = $request->get('status');        // confirmed/checked_in/checked_out/cancelled (UI)
        $roomTypeId = $request->get('room_type_id');
        $q          = trim((string) $request->get('q', ''));

        $baseOverlap = Booking::query()
            ->whereDate('check_in', '<=', $date)
            ->whereDate('check_out', '>', $date);

        // KPI counts (support both variants)
        $kpiConfirmedArrivals = Booking::query()
            ->whereDate('check_in', $date)
            ->whereIn('status', $this->statusSet(self::ST_CONFIRMED))
            ->count();

        $kpiInHouseCheckedIn = (clone $baseOverlap)
            ->whereIn('status', $this->statusSet(self::ST_CHECKED_IN))
            ->count();

        $kpiCheckedOutToday = Booking::query()
            ->whereDate('check_out', $date)
            ->whereIn('status', $this->statusSet(self::ST_CHECKED_OUT))
            ->count();

        $kpiCancelledToday = Booking::query()
            ->whereDate('updated_at', $date)
            ->whereIn('status', $this->statusSet(self::ST_CANCELLED))
            ->count();

        $bookingsQuery = Booking::query()
            ->with(['roomType', 'guest', 'physicalRoom'])
            ->orderByRaw("FIELD(status,'CHECKED_IN','checked_in','CONFIRMED','confirmed','CHECKED_OUT','checked_out','CANCELLED','cancelled')")
            ->orderBy('check_in')
            ->orderByDesc('id');

        if ($view === 'today') {
            $bookingsQuery
                ->whereDate('check_in', '<=', $date)
                ->whereDate('check_out', '>', $date);
        } elseif ($view === 'upcoming') {
            $bookingsQuery
                ->whereIn('status', array_merge(
                    $this->statusSet(self::ST_CONFIRMED),
                    $this->statusSet(self::ST_CHECKED_IN)
                ))
                ->whereDate('check_in', '>=', $date)
                ->whereDate('check_in', '<=', Carbon::parse($date)->addDays(30)->toDateString());
        } elseif ($view === 'recent') {
            $bookingsQuery
                ->whereIn('status', array_merge(
                    $this->statusSet(self::ST_CONFIRMED),
                    $this->statusSet(self::ST_CHECKED_IN)
                ))
                ->where('created_at', '>=', now()->subHours(24));
        } else {
            $bookingsQuery
                ->whereDate('check_in', '<=', $date)
                ->whereDate('check_out', '>', $date);
            $view = 'today';
        }

        // Status filter from UI: confirmed/checked_in/checked_out/cancelled
        if ($status) {
            $status = strtolower(trim($status));
            $bookingsQuery->whereIn('status', match ($status) {
                'confirmed'   => $this->statusSet(self::ST_CONFIRMED),
                'checked_in'  => $this->statusSet(self::ST_CHECKED_IN),
                'checked_out' => $this->statusSet(self::ST_CHECKED_OUT),
                'cancelled'   => $this->statusSet(self::ST_CANCELLED),
                default       => [$status],
            });
        }

        if ($roomTypeId) $bookingsQuery->where('room_type_id', $roomTypeId);

        // ✅ PRODUCTION search: search snapshot first + then guest table
        if ($q !== '') {
            $bookingsQuery->where(function ($sub) use ($q) {
                $sub->where('code', 'like', "%{$q}%")
                    ->orWhere('guest_name', 'like', "%{$q}%")
                    ->orWhere('guest_phone', 'like', "%{$q}%")
                    ->orWhereHas('guest', function ($g) use ($q) {
                        $g->where('full_name', 'like', "%{$q}%")
                          ->orWhere('phone', 'like', "%{$q}%");
                    });
            });
        }

        $bookings  = $bookingsQuery->paginate(20)->withQueryString();
        $roomTypes = RoomType::orderBy('name')->get();

        return view('reception.bookings.index', compact(
            'bookings',
            'roomTypes',
            'date',
            'view',
            'status',
            'roomTypeId',
            'q',
            'kpiConfirmedArrivals',
            'kpiInHouseCheckedIn',
            'kpiCheckedOutToday',
            'kpiCancelledToday'
        ));
    }

    public function show(Booking $booking)
    {
        $booking->load(['roomType', 'guest', 'physicalRoom']);

        $availableRooms = collect();

        if ($booking->room_type_id && $booking->check_in && $booking->check_out) {
            $checkIn  = Carbon::parse($booking->check_in)->startOfDay();
            $checkOut = Carbon::parse($booking->check_out)->startOfDay();

            $query = PhysicalRoom::query()
                ->where('status', self::ROOM_AVAILABLE)
                ->where('room_type_id', (int) $booking->room_type_id)
                ->orderBy('room_number');

            $availableRooms = $query->get(['id', 'room_number', 'status', 'room_type_id'])
                ->filter(fn ($r) => $this->physicalRoomAvailableForStay((int)$r->id, $checkIn, $checkOut, (int)$booking->id))
                ->values();
        }

        return view('reception.bookings.show', compact('booking', 'availableRooms'));
    }

    public function rooms(Request $request)
    {
        $this->syncPhysicalRoomStatusesForToday();

        $statusRaw = $request->get('status');
        $status    = $this->normalizeRoomStatusFilter($statusRaw);

        $roomTypes = RoomType::with(['physicalRooms' => function ($q) use ($status) {
            if ($status) $q->where('status', $status);
            $q->orderBy('room_number');
        }])->orderBy('name')->get();

        $tbdRoomsQuery = PhysicalRoom::query()->whereNull('room_type_id')->orderBy('room_number');
        if ($status) $tbdRoomsQuery->where('status', $status);
        $tbdRooms = $tbdRoomsQuery->get();

        return view('reception.rooms.index', [
            'roomTypes' => $roomTypes,
            'tbdRooms'  => $tbdRooms,
            'status'    => $statusRaw,
            'statusDb'  => $status,
        ]);
    }

    public function create(Request $request)
    {
        $roomTypes = RoomType::orderBy('name')->get();
        $defaultCheckIn  = now()->toDateString();
        $defaultCheckOut = now()->addDay()->toDateString();

        return view('reception.bookings.create', compact('roomTypes', 'defaultCheckIn', 'defaultCheckOut'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guest_name'        => ['required', 'string', 'max:255'],
            'guest_phone'       => ['required', 'string', 'max:50'],
            'room_type_id'      => ['required', 'integer', 'exists:room_types,id'],
            'check_in'          => ['required', 'date'],
            'check_out'         => ['required', 'date', 'after:check_in'],
            'special_requests'  => ['nullable', 'string', 'max:2000'],
            'check_in_now'      => ['nullable', 'in:1'],

            // ✅ Prefer physical_room_id (new). Accept room_id for backward UI safety.
            'physical_room_id'  => ['nullable', 'integer', 'exists:physical_rooms,id'],
            'room_id'           => ['nullable', 'integer', 'exists:physical_rooms,id'],
        ]);

        $checkInNow = $request->get('check_in_now') === '1';

        $phone = trim((string)$request->guest_phone);

        /**
         * ✅ BEST PRACTICE:
         * - Use phone to find returning guest
         * - DO NOT overwrite the old name automatically (avoids history rewrite)
         * - Booking snapshot will always store the typed name anyway
         */
        $guest = Guest::firstOrCreate(
            ['phone' => $phone],
            ['full_name' => $request->guest_name]
        );

        // Optional: if guest name is empty in DB, fill it once.
        if (!$guest->full_name) {
            $guest->full_name = $request->guest_name;
            $guest->save();
        }

        $rt = RoomType::findOrFail($request->room_type_id);
        $checkIn  = Carbon::parse($request->check_in)->startOfDay();
        $checkOut = Carbon::parse($request->check_out)->startOfDay();

        $nights = max(1, $checkIn->diffInDays($checkOut));
        $totalAmount = (int) ($nights * (int) ($rt->price_tzs ?? 0));

        $booking = null;

        DB::transaction(function () use ($request, $guest, $checkInNow, $checkIn, $checkOut, $totalAmount, &$booking) {

            $physicalRoomId = null;

            if ($checkInNow) {
                $incomingRoomId = $request->physical_room_id ?? $request->room_id;

                if (!$incomingRoomId) {
                    abort(422, 'Please select a physical room for immediate check-in.');
                }

                $room = PhysicalRoom::whereKey($incomingRoomId)->lockForUpdate()->firstOrFail();

                if ((int)$room->room_type_id !== (int)$request->room_type_id) abort(422, 'Room type mismatch.');
                if ($room->status === self::ROOM_OOS) abort(422, 'Room is out of service.');

                $ok = $this->physicalRoomAvailableForStay((int)$room->id, $checkIn, $checkOut, null);
                if (!$ok) abort(422, 'Selected room is not available for these dates.');

                $room->update(['status' => self::ROOM_OCCUPIED]);
                $physicalRoomId = $room->id;
            }

            $code = strtoupper('MH-' . now()->format('ymd') . '-' . Str::random(5));

            $booking = Booking::create([
                'physical_room_id' => $physicalRoomId,
                'room_type_id'     => $request->room_type_id,
                'guest_id'         => $guest->id,

                // ✅ SNAPSHOT (THIS IS THE REAL FIX)
                'guest_name'       => $request->guest_name,
                'guest_phone'      => $request->guest_phone,
                'guest_email'      => $guest->email ?? null,

                'check_in'         => $checkIn->toDateString(),
                'check_out'        => $checkOut->toDateString(),
                'total_amount'     => $totalAmount,
                'code'             => $code,
                'status'           => $checkInNow ? self::ST_CHECKED_IN : self::ST_CONFIRMED,
                'special_requests' => $request->special_requests,
            ]);
        });

        return redirect()->route('reception.bookings.show', $booking)
            ->with('success', $checkInNow ? 'Walk-in checked in successfully.' : 'Walk-in booking created (confirmed).');
    }

    public function checkIn(Request $request, Booking $booking)
    {
        $status = strtoupper(str_replace([' ', '-'], '_', (string)$booking->status));
        if (!in_array($status, $this->statusSet(self::ST_CONFIRMED), true)) {
            return back()->with('error', 'Only CONFIRMED bookings can be checked in.');
        }

        $now = now();

        $checkInDate  = Carbon::parse($booking->check_in)->startOfDay();
        $checkOutDate = Carbon::parse($booking->check_out)->startOfDay();

        $earlyAllowedFrom = $checkInDate->copy()->subDay()->setTime(12, 0, 0);

        if ($now->lt($earlyAllowedFrom)) {
            return back()->with('error', 'Too early to check-in. Allowed from 12:00 the day before arrival.');
        }

        if ($now->copy()->startOfDay()->gte($checkOutDate)) {
            return back()->with('error', 'Cannot check-in: booking dates already ended.');
        }

        $request->validate([
            'physical_room_id' => ['nullable', 'integer', 'exists:physical_rooms,id'],
            'room_id'          => ['nullable', 'integer', 'exists:physical_rooms,id'],
        ]);

        $incomingRoomId = $request->physical_room_id ?? $request->room_id;
        if (!$incomingRoomId) {
            return back()->with('error', 'Please select a physical room.');
        }

        DB::transaction(function () use ($incomingRoomId, $booking) {

            $booking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $st = strtoupper(str_replace([' ', '-'], '_', (string)$booking->status));

            if (!in_array($st, $this->statusSet(self::ST_CONFIRMED), true)) {
                abort(422, 'Booking is no longer CONFIRMED.');
            }

            $room = PhysicalRoom::whereKey($incomingRoomId)->lockForUpdate()->firstOrFail();

            if ((int)$room->room_type_id !== (int)$booking->room_type_id) abort(422, 'Room type mismatch.');
            if ($room->status === self::ROOM_OOS) abort(422, 'Room is out of service.');
            if ($room->status !== self::ROOM_AVAILABLE) abort(422, 'Room is not available.');

            $checkIn  = Carbon::parse($booking->check_in)->startOfDay();
            $checkOut = Carbon::parse($booking->check_out)->startOfDay();

            $ok = $this->physicalRoomAvailableForStay((int)$room->id, $checkIn, $checkOut, (int)$booking->id);
            if (!$ok) abort(422, 'Room is not available for these dates.');

            $room->update(['status' => self::ROOM_OCCUPIED]);

            $booking->update([
                'physical_room_id' => $room->id,
                'status'           => self::ST_CHECKED_IN,
            ]);
        });

        return redirect()->route('reception.bookings.show', $booking)
            ->with('success', 'Checked in successfully.');
    }

    public function checkOut(Booking $booking)
    {
        $st = strtoupper(str_replace([' ', '-'], '_', (string)$booking->status));
        if (!in_array($st, $this->statusSet(self::ST_CHECKED_IN), true)) {
            return back()->with('error', 'Only CHECKED-IN bookings can be checked out.');
        }

        DB::transaction(function () use ($booking) {

            $booking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $st = strtoupper(str_replace([' ', '-'], '_', (string)$booking->status));

            if (!in_array($st, $this->statusSet(self::ST_CHECKED_IN), true)) {
                abort(422, 'Booking is no longer CHECKED-IN.');
            }

            if ($booking->physical_room_id) {
                $room = PhysicalRoom::whereKey($booking->physical_room_id)->lockForUpdate()->first();
                if ($room && $room->status !== self::ROOM_OOS) {
                    $room->update(['status' => self::ROOM_AVAILABLE]);
                }
            }

            $booking->update([
                'status' => self::ST_CHECKED_OUT,
            ]);
        });

        return redirect()->route('reception.bookings.show', $booking)
            ->with('success', 'Checked out successfully.');
    }

    public function cancel(Booking $booking)
    {
        $st = strtoupper(str_replace([' ', '-'], '_', (string)$booking->status));
        if (!in_array($st, $this->statusSet(self::ST_CONFIRMED), true)) {
            return back()->with('error', 'Only CONFIRMED bookings can be cancelled.');
        }

        DB::transaction(function () use ($booking) {
            $booking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $st = strtoupper(str_replace([' ', '-'], '_', (string)$booking->status));

            if (!in_array($st, $this->statusSet(self::ST_CONFIRMED), true)) {
                abort(422, 'Booking is no longer CONFIRMED.');
            }

            $booking->update(['status' => self::ST_CANCELLED]);
        });

        return redirect()->route('reception.bookings.show', $booking)
            ->with('success', 'Booking cancelled.');
    }

    public function overrideRoom(Request $request, Booking $booking)
    {
        $st = strtoupper(str_replace([' ', '-'], '_', (string)$booking->status));
        if (!in_array($st, $this->statusSet(self::ST_CHECKED_IN), true)) {
            return back()->with('error', 'Room override is only allowed for CHECKED-IN bookings.');
        }

        $request->validate([
            'new_physical_room_id' => ['nullable', 'integer', 'exists:physical_rooms,id'],
            'new_room_id'          => ['nullable', 'integer', 'exists:physical_rooms,id'],
        ]);

        $newRoomId = $request->new_physical_room_id ?? $request->new_room_id;
        if (!$newRoomId) {
            return back()->with('error', 'Please select a new physical room.');
        }

        DB::transaction(function () use ($newRoomId, $booking) {

            $booking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $st = strtoupper(str_replace([' ', '-'], '_', (string)$booking->status));

            if (!in_array($st, $this->statusSet(self::ST_CHECKED_IN), true)) {
                abort(422, 'Booking is no longer CHECKED-IN.');
            }

            $currentRoom = null;
            if ($booking->physical_room_id) {
                $currentRoom = PhysicalRoom::whereKey($booking->physical_room_id)->lockForUpdate()->first();
            }

            $newRoom = PhysicalRoom::whereKey($newRoomId)->lockForUpdate()->firstOrFail();

            if ((int)$newRoom->room_type_id !== (int)$booking->room_type_id) abort(422, 'Room type mismatch.');
            if ($newRoom->status === self::ROOM_OOS) abort(422, 'Room is out of service.');
            if ($newRoom->status !== self::ROOM_AVAILABLE) abort(422, 'Selected room is not available.');

            $checkIn  = Carbon::parse($booking->check_in)->startOfDay();
            $checkOut = Carbon::parse($booking->check_out)->startOfDay();

            $ok = $this->physicalRoomAvailableForStay((int)$newRoom->id, $checkIn, $checkOut, (int)$booking->id);
            if (!$ok) abort(422, 'Selected room is not available for these dates.');

            if ($currentRoom && $currentRoom->status !== self::ROOM_OOS) {
                $currentRoom->update(['status' => self::ROOM_AVAILABLE]);
            }

            $newRoom->update(['status' => self::ROOM_OCCUPIED]);

            $booking->update(['physical_room_id' => $newRoom->id]);
        });

        return redirect()->route('reception.bookings.show', $booking)
            ->with('success', 'Room overridden successfully.');
    }

    public function availableRoomsApi(Request $request)
    {
        $roomTypeId = (int) $request->get('room_type_id');
        $checkIn    = $request->get('check_in');
        $checkOut   = $request->get('check_out');

        $excludeBookingId = $request->get('exclude_booking_id');
        $excludeBookingId = $excludeBookingId ? (int)$excludeBookingId : null;

        if (!$roomTypeId || !$checkIn || !$checkOut) {
            return response()->json([]);
        }

        $checkIn  = Carbon::parse($checkIn)->startOfDay();
        $checkOut = Carbon::parse($checkOut)->startOfDay();

        if ($checkOut->lessThanOrEqualTo($checkIn)) {
            return response()->json([]);
        }

        $rooms = PhysicalRoom::query()
            ->where('status', self::ROOM_AVAILABLE)
            ->where('room_type_id', $roomTypeId)
            ->orderBy('room_number')
            ->get(['id', 'room_number'])
            ->filter(fn ($r) => $this->physicalRoomAvailableForStay((int)$r->id, $checkIn, $checkOut, $excludeBookingId))
            ->values();

        return response()->json($rooms);
    }

    private function syncPhysicalRoomStatusesForToday(): void
    {
        $today = now()->toDateString();

        $occupiedRoomIds = Booking::query()
            ->whereIn('status', $this->statusSet(self::ST_CHECKED_IN))
            ->whereNotNull('physical_room_id')
            ->whereDate('check_in', '<=', $today)
            ->whereDate('check_out', '>', $today)
            ->pluck('physical_room_id')
            ->unique()
            ->values();

        PhysicalRoom::whereIn('id', $occupiedRoomIds)
            ->where('status', '!=', self::ROOM_OOS)
            ->update(['status' => self::ROOM_OCCUPIED]);

        PhysicalRoom::whereNotIn('id', $occupiedRoomIds)
            ->where('status', '!=', self::ROOM_OOS)
            ->update(['status' => self::ROOM_AVAILABLE]);
    }
}
