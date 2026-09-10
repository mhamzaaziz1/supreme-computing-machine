<?php

namespace App\Http\Controllers\Ops;

use App\Services\Ops\SchemeEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Manage trade schemes, and evaluate them against a cart.
 */
class SchemeController extends OpsController
{
    public function __construct(private SchemeEngine $engine) {}

    public function index(): JsonResponse
    {
        $this->authorizeManage();
        $b = $this->businessId();

        $usage = DB::table('scheme_applications')->where('business_id', $b)
            ->groupBy('scheme_id')->selectRaw('scheme_id, COUNT(*) AS n, SUM(benefit_value) AS benefit')->get()->keyBy('scheme_id');

        $schemes = DB::table('trade_schemes')->where('business_id', $b)->orderByDesc('is_active')->orderByDesc('id')->get()
            ->map(fn ($s) => SchemeEngine::hydrate($s) + [
                'used' => (int) ($usage[$s->id]->n ?? 0),
                'benefit' => round((float) ($usage[$s->id]->benefit ?? 0), 2),
            ]);

        $variationIds = $schemes->flatMap(fn ($s) => $s['scope']['variation_ids'] ?? [])
            ->merge($schemes->map(fn ($s) => $s['rules']['free_variation_id'] ?? null))->filter()->unique()->values();

        return response()->json([
            'schemes' => $schemes->values(),
            'options' => [
                'categories' => DB::table('categories')->where('business_id', $b)->where('category_type', 'product')->orderBy('name')->get(['id', 'name']),
                'brands' => DB::table('brands')->where('business_id', $b)->orderBy('name')->get(['id', 'name']),
                'groups' => DB::table('customer_groups')->where('business_id', $b)->orderBy('name')->get(['id', 'name']),
                'routes' => DB::table('customer_routes')->where('business_id', $b)->orderBy('name')->get(['id', 'name']),
                'variations' => DB::table('variations AS v')->join('products AS p', 'p.id', '=', 'v.product_id')
                    ->where('p.business_id', $b)->whereIn('v.id', $variationIds)
                    ->get(['v.id', DB::raw("TRIM(CONCAT(p.name, IF(v.name = 'DUMMY', '', CONCAT(' ', v.name)))) AS name")]),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeManage();
        $data = $this->validated($request);

        $id = DB::table('trade_schemes')->insertGetId($this->row($data) + [
            'business_id' => $this->businessId(),
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);

        return response()->json(['message' => 'Scheme created.', 'id' => $id]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $this->authorizeManage();
        $this->find($id);

        DB::table('trade_schemes')->where('id', $id)->update($this->row($this->validated($request)));

        return response()->json(['message' => 'Scheme saved.']);
    }

    public function toggle(int $id): JsonResponse
    {
        $this->authorizeManage();
        $s = $this->find($id);

        DB::table('trade_schemes')->where('id', $id)->update(['is_active' => ! $s->is_active, 'updated_at' => now()]);

        return response()->json(['message' => $s->is_active ? 'Scheme paused.' : 'Scheme running.']);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->authorizeManage();
        $this->find($id);

        if (DB::table('scheme_applications')->where('scheme_id', $id)->exists()) {
            return response()->json(['message' => 'This scheme has been used on invoices. Pause it instead of deleting it.'], 422);
        }

        DB::table('trade_schemes')->where('id', $id)->delete();

        return response()->json(['message' => 'Scheme deleted.']);
    }

    public function evaluate(Request $request): JsonResponse
    {
        if (! $this->isAdmin() && ! auth()->user()->hasAnyPermission(['sell.create', 'direct_sell.access', 'so.create'])) {
            abort(403);
        }

        $data = $request->validate([
            'contact_id' => ['nullable', 'integer'],
            'lines' => ['array', 'max:300'],
            'lines.*.variation_id' => ['required', 'integer'],
            'lines.*.quantity' => ['required', 'numeric'],
            'lines.*.unit_price_inc_tax' => ['nullable', 'numeric'],
        ]);

        return response()->json($this->engine->evaluate($this->businessId(), $data['contact_id'] ?? null, $data['lines'] ?? []));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'type' => ['required', Rule::in(SchemeEngine::TYPES)],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'is_active' => ['boolean'],
            'scope' => ['array'],
            'scope.*' => ['array'],
            'scope.*.*' => ['integer'],
            'audience' => ['array'],
            'audience.*' => ['array'],
            'audience.*.*' => ['integer'],
            'rules' => ['required', 'array'],
            'rules.unit_label' => ['nullable', 'string', 'max:20'],

            'rules.buy_qty' => ['required_if:type,free_goods', 'nullable', 'integer', 'min:1'],
            'rules.free_qty' => ['required_if:type,free_goods', 'nullable', 'integer', 'min:1'],
            'rules.free_variation_id' => ['nullable', 'integer'],

            'rules.slabs' => ['required_if:type,slab_discount', 'nullable', 'array', 'min:1'],
            'rules.slabs.*.min_qty' => ['required', 'numeric', 'gt:0'],
            'rules.slabs.*.percent' => ['required', 'numeric', 'gt:0', 'max:100'],

            'rules.period' => ['required_if:type,target_rebate', 'nullable', 'in:month,quarter,scheme'],
            'rules.tiers' => ['required_if:type,target_rebate', 'nullable', 'array', 'min:1'],
            'rules.tiers.*.name' => ['required', 'string', 'max:30'],
            'rules.tiers.*.min_qty' => ['required', 'numeric', 'gt:0'],
            'rules.tiers.*.rebate_percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ], [
            'rules.buy_qty.required_if' => 'Say how many must be bought.',
            'rules.free_qty.required_if' => 'Say how many are free.',
            'rules.slabs.required_if' => 'Add at least one slab.',
            'rules.tiers.required_if' => 'Add at least one target tier.',
        ]);

        $keep = match ($data['type']) {
            'free_goods' => ['buy_qty', 'free_qty', 'free_variation_id', 'unit_label'],
            'slab_discount' => ['slabs', 'unit_label'],
            'target_rebate' => ['period', 'tiers', 'unit_label'],
        };
        $data['rules'] = array_intersect_key($data['rules'], array_flip($keep));

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function row(array $data): array
    {
        $clean = fn ($arr) => array_filter(array_map(fn ($ids) => array_values(array_unique(array_map('intval', $ids))), $arr ?? []));

        return [
            'name' => $data['name'],
            'type' => $data['type'],
            'starts_on' => $data['starts_on'],
            'ends_on' => $data['ends_on'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'scope' => json_encode($clean($data['scope'] ?? [])),
            'audience' => json_encode($clean($data['audience'] ?? [])),
            'rules' => json_encode($data['rules']),
            'updated_at' => now(),
        ];
    }

    private function find(int $id): object
    {
        $s = DB::table('trade_schemes')->where('business_id', $this->businessId())->where('id', $id)->first();
        abort_unless($s, 404, 'That scheme does not exist.');

        return $s;
    }

    private function authorizeManage(): void
    {
        if (! $this->isAdmin() && ! auth()->user()->can('discount.access')) {
            abort(403, 'You do not have permission to manage schemes.');
        }
    }
}
