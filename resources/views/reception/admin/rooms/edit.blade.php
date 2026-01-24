@extends('layouts.reception')

@section('content')
<div class="container py-4" style="max-width: 720px;">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Edit Room • {{ $room->room_number }}</h4>
        <a href="{{ route('reception.admin.rooms.index') }}"
           class="btn btn-outline-secondary btn-sm">
            Back
        </a>
    </div>

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

    <form method="POST"
          action="{{ route('reception.admin.rooms.update', $room) }}"
          class="card">
        @csrf
        @method('PUT')

        <div class="card-body">

            {{-- Room number --}}
            <div class="mb-3">
                <label class="form-label">Room Number</label>
                <input
                    name="room_number"
                    class="form-control"
                    value="{{ old('room_number', $room->room_number) }}"
                    placeholder="e.g. 101, A3, G-12"
                    required>
            </div>

            {{-- Room type assignment --}}
            <div class="mb-3">
                <label class="form-label">Room Type</label>
                <select name="room_type_id" class="form-select">
                    <option value="">TBD (Unassigned)</option>
                    @foreach($roomTypes as $t)
                        <option value="{{ $t->id }}"
                          @selected((string)old('room_type_id', $room->room_type_id) === (string)$t->id)>
                          {{ $t->name }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text">
                    Unassigned rooms do not appear online and cannot be auto-booked.
                </div>
            </div>

            {{-- Status --}}
            <div class="mb-3">
                <label class="form-label">Room Status</label>
                <select name="status" class="form-select" required>
                    @foreach(['Available','Booked','Occupied','OutOfService'] as $s)
                        <option value="{{ $s }}"
                          @selected(old('status', $room->status) === $s)>
                          {{ $s }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Notes --}}
            <div class="mb-0">
                <label class="form-label">Internal Notes</label>
                <textarea
                    name="notes"
                    class="form-control"
                    rows="3"
                    placeholder="Maintenance notes, issues, etc.">{{ old('notes', $room->notes) }}</textarea>
            </div>

        </div>

        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('reception.admin.rooms.index') }}"
               class="btn btn-outline-secondary">
                Cancel
            </a>
            <button class="btn btn-primary">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
