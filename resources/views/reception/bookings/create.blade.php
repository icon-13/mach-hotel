{{-- resources/views/reception/bookings/create.blade.php --}}
@extends('layouts.reception')
@section('title','Reception — New Walk-in')

@section('content')
<section class="container rx-container py-4 py-lg-5">

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-3">
    <div>
      <div class="text-muted small">Reception</div>
      <h2 class="mb-1">New Walk-in</h2>
      <div class="text-muted">Create a booking fast • optionally check-in immediately</div>
    </div>
    <a class="btn btn-outline-dark" href="{{ route('reception.bookings.index') }}">
      <i class="bi bi-arrow-left me-1"></i> Back
    </a>
  </div>

  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Please fix the following:</div>
      <ul class="mb-0">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <div class="row g-3">
    {{-- Form --}}
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-body">

          <form method="POST" action="{{ route('reception.bookings.store') }}" id="walkinForm">
            @csrf

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Guest name</label>
                <input
                  name="guest_name"
                  value="{{ old('guest_name') }}"
                  required
                  class="form-control"
                  placeholder="e.g. John Mjema"
                  autocomplete="name"
                >
              </div>

              <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input
                  name="guest_phone"
                  value="{{ old('guest_phone') }}"
                  required
                  class="form-control"
                  placeholder="e.g. +255 7xx xxx xxx"
                  inputmode="tel"
                  autocomplete="tel"
                >
                <div class="form-text">Tip: use phone to auto-find returning guests.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Room type</label>
                <select name="room_type_id" id="roomTypeSelect" required class="form-select">
                  <option value="">Select…</option>
                  @foreach($roomTypes as $rt)
                    <option value="{{ $rt->id }}" @selected(old('room_type_id')==(string)$rt->id)>
                      {{ $rt->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-3">
                <label class="form-label">Check-in</label>
                <input
                  type="date"
                  name="check_in"
                  id="checkInInput"
                  value="{{ old('check_in', $defaultCheckIn) }}"
                  required
                  class="form-control js-date-in"
                  min="{{ now('Africa/Dar_es_Salaam')->toDateString() }}"
                >
              </div>

              <div class="col-md-3">
                <label class="form-label">Check-out</label>
                <input
                  type="date"
                  name="check_out"
                  id="checkOutInput"
                  value="{{ old('check_out', $defaultCheckOut) }}"
                  required
                  class="form-control js-date-out"
                  min="{{ now('Africa/Dar_es_Salaam')->addDay()->toDateString() }}"
                >
              </div>

              <div class="col-12">
                <label class="form-label">Special requests</label>
                <textarea
                  name="special_requests"
                  rows="3"
                  class="form-control"
                  placeholder="Optional (late arrival, extra towel, etc.)"
                >{{ old('special_requests') }}</textarea>
              </div>
            </div>

            <hr class="my-4">

            {{-- Check-in now --}}
            <div class="d-flex flex-column gap-2">
              <div class="form-check">
                <input
                  class="form-check-input"
                  type="checkbox"
                  value="1"
                  name="check_in_now"
                  id="checkInNow"
                  @checked(old('check_in_now'))
                >
                <label class="form-check-label fw-semibold" for="checkInNow">
                  Check-in immediately (assign room now)
                </label>
              </div>

              <div id="roomPickWrap" class="mt-2 d-none">
                <label class="form-label">Pick available physical room</label>

                {{-- ✅ NEW FIELD NAME --}}
                <select name="physical_room_id" id="roomSelect" class="form-select">
                  <option value="">Select a room…</option>
                </select>

                <div class="text-muted small mt-2">
                  If no rooms appear, that room type has no available rooms for those dates.
                </div>
              </div>
            </div>

            <div class="d-flex gap-2 flex-wrap mt-4">
              <button type="submit" class="btn btn-dark">
                <i class="bi bi-check2-circle me-1"></i> Create
              </button>
              <a href="{{ route('reception.bookings.index') }}" class="btn btn-outline-secondary">
                Cancel
              </a>
            </div>

          </form>

        </div>
      </div>
    </div>

    {{-- Side help --}}
    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="icon-pill" style="width:38px;height:38px;"><i class="bi bi-lightning-charge"></i></span>
            <h5 class="mb-0">Fast flow</h5>
          </div>
          <p class="text-muted mb-0">
            Turn on <span class="fw-semibold">Check-in immediately</span> to assign a physical room now.
            If off, the booking is created as <span class="fw-semibold">Confirmed</span> (room assigned later).
          </p>
          <hr>
          <div class="text-muted small">
            Best practice: walk-ins usually check-in now. Online bookings usually check-in after arrival.
          </div>
        </div>
      </div>
    </div>
  </div>

</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const check = document.getElementById('checkInNow');
  const wrap  = document.getElementById('roomPickWrap');
  const typeSelect = document.getElementById('roomTypeSelect');
  const roomSelect = document.getElementById('roomSelect');

  const checkInInput  = document.getElementById('checkInInput');
  const checkOutInput = document.getElementById('checkOutInput');

  function addDays(dateStr, days){
    const d = new Date(dateStr + 'T00:00:00');
    d.setDate(d.getDate() + days);
    return d.toISOString().slice(0,10);
  }

  function syncDates(){
    if (!checkInInput || !checkOutInput || !checkInInput.value) return;
    const minOut = addDays(checkInInput.value, 1);
    checkOutInput.min = minOut;
    if (!checkOutInput.value || checkOutInput.value < minOut) checkOutInput.value = minOut;
  }

    function toggleRoomPick() {
    const on = check.checked;
    wrap.classList.toggle('d-none', !on);
    roomSelect.required = on; // ✅ makes browser enforce it too
  }


  function buildUrl() {
    const typeId = typeSelect.value;
    const ci = checkInInput?.value || '';
    const co = checkOutInput?.value || '';

    const params = new URLSearchParams();
    params.set('room_type_id', typeId);
    params.set('check_in', ci);
    params.set('check_out', co);

    return "{{ route('reception.api.availableRooms') }}" + "?" + params.toString();
  }

  async function loadRooms() {
    roomSelect.innerHTML = '<option value="">Loading…</option>';

    const typeId = typeSelect.value;
    const ci = checkInInput?.value;
    const co = checkOutInput?.value;

    if (!typeId) {
      roomSelect.innerHTML = '<option value="">Select room type first…</option>';
      return;
    }
    if (!ci || !co) {
      roomSelect.innerHTML = '<option value="">Select dates first…</option>';
      return;
    }

    try {
      const res = await fetch(buildUrl(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const data = await res.json();

      let html = '<option value="">Select a room…</option>';
      if (!Array.isArray(data) || data.length === 0) {
        html = '<option value="">No available rooms</option>';
      } else {
        data.forEach(function (r) {
          html += '<option value="'+r.id+'">'+r.room_number+'</option>';
        });
      }
      roomSelect.innerHTML = html;
    } catch (e) {
      roomSelect.innerHTML = '<option value="">Failed to load rooms</option>';
    }
  }

  // Events
  checkInInput?.addEventListener('change', () => { syncDates(); if (check.checked) loadRooms(); });
  checkOutInput?.addEventListener('change', () => { if (check.checked) loadRooms(); });
  syncDates();

  check.addEventListener('change', function () {
    toggleRoomPick();
    if (check.checked) loadRooms();
  });

  typeSelect.addEventListener('change', function () {
    syncDates();
    if (check.checked) loadRooms();
  });

  toggleRoomPick();
  if (check.checked) loadRooms(); // handles old('check_in_now') after validation errors
  
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // ✅ Flatpickr for iOS past-date blocking
  if (typeof flatpickr !== 'undefined') {
    const inEl  = document.querySelector('.js-date-in');
    const outEl = document.querySelector('.js-date-out');

    if (inEl && outEl) {
      const today = new Date(); today.setHours(0,0,0,0);

      const outPicker = flatpickr(outEl, {
        dateFormat: "Y-m-d",
        minDate: new Date(today.getTime() + 86400000),
        defaultDate: outEl.value || null,
        allowInput: false
      });

      const inPicker = flatpickr(inEl, {
        dateFormat: "Y-m-d",
        minDate: today,
        defaultDate: inEl.value || null,
        allowInput: false,
        onChange: function(selectedDates){
          const ci = selectedDates && selectedDates[0] ? selectedDates[0] : null;
          if (!ci) return;

          ci.setHours(0,0,0,0);
          const minOut = new Date(ci.getTime() + 86400000);

          outPicker.set('minDate', minOut);

          const currentOut = outPicker.selectedDates && outPicker.selectedDates[0] ? outPicker.selectedDates[0] : null;
          if (!currentOut || currentOut < minOut) {
            outPicker.setDate(minOut, true);
          }
        }
      });

      if (inPicker.selectedDates.length && !outPicker.selectedDates.length) {
        const ci = inPicker.selectedDates[0];
        const minOut = new Date(ci.getTime() + 86400000);
        outPicker.set('minDate', minOut);
        outPicker.setDate(minOut, true);
      }
    }
  }

  // ✅ keep your existing walk-in JS below (room loading, etc.)
  const check = document.getElementById('checkInNow');
  const wrap  = document.getElementById('roomPickWrap');
  const typeSelect = document.getElementById('roomTypeSelect');
  const roomSelect = document.getElementById('roomSelect');

  const checkInInput  = document.getElementById('checkInInput');
  const checkOutInput = document.getElementById('checkOutInput');

  function toggleRoomPick() {
    wrap.classList.toggle('d-none', !check.checked);
  }

  function buildUrl() {
    const typeId = typeSelect.value;
    const ci = checkInInput?.value || '';
    const co = checkOutInput?.value || '';

    const params = new URLSearchParams();
    params.set('room_type_id', typeId);
    params.set('check_in', ci);
    params.set('check_out', co);

    return "{{ route('reception.api.availableRooms') }}" + "?" + params.toString();
  }

  async function loadRooms() {
    roomSelect.innerHTML = '<option value="">Loading…</option>';

    const typeId = typeSelect.value;
    const ci = checkInInput?.value;
    const co = checkOutInput?.value;

    if (!typeId) {
      roomSelect.innerHTML = '<option value="">Select room type first…</option>';
      return;
    }
    if (!ci || !co) {
      roomSelect.innerHTML = '<option value="">Select dates first…</option>';
      return;
    }

    try {
      const res = await fetch(buildUrl(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      const data = await res.json();

      let html = '<option value="">Select a room…</option>';
      if (!Array.isArray(data) || data.length === 0) {
        html = '<option value="">No available rooms</option>';
      } else {
        data.forEach(function (r) {
          html += '<option value="'+r.id+'">'+r.room_number+'</option>';
        });
      }
      roomSelect.innerHTML = html;
    } catch (e) {
      roomSelect.innerHTML = '<option value="">Failed to load rooms</option>';
    }
  }

  check.addEventListener('change', function () {
    toggleRoomPick();
    if (check.checked) loadRooms();
  });

  typeSelect.addEventListener('change', function () {
    if (check.checked) loadRooms();
  });

  checkInInput?.addEventListener('change', () => { if (check.checked) loadRooms(); });
  checkOutInput?.addEventListener('change', () => { if (check.checked) loadRooms(); });

  toggleRoomPick();
  if (check.checked) loadRooms();

    // ✅ Prevent submit if check-in-now is ON but no room selected
  const form = document.getElementById('walkinForm');
  form?.addEventListener('submit', (e) => {
    if (check.checked) {
      const val = roomSelect.value;
      if (!val) {
        e.preventDefault();
        roomSelect.focus();
        alert('Please select a physical room for immediate check-in.');
      }
    }
  });

});
</script>

@endsection
