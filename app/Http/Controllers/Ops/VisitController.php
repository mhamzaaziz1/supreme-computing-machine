<?php

namespace App\Http\Controllers\Ops;

use App\Exceptions\OpsException;
use App\Services\Ops\FieldCheckIn;
use App\Services\Ops\FieldReturns;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Everything recorded at an outlet other than a sale or a collection: the
 * visit itself and its outcome, damaged stock, used oil collected, and
 * returns (which wait for a manager).
 */
class VisitController extends OpsController
{
    public const OUTCOMES = [
        'order_taken' => 'Order taken',
        'stock_enough' => 'No order — has enough stock',
        'price' => 'No order — price',
        'competitor' => 'No order — bought from a competitor',
        'closed' => 'Shop closed',
        'owner_away' => 'Owner not available',
        'payment_only' => 'Collection only',
        'other' => 'Other',
    ];

    public function __construct(
        private FieldCheckIn $checkIn,
        private FieldReturns $returns,
    ) {}

    public function form(int $id): JsonResponse
    {
        $c = $this->outlet($id, ['name', 'supplier_business_name', 'customer_route_id']);

        return response()->json([
            'outlet' => ['id' => $id, 'name' => self::outletName($c), 'has_route' => (bool) $c->customer_route_id],
            'outcomes' => collect(self::OUTCOMES)->map(fn ($label, $value) => compact('value', 'label'))->values(),
            'invoices' => $this->canReturn() ? $this->returns->returnable($this->businessId(), $id) : [],
            'can_return' => $this->canReturn(),
            'field_user' => $this->checkIn->isFieldUser($this->businessId(), (int) auth()->id()),
        ]);
    }

    /**
     * Log a visit. For a field seller this is a check-in (and fails on an
     * enforced route without a reason, like any other check-in).
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contact_id' => ['required', 'integer'],
            'outcome' => ['required', 'in:'.implode(',', array_keys(self::OUTCOMES))],
            'notes' => ['nullable', 'string', 'max:400'],
            'followup_date' => ['nullable', 'date', 'after_or_equal:today'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0'],
            'reason' => ['nullable', 'string', 'max:190'],
            'photo' => ['nullable', 'image', 'max:6144'],
        ]);

        $c = $this->outlet((int) $data['contact_id'], ['customer_route_id']);
        $notes = trim(self::OUTCOMES[$data['outcome']].(! empty($data['notes']) ? ' — '.$data['notes'] : ''));

        $check = $this->checkIn->check($this->businessId(), (int) auth()->id(), (int) $c->id, 'mark_visit_done',
            isset($data['lat']) ? (float) $data['lat'] : null, isset($data['lng']) ? (float) $data['lng'] : null,
            isset($data['accuracy']) ? (float) $data['accuracy'] : null, false, $data['reason'] ?? null, $notes);

        if (! $check['allowed']) {
            return response()->json(['message' => $check['message'], 'checkin' => $check], 422);
        }

        $photo = $this->storePhoto($request);
        if ($photo && $check['visit_id']) {
            DB::table('route_visit_logs')->where('id', $check['visit_id'])->update(['photo_url' => $photo]);
        }

        $followup = false;
        if (! empty($data['followup_date']) && $c->customer_route_id) {
            DB::table('route_followups')->insert([
                'business_id' => $this->businessId(),
                'customer_route_id' => $c->customer_route_id,
                'contact_id' => $c->id,
                'user_id' => auth()->id(),
                'notes' => $notes,
                'followup_date' => $data['followup_date'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $followup = true;
        }

        $message = $check['logged_visit'] ? 'Visit logged.' : 'Saved. No visit log without a GPS fix and a route.';
        if ($followup) {
            $message .= ' Follow-up set for '.date('d M', strtotime($data['followup_date'])).'.';
        }

        return response()->json(['message' => $message, 'checkin' => $check]);
    }

    public function report(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contact_id' => ['required', 'integer'],
            'kind' => ['required', 'in:damage,used_oil'],
            'variation_id' => ['nullable', 'integer'],
            'quantity' => ['required_if:kind,damage', 'nullable', 'numeric', 'gt:0'],
            'litres' => ['required_if:kind,used_oil', 'nullable', 'numeric', 'gt:0'],
            'rate' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
            'photo' => ['required_if:kind,damage', 'nullable', 'image', 'max:6144'],
        ], [
            'photo.required_if' => 'Take a photo of the damaged stock.',
            'quantity.required_if' => 'Enter how many are damaged.',
            'litres.required_if' => 'Enter the litres collected.',
        ]);

        $this->outlet((int) $data['contact_id']);

        if (! empty($data['variation_id'])) {
            $ok = DB::table('variations AS v')->join('products AS p', 'p.id', '=', 'v.product_id')
                ->where('p.business_id', $this->businessId())->where('v.id', $data['variation_id'])->exists();
            abort_unless($ok, 422, 'That product does not exist.');
        }

        $amount = $data['kind'] === 'used_oil' && isset($data['rate']) ? round((float) $data['litres'] * (float) $data['rate'], 2) : null;

        DB::table('field_reports')->insert([
            'business_id' => $this->businessId(),
            'contact_id' => $data['contact_id'],
            'user_id' => auth()->id(),
            'kind' => $data['kind'],
            'variation_id' => $data['variation_id'] ?? null,
            'quantity' => $data['quantity'] ?? null,
            'litres' => $data['litres'] ?? null,
            'rate' => $data['rate'] ?? null,
            'amount' => $amount,
            'photo' => $this->storePhoto($request),
            'notes' => $data['notes'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => $data['kind'] === 'damage' ? 'Damage report saved.' : sprintf('%s litres of used oil recorded.', rtrim(rtrim(number_format((float) $data['litres'], 2), '0'), '.')),
        ]);
    }

    public function requestReturn(Request $request): JsonResponse
    {
        abort_unless($this->canReturn(), 403, 'You do not have permission to take returns.');

        $data = $request->validate([
            'contact_id' => ['required', 'integer'],
            'transaction_id' => ['required', 'integer'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.sell_line_id' => ['required', 'integer'],
            'lines.*.quantity' => ['required', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'max:190'],
        ], ['reason.required' => 'Say why the stock is coming back.']);

        $this->outlet((int) $data['contact_id']);

        try {
            $id = $this->returns->request($this->businessId(), (int) $data['contact_id'], (int) $data['transaction_id'],
                collect($data['lines'])->mapWithKeys(fn ($l) => [(int) $l['sell_line_id'] => (float) $l['quantity']])->all(),
                $data['reason'], (int) auth()->id());
        } catch (OpsException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Return sent to a manager for approval. Stock moves once it is approved.', 'approval_id' => $id]);
    }

    private function storePhoto(Request $request): ?string
    {
        if (! $request->hasFile('photo')) {
            return null;
        }

        $dir = 'uploads/visits/'.date('Y/m');
        $name = Str::random(24).'.'.$request->file('photo')->extension();
        $request->file('photo')->move(public_path($dir), $name);

        return $dir.'/'.$name;
    }

    private function canReturn(): bool
    {
        return $this->isAdmin() || auth()->user()->hasAnyPermission(['access_sell_return', 'access_own_sell_return']);
    }
}
