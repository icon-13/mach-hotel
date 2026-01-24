@php
  $rxUser = auth('reception')->user();
  $role   = $rxUser->role ?? 'staff';
  $isAdmin = ($role === 'admin');
@endphp

<nav class="navbar navbar-expand-lg nav-glass sticky-top border-bottom" style="border-color: var(--border) !important;">
  <div class="container rx-container">

    {{-- Brand --}}
    <a class="navbar-brand d-flex align-items-center gap-2"
       href="{{ $isAdmin ? route('reception.admin.rooms.index') : route('reception.bookings.index') }}">
      {{-- Fallback dot (shows only if logo fails) --}}
      <span class="brand-dot" id="rxBrandDot" style="display:none;"></span>

      <img
        src="{{ asset('brand/logo-light.png') }}"
        data-logo-light="{{ asset('brand/logo-light.png') }}"
        data-logo-dark="{{ asset('brand/logo-dark.png') }}"
        alt="Mach Hotel"
        class="brand-logo"
        id="rxBrandLogo"
        style="height:44px; width:44px;"
      >

      <div class="lh-sm">
        <div class="fw-semibold text-white">Mach Hotel</div>
        <div class="text-white-50 small">
          {{ $isAdmin ? 'Admin' : 'Reception' }}
        </div>
      </div>
    </a>

    {{-- Toggler --}}
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#rxNav" aria-controls="rxNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="rxNav">

      {{-- Links --}}
      <ul class="navbar-nav ms-lg-4 me-auto mb-2 mb-lg-0">

        {{-- Reception links (admin + reception both can see) --}}
        <li class="nav-item">
          <a class="nav-link text-white-75 @if(request()->routeIs('reception.bookings.*')) active text-white @endif"
             href="{{ route('reception.bookings.index') }}">
            <i class="bi bi-journal-text me-1"></i> Bookings
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white-75 @if(request()->routeIs('reception.bookings.create')) active text-white @endif"
             href="{{ route('reception.bookings.create') }}">
            <i class="bi bi-plus-circle me-1"></i> New Walk-in
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link text-white-75 @if(request()->routeIs('reception.rooms.*')) active text-white @endif"
             href="{{ route('reception.rooms.index') }}">
            <i class="bi bi-door-open me-1"></i> Rooms Board
          </a>
        </li>

        {{-- Admin-only dropdown --}}
        @if($isAdmin)
          <li class="nav-item dropdown">
            <a class="nav-link text-white-75 dropdown-toggle
                      @if(request()->routeIs('reception.admin.*')) active text-white @endif"
               href="#"
               role="button"
               data-bs-toggle="dropdown"
               aria-expanded="false">
              <i class="bi bi-shield-lock me-1"></i> Admin
            </a>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" href="{{ route('reception.admin.staff.index') }}">
                  <i class="bi bi-people me-2"></i> Staff Accounts
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('reception.admin.rooms.index') }}">
                  <i class="bi bi-grid-3x3-gap me-2"></i> Rooms Management
                </a>
              </li>
            </ul>
          </li>
        @endif

        <li class="nav-item">
          <a class="nav-link text-white-75" href="{{ route('home') }}">
            <i class="bi bi-globe2 me-1"></i> Public Site
          </a>
        </li>
      </ul>

      {{-- Right side --}}
      <div class="d-flex align-items-center gap-2">

        {{-- Role pill --}}
        <span class="chip" style="font-weight:800;">
          {{ strtoupper($role) }}
        </span>

        {{-- Theme toggle --}}
        <button type="button" class="btn btn-sm btn-outline-light px-3" id="rxThemeToggle" aria-label="Toggle theme">
          <i class="bi bi-moon-stars"></i>
        </button>

        {{-- User dropdown --}}
        <div class="dropdown">
          <button class="btn btn-sm btn-outline-light px-3 dropdown-toggle"
                  type="button"
                  data-bs-toggle="dropdown"
                  aria-expanded="false">
            <i class="bi bi-person-circle me-1"></i> {{ $rxUser->name ?? 'Staff' }}
          </button>

          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="{{ route('home') }}">
                <i class="bi bi-globe2 me-2"></i> Public Site
              </a>
            </li>

            <li><hr class="dropdown-divider"></li>

            <li>
              <form method="POST" action="{{ route('reception.logout') }}" class="m-0">
                @csrf
                <button class="dropdown-item text-danger" type="submit">
                  <i class="bi bi-box-arrow-right me-2"></i> Log Out
                </button>
              </form>
            </li>
          </ul>
        </div>

        {{-- Primary action --}}
        <a href="{{ route('reception.bookings.create') }}" class="btn btn-sm btn-gold px-3">
          Walk-in
        </a>

      </div>
    </div>
  </div>
</nav>

@push('scripts')
<script>
(function () {
  // Logo fallback (if logo missing -> show dot)
  const logo = document.getElementById('rxBrandLogo');
  const dot  = document.getElementById('rxBrandDot');

  function showDot(){
    if (logo) logo.style.display = 'none';
    if (dot) dot.style.display = 'inline-block';
  }
  function showLogo(){
    if (logo) logo.style.display = '';
    if (dot) dot.style.display = 'none';
  }

  if (logo) {
    logo.addEventListener('error', showDot, { once:true });
    logo.addEventListener('load', showLogo, { once:true });
  } else {
    showDot();
  }

  // Theme toggle (reuses same key as public)
  const root = document.documentElement;
  const KEY = 'mach_theme';
  const btn = document.getElementById('rxThemeToggle');

  const saved = localStorage.getItem(KEY);
  if (saved) root.setAttribute('data-theme', saved);

  function setIcon(theme){
    if (!btn) return;
    btn.innerHTML = (theme === 'light')
      ? '<i class="bi bi-sun"></i>'
      : '<i class="bi bi-moon-stars"></i>';
  }

  function applyLogo(theme){
    if (!logo) return;
    const lightLogo = logo.getAttribute('data-logo-light');
    const darkLogo  = logo.getAttribute('data-logo-dark');
    logo.src = (theme === 'light') ? darkLogo : lightLogo;
  }

  const current = root.getAttribute('data-theme') || 'dark';
  setIcon(current);
  applyLogo(current);

  if (btn) {
    btn.addEventListener('click', () => {
      const next = (root.getAttribute('data-theme') === 'light') ? 'dark' : 'light';
      root.setAttribute('data-theme', next);
      localStorage.setItem(KEY, next);
      setIcon(next);
      applyLogo(next);
    });
  }
})();
</script>
@endpush
