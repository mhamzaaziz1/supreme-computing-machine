<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SellPosController;
use App\TaxRate;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Add Sale, rebuilt on Inertia.
 *
 * This screen only gathers input. It posts to SellPosController@store with
 * is_direct_sale = 1 and the field names the legacy form used, so stock
 * movements, invoice numbering, tax, payments and credit-limit checks stay
 * in the one place that already gets them right. Reimplementing that here
 * would be a second source of truth for the most consequential write in the
 * app.
 *
 * Line rows are filled from pos/variation/{variation}/{location}, the same
 * endpoint the legacy screen used to render a product row, so prices, units
 * and tax come from the server rather than being guessed on the client.
 */
class SaleFormController extends Controller
{
    public function __construct(
        private BusinessUtil $businessUtil,
        private TransactionUtil $transactionUtil,
        private ContactUtil $contactUtil,
        private ModuleUtil $moduleUtil,
    ) {}

    public function create(Request $request): Response
    {
        if (! auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }

        $businessId = $request->session()->get('user.business_id');
        $business = $this->businessUtil->getDetails($businessId);
        $walkIn = $this->contactUtil->getWalkInCustomer($businessId);

        $locations = DB::table('business_locations')
            ->where('business_id', $businessId)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($l) => ['id' => (int) $l->id, 'name' => $l->name])
            ->all();

        $permitted = auth()->user()->permitted_locations();
        if ($permitted !== 'all') {
            $locations = array_values(array_filter($locations, fn ($l) => in_array($l['id'], $permitted)));
        }

        $customers = $this->customers($businessId);

        return Inertia::render('Sales/Create', [
            'locations' => $locations,
            'defaultLocationId' => $locations[0]['id'] ?? null,
            'customers' => $customers,
            'prefill' => $this->prefill($request, $businessId, $customers),
            // getWalkInCustomer hands back an array, not a model.
            'walkInCustomerId' => is_array($walkIn) ? ($walkIn['id'] ?? null) : $walkIn?->id,
            'taxRates' => $this->taxRates($businessId),
            'paymentTypes' => $this->paymentTypes($businessId),
            'today' => now()->format('Y-m-d\TH:i'),
            // SellPosController@store parses transaction_date with uf_date,
            // which uses the business's own date format - an ISO string throws
            // "The separation symbol could not be found" and the sale is lost
            // to a generic error. The client formats the picked date to match.
            'dateFormat' => $business->date_format ?: 'm/d/Y',
            'timeFormat' => (int) ($business->time_format ?: 24),
            'currency' => [
                'symbol' => $business->currency_symbol ?? '',
            ],
            'links' => [
                // The legacy endpoints, reused rather than duplicated.
                'searchProducts' => url('products/list'),
                'variation' => url('pos/variation'),
                'store' => action([SellPosController::class, 'store']),
                'cancel' => route('sales.index'),
            ],
        ]);
    }

    /**
     * Customers are sent up front and filtered in the browser: a route-based
     * distribution business has hundreds, not millions, and searching without
     * a round trip per keystroke is the point of the screen.
     *
     * @return array<int, array<string, mixed>>
     */
    private function customers(int $businessId): array
    {
        return DB::table('contacts')
            ->where('business_id', $businessId)
            ->whereIn('type', ['customer', 'both'])
            ->orderBy('name')
            ->limit(2000)
            ->get(['id', 'name', 'mobile', 'contact_id', 'supplier_business_name'])
            ->map(function ($c) {
                // Either half can be blank, so join only what is actually there
                // rather than emitting a stray dash.
                $label = implode(' — ', array_filter([trim((string) $c->name), trim((string) $c->supplier_business_name)]));

                return [
                    'id' => (int) $c->id,
                    'name' => $label !== '' ? $label : ($c->contact_id ?: 'Unnamed contact'),
                    'mobile' => $c->mobile,
                    'code' => $c->contact_id,
                ];
            })
            ->all();
    }

    /**
     * What the form should open with when it is reached from an overlay:
     * the outlet (?contact_id=), and optionally its usual basket (?basket=1)
     * or a repeat of one earlier invoice (?repeat={transaction id}) — the
     * bay's "same as last time" oil change.
     *
     * Only variation ids and quantities are sent; the form fetches each row
     * from the server so prices are today's prices at the chosen location.
     *
     * @param  array<int, array<string, mixed>>  $customers
     * @return array<string, mixed>
     */
    private function prefill(Request $request, int $businessId, array $customers): array
    {
        $contactId = (int) $request->query('contact_id');
        if (! $contactId || ! in_array($contactId, array_column($customers, 'id'), true)) {
            return ['contact_id' => null, 'lines' => [], 'source' => null];
        }

        $lines = [];
        $source = null;

        if ($request->query('repeat')) {
            $lines = DB::table('transaction_sell_lines AS l')
                ->join('transactions AS t', 't.id', '=', 'l.transaction_id')
                ->where('t.business_id', $businessId)
                ->where('t.contact_id', $contactId)
                ->where('t.id', (int) $request->query('repeat'))
                ->whereNull('l.parent_sell_line_id')
                ->get(['l.variation_id', 'l.quantity'])
                ->map(fn ($l) => ['variation_id' => (int) $l->variation_id, 'quantity' => (float) $l->quantity])
                ->all();
            $source = $lines ? 'repeat' : null;
        } elseif ($request->boolean('basket')) {
            $pattern = app(\App\Services\Ops\BuyingPattern::class)->forContact($businessId, $contactId);
            $lines = array_map(fn ($b) => ['variation_id' => $b['variation_id'], 'quantity' => $b['quantity']], $pattern['basket']);
            $source = $lines ? 'basket' : null;
        }

        return ['contact_id' => $contactId, 'lines' => $lines, 'source' => $source];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function taxRates(int $businessId): array
    {
        $rates = TaxRate::forBusinessDropdown($businessId, true, true);
        $out = [];

        foreach (($rates['tax_rates'] ?? []) as $id => $name) {
            if (empty($id)) {
                continue;
            }

            $out[] = [
                'id' => (int) $id,
                'name' => $name,
                'rate' => (float) ($rates['attributes'][$id]['data-rate'] ?? 0),
            ];
        }

        return $out;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function paymentTypes(int $businessId): array
    {
        $types = $this->transactionUtil->payment_types(null, true, $businessId);

        return collect($types)
            ->map(fn ($label, $key) => ['value' => (string) $key, 'label' => $label])
            ->values()
            ->all();
    }
}
