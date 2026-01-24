@extends('layouts.reception')
@section('title','Reception — Bookings')

@section('content')
<section class="container rx-container py-4 py-lg-5">

  {{-- Header --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-3">
    <div class="rx-page-title">
      <div class="text-muted small">Reception</div>
      <h2 class="mb-1">Bookings</h2>
      <div class="text-muted">Overlapping stays • Check-in/out • Room assignment</div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('reception.bookings.create') }}" class="btn btn-dark">
        <i class="bi bi-plus-lg me-1"></i> New Walk-in
      </a>
      <a href="{{ route('reception.rooms.index') }}" class="btn btn-outline-dark">
        <i class="bi bi-door-open me-1"></i> Rooms Board
      </a>
    </div>
  </div>

  {{-- Flash --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  {{-- KPIs --}}
  <div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
      <div class="rx-kpi card">
        <div class="card-body">
          <div class="rx-kpi-label">Arrivals (Confirmed)</div>
          <div class="rx-kpi-value">{{ $kpiConfirmedArrivals ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="rx-kpi card">
        <div class="card-body">
          <div class="rx-kpi-label">In-house (Checked-in)</div>
          <div class="rx-kpi-value">{{ $kpiInHouseCheckedIn ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="rx-kpi card">
        <div class="card-body">
          <div class="rx-kpi-label">Checked-out Today</div>
          <div class="rx-kpi-value">{{ $kpiCheckedOutToday ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="rx-kpi card">
        <div class="card-body">
          <div class="rx-kpi-label">Cancelled Today</div>
          <div class="rx-kpi-value">{{ $kpiCancelledToday ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Filters --}}
  <div class="card rx-filter-card mb-3">
    <div class="card-body">
      <form class="row g-2 align-items-end" method="GET" action="{{ route('reception.bookings.index') }}">

        <div class="col-12 col-md-3">
          <label class="form-label mb-1">Date</label>
          <input type="date" name="date" value="{{ $date }}" class="form-control">
        </div>

        <div class="col-12 col-md-2">
          <label class="form-label mb-1">Status</label>
          <select name="status" class="form-select">
            <option value="">All</option>

            {{-- ✅ DB enum values --}}
            @foreach(['CONFIRMED','CHECKED_IN','CHECKED_OUT','CANCELLED'] as $st)
              <option value="{{ $st }}" @selected(($status ?? '')===$st)>
                {{ strtoupper(str_replace('_',' ', $st)) }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label mb-1">Room Type</label>
          <select name="room_type_id" class="form-select">
            <option value="">All</option>
            @foreach($roomTypes as $rt)
              <option value="{{ $rt->id }}" @selected((string)($roomTypeId ?? '')===(string)$rt->id)>
                {{ $rt->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-md-3">
          <label class="form-label mb-1">Search</label>
          <input type="text" name="q" value="{{ $q }}" placeholder="Code / guest / phone" class="form-control">
        </div>

        <div class="col-12 col-md-1 d-grid">
          <button class="btn btn-dark">
            <i class="bi bi-search me-1"></i> Go
          </button>
        </div>

        <div class="col-12 d-flex flex-wrap gap-2 pt-1">
          <a class="btn btn-outline-secondary btn-sm"
             href="{{ route('reception.bookings.index', array_merge(request()->query(), ['date' => now()->toDateString()])) }}">
            Today
          </a>
          <a class="btn btn-outline-secondary btn-sm"
             href="{{ route('reception.bookings.index', array_merge(request()->query(), ['date' => now()->addDay()->toDateString()])) }}">
            Tomorrow
          </a>
          <a class="btn btn-outline-secondary btn-sm"
             href="{{ route('reception.bookings.index') }}">
            Reset
          </a>
        </div>

      </form>
    </div>
  </div>

  {{-- Table --}}
  <div class="table-wrap">
    <div class="table-responsive">
      <table class="table table-hover table-striped mb-0">
        <thead class="rx-thead">
          <tr>
            <th>Guest</th>
            <th>Stay</th>
            <th>Type</th>
            <th class="text-center">Room</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>

        <tbody>
          @forelse($bookings as $b)
            @php
              $guestName = $b->guest_name ?: (optional($b->guest)->full_name ?: (optional($b->guest)->name ?: '-'));
              $phone     = $b->guest_phone ?: (optional($b->guest)->phone ?: '-');


              $in  = optional($b->check_in)->format('Y-m-d');
              $out = optional($b->check_out)->format('Y-m-d');

              $nights = null;
              if ($b->check_in && $b->check_out) {
                $nights = $b->check_in->diffInDays($b->check_out);
              }

              // ✅ DB values already UPPER_SNAKE_CASE
              $statusKey = strtoupper((string)$b->status);

              $badgeClass = match($statusKey) {
                'CONFIRMED'   => 'rx-badge rx-confirmed',
                'CHECKED_IN'  => 'rx-badge rx-in',
                'CHECKED_OUT' => 'rx-badge rx-out',
                'CANCELLED'   => 'rx-badge rx-cancelled',
                default       => 'rx-badge rx-default',
              };

              $code = $b->code ?: ('#'.$b->id);
              $roomTypeName = optional($b->roomType)->name ?: '-';

              // ✅ Physical room display with fallback to legacy
              $roomNumber = optional($b->physicalRoom)->room_number
                ?: (optional($b->room)->room_number ?: '—');
            @endphp

            <tr>
              <td class="rx-col-guest">
                <div class="fw-semibold">{{ $guestName }}</div>
                <div class="text-muted small">{{ $phone }}</div>
                <div class="text-muted small">Code: <span class="fw-semibold">{{ $code }}</span></div>
              </td>

              <td class="rx-col-stay">
                <div class="fw-semibold">{{ $in }} → {{ $out }}</div>
                <div class="text-muted small">
                  {{ $nights !== null ? ($nights.' night'.($nights==1?'':'s')) : '-' }}
                </div>
              </td>

              <td class="rx-col-type">
                <div class="fw-semibold">{{ $roomTypeName }}</div>
                @if(!empty($b->special_requests))
                  <div class="text-muted small text-truncate rx-req">
                    <i class="bi bi-chat-left-text me-1"></i>{{ $b->special_requests }}
                  </div>
                @endif
              </td>

              <td class="text-center rx-col-room">
                <span class="rx-roomchip">{{ $roomNumber }}</span>
              </td>

              <td class="rx-col-status">
                <span class="{{ $badgeClass }}">
                  {{ strtoupper(str_replace('_',' ', $statusKey)) }}
                </span>
              </td>

              <td class="text-end rx-col-actions">
                <div class="d-inline-flex gap-2 flex-wrap justify-content-end">

                  <a class="btn btn-sm btn-outline-secondary"
                     href="{{ route('reception.bookings.show', $b) }}">
                    Open
                  </a>

                  @php
                    $canCheckInToday = false;

                    if ($b->check_in) {
                      $checkInDate = $b->check_in->copy()->startOfDay();
                      $earlyAllowedFrom = $checkInDate->copy()->subDay()->setTime(12, 0, 0);
                      $canCheckInToday = now()->gte($earlyAllowedFrom);
                    }
                  @endphp

                  @if($statusKey === 'CONFIRMED')

                    @if($canCheckInToday)
                      {{-- ✅ CHECK-IN MODAL TRIGGER --}}
                      <button
                        type="button"
                        class="btn btn-sm btn-dark"
                        data-bs-toggle="modal"
                        data-bs-target="#checkinModal"
                        data-checkin-url="{{ route('reception.bookings.checkin', $b) }}"
                        data-room-type-id="{{ $b->room_type_id }}"
                        data-room-type-name="{{ $roomTypeName }}"
                        data-check-in="{{ $in }}"
                        data-check-out="{{ $out }}"
                        data-booking-code="{{ $code }}"
                        data-guest-name="{{ $guestName }}"
                      >
                        Check-in
                      </button>
                    @else
                      <span class="btn btn-sm btn-outline-danger text-dark border">
                        <i class="bi bi-clock-history me-1"></i> Too early
                      </span>
                    @endif

                    <form method="POST" action="{{ route('reception.bookings.cancel', $b) }}" class="d-inline">
                      @csrf
                      <button class="btn btn-sm btn-outline-danger"
                              type="submit"
                              onclick="return confirm('Cancel this booking?');">
                        Cancel
                      </button>
                    </form>
                  @endif

                  @if($statusKey === 'CHECKED_IN')
                    <form method="POST" action="{{ route('reception.bookings.checkout', $b) }}" class="d-inline">
                      @csrf
                      <button class="btn btn-sm btn-success"
                              type="submit"
                              onclick="return confirm('Check out guest and free room?');">
                        Check-out
                      </button>
                    </form>
                  @endif

                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted py-5">No bookings found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="p-3">
      {{ $bookings->links() }}
    </div>
  </div>

</section>

{{-- ✅ Check-in Modal (Premium) --}}
<div class="modal fade" id="checkinModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rx-modal">
      <div class="modal-header rx-modal-head">
        <div class="me-3">
          <div class="rx-modal-kicker">Check-in</div>
          <h5 class="modal-title mb-0" id="checkinTitle">Assign room</h5>
          <div class="rx-modal-sub" id="checkinSub">—</div>
        </div>

        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form method="POST" id="checkinForm" class="rx-modal-form" action="#">
        @csrf

        {{-- ✅ NEW: send physical_room_id --}}
        <input type="hidden" name="physical_room_id" id="checkinRoomId" value="">

        <div class="modal-body">
          {{-- Top summary bar --}}
          <div class="rx-modal-summary">
            <div class="rx-summary-item">
              <div class="rx-summary-label">Booking</div>
              <div class="rx-summary-value" id="checkinSummaryCode">—</div>
            </div>
            <div class="rx-summary-item">
              <div class="rx-summary-label">Guest</div>
              <div class="rx-summary-value" id="checkinSummaryGuest">—</div>
            </div>
            <div class="rx-summary-item">
              <div class="rx-summary-label">Room Type</div>
              <div class="rx-summary-value" id="checkinSummaryType">—</div>
            </div>
          </div>

          {{-- Alert --}}
          <div class="alert alert-warning d-none mb-3" id="checkinAlert">
            No available rooms for this room type right now.
          </div>

          {{-- Search --}}
          <div class="rx-room-searchbar mb-3">
            <div class="input-group">
              <span class="input-group-text rx-input-addon">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" class="form-control rx-room-search" id="checkinRoomSearch" placeholder="Search room number (e.g. 101, A3)">
              <button type="button" class="btn btn-outline-secondary" id="checkinClearSearch">Clear</button>
            </div>
            <div class="rx-help mt-2">
              Pick a room to enable <b>Confirm Check-in</b>.
            </div>
          </div>

          {{-- Loading --}}
          <div class="rx-room-loading d-none" id="checkinLoading">
            <div class="spinner-border" role="status" aria-hidden="true"></div>
            <div class="ms-2">Loading available rooms…</div>
          </div>

          {{-- Room tiles --}}
          <div class="rx-room-list" id="checkinRoomList">
            {{-- injected by JS --}}
          </div>
        </div>

        <div class="modal-footer rx-modal-foot">
          <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>

          <button type="submit" class="btn btn-gold" id="checkinSubmitBtn" disabled>
            <span class="rx-btn-label">
              <i class="bi bi-check2-circle me-1"></i> Confirm Check-in
            </span>
            <span class="rx-btn-loading d-none">
              <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
              Processing…
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const modalEl = document.getElementById('checkinModal');
  if (!modalEl) return;

  const roomsApiBase = "{{ route('reception.api.availableRooms') }}";

  const form   = document.getElementById('checkinForm');
  const title  = document.getElementById('checkinTitle');
  const sub    = document.getElementById('checkinSub');

  const sumCode  = document.getElementById('checkinSummaryCode');
  const sumGuest = document.getElementById('checkinSummaryGuest');
  const sumType  = document.getElementById('checkinSummaryType');

  const alertEl = document.getElementById('checkinAlert');
  const listEl  = document.getElementById('checkinRoomList');
  const loadEl  = document.getElementById('checkinLoading');

  const searchEl = document.getElementById('checkinRoomSearch');
  const clearEl  = document.getElementById('checkinClearSearch');

  const roomIdInput = document.getElementById('checkinRoomId');
  const submitBtn   = document.getElementById('checkinSubmitBtn');

  function resetUI() {
    title.textContent = 'Assign room';
    sub.textContent = '—';

    sumCode.textContent = '—';
    sumGuest.textContent = '—';
    sumType.textContent = '—';

    alertEl.classList.add('d-none');
    loadEl.classList.add('d-none');

    listEl.innerHTML = '';
    roomIdInput.value = '';
    submitBtn.disabled = true;

    if (searchEl) searchEl.value = '';
  }

  function setLoading(on) {
    if (on) loadEl.classList.remove('d-none');
    else loadEl.classList.add('d-none');
  }

  function setNoRooms(msg) {
    alertEl.textContent = msg || 'No available rooms for this room type right now.';
    alertEl.classList.remove('d-none');
    listEl.innerHTML = '';
    roomIdInput.value = '';
    submitBtn.disabled = true;
  }

  function renderRooms(rooms) {
    alertEl.classList.add('d-none');

    listEl.innerHTML = rooms.map(r => `
      <label class="rx-room-tile" data-room="${String(r.room_number || '').toLowerCase()}">
        <input type="radio" name="rx_room_pick" value="${r.id}" class="rx-room-radio">
        <div class="rx-room-tile-body">
          <div class="rx-room-num">${r.room_number}</div>
          <div class="rx-room-meta">Available</div>
        </div>
        <div class="rx-room-check">
          <i class="bi bi-check2"></i>
        </div>
      </label>
    `).join('');

    Array.from(listEl.querySelectorAll('.rx-room-radio')).forEach(radio => {
      radio.addEventListener('change', () => {
        roomIdInput.value = radio.value;
        submitBtn.disabled = !roomIdInput.value;
      });
    });
  }

  function applySearch(q) {
    q = (q || '').trim().toLowerCase();
    const tiles = Array.from(listEl.querySelectorAll('.rx-room-tile'));
    tiles.forEach(t => {
      const rn = t.getAttribute('data-room') || '';
      const match = !q || rn.includes(q);
      t.classList.toggle('is-dim', !match);
    });
  }

  modalEl.addEventListener('show.bs.modal', async function (ev) {
    resetUI();

    const btn = ev.relatedTarget;
    if (!btn) return;

    const checkinUrl  = btn.getAttribute('data-checkin-url') || '';
    const roomTypeId  = btn.getAttribute('data-room-type-id') || '';
    const roomTypeNm  = btn.getAttribute('data-room-type-name') || '-';
    const bookingCode = btn.getAttribute('data-booking-code') || 'Booking';
    const guestName   = btn.getAttribute('data-guest-name') || '-';

    const checkIn  = btn.getAttribute('data-check-in') || '';
    const checkOut = btn.getAttribute('data-check-out') || '';

    form.setAttribute('action', checkinUrl);

    title.textContent = `Assign room for ${bookingCode}`;
    sub.textContent   = `Guest: ${guestName}`;

    sumCode.textContent  = bookingCode;
    sumGuest.textContent = guestName;
    sumType.textContent  = roomTypeNm;

    if (!roomTypeId || !checkIn || !checkOut) {
      setNoRooms('Missing stay dates. Please refresh and try again.');
      return;
    }

    setLoading(true);

    try {
      const res = await fetch(
        `${roomsApiBase}?room_type_id=${encodeURIComponent(roomTypeId)}&check_in=${encodeURIComponent(checkIn)}&check_out=${encodeURIComponent(checkOut)}`,
        { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
      );

      const data = await res.json();
      setLoading(false);

      if (!Array.isArray(data) || data.length === 0) {
        setNoRooms();
        return;
      }

      renderRooms(data);
      setTimeout(() => searchEl?.focus(), 120);

    } catch (e) {
      setLoading(false);
      setNoRooms('Failed to load rooms. Please try again.');
    }
  });

  modalEl.addEventListener('hidden.bs.modal', function () {
    form.setAttribute('action', '#');
    resetUI();
  });

  searchEl?.addEventListener('input', () => applySearch(searchEl.value));
  clearEl?.addEventListener('click', () => {
    if (!searchEl) return;
    searchEl.value = '';
    applySearch('');
    searchEl.focus();
  });

  form?.addEventListener('submit', function (e) {
    if (!roomIdInput.value) {
      e.preventDefault();
      setNoRooms('Please choose a room to continue.');
      return;
    }

    const btnLabel = submitBtn.querySelector('.rx-btn-label');
    const btnLoad  = submitBtn.querySelector('.rx-btn-loading');

    submitBtn.disabled = true;
    if (btnLabel) btnLabel.classList.add('d-none');
    if (btnLoad) btnLoad.classList.remove('d-none');
  });
});
</script>
@endpush
