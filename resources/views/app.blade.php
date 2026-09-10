<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr' }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    {{-- The app is often served from a subdirectory; overlays build /ops URLs from this. --}}
    <meta name="app-base" content="{{ url('/') }}" />

    {{-- Applied before first paint so the shell never flashes light then dark. --}}
    <script>
      try {
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && matchMedia('(prefers-color-scheme: dark)').matches)) {
          document.documentElement.classList.add('dark');
        }
      } catch (e) {}
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
    <script>
      window.addEventListener('error', function(e) {
        fetch('/log_error.php', { method: 'POST', body: 'Error: ' + e.message + ' at ' + e.filename + ':' + e.lineno + ':' + e.colno + '\nStack: ' + (e.error ? e.error.stack : '') });
      });
      window.addEventListener('unhandledrejection', function(e) {
        fetch('/log_error.php', { method: 'POST', body: 'Promise Rejection: ' + e.reason });
      });
    </script>
  </head>
  <body>
    @inertia
  </body>
</html>
