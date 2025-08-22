<?php

namespace App\Http\Controllers;

use App\Contact;
use App\CustomerRoute;
use App\RouteFollowup;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RouteFollowupController extends Controller
{
    /** @var Util */
    protected $commonUtil;

    /**
     * Constructor
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

        $business_id = request()->session()->get('user.business_id');
        $customer_routes = CustomerRoute::forDropdown($business_id, true, false);

        if (request()->ajax()) {
            $filters = [];

            if (request()->has('customer_route_id') && !empty(request()->customer_route_id)) {
                $filters['customer_route_id'] = request()->customer_route_id;
            }

            if (request()->has('contact_id') && !empty(request()->contact_id)) {
                $filters['contact_id'] = request()->contact_id;
            }

            if (request()->has('start_date') && !empty(request()->start_date) && 
                request()->has('end_date') && !empty(request()->end_date)) {
                try {
                    $filters['start_date'] = $this->commonUtil->uf_date(request()->start_date);
                    $filters['end_date'] = $this->commonUtil->uf_date(request()->end_date);
                } catch (\Exception $e) {
                    \Log::emergency("Date conversion failed: " . $e->getMessage());
                    // Fallback to direct parsing if uf_date fails
                    try {
                        $filters['start_date'] = \Carbon\Carbon::parse(request()->start_date)->format('Y-m-d');
                        $filters['end_date'] = \Carbon\Carbon::parse(request()->end_date)->format('Y-m-d');
                    } catch (\Exception $e2) {
                        \Log::emergency("Direct date parsing also failed: " . $e2->getMessage());
                    }
                }
            }

            $followups = RouteFollowup::forBusiness($business_id, $filters)
                ->with(['customerRoute', 'contact', 'user'])
                ->select('route_followups.*');

            return DataTables::of($followups)
                ->addColumn('action', function ($row) {
                    $html = '';
                    if (auth()->user()->can('customer.update')) {
                        $html .= '<button data-href="' . action('App\\Http\\Controllers\\RouteFollowupController@edit', [$row->id]) . '" class="btn btn-xs btn-primary edit_followup_button"><i class="glyphicon glyphicon-edit"></i> ' . __('messages.edit') . '</button> ';
                    }
                    if (auth()->user()->can('customer.delete')) {
                        $html .= '<button data-href="' . action('App\\Http\\Controllers\\RouteFollowupController@destroy', [$row->id]) . '" class="btn btn-xs btn-danger delete_followup_button"><i class="glyphicon glyphicon-trash"></i> ' . __('messages.delete') . '</button>';
                    }
                    return $html;
                })
                ->editColumn('followup_date', function ($row) {
                    return $this->commonUtil->format_date($row->followup_date, true);
                })
                ->editColumn('customer_route_id', function ($row) {
                    return $row->customerRoute ? $row->customerRoute->name : '';
                })
                ->editColumn('contact_id', function ($row) {
                    if (!$row->contact) {
                        return '';
                    }

                    $contact_name = $row->contact->name;
                    $business_name = $row->contact->supplier_business_name;

                    if (!empty($business_name)) {
                        return $business_name . ' - ' . $contact_name;
                    }

                    return $contact_name;
                })
                ->editColumn('user_id', function ($row) {
                    return $row->user ? $row->user->username : '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('route_followup.index', compact('customer_routes'));
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
        $customer_routes = CustomerRoute::forDropdown($business_id, false, false);

        // Get contacts for the selected route if route_id is provided
        $contacts = [];
        if (request()->has('route_id') && !empty(request()->route_id)) {
            $contactsQuery = Contact::where('business_id', $business_id)
                ->where('customer_route_id', request()->route_id)
                ->select('id', 'name', 'supplier_business_name')
                ->get();

            foreach ($contactsQuery as $contact) {
                $displayName = $contact->name;
                if (!empty($contact->supplier_business_name)) {
                    $displayName = $contact->supplier_business_name . ' - ' . $contact->name;
                }
                $contacts[$contact->id] = $displayName;
            }
        }

        return view('route_followup.create', compact('customer_routes', 'contacts'));
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

        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');

        $validated = $request->validate([
            'customer_route_id' => 'required|exists:customer_routes,id',
            'contact_id' => 'required|exists:contacts,id',
            'notes' => 'required|string',
            'followup_date' => 'required|date',
        ]);

        try {
            try {
                $followup_date = $this->commonUtil->uf_date($validated['followup_date'], true);
            } catch (\Exception $e) {
                // If uf_date fails, try to parse the date directly
                $followup_date = \Carbon\Carbon::createFromFormat('Y-m-d', $validated['followup_date'])->format('Y-m-d H:i:s');
            }

            $followup_data = [
                'business_id' => $business_id,
                'customer_route_id' => $validated['customer_route_id'],
                'contact_id' => $validated['contact_id'],
                'user_id' => $user_id,
                'notes' => $validated['notes'],
                'followup_date' => $followup_date,
            ];

            DB::beginTransaction();
            $followup = RouteFollowup::create($followup_data);
            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->action('App\\Http\\Controllers\\RouteFollowupController@index')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $followup = RouteFollowup::where('business_id', $business_id)
            ->with(['customerRoute', 'contact', 'user'])
            ->findOrFail($id);

        return view('route_followup.show', compact('followup'));
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
        $followup = RouteFollowup::where('business_id', $business_id)->findOrFail($id);
        $customer_routes = CustomerRoute::forDropdown($business_id, false, false);

        $contacts = [];
        $contactsQuery = Contact::where('business_id', $business_id)
            ->where('customer_route_id', $followup->customer_route_id)
            ->select('id', 'name', 'supplier_business_name')
            ->get();

        foreach ($contactsQuery as $contact) {
            $displayName = $contact->name;
            if (!empty($contact->supplier_business_name)) {
                $displayName = $contact->supplier_business_name . ' - ' . $contact->name;
            }
            $contacts[$contact->id] = $displayName;
        }

        return view('route_followup.edit', compact('followup', 'customer_routes', 'contacts'));
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

        $business_id = $request->session()->get('user.business_id');

        $validated = $request->validate([
            'customer_route_id' => 'required|exists:customer_routes,id',
            'contact_id' => 'required|exists:contacts,id',
            'notes' => 'required|string',
            'followup_date' => 'required|date',
        ]);

        try {
            $followup = RouteFollowup::where('business_id', $business_id)->findOrFail($id);

            try {
                $followup_date = $this->commonUtil->uf_date($validated['followup_date'], true);
            } catch (\Exception $e) {
                // If uf_date fails, try to parse the date directly
                $followup_date = \Carbon\Carbon::createFromFormat('Y-m-d', $validated['followup_date'])->format('Y-m-d H:i:s');
            }

            $followup_data = [
                'customer_route_id' => $validated['customer_route_id'],
                'contact_id' => $validated['contact_id'],
                'notes' => $validated['notes'],
                'followup_date' => $followup_date,
            ];

            DB::beginTransaction();
            $followup->update($followup_data);
            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return redirect()->action('App\\Http\\Controllers\\RouteFollowupController@index')->with('status', $output);
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

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $followup = RouteFollowup::where('business_id', $business_id)->findOrFail($id);

                DB::beginTransaction();
                $followup->delete();
                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Get contacts for a customer route
     *
     * @return \Illuminate\Http\Response
     */
    public function getContacts()
    {
        if (!auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $route_id = request()->input('route_id');

        $contacts = Contact::where('business_id', $business_id)
            ->where('customer_route_id', $route_id)
            ->select('id', 'name', 'supplier_business_name')
            ->get();

        return response()->json(['data' => $contacts]);
    }

    /**
     * Get followups for a customer route
     *
     * @return \Illuminate\Http\Response
     */
    public function getFollowups()
    {
        if (!auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $route_id = request()->input('route_id');
        $contact_id = request()->input('contact_id');

        $query = RouteFollowup::where('business_id', $business_id);

        if (!empty($route_id)) {
            $query->where('customer_route_id', $route_id);
        }

        if (!empty($contact_id)) {
            $query->where('contact_id', $contact_id);
        }

        $followups = $query->with(['contact', 'user'])
            ->orderBy('followup_date', 'desc')
            ->get();

        return response()->json(['data' => $followups]);
    }
}
