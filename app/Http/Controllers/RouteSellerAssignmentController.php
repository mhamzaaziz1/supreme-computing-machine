<?php

namespace App\Http\Controllers;

use App\CustomerRoute;
use App\RouteSellerAssignment;
use App\User;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RouteSellerAssignmentController extends Controller
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

            $route_assignments = RouteSellerAssignment::where('route_seller_assignments.business_id', $business_id)
                ->join('users', 'route_seller_assignments.user_id', '=', 'users.id')
                ->join('customer_routes', 'route_seller_assignments.customer_route_id', '=', 'customer_routes.id')
                ->select(
                    'route_seller_assignments.id',
                    'users.first_name',
                    'users.last_name',
                    'customer_routes.name as route_name',
                    'route_seller_assignments.is_active',
                    'route_seller_assignments.assignment_date',
                    'route_seller_assignments.created_at'
                );

            return DataTables::of($route_assignments)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'. __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">';

                    if (auth()->user()->can('customer.update')) {
                        $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\RouteSellerAssignmentController::class, 'edit'], [$row->id]) . '" class="edit_assignment_button"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                    }

                    if (auth()->user()->can('customer.delete')) {
                        $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\RouteSellerAssignmentController::class, 'destroy'], [$row->id]) . '" class="delete_assignment_button"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>';
                    }

                    $html .= '</ul></div>';

                    return $html;
                })
                ->editColumn('first_name', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })
                ->editColumn('is_active', function ($row) {
                    return $row->is_active ? __('lang_v1.active') : __('lang_v1.inactive');
                })
                ->editColumn('assignment_date', function ($row) {
                    return $row->assignment_date ? $this->commonUtil->format_date($row->assignment_date) : __('lang_v1.permanent_assignment');
                })
                ->editColumn('created_at', function ($row) {
                    return $this->commonUtil->format_date($row->created_at);
                })
                ->removeColumn('last_name')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('route_seller_assignment.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get all routes
        $routes = CustomerRoute::forDropdown($business_id, false);

        // Get all sellers (users with sell permission)
        $sellers = User::where('business_id', $business_id)
                    ->whereHas('permissions', function($q) {
                        $q->where('name', 'sell.create');
                    })
                    ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as full_name"))
                    ->pluck('full_name', 'id');

        return view('route_seller_assignment.create')
                ->with(compact('routes', 'sellers'));
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
            $input = $request->only(['user_id', 'customer_route_id', 'is_active', 'assignment_date']);
            $business_id = $request->session()->get('user.business_id');
            $user_id = $request->session()->get('user.id');

            $input['business_id'] = $business_id;
            $input['created_by'] = $user_id;

            // Check if assignment already exists for the same date
            $query = RouteSellerAssignment::where('business_id', $business_id)
                        ->where('user_id', $input['user_id'])
                        ->where('customer_route_id', $input['customer_route_id']);

            if (!empty($input['assignment_date'])) {
                $query->where('assignment_date', $input['assignment_date']);
            } else {
                $query->whereNull('assignment_date');
            }

            $existing = $query->first();

            if ($existing) {
                // Update existing assignment
                $existing->is_active = $input['is_active'];
                $existing->save();
            } else {
                // Create new assignment
                RouteSellerAssignment::create($input);
            }

            $output = ['success' => true,
                        'msg' => __("lang_v1.route_assignment_added_success")
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

        $business_id = request()->session()->get('user.business_id');
        $assignment = RouteSellerAssignment::where('business_id', $business_id)->findOrFail($id);

        // Get all routes
        $routes = CustomerRoute::forDropdown($business_id, false);

        // Get all sellers (users with sell permission)
        $sellers = User::where('business_id', $business_id)
                    ->whereHas('permissions', function($q) {
                        $q->where('name', 'sell.create');
                    })
                    ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as full_name"))
                    ->pluck('full_name', 'id');

        return view('route_seller_assignment.edit')
                ->with(compact('routes', 'sellers', 'assignment'));
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
            $input = $request->only(['user_id', 'customer_route_id', 'is_active', 'assignment_date']);
            $business_id = $request->session()->get('user.business_id');

            $assignment = RouteSellerAssignment::where('business_id', $business_id)->findOrFail($id);

            // Check if assignment with new values already exists (if changing user, route, or date)
            if ($assignment->user_id != $input['user_id'] || 
                $assignment->customer_route_id != $input['customer_route_id'] || 
                (isset($input['assignment_date']) && $assignment->assignment_date != $input['assignment_date'])) {

                $query = RouteSellerAssignment::where('business_id', $business_id)
                            ->where('user_id', $input['user_id'])
                            ->where('customer_route_id', $input['customer_route_id']);

                if (!empty($input['assignment_date'])) {
                    $query->where('assignment_date', $input['assignment_date']);
                } else {
                    $query->whereNull('assignment_date');
                }

                $existing = $query->where('id', '!=', $id)->first();

                if ($existing) {
                    return ['success' => false,
                            'msg' => __("lang_v1.route_assignment_already_exists")
                        ];
                }
            }

            $assignment->user_id = $input['user_id'];
            $assignment->customer_route_id = $input['customer_route_id'];
            $assignment->is_active = $input['is_active'];
            $assignment->assignment_date = $input['assignment_date'] ?? null;
            $assignment->save();

            $output = ['success' => true,
                        'msg' => __("lang_v1.route_assignment_updated_success")
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
            $business_id = request()->session()->get('user.business_id');
            $assignment = RouteSellerAssignment::where('business_id', $business_id)->findOrFail($id);
            $assignment->delete();

            $output = ['success' => true,
                        'msg' => __("lang_v1.route_assignment_deleted_success")
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
