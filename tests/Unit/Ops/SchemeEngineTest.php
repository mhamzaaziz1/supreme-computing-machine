<?php

namespace Tests\Unit\Ops;

use App\Services\Ops\SchemeEngine;
use PHPUnit\Framework\TestCase;

class SchemeEngineTest extends TestCase
{
    private function line(int $variation, float $qty, float $price = 100, int $product = 1, ?int $category = 5, ?int $brand = 7): array
    {
        return ['variation_id' => $variation, 'product_id' => $product, 'category_id' => $category, 'brand_id' => $brand,
            'quantity' => $qty, 'unit_price_inc_tax' => $price];
    }

    private function scheme(string $type, array $rules, array $scope = []): array
    {
        return ['id' => 1, 'name' => 'Q3', 'type' => $type, 'starts_on' => '2026-07-01', 'ends_on' => null,
            'is_active' => true, 'scope' => $scope, 'audience' => [], 'rules' => $rules];
    }

    public function test_buy_ten_get_one_counts_across_qualifying_lines(): void
    {
        $r = SchemeEngine::apply(
            [$this->scheme('free_goods', ['buy_qty' => 10, 'free_qty' => 1], ['category_ids' => [5]])],
            [$this->line(10, 14, 1200), $this->line(11, 8, 900), $this->line(12, 50, 10, 3, 9)],
        );

        $this->assertCount(1, $r['applied']);
        $this->assertSame([['kind' => 'free', 'variation_id' => 10, 'quantity' => 2]], $r['applied'][0]['effects']);
        $this->assertEquals(2400, $r['applied'][0]['benefit']);
        $this->assertStringContainsString('Add 8 more', $r['hints'][0]['message'] ?? 'Add 8 more');
    }

    public function test_slab_picks_the_highest_slab_reached_and_hints_the_next(): void
    {
        $r = SchemeEngine::apply(
            [$this->scheme('slab_discount', ['slabs' => [['min_qty' => 25, 'percent' => 3.5], ['min_qty' => 10, 'percent' => 2]], 'unit_label' => 'cartons'])],
            [$this->line(10, 12, 1000)],
        );

        $this->assertSame(2.0, $r['applied'][0]['effects'][0]['percent']);
        $this->assertEquals(240, $r['applied'][0]['benefit']);
        $this->assertSame('Add 13 more cartons to get 3.5% off', $r['hints'][0]['message']);
    }

    public function test_nothing_applies_below_the_first_slab_or_outside_scope(): void
    {
        $r = SchemeEngine::apply(
            [$this->scheme('slab_discount', ['slabs' => [['min_qty' => 10, 'percent' => 2]]], ['brand_ids' => [99]])],
            [$this->line(10, 40)],
        );

        $this->assertSame([], $r['applied']);
        $this->assertSame([], $r['hints']);
    }

    public function test_scope_and_audience_matching(): void
    {
        $this->assertTrue(SchemeEngine::qualifies([], $this->line(1, 1)));
        $this->assertTrue(SchemeEngine::qualifies(['product_ids' => [2], 'brand_ids' => [7]], $this->line(1, 1)));
        $this->assertFalse(SchemeEngine::qualifies(['variation_ids' => [2]], $this->line(1, 1)));

        $this->assertTrue(SchemeEngine::audienceMatches([], null, null));
        $this->assertTrue(SchemeEngine::audienceMatches(['route_ids' => [3]], null, 3));
        $this->assertFalse(SchemeEngine::audienceMatches(['route_ids' => [3], 'customer_group_ids' => [1]], 2, 3));
    }
}
