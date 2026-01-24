@extends('layouts.reception')
@section('title','Reception — Rooms Board')

@section('content')
<section class="container rx-container py-4 py-lg-5">

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-3">
    <div>
      <div class="text-muted small">Reception</div>
      <h2 class="mb-1">Rooms Board</h2>
      <div class="text-muted">Filter by status • Quick scan by room number</div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-dark" href="{{ route('reception.bookings.index') }}">
        <i class="bi bi-journal-text me-1"></i> Bookings
      </a>
    </div>
  </div>

  {{-- Filters + Search --}}
  @php
    // ✅ UI uses lowercase keys: all|available|occupied|booked|maintenance
    $active = strtolower((string)($status ?: 'all'));
  @endphp

  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <div class="row g-2 align-items-center">
        <div class="col-12 col-lg-7">
          <ul class="nav nav-pills flex-wrap gap-2 mb-0">

            <li class="nav-item">
              <a class="nav-link @if($active==='all') active @endif"
                 href="{{ route('reception.rooms.index') }}">
                All
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link @if($active==='available') active @endif"
                 href="{{ route('reception.rooms.index', ['status' => 'available']) }}">
                <i class="bi bi-check2-circle me-1"></i> Available
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link @if($active==='occupied') active @endif"
                 href="{{ route('reception.rooms.index', ['status' => 'occupied']) }}">
                <i class="bi bi-person-fill me-1"></i> Occupied
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link @if($active==='booked') active @endif"
                 href="{{ route('reception.rooms.index', ['status' => 'booked']) }}">
                <i class="bi bi-bookmark-check me-1"></i> Booked
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link @if($active==='maintenance') active @endif"
                 href="{{ route('reception.rooms.index', ['status' => 'maintenance']) }}">
                <i class="bi bi-tools me-1"></i> Maintenance
              </a>
            </li>

          </ul>
        </div>

        <div class="col-12 col-lg-5">
          <label class="form-label mb-1">Quick find room</label>
          <div class="input-group">
            <span class="input-group-text rx-input-addon">
              <i class="bi bi-search"></i>
            </span>
            <input id="roomSearch" class="form-control" placeholder="Type room number e.g. 101, 12, A3…">
            <button class="btn btn-outline-secondary" type="button" id="clearSearch">Clear</button>
          </div>
          <div class="text-muted small mt-1">Tip: type any digits/letters — matching tiles will glow.</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Board --}}
  <div class="row g-3">

    @php
      /**
       * ✅ Normalize DB room status -> UI key
       * DB: Available / Booked / Occupied / OutOfService
       * UI: available / booked / occupied / maintenance
       */
      $normStatus = function($dbStatus) {
        $s = strtolower(trim((string)$dbStatus));
        return match ($s) {
          'available'     => 'available',
          'occupied'      => 'occupied',
          'booked'        => 'booked',
          'outofservice',
          'out_of_service',
          'oos',
          'maintenance'   => 'maintenance',
          default         => 'available',
        };
      };
    @endphp

    {{-- ✅ TBD Rooms (room_type_id = null) --}}
    @if(isset($tbdRooms) && $tbdRooms->count())
      @php
        $available = $tbdRooms->filter(fn($r) => $normStatus($r->status) === 'available')->count();
        $occupied  = $tbdRooms->filter(fn($r) => $normStatus($r->status) === 'occupied')->count();
        $booked    = $tbdRooms->filter(fn($r) => $normStatus($r->status) === 'booked')->count();
        $maint     = $tbdRooms->filter(fn($r) => $normStatus($r->status) === 'maintenance')->count();
        $total     = $tbdRooms->count();
      @endphp

      <div class="col-lg-6">
        <div class="card shadow-sm h-100 border border-warning">
          <div class="card-body">

            <div class="d-flex justify-content-between align-items-start gap-2">
              <div>
                <h5 class="mb-1">
                  <i class="bi bi-question-circle me-1 text-warning"></i> TBD Rooms
                </h5>
                <div class="text-muted small">
                  Not assigned to a room type yet • Admin must classify these rooms.
                </div>
                <div class="text-muted small mt-1">
                  Total: {{ $total }} •
                  <span class="text-success">Avail: {{ $available }}</span> •
                  <span class="text-danger">Occ: {{ $occupied }}</span> •
                  <span class="text-primary">Booked: {{ $booked }}</span> •
                  <span class="text-warning">Maint: {{ $maint }}</span>
                </div>
              </div>

              <div class="text-muted small">
                Online quota: <span class="fw-semibold">N/A</span>
              </div>
            </div>

            <hr>

            <div class="room-grid">
              @foreach($tbdRooms as $room)
                @php
                  $statusKey = $normStatus($room->status);
                  $badgeText = strtoupper($statusKey);

                  $tileClass = match($statusKey) {
                    'available'   => 'room-tile room-available',
                    'occupied'    => 'room-tile room-occupied',
                    'booked'      => 'room-tile room-booked',
                    'maintenance' => 'room-tile room-maintenance',
                    default       => 'room-tile room-default',
                  };

                  $pillClass = match($statusKey) {
                    'available'   => 'room-pill room-pill-available',
                    'occupied'    => 'room-pill room-pill-occupied',
                    'booked'      => 'room-pill room-pill-booked',
                    'maintenance' => 'room-pill room-pill-maintenance',
                    default       => 'room-pill room-pill-default',
                  };
                @endphp

                <div class="{{ $tileClass }}"
                     data-room="{{ $room->room_number }}"
                     data-status="{{ $statusKey }}">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="fw-semibold room-number">{{ $room->room_number }}</div>
                    <span class="{{ $pillClass }}">{{ $badgeText }}</span>
                  </div>
                </div>
              @endforeach
            </div>

          </div>
        </div>
      </div>
    @endif

    {{-- Room types --}}
    @foreach($roomTypes as $rt)

      @php
        // ✅ switched from rooms -> physicalRooms
        $rooms = $rt->physicalRooms ?? collect();

        $available = $rooms->filter(fn($r) => $normStatus($r->status) === 'available')->count();
        $occupied  = $rooms->filter(fn($r) => $normStatus($r->status) === 'occupied')->count();
        $booked    = $rooms->filter(fn($r) => $normStatus($r->status) === 'booked')->count();
        $maint     = $rooms->filter(fn($r) => $normStatus($r->status) === 'maintenance')->count();
        $total     = $rooms->count();
      @endphp

      <div class="col-lg-6">
        <div class="card shadow-sm h-100">
          <div class="card-body">

            <div class="d-flex justify-content-between align-items-start gap-2">
              <div>
                <h5 class="mb-1">{{ $rt->name }}</h5>
                <div class="text-muted small">
                  Total: {{ $total }} •
                  <span class="text-success">Avail: {{ $available }}</span> •
                  <span class="text-danger">Occ: {{ $occupied }}</span> •
                  <span class="text-primary">Booked: {{ $booked }}</span> •
                  <span class="text-warning">Maint: {{ $maint }}</span>
                </div>
              </div>

              <div class="text-muted small">
                Online quota: <span class="fw-semibold">{{ $rt->online_quota ?? '-' }}</span>
              </div>
            </div>

            <hr>

            <div class="room-grid">
              @forelse($rooms as $room)
                @php
                  $statusKey = $normStatus($room->status);
                  $badgeText = strtoupper($statusKey);

                  $tileClass = match($statusKey) {
                    'available'   => 'room-tile room-available',
                    'occupied'    => 'room-tile room-occupied',
                    'booked'      => 'room-tile room-booked',
                    'maintenance' => 'room-tile room-maintenance',
                    default       => 'room-tile room-default',
                  };

                  $pillClass = match($statusKey) {
                    'available'   => 'room-pill room-pill-available',
                    'occupied'    => 'room-pill room-pill-occupied',
                    'booked'      => 'room-pill room-pill-booked',
                    'maintenance' => 'room-pill room-pill-maintenance',
                    default       => 'room-pill room-pill-default',
                  };
                @endphp

                <div class="{{ $tileClass }}"
                     data-room="{{ $room->room_number }}"
                     data-status="{{ $statusKey }}">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="fw-semibold room-number">{{ $room->room_number }}</div>
                    <span class="{{ $pillClass }}">{{ $badgeText }}</span>
                  </div>
                </div>
              @empty
                <div class="text-muted">No rooms created yet.</div>
              @endforelse
            </div>

          </div>
        </div>
      </div>
    @endforeach
  </div>

</section>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('roomSearch');
    const clear = document.getElementById('clearSearch');
    const tiles = Array.from(document.querySelectorAll('.room-tile[data-room]'));

    function applyFilter(q){
      q = (q || '').trim().toLowerCase();

      tiles.forEach(t => {
        t.classList.remove('is-match','is-dim');
        if (!q) return;

        const rn = (t.getAttribute('data-room') || '').toLowerCase();
        const isMatch = rn.includes(q);

        if (isMatch) t.classList.add('is-match');
        else t.classList.add('is-dim');
      });
    }

    input?.addEventListener('input', () => applyFilter(input.value));
    clear?.addEventListener('click', () => {
      if (!input) return;
      input.value = '';
      applyFilter('');
      input.focus();
    });
  });
</script>
@endpush
