@extends('layouts.reception')

@section('content')
<div class="container py-4">

  <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
    <div>
      <div class="text-muted small">Admin</div>
      <h4 class="mb-0">Physical Rooms (21)</h4>
      <div class="text-muted small">Classify rooms • Set maintenance • Keep TBD hidden online</div>
    </div>

    <a href="{{ route('reception.bookings.index') }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i> Back to Bookings
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- Filters --}}
  <form class="row g-2 mb-3" method="GET">
    <div class="col-12 col-md-5">
      <label class="form-label mb-1">Room Type</label>
      <select name="room_type_id" class="form-select">
        <option value="">All rooms</option>
        <option value="tbd" {{ ($selectedType ?? '') === 'tbd' ? 'selected' : '' }}>TBD (Unassigned)</option>
        @foreach($roomTypes as $t)
          <option value="{{ $t->id }}" {{ (string)($selectedType ?? '') === (string)$t->id ? 'selected' : '' }}>
            {{ $t->name }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="col-12 col-md-5">
      <label class="form-label mb-1">Status</label>
      <select name="status" class="form-select">
        <option value="">All statuses</option>
        @foreach(['Available','Booked','Occupied','OutOfService'] as $s)
          <option value="{{ $s }}" {{ (string)($selectedStatus ?? '') === (string)$s ? 'selected' : '' }}>
            {{ $s }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="col-12 col-md-2 d-grid">
      <label class="form-label mb-1 d-none d-md-block">&nbsp;</label>
      <button class="btn btn-primary">
        <i class="bi bi-funnel me-1"></i> Filter
      </button>
    </div>

    <div class="col-12 d-flex flex-wrap gap-2">
      <a class="btn btn-outline-secondary btn-sm"
         href="{{ route('reception.admin.rooms.index') }}">
        Reset
      </a>
      <span class="text-muted small align-self-center">
        Tip: mark the 9 not-ready rooms as <b>OutOfService</b>. You can revert later.
      </span>
    </div>
  </form>

  {{-- Bulk Assign + Bulk Status --}}
  <div class="card mb-4">
    <div class="card-header fw-semibold">
      Bulk Update (fast)
      <div class="text-muted small fw-normal">Assign room type + optionally set status (maintenance)</div>
    </div>

    <div class="card-body">
      <form class="row g-2" method="POST" action="{{ route('reception.admin.rooms.bulkAssign') }}">
        @csrf

        <div class="col-12 col-lg-4">
          <label class="form-label">Assign to Room Type</label>
          <select name="room_type_id" class="form-select">
            <option value="">TBD (Unassigned)</option>
            @foreach($roomTypes as $t)
              <option value="{{ $t->id }}">{{ $t->name }}</option>
            @endforeach
          </select>
          <div class="form-text">Leave blank to keep rooms as TBD (hidden online).</div>
        </div>

        <div class="col-12 col-lg-4">
          <label class="form-label">Optional: Set Status</label>
          <select name="status" class="form-select">
            <option value="">(Do not change status)</option>
            @foreach(['Available','Booked','Occupied','OutOfService'] as $s)
              <option value="{{ $s }}">{{ $s }}</option>
            @endforeach
          </select>
          <div class="form-text">Use <b>OutOfService</b> for the 9 rooms under maintenance.</div>
        </div>

        <div class="col-12 col-lg-3">
          <label class="form-label">Room numbers</label>
          <input name="room_numbers" class="form-control" placeholder="101,102,A1,B2" required>
          <div class="form-text">Comma-separated. Any format is allowed.</div>
        </div>

        <div class="col-12 col-lg-1 d-grid align-items-end">
          <label class="form-label d-none d-lg-block">&nbsp;</label>
          <button class="btn btn-success">
            <i class="bi bi-check2-circle me-1"></i> Save
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- Rooms Table --}}
  <div class="card overflow-hidden">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Room #</th>
            <th>Type</th>
            <th>Status</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>

        <tbody>
          @forelse($rooms as $r)
            <tr>
              <td class="fw-semibold">{{ $r->room_number }}</td>

              <td class="fw-semibold">
                @if($r->roomType)
                  {{ $r->roomType->name }}
                @else
                  <span class="badge bg-secondary-subtle text-dark border">TBD</span>
                @endif
              </td>

              <td>
                @php
                  $st = (string)$r->status;
                  $badge = match($st) {
                    'Available' => 'bg-success-subtle text-dark border',
                    'Booked' => 'bg-primary-subtle text-dark border',
                    'Occupied' => 'bg-danger-subtle text-dark border',
                    'OutOfService' => 'bg-warning-subtle text-dark border',
                    default => 'bg-secondary-subtle text-dark border',
                  };
                @endphp
                <span class="badge {{ $badge }}">{{ $st }}</span>
              </td>

              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary"
                   href="{{ route('reception.admin.rooms.edit', $r) }}">
                  Edit
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-muted py-4">No rooms found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
