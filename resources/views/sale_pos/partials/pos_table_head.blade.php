@php
    //Shared header for the POS billing table (create + edit). Kept in one
    //place so the column set can never drift from sale_pos.product_row, which
    //renders the matching cells.
    $pos_th = 'padding:12px 14px;border:0;font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#7b849a;background:#f6f8fd;white-space:nowrap;';
@endphp
<thead>
    <tr>
        <th style="{{ $pos_th }}text-align:left;border-radius:12px 0 0 12px;width:42%;">
            @lang('sale.product') @show_tooltip(__('lang_v1.tooltip_sell_product_column'))
        </th>
        <th style="{{ $pos_th }}text-align:center;width:20%;">
            @lang('sale.qty')
        </th>
        @if (!empty($pos_settings['inline_service_staff']))
            <th style="{{ $pos_th }}text-align:center;width:14%;">
                @lang('restaurant.service_staff')
            </th>
        @endif
        <th style="{{ $pos_th }}text-align:right;width:16%;">
            @lang('sale.price_inc_tax')
        </th>
        <th style="{{ $pos_th }}text-align:right;width:18%;">
            @lang('sale.subtotal')
        </th>
        <th style="{{ $pos_th }}text-align:center;width:48px;border-radius:0 12px 12px 0;"></th>
    </tr>
</thead>
