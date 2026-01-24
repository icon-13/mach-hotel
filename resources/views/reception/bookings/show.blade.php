@extends('layouts.reception')
@section('title','Reception — Booking')

@section('content')
<section class="container rx-container py-4 py-lg-5">

  {{-- Header --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-3">
    <div>
      <div class="text-muted small">Booking</div>

      <div class="d-flex align-items-center gap-2 flex-wrap">
        <h2 class="mb-0">
          <span id="bookingCodeText">{{ $booking->code ?? ('#'.$booking->id) }}</span>
        </h2>

        <button class="btn btn-sm btn-outline-secondary" type="button" id="copyBtn" onclick="copyBookingCode(this)">
          Copy
        </button>

        @php
          // ✅ DB enums are UPPER_SNAKE_CASE now
          $map = [
            'CONFIRMED'   => 'bg-primary',
            'CHECKED_IN'  => 'bg-success',
            'CHECKED_OUT' => 'bg-secondary',
            'CANCELLED'   => 'bg-danger',
          ];
          $statusClass = $map[$booking->status] ?? 'bg-info';
        @endphp

        <span class="badge {{ $statusClass }}" style="border-radius:999px; padding:.45rem .7rem; font-weight:700;">
          {{ strtoupper(str_replace('_',' ', $booking->status ?? '')) }}
        </span>
      </div>

      <div class="text-muted mt-1">
        {{ $booking->guest?->full_name ?? ($booking->guest?->name ?? 'Guest') }}
        <span class="text-muted">•</span>
        {{ $booking->guest?->phone ?? '-' }}
      </div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <a class="btn btn-outline-secondary" href="{{ route('reception.bookings.index') }}">Back</a>

      @php $phone = $booking->guest?->phone; @endphp
      @if($phone)
        <a class="btn btn-outline-dark" href="tel:{{ $phone }}">Call</a>
        <a class="btn btn-dark" href="https://wa.me/{{ preg_replace('/\D+/', '', $phone) }}" target="_blank">
          WhatsApp
        </a>
      @endif
    </div>
  </div>

  {{-- Flash --}}
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <div class="row g-3">
    {{-- Left: Details --}}
    <div class="col-lg-7">
      <div class="card">
        <div class="card-body">

          <div class="d-flex justify-content-between align-items-start gap-2">
            <div>
              <h5 class="mb-1">Details</h5>
              <div class="text-muted small">Review before actions</div>
            </div>
          </div>

          <hr>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="text-muted small">Check-in</div>
              <div class="fw-semibold">{{ optional($booking->check_in)->format('Y-m-d') ?? '-' }}</div>
            </div>

            <div class="col-md-6">
              <div class="text-muted small">Check-out</div>
              <div class="fw-semibold">{{ optional($booking->check_out)->format('Y-m-d') ?? '-' }}</div>
            </div>

            <div class="col-md-6">
              <div class="text-muted small">Room Type</div>
              <div class="fw-semibold">{{ $booking->roomType?->name ?? '-' }}</div>
            </div>

            <div class="col-md-6">
              <div class="text-muted small">Assigned Room</div>
              {{-- ✅ Use physical room (21 rooms system). Fallback to legacy room --}}
              <div class="fw-semibold">
                {{ $booking->physicalRoom?->room_number ?? $booking->room?->room_number ?? '-' }}
              </div>
            </div>

            <div class="col-md-6">
              <div class="text-muted small">Total</div>
              <div class="fw-semibold">
                {{ $booking->total_amount ? 'TZS '.number_format($booking->total_amount) : '-' }}
              </div>
            </div>

            <div class="col-md-6">
              <div class="text-muted small">Guest</div>
              <div class="fw-semibold">{{ $booking->guest?->full_name ?? ($booking->guest?->name ?? 'Guest') }}</div>
              <div class="text-muted">{{ $booking->guest?->phone ?? '-' }}</div>
            </div>
          </div>

          @if(!empty($booking->special_requests))
            <hr>
            <div class="fw-semibold mb-1">Special Requests</div>
            <div class="text-muted">{{ $booking->special_requests }}</div>
          @endif

          {{-- Actions --}}
          <hr>
          <div class="d-flex flex-wrap gap-2">

            @if($booking->status === 'CONFIRMED')
              <form method="POST" action="{{ route('reception.bookings.cancel', $booking) }}">
                @csrf
                <button class="btn btn-outline-danger" type="submit"
                        onclick="return confirm('Cancel this booking?');">
                  Cancel
                </button>
              </form>
            @endif

            @if($booking->status === 'CHECKED_IN')
              <form method="POST" action="{{ route('reception.bookings.checkout', $booking) }}">
                @csrf
                <button class="btn btn-success" type="submit"
                        onclick="return confirm('Check out guest now?');">
                  Check Out
                </button>
              </form>
            @endif

          </div>

          {{-- Override Room --}}
          @if($booking->status === 'CHECKED_IN')
            <hr>
            <h6 class="mb-1">Override Room</h6>
            <div class="text-muted small mb-2">Move guest to another available room (same room type).</div>

            @if(($availableRooms ?? collect())->count() === 0)
              <div class="alert alert-warning mb-0">No available rooms to override into.</div>
            @else
              <form method="POST" action="{{ route('reception.bookings.overrideRoom', $booking) }}" class="d-flex gap-2 flex-wrap">
                @csrf

                {{-- ✅ Use new field name. Controller accepts old too, but we standardize now --}}
                <select name="new_physical_room_id" class="form-select" style="max-width:260px;" required>
                  <option value="">Select new room…</option>
                  @foreach($availableRooms as $room)
                    <option value="{{ $room->id }}">{{ $room->room_number }}</option>
                  @endforeach
                </select>

                <button class="btn btn-warning" type="submit"
                        onclick="return confirm('Override room? This will free the old room and occupy the new one.');">
                  Override
                </button>
              </form>
            @endif
          @endif

        </div>
      </div>
    </div>

    {{-- Right: Check-in --}}
    <div class="col-lg-5">
      <div class="card">
        <div class="card-body">
          <h5 class="mb-1">Check-in</h5>
          <div class="text-muted small mb-3">Assign a physical room number</div>

          {{-- ✅ FIX: compare against DB enum --}}
          @if($booking->status !== 'CONFIRMED')
            <div class="alert alert-light border mb-0">
              Check-in is only available for <strong>CONFIRMED</strong> bookings.
            </div>
          @else
            @if(($availableRooms ?? collect())->count() === 0)
              <div class="alert alert-warning mb-0">
                No available rooms for this room type right now.
              </div>
            @else
              <form method="POST" action="{{ route('reception.bookings.checkin', $booking) }}">
                @csrf
                <label class="form-label">Select Room</label>

                {{-- ✅ Use new field name --}}
                <select name="physical_room_id" class="form-select mb-3" required>
                  <option value="">Choose…</option>
                  @foreach($availableRooms as $room)
                    <option value="{{ $room->id }}">{{ $room->room_number }}</option>
                  @endforeach
                </select>

                <button class="btn btn-dark w-100" type="submit"
                        onclick="return confirm('Confirm check-in and mark room as occupied?');">
                  Confirm Check-in
                </button>
              </form>
            @endif
          @endif
        </div>
      </div>

      <div class="text-muted small mt-2">
        Tip: If a room shows “occupied” but guest left, use <strong>Check Out</strong> to free it.
      </div>
    </div>
  </div>

</section>

<script>
function copyBookingCode(btn) {
  const txt = document.getElementById('bookingCodeText')?.innerText || '';
  if (!txt) return;

  navigator.clipboard.writeText(txt).then(() => {
    if (!btn) return;
    const old = btn.innerText;
    btn.innerText = 'Copied';
    btn.disabled = true;
    setTimeout(() => {
      btn.innerText = old;
      btn.disabled = false;
    }, 900);
  });
}
</script>
@endsection
