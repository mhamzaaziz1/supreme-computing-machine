<?php

// Path to the Laravel application
$laravel_path = __DIR__;

// Change to the Laravel application directory
chdir($laravel_path);

// Execute the route:clear command
echo shell_exec('php artisan route:clear');

// Execute the cache:clear command
echo shell_exec('php artisan cache:clear');

// Execute the config:clear command
echo shell_exec('php artisan config:clear');

// Execute the view:clear command
echo shell_exec('php artisan view:clear');

echo "All caches cleared successfully!\n";