@extends('layouts.public')
@section('title','Contact — Mach Hotel')

@section('content')
<section class="container py-5">
  <div class="row g-4">

    {{-- LEFT: Contact --}}
    <div class="col-lg-6">
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-3">
        <div>
          <h2 class="mb-1">Contact</h2>
          <p class="text-muted mb-0">For quick help, WhatsApp or call us anytime.</p>
        </div>
        <span class="badge badge-soft d-none d-md-inline-flex">
          <i class="bi bi-clock me-1"></i> Quick response
        </span>
      </div>

      {{-- ✅ IMPORTANT: no h-100 (prevents mobile crop) --}}
      <div class="search-card p-4 contact-card">
        <div class="d-grid gap-3">

          <div class="d-flex align-items-start gap-3">
            <span class="icon-pill flex-shrink-0">
              <i class="bi bi-geo-alt"></i>
            </span>
            <div class="min-w-0">
              <div class="fw-semibold">Location</div>
              <div class="text-muted">Dar es Salaam, Tanzania</div>
              <a class="small text-decoration-none" target="_blank"
                 href="https://www.google.com/maps?q=Mach+Hotel+Dar+es+Salaam">
                <i class="bi bi-map me-1"></i> Open in Google Maps
              </a>
            </div>
          </div>

          <div class="d-flex align-items-start gap-3">
            <span class="icon-pill flex-shrink-0">
              <i class="bi bi-telephone"></i>
            </span>
            <div>
              <div class="fw-semibold">Phone</div>
              <a class="text-decoration-none" href="tel:+255680082937">+255 680 082 937</a>
              <div class="text-muted small">Tap to call from mobile.</div>
            </div>
          </div>

          <div class="d-flex align-items-start gap-3">
            <span class="icon-pill flex-shrink-0">
              <i class="bi bi-envelope"></i>
            </span>
            <div class="min-w-0">
              <div class="fw-semibold">Email</div>
              <a class="text-decoration-none" href="mailto:info@machhotel.com">info@machhotel.com</a>
              <div class="text-muted small">Best for formal requests.</div>
            </div>
          </div>

          <hr class="my-1">

          <a class="btn btn-gold w-100 btn-lg" target="_blank"
             href="https://wa.me/255680082937?text={{ urlencode('Hello Mach Hotel, I need assistance with booking.') }}">
            <i class="bi bi-whatsapp me-1"></i> Chat on WhatsApp
          </a>

          {{-- ✅ Mobile-safe: stack buttons, never overflow --}}
          <div class="row g-2">
            <div class="col-12 col-md-6">
              <a class="btn btn-outline-light w-100" href="tel:+255680082937">
                <i class="bi bi-telephone-outbound me-1"></i> Call
              </a>
            </div>
            <div class="col-12 col-md-6">
              <a class="btn btn-outline-light w-100" href="mailto:info@machhotel.com">
                <i class="bi bi-send me-1"></i> Email
              </a>
            </div>
          </div>

          <div class="text-muted small">
            <i class="bi bi-shield-check me-1"></i> Your details are kept private.
          </div>
        </div>
      </div>
    </div>

    {{-- RIGHT: Map --}}
    <div class="col-lg-6">
      <div class="d-flex align-items-end justify-content-between flex-wrap gap-2 mb-3">
        <div>
          <h4 class="mb-1">Find Us</h4>
          <div class="text-muted">Tap the map to explore nearby landmarks.</div>
        </div>
        <a class="btn btn-outline-light btn-sm" target="_blank"
           href="https://www.google.com/maps?q=Mach+Hotel+Dar+es+Salaam">
          <i class="bi bi-box-arrow-up-right me-1"></i> Open full map
        </a>
      </div>

      <div class="map-shell">
        <div class="map-overlay">
          <div class="map-chip">
            <i class="bi bi-geo-alt me-1"></i> Mach Hotel
          </div>
        </div>

        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.8567438824957!2d39.08768977419072!3d-6.787281466376238!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x185c4500103f6a85%3A0x90e4d05694abf3a1!2sMach%20Hotel!5e0!3m2!1sen!2stz!4v1769011144919!5m2!1sen!2stz"
          allowfullscreen
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>

      <div class="text-muted small mt-2">
        <i class="bi bi-info-circle me-1"></i> If the map fails to load, use “Open full map”.
      </div>
    </div>

  </div>
</section>
@endsection
