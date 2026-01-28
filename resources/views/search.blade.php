@extends('layouts.public')
@section('title','Check Availability — Mach Hotel')

@section('content')
<section class="container py-5">

  {{-- Header --}}
  <div class="mb-4">
    <h2 class="mb-1">Check Availability</h2>
    <div class="text-muted">Find available rooms instantly. Bookings are auto-confirmed.</div>
  </div>

  {{-- Search Card --}}
  <div class="search-card p-3 p-md-4">
    <form class="row g-2 g-md-3 align-items-end" method="get" action="{{ route('search') }}">

      {{-- Check-in --}}
      <div class="col-md-3 col-6">
        <label class="form-label small mb-1">Check-in</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
          <input
            class="form-control"
            type="date"
            id="checkIn"
            name="check_in"
            min="{{ now('Africa/Dar_es_Salaam')->toDateString() }}"
            value="{{ request('check_in') }}"
            required
          >
        </div>
      </div>

      {{-- Check-out --}}
      <div class="col-md-3 col-6">
        <label class="form-label small mb-1">Check-out</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-calendar2-check"></i></span>
          <input
            class="form-control"
            type="date"
            id="checkOut"
            name="check_out"
            min="{{ now('Africa/Dar_es_Salaam')->addDay()->toDateString() }}"
            value="{{ request(key: 'check_out') }}"
            required
          >
        </div>
        
      </div>

      {{-- Guests --}}
      <div class="col-md-3">
        <label class="form-label small mb-1">Guests</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-people"></i></span>
          <select class="form-select" name="guests">
            @foreach([1,2] as $g)
              <option value="{{ $g }}" @selected((int)request('guests',1) === $g)>
                {{ $g }} {{ $g === 1 ? 'Guest' : 'Guests' }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      {{-- Button --}}
      <div class="col-md-3 d-grid">
        <button class="btn btn-dark btn-lg">
          <i class="bi bi-search me-1"></i> Search
        </button>
      </div>

    </form>
  </div>

  {{-- RESULTS --}}
  @if(request()->filled(['check_in','check_out']))
    <div class="mt-4">
      @if(isset($roomTypes) && $roomTypes->count())
        <div class="row g-3">
          @foreach($roomTypes as $type)
            <div class="col-lg-4">
              <div class="room-card h-100">
                <div class="room-thumb"></div>

                <div class="p-3">
                  <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="fw-semibold">{{ $type->name }}</div>
                    <span class="badge rounded-pill text-bg-dark">
                      {{ $type->capacity }} pax
                    </span>
                  </div>

                  <div class="text-muted small mt-1">
                    <span class="fw-semibold">TZS {{ number_format($type->price_tzs) }}</span>
                    • USD {{ number_format($type->price_usd) }}
                    • per night
                  </div>

                  <div class="d-grid mt-3">
                    <a class="btn btn-gold"
                       href="{{ route('book', [
                         'roomType' => $type->id,
                         'check_in' => request('check_in'),
                         'check_out' => request('check_out'),
                         'guests' => request('guests',1)
                       ]) }}">
                      Book now
                    </a>
                  </div>

                  <div class="text-muted small mt-2">
                    Pay at counter • Instant confirmation
                  </div>
                </div>

              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="alert alert-warning">
          <div class="fw-semibold mb-1">No rooms available for those dates.</div>
          <div class="small text-muted">
            Try adjusting dates or guests. If online quota is full, reception can still help with walk-ins.
          </div>
        </div>
      @endif
    </div>
  @else
    <div class="mt-4">
      <div class="alert alert-secondary mb-0">
        Pick your dates above, then tap <strong>Search</strong> to see available rooms.
      </div>
    </div>
  @endif

</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const inEl  = document.getElementById('checkIn');
  const outEl = document.getElementById('checkOut');
  if (!inEl || !outEl) return;

  const addDays = (dateStr, days) => {
    const d = new Date(dateStr + 'T00:00:00');
    d.setDate(d.getDate() + days);
    return d.toISOString().slice(0, 10);
  };

  const setInvalid = (on) => {
    outEl.classList.toggle('is-invalid', !!on);
  };

  const sync = () => {
    // If no check-in selected yet, don't force anything
    if (!inEl.value) {
      outEl.min = '';
      setInvalid(false);
      return;
    }

    // ✅ Hard rule: checkout min = checkin + 1 day (prevents same-day selection)
    const minOut = addDays(inEl.value, 1);
    outEl.min = minOut;

    // ✅ Premium UX: do NOT auto-fill checkout.
    // Only clear it if it's invalid (same-day or earlier).
    if (outEl.value && outEl.value < minOut) {
      outEl.value = '';
      setInvalid(true);
    } else {
      setInvalid(false);
    }
  };

  // On load (handles back button + prefilled values)
  sync();

  inEl.addEventListener('change', sync);
  outEl.addEventListener('change', sync);

  // Extra guard: if user focuses checkout without picking checkin,
  // gently push them to check-in first (no annoying alert).
  outEl.addEventListener('focus', () => {
    if (!inEl.value) inEl.focus();
  });
});
</script>
@endpush
