<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Models\User;
use App\User as OldUser;
use Illuminate\Support\Facades\Auth;

class TestAnalytics extends Command
{
    protected $signature = 'test:analytics';
    protected $description = 'Test analytics page';

    public function handle()
    {
        $user = class_exists(User::class) ? User::first() : OldUser::first();
        Auth::login($user);
        session()->put('user.business_id', $user->business_id);
        
        $request = Request::create('/reports/business-advance-analytics', 'GET', ['start_date' => '2026-06-01', 'end_date' => '2026-06-10']);
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $response = app()->handle($request);
        
        $this->info("Status: " . $response->getStatusCode());
        if ($response->getStatusCode() != 200) {
            $content = $response->getContent();
            if (strpos($content, '<html') !== false) {
                $content = strip_tags($content);
                $content = preg_replace('/\s+/', ' ', $content);
            }
            $this->error("Error: " . substr($content, 0, 1500));
        } else {
            $this->info("Success!");
        }
    }
}
