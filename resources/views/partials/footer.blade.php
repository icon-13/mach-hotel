<footer class="footer-dark mt-5">
  <div class="container py-5">
    <div class="row g-4">
      {{-- Brand / About --}}
      <div class="col-lg-4">
        <div class="d-flex align-items-center gap-2 mb-2">
          {{-- ✅ Brand mark (dot works even if logo missing elsewhere) --}}
          <span class="brand-dot" aria-hidden="true"></span>
          <span class="fw-semibold text-white">Mach Hotel</span>
        </div>

        <p class="text-white-50 mb-3">
          Experience modern and luxurious comfort. Book fast. Pay at the counter.
        </p>

        <div class="d-flex gap-2 flex-wrap">
          {{-- ✅ Use E.164 formatting where possible --}}
          <a class="icon-pill" href="tel:+255680082937" aria-label="Call Mach Hotel">
            <i class="bi bi-telephone-fill"></i>
          </a>

          {{-- ✅ External links: security best practice --}}
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

          {{-- ✅ Quick CTA (conversion) --}}
          <a class="btn btn-gold btn-sm px-3 ms-lg-2"
             href="{{ route('search') }}"
             style="border-radius:12px; height:40px; display:inline-flex; align-items:center;">
            Check Availability
          </a>
        </div>

        <div class="mt-3 small text-white-50">
          <i class="bi bi-clock me-1"></i> Reception support available daily
        </div>
      </div>

      {{-- Explore --}}
      <div class="col-6 col-lg-2">
        <div class="text-white fw-semibold mb-2">Explore</div>
        <ul class="list-unstyled small mb-0">
          <li class="mb-2"><a class="footer-link" href="{{ route('rooms.index') }}">Rooms</a></li>
          <li class="mb-2"><a class="footer-link" href="{{ route('search') }}">Availability</a></li>
          <li class="mb-2"><a class="footer-link" href="{{ route('contact') }}">Contact</a></li>
        </ul>
      </div>

      {{-- Contact --}}
      <div class="col-6 col-lg-3">
        <div class="text-white fw-semibold mb-2">Contact</div>
        <ul class="list-unstyled small text-white-50 mb-0">
          <li class="mb-2">
            <i class="bi bi-geo-alt me-2"></i>
            Mbezi Louis, Dar es Salaam, Tanzania
          </li>
          <li class="mb-2">
            <i class="bi bi-telephone me-2"></i>
            <a class="footer-link" href="tel:+255680082937">+255 680 082 937</a>
          </li>
          <li class="mb-2">
            <i class="bi bi-envelope me-2"></i>
            <a class="footer-link" href="mailto:info@machhotel.com">info@machhotel.com</a>
          </li>
        </ul>
      </div>

      {{-- Payment / Trust --}}
      <div class="col-lg-3">
        <div class="text-white fw-semibold mb-2">Pay at Counter</div>

        <div class="glass-card p-3">
          <div class="d-flex align-items-start gap-2 text-white">
            <i class="bi bi-shield-check fs-5"></i>
            <div class="small">
              No online payments. Your booking is confirmed via WhatsApp, call, or email.
              <div class="text-white-50 mt-2">
                <i class="bi bi-info-circle me-1"></i> Keep your booking code for quick check-in.
              </div>
            </div>
          </div>
        </div>

        {{-- Optional quick support link --}}
        <div class="mt-3">
          <a class="btn btn-outline-light btn-sm w-100"
             href="{{ route('contact') }}"
             style="border-radius:12px;">
            Need help? Contact us
          </a>
        </div>
      </div>
    </div>

    <hr class="border-secondary my-4">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center text-white-50 small gap-2">
      <div class="mt-2 mt-md-0">“Home from Home”</div>

      {{-- ✅ Staff Portal: subtle, non-confusing for guests --}}
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-shield-lock"></i>
        <a class="footer-link"
           href="{{ route('reception.login') }}"
           aria-label="Staff Portal (for hotel staff only)"
           style="text-decoration:none;">
          Staff Portal
        </a>
        <span class="text-white-50" style="opacity:.7;">• For staff only</span>
      </div>

      <div>© {{ date('Y') }} Mach Hotel. All rights reserved.</div>
    </div>
  </div>
</footer>
