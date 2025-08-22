@extends('layouts.app')
@section('title', __('Calculate Purchase Requirements'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('Calculate Purchase Requirements')
        <small></small>
    </h1>
</section>

<!-- Main content -->
<section class="content no-print">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('Purchase Requirements')])
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="purchase_requirements_table">
                        <thead>
                            <tr>
                                <th>@lang('product.product_name')</th>
                                <th>@lang('product.sku')</th>
                                <th>@lang('lang_v1.sales_last_period')</th>
                                <th>@lang('lang_v1.forecast_demand')</th>
                                <th>@lang('lang_v1.safety_stock')</th>
                                <th>@lang('lang_v1.current_stock')</th>
                                <th>@lang('lang_v1.purchase_qty')</th>
                                <th>@lang('lang_v1.unit')</th>
                                <th>@lang('lang_v1.select')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase_requirements as $requirement)
                                <tr>
                                    <td>
                                        {{ $requirement['product_name'] }}
                                        @if(!empty($requirement['variation_name']))
                                            <br/>
                                            <small>{{ $requirement['variation_name'] }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $requirement['sku'] }}</td>
                                    <td>{{ @num_format($requirement['sales_last_period']) }}</td>
                                    <td>{{ @num_format($requirement['forecast_demand']) }}</td>
                                    <td>{{ @num_format($requirement['safety_stock']) }}</td>
                                    <td>{{ @num_format($requirement['current_stock']) }}</td>
                                    <td>{{ @num_format($requirement['purchase_qty']) }}</td>
                                    <td>{{ $requirement['unit'] }}</td>
                                    <td>
                                        <input type="checkbox" class="product-selection" 
                                            data-product-id="{{ $requirement['product_id'] }}"
                                            data-variation-id="{{ $requirement['variation_id'] }}"
                                            data-quantity="{{ $requirement['purchase_qty'] }}"
                                            checked>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('supplier_id', __('purchase.supplier') . ':*') !!}
                    {!! Form::select('supplier_id', $suppliers, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'supplier_id']); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('location_id', __('purchase.business_location').':*') !!}
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'id' => 'location_id']); !!}
                </div>
            </div>
            <div class="col-md-4">
                <button type="button" class="tw-dw-btn tw-dw-btn-primary tw-text-white" id="create_purchase_order" style="margin-top: 23px;">
                    @lang('lang_v1.create_purchase_order')
                </button>
            </div>
        </div>
    @endcomponent
</section>

@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        $('#purchase_requirements_table').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });

        // Create purchase order
        $('#create_purchase_order').click(function() {
            var supplier_id = $('#supplier_id').val();
            var location_id = $('#location_id').val();

            if (!supplier_id) {
                toastr.error('@lang("purchase.please_select_supplier")');
                return;
            }

            if (!location_id) {
                toastr.error('@lang("purchase.please_select_location")');
                return;
            }

            var selected_products = [];
            $('.product-selection:checked').each(function() {
                selected_products.push({
                    product_id: $(this).data('product-id'),
                    variation_id: $(this).data('variation-id'),
                    quantity: $(this).data('quantity')
                });
            });

            if (selected_products.length === 0) {
                toastr.error('@lang("purchase.no_products_selected")');
                return;
            }

            $.ajax({
                method: 'POST',
                url: "{{ action([\App\Http\Controllers\PurchaseController::class, 'store']) }}",
                data: { 
                    products: selected_products,
                    location_id: location_id,
                    contact_id: supplier_id,
                    _token: "{{ csrf_token() }}"
                },
                success: function(result) {
                    if (result.success == 1) {
                        toastr.success(result.msg);
                        window.location.href = result.redirect_url;
                    } else {
                        toastr.error(result.msg);
                    }
                }
            });
        });
    });
</script>
@endsection