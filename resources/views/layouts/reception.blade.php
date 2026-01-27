{{-- resources/views/layouts/reception.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}" data-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'Reception — Mach Hotel')</title>

  {{-- Bootstrap --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
 


  {{-- Premium theme tokens (same as public) --}}
  <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}?v={{ filemtime(public_path('assets/css/theme.css')) }}">

  
  <link rel="icon" href="/favicon.ico">

</head>

<body class="bg-soft">

  {{-- ✅ Topbar partial --}}
  @include('partials.reception.topbar')

  <main class="rx-shell">
    <div class="container rx-container rx-main-pad">
      @yield('content')
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  {{-- Theme toggle + logo swap + fallback dot --}}
  <script>
    (function () {
      const root = document.documentElement;
      const KEY = 'mach_theme';

      // restore theme
      const saved = localStorage.getItem(KEY);
      if (saved) root.setAttribute('data-theme', saved);

      const btn  = document.getElementById('rxThemeToggle');
      const logo = document.getElementById('rxBrandLogo');
      const dot  = document.getElementById('rxBrandDot');

      function showDotFallback() {
        if (logo) logo.style.display = 'none';
        if (dot) dot.style.display = 'inline-block';
      }
      function showLogo() {
        if (logo) logo.style.display = '';
        if (dot) dot.style.display = 'none';
      }

      function applyLogo(theme){
        if (!logo) { showDotFallback(); return; }
        const lightLogo = logo.getAttribute('data-logo-light'); // for dark theme
        const darkLogo  = logo.getAttribute('data-logo-dark');  // for light theme
        // DARK theme => light logo, LIGHT theme => dark logo
        logo.src = (theme === 'light') ? darkLogo : lightLogo;
      }

      function setIcon(theme){
        if (!btn) return;
        btn.innerHTML = (theme === 'light')
          ? '<i class="bi bi-sun"></i>'
          : '<i class="bi bi-moon-stars"></i>';
      }

      const current = root.getAttribute('data-theme') || 'dark';
      setIcon(current);
      applyLogo(current);

      if (logo) {
        logo.addEventListener('load', showLogo, { once:true });
        logo.addEventListener('error', showDotFallback, { once:true });
      } else {
        showDotFallback();
      }

      if (!btn) return;

      btn.addEventListener('click', () => {
        const next = (root.getAttribute('data-theme') === 'light') ? 'dark' : 'light';
        root.setAttribute('data-theme', next);
        localStorage.setItem(KEY, next);
        setIcon(next);
        applyLogo(next);

        if (logo) {
          logo.onerror = showDotFallback;
          logo.onload  = showLogo;
        }
      });
    })();
  </script>

  @stack('scripts')
</body>
</html>
