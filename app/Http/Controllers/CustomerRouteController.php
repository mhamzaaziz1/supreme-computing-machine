<?php

namespace App\Http\Controllers;

use App\CustomerRoute;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class CustomerRouteController extends Controller
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
     */
    public function index()
    {
        if (! auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            // Child count subquery
            $childSub = DB::table('customer_routes as cr2')
                ->select('cr2.parent_id', DB::raw('COUNT(*) as child_count'))
                ->groupBy('cr2.parent_id');

            // Customer count subquery
            $customerSub = DB::table('contacts')
                ->select('customer_route_id', DB::raw('COUNT(*) as customer_count'))
                ->groupBy('customer_route_id');

            $customer_routes = CustomerRoute::query()
                ->where('customer_routes.business_id', $business_id)
                ->leftJoin('customer_routes as parent', 'customer_routes.parent_id', '=', 'parent.id')
                ->leftJoinSub($childSub, 'cr_children', function ($join) {
                    $join->on('customer_routes.id', '=', 'cr_children.parent_id');
                })
                ->leftJoinSub($customerSub, 'cr_customers', function ($join) {
                    $join->on('customer_routes.id', '=', 'cr_customers.customer_route_id');
                })
                ->select([
                    'customer_routes.id',
                    'customer_routes.name',
                    'customer_routes.slug',
                    'customer_routes.description',
                    'customer_routes.is_active',
                    'parent.name as parent_name',
                    DB::raw('COALESCE(cr_children.child_count, 0) as child_count'),
                    DB::raw('COALESCE(cr_customers.customer_count, 0) as customer_count'),
                ]);

            // Filter by parent_id if provided
            if (request()->filled('parent_id')) {
                $customer_routes->where('customer_routes.parent_id', request()->input('parent_id'));
            }

            return DataTables::of($customer_routes)
                ->addColumn('action', function ($row) {
                    $html = '';
                    if (auth()->user()->can('customer.update')) {
                        $html .= '<button data-href="'.action('App\\Http\\Controllers\\CustomerRouteController@edit', [$row->id]).'" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline tw-dw-btn-primary tw-m-0.5 edit_customer_route_button"><i class="glyphicon glyphicon-edit"></i> '.e(__('messages.edit')).'</button> ';
                    }
                    if (auth()->user()->can('customer.delete')) {
                        $html .= '<button data-href="'.action('App\\Http\\Controllers\\CustomerRouteController@destroy', [$row->id]).'" class="tw-dw-btn tw-dw-btn-outline tw-dw-btn-xs tw-dw-btn-error tw-m-0.5 delete_customer_route_button"><i class="glyphicon glyphicon-trash"></i> '.e(__('messages.delete')).'</button>';
                    }
                    return $html;
                })
                ->editColumn('is_active', function ($row) {
                    return $row->is_active
                        ? '<span class="label bg-green">'.e(__('messages.active')).'</span>'
                        : '<span class="label bg-gray">'.e(__('messages.inactive')).'</span>';
                })
                ->editColumn('name', function ($row) {
                    $name = e($row->name);
                    if (!empty($row->parent_name)) {
                        $name = '<span class="ml-20">'.$name.'</span> <span class="text-muted">('.e(__('lang_v1.sub_route_of')).': '.e($row->parent_name).')</span>';
                    }
                    return $name;
                })
                ->editColumn('child_count', function ($row) {
                    return '<span class="label bg-info">'.(int) $row->child_count.'</span>';
                })
                ->editColumn('customer_count', function ($row) {
                    return '<span class="label bg-primary">'.(int) $row->customer_count.'</span>';
                })
                ->removeColumn('parent_name')
                ->rawColumns(['action', 'name', 'is_active', 'child_count', 'customer_count'])
                ->make(true);
        }

        return view('customer_route.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (! auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $parent_routes = CustomerRoute::where('business_id', $business_id)
            ->where('is_active', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('customer_route.create', compact('parent_routes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (! auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = $request->session()->get('user.business_id');
        $user_id = $request->session()->get('user.id');

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:191',
                Rule::unique('customer_routes', 'name')->where(fn ($q) => $q->where('business_id', $business_id)),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => [
                'nullable', 'integer',
                Rule::exists('customer_routes', 'id')->where(fn ($q) => $q->where('business_id', $business_id)),
            ],
            'is_active' => ['nullable'],
        ]);

        try {
            $payload = [
                'business_id' => $business_id,
                'created_by'  => $user_id,
                'name'        => $validated['name'],
                'description' => isset($validated['description']) ? strip_tags($validated['description']) : null,
                'parent_id'   => $validated['parent_id'] ?? null,
                'is_active'   => $request->boolean('is_active'),
            ];

            $payload['slug'] = $this->generateUniqueSlug($payload['name'], $business_id);

            DB::beginTransaction();
            $customer_route = CustomerRoute::create($payload);
            DB::commit();

            return response()->json([
                'success' => true,
                'data'    => $customer_route,
                'msg'     => __('lang_v1.success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:'.$e->getFile().' Line:'.$e->getLine().' Message:'.$e->getMessage());

            return response()->json([
                'success' => false,
                'msg'     => __('messages.something_went_wrong'),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        if (! auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $customer_route = CustomerRoute::where('business_id', $business_id)->findOrFail($id);

            // Exclude current + its descendants from potential parents to prevent circular refs in UI
            $excludeIds = array_merge([$id], $this->getAllChildrenRoutes($id));

            $parent_routes = CustomerRoute::where('business_id', $business_id)
                ->where('is_active', 1)
                ->whereNotIn('id', $excludeIds)
                ->orderBy('name')
                ->pluck('name', 'id');

            return view('customer_route.edit', compact('customer_route', 'parent_routes'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (! auth()->user()->can('customer.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = $request->session()->get('user.business_id');

            $validated = $request->validate([
                'name' => [
                    'required', 'string', 'max:191',
                    Rule::unique('customer_routes', 'name')
                        ->where(fn ($q) => $q->where('business_id', $business_id))
                        ->ignore($id),
                ],
                'description' => ['nullable', 'string', 'max:1000'],
                'parent_id' => [
                    'nullable', 'integer',
                    Rule::exists('customer_routes', 'id')->where(fn ($q) => $q->where('business_id', $business_id)),
                ],
                'is_active' => ['nullable'],
            ]);

            try {
                $customer_route = CustomerRoute::where('business_id', $business_id)->findOrFail($id);

                // Prevent self-parenting
                if (!empty($validated['parent_id']) && (int) $validated['parent_id'] === (int) $id) {
                    return response()->json(['success' => false, 'msg' => __('lang_v1.cannot_set_child_as_parent')], 422);
                }

                // Prevent circular parenting (cannot set a descendant as parent)
                if (!empty($validated['parent_id'])) {
                    $descendants = $this->getAllChildrenRoutes($id);
                    if (in_array((int) $validated['parent_id'], $descendants, true)) {
                        return response()->json(['success' => false, 'msg' => __('lang_v1.cannot_set_child_as_parent')], 422);
                    }
                }

                $payload = [
                    'name'        => $validated['name'],
                    'description' => isset($validated['description']) ? strip_tags($validated['description']) : null,
                    'parent_id'   => $validated['parent_id'] ?? null,
                    'is_active'   => $request->boolean('is_active'),
                ];

                // Regenerate slug if name changed (and ensure uniqueness per business)
                if ($customer_route->name !== $payload['name']) {
                    $payload['slug'] = $this->generateUniqueSlug($payload['name'], $business_id, $id);
                }

                DB::beginTransaction();
                $customer_route->update($payload);
                DB::commit();

                return response()->json([
                    'success' => true,
                    'msg'     => __('lang_v1.success'),
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency('File:'.$e->getFile().' Line:'.$e->getLine().' Message:'.$e->getMessage());

                return response()->json([
                    'success' => false,
                    'msg'     => __('messages.something_went_wrong'),
                ], 500);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (! auth()->user()->can('customer.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $customer_route = CustomerRoute::where('business_id', $business_id)->findOrFail($id);

                // Check if route has children
                $child_count = CustomerRoute::where('parent_id', $id)->count();
                if ($child_count > 0) {
                    return response()->json(['success' => false, 'msg' => __('lang_v1.route_has_children')], 422);
                }

                // Check if route has customers
                $customer_count = \App\Contact::where('customer_route_id', $id)->count();
                if ($customer_count > 0) {
                    return response()->json(['success' => false, 'msg' => __('lang_v1.route_has_customers')], 422);
                }

                DB::beginTransaction();
                $customer_route->delete();
                DB::commit();

                return response()->json([
                    'success' => true,
                    'msg'     => __('lang_v1.success'),
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency('File:'.$e->getFile().' Line:'.$e->getLine().' Message:'.$e->getMessage());

                return response()->json([
                    'success' => false,
                    'msg'     => __('messages.something_went_wrong'),
                ], 500);
            }
        }
    }

    /**
     * Get all children route ids for a given route
     */
    private function getAllChildrenRoutes($route_id)
    {
        $children = CustomerRoute::where('parent_id', $route_id)->pluck('id')->toArray();

        foreach ($children as $child_id) {
            $grand_children = $this->getAllChildrenRoutes($child_id);
            $children = array_merge($children, $grand_children);
        }

        return array_unique($children);
    }

    /**
     * Generate a unique slug per business.
     */
    private function generateUniqueSlug(string $name, int $business_id, $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'n-a';
        $slug = $base;
        $i = 2;

        $exists = function ($candidate) use ($business_id, $ignoreId) {
            $q = CustomerRoute::where('business_id', $business_id)->where('slug', $candidate);
            if ($ignoreId) {
                $q->where('id', '!=', $ignoreId);
            }
            return $q->exists();
        };

        while ($exists($slug)) {
            $slug = $base.'-'.$i;
            $i++;
            if ($i > 1000) { // safety valve
                break;
            }
        }

        return $slug;
    }
}
