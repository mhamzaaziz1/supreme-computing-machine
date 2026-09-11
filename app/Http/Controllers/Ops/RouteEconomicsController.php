<?php

namespace App\Http\Controllers\Ops;

use App\Services\Ops\RouteEconomics;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Cost to serve per route. Margin figures, so gated like the P&L report.
 */
class RouteEconomicsController extends OpsController
{
    public function index(Request $request, RouteEconomics $economics): JsonResponse
    {
        if (! $this->isAdmin() && ! auth()->user()->can('profit_loss_report.view')) {
            abort(403, 'You do not have permission to see route margins.');
        }

        $days = in_array((int) $request->query('days'), [7, 30, 90], true) ? (int) $request->query('days') : 30;
        $to = Carbon::today();

        return response()->json($economics->forPeriod($this->businessId(), $to->copy()->subDays($days - 1), $to) + ['days' => $days]);
    }
}
