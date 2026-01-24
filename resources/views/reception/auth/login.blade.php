{{-- resources/views/reception/auth/login.blade.php --}}
<!doctype html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Staff Portal — Mach Hotel</title>

  {{-- Bootstrap --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  {{-- Theme tokens --}}
  <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}?v={{ filemtime(public_path('assets/css/theme.css')) }}">

  <style>
    /* Premium login wrapper (minimal extra CSS, uses your theme tokens) */
    .rx-auth-page{
      min-height: 100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding: 24px 14px;
      background:
        radial-gradient(900px 500px at 20% 10%, rgba(255,255,255,.08), transparent 60%),
        radial-gradient(900px 500px at 80% 90%, rgba(255,255,255,.06), transparent 60%),
        var(--bg, #0b1220);
    }
    .rx-auth-shell{
      width: 100%;
      max-width: 980px;
      border-radius: 18px;
      overflow: hidden;
      border: 1px solid rgba(255,255,255,.10);
      box-shadow: 0 25px 80px rgba(0,0,0,.35);
      background: rgba(255,255,255,.06);
      backdrop-filter: blur(10px);
    }
    .rx-auth-left{
      padding: 28px;
      min-height: 100%;
      background:
        linear-gradient(135deg, rgba(0,0,0,.55), rgba(0,0,0,.20)),
        radial-gradient(600px 300px at 30% 30%, rgba(255,215,0,.10), transparent 70%);
    }
    .rx-auth-right{
      padding: 28px;
      background: rgba(10,16,28,.55);
    }
    .rx-auth-logo{
      height: 54px;
      width: 54px;
      border-radius: 14px;
      object-fit: cover;
      border: 1px solid rgba(255,255,255,.15);
      background: rgba(255,255,255,.06);
    }
    .rx-auth-kicker{
      letter-spacing: .12em;
      text-transform: uppercase;
      font-size: .78rem;
      color: rgba(255,255,255,.65);
    }
    .rx-auth-title{
      font-weight: 800;
      font-size: 1.6rem;
      line-height: 1.15;
      color: #fff;
      margin: 8px 0 10px;
    }
    .rx-auth-sub{
      color: rgba(255,255,255,.70);
      max-width: 34ch;
    }
    .rx-auth-badge{
      display:inline-flex;
      align-items:center;
      gap:.45rem;
      padding:.35rem .6rem;
      border-radius:999px;
      background: rgba(255,255,255,.10);
      border:1px solid rgba(255,255,255,.14);
      color: rgba(255,255,255,.85);
      font-size: .84rem;
      font-weight: 700;
    }
    .rx-auth-card{
      border-radius: 16px;
      border: 1px solid rgba(255,255,255,.10);
      background: rgba(255,255,255,.05);
    }
    .rx-input{
      border-radius: 12px;
      padding: 12px 12px;
      border: 1px solid rgba(255,255,255,.14);
      background: rgba(0,0,0,.18);
      color: #fff;
    }
    .rx-input:focus{
      box-shadow: 0 0 0 .25rem rgba(255,215,0,.12);
      border-color: rgba(255,215,0,.35);
    }
    .rx-muted{
      color: rgba(255,255,255,.65);
    }
    .rx-divider{
      border-color: rgba(255,255,255,.12) !important;
    }
    .rx-pwd-wrap{
      position: relative;
    }
    .rx-pwd-toggle{
      position:absolute;
      top:50%;
      right:10px;
      transform: translateY(-50%);
      border:0;
      background: transparent;
      color: rgba(255,255,255,.75);
      padding: 6px 8px;
      border-radius: 10px;
    }
    .rx-pwd-toggle:hover{
      background: rgba(255,255,255,.08);
      color: #fff;
    }
    .rx-auth-foot{
      color: rgba(255,255,255,.55);
      font-size: .85rem;
      margin-top: 14px;
      text-align:center;
    }
    .rx-link{
      color: rgba(255,255,255,.80);
      text-decoration: none;
      border-bottom: 1px dashed rgba(255,255,255,.35);
    }
    .rx-link:hover{
      color:#fff;
      border-bottom-color: rgba(255,215,0,.55);
    }
  </style>
</head>

<body>
  <div class="rx-auth-page">
    <div class="rx-auth-shell">
      <div class="row g-0">

        {{-- LEFT BRAND PANEL --}}
        <div class="col-lg-5">
          <div class="rx-auth-left h-100 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex align-items-center gap-3">
                <img
                  src="{{ asset('brand/logo-light.png') }}"
                  alt="Mach Hotel"
                  class="rx-auth-logo"
                  id="rxAuthLogo"
                  data-logo-light="{{ asset('brand/logo-light.png') }}"
                  data-logo-dark="{{ asset('brand/logo-dark.png') }}"
                >
                <div>
                  <div class="rx-auth-kicker">Mach Hotel</div>
                  <div class="rx-auth-title mb-0">Staff Portal</div>
                </div>
              </div>

              <div class="mt-3 rx-auth-sub">
                Sign in to manage bookings, check-ins, and rooms. Admins can manage staff & room assignments.
              </div>

              <div class="mt-3">
                <span class="rx-auth-badge">
                  <i class="bi bi-shield-lock"></i> Authorized staff only
                </span>
              </div>

              <hr class="rx-divider my-4">

              <div class="rx-muted small">
                Tip: If you’re a guest trying to manage a booking, go to the public site and use your booking code.
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
              <a class="rx-link small" href="{{ route('home') }}">
                <i class="bi bi-globe2 me-1"></i> Back to public site
              </a>
              <div class="rx-muted small">
                <i class="bi bi-lock me-1"></i> Secure access
              </div>
            </div>
          </div>
        </div>

        {{-- RIGHT FORM PANEL --}}
        <div class="col-lg-7">
          <div class="rx-auth-right h-100">
            <div class="card rx-auth-card">
              <div class="card-body p-4 p-lg-4">

                <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                  <div>
                    <div class="rx-auth-kicker">Login</div>
                    <h4 class="mb-1 text-white" style="font-weight:800;">Welcome back</h4>
                    <div class="rx-muted small">Use your staff email and password to continue.</div>
                  </div>

                  {{-- Theme toggle (optional) --}}
                  <button type="button" class="btn btn-sm btn-outline-light px-3" id="rxThemeToggle" aria-label="Toggle theme">
                    <i class="bi bi-moon-stars"></i>
                  </button>
                </div>

                {{-- Flash --}}
                @if(session('success'))
                  <div class="alert alert-success mb-3">{{ session('success') }}</div>
                @endif

                {{-- Errors --}}
                @if($errors->any())
                  <div class="alert alert-danger mb-3">
                    <div class="fw-semibold mb-1">Please fix the following:</div>
                    <ul class="mb-0 small">
                      @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif

                <form method="POST" action="{{ route('reception.login.submit') }}">
                  @csrf

                  <div class="row g-3">
                    <div class="col-12">
                      <label class="form-label text-white-50 mb-1">Email</label>
                      <input
                        type="email"
                        name="email"
                        class="form-control rx-input"
                        placeholder="staff@machhotel.com"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                      >
                    </div>

                    <div class="col-12">
                      <label class="form-label text-white-50 mb-1">Password</label>
                      <div class="rx-pwd-wrap">
                        <input
                          type="password"
                          name="password"
                          id="rxPassword"
                          class="form-control rx-input"
                          placeholder="••••••••"
                          required
                          autocomplete="current-password"
                        >
                        <button type="button" class="rx-pwd-toggle" id="rxTogglePassword" aria-label="Show password">
                          <i class="bi bi-eye"></i>
                        </button>
                      </div>
                    </div>

                    <div class="col-12 d-flex align-items-center justify-content-between flex-wrap gap-2">
                      <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember">
                        <label class="form-check-label rx-muted" for="remember">Remember me</label>
                      </div>

                      <span class="rx-muted small">
                        <i class="bi bi-info-circle me-1"></i> If inactive, contact admin.
                      </span>
                    </div>

                    <div class="col-12">
                      <button type="submit" class="btn btn-gold btn-lg w-100" style="border-radius:14px;">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                      </button>
                    </div>
                  </div>

                </form>

                <div class="rx-auth-foot">
                  Unauthorized access prohibited • Mach Hotel Reception System
                </div>

              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // password show/hide
    (function () {
      const input = document.getElementById('rxPassword');
      const btn   = document.getElementById('rxTogglePassword');
      if (!input || !btn) return;

      btn.addEventListener('click', () => {
        const isPwd = input.type === 'password';
        input.type = isPwd ? 'text' : 'password';
        btn.innerHTML = isPwd
          ? '<i class="bi bi-eye-slash"></i>'
          : '<i class="bi bi-eye"></i>';
      });
    })();

    // theme toggle + logo swap (uses same key as your site)
    (function () {
      const root = document.documentElement;
      const KEY  = 'mach_theme';

      const btn  = document.getElementById('rxThemeToggle');
      const logo = document.getElementById('rxAuthLogo');

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
        const lightLogo = logo.getAttribute('data-logo-light'); // for dark theme
        const darkLogo  = logo.getAttribute('data-logo-dark');  // for light theme
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
</body>
</html>
