<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SellController;
use App\Http\Controllers\SellPosController;
use App\Utils\BusinessUtil;
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

    public function __construct(private BusinessUtil $businessUtil) {}

    public function index(Request $request): Response
    {
        $isAdmin = $this->businessUtil->is_admin(auth()->user());

        if (! $isAdmin && ! auth()->user()->hasAnyPermission([
            'sell.view', 'sell.create', 'direct_sell.access', 'direct_sell.view',
            'view_own_sell_only', 'view_commission_agent_sell',
            'access_shipping', 'access_own_shipping', 'access_commission_agent_shipping',
        ])) {
            abort(403, 'Unauthorized action.');
        }

        $businessId = $request->session()->get('user.business_id');
        $filters = $this->filters($request);

        $rows = $this->query($businessId, $isAdmin, $filters)
            ->orderByDesc('t.transaction_date')
            ->orderByDesc('t.id')
            ->paginate($filters['per_page'])
            ->withQueryString();

        return Inertia::render('Sales/Index', [
            'filters' => $filters,
            'summary' => $this->summary($businessId, $isAdmin, $filters),
            'locations' => $this->locations($businessId),
            'sells' => [
                'data' => collect($rows->items())->map($this->present(...))->all(),
                'from' => $rows->firstItem(),
                'to' => $rows->lastItem(),
                'total' => $rows->total(),
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
            ],
            'perPageOptions' => self::PER_PAGE,
            'links' => [
                'addSale' => action([SellController::class, 'create']),
                'pos' => action([SellPosController::class, 'create']),
                'show' => url('sells'),
            ],
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
    private function query(int $businessId, bool $isAdmin, array $filters): \Illuminate\Database\Query\Builder
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
                'c.name AS customer',
                'c.contact_id AS customer_code',
                'bl.name AS location',
                DB::raw("TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))) AS created_by"),
                DB::raw('COALESCE((
                    SELECT SUM(tp.amount) FROM transaction_payments tp
                    WHERE tp.transaction_id = t.id AND tp.is_return = 0
                ), 0) AS paid'),
            );

        $this->scope($sells, $isAdmin);
        $this->applyFilters($sells, $filters);

        return $sells;
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

        if ($filters['status']) {
            $sells->where('t.status', $filters['status']);
        }

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
    private function summary(int $businessId, bool $isAdmin, array $filters): array
    {
        $totals = $this->query($businessId, $isAdmin, $filters)
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
     * @return array<string, mixed>
     */
    private function present(object $row): array
    {
        $total = (float) $row->final_total;
        $paid = (float) $row->paid;

        return [
            'id' => (int) $row->id,
            'invoice_no' => $row->invoice_no,
            'date' => $row->transaction_date,
            'status' => $row->status,
            'payment_status' => $row->payment_status,
            'total' => $total,
            'paid' => $paid,
            'due' => round($total - $paid, 4),
            'customer' => $row->customer,
            'customer_code' => $row->customer_code,
            'location' => $row->location,
            'created_by' => $row->created_by ?: null,
        ];
    }
}
