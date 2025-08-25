<?php

// This is a simple script to test if the cache is working properly with closures
// Run this script from the command line: php test-cache-closure.php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

// Get the cache instance
$app = app();
$cache = $app->make('cache');

echo "Current cache driver: " . config('cache.default') . "\n";

// Test 1: Caching a simple value (should work)
echo "\nTest 1: Caching a simple value\n";
$key1 = 'test_simple_value';
$value1 = 'Hello from cache test!';

echo "Writing to cache... ";
$cache->put($key1, $value1, 60);
echo "Done.\n";

echo "Reading from cache... ";
$cachedValue1 = $cache->get($key1);
echo "Value: " . $cachedValue1 . "\n";

echo "Test 1 " . ($value1 === $cachedValue1 ? "PASSED" : "FAILED") . "\n";

// Test 2: Caching an array (should work)
echo "\nTest 2: Caching an array\n";
$key2 = 'test_array';
$value2 = ['name' => 'John', 'age' => 30, 'city' => 'New York'];

echo "Writing to cache... ";
$cache->put($key2, $value2, 60);
echo "Done.\n";

echo "Reading from cache... ";
$cachedValue2 = $cache->get($key2);
echo "Value: " . json_encode($cachedValue2) . "\n";

echo "Test 2 " . (json_encode($value2) === json_encode($cachedValue2) ? "PASSED" : "FAILED") . "\n";

// Test 3: Caching an object with a closure (should fail)
echo "\nTest 3: Caching an object with a closure\n";
$key3 = 'test_closure';
$value3 = new class {
    public $name = 'Test Object';
    public $callback;
    
    public function __construct() {
        $this->callback = function() {
            return 'Hello from closure!';
        };
    }
};

echo "Writing to cache... ";
try {
    $cache->put($key3, $value3, 60);
    echo "Done (unexpected success).\n";
    echo "Test 3 FAILED (should have thrown an exception)\n";
} catch (\Exception $e) {
    echo "Failed with exception: " . $e->getMessage() . "\n";
    echo "Test 3 PASSED (correctly threw an exception)\n";
}

// Test 4: Caching data without closures (should work)
echo "\nTest 4: Caching data without closures\n";
$key4 = 'test_no_closure';
$value4 = new class {
    public $name = 'Test Object';
    public $data = 'Some data';
    
    public function toArray() {
        return [
            'name' => $this->name,
            'data' => $this->data
        ];
    }
};

echo "Writing to cache... ";
try {
    // Only cache the array representation, not the object itself
    $cache->put($key4, $value4->toArray(), 60);
    echo "Done.\n";
    
    echo "Reading from cache... ";
    $cachedValue4 = $cache->get($key4);
    echo "Value: " . json_encode($cachedValue4) . "\n";
    
    echo "Test 4 PASSED\n";
} catch (\Exception $e) {
    echo "Failed with exception: " . $e->getMessage() . "\n";
    echo "Test 4 FAILED\n";
}

// Clean up
$cache->forget($key1);
$cache->forget($key2);
$cache->forget($key4);

echo "\nAll tests completed.\n";