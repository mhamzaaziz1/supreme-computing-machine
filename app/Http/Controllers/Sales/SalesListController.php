<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SellController;
use App\Http\Controllers\SellPosController;
use App\Http\Controllers\SellReturnController;
use App\Utils\BusinessUtil;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The sales list, rebuilt on Inertia.
 *
 * The legacy screen ran through TransactionUtil::getListSells(), which joins
 * eight tables and selects forty-odd columns so that DataTables can render
 * eight of them, then re-runs the whole thing on every sort, filter and page
 * step. This asks for the columns the list actually shows and computes the
 * amount paid with one correlated subquery, so paging is a single lean query.
 *
 * Permission scoping is ported from SellController@index rather than
 * reinvented: the same 403 gate, the same location restriction, the same
 * own-sales/commission-agent narrowing and the same payment-status limits.
 */
class SalesListController extends Controller
{
    /** Page sizes the UI is allowed to ask for. */
    private const PER_PAGE = [25, 50, 100];

    /**
     * The list serves four screens that differ only in what they pin.
     *
     * "Quotation" is not a status: a quotation is a draft carrying
     * is_quotation = 1, and a plain draft is the same status with the flag
     * clear. The only statuses a sell ever has are draft and final.
     *
     * @var array<string, array<string, mixed>>
     */
    private const VIEWS = [
        'all' => [
            'heading' => 'Sales',
            'noun' => 'invoice',
            'can' => ['sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view',
                'view_own_sell_only', 'view_commission_agent_sell',
                'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping'],
        ],
        'pos' => [
            'heading' => 'POS sales',
            'noun' => 'sale',
            'can' => ['sell.view'],
        ],
        'drafts' => [
            'heading' => 'Drafts',
            'noun' => 'draft',
            'can' => ['draft.view_all', 'draft.view_own'],
        ],
        'quotations' => [
            'heading' => 'Quotations',
            'noun' => 'quotation',
            'can' => ['quotation.view_all', 'quotation.view_own'],
        ],
    ];

    public function __construct(private BusinessUtil $businessUtil) {}

    public function index(Request $request, string $view = 'all'): Response
    {
        $view = isset(self::VIEWS[$view]) ? $view : 'all';
        $isAdmin = $this->businessUtil->is_admin(auth()->user());

        if (! $isAdmin && ! auth()->user()->hasAnyPermission(self::VIEWS[$view]['can'])) {
            abort(403, 'Unauthorized action.');
        }

        $businessId = $request->session()->get('user.business_id');
        $filters = $this->filters($request);

        $rows = $this->query($businessId, $isAdmin, $filters, $view)
            ->orderByDesc('t.transaction_date')
            ->orderByDesc('t.id')
            ->paginate($filters['per_page'])
            ->withQueryString();

        $can = $this->abilities($isAdmin);

        return Inertia::render('Sales/Index', [
            'view' => $view,
            'heading' => self::VIEWS[$view]['heading'],
            'noun' => self::VIEWS[$view]['noun'],
            'filters' => $filters,
            'summary' => $this->summary($businessId, $isAdmin, $filters, $view),
            'locations' => $this->locations($businessId),
            'sells' => [
                'data' => collect($rows->items())->map(fn ($row) => $this->present($row, $can))->all(),
                'from' => $rows->firstItem(),
                'to' => $rows->lastItem(),
                'total' => $rows->total(),
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
            ],
            'perPageOptions' => self::PER_PAGE,
            'links' => [
                'addSale' => route('sales.create'),
                'pos' => action([SellPosController::class, 'create']),
                'show' => url('sells'),
            ],
        ]);
    }

    /**
     * The invoice behind one row, as JSON for the detail drawer.
     *
     * The legacy "View" action loaded a Blade fragment into a Bootstrap
     * modal. The Inertia shell ships neither jQuery nor Bootstrap, so this
     * returns data and the drawer renders it.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $isAdmin = $this->businessUtil->is_admin(auth()->user());

        if (! $this->abilities($isAdmin)['view']) {
            abort(403, 'Unauthorized action.');
        }

        $businessId = $request->session()->get('user.business_id');

        // Re-run the scoped list query for this one id, so a user cannot read
        // an invoice from a location or seller they are not allowed to see.
        $sell = $this->query($businessId, $isAdmin, $this->filters(new Request))
            ->where('t.id', $id)
            ->first();

        if (! $sell) {
            abort(404);
        }

        $lines = DB::table('transaction_sell_lines AS l')
            ->leftJoin('products AS p', 'p.id', '=', 'l.product_id')
            ->leftJoin('variations AS v', 'v.id', '=', 'l.variation_id')
            ->leftJoin('units AS un', 'un.id', '=', 'l.sub_unit_id')
            ->where('l.transaction_id', $id)
            ->whereNull('l.parent_sell_line_id')
            ->select(
                'l.id',
                'p.name AS product',
                'v.name AS variation',
                'v.sub_sku AS sku',
                'un.short_name AS unit',
                'l.quantity',
                'l.unit_price_inc_tax AS unit_price',
                DB::raw('(l.quantity * l.unit_price_inc_tax) AS line_total'),
            )
            ->get()
            ->map(fn ($l) => [
                'id' => (int) $l->id,
                'product' => trim($l->product.(($l->variation && $l->variation !== 'DUMMY') ? ' ('.$l->variation.')' : '')),
                'sku' => $l->sku,
                'unit' => $l->unit,
                'quantity' => (float) $l->quantity,
                'unit_price' => (float) $l->unit_price,
                'line_total' => (float) $l->line_total,
            ])
            ->all();

        $payments = DB::table('transaction_payments')
            ->where('transaction_id', $id)
            ->where('is_return', 0)
            ->orderBy('paid_on')
            ->get(['id', 'amount', 'method', 'paid_on', 'payment_ref_no'])
            ->map(fn ($p) => [
                'id' => (int) $p->id,
                'amount' => (float) $p->amount,
                'method' => $p->method,
                'paid_on' => $p->paid_on,
                'ref' => $p->payment_ref_no,
            ])
            ->all();

        return response()->json([
            'sell' => $this->present($sell, $this->abilities($isAdmin)),
            'lines' => $lines,
            'payments' => $payments,
        ]);
    }

    /**
     * Read the filter state off the query string, clamped to what the list
     * supports so a hand-edited URL cannot widen the query.
     *
     * @return array<string, mixed>
     */
    private function filters(Request $request): array
    {
        $perPage = (int) $request->input('per_page', self::PER_PAGE[0]);

        return [
            'search' => trim((string) $request->input('search', '')) ?: null,
            'date_from' => $this->date($request->input('date_from')),
            'date_to' => $this->date($request->input('date_to')),
            'status' => $this->oneOf($request->input('status'), ['final', 'draft', 'quotation']),
            'payment_status' => $this->oneOf($request->input('payment_status'), ['paid', 'due', 'partial', 'overdue']),
            'location_id' => $request->filled('location_id') ? (int) $request->input('location_id') : null,
            'per_page' => in_array($perPage, self::PER_PAGE, true) ? $perPage : self::PER_PAGE[0],
        ];
    }

    private function date(mixed $value): ?string
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $value) ? (string) $value : null;
    }

    /**
     * @param  array<int, string>  $allowed
     */
    private function oneOf(mixed $value, array $allowed): ?string
    {
        return in_array($value, $allowed, true) ? $value : null;
    }

    /**
     * The list query: only the columns the table renders.
     *
     * @param  array<string, mixed>  $filters
     */
    private function query(int $businessId, bool $isAdmin, array $filters, string $view = 'all'): \Illuminate\Database\Query\Builder
    {
        $sells = DB::table('transactions AS t')
            ->leftJoin('contacts AS c', 'c.id', '=', 't.contact_id')
            ->join('business_locations AS bl', 'bl.id', '=', 't.location_id')
            ->leftJoin('users AS u', 'u.id', '=', 't.created_by')
            ->where('t.business_id', $businessId)
            ->where('t.type', 'sell')
            // Project invoices live in the same table but belong to their own
            // screen, exactly as the legacy list excluded them.
            ->where(fn ($q) => $q->where('t.sub_type', '!=', 'project_invoice')->orWhereNull('t.sub_type'))
            ->select(
                't.id',
                't.invoice_no',
                't.transaction_date',
                't.status',
                't.payment_status',
                't.final_total',
                't.is_direct_sale',
                't.is_quotation',
                't.shipping_status',
                't.document',
                't.contact_id',
                'c.name AS customer',
                'c.contact_id AS customer_code',
                'bl.name AS location',
                DB::raw("TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) AS created_by"),
                DB::raw('COALESCE((
                    SELECT SUM(tp.amount) FROM transaction_payments tp
                    WHERE tp.transaction_id = t.id AND tp.is_return = 0
                ), 0) AS paid'),
            );

        $this->pin($sells, $view);
        $this->scope($sells, $isAdmin);
        $this->applyFilters($sells, $filters);

        return $sells;
    }

    /**
     * What each screen fixes regardless of the user's filters.
     */
    private function pin(\Illuminate\Database\Query\Builder $sells, string $view): void
    {
        match ($view) {
            // A POS sale is one raised at the till rather than on the sell form.
            'pos' => $sells->where('t.is_direct_sale', 0)->where('t.status', 'final'),
            'drafts' => $sells->where('t.status', 'draft')->where('t.is_quotation', 0),
            'quotations' => $sells->where('t.status', 'draft')->where('t.is_quotation', 1),
            default => null,
        };
    }

    /**
     * Narrow the query to what this user is allowed to see. Ported verbatim in
     * behaviour from SellController@index.
     */
    private function scope(\Illuminate\Database\Query\Builder $sells, bool $isAdmin): void
    {
        $permitted = auth()->user()->permitted_locations();
        if ($permitted !== 'all') {
            $sells->whereIn('t.location_id', $permitted);
        }

        if (! auth()->user()->can('direct_sell.view')) {
            $userId = session('user.id');

            $sells->where(function ($q) use ($userId) {
                if (auth()->user()->hasAnyPermission(['view_own_sell_only', 'access_own_shipping'])) {
                    $q->where('t.created_by', $userId);
                }

                if (auth()->user()->hasAnyPermission(['view_commission_agent_sell', 'access_commission_agent_shipping'])) {
                    $q->orWhere('t.commission_agent', $userId);
                }
            });
        }

        if ($isAdmin) {
            return;
        }

        // A user restricted to, say, paid sales must not see the others even
        // by clearing the filter.
        $visible = array_values(array_filter([
            auth()->user()->can('view_paid_sells_only') ? 'paid' : null,
            auth()->user()->can('view_due_sells_only') ? 'due' : null,
            auth()->user()->can('view_partial_sells_only') ? 'partial' : null,
            auth()->user()->can('view_overdue_sells_only') ? 'overdue' : null,
        ]));

        if ($visible) {
            $sells->whereIn('t.payment_status', $visible);
        }
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters(\Illuminate\Database\Query\Builder $sells, array $filters): void
    {
        if ($filters['search']) {
            $term = '%'.$filters['search'].'%';

            $sells->where(fn ($q) => $q
                ->where('t.invoice_no', 'like', $term)
                ->orWhere('c.name', 'like', $term)
                ->orWhere('c.contact_id', 'like', $term)
                ->orWhere('c.mobile', 'like', $term));
        }

        if ($filters['date_from']) {
            $sells->whereDate('t.transaction_date', '>=', $filters['date_from']);
        }

        if ($filters['date_to']) {
            $sells->whereDate('t.transaction_date', '<=', $filters['date_to']);
        }

        // "Quotation" is a draft with the flag set, not a status of its own.
        match ($filters['status']) {
            'final' => $sells->where('t.status', 'final'),
            'draft' => $sells->where('t.status', 'draft')->where('t.is_quotation', 0),
            'quotation' => $sells->where('t.status', 'draft')->where('t.is_quotation', 1),
            default => null,
        };

        if ($filters['payment_status']) {
            $sells->where('t.payment_status', $filters['payment_status']);
        }

        if ($filters['location_id']) {
            $sells->where('t.location_id', $filters['location_id']);
        }
    }

    /**
     * Totals for the whole filtered set, not just the visible page — the
     * figures are only useful if they answer for everything matched.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, float|int>
     */
    private function summary(int $businessId, bool $isAdmin, array $filters, string $view = 'all'): array
    {
        $totals = $this->query($businessId, $isAdmin, $filters, $view)
            ->selectRaw('COUNT(*) AS invoices, COALESCE(SUM(t.final_total), 0) AS total, COALESCE(SUM((
                SELECT SUM(tp.amount) FROM transaction_payments tp
                WHERE tp.transaction_id = t.id AND tp.is_return = 0
            )), 0) AS paid')
            ->first();

        $total = (float) ($totals->total ?? 0);
        $paid = (float) ($totals->paid ?? 0);

        return [
            'invoices' => (int) ($totals->invoices ?? 0),
            'total' => $total,
            'paid' => $paid,
            'due' => round($total - $paid, 4),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function locations(int $businessId): array
    {
        $permitted = auth()->user()->permitted_locations();

        return DB::table('business_locations')
            ->where('business_id', $businessId)
            ->where('is_active', 1)
            ->when($permitted !== 'all', fn ($q) => $q->whereIn('id', $permitted))
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($l) => ['id' => (int) $l->id, 'name' => $l->name])
            ->all();
    }

    /**
     * Which actions this user may take at all. Evaluated once per request
     * rather than per row, since only edit/delete vary by row.
     *
     * @return array<string, bool>
     */
    private function abilities(bool $isAdmin): array
    {
        $user = auth()->user();

        return [
            'view' => $isAdmin || $user->hasAnyPermission(['sell.view', 'direct_sell.view', 'view_own_sell_only']),
            'edit_pos' => $isAdmin || $user->can('sell.update'),
            'edit_direct' => $isAdmin || $user->can('direct_sell.update'),
            'delete_pos' => $isAdmin || $user->can('sell.delete'),
            'delete_direct' => $isAdmin || $user->can('direct_sell.delete'),
            'print' => $isAdmin || $user->can('print_invoice'),
            'pdf' => config('constants.enable_download_pdf') && ($isAdmin || $user->can('print_invoice')),
            'sell_return' => $isAdmin || $user->hasAnyPermission(['sell.create', 'direct_sell.access']),
            'document' => $isAdmin || $user->hasAnyPermission(['sell.view', 'direct_sell.access']),
            'pos_payment' => $isAdmin || $user->can('edit_pos_payment'),
        ];
    }

    /**
     * @param  array<string, bool>  $can
     * @return array<string, mixed>
     */
    private function present(object $row, array $can): array
    {
        $total = (float) $row->final_total;
        $paid = (float) $row->paid;
        $isPos = (int) $row->is_direct_sale === 0;

        // A POS sale is edited and deleted through the POS screen and under
        // the sell.* permissions; a direct sale through the sell form under
        // direct_sell.*. The legacy list drew the same distinction.
        $actions = [
            'edit' => ($isPos ? $can['edit_pos'] : $can['edit_direct'])
                ? ($isPos
                    ? action([SellPosController::class, 'edit'], [$row->id])
                    : action([SellController::class, 'edit'], [$row->id]))
                : null,
            'delete' => ($isPos ? $can['delete_pos'] : $can['delete_direct'])
                ? action([SellPosController::class, 'destroy'], [$row->id])
                : null,
            'show' => $can['view'] ? route('sales.show', [$row->id]) : null,
            'print' => $can['print'] ? route('sell.printInvoice', [$row->id]) : null,
            'pdf' => $can['pdf'] ? route('sell.downloadPdf', [$row->id]) : null,
            'packingPdf' => $can['pdf'] && ! empty($row->shipping_status)
                ? route('packing.downloadPdf', [$row->id])
                : null,
            'sellReturn' => $can['sell_return'] ? action([SellReturnController::class, 'add'], [$row->id]) : null,
            'posPayment' => $isPos && $can['pos_payment'] ? route('edit-pos-payment', [$row->id]) : null,
            'document' => $can['document'] && ! empty($row->document)
                ? url('uploads/documents/'.$row->document)
                : null,
        ];

        return [
            'id' => (int) $row->id,
            'invoice_no' => $row->invoice_no,
            'date' => $row->transaction_date,
            'status' => $row->status,
            'is_quotation' => (bool) $row->is_quotation,
            'payment_status' => $row->payment_status,
            'total' => $total,
            'paid' => $paid,
            'due' => round($total - $paid, 4),
            'contact_id' => $row->contact_id ? (int) $row->contact_id : null,
            'customer' => $row->customer,
            'customer_code' => $row->customer_code,
            'location' => $row->location,
            'created_by' => $row->created_by ?: null,
            'actions' => array_filter($actions),
        ];
    }
}
