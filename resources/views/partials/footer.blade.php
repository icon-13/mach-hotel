<footer class="footer-dark mt-5">
  <div class="container py-5">
    <div class="row g-4 align-items-start">
      {{-- Brand / About --}}
      <div class="col-lg-4">
        <div class="d-flex align-items-center gap-2 mb-2 footer-brand">
          <span class="brand-dot" aria-hidden="true"></span>
          <span class="footer-brand-name">Mach Hotel</span>
        </div>

        <p class="footer-muted mb-3">
          Experience modern and luxurious comfort. Book fast. Pay at the counter.
        </p>

        <div class="d-flex gap-2 flex-wrap align-items-center">
          <a class="icon-pill" href="tel:+255680082937" aria-label="Call Mach Hotel">
            <i class="bi bi-telephone-fill"></i>
          </a>

          <a class="icon-pill"
             href="https://www.instagram.com/machhotel?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
             target="_blank" rel="noopener"
             aria-label="Mach Hotel on Instagram">
            <i class="bi bi-instagram"></i>
          </a>

          <a class="icon-pill"
             href="https://wa.me/255680082937"
             target="_blank" rel="noopener"
             aria-label="Chat on WhatsApp">
            <i class="bi bi-whatsapp"></i>
          </a>

          <a class="btn btn-gold btn-sm px-3 ms-lg-2 footer-cta"
             href="{{ route('search') }}">
            Check Availability
          </a>
        </div>

        <div class="mt-3 small footer-muted">
          <i class="bi bi-clock me-1"></i> Reception support available daily
        </div>
      </div>

      {{-- Explore --}}
      <div class="col-6 col-lg-2">
        <div class="footer-title">Explore</div>
        <ul class="list-unstyled small mb-0 footer-list">
          <li><a class="footer-link" href="{{ route('rooms.index') }}">Rooms</a></li>
          <li><a class="footer-link" href="{{ route('search') }}">Availability</a></li>
          <li><a class="footer-link" href="{{ route('contact') }}">Contact</a></li>
        </ul>
      </div>

      {{-- Contact --}}
      <div class="col-6 col-lg-3">
        <div class="footer-title">Contact</div>
        <ul class="list-unstyled small mb-0 footer-list footer-muted">
          <li>
            <i class="bi bi-geo-alt me-2"></i>
            Mbezi Louis, Dar es Salaam, Tanzania
          </li>
          <li>
            <i class="bi bi-telephone me-2"></i>
            <a class="footer-link" href="tel:+255680082937">+255 680 082 937</a>
          </li>
          <li>
            <i class="bi bi-envelope me-2"></i>
            <a class="footer-link" href="mailto:info@machhotel.com">info@machhotel.com</a>
          </li>
        </ul>
      </div>

      {{-- Payment / Trust --}}
      <div class="col-lg-3">
        <div class="footer-title">Pay at Counter</div>

        <div class="glass-card p-3 footer-card">
          <div class="d-flex align-items-start gap-2">
            <i class="bi bi-shield-check fs-5 text-white"></i>
            <div class="small text-white">
              No online payments. Your booking is confirmed via WhatsApp, call, or email.
              <div class="footer-muted mt-2">
                <i class="bi bi-info-circle me-1"></i> Keep your booking code for quick check-in.
              </div>
            </div>
          </div>
        </div>

        <div class="mt-3">
          <a class="btn btn-outline-light btn-sm w-100 footer-help"
             href="{{ route('contact') }}">
            Need help? Contact us
          </a>
        </div>
      </div>
    </div>

    <hr class="footer-hr my-4">

    <div class="footer-bottom">
      <div class="footer-quote">“Home from Home”</div>

      <div class="footer-staff">
        <i class="bi bi-shield-lock"></i>
        <a class="footer-link"
           href="{{ route('reception.login') }}"
           aria-label="Staff Portal (for hotel staff only)">
          Staff Portal
        </a>
        <span class="footer-muted dot-sep">•</span>
        <span class="footer-muted">For staff only</span>
      </div>

      <div class="footer-copy">© {{ date('Y') }} Mach Hotel. All rights reserved.</div>
    </div>
  </div>
</footer>