<?php

namespace Tests\Unit\Ops;

use App\Services\Ops\CreditPosition;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class CreditPositionTest extends TestCase
{
    private function inv(int $id, string $date, float $total, float $paid = 0, ?int $term = null): array
    {
        return ['id' => $id, 'invoice_no' => "INV$id", 'date' => $date, 'total' => $total, 'paid' => $paid,
            'pay_term_number' => $term, 'pay_term_type' => $term ? 'days' : null];
    }

    public function test_it_buckets_dues_by_invoice_age_and_ignores_paid_invoices(): void
    {
        $today = Carbon::parse('2026-09-10');
        $p = CreditPosition::summarise([
            $this->inv(1, '2026-09-01', 1000),          // 9 days
            $this->inv(2, '2026-07-25', 500, 100),      // 47 days
            $this->inv(3, '2026-06-20', 300),           // 82 days
            $this->inv(4, '2026-05-01', 200),           // 132 days
            $this->inv(5, '2026-05-01', 200, 200),      // settled
        ], 5000, null, [0, null], $today);

        $this->assertSame(1900.0, $p['outstanding']);
        $this->assertSame(['0_30' => 1000.0, '31_60' => 400.0, '61_90' => 300.0, '90_plus' => 200.0], $p['aging']);
        $this->assertCount(4, $p['invoices']);
        $this->assertSame(3100.0, $p['available']);
    }

    public function test_overdue_days_respect_the_payment_term(): void
    {
        $today = Carbon::parse('2026-09-10');
        $p = CreditPosition::summarise([
            $this->inv(1, '2026-08-01', 100, 0, 30),    // due 31 Aug → 10 days overdue
        ], null, 7, [0, null], $today);

        $this->assertSame(10, $p['oldest_overdue_days']);

        $contactTerm = CreditPosition::summarise([
            $this->inv(1, '2026-08-01', 100),           // contact term 60 days → not yet due
        ], null, 7, [60, 'days'], $today);

        $this->assertSame(0, $contactTerm['oldest_overdue_days']);
    }

    public function test_reasons_cover_limit_overdue_and_hold(): void
    {
        $today = Carbon::parse('2026-09-10');
        $position = CreditPosition::summarise([
            $this->inv(1, '2026-06-01', 184500),
        ], 250000, 60, [0, null], $today);
        $position['hold'] = ['flag' => true, 'reason' => 'Cheque bounced'];

        $reasons = CreditPosition::reasons($position, 77800);
        $codes = array_column($reasons, 'code');

        $this->assertSame(['on_hold', 'over_limit', 'overdue'], $codes);
        $this->assertEqualsWithDelta(12300, $reasons[1]['over_by'], 0.001);
        $this->assertSame('Cheque bounced', $reasons[0]['message']);
    }

    public function test_a_fully_paid_sale_is_never_held(): void
    {
        $position = CreditPosition::summarise([], 0.0, 0, [0, null], Carbon::parse('2026-09-10'));
        $position['hold'] = ['flag' => true, 'reason' => null];

        $this->assertSame([], CreditPosition::reasons($position, 0));
    }

    public function test_within_limit_passes(): void
    {
        $position = CreditPosition::summarise([
            $this->inv(1, '2026-09-01', 1000),
        ], 5000, 30, [0, null], Carbon::parse('2026-09-10'));
        $position['hold'] = ['flag' => false, 'reason' => null];

        $this->assertSame([], CreditPosition::reasons($position, 3999));
        $this->assertSame('over_limit', CreditPosition::reasons($position, 4001)[0]['code']);
    }
}
