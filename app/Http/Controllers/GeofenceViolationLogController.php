<?php

namespace App\Http\Controllers;

use App\GeofenceViolationLog;
use App\CustomerRoute;
use App\User;
use App\Utils\Util;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GeofenceViolationLogController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $violation_logs = GeofenceViolationLog::where('geofence_violation_logs.business_id', $business_id)
                ->join('users', 'geofence_violation_logs.user_id', '=', 'users.id')
                ->leftJoin('customer_routes', 'geofence_violation_logs.customer_route_id', '=', 'customer_routes.id')
                ->leftJoin('contacts', 'geofence_violation_logs.contact_id', '=', 'contacts.id')
                ->select(
                    'geofence_violation_logs.id',
                    'users.first_name',
                    'users.last_name',
                    'customer_routes.name as route_name',
                    'contacts.name as contact_name',
                    'geofence_violation_logs.violation_type',
                    'geofence_violation_logs.attempted_action',
                    'geofence_violation_logs.latitude',
                    'geofence_violation_logs.longitude',
                    'geofence_violation_logs.accuracy',
                    'geofence_violation_logs.distance_from_valid',
                    'geofence_violation_logs.is_mock_location',
                    'geofence_violation_logs.details',
                    'geofence_violation_logs.created_at'
                );

            return DataTables::of($violation_logs)
                ->editColumn('first_name', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })
                ->editColumn('violation_type', function ($row) {
                    return __('lang_v1.' . $row->violation_type);
                })
                ->editColumn('attempted_action', function ($row) {
                    return __('lang_v1.' . $row->attempted_action);
                })
                ->editColumn('is_mock_location', function ($row) {
                    return $row->is_mock_location ? __('messages.yes') : __('messages.no');
                })
                ->editColumn('created_at', function ($row) {
                    return $this->commonUtil->format_date($row->created_at);
                })
                ->removeColumn('last_name')
                ->make(true);
        }

        return view('geofence_violation_log.index');
    }
}