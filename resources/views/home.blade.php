@extends('layouts.public')

@section('title', 'Mach Hotel — Modern Luxury in Dar es Salaam')

@section('content')

<header class="hero-lux">
  <div class="container py-5">
    <div class="row align-items-center g-4">

      <div class="col-lg-7">
        <span class="badge badge-soft mb-3">
          <i class="bi bi-star-fill me-1"></i> 3-Star Comfort • Pay at Counter
        </span>

        <h1 class="display-5 fw-bold text-white mb-3">
          Modern Luxury, <span class="text-gold">Made Simple</span>
        </h1>

        <p class="lead text-white-75 mb-4">
          Book in seconds. Get confirmation. Enjoy a smooth stay at Mach Hotel in Dar es Salaam.
          Your “Home from Home”.
        </p>

        <div class="d-flex gap-2 flex-wrap">
          <a href="{{ route('search') }}" class="btn btn-gold btn-lg">
            Check Availability
          </a>
          <a href="{{ route('rooms.index') }}" class="btn btn-outline-light btn-lg">
            View Rooms
          </a>
        </div>

        <div class="mt-4 d-flex gap-3 flex-wrap text-white-75 small">
          <div class="d-flex align-items-center gap-2"><i class="bi bi-wifi"></i> Free Wi-Fi</div>
          <div class="d-flex align-items-center gap-2"><i class="bi bi-cup-hot"></i> Breakfast Options</div>
          <div class="d-flex align-items-center gap-2"><i class="bi bi-car-front"></i> Parking</div>
          <div class="d-flex align-items-center gap-2"><i class="bi bi-cup-straw"></i> Lounge & Bar</div>
          <div class="d-flex align-items-center gap-2"><i class="bi bi-easel"></i> Conference Hall</div>
          <div class="d-flex align-items-center gap-2"><i class="bi bi-egg-fried"></i> Restaurant</div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="search-card p-3 p-md-4">
          <h5 class="mb-1 fw-bold">Find a Room</h5>
          <p class="text-muted small mb-3">Search available room types instantly.</p>

          <form action="{{ route('search') }}" method="get" class="row g-2" id="heroSearchForm">

            {{-- Check-in --}}
            <div class="col-6">
              <label class="form-label small mb-1">Check-in</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                <input
                  type="date"
                  id="heroCheckIn"
                  name="check_in"
                  class="form-control"
                  min="{{ now('Africa/Dar_es_Salaam')->toDateString() }}"
                  value="{{ request('check_in') }}"
                  required
                >
              </div>
            </div>

            {{-- Check-out --}}
            <div class="col-6">
              <label class="form-label small mb-1">Check-out</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar2-check"></i></span>
                <input
                  type="date"
                  id="heroCheckOut"
                  name="check_out"
                  class="form-control"
                  value="{{ request('check_out') }}"
                  required
                >
              </div>
              <div class="form-text">
                Minimum stay is <span class="fw-semibold">1 night</span>.
              </div>
            </div>

            {{-- Guests --}}
            <div class="col-12">
              <label class="form-label small mb-1">Guests</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-people"></i></span>
                <select name="guests" class="form-select">
                  @foreach([1,2] as $g)
                    <option value="{{ $g }}" @selected((int)request('guests', 1) === $g)>
                      {{ $g }} {{ $g === 1 ? 'Guest' : 'Guests' }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            {{-- CTA --}}
            <div class="col-12 pt-1">
              <button class="btn btn-dark w-100 btn-lg">
                <i class="bi bi-search me-1"></i> Search Availability
              </button>

              <div class="text-muted small mt-2">
                <i class="bi bi-whatsapp me-1"></i> Confirmation via WhatsApp • Email optional
              </div>
            </div>

          </form>

        </div>
      </div>

    </div>
  </div>

  <div class="hero-fade"></div>
</header>

{{-- ✅ VALUE PROPS --}}
<section class="container py-5">
  <div class="row g-3">
    <div class="col-md-4">
      <div class="feature-card p-4 h-100">
        <i class="bi bi-lightning-charge fs-3 text-gold"></i>
        <div class="mt-2 fw-semibold">Fast Booking</div>
        <div class="text-muted small">Simple flow designed for mobile users.</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card p-4 h-100">
        <i class="bi bi-whatsapp fs-3 text-gold"></i>
        <div class="mt-2 fw-semibold">Instant Confirmation</div>
        <div class="text-muted small">WhatsApp message + booking code.</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-card p-4 h-100">
        <i class="bi bi-shield-check fs-3 text-gold"></i>
        <div class="mt-2 fw-semibold">Secure & Reliable</div>
        <div class="text-muted small">Clean Interface + secure backend workflow.</div>
      </div>
    </div>
  </div>
</section>

{{-- ✅ AMENITIES --}}
<section class="container pb-5">
  <div class="d-flex justify-content-between align-items-end mb-3">
    <div>
      <h3 class="mb-1">Amenities</h3>
      <div class="text-muted">Everything you need for a smooth and comfortable stay.</div>
    </div>
  </div>

  <div class="row g-3">
    @foreach([
      ['bi-wifi','Free Wi-Fi','Stay connected throughout the hotel.'],
      ['bi-cup-hot','Breakfast Options','Start your day the right way.'],
      ['bi-car-front','Secure Parking','Easy and safe parking on site.'],
      ['bi-snow','Air Conditioning','Cool, quiet, and comfortable rooms.'],
      ['bi-tv','Flat-Screen TV','Relax with your favorite channels.'],
      ['bi-shield-check','24/7 Security','Peace of mind, day and night.'],
    ] as $a)
      <div class="col-6 col-md-4">
        <div class="feature-card p-3 h-100">
          <div class="d-flex align-items-center gap-2">
            <i class="bi {{ $a[0] }} fs-4 text-gold"></i>
            <div class="fw-semibold">{{ $a[1] }}</div>
          </div>
          <div class="text-muted small mt-1">{{ $a[2] }}</div>
        </div>
      </div>
    @endforeach
  </div>
</section>

{{-- ✅ GALLERY --}}
@php
  $gallery = [
    ['src' => asset('gallery/1.webp'), 'alt' => 'Mach Hotel front view'],
    ['src' => asset('gallery/2.webp'), 'alt' => 'Mach Hotel suites'],
    ['src' => asset('gallery/3.webp'), 'alt' => 'Mach Hotel bed'],
    ['src' => asset('gallery/4.webp'), 'alt' => 'Mach Hotel details'],
    ['src' => asset('gallery/5.webp'), 'alt' => 'Mach Hotel exterior'],
    ['src' => asset('gallery/6.webp'), 'alt' => 'Mach Hotel cafeteria'],
  ];
@endphp

<section class="container pb-5">
  <div class="d-flex justify-content-between align-items-end mb-3">
    <div>
      <h3 class="mb-1">Gallery</h3>
      <div class="text-muted">Tap any photo to view full screen.</div>
    </div>
  </div>

  <div class="row g-3">
    @foreach($gallery as $idx => $img)
      <div class="col-6 col-md-4">
        <div
          class="gallery-tile"
          role="button"
          data-bs-toggle="modal"
          data-bs-target="#galleryModal"
          data-index="{{ $idx }}"
          aria-label="Open gallery image {{ $idx + 1 }}"
        >
          <img
            src="{{ $img['src'] }}"
            alt="{{ $img['alt'] }}"
            loading="lazy"
            decoding="async"
            width="800"
            height="600"
            style="width:100%; height:160px; object-fit:cover;"
          >
        </div>
      </div>
    @endforeach
  </div>
</section>

{{-- ✅ ROOM TYPES --}}
<section class="container pb-5">
  <div class="d-flex justify-content-between align-items-end mb-3">
    <div>
      <h3 class="mb-1">Room Types</h3>
      <div class="text-muted">
        Online booking uses limited quota • Final assignment at reception • Contact us for assistance.
      </div>
    </div>
    <a class="btn btn-outline-dark d-none d-md-inline-flex" href="{{ route('search') }}">See availability</a>
  </div>

  <div class="row g-3">
    @forelse(($roomTypes ?? collect()) as $type)
      @php
        $slug = strtolower($type->slug ?? '');
        $tag = str_contains($slug, 'ddr') ? 'Popular' : 'Best Value';

        $img = str_contains($slug, 'ddr')
          ? asset('images/rooms/rooms.webp')
          : (str_contains($slug, 'dsr')
              ? asset('images/rooms/rooms.webp')
              : asset('images/rooms/rooms.webp'));
      @endphp

      <div class="col-md-6">
        <a href="{{ route('search') }}" class="room-card text-decoration-none" aria-label="View availability for {{ $type->name }}">
          <div class="room-thumb" style="--room-thumb: url('{{ $img }}');">
            <span class="room-badge">{{ $tag }}</span>
          </div>

          <div class="p-3">
            <div class="d-flex justify-content-between align-items-start gap-2">
              <div class="fw-semibold">{{ $type->name }}</div>
              <div class="fw-semibold">TZS {{ number_format($type->price_tzs) }}</div>
            </div>

            <div class="text-muted small mt-1">
              USD {{ number_format($type->price_usd) }} • per night • Online spots: {{ $type->online_quota }} • Pay at counter
            </div>

            <div class="mt-3">
              <span class="btn btn-dark w-100">Check availability</span>
            </div>
          </div>
        </a>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-warning mb-0">
          No room types found yet. Run the RoomTypeSeeder to load DDR and DSR.
        </div>
      </div>
    @endforelse
  </div>

  <div class="d-md-none mt-3">
    <a class="btn btn-outline-dark w-100" href="{{ route('search') }}">See availability</a>
  </div>
</section>

{{-- ✅ GALLERY MODAL --}}
<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content" style="background: transparent; border:0;">
      <div class="modal-body p-0">
        <div class="position-relative">

          <button
            type="button"
            class="btn btn-dark position-absolute top-0 end-0 m-3 gallery-close-btn"
            data-bs-dismiss="modal"
            aria-label="Close"
          >
            <i class="bi bi-x-lg"></i>
          </button>

          <div id="galleryCarousel" class="carousel slide" data-bs-ride="false">
            <div class="carousel-inner">
              @foreach($gallery as $idx => $img)
                <div class="carousel-item @if($idx===0) active @endif">
                  <img class="gallery-modal-img" src="{{ $img['src'] }}" alt="{{ $img['alt'] }}">
                </div>
              @endforeach
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
/**
 * ✅ Premium date UX (same as Search page):
 * - checkout min = checkin + 1 day
 * - DO NOT auto-fill checkout
 * - If checkout becomes invalid, clear it + show invalid style
 */
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('heroSearchForm');
  if (!form) return;

  const inEl  = document.getElementById('heroCheckIn');
  const outEl = document.getElementById('heroCheckOut');
  if (!inEl || !outEl) return;

  const addDays = (dateStr, days) => {
    const d = new Date(dateStr + 'T00:00:00');
    d.setDate(d.getDate() + days);
    return d.toISOString().slice(0, 10);
  };

  const setInvalid = (on) => {
    outEl.classList.toggle('is-invalid', !!on);
  };

  const sync = () => {
    if (!inEl.value) {
      outEl.min = '';
      setInvalid(false);
      return;
    }

    const minOut = addDays(inEl.value, 1);
    outEl.min = minOut;

    // don’t auto-fill, only clear invalid values
    if (outEl.value && outEl.value < minOut) {
      outEl.value = '';
      setInvalid(true);
    } else {
      setInvalid(false);
    }
  };

  // handle prefilled values (back button)
  sync();

  inEl.addEventListener('change', sync);
  outEl.addEventListener('change', sync);

  // gentle guard: if checkout focused before checkin
  outEl.addEventListener('focus', () => {
    if (!inEl.value) inEl.focus();
  });

  // ✅ enforce also on submit (no sneaky same-day in URL)
  form.addEventListener('submit', (e) => {
    sync();
    if (!inEl.value || !outEl.value) {
      e.preventDefault();
      if (!inEl.value) inEl.focus();
      else outEl.focus();
    }
  });
});

// ✅ Gallery: open modal at clicked image index
(function(){
  const carouselEl = document.getElementById('galleryCarousel');
  if (!carouselEl || typeof bootstrap === 'undefined') return;

  const carousel = new bootstrap.Carousel(carouselEl, { interval: false, ride: false });

  document.querySelectorAll('[data-bs-target="#galleryModal"][data-index]').forEach(tile => {
    tile.addEventListener('click', () => {
      const idx = parseInt(tile.getAttribute('data-index') || '0', 10);
      carousel.to(idx);
    });
  });
})();

// ✅ Gallery close button: force-close fallback
(function(){
  const modalEl = document.getElementById('galleryModal');
  if (!modalEl || typeof bootstrap === 'undefined') return;

  modalEl.addEventListener('click', (e) => {
    const btn = e.target.closest('.gallery-close-btn');
    if (!btn) return;

    const inst = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    inst.hide();
  });
})();
</script>
@endpush
