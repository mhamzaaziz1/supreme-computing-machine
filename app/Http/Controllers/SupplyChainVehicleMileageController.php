<?php

namespace App\Http\Controllers;

use App\SupplyChainVehicle;
use App\SupplyChainVehicleMileage;
use App\Utils\Util;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class SupplyChainVehicleMileageController extends Controller
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
     * Get the last mileage record for a supply chain vehicle
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

            $last_record = SupplyChainVehicleMileage::getLastMileageRecord($business_id, $vehicle_id);

            $data = [
                'last_mileage' => $last_record ? $last_record->end_mileage : 0
            ];

            return response()->json($data);
        }
    }

    /**
     * Get mileage history for a supply chain vehicle
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

            $mileage_records = SupplyChainVehicleMileage::where('business_id', $business_id)
                ->where('supply_chain_vehicle_id', $vehicle_id)
                ->with(['supplyChainVehicle'])
                ->orderBy('date', 'desc')
                ->get();

            // Disable debug output for this response to ensure clean JSON
            $previous_debug = config('app.debug');
            config(['app.debug' => false]);

            $response = DataTables::of($mileage_records)
                ->addColumn('travel_distance', function ($row) {
                    return $row->getDailyTravelDistance();
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    
                    if (!empty($row->start_picture)) {
                        $html .= '<li><a href="#" class="view_image" data-href="' . asset('storage/' . $row->start_picture) . '"><i class="fa fa-picture-o"></i> ' . __("lang_v1.view_start_image") . '</a></li>';
                    }
                    
                    if (!empty($row->end_picture)) {
                        $html .= '<li><a href="#" class="view_image" data-href="' . asset('storage/' . $row->end_picture) . '"><i class="fa fa-picture-o"></i> ' . __("lang_v1.view_end_image") . '</a></li>';
                    }
                    
                    $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\SupplyChainVehicleMileageController::class, 'edit'], [$row->id]) . '" class="btn-modal" data-container=".mileage_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                    
                    $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\SupplyChainVehicleMileageController::class, 'destroy'], [$row->id]) . '" class="delete_mileage_record"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>';
                    
                    $html .= '</ul></div>';
                    
                    return $html;
                })
                ->editColumn('date', '{{@format_date($date)}}')
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->rawColumns(['action'])
                ->make(true);

            // Restore debug setting
            config(['app.debug' => $previous_debug]);

            return $response;
        }

        $vehicle = SupplyChainVehicle::findOrFail($vehicle_id);
        return view('supply_chain_vehicle_mileage.history_modal')
            ->with(compact('vehicle'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $vehicle_id
     * @return \Illuminate\Http\Response
     */
    public function create($vehicle_id)
    {
        if (!auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $vehicle = SupplyChainVehicle::findOrFail($vehicle_id);
        $business_id = request()->session()->get('user.business_id');
        
        // Get the last mileage record to pre-fill the start mileage
        $last_record = SupplyChainVehicleMileage::getLastMileageRecord($business_id, $vehicle_id);
        $last_mileage = $last_record ? $last_record->end_mileage : 0;

        return view('supply_chain_vehicle_mileage.create')
            ->with(compact('vehicle', 'last_mileage'));
    }

    /**
     * Store a newly created resource in storage.
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
            $input = $request->only(['supply_chain_vehicle_id', 'date', 'start_mileage', 'end_mileage', 'notes']);
            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');

            $input['business_id'] = $business_id;
            $input['created_by'] = $user_id;

            // Handle start picture upload
            if ($request->hasFile('start_picture')) {
                $start_picture = $request->file('start_picture');
                $start_picture_path = $start_picture->store('supply_chain_vehicle_mileage', 'public');
                $input['start_picture'] = $start_picture_path;
            }

            // Handle end picture upload
            if ($request->hasFile('end_picture')) {
                $end_picture = $request->file('end_picture');
                $end_picture_path = $end_picture->store('supply_chain_vehicle_mileage', 'public');
                $input['end_picture'] = $end_picture_path;
            }

            $mileage_record = SupplyChainVehicleMileage::create($input);

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
        if (!auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        $mileage_record = SupplyChainVehicleMileage::findOrFail($id);
        $vehicle = SupplyChainVehicle::findOrFail($mileage_record->supply_chain_vehicle_id);

        return view('supply_chain_vehicle_mileage.edit')
            ->with(compact('mileage_record', 'vehicle'));
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
        if (!auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only(['date', 'start_mileage', 'end_mileage', 'notes']);
            
            $mileage_record = SupplyChainVehicleMileage::findOrFail($id);

            // Handle start picture upload
            if ($request->hasFile('start_picture')) {
                // Delete old picture if exists
                if (!empty($mileage_record->start_picture) && Storage::disk('public')->exists($mileage_record->start_picture)) {
                    Storage::disk('public')->delete($mileage_record->start_picture);
                }
                
                $start_picture = $request->file('start_picture');
                $start_picture_path = $start_picture->store('supply_chain_vehicle_mileage', 'public');
                $input['start_picture'] = $start_picture_path;
            }

            // Handle end picture upload
            if ($request->hasFile('end_picture')) {
                // Delete old picture if exists
                if (!empty($mileage_record->end_picture) && Storage::disk('public')->exists($mileage_record->end_picture)) {
                    Storage::disk('public')->delete($mileage_record->end_picture);
                }
                
                $end_picture = $request->file('end_picture');
                $end_picture_path = $end_picture->store('supply_chain_vehicle_mileage', 'public');
                $input['end_picture'] = $end_picture_path;
            }

            $mileage_record->update($input);

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
        if (!auth()->user()->can('customer.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $mileage_record = SupplyChainVehicleMileage::findOrFail($id);
            
            // Delete pictures if they exist
            if (!empty($mileage_record->start_picture) && Storage::disk('public')->exists($mileage_record->start_picture)) {
                Storage::disk('public')->delete($mileage_record->start_picture);
            }
            
            if (!empty($mileage_record->end_picture) && Storage::disk('public')->exists($mileage_record->end_picture)) {
                Storage::disk('public')->delete($mileage_record->end_picture);
            }
            
            $mileage_record->delete();

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