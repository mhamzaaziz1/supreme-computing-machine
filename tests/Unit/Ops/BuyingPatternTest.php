<?php

namespace Tests\Unit\Ops;

use App\Services\Ops\BuyingPattern;
use App\Services\Ops\ServiceDue;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class BuyingPatternTest extends TestCase
{
    public function test_rhythm_is_the_median_gap_between_distinct_order_days(): void
    {
        $this->assertNull(BuyingPattern::rhythm(['2026-09-01']));
        $this->assertSame(7.0, BuyingPattern::rhythm(['2026-09-08', '2026-09-01', '2026-08-25', '2026-08-25', '2026-07-01']));
    }

    public function test_a_sku_quiet_for_twice_its_interval_is_flagged(): void
    {
        $today = Carbon::parse('2026-09-10');
        $history = [
            10 => ['product_id' => 1, 'variation_id' => 10, 'name' => '20W-50 Drum', 'sku' => 'D1',
                'dates' => ['2026-06-01', '2026-06-22', '2026-07-13']],   // every 21 days, last 59 days ago
            11 => ['product_id' => 2, 'variation_id' => 11, 'name' => '5W-30 4L', 'sku' => 'C1',
                'dates' => ['2026-08-01', '2026-08-15', '2026-09-01']],   // fine
            12 => ['product_id' => 3, 'variation_id' => 12, 'name' => 'ATF', 'sku' => 'A1',
                'dates' => ['2026-01-01', '2026-02-01']],                 // too few orders to judge
        ];

        $stopped = BuyingPattern::stopped($history, $today);

        $this->assertCount(1, $stopped);
        $this->assertSame(10, $stopped[0]['variation_id']);
        $this->assertSame(59, $stopped[0]['days_since']);
    }

    public function test_basket_keeps_skus_in_at_least_forty_percent_of_recent_orders(): void
    {
        $history = [
            10 => ['product_id' => 1, 'variation_id' => 10, 'name' => 'A', 'sku' => 'A', 'qty' => [1 => 5, 2 => 7, 3 => 5, 4 => 6]],
            11 => ['product_id' => 2, 'variation_id' => 11, 'name' => 'B', 'sku' => 'B', 'qty' => [1 => 2]],
        ];

        $basket = BuyingPattern::basket($history, [1, 2, 3, 4, 5]);

        $this->assertCount(1, $basket);
        $this->assertSame(5.5, $basket[0]['quantity']);
        $this->assertSame('4/5', $basket[0]['frequency']);
    }

    public function test_service_due_uses_the_vehicles_own_km_per_day(): void
    {
        $today = Carbon::parse('2026-09-10');
        $p = ServiceDue::predict([
            ['date' => '2026-03-01', 'reading' => 40000, 'next' => 45000],
            ['date' => '2026-06-01', 'reading' => 44600, 'next' => 49600],   // 4600 km in 92 days = 50/day
        ], $today);

        $this->assertSame('history', $p['basis']);
        $this->assertSame(50.0, $p['km_per_day']);
        $this->assertSame('2026-09-09', $p['due_on']);                      // 5000 km / 50 = 100 days after 1 Jun
        $this->assertSame('overdue', $p['status']);
    }

    public function test_service_due_falls_back_to_the_default_rate_with_one_visit(): void
    {
        $p = ServiceDue::predict([
            ['date' => '2026-09-01', 'reading' => 10000, 'next' => 12000],
        ], Carbon::parse('2026-09-10'), 40);

        $this->assertSame('default', $p['basis']);
        $this->assertSame('2026-10-21', $p['due_on']);
        $this->assertSame('ok', $p['status']);
    }
}
