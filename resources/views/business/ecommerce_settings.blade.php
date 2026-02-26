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
                                    <div class="col-md-3">
                                        <div class="thumbnail">
                                            <img src="{{ asset('uploads/ecommerce/' . $slider_image) }}" alt="Slider Image" class="img-responsive" style="max-height: 100px;">
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

