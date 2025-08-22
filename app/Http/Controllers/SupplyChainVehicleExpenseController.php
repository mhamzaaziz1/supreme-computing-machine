<?php

namespace App\Http\Controllers;

use App\SupplyChainVehicle;
use App\SupplyChainVehicleMileage;
use App\SupplyChainVehicleExpense;
use App\Utils\Util;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use DB;

class SupplyChainVehicleExpenseController extends Controller
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
     * Display a listing of all vehicle expenses
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('customer.view') && !auth()->user()->can('customer.view_own')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Get all vehicles for filter dropdown
        $vehicles = SupplyChainVehicle::where('business_id', $business_id)
                    ->select(DB::raw("CONCAT(make, ' ', model, IF(year IS NOT NULL AND year != '', CONCAT(' (', year, ')'), ''), IF(license_plate IS NOT NULL AND license_plate != '', CONCAT(' - ', license_plate), '')) as vehicle_name"), 'id')
                    ->pluck('vehicle_name', 'id');

        // Get expense categories
        $expense_categories = \App\ExpenseCategory::where('business_id', $business_id)
                            ->pluck('name', 'id');

        // Get expense types for dropdown
        $expense_types = SupplyChainVehicleExpense::expenseTypes();

        return view('supply_chain_vehicle_expense.index')
                ->with(compact('vehicles', 'expense_categories', 'expense_types'));
    }

    /**
     * Get all vehicle expenses for datatable
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllExpenses()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            // Get expenses from the transactions table
            $expenses = \App\Transaction::leftJoin('expense_categories as ec', 'transactions.expense_category_id', '=', 'ec.id')
                        ->leftJoin('users as u', 'transactions.expense_for', '=', 'u.id')
                        ->leftJoin('supply_chain_vehicles as scv', 'transactions.supply_chain_vehicle_id', '=', 'scv.id')
                        ->where('transactions.business_id', $business_id)
                        ->where('transactions.type', 'expense')
                        ->whereNotNull('transactions.supply_chain_vehicle_id')
                        ->select(
                            'transactions.id',
                            'transactions.document as receipt_image',
                            'transactions.transaction_date',
                            'ec.name as category_name',
                            'transactions.final_total',
                            'transactions.additional_notes',
                            'transactions.supply_chain_vehicle_id as vehicle_id',
                            'transactions.sub_type as expense_type',
                            'transactions.created_at',
                            'scv.make',
                            'scv.model',
                            'scv.year',
                            'scv.license_plate'
                        );

            // Apply vehicle filter if provided
            if (!empty(request()->vehicle_id)) {
                $vehicle_id = request()->vehicle_id;
                $expenses->where('transactions.supply_chain_vehicle_id', $vehicle_id);
            }

            // Apply expense category filter if provided
            if (!empty(request()->expense_category_id)) {
                $category_id = request()->expense_category_id;
                $expenses->where('transactions.expense_category_id', $category_id);
            }

            // Apply expense type filter if provided
            if (!empty(request()->expense_type)) {
                $expense_type = request()->expense_type;
                $expenses->where('transactions.sub_type', $expense_type);
            }

            // Apply date range filter if provided
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start_date = request()->start_date;
                $end_date = request()->end_date;
                $expenses->whereBetween('transactions.transaction_date', [$start_date, $end_date]);
            }

            // Disable debug output for this response to ensure clean JSON
            $previous_debug = config('app.debug');
            config(['app.debug' => false]);

            $response = DataTables::of($expenses)
                ->addColumn('vehicle_name', function ($row) {
                    if (!empty($row->make) && !empty($row->model)) {
                        $name = $row->make . ' ' . $row->model;
                        if (!empty($row->year)) {
                            $name .= ' (' . $row->year . ')';
                        }
                        if (!empty($row->license_plate)) {
                            $name .= ' - ' . $row->license_plate;
                        }
                        return $name;
                    } else if (!empty($row->vehicle_id)) {
                        // Fallback to looking up the vehicle if the join didn't work
                        $vehicle = SupplyChainVehicle::find($row->vehicle_id);
                        if ($vehicle) {
                            $name = $vehicle->make . ' ' . $vehicle->model;
                            if (!empty($vehicle->year)) {
                                $name .= ' (' . $vehicle->year . ')';
                            }
                            if (!empty($vehicle->license_plate)) {
                                $name .= ' - ' . $vehicle->license_plate;
                            }
                            return $name;
                        }
                    }
                    return '';
                })
                ->addColumn('expense_type_text', function ($row) {
                    $expense_types = SupplyChainVehicleExpense::expenseTypes();
                    return isset($expense_types[$row->expense_type]) ? $expense_types[$row->expense_type] : $row->expense_type;
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    if (!empty($row->receipt_image)) {
                        $html .= '<li><a href="' . asset('uploads/documents/' . $row->receipt_image) . '" download=""><i class="fa fa-download" aria-hidden="true"></i> ' . __("purchase.download_document") . '</a></li>';
                        if (isFileImage($row->receipt_image)) {
                            $html .= '<li><a href="#" data-href="' . asset('uploads/documents/' . $row->receipt_image) . '" class="view_uploaded_document"><i class="fa fa-picture-o"></i> ' . __("lang_v1.view_document") . '</a></li>';
                        }
                    }

                    $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\SupplyChainVehicleExpenseController::class, 'edit'], [$row->id]) . '" class="btn-modal" data-container=".expense_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';

                    $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\SupplyChainVehicleExpenseController::class, 'destroy'], [$row->id]) . '" class="delete_expense_record"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>';

                    $html .= '</ul></div>';

                    return $html;
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('final_total', function ($row) {
                    return '<span class="display_currency" data-currency_symbol="true">' . $row->final_total . '</span>';
                })
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->rawColumns(['action', 'final_total', 'vehicle_name', 'expense_type_text'])
                ->make(true);

            // Restore debug setting
            config(['app.debug' => $previous_debug]);

            return $response;
        }
    }

    /**
     * Get expense history for a supply chain vehicle
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $vehicle_id
     * @return \Illuminate\Http\Response
     */
    public function getExpenseHistory(Request $request, $vehicle_id)
    {
        if (!auth()->user()->can('customer.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = $request->session()->get('user.business_id');

            $expenses = SupplyChainVehicleExpense::where('business_id', $business_id)
                ->where('supply_chain_vehicle_id', $vehicle_id)
                ->with(['supplyChainVehicle', 'mileageRecord'])
                ->orderBy('date', 'desc')
                ->get();

            // Disable debug output for this response to ensure clean JSON
            $previous_debug = config('app.debug');
            config(['app.debug' => false]);

            $response = DataTables::of($expenses)
                ->addColumn('expense_type_text', function ($row) {
                    $expense_types = SupplyChainVehicleExpense::expenseTypes();
                    return isset($expense_types[$row->expense_type]) ? $expense_types[$row->expense_type] : $row->expense_type;
                })
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    if (!empty($row->receipt_image)) {
                        $html .= '<li><a href="#" class="view_image" data-href="' . asset('storage/' . $row->receipt_image) . '"><i class="fa fa-picture-o"></i> ' . __("lang_v1.view_receipt") . '</a></li>';
                    }

                    $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\SupplyChainVehicleExpenseController::class, 'edit'], [$row->id]) . '" class="btn-modal" data-container=".expense_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';

                    $html .= '<li><a href="#" data-href="' . action([\App\Http\Controllers\SupplyChainVehicleExpenseController::class, 'destroy'], [$row->id]) . '" class="delete_expense_record"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</a></li>';

                    $html .= '</ul></div>';

                    return $html;
                })
                ->editColumn('date', '{{@format_date($date)}}')
                ->editColumn('amount', function ($row) {
                    return '<span class="display_currency" data-currency_symbol="true">' . $row->amount . '</span>';
                })
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->rawColumns(['action', 'amount'])
                ->make(true);

            // Restore debug setting
            config(['app.debug' => $previous_debug]);

            return $response;
        }

        $vehicle = SupplyChainVehicle::findOrFail($vehicle_id);
        $expense_types = SupplyChainVehicleExpense::expenseTypes();

        return view('supply_chain_vehicle_expense.history_modal')
            ->with(compact('vehicle', 'expense_types'));
    }

    /**
     * Show the form for selecting a vehicle before creating a new expense.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('expense.add')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // If vehicle_id is provided as a parameter, use it
        if (request()->has('vehicle_id')) {
            $vehicle_id = request()->get('vehicle_id');
            return $this->createForVehicle($vehicle_id);
        }

        // Get all vehicles for dropdown
        $vehicles = SupplyChainVehicle::where('business_id', $business_id)
                    ->select(\DB::raw("CONCAT(make, ' ', model, IF(year IS NOT NULL AND year != '', CONCAT(' (', year, ')'), ''), IF(license_plate IS NOT NULL AND license_plate != '', CONCAT(' - ', license_plate), '')) as vehicle_name"), 'id')
                    ->pluck('vehicle_name', 'id');

        // Redirect to the normal expense form
        return redirect()->action(
            [\App\Http\Controllers\ExpenseController::class, 'create']
        );
    }

    /**
     * Show the form for creating a new resource for a specific vehicle.
     *
     * @param  int  $vehicle_id
     * @return \Illuminate\Http\Response
     */
    public function createForVehicle($vehicle_id)
    {
        if (!auth()->user()->can('expense.add')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        // Redirect to the normal expense form with the vehicle ID pre-selected
        return redirect()->action(
            [\App\Http\Controllers\ExpenseController::class, 'create'],
            ['supply_chain_vehicle_id' => $vehicle_id]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('expense.add')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $user_id = request()->session()->get('user.id');

            // Create a new expense transaction
            $transaction_data = [
                'business_id' => $business_id,
                'created_by' => $user_id,
                'type' => 'expense',
                'status' => 'final',
                'payment_status' => 'due',
                'supply_chain_vehicle_id' => $request->input('supply_chain_vehicle_id'),
                'transaction_date' => $request->input('date'),
                'final_total' => $request->input('amount'),
                'additional_notes' => $request->input('description'),
                'sub_type' => $request->input('expense_type'),
                'total_before_tax' => $request->input('amount')
            ];

            // Handle document upload
            if ($request->hasFile('receipt_image')) {
                $file = $request->file('receipt_image');
                $file_name = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/documents'), $file_name);
                $transaction_data['document'] = $file_name;
            }

            // Create the transaction
            $transaction = \App\Transaction::create($transaction_data);

            // Update reference count
            $ref_count = \App\Utils\Util::setAndGetReferenceCount('expense', $business_id);
            // Generate reference number
            if (empty($transaction->ref_no)) {
                $transaction->ref_no = \App\Utils\Util::generateReferenceNumber('expense', $ref_count, $business_id);
                $transaction->save();
            }

            $output = ['success' => true,
                        'msg' => __("expense.expense_add_success")
                    ];

            return $output;

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => false,
                        'msg' => __("messages.something_went_wrong")
                    ];

            return $output;
        }
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

        // Find the expense in the transactions table
        $expense = \App\Transaction::where('business_id', $business_id)
                    ->where('id', $id)
                    ->where('type', 'expense')
                    ->whereNotNull('supply_chain_vehicle_id')
                    ->firstOrFail();

        $vehicle = SupplyChainVehicle::findOrFail($expense->supply_chain_vehicle_id);

        // Get expense categories
        $expense_categories = \App\ExpenseCategory::where('business_id', $business_id)
                            ->pluck('name', 'id');

        // Get expense types for dropdown
        $expense_types = SupplyChainVehicleExpense::expenseTypes();

        // Get mileage records for this vehicle for dropdown
        $mileage_records = SupplyChainVehicleMileage::where('business_id', $business_id)
            ->where('supply_chain_vehicle_id', $vehicle->id)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($record) {
                $date = $record->date->format('Y-m-d');
                $distance = $record->getDailyTravelDistance();
                return [
                    'id' => $record->id,
                    'text' => "{$date} - {$distance} km"
                ];
            })
            ->pluck('text', 'id')
            ->toArray();

        return view('expense.edit')
            ->with(compact('expense', 'expense_categories', 'vehicle', 'expense_types', 'mileage_records'));
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
            $business_id = request()->session()->get('user.business_id');

            // Find the expense in the transactions table
            $expense = \App\Transaction::where('business_id', $business_id)
                        ->where('id', $id)
                        ->where('type', 'expense')
                        ->whereNotNull('supply_chain_vehicle_id')
                        ->firstOrFail();

            // Prepare update data
            $transaction_data = [
                'transaction_date' => $request->input('date'),
                'final_total' => $request->input('amount'),
                'additional_notes' => $request->input('description'),
                'sub_type' => $request->input('expense_type'),
                'total_before_tax' => $request->input('amount')
            ];

            // Handle document upload
            if ($request->hasFile('receipt_image')) {
                // Delete old document if exists
                if (!empty($expense->document) && file_exists(public_path('uploads/documents/' . $expense->document))) {
                    unlink(public_path('uploads/documents/' . $expense->document));
                }

                $file = $request->file('receipt_image');
                $file_name = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/documents'), $file_name);
                $transaction_data['document'] = $file_name;
            }

            // Update the transaction
            $expense->update($transaction_data);

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
            $business_id = request()->session()->get('user.business_id');

            // Find the expense in the transactions table
            $expense = \App\Transaction::where('business_id', $business_id)
                        ->where('id', $id)
                        ->where('type', 'expense')
                        ->whereNotNull('supply_chain_vehicle_id')
                        ->firstOrFail();

            // Delete document if exists
            if (!empty($expense->document) && file_exists(public_path('uploads/documents/' . $expense->document))) {
                unlink(public_path('uploads/documents/' . $expense->document));
            }

            // Delete related payment transactions
            $expense->payment_lines()->delete();

            // Delete the expense
            $expense->delete();

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
