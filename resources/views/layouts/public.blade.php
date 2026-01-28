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
  {{-- flatPickr --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

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

  <script>
    src="https://cdn.jsdelivr.net/npm/flatpickr"
  </script>
  @stack('scripts')

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

  <script>
(function(){
  const tzToday = "{{ now('Africa/Dar_es_Salaam')->toDateString() }}";

  const inEl  = document.getElementById('check_in');
  const outEl = document.getElementById('check_out');

  if(!inEl || !outEl) return;

  // Force min every time page loads (iOS safe)
  inEl.setAttribute('min', tzToday);

  function syncOutMin(){
    const inVal = inEl.value || tzToday;
    // check_out must be at least next day after check_in
    const d = new Date(inVal + "T00:00:00");
    d.setDate(d.getDate() + 1);
    const nextDay = d.toISOString().slice(0,10);
    outEl.setAttribute('min', nextDay);

    // If user already picked an invalid check_out, auto-fix it
    if(outEl.value && outEl.value < nextDay){
      outEl.value = nextDay;
    }
  }

  // If user types past date manually, snap back to today
  inEl.addEventListener('change', () => {
    if(inEl.value && inEl.value < tzToday){
      inEl.value = tzToday;
    }
    syncOutMin();
  });

  outEl.addEventListener('change', () => {
    syncOutMin();
  });

  syncOutMin();
})();
</script>


  @stack('scripts')
</body>
</html>
