<?php

// This is a simple script to test if the cache is working properly
// Run this script from the command line: php test-cache.php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

// Get the cache instance
$app = app();
$cache = $app->make('cache');

// Test writing to cache
echo "Testing cache write... ";
$cache->put('test_key', 'Hello from cache test!', 60);
echo "Done.\n";

// Test reading from cache
echo "Testing cache read... ";
$value = $cache->get('test_key');
echo "Value: " . $value . "\n";

// Test cache exists
echo "Testing cache exists... ";
$exists = $cache->has('test_key');
echo ($exists ? "Key exists" : "Key does not exist") . "\n";

// Test cache delete
echo "Testing cache delete... ";
$cache->forget('test_key');
echo "Done.\n";

// Verify deletion
echo "Verifying deletion... ";
$exists = $cache->has('test_key');
echo ($exists ? "Key still exists (error)" : "Key successfully deleted") . "\n";

// Show cache driver being used
echo "\nCache driver in use: " . config('cache.default') . "\n";
echo "Cache store: " . get_class($cache->store()) . "\n";

echo "\nCache test completed successfully!\n";