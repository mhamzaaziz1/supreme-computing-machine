<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
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
