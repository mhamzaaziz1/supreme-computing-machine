@extends('layouts.app')

@section('title', __('sale.pos_sale'))

@section('content')
    <section class="content no-print">
        <input type="hidden" id="amount_rounding_method" value="{{ $pos_settings['amount_rounding_method'] ?? '' }}">
        @if (!empty($pos_settings['allow_overselling']))
            <input type="hidden" id="is_overselling_allowed">
        @endif
        @if (session('business.enable_rp') == 1)
            <input type="hidden" id="reward_point_enabled">
        @endif
        @php
            $is_discount_enabled = $pos_settings['disable_discount'] != 1 ? true : false;
            $is_rp_enabled = session('business.enable_rp') == 1 ? true : false;
        @endphp
        {!! Form::open([
            'url' => action([\App\Http\Controllers\SellPosController::class, 'store']),
            'method' => 'post',
            'id' => 'add_pos_sell_form',
        ]) !!}
        <div class="row mb-12">
            <div class="col-md-12 tw-pt-0 tw-mb-14">
                <div class="row tw-flex lg:tw-flex-row md:tw-flex-col sm:tw-flex-col tw-flex-col tw-items-start md:tw-gap-4">
                    {{-- <div class="@if (empty($pos_settings['hide_product_suggestion'])) col-md-7 @else col-md-10 col-md-offset-1 @endif no-padding pr-12"> --}}
                    <div class="tw-px-3 tw-w-full  lg:tw-px-0 lg:tw-pr-0 @if(empty($pos_settings['hide_product_suggestion'])) lg:tw-w-[60%]  @else lg:tw-w-[100%] @endif">

                        <div class="tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-rounded-2xl tw-bg-white tw-mb-2 md:tw-mb-8 tw-p-2">

                            {{-- <div class="box box-solid mb-12 @if (!isMobile()) mb-40 @endif"> --}}
                                <div class="box-body pb-0">
                                    {!! Form::hidden('location_id', $default_location->id ?? null, [
                                        'id' => 'location_id',
                                        'data-receipt_printer_type' => !empty($default_location->receipt_printer_type)
                                            ? $default_location->receipt_printer_type
                                            : 'browser',
                                        'data-default_payment_accounts' => $default_location->default_payment_accounts ?? '',
                                    ]) !!}
                                    <!-- sub_type -->
                                    {!! Form::hidden('sub_type', isset($sub_type) ? $sub_type : null) !!}
                                    <input type="hidden" id="item_addition_method"
                                        value="{{ $business_details->item_addition_method }}">
                                    @include('sale_pos.partials.pos_form')

                                    @include('sale_pos.partials.pos_form_totals')

                                    @include('sale_pos.partials.payment_modal')

                                    @if (empty($pos_settings['disable_suspend']))
                                        @include('sale_pos.partials.suspend_note_modal')
                                    @endif

                                    @if (empty($pos_settings['disable_recurring_invoice']))
                                        @include('sale_pos.partials.recurring_invoice_modal')
                                    @endif
                                </div>
                            {{-- </div> --}}
                        </div>
                    </div>
                    @if (empty($pos_settings['hide_product_suggestion']) && !isMobile())
                        <div class="md:tw-no-padding tw-w-full lg:tw-w-[40%] tw-px-5">
                            @include('sale_pos.partials.pos_sidebar')
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @include('sale_pos.partials.pos_form_actions')
        {!! Form::close() !!}
    </section>

    <!-- This will be printed -->
    <section class="invoice print_section" id="receipt_section">
    </section>
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        @include('contact.create', ['quick_add' => true])
    </div>
    @if (empty($pos_settings['hide_product_suggestion']) && isMobile())
        @include('sale_pos.partials.mobile_product_suggestions')
    @endif
    <!-- /.content -->
    <div class="modal fade register_details_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade close_register_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <!-- quick product modal -->
    <div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>

    <div class="modal fade" id="expense_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    @include('sale_pos.partials.configure_search_modal')

    @include('sale_pos.partials.recent_transactions_modal')

    @include('sale_pos.partials.weighing_scale_modal')

<!-- Camera Barcode Scanner Modal -->
<div class="modal fade" id="camera_barcode_modal" tabindex="-1" role="dialog" aria-labelledby="cameraBarcodeScannerLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content camera-scanner-content">
            <div class="modal-header camera-scanner-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="cameraBarcodeScannerLabel">
                    <i class="fa fa-qrcode" style="font-size: 1.2em;"></i> Live Barcode Scanner
                </h4>
            </div>
            <div class="modal-body" style="padding: 25px; background: #fafbfe;">
                <div class="row">
                    <div class="col-md-7">
                        <div id="scanner-container" class="scanner-container-premium"></div>
                        <div id="scanner-result" class="text-center" style="margin-top: 25px;">
                            <div class="scanner-status">
                                <i class="fa fa-circle-o-notch fa-spin"></i> <span>Ready to scan items...</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="scan-history-container">
                            <h5 class="scan-history-title"><i class="fa fa-history"></i> Session Scan History</h5>
                            <ul id="scan-history-list" class="scan-history-list">
                                <li class="scan-history-empty text-muted">No items scanned yet.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #eef0f5; background: #fff;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 30px; padding: 8px 24px; font-weight: 600; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">Close</button>
            </div>
        </div>
    </div>
</div>

@stop
@section('css')
    <style>
        /* Premium Camera Scanner Styles */
        .camera-scanner-content {
            border-radius: 20px !important;
            border: none !important;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2) !important;
            overflow: hidden;
        }
        .camera-scanner-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none !important;
            padding: 20px 25px !important;
        }
        .camera-scanner-header .modal-title {
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.4rem;
        }
        .camera-scanner-header .close {
            color: white;
            opacity: 0.8;
            text-shadow: none;
            font-size: 2rem;
            margin-top: -5px;
        }
        .camera-scanner-header .close:hover {
            opacity: 1;
        }
        .scanner-container-premium {
            width: 100%;
            height: 350px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: inset 0 0 15px rgba(0,0,0,0.5), 0 10px 20px rgba(0,0,0,0.05);
            background-color: #111;
            position: relative;
        }
        .scanner-container-premium video {
            object-fit: cover;
            border-radius: 16px;
        }
        .scanner-status {
            background: #fff;
            padding: 12px 24px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            font-weight: 600;
            color: #4a5568;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            font-size: 1.05rem;
        }
        .scanner-status i {
            color: #667eea;
            margin-right: 10px;
            font-size: 1.2rem;
        }
        .scan-history-container {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            height: 100%;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #eef0f5;
            display: flex;
            flex-direction: column;
            height: 420px;
        }
        .scan-history-title {
            font-weight: 700;
            color: #4a5568;
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eef0f5;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
        }
        .scan-history-list {
            list-style: none;
            padding: 0;
            margin: 0;
            overflow-y: auto;
            flex-grow: 1;
        }
        .scan-history-list li {
            padding: 12px;
            border-bottom: 1px solid #f8f9fa;
            font-size: 1rem;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fdfdfd;
            border-radius: 8px;
            margin-bottom: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: transform 0.2s;
        }
        .scan-history-list li:hover {
            transform: translateX(2px);
        }
        .scan-history-list li i {
            color: #48bb78;
            font-size: 1.2rem;
        }
        .scan-history-empty {
            text-align: center;
            padding: 30px 0 !important;
            font-style: italic;
            color: #a0aec0 !important;
            justify-content: center;
            box-shadow: none !important;
            background: transparent !important;
            border: none !important;
        }
        .scan-history-list::-webkit-scrollbar {
            width: 6px;
        }
        .scan-history-list::-webkit-scrollbar-track {
            background: #f1f1f1; 
            border-radius: 4px;
        }
        .scan-history-list::-webkit-scrollbar-thumb {
            background: #cbd5e0; 
            border-radius: 4px;
        }
    </style>
    <!-- include module css -->
    @if (!empty($pos_module_data))
        @foreach ($pos_module_data as $key => $value)
            @if (!empty($value['module_css_path']))
                @includeIf($value['module_css_path'])
            @endif
        @endforeach
    @endif
@stop
@section('javascript')
    <script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
    <!-- Load HTML5 QR Code library first -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="{{ asset('js/camera_barcode_scanner.js?v=' . $asset_v) }}"></script>
    @include('sale_pos.partials.keyboard_shortcuts')

    <!-- Call restaurant module if defined -->
    @if (in_array('tables', $enabled_modules) ||
            in_array('modifiers', $enabled_modules) ||
            in_array('service_staff', $enabled_modules))
        <script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif
    <!-- include module js -->
    @if (!empty($pos_module_data))
        @foreach ($pos_module_data as $key => $value)
            @if (!empty($value['module_js_path']))
                @includeIf($value['module_js_path'], ['view_data' => $value['view_data']])
            @endif
        @endforeach
    @endif
@endsection
