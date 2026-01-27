<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}" data-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'Mach Hotel')</title>
  <meta name="description" content="@yield('meta_description', 'Mach Hotel — Modern Luxury in Dar es Salaam. Book in seconds, pay at counter, instant WhatsApp confirmation.')">
  <meta name="theme-color" content="#0b0f14">

  {{-- Performance: warm up CDN connections --}}
  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
  <link rel="dns-prefetch" href="//cdn.jsdelivr.net">

  {{-- Bootstrap --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  {{-- ✅ Mach Hotel theme --}}
  <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}?v={{ filemtime(public_path('assets/css/theme.css')) }}">

  @stack('styles')

  {{-- ✅ Theme restore early (prevents flash) --}}
  <script>
    (function () {
      const saved = localStorage.getItem('mh_theme');
      if (saved) document.documentElement.setAttribute('data-theme', saved);

      // keep theme-color in sync (nice polish on mobile address bar)
      const meta = document.querySelector('meta[name="theme-color"]');
      if (meta) meta.setAttribute('content', saved === 'light' ? '#f7f8fb' : '#0b0f14');
    })();
  </script>

  {{-- ✅ Tiny critical safety net (prevents ghost scroll on mobile) --}}
  <style>
    html, body { max-width: 100%; overflow-x: hidden; }
    
  </style>
  <link rel="icon" href="/favicon.ico">

</head>

<body class="d-flex flex-column min-vh-100">

  {{-- ✅ Navbar --}}
  @include('partials.navbar')

  {{-- ✅ Main fills space (footer sits correctly, no gaps) --}}
  <main class="flex-fill">
    @yield('content')
  </main>

  {{-- ✅ Footer --}}
  @include('partials.footer')

  {{-- Bootstrap JS (needed for modal + carousel) --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  {{-- ✅ Sticky bar detector (fixes hidden buttons + footer gap) --}}
  <script>
    (function(){
      function syncSticky(){
        const bar = document.querySelector('.mobile-sticky-bar');
        document.body.classList.toggle('has-stickybar', !!bar);
      }
      document.addEventListener('DOMContentLoaded', syncSticky);
      window.addEventListener('resize', syncSticky);
      window.addEventListener('pageshow', syncSticky);
    })();
  </script>

  {{-- ✅ Smooth iOS viewport fix (reduces extra scroll space on mobile Safari) --}}
  <script>
    (function(){
      function setVh(){
        document.documentElement.style.setProperty('--vh', (window.innerHeight * 0.01) + 'px');
      }
      setVh();
      window.addEventListener('resize', setVh);
      window.addEventListener('orientationchange', setVh);
    })();
  </script>

  @stack('scripts')
</body>
</html>
