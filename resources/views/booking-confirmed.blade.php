@extends('layouts.public')
@section('title','Booking Confirmed — Mach Hotel')

@section('content')
<section class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-9">

      <div class="search-card p-4 p-md-5">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
          <div>
            <h2 class="mb-1">Booking Confirmed ✅</h2>
            <div class="text-muted">Your booking is confirmed instantly. Pay at the counter on arrival.</div>
          </div>

          <div class="text-end">
            <div class="text-muted small">Booking Code</div>
            <div class="h4 mb-0" style="letter-spacing:.12em">{{ $booking->code }}</div>
          </div>
        </div>

        <hr class="my-4">

        <div class="row g-3">
          <div class="col-md-6">
            <div class="text-muted small">Guest</div>
            <div class="fw-semibold">{{ $booking->guest->full_name }}</div>
            <div class="text-muted small">{{ $booking->guest->phone }}</div>
            @if($booking->guest->email)
              <div class="text-muted small">{{ $booking->guest->email }}</div>
            @endif
          </div>

          <div class="col-md-6">
            <div class="text-muted small">Room Type</div>
            <div class="fw-semibold">{{ $booking->roomType?->name ?? '—' }}</div>
            @if($booking->roomType)
              <div class="text-muted small">
                TZS {{ number_format($booking->roomType->price_tzs) }} / night
                <span class="text-muted">• USD {{ number_format($booking->roomType->price_usd) }}</span>
              </div>
            @endif
          </div>

          <div class="col-md-6">
            <div class="text-muted small">Dates</div>
            <div class="fw-semibold">
              {{ $booking->check_in->format('Y-m-d') }} → {{ $booking->check_out->format('Y-m-d') }}
            </div>
            <div class="text-muted small">Check-in from 14:00 • Check-out before 11:00</div>
          </div>

          <div class="col-md-6">
            <div class="text-muted small">Total</div>
            <div class="fw-semibold">TZS {{ number_format($booking->total_amount) }}</div>
            <div class="text-muted small">Pay at counter</div>
          </div>
        </div>

        @if($booking->special_requests)
          <hr class="my-4">
          <div class="text-muted small mb-1">Special Requests</div>
          <div class="fw-semibold">{{ $booking->special_requests }}</div>
        @endif

        <hr class="my-4">

        <div class="d-flex flex-wrap gap-2">
          <a class="btn btn-dark" href="{{ $whatsappUrl }}" target="_blank" rel="noopener">
            WhatsApp the hotel
          </a>

          <a class="btn btn-gold" href="{{ route('booking.pdf', $booking->code) }}">
            Download Receipt (PDF)
          </a>

          <a class="btn btn-outline-secondary" href="{{ route('search') }}">
            Make another booking
          </a>
        </div>

        <div class="text-muted small mt-3">
          Note: Online booking uses limited quota (not room numbers). Final room assignment happens at reception.
        </div>
      </div>

    </div>
  </div>
</section>
@endsection
