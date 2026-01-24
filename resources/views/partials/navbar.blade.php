<nav class="navbar navbar-expand-lg nav-glass sticky-top">
  <div class="container">

    {{-- Brand --}}
    <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">

      {{-- ✅ Brand dot fallback (hidden by default; shown only if logo fails) --}}
      <span class="brand-dot" id="brandDot" style="display:none;"></span>

      <img
        src="{{ asset('brand/logo-light.png') }}"
        data-logo-light="{{ asset('brand/logo-light.png') }}"
        data-logo-dark="{{ asset('brand/logo-dark.png') }}"
        alt="Mach Hotel"
        class="brand-logo"
        id="brandLogo"
      >

      <span class="fw-semibold text-white ms-1">Mach Hotel</span>
    </a>

    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#machNav"
      aria-controls="machNav"
      aria-expanded="false"
      aria-label="Toggle navigation"
      style="border-radius:12px; padding:.55rem .7rem;"
    >
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="machNav">
      <ul class="navbar-nav ms-lg-4 me-auto mb-2 mb-lg-0 gap-lg-1">

        <li class="nav-item">
          <a class="nav-link nav-pill @if(request()->routeIs('home')) active @endif"
             href="{{ route('home') }}">
            Home
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link nav-pill @if(request()->routeIs('rooms.*')) active @endif"
             href="{{ route('rooms.index') }}">
            Rooms
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link nav-pill @if(request()->routeIs('contact')) active @endif"
             href="{{ route('contact') }}">
            Contact
          </a>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0">
        <button
          type="button"
          class="btn btn-sm btn-outline-light px-3"
          id="themeToggle"
          aria-label="Toggle theme"
          style="border-radius:12px; height:38px;"
        >
          <i class="bi bi-moon-stars"></i>
        </button>

        <a
          href="{{ route('search') }}"
          class="btn btn-gold btn-sm px-3"
          style="border-radius:12px; height:38px; display:inline-flex; align-items:center;"
        >
          Check Availability
        </a>
      </div>
    </div>
  </div>
</nav>

@push('styles')
<style>
  /* Premium active state (uses your theme tokens) */
  .nav-pill{
    border-radius: 999px;
    padding: .55rem .85rem !important;
    font-weight: 600;
    transition: background .15s ease, color .15s ease, border-color .15s ease;
  }

  html[data-theme="dark"] .nav-pill{
    color: rgba(255,255,255,.78) !important;
  }
  html[data-theme="dark"] .nav-pill:hover{
    background: rgba(255,255,255,.08);
    color: rgba(255,255,255,.92) !important;
  }
  html[data-theme="dark"] .nav-pill.active{
    background: rgba(255,255,255,.12);
    color: rgba(255,255,255,.95) !important;
    border: 1px solid rgba(255,255,255,.14);
  }

  html[data-theme="light"] .nav-pill{
    color: rgba(13,18,26,.74) !important;
  }
  html[data-theme="light"] .nav-pill:hover{
    background: rgba(13,18,26,.06);
    color: rgba(13,18,26,.92) !important;
  }
  html[data-theme="light"] .nav-pill.active{
    background: rgba(13,18,26,.08);
    color: rgba(13,18,26,.92) !important;
    border: 1px solid rgba(13,18,26,.12);
  }
</style>
@endpush

@push('scripts')
<script>
  (function () {
    const root = document.documentElement;
    const KEY = 'mach_theme';

    // Load saved theme if exists
    const saved = localStorage.getItem(KEY);
    if (saved) root.setAttribute('data-theme', saved);

    const btn  = document.getElementById('themeToggle');
    const logo = document.getElementById('brandLogo');
    const dot  = document.getElementById('brandDot');

    function showDotFallback() {
      if (logo) logo.style.display = 'none';
      if (dot) dot.style.display = 'inline-block';
    }

    function showLogo() {
      if (logo) logo.style.display = '';
      if (dot) dot.style.display = 'none';
    }

    function applyLogo(theme){
      // If image element missing, show dot
      if (!logo) { showDotFallback(); return; }

      const lightLogo = logo.getAttribute('data-logo-light');
      const darkLogo  = logo.getAttribute('data-logo-dark');

      // Best contrast:
      // - dark theme => light logo
      // - light theme => dark logo
      logo.src = (theme === 'light') ? darkLogo : lightLogo;
    }

    function setIcon(theme){
      if (!btn) return;
      btn.innerHTML = (theme === 'light')
        ? '<i class="bi bi-sun"></i>'
        : '<i class="bi bi-moon-stars"></i>';
    }

    // Initial state
    const current = root.getAttribute('data-theme') || 'dark';
    setIcon(current);
    applyLogo(current);

    // Logo load/fail handling
    if (logo) {
      logo.addEventListener('load', showLogo, { once:true });
      logo.addEventListener('error', showDotFallback, { once:true });
    } else {
      showDotFallback();
    }

    // Toggle theme
    if (!btn) return;

    btn.addEventListener('click', () => {
      const next = (root.getAttribute('data-theme') === 'light') ? 'dark' : 'light';
      root.setAttribute('data-theme', next);
      localStorage.setItem(KEY, next);
      setIcon(next);

      // Swap logo and keep fallback safe
      applyLogo(next);

      if (logo) {
        logo.onerror = showDotFallback;
        logo.onload  = showLogo;
      }
    });
  })();
</script>
@endpush
