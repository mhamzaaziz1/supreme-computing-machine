<?php

namespace App\Http\Controllers;

use App\Contact;
use App\CustomerVehicle;
use App\Utils\Util;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;

class CustomerVehicleController extends Controller
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
     * Display a listing of all vehicles
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('customer.view') && !auth()->user()->can('customer.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get customers for filter dropdown
        $customers = Contact::where('business_id', $business_id)
                        ->whereIn('type', ['customer', 'both'])
                        ->pluck('name', 'id');

        return view('customer_vehicle.index')
                ->with(compact('customers'));
    }

    /**
     * Get all vehicles for datatable
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllVehicles()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $vehicles = CustomerVehicle::join('contacts', 'customer_vehicles.contact_id', '=', 'contacts.id')
                        ->where('customer_vehicles.business_id', $business_id)
                        ->select([
                            'customer_vehicles.id', 
                            'customer_vehicles.make', 
                            'customer_vehicles.model', 
                            'customer_vehicles.year', 
                            'customer_vehicles.license_plate', 
                            'customer_vehicles.color',
                            'customer_vehicles.vin', 
                            'customer_vehicles.notes',
                            'customer_vehicles.created_at',
                            'contacts.name as customer_name',
                            'contacts.id as contact_id'
                        ]);

            // Apply license plate filter if provided
            if (!empty(request()->license_plate)) {
                $license_plate = request()->license_plate;
                $vehicles->where('customer_vehicles.license_plate', 'like', "%{$license_plate}%");
            }

            // Apply make filter if provided
            if (!empty(request()->make)) {
                $make = request()->make;
                $vehicles->where('customer_vehicles.make', 'like', "%{$make}%");
            }

            // Apply customer filter if provided
            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $vehicles->where('customer_vehicles.contact_id', $customer_id);
            }

            return DataTables::of($vehicles)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">
                            <li><a href="{{action([\App\Http\Controllers\ContactController::class, \'show\'], [$contact_id])}}?view_type=vehicles"><i class="fas fa-user" aria-hidden="true"></i> @lang("lang_v1.view_customer")</a></li>
                            <li><a href="#" data-href="{{action([\App\Http\Controllers\VehicleMileageController::class, \'getMileageHistory\'], [$id])}}" class="btn-modal view_mileage_history" data-container=".mileage_history_modal"><i class="fas fa-tachometer-alt"></i> @lang("lang_v1.view_mileage_history")</a></li>
                            <li><a href="#" data-href="{{action([\App\Http\Controllers\CustomerVehicleController::class, \'edit\'], [$id])}}" class="btn-modal" data-container=".vehicle_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a></li>
                            <li><a href="#" data-href="{{action([\App\Http\Controllers\CustomerVehicleController::class, \'destroy\'], [$id])}}" class="delete_vehicle_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</a></li>
                        </ul>
                    </div>'
                )
                ->editColumn('customer_name', function ($row) {
                    return '<a href="' . action([\App\Http\Controllers\ContactController::class, 'show'], [$row->contact_id]) . '?view_type=vehicles">' . $row->customer_name . '</a>';
                })
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->removeColumn('id')
                ->removeColumn('contact_id')
                ->rawColumns(['action', 'customer_name'])
                ->make(true);
        }
    }

    /**
     * Get vehicles for a contact
     *
     * @param int $contact_id
     * @return \Illuminate\Http\Response
     */
    public function getVehicles($contact_id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $vehicles = CustomerVehicle::where('customer_vehicles.contact_id', $contact_id)
                        ->where('customer_vehicles.business_id', $business_id);

            // Apply license plate filter if provided
            if (!empty(request()->license_plate)) {
                $license_plate = request()->license_plate;
                $vehicles->where('customer_vehicles.license_plate', 'like', "%{$license_plate}%");
            }

            // Apply make filter if provided
            if (!empty(request()->make)) {
                $make = request()->make;
                $vehicles->where('customer_vehicles.make', 'like', "%{$make}%");
            }

            $vehicles = $vehicles->select(['customer_vehicles.id', 'customer_vehicles.make', 
                                'customer_vehicles.model', 'customer_vehicles.year', 
                                'customer_vehicles.license_plate', 'customer_vehicles.color',
                                'customer_vehicles.vin', 'customer_vehicles.notes',
                                'customer_vehicles.created_at']);

            return DataTables::of($vehicles)
                ->addColumn(
                    'action',
                    '<button data-href="{{action([\App\Http\Controllers\VehicleMileageController::class, \'getMileageHistory\'], [$id])}}" class="btn btn-xs btn-info btn-modal view_mileage_history" data-container=".mileage_history_modal"><i class="fas fa-tachometer-alt"></i> @lang("lang_v1.mileage_history")</button>
                    &nbsp;
                    <button data-href="{{action([\App\Http\Controllers\CustomerVehicleController::class, \'edit\'], [$id])}}" class="btn btn-xs btn-primary btn-modal" data-container=".vehicle_modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    &nbsp;
                    <button data-href="{{action([\App\Http\Controllers\CustomerVehicleController::class, \'destroy\'], [$id])}}" class="btn btn-xs btn-danger delete_vehicle_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>'
                )
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Get vehicles for a contact for dropdown
     *
     * @param int $contact_id
     * @return \Illuminate\Http\Response
     */
    public function getVehiclesForDropdown($contact_id)
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $vehicles = CustomerVehicle::forDropdown($business_id, $contact_id);

            return response()->json($vehicles);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param int $contact_id
     * @return \Illuminate\Http\Response
     */
    public function create($contact_id)
    {
        $contact = Contact::findOrFail($contact_id);

        return view('customer_vehicle.create')
            ->with(compact('contact'));
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
            $input = $request->only(['contact_id', 'make', 'model', 'year', 'license_plate', 'color', 'vin', 'notes']);
            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');

            $input['business_id'] = $business_id;
            $input['created_by'] = $user_id;

            $vehicle = CustomerVehicle::create($input);

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
        $vehicle = CustomerVehicle::findOrFail($id);
        $contact = Contact::findOrFail($vehicle->contact_id);

        return view('customer_vehicle.edit')
            ->with(compact('vehicle', 'contact'));
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

            $vehicle = CustomerVehicle::findOrFail($id);
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
            $vehicle = CustomerVehicle::findOrFail($id);
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
