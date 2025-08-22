<?php

namespace App\Http\Controllers;

use App\RouteVisitLog;
use App\CustomerRoute;
use App\User;
use App\Utils\Util;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RouteVisitLogController extends Controller
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

            $visit_logs = RouteVisitLog::where('route_visit_logs.business_id', $business_id)
                ->join('users', 'route_visit_logs.user_id', '=', 'users.id')
                ->join('customer_routes', 'route_visit_logs.customer_route_id', '=', 'customer_routes.id')
                ->leftJoin('contacts', 'route_visit_logs.contact_id', '=', 'contacts.id')
                ->select(
                    'route_visit_logs.id',
                    'users.first_name',
                    'users.last_name',
                    'customer_routes.name as route_name',
                    'contacts.name as contact_name',
                    'route_visit_logs.visit_type',
                    'route_visit_logs.latitude',
                    'route_visit_logs.longitude',
                    'route_visit_logs.accuracy',
                    'route_visit_logs.visit_time',
                    'route_visit_logs.created_at'
                );

            return DataTables::of($visit_logs)
                ->editColumn('first_name', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })
                ->editColumn('visit_type', function ($row) {
                    return __('lang_v1.' . $row->visit_type);
                })
                ->editColumn('visit_time', function ($row) {
                    return $this->commonUtil->format_date($row->visit_time, true);
                })
                ->editColumn('created_at', function ($row) {
                    return $this->commonUtil->format_date($row->created_at);
                })
                ->removeColumn('last_name')
                ->make(true);
        }

        return view('route_visit_log.index');
    }
}