<?php

namespace App\Http\Controllers\Ops;

use App\Http\Controllers\SellPosController;
use App\Services\Ops\FieldCheckIn;
use App\Services\Ops\SaleGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Where the field app's queued actions land when the phone is back online.
 *
 * Each action carries a client id; a repeat of one already done returns
 * the first result rather than doing it twice. Each kind goes through the
 * same code the office screens use — the visit and collect endpoints, and
 * SellPosController@store for orders — so an order taken in a forecourt
 * with no signal is priced, stocked, credit-checked and scheme-recorded
 * exactly like one typed at the counter. Prices come from the server, never
 * from the phone.
 */
class FieldSyncController extends OpsController
{
    public function sync(Request $request): JsonResponse
    {
        $data = $request->validate([
            'client_id' => ['required', 'string', 'max:64'],
            'kind' => ['required', 'in:visit,collect,order'],
            'happened_at' => ['required', 'date'],
            'payload' => ['required', 'array'],
        ]);

        $done = DB::table('field_sync_log')->where('user_id', auth()->id())->where('client_id', $data['client_id'])->first();
        if ($done) {
            return response()->json(['status' => 'done', 'duplicate' => true] + (json_decode($done->result, true) ?: []));
        }

        $at = Carbon::parse($data['happened_at']);
        if ($at->gt(now()->addMinutes(10)) || $at->lt(now()->subDays(7))) {
            return response()->json(['status' => 'failed', 'message' => 'This was recorded more than a week ago or in the future. Check the phone\'s clock.']);
        }

        try {
            $result = match ($data['kind']) {
                'visit' => $this->visit($request, $data['payload'], $at),
                'collect' => $this->collect($request, $data['payload'], $at),
                'order' => $this->order($request, $data['payload'], $at),
            };
        } catch (ValidationException $e) {
            return response()->json(['status' => 'failed', 'message' => collect($e->errors())->flatten()->first()]);
        }

        if ($result['status'] === 'done') {
            DB::table('field_sync_log')->insert([
                'business_id' => $this->businessId(),
                'user_id' => auth()->id(),
                'client_id' => $data['client_id'],
                'kind' => $data['kind'],
                'status' => 'done',
                'result' => json_encode($result),
                'happened_at' => $at,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json($result);
    }

    /**
     * @param  array<string, mixed>  $p
     * @return array<string, mixed>
     */
    private function visit(Request $request, array $p, Carbon $at): array
    {
        $response = app(VisitController::class)->store($this->sub($request, [
            'contact_id' => $p['contact_id'] ?? null,
            'outcome' => $p['outcome'] ?? 'other',
            'notes' => $p['notes'] ?? null,
            'lat' => $p['lat'] ?? null,
            'lng' => $p['lng'] ?? null,
            'accuracy' => $p['accuracy'] ?? null,
            'reason' => $p['reason'] ?? null,
        ]));
        $body = $response->getData(true);

        if ($response->getStatusCode() !== 200) {
            return ['status' => 'failed', 'message' => $body['message'] ?? 'Visit not saved.', 'needs_reason' => isset($body['checkin'])];
        }

        // The visit happened when the phone recorded it, not when it synced.
        if (! empty($body['checkin']['visit_id'])) {
            DB::table('route_visit_logs')->where('id', $body['checkin']['visit_id'])->update(['visit_time' => $at]);
        }

        return ['status' => 'done', 'message' => $body['message']];
    }

    /**
     * @param  array<string, mixed>  $p
     * @return array<string, mixed>
     */
    private function collect(Request $request, array $p, Carbon $at): array
    {
        $response = app(CollectController::class)->store($this->sub($request, [
            'contact_id' => $p['contact_id'] ?? null,
            'amount' => $p['amount'] ?? null,
            'method' => $p['method'] ?? 'cash',
            'paid_on' => $at->toDateTimeString(),
            'note' => trim('Field app'.(! empty($p['note']) ? ' — '.$p['note'] : '')),
            'cheque_number' => $p['cheque_number'] ?? null,
            'cheque_bank' => $p['cheque_bank'] ?? null,
            'cheque_date' => $p['cheque_date'] ?? null,
        ]));
        $body = $response->getData(true);

        return ['status' => 'done', 'message' => $body['message'], 'payment_ref' => $body['payment_ref'] ?? null, 'whatsapp' => $body['whatsapp'] ?? null];
    }

    /**
     * @param  array<string, mixed>  $p
     * @return array<string, mixed>
     */
    private function order(Request $request, array $p, Carbon $at): array
    {
        $b = $this->businessId();
        $contactId = (int) ($p['contact_id'] ?? 0);
        $locationId = (int) ($p['location_id'] ?? 0);
        $this->outlet($contactId);

        $permitted = auth()->user()->permitted_locations();
        if ($permitted !== 'all' && ! in_array($locationId, $permitted)) {
            return ['status' => 'failed', 'message' => 'You cannot sell from that location any more.'];
        }

        $wanted = collect($p['lines'] ?? [])->filter(fn ($l) => (float) ($l['quantity'] ?? 0) > 0)
            ->mapWithKeys(fn ($l) => [(int) $l['variation_id'] => (float) $l['quantity']]);
        if ($wanted->isEmpty()) {
            return ['status' => 'failed', 'message' => 'The order has no products.'];
        }

        $rows = DB::table('variations AS v')->join('products AS p', 'p.id', '=', 'v.product_id')
            ->where('p.business_id', $b)->whereIn('v.id', $wanted->keys())
            ->get(['v.id', 'v.product_id', 'v.default_sell_price', 'v.sell_price_inc_tax', 'p.tax', 'p.enable_stock', 'p.type', 'p.unit_id']);

        $products = [];
        $total = 0;
        foreach ($rows as $i => $r) {
            $qty = $wanted[$r->id];
            $excl = (float) $r->default_sell_price;
            $incl = (float) $r->sell_price_inc_tax ?: $excl;
            $total += $qty * $incl;
            $products[] = [
                'product_id' => $r->product_id, 'variation_id' => $r->id, 'quantity' => $qty,
                'unit_price' => $excl, 'unit_price_inc_tax' => $incl, 'item_tax' => max(0, $incl - $excl),
                'tax_id' => $r->tax, 'enable_stock' => $r->enable_stock, 'product_type' => $r->type,
                'product_unit_id' => $r->unit_id, 'base_unit_multiplier' => 1,
                'line_discount_type' => 'fixed', 'line_discount_amount' => 0, 'sell_line_note' => '',
            ];
        }
        if (count($products) !== $wanted->count()) {
            return ['status' => 'failed', 'message' => 'One of the products no longer exists.'];
        }

        $business = DB::table('business')->where('id', $b)->first(['date_format', 'time_format']);
        $when = $at->copy()->setTimezone(config('app.timezone'))
            ->format(($business->date_format ?: 'm/d/Y').' '.((int) $business->time_format === 12 ? 'h:i A' : 'H:i'));

        // Presence, with the coordinates the phone captured at the time.
        $check = app(FieldCheckIn::class)->check($b, (int) auth()->id(), $contactId, 'place_order',
            isset($p['lat']) ? (float) $p['lat'] : null, isset($p['lng']) ? (float) $p['lng'] : null,
            isset($p['accuracy']) ? (float) $p['accuracy'] : null, false, $p['reason'] ?? null);
        if (! $check['allowed']) {
            return ['status' => 'failed', 'needs_reason' => true, 'message' => ($check['message'] ?? 'Check-in failed').' Add a reason to send it anyway.'];
        }

        $paid = round((float) ($p['payment']['amount'] ?? 0), 2);
        $input = [
            'is_direct_sale' => 1,
            'status' => 'final',
            'sale_type' => 'sell',
            'location_id' => $locationId,
            'contact_id' => $contactId,
            'transaction_date' => $when,
            'discount_type' => 'fixed',
            'discount_amount' => 0,
            'tax_rate_id' => null,
            'shipping_charges' => 0,
            'final_total' => round($total, 2),
            'sale_note' => 'Taken in the field app',
            'products' => $products,
            'checkin_token' => $check['token'],
            'credit_override_token' => $p['credit_override_token'] ?? null,
        ];
        if ($paid > 0) {
            $input['payment'] = [['amount' => $paid, 'method' => $p['payment']['method'] ?? 'cash', 'paid_on' => $when, 'note' => 'Field app']];
        }

        // Credit rules first, so a hold comes back with its details rather
        // than as the store's one-line redirect message.
        $guard = app(SaleGuard::class)->inspect($input, $b);
        if ($guard['blocked'] !== null) {
            return ['status' => 'failed', 'message' => $guard['blocked']['msg'], 'credit_hold' => $guard['blocked']['credit_hold'] ?? null];
        }

        $before = (int) DB::table('transactions')->max('id');
        $sub = $this->sub($request, $input);
        app(SellPosController::class)->store($sub);
        $status = $request->session()->pull('status');

        if (empty($status['success'])) {
            return ['status' => 'failed', 'message' => $status['msg'] ?? 'The order was not saved.'];
        }

        $sale = DB::table('transactions')->where('id', '>', $before)->where('type', 'sell')
            ->where('created_by', auth()->id())->where('contact_id', $contactId)->orderByDesc('id')->first(['id', 'invoice_no', 'final_total']);

        return [
            'status' => 'done',
            'message' => 'Order saved'.($sale ? ' as '.$sale->invoice_no : '').'.',
            'transaction_id' => $sale ? (int) $sale->id : null,
            'invoice_no' => $sale->invoice_no ?? null,
            'total' => $sale ? (float) $sale->final_total : round($total, 2),
        ];
    }

    /**
     * A request for an internal call: same user, same session, new input.
     *
     * Nulls are kept: the legacy store reads keys such as tax_rate_id
     * unconditionally and throws on a missing one.
     *
     * @param  array<string, mixed>  $input
     */
    private function sub(Request $request, array $input): Request
    {
        $sub = Request::create($request->url(), 'POST', $input);
        $sub->setLaravelSession($request->session());
        $sub->setUserResolver(fn () => auth()->user());
        $sub->headers->set('Accept', 'application/json');

        return $sub;
    }
}
