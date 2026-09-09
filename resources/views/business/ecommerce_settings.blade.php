@extends('layouts.app')
@section('title', __('ecommerce.settings'))

@section('content')
<section class="content-header">
    <h1>@lang('ecommerce.settings')</h1>
</section>

<section class="content">
    {!! Form::open(['url' => action([\App\Http\Controllers\BusinessController::class, 'postEcommerceSettings']), 'method' => 'post', 'id' => 'ecommerce_settings_form', 'files' => true ]) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('ecommerce.settings')</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('enable_ecommerce', __('ecommerce.enable_ecommerce') . ':') !!}
                                {!! Form::select('enable_ecommerce', ['1' => __('messages.yes'), '0' => __('messages.no')], !empty($ecom_settings['enable_ecommerce']) ? $ecom_settings['enable_ecommerce'] : 0, ['class' => 'form-control']) !!}
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('ecommerce_store_name', __('ecommerce.store_name') . ':') !!}
                                {!! Form::text('ecommerce_store_name', !empty($ecom_settings['store_name']) ? $ecom_settings['store_name'] : $business->name, ['class' => 'form-control', 'placeholder' => __('ecommerce.store_name')]) !!}
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                {!! Form::label('ecommerce_store_tagline', __('ecommerce.store_tagline') . ':') !!}
                                {!! Form::text('ecommerce_store_tagline', !empty($ecom_settings['store_tagline']) ? $ecom_settings['store_tagline'] : '', ['class' => 'form-control', 'placeholder' => __('ecommerce.store_tagline')]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                {!! Form::label('ecommerce_store_description', __('ecommerce.store_description') . ':') !!}
                                {!! Form::textarea('ecommerce_store_description', !empty($ecom_settings['store_description']) ? $ecom_settings['store_description'] : '', ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('ecommerce.store_description')]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('ecommerce_store_logo', __('ecommerce.store_logo') . ':') !!}
                                {!! Form::file('ecommerce_store_logo', ['id' => 'ecommerce_store_logo', 'accept' => 'image/*']) !!}
                                <p class="help-block">@lang('ecommerce.logo_help')</p>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                {!! Form::label('ecommerce_store_banner', __('ecommerce.store_banner') . ':') !!}
                                {!! Form::file('ecommerce_store_banner', ['id' => 'ecommerce_store_banner', 'accept' => 'image/*']) !!}
                                <p class="help-block">@lang('ecommerce.banner_help')</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>{{ __('ecommerce.slider_images') }}:</label>
                                <div class="row" id="slider_images_container">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {!! Form::file('slider_images[]', ['class' => 'slider-image-input', 'accept' => 'image/*']) !!}
                                            <button type="button" class="btn btn-danger btn-xs remove-slider-image" style="display: none;"><i class="fa fa-times"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary" id="add_more_slider_images">{{ __('ecommerce.add_more_images') }}</button>
                                <p class="help-block">{{ __('ecommerce.slider_images_help') }}</p>
                                <p class="help-block"><strong class="text-danger">Strictly Recommended Dimension: 1920x600px</strong>. Text should be part of the image; no code overlays will be applied.</p>
                            </div>
                        </div>
                    </div>

                    @if(!empty($ecom_settings['slider_images']))
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>{{ __('ecommerce.current_slider_images') }}:</label>
                                <div class="row">
                                    @foreach($ecom_settings['slider_images'] as $key => $slider_image)
                                    <div class="col-md-2">
                                        <div class="thumbnail">
                                            <img src="{{ asset('uploads/ecommerce/' . $slider_image) }}" alt="Slider Image" class="img-responsive" style="max-height: 80px;">
                                            <div class="caption">
                                                <button type="button" class="btn btn-danger btn-xs delete-slider-image" data-image-key="{{ $key }}">{{ __('messages.delete') }}</button>
                                                <input type="hidden" name="existing_slider_images[]" value="{{ $slider_image }}">
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <h4 class="box-title">Brands Section</h4>
                            <div id="brands_container">
                                @if(!empty($ecom_settings['brands']))
                                    @foreach($ecom_settings['brands'] as $brand)
                                        <div class="row brand-row mb-3">
                                            <div class="col-md-2">
                                                <img src="{{ asset('uploads/ecommerce/' . $brand['logo']) }}" class="img-thumbnail" style="max-height: 50px;">
                                                <input type="hidden" name="existing_brand_logos[]" value="{{ $brand['logo'] }}">
                                            </div>
                                            <div class="col-md-8">
                                                {!! Form::text('brand_links[]', $brand['link'], ['class' => 'form-control', 'placeholder' => 'Brand Link (e.g. https://google.com)']) !!}
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger remove-brand-row"><i class="fa fa-trash"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add_brand_row"><i class="fa fa-plus"></i> Add Brand</button>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <h4 class="box-title">Promo Banners</h4>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Promo Banner 1 (600x300px):</label>
                                        @if(!empty($ecom_settings['promo_banners'][1]['image']))
                                            <div class="mb-2">
                                                <img src="{{ asset('uploads/ecommerce/' . $ecom_settings['promo_banners'][1]['image']) }}" class="img-thumbnail" style="max-height: 100px;">
                                            </div>
                                        @endif
                                        {!! Form::file('promo_banner_1', ['accept' => 'image/*']) !!}
                                        {!! Form::text('promo_banner_link_1', $ecom_settings['promo_banners'][1]['link'] ?? '', ['class' => 'form-control mt-1', 'placeholder' => 'Link for Banner 1']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Promo Banner 2 (600x300px):</label>
                                        @if(!empty($ecom_settings['promo_banners'][2]['image']))
                                            <div class="mb-2">
                                                <img src="{{ asset('uploads/ecommerce/' . $ecom_settings['promo_banners'][2]['image']) }}" class="img-thumbnail" style="max-height: 100px;">
                                            </div>
                                        @endif
                                        {!! Form::file('promo_banner_2', ['accept' => 'image/*']) !!}
                                        {!! Form::text('promo_banner_link_2', $ecom_settings['promo_banners'][2]['link'] ?? '', ['class' => 'form-control mt-1', 'placeholder' => 'Link for Banner 2']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <h4 class="box-title">Trending Products Settings</h4>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('trending_mode', 'Trending Mode:') !!}
                                        {!! Form::select('trending_mode', ['auto' => 'Auto (Based on Sales)', 'manual' => 'Manual Selection'], $ecom_settings['trending_mode'] ?? 'auto', ['class' => 'form-control', 'id' => 'trending_mode']) !!}
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        {!! Form::label('trending_limit', 'Display Limit:') !!}
                                        {!! Form::number('trending_limit', $ecom_settings['trending_limit'] ?? 8, ['class' => 'form-control', 'min' => 1]) !!}
                                    </div>
                                </div>
                                <div class="col-sm-4 manual-trending" style="{{ ($ecom_settings['trending_mode'] ?? 'auto') == 'auto' ? 'display: none;' : '' }}">
                                    <div class="form-group">
                                        {!! Form::label('trending_products', 'Select Products:') !!}
                                        {!! Form::select('trending_products[]', [], null, ['class' => 'form-control select2', 'multiple', 'style' => 'width: 100%;', 'id' => 'trending_products_dropdown']) !!}
                                        <p class="help-block">Search and select products for manual trending.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 text-center">
            <button class="btn btn-primary btn-lg" type="submit">@lang('business.update_settings')</button>
        </div>
    </div>
    {!! Form::close() !!}
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
        $('#add_brand_row').click(function(){
            var html = '<div class="row brand-row mb-3">' +
                            '<div class="col-md-4">' +
                                '<input type="file" name="brand_logos[]" accept="image/*" class="form-control" required>' +
                            '</div>' +
                            '<div class="col-md-6">' +
                                '<input type="text" name="new_brand_links[]" class="form-control" placeholder="Brand Link">' +
                            '</div>' +
                            '<div class="col-md-2">' +
                                '<button type="button" class="btn btn-danger remove-brand-row"><i class="fa fa-trash"></i></button>' +
                            '</div>' +
                        '</div>';
            $('#brands_container').append(html);
        });

        $(document).on('click', '.remove-brand-row', function(){
            $(this).closest('.brand-row').remove();
        });

        $('#trending_mode').change(function(){
            if($(this).val() == 'manual'){
                $('.manual-trending').fadeIn();
            } else {
                $('.manual-trending').fadeOut();
            }
        });

        // Initialize select2 for trending products with AJAX search
        $('#trending_products_dropdown').select2({
            ajax: {
                url: '/products/list?not_for_selling=0',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (obj) {
                            return { id: obj.id, text: obj.text };
                        })
                    };
                }
            },
            minimumInputLength: 2
        });

        // Load existing products into select2 if manual
        @if(!empty($ecom_settings['trending_products']))
            $.ajax({
                url: '/products/list?not_for_selling=0',
                dataType: 'json',
                data: { product_ids: {!! json_encode($ecom_settings['trending_products']) !!} },
                success: function(data){
                    $.each(data, function(i, item){
                        var option = new Option(item.text, item.id, true, true);
                        $('#trending_products_dropdown').append(option).trigger('change');
                    });
                }
            });
        @endif
    });
</script>
@endsection

