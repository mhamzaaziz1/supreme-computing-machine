<?php

namespace App\Services\Ops;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Trade schemes: which apply to a cart, what they give, and how close an
 * outlet is to its next target tier.
 *
 * The cart-level rules (free goods, slab discounts) are pure functions over
 * the lines so they can be tested without a database; the database is only
 * read to find which schemes are live and who they are for.
 *
 * The sale form applies the effects it is given (free units as extra
 * quantity at a percentage line discount, slabs as a percentage line
 * discount) and posts through the ordinary store endpoint. What was applied
 * is recorded against the invoice afterwards for rebate and scheme reports.
 */
class SchemeEngine
{
    public const TYPES = ['free_goods', 'slab_discount', 'target_rebate'];

    /**
     * Live schemes for this business on this day.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function active(int $businessId, ?Carbon $on = null): Collection
    {
        $on ??= Carbon::today();

        return DB::table('trade_schemes')
            ->where('business_id', $businessId)
            ->where('is_active', 1)
            ->whereDate('starts_on', '<=', $on)
            ->where(fn ($q) => $q->whereNull('ends_on')->orWhereDate('ends_on', '>=', $on))
            ->orderBy('id')
            ->get()
            ->map(fn ($s) => self::hydrate($s));
    }

    /**
     * @param  array<int, array{variation_id: int, quantity: float|int, unit_price_inc_tax?: float}>  $lines
     * @return array<string, mixed>
     */
    public function evaluate(int $businessId, ?int $contactId, array $lines): array
    {
        $contact = $contactId
            ? DB::table('contacts')->where('business_id', $businessId)->where('id', $contactId)->first(['customer_group_id', 'customer_route_id'])
            : null;

        $schemes = $this->active($businessId)
            ->filter(fn ($s) => self::audienceMatches($s['audience'], $contact?->customer_group_id, $contact?->customer_route_id))
            ->values()
            ->all();

        $result = self::apply($schemes, $this->withMeta($businessId, $lines));
        $result['targets'] = $contactId ? $this->progressFor($businessId, $contactId) : [];

        return $result;
    }

    /**
     * Free goods and slab discounts over a cart.
     *
     * @param  array<int, array<string, mixed>>  $schemes  hydrated
     * @param  array<int, array<string, mixed>>  $lines  each: variation_id, product_id, category_id, brand_id, quantity, unit_price_inc_tax
     * @return array{applied: array<int, array<string, mixed>>, hints: array<int, array<string, mixed>>}
     */
    public static function apply(array $schemes, array $lines): array
    {
        $applied = [];
        $hints = [];

        foreach ($schemes as $s) {
            if ($s['type'] === 'target_rebate') {
                continue;
            }

            $qualifying = array_values(array_filter($lines, fn ($l) => (float) $l['quantity'] > 0 && self::qualifies($s['scope'], $l)));
            if (! $qualifying) {
                continue;
            }

            $total = array_sum(array_map(fn ($l) => (float) $l['quantity'], $qualifying));
            $unit = $s['rules']['unit_label'] ?? 'units';

            if ($s['type'] === 'free_goods') {
                $buy = max(1, (int) ($s['rules']['buy_qty'] ?? 0));
                $free = max(1, (int) ($s['rules']['free_qty'] ?? 0));
                $sets = (int) floor($total / $buy);

                if ($sets > 0) {
                    usort($qualifying, fn ($a, $b) => $b['quantity'] <=> $a['quantity']);
                    $target = ! empty($s['rules']['free_variation_id']) ? (int) $s['rules']['free_variation_id'] : (int) $qualifying[0]['variation_id'];
                    $price = 0.0;
                    foreach ($lines as $l) {
                        if ((int) $l['variation_id'] === $target) {
                            $price = (float) ($l['unit_price_inc_tax'] ?? 0);
                        }
                    }

                    $applied[] = [
                        'scheme_id' => $s['id'],
                        'name' => $s['name'],
                        'type' => $s['type'],
                        'summary' => sprintf('Buy %d get %d: %d free', $buy, $free, $sets * $free),
                        'effects' => [['kind' => 'free', 'variation_id' => $target, 'quantity' => $sets * $free]],
                        'benefit' => round($sets * $free * $price, 4),
                    ];
                }

                $toNext = $buy - fmod($total, $buy);
                if ($toNext > 0 && $toNext <= max(1, $buy / 2)) {
                    $hints[] = ['scheme_id' => $s['id'], 'name' => $s['name'],
                        'message' => sprintf('Add %s more %s for %d more free', self::num($toNext), $unit, $free)];
                }
            }

            if ($s['type'] === 'slab_discount') {
                $slabs = $s['rules']['slabs'] ?? [];
                usort($slabs, fn ($a, $b) => $a['min_qty'] <=> $b['min_qty']);

                $best = null;
                $next = null;
                foreach ($slabs as $slab) {
                    if ($total + 1e-9 >= (float) $slab['min_qty']) {
                        $best = $slab;
                    } elseif ($next === null) {
                        $next = $slab;
                    }
                }

                if ($best) {
                    $percent = (float) $best['percent'];
                    $value = array_sum(array_map(fn ($l) => (float) $l['quantity'] * (float) ($l['unit_price_inc_tax'] ?? 0), $qualifying));
                    $applied[] = [
                        'scheme_id' => $s['id'],
                        'name' => $s['name'],
                        'type' => $s['type'],
                        'summary' => sprintf('%s%% off at %s+ %s', self::num($percent), self::num((float) $best['min_qty']), $unit),
                        'effects' => array_map(fn ($l) => ['kind' => 'discount', 'variation_id' => (int) $l['variation_id'], 'percent' => $percent], $qualifying),
                        'benefit' => round($value * $percent / 100, 4),
                    ];
                }

                if ($next) {
                    $hints[] = ['scheme_id' => $s['id'], 'name' => $s['name'],
                        'message' => sprintf('Add %s more %s to get %s%% off', self::num((float) $next['min_qty'] - $total), $unit, self::num((float) $next['percent']))];
                }
            }
        }

        return ['applied' => $applied, 'hints' => $hints];
    }

    /**
     * Progress toward each live target-rebate scheme for one outlet.
     *
     * @return array<int, array<string, mixed>>
     */
    public function progressFor(int $businessId, int $contactId, ?Carbon $today = null): array
    {
        $today ??= Carbon::today();
        $contact = DB::table('contacts')->where('id', $contactId)->first(['customer_group_id', 'customer_route_id']);
        if (! $contact) {
            return [];
        }

        $out = [];
        foreach ($this->active($businessId, $today) as $s) {
            if ($s['type'] !== 'target_rebate' || ! self::audienceMatches($s['audience'], $contact->customer_group_id, $contact->customer_route_id)) {
                continue;
            }

            [$from, $to] = self::window($s, $today);

            $q = DB::table('transaction_sell_lines AS l')
                ->join('transactions AS t', 't.id', '=', 'l.transaction_id')
                ->join('products AS p', 'p.id', '=', 'l.product_id')
                ->where('t.business_id', $businessId)->where('t.contact_id', $contactId)
                ->where('t.type', 'sell')->where('t.status', 'final')
                ->whereBetween('t.transaction_date', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
                ->whereNull('l.parent_sell_line_id');
            self::scopeQuery($q, $s['scope']);
            $achieved = round((float) $q->sum(DB::raw('l.quantity - l.quantity_returned')), 2);

            $tiers = $s['rules']['tiers'] ?? [];
            usort($tiers, fn ($a, $b) => $a['min_qty'] <=> $b['min_qty']);
            if (! $tiers) {
                continue;
            }

            $reached = null;
            $next = null;
            foreach ($tiers as $t) {
                if ($achieved + 1e-9 >= (float) $t['min_qty']) {
                    $reached = $t;
                } elseif ($next === null) {
                    $next = $t;
                }
            }

            $target = (float) ($next['min_qty'] ?? end($tiers)['min_qty']);
            $daysLeft = (int) $today->diffInDays($to, false);

            $out[] = [
                'id' => $s['id'],
                'name' => $s['name'],
                'reached_tier' => $reached['name'] ?? null,
                'next_tier' => $next['name'] ?? null,
                'next_rebate' => $next ? (float) $next['rebate_percent'] : null,
                'achieved' => $achieved,
                'target' => $target,
                'remaining' => max(0, round($target - $achieved, 2)),
                'percent' => $target > 0 ? min(100, round($achieved / $target * 100, 1)) : 100,
                'unit' => $s['rules']['unit_label'] ?? 'units',
                'ends_on' => $to->toDateString(),
                'ends_label' => $daysLeft <= 0 ? 'ends today' : ($daysLeft < 14 ? "$daysLeft days left" : (int) ceil($daysLeft / 7).' weeks left'),
            ];
        }

        return $out;
    }

    /**
     * Record which schemes an invoice used, for rebate and scheme reports.
     *
     * @param  array<string, mixed>  $input  the store's request input
     */
    public function recordFor(int $businessId, object $transaction, array $input): void
    {
        if (empty($input['products']) || ! is_array($input['products'])) {
            return;
        }

        $lines = [];
        foreach ($input['products'] as $p) {
            if (! empty($p['variation_id'])) {
                // The posted quantity includes free units; schemes are earned
                // on what was paid for.
                $lines[] = [
                    'variation_id' => (int) $p['variation_id'],
                    'quantity' => (float) ($p['quantity'] ?? 0) - (float) ($p['scheme_free_qty'] ?? 0),
                    'unit_price_inc_tax' => (float) ($p['unit_price_inc_tax'] ?? 0),
                ];
            }
        }

        $result = $this->evaluate($businessId, $transaction->contact_id ? (int) $transaction->contact_id : null, $lines);

        foreach ($result['applied'] as $a) {
            DB::table('scheme_applications')->insert([
                'business_id' => $businessId,
                'scheme_id' => $a['scheme_id'],
                'transaction_id' => $transaction->id,
                'contact_id' => $transaction->contact_id,
                'benefit_value' => $a['benefit'],
                'details' => json_encode(['summary' => $a['summary'], 'effects' => $a['effects']]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // ------------------------------------------------------------------

    /**
     * @param  array<string, mixed>  $scope
     * @param  array<string, mixed>  $line
     */
    public static function qualifies(array $scope, array $line): bool
    {
        $keys = ['variation_ids' => 'variation_id', 'product_ids' => 'product_id', 'category_ids' => 'category_id', 'brand_ids' => 'brand_id'];

        $any = false;
        foreach ($keys as $list => $field) {
            if (! empty($scope[$list])) {
                $any = true;
                if (in_array((int) ($line[$field] ?? 0), array_map('intval', $scope[$list]), true)) {
                    return true;
                }
            }
        }

        return ! $any;
    }

    /**
     * @param  array<string, mixed>  $audience
     */
    public static function audienceMatches(array $audience, $groupId, $routeId): bool
    {
        if (! empty($audience['customer_group_ids']) && ! in_array((int) $groupId, array_map('intval', $audience['customer_group_ids']), true)) {
            return false;
        }
        if (! empty($audience['route_ids']) && ! in_array((int) $routeId, array_map('intval', $audience['route_ids']), true)) {
            return false;
        }

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public static function hydrate(object $s): array
    {
        return [
            'id' => (int) $s->id,
            'name' => $s->name,
            'type' => $s->type,
            'starts_on' => $s->starts_on,
            'ends_on' => $s->ends_on,
            'is_active' => (bool) $s->is_active,
            'scope' => json_decode($s->scope ?? 'null', true) ?: [],
            'audience' => json_decode($s->audience ?? 'null', true) ?: [],
            'rules' => json_decode($s->rules, true) ?: [],
        ];
    }

    /**
     * @param  array<string, mixed>  $s
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function window(array $s, Carbon $today): array
    {
        return match ($s['rules']['period'] ?? 'quarter') {
            'month' => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
            'scheme' => [Carbon::parse($s['starts_on']), $s['ends_on'] ? Carbon::parse($s['ends_on']) : $today->copy()->endOfQuarter()],
            default => [$today->copy()->startOfQuarter(), $today->copy()->endOfQuarter()],
        };
    }

    /**
     * @param  array<string, mixed>  $scope
     */
    private static function scopeQuery($q, array $scope): void
    {
        $filters = array_filter([
            'l.variation_id' => $scope['variation_ids'] ?? [],
            'l.product_id' => $scope['product_ids'] ?? [],
            'p.category_id' => $scope['category_ids'] ?? [],
            'p.brand_id' => $scope['brand_ids'] ?? [],
        ]);

        if ($filters) {
            $q->where(function ($w) use ($filters) {
                foreach ($filters as $column => $ids) {
                    $w->orWhereIn($column, $ids);
                }
            });
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @return array<int, array<string, mixed>>
     */
    private function withMeta(int $businessId, array $lines): array
    {
        $meta = DB::table('variations AS v')->join('products AS p', 'p.id', '=', 'v.product_id')
            ->where('p.business_id', $businessId)
            ->whereIn('v.id', array_map(fn ($l) => (int) $l['variation_id'], $lines))
            ->get(['v.id', 'v.product_id', 'p.category_id', 'p.brand_id'])
            ->keyBy('id');

        $out = [];
        foreach ($lines as $l) {
            $m = $meta[(int) $l['variation_id']] ?? null;
            if (! $m) {
                continue;
            }
            $out[] = [
                'variation_id' => (int) $l['variation_id'],
                'product_id' => (int) $m->product_id,
                'category_id' => $m->category_id ? (int) $m->category_id : null,
                'brand_id' => $m->brand_id ? (int) $m->brand_id : null,
                'quantity' => (float) $l['quantity'],
                'unit_price_inc_tax' => (float) ($l['unit_price_inc_tax'] ?? 0),
            ];
        }

        return $out;
    }

    private static function num(float $n): string
    {
        return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
    }
}
