@extends('layouts.reception')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Admin • Physical Rooms (21)</h4>
        <a href="{{ route('reception.bookings.index') }}" class="btn btn-outline-secondary btn-sm">Back to Bookings</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form class="row g-2 mb-3" method="GET">
        <div class="col-md-4">
            <select name="room_type_id" class="form-select">
                <option value="">All rooms</option>
                <option value="tbd" {{ $selectedType === 'tbd' ? 'selected' : '' }}>TBD (Unassigned)</option>
                @foreach($roomTypes as $t)
                    <option value="{{ $t->id }}" {{ (string)$selectedType === (string)$t->id ? 'selected' : '' }}>
                        {{ $t->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <div class="card mb-4">
        <div class="card-header fw-semibold">Bulk Assign (fast way to classify TBD rooms)</div>
        <div class="card-body">
            <form class="row g-2" method="POST" action="{{ route('reception.admin.rooms.bulkAssign') }}">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Assign to Room Type (or leave blank for TBD)</label>
                    <select name="room_type_id" class="form-select">
                        <option value="">TBD (Unassigned)</option>
                        @foreach($roomTypes as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Room numbers (comma-separated)</label>
                    <input name="room_numbers" class="form-control" placeholder="101,102,103" required>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button class="btn btn-success w-100">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-wrap">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th>Room #</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Online</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rooms as $r)
                        <tr>
                            <td class="fw-semibold">{{ $r->room_number }}</td>
                            <td>
                                {{ $r->roomType?->name ?? 'TBD' }}
                            </td>
                            <td>{{ $r->status }}</td>
                            <td>
                                @if($r->is_bookable_online)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary"
                                   href="{{ route('reception.admin.rooms.edit', $r) }}">
                                   Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    @if($rooms->isEmpty())
                        <tr><td colspan="5" class="text-center text-muted py-4">No rooms found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
