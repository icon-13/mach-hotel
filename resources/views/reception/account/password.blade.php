@php
  $rxUser  = auth('reception')->user();
  $role    = $rxUser->role ?? 'staff';
  $isAdmin = ($role === 'admin');
  $context = $isAdmin ? 'Admin Panel' : 'Reception Desk';
@endphp

<nav class="navbar navbar-expand-lg nav-glass sticky-top rx-nav"
     style="border-bottom:1px solid var(--border) !important;">
  <div class="container rx-container">

    {{-- Brand --}}
    <a class="navbar-brand d-flex align-items-center gap-2 rx-brand"
       href="{{ $isAdmin ? route('reception.admin.rooms.index') : route('reception.bookings.index') }}">

      <span class="brand-dot d-none" id="rxBrandDot"></span>

      {{-- ✅ Premium rounded logo with subtle ring --}}
      <span class="d-inline-flex align-items-center justify-content-center"
            style="
              height:48px; width:48px;
              border-radius:999px;
              padding:2px;
              background: linear-gradient(135deg, rgba(255,215,128,.55), rgba(255,255,255,.10));
              box-shadow: 0 10px 28px rgba(0,0,0,.25);
            ">
        <img
          src="{{ asset('brand/logo-light.png') }}"
          data-logo-light="{{ asset('brand/logo-light.png') }}"
          data-logo-dark="{{ asset('brand/logo-dark.png') }}"
          alt="Mach Hotel"
          class="brand-logo"
          id="rxBrandLogo"
          style="
            height:44px; width:44px;
            border-radius:999px;
            object-fit:cover;
            background: rgba(255,255,255,.08);
          "
        >
      </span>

      <div class="lh-sm rx-brand-text">
        <div class="fw-semibold rx-brand-title d-flex align-items-center gap-2">
          <span class="rx-brand-name">Mach Hotel</span>
          <span class="badge rounded-pill rx-role-badge">
            {{ $isAdmin ? 'ADMIN' : 'RECEPTION' }}
          </span>
        </div>
        <div class="rx-brand-sub small">{{ $context }}</div>
      </div>
    </a>

    {{-- Toggler --}}
    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#rxNav"
            aria-controls="rxNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="rxNav">

      {{-- Left: Main nav --}}
      <ul class="navbar-nav ms-lg-4 me-auto mb-2 mb-lg-0 gap-lg-1">

        <li class="nav-item">
          <a class="nav-link @if(request()->routeIs('reception.bookings.*')) active @endif"
             href="{{ route('reception.bookings.index') }}">
            <i class="bi bi-journal-text me-1"></i><span>Bookings</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link @if(request()->routeIs('reception.rooms.*')) active @endif"
             href="{{ route('reception.rooms.index') }}">
            <i class="bi bi-door-open me-1"></i><span>Rooms</span>
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link @if(request()->routeIs('reception.bookings.create')) active @endif"
             href="{{ route('reception.bookings.create') }}">
            <i class="bi bi-plus-circle me-1"></i><span>Walk-in</span>
          </a>
        </li>

        @if($isAdmin)
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle @if(request()->routeIs('reception.admin.*')) active @endif"
               href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-shield-lock me-1"></i><span>Admin</span>
            </a>

            <ul class="dropdown-menu rx-dd">
              <li>
                <a class="dropdown-item" href="{{ route('reception.admin.rooms.index') }}">
                  <i class="bi bi-grid-3x3-gap me-2"></i> Rooms Management
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('reception.admin.staff.index') }}">
                  <i class="bi bi-people me-2"></i> Staff Accounts
                </a>
              </li>
            </ul>
          </li>
        @endif

        <li class="nav-item">
          <a class="nav-link" href="{{ route('home') }}">
            <i class="bi bi-globe2 me-1"></i><span>Public Site</span>
          </a>
        </li>

        {{-- ✅ Mobile (expanded) quick actions: NO GET logout --}}
        <li class="nav-item d-lg-none mt-2">
          <div class="card border-0" style="background: rgba(255,255,255,.06);">
            <div class="card-body p-2">
              <div class="small text-muted px-1 mb-2">Quick Actions</div>

              <div class="d-grid gap-2">
                <a class="btn btn-outline-light"
                   href="{{ route('reception.account.password') }}">
                  <i class="bi bi-shield-lock me-1"></i> Change Password
                </a>

                {{-- ✅ POST logout (fixes 419) --}}
                <form method="POST" action="{{ route('reception.logout') }}">
                  @csrf
                  <button type="submit" class="btn btn-outline-danger w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                  </button>
                </form>

              </div>
            </div>
          </div>
        </li>

      </ul>

      {{-- Right: Actions (ALWAYS visible on desktop, still works on mobile too) --}}
      <div class="d-flex align-items-center gap-2 rx-actions">

        {{-- Theme toggle --}}
        <button type="button" class="btn btn-sm btn-outline-light px-3"
                id="rxThemeToggle" aria-label="Toggle theme">
          <i class="bi bi-moon-stars"></i>
        </button>

        {{-- Profile dropdown --}}
        <div class="dropdown">
          <button class="btn btn-sm btn-outline-light px-3 dropdown-toggle"
                  type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle me-1"></i>
            <span class="d-none d-lg-inline">{{ $rxUser->name ?? 'Staff' }}</span>
            <span class="d-lg-none">Account</span>
          </button>

          <ul class="dropdown-menu dropdown-menu-end rx-dd">

            <li class="px-3 py-2">
              <div class="fw-semibold">{{ $rxUser->name ?? 'Staff' }}</div>
              <div class="rx-dd-muted small">{{ $rxUser->email ?? '' }}</div>
              <div class="rx-dd-muted small">Role: {{ strtoupper($role) }}</div>
            </li>

            <li><hr class="dropdown-divider"></li>

            <li>
              <a class="dropdown-item" href="{{ route('reception.account.password') }}">
                <i class="bi bi-shield-lock me-2"></i> Change Password
              </a>
            </li>

            <li><hr class="dropdown-divider"></li>

            @if($isAdmin)
              <li>
                <a class="dropdown-item" href="{{ route('reception.admin.rooms.index') }}">
                  <i class="bi bi-grid-3x3-gap me-2"></i> Admin • Rooms
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('reception.admin.staff.index') }}">
                  <i class="bi bi-people me-2"></i> Admin • Staff
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
            @endif

            <li>
              <a class="dropdown-item" href="{{ route('reception.bookings.index') }}">
                <i class="bi bi-journal-text me-2"></i> Bookings
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ route('reception.rooms.index') }}">
                <i class="bi bi-door-open me-2"></i> Rooms Board
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ route('home') }}">
                <i class="bi bi-globe2 me-2"></i> Public Site
              </a>
            </li>

            <li><hr class="dropdown-divider"></li>

            {{-- ✅ POST logout inside dropdown --}}
            <li class="px-2 pb-2">
              <form method="POST" action="{{ route('reception.logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100">
                  <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
              </form>
            </li>

          </ul>
        </div>

        {{-- ✅ Desktop visible logout (POST, not GET) --}}
        <form method="POST" action="{{ route('reception.logout') }}" class="d-none d-lg-inline">
          @csrf
          <button type="submit" class="btn btn-sm btn-outline-danger px-3">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
          </button>
        </form>

      </div>
    </div>

  </div>
</nav>
