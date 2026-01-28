@extends('layouts.reception')

@section('title','Rooms Board')

@section('content')
@php
  // Premium chip helper
  $chip = function(string $kind){
    return match($kind){
      'available' => 'badge text-bg-success',
      'booked'    => 'badge text-bg-primary',
      'occupied'  => 'badge text-bg-danger',
      'oos'       => 'badge text-bg-warning',
      'total'     => 'badge text-bg-secondary',
      default     => 'badge text-bg-secondary',
    };
  };

  $visualBookedRoomIdsByType = $visualBookedRoomIdsByType ?? [];
  $countsByType = $countsByType ?? [];
@endphp

<div class="container rx-container py-4">

  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-3">
    <div>
      <div class="text-muted small">Reception</div>
      <h2 class="mb-1">Rooms Board</h2>
      <div class="text-muted">Live view by date • booked holds reflect confirmed reservations</div>
    </div>

    <form class="d-flex gap-2 flex-wrap" method="GET">
      <input type="date" name="date" class="form-control" value="{{ $date ?? now()->toDateString() }}">
      <select name="status" class="form-select">
        <option value="">All</option>
        <option value="available" @selected(($status ?? '')==='available')>Available</option>
        <option value="booked" @selected(($status ?? '')==='booked')>Booked</option>
        <option value="occupied" @selected(($status ?? '')==='occupied')>Occupied</option>
        <option value="outofservice" @selected(($status ?? '')==='outofservice')>Maintenance</option>
      </select>
      <button class="btn btn-dark"><i class="bi bi-funnel me-1"></i>Apply</button>
    </form>
  </div>

  {{-- Room Types --}}
  <div class="row g-3">
    @foreach($roomTypes as $rt)
      @php
        $c = $countsByType[$rt->id] ?? ['available'=>0,'booked'=>0,'occupied'=>0,'oos'=>0,'total'=>0];
        $visualBookedIds = collect($visualBookedRoomIdsByType[$rt->id] ?? [])->map(fn($x)=>(int)$x);
      @endphp

      <div class="col-12">
        <div class="card shadow-sm" style="border:1px solid var(--border); background: var(--card);">
          <div class="card-body">

            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
              <div>
                <div class="d-flex align-items-center gap-2">
                  <h4 class="mb-0">{{ $rt->name }}</h4>
                  <span class="badge rounded-pill" style="background: rgba(255,215,128,.18); border:1px solid rgba(255,215,128,.25); color: var(--gold);">
                    Online quota: {{ $rt->online_quota }}
                  </span>
                </div>
                <div class="text-muted small mt-1">
                  Total: <b>{{ $c['total'] }}</b>
                  <span class="mx-1">•</span>
                  Avail: <b class="text-success">{{ $c['available'] }}</b>
                  <span class="mx-1">•</span>
                  Occ: <b class="text-danger">{{ $c['occupied'] }}</b>
                  <span class="mx-1">•</span>
                  Booked: <b class="text-primary">{{ $c['booked'] }}</b>
                  <span class="mx-1">•</span>
                  Maint: <b class="text-warning">{{ $c['oos'] }}</b>
                </div>
              </div>

              <div class="d-flex gap-2 flex-wrap">
                <span class="{{ $chip('available') }} rounded-pill px-3 py-2">
                  <i class="bi bi-check-circle me-1"></i> Avail {{ $c['available'] }}
                </span>
                <span class="{{ $chip('booked') }} rounded-pill px-3 py-2">
                  <i class="bi bi-bookmark-check me-1"></i> Booked {{ $c['booked'] }}
                </span>
                <span class="{{ $chip('occupied') }} rounded-pill px-3 py-2">
                  <i class="bi bi-person-fill-check me-1"></i> Occupied {{ $c['occupied'] }}
                </span>
                <span class="{{ $chip('oos') }} rounded-pill px-3 py-2">
                  <i class="bi bi-tools me-1"></i> Maint {{ $c['oos'] }}
                </span>
              </div>
            </div>

            <hr style="border-color: var(--border);">

            {{-- Room tiles --}}
            <div class="row g-2">
              @forelse($rt->physicalRooms as $room)
                @php
                  // Base DB status (after sync)
                  $dbStatus = $room->status;

                  // Overlay: if it's available but in visualBookedIds => show Booked
                  $effective = $dbStatus;
                  if ($dbStatus === 'Available' && $visualBookedIds->contains((int)$room->id)) {
                    $effective = 'Booked';
                  }

                  $badgeClass = match($effective){
                    'Available' => 'text-bg-success',
                    'Booked' => 'text-bg-primary',
                    'Occupied' => 'text-bg-danger',
                    'OutOfService' => 'text-bg-warning',
                    default => 'text-bg-secondary',
                  };

                  $pillIcon = match($effective){
                    'Available' => 'bi-check-circle',
                    'Booked' => 'bi-bookmark-check',
                    'Occupied' => 'bi-person-fill-check',
                    'OutOfService' => 'bi-tools',
                    default => 'bi-dot',
                  };
                @endphp

                <div class="col-6 col-md-3 col-lg-2">
                  <div class="p-2 rounded-4 h-100"
                       style="border:1px solid var(--border); background: rgba(0,0,0,.08);">
                    <div class="d-flex justify-content-between align-items-center">
                      <div class="fw-semibold">{{ $room->room_number }}</div>
                      <span class="badge {{ $badgeClass }} rounded-pill">
                        <i class="bi {{ $pillIcon }} me-1"></i>{{ strtoupper($effective) }}
                      </span>
                    </div>

                    @if($effective === 'Booked')
                      <div class="text-muted small mt-1">
                        Reserved (confirmed) • room not assigned yet
                      </div>
                    @elseif($effective === 'Occupied')
                      <div class="text-muted small mt-1">
                        Guest checked-in
                      </div>
                    @elseif($effective === 'OutOfService')
                      <div class="text-muted small mt-1">
                        Maintenance / blocked
                      </div>
                    @else
                      <div class="text-muted small mt-1">
                        Ready for assignment
                      </div>
                    @endif
                  </div>
                </div>
              @empty
                <div class="col-12">
                  <div class="text-muted">No rooms for this type.</div>
                </div>
              @endforelse
            </div>

          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- TBD block --}}
  <div class="mt-4">
    @php $tc = $tbdCounts ?? ['available'=>0,'oos'=>0,'total'=>0]; @endphp
    <div class="card shadow-sm" style="border:1px solid var(--border); background: var(--card);">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
          <div>
            <h4 class="mb-0">Unassigned (TBD)</h4>
            <div class="text-muted small mt-1">
              Total: <b>{{ $tc['total'] }}</b> • Avail: <b class="text-success">{{ $tc['available'] }}</b> • Maint: <b class="text-warning">{{ $tc['oos'] }}</b>
            </div>
          </div>

          <div class="d-flex gap-2 flex-wrap">
            <span class="badge text-bg-secondary rounded-pill px-3 py-2">
              <i class="bi bi-grid-3x3-gap me-1"></i> Total {{ $tc['total'] }}
            </span>
            <span class="badge text-bg-success rounded-pill px-3 py-2">
              <i class="bi bi-check-circle me-1"></i> Avail {{ $tc['available'] }}
            </span>
            <span class="badge text-bg-warning rounded-pill px-3 py-2">
              <i class="bi bi-tools me-1"></i> Maint {{ $tc['oos'] }}
            </span>
          </div>
        </div>

        <hr style="border-color: var(--border);">

        <div class="row g-2">
          @forelse($tbdRooms as $room)
            @php
              $effective = $room->status;
              $badgeClass = match($effective){
                'Available' => 'text-bg-success',
                'Booked' => 'text-bg-primary',
                'Occupied' => 'text-bg-danger',
                'OutOfService' => 'text-bg-warning',
                default => 'text-bg-secondary',
              };
            @endphp

            <div class="col-6 col-md-3 col-lg-2">
              <div class="p-2 rounded-4 h-100" style="border:1px solid var(--border); background: rgba(0,0,0,.08);">
                <div class="d-flex justify-content-between align-items-center">
                  <div class="fw-semibold">{{ $room->room_number }}</div>
                  <span class="badge {{ $badgeClass }} rounded-pill">
                    {{ strtoupper($effective) }}
                  </span>
                </div>
                <div class="text-muted small mt-1">Not assigned to any type</div>
              </div>
            </div>
          @empty
            <div class="col-12">
              <div class="text-muted">No TBD rooms.</div>
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
