<?php

namespace App\Http\Controllers;

use App\CustomerRoute;
use App\SupplyChainVehicle;
use App\Utils\Util;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;

class SupplyChainVehicleController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of all supply chain vehicles
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('customer.view') && !auth()->user()->can('customer.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get customer routes for filter dropdown
        $customer_routes = CustomerRoute::where('business_id', $business_id)
                        ->where('is_active', 1)
                        ->pluck('name', 'id');

        return view('supply_chain_vehicle.index')
                ->with(compact('customer_routes'));
    }

    /**
     * Get all supply chain vehicles for datatable
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllVehicles()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $vehicles = SupplyChainVehicle::where('supply_chain_vehicles.business_id', $business_id)
                        ->select([
                            'supply_chain_vehicles.id', 
                            'supply_chain_vehicles.make', 
                            'supply_chain_vehicles.model', 
                            'supply_chain_vehicles.year', 
                            'supply_chain_vehicles.license_plate', 
                            'supply_chain_vehicles.color',
                            'supply_chain_vehicles.vin', 
                            'supply_chain_vehicles.notes',
                            'supply_chain_vehicles.created_at'
                        ]);

            // Apply license plate filter if provided
            if (!empty(request()->license_plate)) {
                $license_plate = request()->license_plate;
                $vehicles->where('supply_chain_vehicles.license_plate', 'like', "%{$license_plate}%");
            }

            // Apply make filter if provided
            if (!empty(request()->make)) {
                $make = request()->make;
                $vehicles->where('supply_chain_vehicles.make', 'like', "%{$make}%");
            }


            return DataTables::of($vehicles)
                ->addColumn(
                    'action',
                    function ($row) {
                        $actions = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                                __("messages.actions") .
                                '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                <li><a href="#" data-href="' . action([\App\Http\Controllers\SupplyChainVehicleMileageController::class, 'getMileageHistory'], [$row->id]) . '" class="btn-modal view_mileage_history" data-container=".mileage_history_modal"><i class="fas fa-tachometer-alt"></i> ' . __("lang_v1.view_mileage_history") . '</a></li>
                                <li><a href="#" data-href="' . action([\App\Http\Controllers\SupplyChainVehicleController::class, 'edit'], [$row->id]) . '" class="btn-modal" data-container=".vehicle_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>
                                <li><a href="#" data-href="' . action([\App\Http\Controllers\SupplyChainVehicleController::class, 'destroy'], [$row->id]) . '" class="delete_vehicle_button"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>
                            </ul>
                        </div>';

                        return $actions;
                    }
                )
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Get vehicles for a customer route
     *
     * @param int $customer_route_id
     * @return \Illuminate\Http\Response
     */
    public function getVehicles($customer_route_id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $vehicles = SupplyChainVehicle::where('supply_chain_vehicles.customer_route_id', $customer_route_id)
                        ->where('supply_chain_vehicles.business_id', $business_id);

            // Apply license plate filter if provided
            if (!empty(request()->license_plate)) {
                $license_plate = request()->license_plate;
                $vehicles->where('supply_chain_vehicles.license_plate', 'like', "%{$license_plate}%");
            }

            // Apply make filter if provided
            if (!empty(request()->make)) {
                $make = request()->make;
                $vehicles->where('supply_chain_vehicles.make', 'like', "%{$make}%");
            }

            $vehicles = $vehicles->select(['supply_chain_vehicles.id', 'supply_chain_vehicles.make', 
                                'supply_chain_vehicles.model', 'supply_chain_vehicles.year', 
                                'supply_chain_vehicles.license_plate', 'supply_chain_vehicles.color',
                                'supply_chain_vehicles.vin', 'supply_chain_vehicles.notes',
                                'supply_chain_vehicles.created_at']);

            return DataTables::of($vehicles)
                ->addColumn(
                    'action',
                    '<button data-href="{{action([\App\Http\Controllers\SupplyChainVehicleMileageController::class, \'getMileageHistory\'], [$id])}}" class="btn btn-xs btn-info btn-modal view_mileage_history" data-container=".mileage_history_modal"><i class="fas fa-tachometer-alt"></i> @lang("lang_v1.mileage_history")</button>
                    &nbsp;
                    <button data-href="{{action([\App\Http\Controllers\SupplyChainVehicleController::class, \'edit\'], [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".vehicle_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    &nbsp;
                    <button data-href="{{action([\App\Http\Controllers\SupplyChainVehicleController::class, \'destroy\'], [$id])}}" class="btn btn-xs btn-danger delete_vehicle_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
                )
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Get vehicles for a customer route for dropdown
     *
     * @param int $customer_route_id
     * @return \Illuminate\Http\Response
     */
    public function getVehiclesForDropdown($customer_route_id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $vehicles = SupplyChainVehicle::forDropdown($business_id, $customer_route_id);

            return response()->json($vehicles);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');

        // Get all active customer routes
        $customer_routes = CustomerRoute::where('business_id', $business_id)
                        ->where('is_active', 1)
                        ->pluck('name', 'id');

        // Get the selected customer route if provided
        $selected_route_id = request()->get('customer_route_id');

        return view('supply_chain_vehicle.create')
            ->with(compact('customer_routes', 'selected_route_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $input = $request->only(['customer_route_id', 'make', 'model', 'year', 'license_plate', 'color', 'vin', 'notes']);
            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');

            $input['business_id'] = $business_id;
            $input['created_by'] = $user_id;

            $vehicle = SupplyChainVehicle::create($input);

            $output = ['success' => true,
                        'msg' => __("lang_v1.added_success")
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => false,
                        'msg' => __("messages.something_went_wrong")
                    ];
        }

        return $output;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $vehicle = SupplyChainVehicle::findOrFail($id);
        $customer_route = null;

        if (!is_null($vehicle->customer_route_id)) {
            $customer_route = CustomerRoute::findOrFail($vehicle->customer_route_id);
        }

        return view('supply_chain_vehicle.edit')
            ->with(compact('vehicle', 'customer_route'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $input = $request->only(['make', 'model', 'year', 'license_plate', 'color', 'vin', 'notes']);

            $vehicle = SupplyChainVehicle::findOrFail($id);
            $vehicle->fill($input);
            $vehicle->save();

            $output = ['success' => true,
                        'msg' => __("lang_v1.updated_success")
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => false,
                        'msg' => __("messages.something_went_wrong")
                    ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $vehicle = SupplyChainVehicle::findOrFail($id);
            $vehicle->delete();

            $output = ['success' => true,
                        'msg' => __("lang_v1.deleted_success")
                    ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => false,
                        'msg' => __("messages.something_went_wrong")
                    ];
        }

        return $output;
    }
}
