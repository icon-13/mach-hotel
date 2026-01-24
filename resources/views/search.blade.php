@extends('layouts.public')
@section('title','Check Availability — Mach Hotel')

@section('content')
<section class="container py-5">
  <h2 class="mb-1">Check Availability</h2>
  <div class="text-muted mb-4">Find available rooms instantly. Bookings are auto-confirmed.</div>

  <div class="search-card p-3 p-md-4">
    <form class="row g-2" method="get" action="{{ route('search') }}">
      <div class="col-md-3 col-6">
        <label class="form-label small">Check-in</label>
<input class="form-control" type="date" name="check_in" min="{{ now()->toDateString() }}" value="{{ request('check_in') }}" required>
        
      </div>

      <div class="col-md-3 col-6">
        <label class="form-label small">Check-out</label>
<input class="form-control" type="date" name="check_out" min="{{ now()->toDateString() }}" value="{{ request('check_out') }}" required>
      </div>

      <div class="col-md-3">
        <label class="form-label small">Guests</label>
        <select class="form-select" name="guests">
          @foreach([1,2] as $g)
            <option value="{{ $g }}" @selected((int)request('guests',1) === $g)>
              {{ $g }} {{ $g === 1 ? 'Guest' : 'Guests' }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-dark btn-lg w-100">Search</button>
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
              <div class="room-card">
                <div class="room-thumb"></div>
                <div class="p-3">
                  <div class="fw-semibold">{{ $type->name }}</div>
                  <div class="text-muted small">
                    TZS {{ number_format($type->price_tzs) }} • USD {{ number_format($type->price_usd) }} • per night • Capacity {{ $type->capacity }}
                  </div>

                  <a class="btn btn-gold w-100 mt-3"
                     href="{{ route('book', [
                       'roomType' => $type->id,
                       'check_in' => request('check_in'),
                       'check_out' => request('check_out'),
                       'guests' => request('guests',1)
                     ]) }}">
                    Book now
                  </a>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="alert alert-warning">No rooms available for selected dates.</div>
      @endif
    </div>
  @else
    <div class="mt-4">
      <div class="alert alert-secondary">
        Pick your dates above, then tap <strong>Search</strong> to see available rooms.
      </div>
    </div>
  @endif

</section>
@endsection
