@extends('layouts.public')
@section('title', $roomType->name.' — Mach Hotel')

@section('content')
<section class="container py-5">
  <div class="row g-4 align-items-start">
    <div class="col-lg-7">
      <div class="room-card">
        <div class="room-thumb"></div>
        <div class="p-4">
          <h2 class="mb-1">{{ $roomType->name }}</h2>
          <div class="text-muted">
            Capacity {{ $roomType->capacity }} • Online spots {{ $roomType->online_quota }} • Pay at counter
          </div>

          <div class="mt-3">
            <div class="h4 mb-0">TZS {{ number_format($roomType->price_tzs) }}</div>
            <div class="text-muted small">USD {{ number_format($roomType->price_usd) }} • per night</div>
          </div>

          @if(!empty($roomType->amenities))
            <hr class="my-4">
            <div class="fw-semibold mb-2">Amenities</div>
            <div class="d-flex flex-wrap gap-2">
              @foreach($roomType->amenities as $a)
                <span class="badge text-bg-light border">{{ $a }}</span>
              @endforeach
            </div>
          @endif

          @if($roomType->description)
            <hr class="my-4">
            <div class="fw-semibold mb-2">About this room</div>
            <div class="text-muted">{{ $roomType->description }}</div>
          @endif

          <hr class="my-4">
          <a href="{{ route('search') }}" class="btn btn-gold btn-lg w-100">
            Check availability
          </a>
          <div class="text-muted small mt-2">
            Note: Online bookings use limited quota. Final room assignment happens at reception.
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="search-card p-3 p-md-4">
        <h5 class="mb-1">Book this room type</h5>
        <div class="text-muted small mb-3">Pick dates, then we’ll show availability.</div>

        <form action="{{ route('search') }}" method="get" class="row g-2">
          <input type="hidden" name="guests" value="{{ $roomType->capacity }}">

          <div class="col-6">
            <label class="form-label small mb-1">Check-in</label>
            <input type="date" name="check_in" class="form-control" min="{{ now()->toDateString() }}" required>
          </div>

          <div class="col-6">
            <label class="form-label small mb-1">Check-out</label>
            <input type="date" name="check_out" class="form-control" min="{{ now()->toDateString() }}" required>
          </div>

          <div class="col-12 pt-1">
            <button class="btn btn-dark w-100 btn-lg">Search Availability</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</section>
@endsection
