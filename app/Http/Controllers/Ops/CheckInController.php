<?php

namespace App\Http\Controllers\Ops;

use App\Services\Ops\FieldCheckIn;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * A seller proving they are at the outlet: before billing it, before
 * collecting from it, or simply to log a visit.
 */
class CheckInController extends OpsController
{
    public function __construct(private FieldCheckIn $checkIn) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contact_id' => ['required', 'integer'],
            'action' => ['required', 'in:place_order,record_payment,check_in,mark_visit_done,return'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'accuracy' => ['nullable', 'numeric', 'min:0'],
            'mock' => ['boolean'],
            'reason' => ['nullable', 'string', 'max:190'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->outlet((int) $data['contact_id']);

        return response()->json($this->checkIn->check(
            $this->businessId(),
            (int) auth()->id(),
            (int) $data['contact_id'],
            $data['action'],
            isset($data['lat']) ? (float) $data['lat'] : null,
            isset($data['lng']) ? (float) $data['lng'] : null,
            isset($data['accuracy']) ? (float) $data['accuracy'] : null,
            (bool) ($data['mock'] ?? false),
            $data['reason'] ?? null,
            $data['notes'] ?? null,
        ));
    }
}
