<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\RoomType;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function show(RoomType $roomType, Request $request, AvailabilityService $availability)
    {
        $request->validate([
            'check_in'  => ['required','date','after_or_equal:today'],
            'check_out' => ['required','date','after:check_in'],
            'guests'    => ['required','integer','min:1','max:10'],
        ]);

        $guests = (int) $request->get('guests', 1);

        // Quota-based availability (no physical room numbers)
        abort_if(
            !$availability->hasAvailability($roomType->id, $request->check_in, $request->check_out),
            404,
            'Not available.'
        );

        return view('book', [
            'roomType'  => $roomType,
            'check_in'  => $request->check_in,
            'check_out' => $request->check_out,
            'guests'    => $guests,
        ]);
    }

    public function store(RoomType $roomType, Request $request, AvailabilityService $availability)
    {
        $data = $request->validate([
            'check_in'  => ['required','date','after_or_equal:today'],
            'check_out' => ['required','date','after:check_in'],
            'guests'    => ['nullable','integer','min:1','max:10'],
            'full_name' => ['required', 'string', 'min:2', 'max:120'],
            'phone'     => ['required', 'string', 'min:7', 'max:25'],
            'email'     => ['nullable', 'email', 'max:120'],
            'special_requests' => ['nullable', 'string', 'max:2000'],
        ]);

        // Re-check quota availability at booking time (prevents overbooking)
        if (!$availability->hasAvailability($roomType->id, $data['check_in'], $data['check_out'])) {
            return back()
                ->withInput()
                ->withErrors(['check_in' => 'Sorry — this room type is sold out for those dates.']);
        }

        // -----------------------------
        // ✅ PRODUCTION Guest handling
        // -----------------------------
        $name  = trim((string) $data['full_name']);
        $phone = preg_replace('/\s+/', '', trim((string) $data['phone'])); // remove spaces
        $email = $data['email'] ?? null;

        // Find existing guest by phone
        $guest = Guest::where('phone', $phone)->first();

        if (!$guest) {
            // New guest
            $guest = Guest::create([
                'full_name' => $name,
                'phone'     => $phone,
                'email'     => $email,
            ]);
        } else {
            // Existing guest:
            // ✅ DO NOT overwrite full_name (prevents historical bookings changing)
            // But safely fill missing email if we got one now
            if (empty($guest->email) && !empty($email)) {
                $guest->email = $email;
                $guest->save();
            }

            // Optional: if the entered name differs, keep a note on the booking (no DB change)
            if (!empty($name) && $guest->full_name !== $name) {
                $data['special_requests'] = trim(
                    ($data['special_requests'] ?? '') .
                    " | Note: entered name '{$name}' (guest record: '{$guest->full_name}')"
                );
            }
        }

        $nights = (int) \Carbon\Carbon::parse($data['check_in'])
            ->diffInDays(\Carbon\Carbon::parse($data['check_out']));

        // Use TZS as the system currency (manager prices are clear in TZS)
        $total = $nights * (int) $roomType->price_tzs;

        $booking = Booking::create([
            'room_id'        => null,                // ✅ no physical room number in quota model
            'room_type_id'   => $roomType->id,       // ✅ room type booked
            'guest_id'       => $guest->id,

             // ✅ snapshot (this is the fix)
            'guest_name'     => $name,
            'guest_phone'    => $phone,
            'guest_email'    => $email,
            'check_in'       => $data['check_in'],
            'check_out'      => $data['check_out'],
            'total_amount'   => $total,              // TZS
            'code'           => $this->makeCode(),
            'status'         => 'confirmed',         // ✅ match reception dashboard filters
            'special_requests' => $data['special_requests'] ?? null,
        ]);

        return redirect()->route('booking.confirmed', $booking->code);
    }

    public function confirmed(string $code)
    {
        $booking = Booking::where('code', $code)
            ->with(['roomType', 'guest'])
            ->firstOrFail();

        $hotelPhone = '255680082937'; // TODO: replace after manager meeting

        $typeName = $booking->roomType?->name ?? 'Room';

        $msg = "Hello Mach Hotel, my booking is confirmed.\n"
            . "Booking Code: {$booking->code}\n"
            . "Name: {$booking->guest->full_name}\n"
            . "Room Type: {$typeName}\n"
            . "Dates: {$booking->check_in->format('Y-m-d')} to {$booking->check_out->format('Y-m-d')}\n"
            . "Total: TZS " . number_format($booking->total_amount) . "\n"
            . "Payment: Pay at counter\n"
            . "Please acknowledge. Thank you.";

        $whatsappUrl = "https://wa.me/{$hotelPhone}?text=" . urlencode($msg);

        return view('booking-confirmed', [
            'booking'      => $booking,
            'whatsappUrl'  => $whatsappUrl,
        ]);
    }

    private function makeCode(): string
    {
        return 'MACH-' . strtoupper(Str::random(6));
    }

    public function pdf(string $code)
    {
        $booking = Booking::where('code', $code)
            ->with(['roomType', 'guest'])
            ->firstOrFail();

        $typeName = $booking->roomType?->name ?? 'Room';

        $qrText =
            "Mach Hotel Booking\n" .
            "Code: {$booking->code}\n" .
            "Guest: {$booking->guest->full_name}\n" .
            "Phone: {$booking->guest->phone}\n" .
            "Room Type: {$typeName}\n" .
            "Dates: {$booking->check_in->format('Y-m-d')} to {$booking->check_out->format('Y-m-d')}";

        // ✅ Generate QR as SVG (no Imagick/GD required)
        $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(180)
            ->margin(1)
            ->generate($qrText);

        $qrDataUri = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.booking-confirmation', [
            'booking'    => $booking,
            'qrDataUri'  => $qrDataUri,
        ])->setPaper([0, 0, 360, 560], 'portrait');

        return $pdf->download("MachHotel-Booking-{$booking->code}.pdf");
    }
}
