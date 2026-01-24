@extends('layouts.public')
@section('title','Book — Mach Hotel')

@section('content')
<section class="container py-5">
  <h2 class="mb-1">Complete Your Booking</h2>
  <div class="text-muted mb-4">Auto-confirmed booking. Pay at the counter on arrival.</div>

  <div class="row g-4">
    <div class="col-lg-7">
      <div class="search-card p-3 p-md-4">
        @if($errors->any())
          <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Please fix the errors below:</div>
            <ul class="mb-0">
              @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="post" action="{{ route('book.store', $roomType->id) }}">
          @csrf

          <input type="hidden" name="check_in" value="{{ $check_in }}">
          <input type="hidden" name="check_out" value="{{ $check_out }}">
          <input type="hidden" name="guests" value="{{ $guests }}">

          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label small">Full name</label>
              <input class="form-control" name="full_name" value="{{ old('full_name') }}" required>
            </div>

            <div class="col-md-6">
              <label class="form-label small">Phone (WhatsApp)</label>
              <input class="form-control" name="phone" placeholder="+255..." value="{{ old('phone') }}" required>
            </div>

            <div class="col-12">
              <label class="form-label small">Email (optional)</label>
              <input class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="optional@email.com">
            </div>

            <div class="col-12">
              <label class="form-label small">Special requests (optional)</label>
              <textarea class="form-control" name="special_requests" rows="3">{{ old('special_requests') }}</textarea>
            </div>

            <div class="col-12 pt-1">
              <button class="btn btn-gold btn-lg w-100" type="submit">
                Confirm Booking
              </button>
              <div class="text-muted small mt-2">
                You’ll receive a booking code immediately. Payment is done at reception.
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="glass-card p-4">
        <div class="text-white-75 small">Your selection</div>
        <div class="h4 text-white mb-1">{{ $roomType->name }}</div>
        <div class="text-white-75">
          TZS {{ number_format($roomType->price_tzs) }} • per night
          <span class="text-white-50">• USD {{ number_format($roomType->price_usd) }}</span>
        </div>

        <hr class="border-secondary my-3">

        @php
          $nights = \Carbon\Carbon::parse($check_in)->diffInDays(\Carbon\Carbon::parse($check_out));
          $total = $nights * (int)$roomType->price_tzs;
        @endphp

        <div class="d-flex justify-content-between text-white-75 small">
          <span>Check-in</span>
          <span class="text-white fw-semibold">{{ $check_in }}</span>
        </div>

        <div class="d-flex justify-content-between text-white-75 small mt-2">
          <span>Check-out</span>
          <span class="text-white fw-semibold">{{ $check_out }}</span>
        </div>

        <div class="d-flex justify-content-between text-white-75 small mt-2">
          <span>Nights</span>
          <span class="text-white fw-semibold">{{ $nights }}</span>
        </div>

        <div class="d-flex justify-content-between text-white-75 small mt-2">
          <span>Total (TZS)</span>
          <span class="text-white fw-semibold">TZS {{ number_format($total) }}</span>
        </div>

        <div class="text-white-75 small mt-3">
          <i class="bi bi-whatsapp me-1"></i> Confirmation via WhatsApp (one tap)
        </div>

        <div class="text-white-50 small mt-2">
          Note: Room numbers are assigned at reception. Online booking uses limited quota.
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
