<?php

namespace App\Http\Controllers\Ops;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Record search for the Ctrl-K palette: outlets, invoices, products,
 * vehicles and cheques, each with what opening it should do.
 *
 * Every source applies the same visibility rule as the screen it stands in
 * for, so the palette never reveals a record its list would hide.
 */
class SearchController extends OpsController
{
    private const PER_TYPE = 6;

    public function index(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $only = $request->query('only');

        // "load" or "settle" on its own lists every van.
        if (mb_strlen($q) < 2 && $only !== 'vans') {
            return response()->json(['results' => []]);
        }

        $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
        $results = [];

        if (! $only || $only === 'outlets') {
            $results = [...$results, ...$this->outlets($like)];
        }
        if (! $only || $only === 'vans') {
            $results = [...$results, ...$this->vans($like, (bool) $only)];
        }
        if (! $only) {
            $results = [...$results, ...$this->invoices($like), ...$this->vehicles($like), ...$this->products($like), ...$this->cheques($like)];
        }

        return response()->json(['results' => $results]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function outlets(string $like): array
    {
        if (! $this->canViewCustomers()) {
            return [];
        }

        $q = DB::table('contacts AS c')
            ->leftJoin('customer_routes AS r', 'r.id', '=', 'c.customer_route_id')
            ->where('c.business_id', $this->businessId())
            ->whereIn('c.type', ['customer', 'both'])
            ->where('c.is_default', 0)
            ->where(fn ($w) => $w->where('c.name', 'like', $like)->orWhere('c.supplier_business_name', 'like', $like)
                ->orWhere('c.contact_id', 'like', $like)->orWhere('c.mobile', 'like', $like));

        if (! $this->isAdmin() && ! auth()->user()->can('customer.view')) {
            $user = auth()->user();
            $ids = User::isSelectedContacts($user->id) ? $user->contactAccess->pluck('id')->all() : [];
            $q->where(fn ($w) => $w->where('c.created_by', $user->id)->orWhereIn('c.id', $ids));
        }

        return $q->limit(self::PER_TYPE)
            ->get(['c.id', 'c.name', 'c.supplier_business_name', 'c.contact_id', 'c.mobile', 'r.name AS route'])
            ->map(fn ($c) => [
                'type' => 'outlet',
                'id' => (int) $c->id,
                'label' => self::outletName($c),
                'sublabel' => implode(' · ', array_filter([$c->route, $c->contact_id, $c->mobile])),
            ])->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function invoices(string $like): array
    {
        $user = auth()->user();
        if (! $this->isAdmin() && ! $user->hasAnyPermission(['sell.view', 'direct_sell.view', 'view_own_sell_only'])) {
            return [];
        }

        $q = DB::table('transactions AS t')->leftJoin('contacts AS c', 'c.id', '=', 't.contact_id')
            ->where('t.business_id', $this->businessId())->where('t.type', 'sell')
            ->where('t.invoice_no', 'like', $like);

        $permitted = $user->permitted_locations();
        if ($permitted !== 'all') {
            $q->whereIn('t.location_id', $permitted);
        }
        if (! $this->isAdmin() && ! $user->can('direct_sell.view') && $user->can('view_own_sell_only')) {
            $q->where('t.created_by', $user->id);
        }

        return $q->orderByDesc('t.transaction_date')->limit(self::PER_TYPE)
            ->get(['t.id', 't.invoice_no', 't.final_total', 't.payment_status', 't.transaction_date', 'c.name', 'c.supplier_business_name'])
            ->map(fn ($t) => [
                'type' => 'invoice',
                'id' => (int) $t->id,
                'label' => $t->invoice_no,
                'sublabel' => trim(($t->supplier_business_name ?: $t->name).' · '.$t->payment_status.' · '.substr((string) $t->transaction_date, 0, 10)),
                'amount' => (float) $t->final_total,
            ])->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function products(string $like): array
    {
        if (! $this->isAdmin() && ! auth()->user()->can('product.view')) {
            return [];
        }

        return DB::table('variations AS v')->join('products AS p', 'p.id', '=', 'v.product_id')
            ->where('p.business_id', $this->businessId())
            ->where(fn ($w) => $w->where('p.name', 'like', $like)->orWhere('p.sku', 'like', $like)->orWhere('v.sub_sku', 'like', $like))
            ->limit(self::PER_TYPE)
            ->get(['p.id', 'p.name', 'v.name AS variation', 'v.sub_sku', 'v.sell_price_inc_tax'])
            ->map(fn ($p) => [
                'type' => 'product',
                'id' => (int) $p->id,
                'label' => trim($p->name.(($p->variation && $p->variation !== 'DUMMY') ? ' '.$p->variation : '')),
                'sublabel' => $p->sub_sku,
                'amount' => (float) $p->sell_price_inc_tax,
                'url' => route('catalog.index', ['search' => $p->sub_sku ?: $p->name]),
            ])->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function vehicles(string $like): array
    {
        if (! $this->canViewCustomers()) {
            return [];
        }

        return DB::table('customer_vehicles AS v')->join('contacts AS c', 'c.id', '=', 'v.contact_id')
            ->where('v.business_id', $this->businessId())->where('v.license_plate', 'like', $like)
            ->limit(self::PER_TYPE)
            ->get(['v.id', 'v.license_plate', 'v.make', 'v.model', 'c.name', 'c.supplier_business_name'])
            ->map(fn ($v) => [
                'type' => 'vehicle',
                'id' => (int) $v->id,
                'label' => $v->license_plate,
                'sublabel' => trim(trim($v->make.' '.$v->model).' · '.($v->supplier_business_name ?: $v->name), ' ·'),
            ])->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function vans(string $like, bool $all): array
    {
        if (! $this->isAdmin() && ! auth()->user()->hasAnyPermission(['purchase.create', 'purchase.view'])) {
            return [];
        }

        return DB::table('supply_chain_vehicles AS v')->leftJoin('customer_routes AS r', 'r.id', '=', 'v.customer_route_id')
            ->where('v.business_id', $this->businessId())
            ->when(! $all || $like !== '%%', fn ($q) => $q->where(fn ($w) => $w->where('v.license_plate', 'like', $like)->orWhere('r.name', 'like', $like)))
            ->limit(self::PER_TYPE)
            ->get(['v.id', 'v.license_plate', 'r.name AS route'])
            ->map(fn ($v) => [
                'type' => 'van',
                'id' => (int) $v->id,
                'label' => $v->license_plate ?: 'Van #'.$v->id,
                'sublabel' => $v->route ? 'Van · '.$v->route : 'Van',
            ])->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function cheques(string $like): array
    {
        if (! $this->isAdmin() && ! auth()->user()->can('sell.payments')) {
            return [];
        }

        return DB::table('transaction_payments')
            ->where('business_id', $this->businessId())->whereNull('parent_id')->whereNotNull('cheque_status')
            ->where('cheque_number', 'like', $like)
            ->limit(self::PER_TYPE)
            ->get(['id', 'cheque_number', 'cheque_bank', 'amount', 'cheque_status'])
            ->map(fn ($p) => [
                'type' => 'cheque',
                'id' => (int) $p->id,
                'label' => 'Cheque #'.$p->cheque_number,
                'sublabel' => trim(($p->cheque_bank ? $p->cheque_bank.' · ' : '').$p->cheque_status),
                'amount' => (float) $p->amount,
            ])->all();
    }
}
