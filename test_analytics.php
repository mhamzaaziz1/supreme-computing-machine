<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/reports/business-advance-analytics', 'GET', ['start_date' => '2026-06-01', 'end_date' => '2026-06-10']);

// Authenticate user
$user = App\User::first();
$app['auth']->login($user);
$request->session()->put('user.business_id', $user->business_id);

try {
    $response = $kernel->handle($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    if ($response->getStatusCode() != 200) {
        $content = $response->getContent();
        // If it's HTML, strip tags for readability
        if (strpos($content, '<html') !== false) {
            $content = strip_tags($content);
        }
        echo "Error: " . substr(trim(preg_replace('/\s+/', ' ', $content)), 0, 500) . "\n";
    }
} catch (\Throwable $e) {
    echo "Caught Exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n";
}
