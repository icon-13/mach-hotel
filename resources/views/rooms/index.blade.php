@extends('layouts.public')
@section('title','Rooms — Mach Hotel')

@section('content')
<section class="container py-5">
  <div class="d-flex justify-content-between align-items-end mb-3">
    <div>
      <h2 class="mb-1">Rooms</h2>
      <div class="text-muted">Choose a room type. Availability is based on online quota.</div>
    </div>
    <a class="btn btn-outline-dark" href="{{ route('search') }}">Check availability</a>
  </div>

  <div class="row g-3">
    @forelse($roomTypes as $type)
      @php
        $tag = $type->slug === 'ddr' ? 'Popular' : 'Best Value';
      @endphp

      <div class="col-md-6">
        <a href="{{ route('rooms.show', $type->slug) }}" class="room-card text-decoration-none">
          <div class="room-thumb">
            <span class="room-badge">{{ $tag }}</span>
          </div>
          <div class="p-3">
            <div class="d-flex justify-content-between align-items-start gap-2">
              <div class="text-muted  fw-semibold ">{{ $type->name }}</div>
              <div class="text-muted  fw-semibold">TZS {{ number_format($type->price_tzs) }}</div>
            </div>

            <div class="text-muted small mt-1">
              USD {{ number_format($type->price_usd) }} • per night • Capacity {{ $type->capacity }} • Online spots {{ $type->online_quota }}
            </div>

            <div class="mt-3">
              <span class="btn btn-dark w-100">View details</span>
            </div>
          </div>
        </a>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-warning mb-0">No room types found. Seed DDR & DSR first.</div>
      </div>
    @endforelse
  </div>
</section>
@endsection
