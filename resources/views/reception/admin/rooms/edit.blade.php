@extends('layouts.reception')

@section('content')
<div class="container py-4">

  <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
    <div>
      <div class="text-muted small">Admin</div>
      <h4 class="mb-0">Edit Room • {{ $room->room_number }}</h4>
      <div class="text-muted small">Update room label, classify type, and manage maintenance status</div>
    </div>

    <a href="{{ route('reception.admin.rooms.index') }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i> Back
    </a>
  </div>

  <div class="row justify-content-center">
    <div class="col-12 col-lg-8">

      @if($errors->any())
        <div class="alert alert-danger">
          <div class="fw-semibold mb-2">Fix these:</div>
          <ul class="mb-0">
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('reception.admin.rooms.update', $room) }}" class="card shadow-sm border-0 overflow-hidden">
        @csrf
        @method('PUT')

        <div class="card-body p-4">

          {{-- Room number --}}
          <div class="mb-3">
            <label class="form-label fw-semibold">Room Number</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="bi bi-hash"></i>
              </span>
              <input
                name="room_number"
                class="form-control"
                value="{{ old('room_number', $room->room_number) }}"
                placeholder="e.g. 101, A3, G-12"
                required
                autocomplete="off"
              >
            </div>
            <div class="form-text">Any format is allowed — match the real hotel numbering.</div>
          </div>

          {{-- Room type assignment --}}
          <div class="mb-3">
            <label class="form-label fw-semibold">Room Type</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="bi bi-grid-3x3-gap"></i>
              </span>
              <select name="room_type_id" class="form-select">
                <option value="">TBD (Unassigned)</option>
                @foreach($roomTypes as $t)
                  <option value="{{ $t->id }}"
                    @selected((string)old('room_type_id', $room->room_type_id) === (string)$t->id)>
                    {{ $t->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="form-text">
              ✅ <b>TBD rooms stay hidden online</b> and cannot be auto-booked.
            </div>
          </div>

          {{-- Status --}}
          <div class="mb-3">
            <label class="form-label fw-semibold d-flex align-items-center justify-content-between">
              <span>Room Status</span>

              @php
                $st = (string)old('status', $room->status);
                $badge = match($st) {
                  'Available' => 'bg-success-subtle text-dark border',
                  'Booked' => 'bg-primary-subtle text-dark border',
                  'Occupied' => 'bg-danger-subtle text-dark border',
                  'OutOfService' => 'bg-warning-subtle text-dark border',
                  default => 'bg-secondary-subtle text-dark border',
                };
              @endphp
              <span class="badge {{ $badge }}">{{ $st }}</span>
            </label>

            <div class="input-group">
              <span class="input-group-text">
                <i class="bi bi-activity"></i>
              </span>
              <select name="status" class="form-select" required>
                @foreach(['Available','Booked','Occupied','OutOfService'] as $s)
                  <option value="{{ $s }}" @selected(old('status', $room->status) === $s)>
                    {{ $s }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="form-text">
              Use <b>OutOfService</b> for rooms under maintenance (the “extra 9”) — you can switch back anytime.
            </div>
          </div>

          {{-- Notes --}}
          <div class="mb-0">
            <label class="form-label fw-semibold">Internal Notes</label>
            <div class="input-group">
              <span class="input-group-text align-items-start pt-2">
                <i class="bi bi-journal-text"></i>
              </span>
              <textarea
                name="notes"
                class="form-control"
                rows="4"
                placeholder="Maintenance notes, issues, etc."
              >{{ old('notes', $room->notes) }}</textarea>
            </div>
          </div>

        </div>

        <div class="card-footer bg-light d-flex flex-column flex-sm-row justify-content-end gap-2 p-3">
          <a href="{{ route('reception.admin.rooms.index') }}" class="btn btn-outline-secondary">
            Cancel
          </a>
          <button class="btn btn-primary">
            <i class="bi bi-save2 me-1"></i> Save Changes
          </button>
        </div>
      </form>

      <div class="text-muted small mt-3">
        Tip: If a manager wants to change room numbering later, edit the room label.
      </div>

    </div>
  </div>

</div>
@endsection
