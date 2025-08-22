<?php

// Set the base path for the Laravel application
$basePath = __DIR__;

// Include the autoloader
require $basePath . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once $basePath . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Run specific migration
$exitCode = $kernel->call('migrate', [
    '--path' => 'database/migrations/2025_08_22_000000_add_supply_chain_vehicle_id_to_transactions_table.php',
    '--force' => true,
]);

if ($exitCode === 0) {
    echo "Migration completed successfully.\n";
} else {
    echo "Migration failed with exit code: $exitCode\n";
}
