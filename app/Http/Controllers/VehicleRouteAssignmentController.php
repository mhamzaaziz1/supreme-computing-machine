<?php

namespace App\Http\Controllers;

use App\CustomerRoute;
use App\SupplyChainVehicle;
use App\Utils\Util;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;

class VehicleRouteAssignmentController extends Controller
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
     * Display a listing of route assignments
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

        return view('vehicle_route_assignment.index')
                ->with(compact('customer_routes'));
    }

    /**
     * Get all vehicle route assignments for datatable
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllAssignments()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $vehicles = SupplyChainVehicle::leftJoin('customer_routes', 'supply_chain_vehicles.customer_route_id', '=', 'customer_routes.id')
                        ->leftJoin('supply_chain_vehicle_mileage', function($join) {
                            $join->on('supply_chain_vehicles.id', '=', 'supply_chain_vehicle_mileage.supply_chain_vehicle_id')
                                 ->on('supply_chain_vehicles.route_assigned_at', '=', 'supply_chain_vehicle_mileage.date');
                        })
                        ->where('supply_chain_vehicles.business_id', $business_id)
                        ->select([
                            'supply_chain_vehicles.id', 
                            'supply_chain_vehicles.make', 
                            'supply_chain_vehicles.model', 
                            'supply_chain_vehicles.year', 
                            'supply_chain_vehicles.license_plate',
                            'supply_chain_vehicles.customer_route_id',
                            'supply_chain_vehicles.route_assigned_at',
                            'customer_routes.name as route_name',
                            'supply_chain_vehicle_mileage.start_mileage',
                            'supply_chain_vehicle_mileage.end_mileage'
                        ]);

            // Apply license plate filter if provided
            if (!empty(request()->license_plate)) {
                $license_plate = request()->license_plate;
                $vehicles->where('supply_chain_vehicles.license_plate', 'like', "%{$license_plate}%");
            }

            // Apply route filter if provided
            if (!empty(request()->customer_route_id)) {
                $customer_route_id = request()->customer_route_id;
                $vehicles->where('supply_chain_vehicles.customer_route_id', $customer_route_id);
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
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                        if (auth()->user()->can('customer.update')) {
                            $actions .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\VehicleRouteAssignmentController::class, 'edit'], [$row->id]) . '" class="btn-modal" data-container=".vehicle_route_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';

                            // Add action to update mileage
                            $actions .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\VehicleRouteAssignmentController::class, 'showUpdateMileageForm'], [$row->id]) . '" class="btn-modal" data-container=".vehicle_route_modal"><i class="fa fa-tachometer"></i> ' . __("lang_v1.update_mileage") . '</a></li>';
                        }

                        if (auth()->user()->can('customer.delete')) {
                            $actions .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\VehicleRouteAssignmentController::class, 'destroy'], [$row->id]) . '" class="delete_assignment_button"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }

                        $actions .= '</ul></div>';

                        return $actions;
                    }
                )
                ->editColumn('route_name', function ($row) {
                    return $row->route_name ?? '<span class="label bg-gray">' . __("lang_v1.no_route_assigned") . '</span>';
                })
                ->editColumn('license_plate', function ($row) {
                    return $row->license_plate ?? '';
                })
                ->editColumn('route_assigned_at', function ($row) {
                    return !empty($row->route_assigned_at) ? \Carbon\Carbon::parse($row->route_assigned_at)->format('m/d/Y') : '';
                })
                ->editColumn('make', function ($row) {
                    $vehicle_info = $row->make;
                    if (!empty($row->model)) {
                        $vehicle_info .= ' ' . $row->model;
                    }
                    if (!empty($row->year)) {
                        $vehicle_info .= ' (' . $row->year . ')';
                    }
                    return $vehicle_info;
                })
                ->removeColumn('id')
                ->removeColumn('model')
                ->removeColumn('year')
                ->removeColumn('customer_route_id')
                ->rawColumns(['action', 'route_name'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new assignment
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get all active customer routes
        $customer_routes = CustomerRoute::where('business_id', $business_id)
                        ->where('is_active', 1)
                        ->pluck('name', 'id');

        // Get all vehicles
        $vehicles = SupplyChainVehicle::where('business_id', $business_id)
                    ->get();

        $formatted_vehicles = $vehicles->mapWithKeys(function ($vehicle) {
            $display_name = $vehicle->make . ' ' . $vehicle->model;
            if (!empty($vehicle->year)) {
                $display_name .= ' (' . $vehicle->year . ')';
            }
            if (!empty($vehicle->license_plate)) {
                $display_name .= ' - ' . $vehicle->license_plate;
            }
            return [$vehicle->id => $display_name];
        });

        return view('vehicle_route_assignment.create')
            ->with(compact('customer_routes', 'formatted_vehicles'));
    }

    /**
     * Store a newly created assignment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['vehicle_id', 'customer_route_id', 'route_assigned_at', 'start_mileage', 'end_mileage', 'notes']);
            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');

            // Validate inputs
            $request->validate([
                'vehicle_id' => 'required|exists:supply_chain_vehicles,id',
                'customer_route_id' => 'required|exists:customer_routes,id',
                'route_assigned_at' => 'nullable|date',
                'start_mileage' => 'nullable|integer|min:0',
                'end_mileage' => 'nullable|integer|min:0|gte:start_mileage'
            ]);

            // Update the vehicle with the new route assignment
            $vehicle = SupplyChainVehicle::where('business_id', $business_id)
                        ->findOrFail($input['vehicle_id']);

            $vehicle->customer_route_id = $input['customer_route_id'];
            $vehicle->route_assigned_at = !empty($input['route_assigned_at']) ? $input['route_assigned_at'] : date('Y-m-d');
            $vehicle->save();

            // Create a mileage record for this route assignment if mileage data is provided
            if (!empty($input['start_mileage']) && !empty($input['end_mileage'])) {
                $mileage_data = [
                    'supply_chain_vehicle_id' => $input['vehicle_id'],
                    'date' => $vehicle->route_assigned_at,
                    'start_mileage' => $input['start_mileage'],
                    'end_mileage' => $input['end_mileage'],
                    'notes' => $input['notes'] ?? 'Route assignment: ' . $vehicle->customerRoute->name,
                    'business_id' => $business_id,
                    'created_by' => $user_id
                ];

                \App\SupplyChainVehicleMileage::create($mileage_data);
            }

            $output = ['success' => true,
                        'msg' => __("lang_v1.route_assigned_successfully")
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
     * Show the form for editing the specified assignment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get the vehicle
        $vehicle = SupplyChainVehicle::where('business_id', $business_id)
                    ->findOrFail($id);

        // Get all active customer routes
        $customer_routes = CustomerRoute::where('business_id', $business_id)
                        ->where('is_active', 1)
                        ->pluck('name', 'id');

        // Format vehicle display name
        $vehicle_display = $vehicle->make . ' ' . $vehicle->model;
        if (!empty($vehicle->year)) {
            $vehicle_display .= ' (' . $vehicle->year . ')';
        }
        if (!empty($vehicle->license_plate)) {
            $vehicle_display .= ' - ' . $vehicle->license_plate;
        }

        // Get the latest mileage record for this vehicle
        $mileage_record = \App\SupplyChainVehicleMileage::where('business_id', $business_id)
                            ->where('supply_chain_vehicle_id', $id)
                            ->where('date', $vehicle->route_assigned_at)
                            ->latest()
                            ->first();

        return view('vehicle_route_assignment.edit')
            ->with(compact('vehicle', 'customer_routes', 'vehicle_display', 'mileage_record'));
    }

    /**
     * Update the specified assignment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['customer_route_id', 'route_assigned_at', 'start_mileage', 'end_mileage', 'notes']);
            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');

            // Validate inputs
            $request->validate([
                'customer_route_id' => 'required|exists:customer_routes,id',
                'route_assigned_at' => 'nullable|date',
                'start_mileage' => 'nullable|integer|min:0',
                'end_mileage' => 'nullable|integer|min:0|gte:start_mileage'
            ]);

            // Update the vehicle with the new route assignment
            $vehicle = SupplyChainVehicle::where('business_id', $business_id)
                        ->findOrFail($id);

            $old_date = $vehicle->route_assigned_at;
            $vehicle->customer_route_id = $input['customer_route_id'];
            $vehicle->route_assigned_at = !empty($input['route_assigned_at']) ? $input['route_assigned_at'] : date('Y-m-d');
            $vehicle->save();

            // Only create or update mileage record if both start_mileage and end_mileage are provided
            if (!empty($input['start_mileage']) && !empty($input['end_mileage'])) {
                // Find existing mileage record or create a new one
                $mileage_record = \App\SupplyChainVehicleMileage::where('business_id', $business_id)
                                    ->where('supply_chain_vehicle_id', $id)
                                    ->where('date', $old_date)
                                    ->latest()
                                    ->first();

                if ($mileage_record) {
                    // Update existing record
                    $mileage_record->date = $vehicle->route_assigned_at;
                    $mileage_record->start_mileage = $input['start_mileage'];
                    $mileage_record->end_mileage = $input['end_mileage'];
                    $mileage_record->notes = $input['notes'] ?? 'Route assignment: ' . $vehicle->customerRoute->name;
                    $mileage_record->save();
                } else {
                    // Create a new mileage record
                    $mileage_data = [
                        'supply_chain_vehicle_id' => $id,
                        'date' => $vehicle->route_assigned_at,
                        'start_mileage' => $input['start_mileage'],
                        'end_mileage' => $input['end_mileage'],
                        'notes' => $input['notes'] ?? 'Route assignment: ' . $vehicle->customerRoute->name,
                        'business_id' => $business_id,
                        'created_by' => $user_id
                    ];

                    \App\SupplyChainVehicleMileage::create($mileage_data);
                }
            }

            $output = ['success' => true,
                        'msg' => __("lang_v1.route_assignment_updated_successfully")
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
     * Remove the route assignment from the specified vehicle.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('customer.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            // Find the vehicle and remove its route assignment
            $vehicle = SupplyChainVehicle::where('business_id', $business_id)
                        ->findOrFail($id);

            $vehicle->customer_route_id = null;
            $vehicle->route_assigned_at = null;
            $vehicle->save();

            $output = ['success' => true,
                        'msg' => __("lang_v1.route_assignment_removed_successfully")
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
     * Show the form for updating mileage for a vehicle route assignment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showUpdateMileageForm($id)
    {
        if (!auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get the vehicle
        $vehicle = SupplyChainVehicle::where('business_id', $business_id)
                    ->findOrFail($id);

        // Format vehicle display name
        $vehicle_display = $vehicle->make . ' ' . $vehicle->model;
        if (!empty($vehicle->year)) {
            $vehicle_display .= ' (' . $vehicle->year . ')';
        }
        if (!empty($vehicle->license_plate)) {
            $vehicle_display .= ' - ' . $vehicle->license_plate;
        }

        // Get the mileage record for this vehicle and assignment date
        $mileage_record = \App\SupplyChainVehicleMileage::where('business_id', $business_id)
                            ->where('supply_chain_vehicle_id', $id)
                            ->where('date', $vehicle->route_assigned_at)
                            ->first();

        return view('vehicle_route_assignment.update_mileage')
            ->with(compact('vehicle', 'vehicle_display', 'mileage_record'));
    }

    /**
     * Update mileage for a vehicle route assignment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateMileage(Request $request, $id)
    {
        if (!auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['start_mileage', 'end_mileage', 'notes']);
            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');

            // Validate inputs
            $request->validate([
                'start_mileage' => 'required|integer|min:0',
                'end_mileage' => 'required|integer|min:0|gte:start_mileage'
            ]);

            // Get the vehicle
            $vehicle = SupplyChainVehicle::where('business_id', $business_id)
                        ->findOrFail($id);

            // Find existing mileage record or create a new one
            $mileage_record = \App\SupplyChainVehicleMileage::where('business_id', $business_id)
                                ->where('supply_chain_vehicle_id', $id)
                                ->where('date', $vehicle->route_assigned_at)
                                ->first();

            if ($mileage_record) {
                // Only update values that are 0 in the database
                // If a value is greater than 0, it cannot be changed through this method
                if ($mileage_record->start_mileage > 0 && $mileage_record->start_mileage != $input['start_mileage']) {
                    return [
                        'success' => false,
                        'msg' => __("lang_v1.value_greater_than_zero_not_editable")
                    ];
                }

                if ($mileage_record->end_mileage > 0 && $mileage_record->end_mileage != $input['end_mileage']) {
                    return [
                        'success' => false,
                        'msg' => __("lang_v1.value_greater_than_zero_not_editable")
                    ];
                }

                // Update the record
                $mileage_record->start_mileage = $input['start_mileage'];
                $mileage_record->end_mileage = $input['end_mileage'];
                $mileage_record->notes = $input['notes'] ?? 'Route assignment: ' . $vehicle->customerRoute->name;
                $mileage_record->save();
            } else {
                // Create a new mileage record
                $mileage_data = [
                    'supply_chain_vehicle_id' => $id,
                    'date' => $vehicle->route_assigned_at,
                    'start_mileage' => $input['start_mileage'],
                    'end_mileage' => $input['end_mileage'],
                    'notes' => $input['notes'] ?? 'Route assignment: ' . $vehicle->customerRoute->name,
                    'business_id' => $business_id,
                    'created_by' => $user_id
                ];

                \App\SupplyChainVehicleMileage::create($mileage_data);
            }

            $output = ['success' => true,
                        'msg' => __("lang_v1.mileage_updated_successfully")
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
