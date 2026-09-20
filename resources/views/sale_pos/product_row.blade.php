@php
	$common_settings = session()->get('business.common_settings');
	$multiplier = 1;

	$action = !empty($action) ? $action : '';

	//The POS screen (create + edit) renders a card-style billing line, while the
	//direct sell / sell edit screens keep the plain table look their own headers
	//are built for. Both share this row, so the styling is switched here rather
	//than duplicating the whole template.
	$is_pos_line = empty($is_direct_sell);

	$td = 'background:#fff;border-top:1px solid #edf0f7;border-bottom:1px solid #edf0f7;padding:12px 14px;vertical-align:middle;';
	$td_first = $td . 'border-left:1px solid #edf0f7;border-radius:12px 0 0 12px;';
	$td_last = $td . 'border-right:1px solid #edf0f7;border-radius:0 12px 12px 0;';

	$s_td = $is_pos_line ? 'style="' . $td . '"' : '';
	$s_td_first = $is_pos_line ? 'style="' . $td_first . '"' : '';
	$s_td_last = $is_pos_line ? 'style="' . $td_last . '"' : '';
@endphp

@foreach($sub_units as $key => $value)
	@if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key)
		@php
			$multiplier = $value['multiplier'];
		@endphp
	@endif
@endforeach

<tr class="product_row" data-row_index="{{$row_count}}" @if(!empty($so_line)) data-so_id="{{$so_line->transaction_id}}" @endif>
	@if(!empty($is_serial_no))
		<td class="serial_no" ></td>
	@endif
	<td {!! $s_td_first !!}>
		@if(!empty($so_line))
			<input type="hidden"
			name="products[{{$row_count}}][so_line_id]"
			value="{{$so_line->id}}">
		@endif
		@php
			$product_image = asset('/img/default.png');
			if(count($product->media) > 0){
				$product_image = $product->media->first()->display_url;
			} elseif(!empty($product->product_image)) {
				$product_image = asset('/uploads/img/' . rawurlencode($product->product_image));
			}

			$product_meta = $product->sub_sku;
			if(!empty($product->brand)){ $product_meta .= ' &middot; ' . $product->brand; }
		@endphp

		@if($is_pos_line)
			<div style="display:flex;align-items:center;gap:12px;min-width:0;">
				<img class="pos_line_thumb" src="{{$product_image}}" alt="{{$product->product_name}}" loading="lazy"
					style="flex:0 0 46px;width:46px;height:46px;border-radius:10px;object-fit:cover;background:#f2f4fa;border:1px solid #e9ecf5;">
				<div style="min-width:0;flex:1 1 auto;">
					@if($edit_price || $edit_discount)
						<span class="pos_line_name cursor-pointer" title="@lang('lang_v1.pos_edit_product_price_help')"
							data-toggle="modal" data-target="#row_edit_product_price_modal_{{$row_count}}"
							style="display:block;font-size:14px;font-weight:600;color:#2d3748;line-height:1.35;">
							{{$product->product_name}}
							<i class="fa fa-pen" style="font-size:10px;color:#646EE4;margin-left:4px;opacity:.75;"></i>
						</span>
					@else
						<span class="pos_line_name" style="display:block;font-size:14px;font-weight:600;color:#2d3748;line-height:1.35;">{{$product->product_name}}</span>
					@endif
					<span class="pos_line_meta" style="display:block;font-size:11.5px;color:#8a94a6;margin-top:2px;">{!! $product_meta !!}</span>
					<span class="pos_line_stock" style="display:inline-block;margin-top:5px;font-size:10.5px;font-weight:700;letter-spacing:.03em;padding:2px 8px;border-radius:20px;@if($product->enable_stock)background:#e8f7f0;color:#12805c;@else background:#f1f3f9;color:#8a94a6;@endif">
						@if($product->enable_stock)
							{{ @num_format($product->qty_available) }} {{$product->unit}} @lang('lang_v1.in_stock')
						@else
							&ndash;&ndash;
						@endif
					</span>
				</div>
			</div>
		@else
			@php
				$product_name = $product->product_name . '<br/>' . $product->sub_sku ;
				if(!empty($product->brand)){ $product_name .= ' ' . $product->brand ;}
			@endphp
			{!! $product_name !!}
			<img src="{{$product_image}}" alt="product-img" loading="lazy" style="height:50px;display: inline;margin-left: 3px; border: black;border-radius: 5px; margin-top: 5px; width: 50px;object-fit: cover;">
			<br>
			<small class="text-muted p-1">
				@if($product->enable_stock)
					{{ @num_format($product->qty_available) }} {{$product->unit}} @lang('lang_v1.in_stock')
				@else
					--
				@endif
			</small>
		@endif

		<input type="hidden" class="enable_sr_no" value="{{$product->enable_sr_no}}">
		<input type="hidden"
			class="product_type"
			name="products[{{$row_count}}][product_type]"
			value="{{$product->product_type}}">

		@php
			$hide_tax = 'hide';
	        if(session()->get('business.enable_inline_tax') == 1){
	            $hide_tax = '';
	        }

			$tax_id = $product->tax_id;
			$item_tax = !empty($product->item_tax) ? $product->item_tax : 0;
			$unit_price_inc_tax = $product->sell_price_inc_tax;

			if($hide_tax == 'hide'){
				$tax_id = null;
				$unit_price_inc_tax = $product->default_sell_price;
			}

			if(!empty($so_line) && $action !== 'edit') {
				$tax_id = $so_line->tax_id;
				$item_tax = $so_line->item_tax;
				$unit_price_inc_tax = $so_line->unit_price_inc_tax;
			}

			$discount_type = !empty($product->line_discount_type) ? $product->line_discount_type : 'fixed';
			$discount_amount = !empty($product->line_discount_amount) ? $product->line_discount_amount : 0;

			if(!empty($discount)) {
				$discount_type = $discount->discount_type;
				$discount_amount = $discount->discount_amount;
			}

			if(!empty($so_line) && $action !== 'edit') {
				$discount_type = $so_line->line_discount_type;
				$discount_amount = $so_line->line_discount_amount;
			}

  			$sell_line_note = '';
  			if(!empty($product->sell_line_note)){
  				$sell_line_note = $product->sell_line_note;
  			}
			  if(!empty($so_line)){
  				$sell_line_note = $so_line->sell_line_note;
  			}

			//A billing line should always show what it is charging, so the POS
			//price column stays visible. The tax column keeps following the
			//inline tax setting, as it did before.
			$price_cell_class = $is_pos_line ? '' : $hide_tax;
  		@endphp

		@if(!empty($discount))
			{!! Form::hidden("products[$row_count][discount_id]", $discount->id); !!}
		@endif

		@php
			$warranty_id = !empty($action) && $action == 'edit' && !empty($product->warranties->first())  ? $product->warranties->first()->id : $product->warranty_id;

			if($discount_type == 'fixed') {
				$discount_amount = $discount_amount * $multiplier;
			}
		@endphp

		@if(empty($is_direct_sell))
		<div class="modal fade row_edit_product_price_model" id="row_edit_product_price_modal_{{$row_count}}" tabindex="-1" role="dialog">
			@include('sale_pos.partials.row_edit_product_price_modal')
		</div>
		@endif

		<!-- Description modal end -->
		@if(in_array('modifiers' , $enabled_modules))
			<div class="modifiers_html">
				@if(!empty($product->product_ms))
					@include('restaurant.product_modifier_set.modifier_for_product', array('edit_modifiers' => true, 'row_count' => $loop->index, 'product_ms' => $product->product_ms ) )
				@endif
			</div>
		@endif

		@php
			$max_quantity = $product->qty_available;
			$formatted_max_quantity = $product->formatted_qty_available;

			if(!empty($action) && $action == 'edit') {
				if(!empty($so_line)) {
					$qty_available = $so_line->quantity - $so_line->so_quantity_invoiced + $product->quantity_ordered;
					$max_quantity = $qty_available;
					$formatted_max_quantity = number_format($qty_available, session('business.quantity_precision', 2), session('currency')['decimal_separator'], session('currency')['thousand_separator']);
				}
			} else {
				if(!empty($so_line) && $so_line->qty_available <= $max_quantity) {
					$max_quantity = $so_line->qty_available;
					$formatted_max_quantity = $so_line->formatted_qty_available;
				}
			}


			$max_qty_rule = $max_quantity;
			$max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=> $formatted_max_quantity, 'unit' => $product->unit  ]);
		@endphp

		@if( session()->get('business.enable_lot_number') == 1 || session()->get('business.enable_product_expiry') == 1)
		@php
			$lot_enabled = session()->get('business.enable_lot_number');
			$exp_enabled = session()->get('business.enable_product_expiry');
			$lot_no_line_id = '';
			if(!empty($product->lot_no_line_id)){
				$lot_no_line_id = $product->lot_no_line_id;
			}
		@endphp
		@if(!empty($product->lot_numbers) && empty($is_sales_order))
			<select class="form-control lot_number input-sm" name="products[{{$row_count}}][lot_no_line_id]" @if(!empty($product->transaction_sell_lines_id)) disabled @endif @if($is_pos_line) style="height:32px;border-radius:8px;border:1px solid #e3e7f1;font-size:12px;margin-top:8px;box-shadow:none;" @endif>
				<option value="">@lang('lang_v1.lot_n_expiry')</option>
				@foreach($product->lot_numbers as $lot_number)
					@php
						$selected = "";
						if($lot_number->purchase_line_id == $lot_no_line_id){
							$selected = "selected";

							$max_qty_rule = $lot_number->qty_available;
							$max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ]);
						}

						$expiry_text = '';
						if($exp_enabled == 1 && !empty($lot_number->exp_date)){
							if( \Carbon::now()->gt(\Carbon::createFromFormat('Y-m-d', $lot_number->exp_date)) ){
								$expiry_text = '(' . __('report.expired') . ')';
							}
						}

						//preselected lot number if product searched by lot number
						if(!empty($purchase_line_id) && $purchase_line_id == $lot_number->purchase_line_id) {
							$selected = "selected";

							$max_qty_rule = $lot_number->qty_available;
							$max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ]);
						}
					@endphp
					<option value="{{$lot_number->purchase_line_id}}" data-qty_available="{{$lot_number->qty_available}}" data-msg-max="@lang('lang_v1.quantity_error_msg_in_lot', ['qty'=> $lot_number->qty_formated, 'unit' => $product->unit  ])" {{$selected}}>@if(!empty($lot_number->lot_number) && $lot_enabled == 1){{$lot_number->lot_number}} @endif @if($lot_enabled == 1 && $exp_enabled == 1) - @endif @if($exp_enabled == 1 && !empty($lot_number->exp_date)) @lang('product.exp_date'): {{@format_date($lot_number->exp_date)}} @endif {{$expiry_text}}</option>
				@endforeach
			</select>
		@endif
	@endif
	@if(!empty($is_direct_sell))
  		<br>
  		<textarea class="form-control" name="products[{{$row_count}}][sell_line_note]" rows="2">{{$sell_line_note}}</textarea>
  		<p class="help-block"><small>@lang('lang_v1.sell_line_description_help')</small></p>
	@endif
	</td>

	<td {!! $s_td !!}>
		{{-- If edit then transaction sell lines will be present --}}
		@if(!empty($product->transaction_sell_lines_id))
			<input type="hidden" name="products[{{$row_count}}][transaction_sell_lines_id]" class="form-control" value="{{$product->transaction_sell_lines_id}}">
		@endif

		<input type="hidden" name="products[{{$row_count}}][product_id]" class="form-control product_id" value="{{$product->product_id}}">

		<input type="hidden" value="{{$product->variation_id}}"
			name="products[{{$row_count}}][variation_id]" class="row_variation_id">

		<input type="hidden" value="{{$product->enable_stock}}"
			name="products[{{$row_count}}][enable_stock]">

		@if(empty($product->quantity_ordered))
			@php
				$product->quantity_ordered = 1;
			@endphp
		@endif

		@php
			$allow_decimal = true;
			if($product->unit_allow_decimal != 1) {
				$allow_decimal = false;
			}
		@endphp
		@foreach($sub_units as $key => $value)
        	@if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key)
        		@php
        			$max_qty_rule = $max_qty_rule / $multiplier;
        			$unit_name = $value['name'];
        			$max_qty_msg = __('validation.custom-messages.quantity_not_available', ['qty'=> $max_qty_rule, 'unit' => $unit_name  ]);

        			if(!empty($product->lot_no_line_id)){
        				$max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', ['qty'=> $max_qty_rule, 'unit' => $unit_name  ]);
        			}

        			if($value['allow_decimal']) {
        				$allow_decimal = true;
        			}
        		@endphp
        	@endif
        @endforeach
		@if($is_pos_line)
			<div class="pos_qty_stepper input-group input-number" style="display:flex;align-items:center;width:132px;max-width:100%;margin:0 auto;border:1px solid #e3e7f1;border-radius:10px;overflow:hidden;background:#f8f9fd;">
				<button type="button" class="quantity-down" style="flex:0 0 34px;height:36px;border:0;background:transparent;color:#d0455e;font-size:13px;cursor:pointer;"><i class="fa fa-minus"></i></button>
		@else
			<div class="input-group input-number">
				<span class="input-group-btn"><button type="button" class="btn btn-default btn-flat quantity-down"><i class="fa fa-minus text-danger"></i></button></span>
		@endif
		<input type="text" data-min="1" @if($is_pos_line) style="flex:1 1 auto;width:100%;min-width:0;height:36px;border:0;border-left:1px solid #e3e7f1;border-right:1px solid #e3e7f1;border-radius:0;background:#fff;text-align:center;font-size:14px;font-weight:700;color:#2d3748;padding:0 4px;box-shadow:none;" @else style="width: auto" @endif
			class="form-control pos_quantity input_number mousetrap input_quantity"
			value="{{@format_quantity($product->quantity_ordered)}}" name="products[{{$row_count}}][quantity]" data-allow-overselling="@if(empty($pos_settings['allow_overselling'])){{'false'}}@else{{'true'}}@endif"
			@if($allow_decimal)
				data-decimal=1
			@else
				data-decimal=0
				data-rule-abs_digit="true"
				data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')"
			@endif
			data-rule-required="true"
			data-msg-required="@lang('validation.custom-messages.this_field_is_required')"
			@if($product->enable_stock && empty($pos_settings['allow_overselling']) && empty($is_sales_order) )
				data-rule-max-value="{{$max_qty_rule}}" data-qty_available="{{$product->qty_available}}" data-msg-max-value="{{$max_qty_msg}}"
				data-msg_max_default="@lang('validation.custom-messages.quantity_not_available', ['qty'=> $product->formatted_qty_available, 'unit' => $product->unit  ])"
			@endif
		>
		@if($is_pos_line)
				<button type="button" class="quantity-up" style="flex:0 0 34px;height:36px;border:0;background:transparent;color:#12805c;font-size:13px;cursor:pointer;"><i class="fa fa-plus"></i></button>
			</div>
		@else
			<span class="input-group-btn"><button type="button" class="btn btn-default btn-flat quantity-up"><i class="fa fa-plus text-success"></i></button></span>
			</div>
		@endif

		<input type="hidden" name="products[{{$row_count}}][product_unit_id]" value="{{$product->unit_id}}">
		@if(count($sub_units) > 0)
			@if(empty($is_pos_line))<br>@endif
			<select name="products[{{$row_count}}][sub_unit_id]" class="form-control input-sm sub_unit" @if($is_pos_line) style="width:132px;max-width:100%;margin:6px auto 0;height:30px;border-radius:8px;border:1px solid #e3e7f1;font-size:12px;box-shadow:none;" @endif>
                @foreach($sub_units as $key => $value)
                    <option value="{{$key}}" data-multiplier="{{$value['multiplier']}}" data-unit_name="{{$value['name']}}" data-allow_decimal="{{$value['allow_decimal']}}" @if(!empty($product->sub_unit_id) && $product->sub_unit_id == $key) selected @endif>
                        {{$value['name']}}
                    </option>
                @endforeach
           </select>
		@else
			@if($is_pos_line)
				<div style="text-align:center;font-size:11px;color:#8a94a6;margin-top:6px;">{{$product->unit}}</div>
			@else
				{{$product->unit}}
			@endif
		@endif

		@if(!empty($product->second_unit))
            <br>
            <span style="white-space: nowrap;">
            @lang('lang_v1.quantity_in_second_unit', ['unit' => $product->second_unit])*:</span><br>
            <input type="text"
            name="products[{{$row_count}}][secondary_unit_quantity]"
            value="{{@format_quantity($product->secondary_unit_quantity)}}"
            class="form-control input-sm input_number"
            required>
        @endif

		<input type="hidden" class="base_unit_multiplier" name="products[{{$row_count}}][base_unit_multiplier]" value="{{$multiplier}}">

		<input type="hidden" class="hidden_base_unit_sell_price" value="{{$product->default_sell_price / $multiplier}}">

		{{-- Hidden fields for combo products --}}
		@if($product->product_type == 'combo'&& !empty($product->combo_products))

			@foreach($product->combo_products as $k => $combo_product)

				@if(isset($action) && $action == 'edit')
					@php
						$combo_product['qty_required'] = $combo_product['quantity'] / $product->quantity_ordered;

						$qty_total = $combo_product['quantity'];
					@endphp
				@else
					@php
						$qty_total = $combo_product['qty_required'];
					@endphp
				@endif

				<input type="hidden"
					name="products[{{$row_count}}][combo][{{$k}}][product_id]"
					value="{{$combo_product['product_id']}}">

					<input type="hidden"
					name="products[{{$row_count}}][combo][{{$k}}][variation_id]"
					value="{{$combo_product['variation_id']}}">

					<input type="hidden"
					class="combo_product_qty"
					name="products[{{$row_count}}][combo][{{$k}}][quantity]"
					data-unit_quantity="{{$combo_product['qty_required']}}"
					value="{{$qty_total}}">

					@if(isset($action) && $action == 'edit')
						<input type="hidden"
							name="products[{{$row_count}}][combo][{{$k}}][transaction_sell_lines_id]"
							value="{{$combo_product['id']}}">
					@endif

			@endforeach
		@endif
	</td>
	@if(!empty($is_direct_sell))
		@if(!empty($pos_settings['inline_service_staff']))
			<td>
				<div class="form-group">
					<div class="input-group">
						{!! Form::select("products[" . $row_count . "][res_service_staff_id]", $waiters, !empty($product->res_service_staff_id) ? $product->res_service_staff_id : null, ['class' => 'form-control select2 order_line_service_staff', 'placeholder' => __('restaurant.select_service_staff'), 'required' => (!empty($pos_settings['is_service_staff_required']) && $pos_settings['is_service_staff_required'] == 1) ? true : false ]); !!}
					</div>
				</div>
			</td>
		@endif
		@php
			$pos_unit_price = !empty($product->unit_price_before_discount) ? $product->unit_price_before_discount : $product->default_sell_price;

			if(!empty($so_line) && $action !== 'edit') {
				$pos_unit_price = $so_line->unit_price_before_discount;
			}
		@endphp
		<td class="@if(!auth()->user()->can('edit_product_price_from_sale_screen')) hide @endif">
			<input type="text" name="products[{{$row_count}}][unit_price]" class="form-control pos_unit_price input_number mousetrap" value="{{@num_format($pos_unit_price)}}" @if(!empty($pos_settings['enable_msp'])) data-rule-min-value="{{$pos_unit_price}}" data-msg-min-value="{{__('lang_v1.minimum_selling_price_error_msg', ['price' => @num_format($pos_unit_price)])}}" @endif>

			@if(!empty($last_sell_line))
				<br>
				<small class="text-muted">@lang('lang_v1.prev_unit_price'): @format_currency($last_sell_line->unit_price_before_discount)</small>
			@endif
		</td>
		<td @if(!$edit_discount) class="hide" @endif>
			{!! Form::text("products[$row_count][line_discount_amount]", @num_format($discount_amount), ['class' => 'form-control input_number row_discount_amount']); !!}<br>
			{!! Form::select("products[$row_count][line_discount_type]", ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')], $discount_type , ['class' => 'form-control row_discount_type']); !!}
			@if(!empty($discount))
				<p class="help-block">{!! __('lang_v1.applied_discount_text', ['discount_name' => $discount->name, 'starts_at' => $discount->formated_starts_at, 'ends_at' => $discount->formated_ends_at]) !!}</p>
			@endif

			@if(!empty($last_sell_line))
				<br>
				<small class="text-muted">
					@lang('lang_v1.prev_discount'):
					@if($last_sell_line->line_discount_type == 'percentage')
						{{@num_format($last_sell_line->line_discount_amount)}}%
					@else
						@format_currency($last_sell_line->line_discount_amount)
					@endif
				</small>
			@endif
		</td>
		<td class="text-center {{$hide_tax}}">
			{!! Form::hidden("products[$row_count][item_tax]", @num_format($item_tax), ['class' => 'item_tax']); !!}

			{!! Form::select("products[$row_count][tax_id]", $tax_dropdown['tax_rates'], $tax_id, ['placeholder' => 'Select', 'class' => 'form-control tax_id'], $tax_dropdown['attributes']); !!}
		</td>

	@else
		@if(!empty($pos_settings['inline_service_staff']))
			<td {!! $s_td !!}>
				<div class="form-group" style="margin-bottom:0;">
					{!! Form::select("products[" . $row_count . "][res_service_staff_id]", $waiters, !empty($product->res_service_staff_id) ? $product->res_service_staff_id : null, ['class' => 'form-control select2 order_line_service_staff', 'placeholder' => __('restaurant.select_service_staff'), 'required' => (!empty($pos_settings['is_service_staff_required']) && $pos_settings['is_service_staff_required'] == 1) ? true : false ]); !!}
				</div>
			</td>
		@endif
	@endif
	<td class="{{$price_cell_class}}" @if($is_pos_line) style="{{$td}}text-align:right;" @endif>
		<input type="text" name="products[{{$row_count}}][unit_price_inc_tax]" class="form-control pos_unit_price_inc_tax input_number"
			@if($is_pos_line)
				style="width:100%;max-width:120px;margin-left:auto;height:36px;text-align:right;border:1px solid #e3e7f1;border-radius:10px;background:{{ $edit_price ? '#fff' : '#f8f9fd' }};font-size:14px;font-weight:600;color:#2d3748;padding:0 10px;box-shadow:none;"
			@else
				style="width: auto"
			@endif
			value="{{@num_format($unit_price_inc_tax)}}" @if(!$edit_price) readonly @endif @if(!empty($pos_settings['enable_msp'])) data-rule-min-value="{{$unit_price_inc_tax}}" data-msg-min-value="{{__('lang_v1.minimum_selling_price_error_msg', ['price' => @num_format($unit_price_inc_tax)])}}" @endif>
	</td>
	@if(!empty($common_settings['enable_product_warranty']) && !empty($is_direct_sell))
		<td>
			{!! Form::select("products[$row_count][warranty_id]", $warranties, $warranty_id, ['placeholder' => __('messages.please_select'), 'class' => 'form-control']); !!}
		</td>
	@endif
	<td class="text-center" @if($is_pos_line) style="{{$td}}text-align:right;" @endif>
		@php
			$subtotal_type = !empty($pos_settings['is_pos_subtotal_editable']) ? 'text' : 'hidden';

		@endphp
		<input type="{{$subtotal_type}}" class="form-control pos_line_total @if(!empty($pos_settings['is_pos_subtotal_editable'])) input_number @endif"
			@if($is_pos_line && $subtotal_type === 'text')
				style="width:100%;max-width:130px;margin-left:auto;height:36px;text-align:right;border:1px solid #e3e7f1;border-radius:10px;font-size:15px;font-weight:700;color:#12805c;padding:0 10px;box-shadow:none;"
			@else
				style="width: auto"
			@endif
			value="{{@num_format($product->quantity_ordered*$unit_price_inc_tax )}}">
		<span class="display_currency pos_line_total_text @if(!empty($pos_settings['is_pos_subtotal_editable'])) hide @endif" data-currency_symbol="true"
			@if($is_pos_line) style="font-size:15px;font-weight:700;color:#12805c;white-space:nowrap;" @endif>{{$product->quantity_ordered*$unit_price_inc_tax}}</span>
	</td>
	<td class="text-center v-center" {!! $s_td_last !!}>
		@if($is_pos_line)
			{{-- pos.js binds the remove handler to `i.pos_remove_row`, so the
			     class has to stay on the icon itself. --}}
			<i class="fa fa-times pos_remove_row cursor-pointer" aria-hidden="true" title="@lang('messages.delete')"
				style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:50%;background:#fdeef1;color:#d0455e;font-size:12px;"></i>
		@else
			<i class="fa fa-times text-danger pos_remove_row cursor-pointer" aria-hidden="true"></i>
		@endif
	</td>
</tr>
