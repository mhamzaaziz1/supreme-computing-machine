<?php

namespace App\Http\Controllers\Ops;

use App\Services\Ops\PrincipalReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The monthly secondary-sales file for the lubricant principal, the
 * principal's targets, and the product pack sizes both depend on.
 */
class PrincipalController extends OpsController
{
    public function __construct(private PrincipalReport $report) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorizeReport();
        [$from, $to, $month] = $this->month($request->query('month'));
        $b = $this->businessId();

        return response()->json([
            'month' => $month,
            'months' => collect(range(0, 11))->map(fn ($i) => Carbon::today()->startOfMonth()->subMonths($i)->format('Y-m'))->all(),
            'brands' => $this->report->byBrand($b, $from, $to),
            'targets' => $this->report->targets($b),
            'options' => [
                'brands' => DB::table('brands')->where('business_id', $b)->orderBy('name')->get(['id', 'name']),
                'categories' => DB::table('categories')->where('business_id', $b)->where('category_type', 'product')->orderBy('name')->get(['id', 'name']),
            ],
            'products_without_pack' => DB::table('products')->where('business_id', $b)->whereNull('pack_litres')->count(),
            'export_url' => route('ops.principal.export', ['month' => $month]),
            'can_manage' => $this->canManage(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorizeReport();
        [$from, $to, $month] = $this->month($request->query('month'));
        $b = $this->businessId();
        $name = preg_replace('/[^A-Za-z0-9]+/', '-', (string) session('business.name')) ?: 'secondary-sales';

        return response()->streamDownload(function () use ($b, $from, $to) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // Excel reads UTF-8 only with a BOM.
            $header = false;
            foreach ($this->report->lines($b, $from, $to) as $row) {
                if (! $header) {
                    fputcsv($out, array_keys($row));
                    $header = true;
                }
                fputcsv($out, $row);
            }
            if (! $header) {
                fputcsv($out, ['No sales in this month']);
            }
            fclose($out);
        }, "{$name}-secondary-sales-{$month}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function storeTarget(Request $request): JsonResponse
    {
        abort_unless($this->canManage(), 403);
        $data = $this->validated($request);

        DB::table('principal_targets')->insert($data + [
            'business_id' => $this->businessId(), 'created_by' => auth()->id(), 'created_at' => now(), 'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Target added.']);
    }

    public function destroyTarget(int $id): JsonResponse
    {
        abort_unless($this->canManage(), 403);
        DB::table('principal_targets')->where('business_id', $this->businessId())->where('id', $id)->delete();

        return response()->json(['message' => 'Target removed.']);
    }

    /**
     * Pack size in litres for one product, edited from the catalogue drawer.
     */
    public function packSize(Request $request, int $productId): JsonResponse
    {
        if (! $this->isAdmin() && ! auth()->user()->can('product.update')) {
            abort(403, 'You do not have permission to edit products.');
        }

        $data = $request->validate(['pack_litres' => ['nullable', 'numeric', 'min:0', 'max:100000']]);

        $updated = DB::table('products')->where('business_id', $this->businessId())->where('id', $productId)
            ->update(['pack_litres' => $data['pack_litres'] ?? null, 'updated_at' => now()]);
        abort_unless($updated !== false, 404);

        return response()->json(['message' => $data['pack_litres'] === null ? 'Pack size cleared.' : 'Pack size saved.']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after_or_equal:starts_on'],
            'measure' => ['required', Rule::in(PrincipalReport::MEASURES)],
            'target' => ['required', 'numeric', 'gt:0'],
            'scope' => ['array'],
            'scope.brand_ids' => ['array'],
            'scope.brand_ids.*' => ['integer'],
            'scope.category_ids' => ['array'],
            'scope.category_ids.*' => ['integer'],
        ]);

        return [
            'name' => $data['name'],
            'starts_on' => $data['starts_on'],
            'ends_on' => $data['ends_on'],
            'measure' => $data['measure'],
            'target' => $data['target'],
            'scope' => json_encode(array_filter($data['scope'] ?? [])),
        ];
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    private function month(?string $month): array
    {
        $m = preg_match('/^\d{4}-\d{2}$/', (string) $month) ? $month : Carbon::today()->subMonthNoOverflow()->format('Y-m');
        $from = Carbon::createFromFormat('Y-m-d', $m.'-01')->startOfDay();

        return [$from, $from->copy()->endOfMonth(), $m];
    }

    private function canManage(): bool
    {
        return $this->isAdmin() || auth()->user()->can('purchase_n_sell_report.view');
    }

    private function authorizeReport(): void
    {
        if (! $this->isAdmin() && ! auth()->user()->hasAnyPermission(['purchase_n_sell_report.view', 'sell.view'])) {
            abort(403, 'You do not have permission to see sales reports.');
        }
    }
}
