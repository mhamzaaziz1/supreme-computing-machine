<?php

namespace App\Http\Controllers;

use App\VehicleMileageRecord;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VehicleMileageController extends Controller
{
    /**
     * Get the last mileage record for a vehicle
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $vehicle_id
     * @return \Illuminate\Http\Response
     */
    public function getLastMileageRecord(Request $request, $vehicle_id)
    {
        if (!auth()->user()->can('sell.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = $request->session()->get('user.business_id');

            $last_record = VehicleMileageRecord::getLastMileageRecord($business_id, $vehicle_id);

            $data = [
                'previous_mileage' => $last_record ? $last_record->next_mileage : null
            ];

            return response()->json($data);
        }
    }

    /**
     * Get mileage history for a vehicle
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $vehicle_id
     * @return \Illuminate\Http\Response
     */
    public function getMileageHistory(Request $request, $vehicle_id)
    {
        if (!auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = $request->session()->get('user.business_id');

            $mileage_records = VehicleMileageRecord::where('business_id', $business_id)
                ->where('vehicle_id', $vehicle_id)
                ->with(['invoice', 'vehicle'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Disable debug output for this response to ensure clean JSON
            $previous_debug = config('app.debug');
            config(['app.debug' => false]);

            $response = DataTables::of($mileage_records)
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : '';
                })
                ->editColumn('invoice_id', function ($row) {
                    if ($row->invoice) {
                        return '<a href="' . action([\App\Http\Controllers\SellController::class, 'show'], [$row->invoice_id]) . '" target="_blank">' . $row->invoice->invoice_no . '</a>';
                    }
                    return '';
                })
                ->editColumn('previous_mileage', function ($row) {
                    return $row->previous_mileage ?? '';
                })
                ->editColumn('oil_change_mileage', function ($row) {
                    return $row->oil_change_mileage ?? '';
                })
                ->editColumn('next_mileage', function ($row) {
                    return $row->next_mileage ?? '';
                })
                ->rawColumns(['invoice_id'])
                ->make(true);

            // Restore debug setting
            config(['app.debug' => $previous_debug]);

            return $response;
        }

        $vehicle = \App\CustomerVehicle::findOrFail($vehicle_id);
        return view('vehicle_mileage.history_modal')
            ->with(compact('vehicle'));
    }
}
