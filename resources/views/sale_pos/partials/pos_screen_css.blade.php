<style type="text/css">
    /* Interaction states for the POS billing lines. Everything static lives as
       inline styles on the elements themselves (see sale_pos.product_row);
       only what a style attribute cannot express is kept here. */
    #pos_table tbody tr.product_row td {
        box-shadow: 0 1px 2px rgba(17, 17, 26, 0.04);
        transition: box-shadow .15s ease, border-color .15s ease;
    }

    #pos_table tbody tr.product_row:hover td {
        border-color: #d9def0 !important;
        box-shadow: 0 4px 14px rgba(17, 17, 26, 0.08);
    }

    #pos_table tbody tr.product_row .pos_line_name:hover {
        color: #646EE4 !important;
    }

    #pos_table .pos_qty_stepper .quantity-up:hover {
        background: #e8f7f0 !important;
    }

    #pos_table .pos_qty_stepper .quantity-down:hover {
        background: #fdeef1 !important;
    }

    #pos_table tbody tr.product_row .pos_remove_row:hover {
        background: #d0455e !important;
        color: #fff !important;
    }

    #pos_table input:focus,
    #pos_table select:focus,
    .oil_change_panel input:focus,
    .oil_change_panel select:focus {
        border-color: #646EE4 !important;
        box-shadow: 0 0 0 3px rgba(100, 110, 228, .14) !important;
    }

    .oil_change_panel .add_new_vehicle:hover {
        background: #4d57cf !important;
        border-color: #4d57cf !important;
    }

    /* Validation messages. These fields sit in flex rows with no bootstrap
       form-group to lean on, so the label needs its own placement. */
    #pos_table label.error,
    .oil_change_panel label.error {
        display: block;
        margin: 4px 0 0;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.3;
        color: #d0455e;
    }

    #pos_table .pos_qty_stepper + label.error {
        width: 132px;
        max-width: 100%;
        margin: 4px auto 0;
        text-align: center;
    }

    /* jQuery validate drops this one inside the vehicle input group, where it
       would stretch the row; hang it underneath instead. */
    .oil_change_panel .oil_vehicle_field label.error {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        margin: 0;
        padding-top: 4px;
    }

    /* Empty basket hint: shown while the table holds no rows. Keyed on the
       absence of a row rather than tbody:empty, because removing a row leaves
       the whitespace text nodes jQuery appended with it. Browsers without
       :has() simply drop these rules and the hint stays hidden. */
    .pos_table_wrap:not(:has(#pos_table tbody tr)) .pos_empty_hint {
        display: block !important;
    }

    .pos_table_wrap:not(:has(#pos_table tbody tr)) #pos_table thead {
        opacity: .55;
    }

    @media (max-width: 767px) {
        .pos_table_wrap {
            padding: 4px !important;
        }

        #pos_table tbody tr.product_row td {
            padding: 10px 8px !important;
        }
    }

    /* Dark theme. The billing line and the oil change panel carry their light
       palette as inline styles (that is the whole point of them being inline),
       so `html.dark` has to override them here. Tokens match tailwind.config.js
       (dark.bg / surface / border / text). */
    .dark .pos_table_wrap {
        background: #0f172a !important;
        border-color: #334155 !important;
    }

    .dark #pos_table thead th {
        background: #1e293b !important;
        color: #94a3b8 !important;
    }

    .dark #pos_table tbody tr.product_row td {
        background: #1e293b !important;
        border-color: #334155 !important;
        box-shadow: none;
    }

    .dark #pos_table tbody tr.product_row:hover td {
        border-color: #475569 !important;
    }

    .dark #pos_table tbody tr.product_row .pos_line_name {
        color: #f1f5f9 !important;
    }

    .dark #pos_table tbody tr.product_row .pos_line_meta {
        color: #94a3b8 !important;
    }

    .dark #pos_table tbody tr.product_row .pos_line_stock {
        background: #0f172a !important;
        color: #34d399 !important;
    }

    .dark #pos_table tbody tr.product_row .pos_line_thumb {
        background: #0f172a !important;
        border-color: #334155 !important;
    }

    .dark #pos_table .pos_qty_stepper {
        background: #0f172a !important;
        border-color: #334155 !important;
    }

    .dark #pos_table input.pos_quantity,
    .dark #pos_table input.pos_unit_price_inc_tax,
    .dark #pos_table input.pos_line_total,
    .dark #pos_table select.sub_unit,
    .dark #pos_table select.lot_number,
    .dark .oil_change_panel input,
    .dark .oil_change_panel select {
        background: #0f172a !important;
        border-color: #334155 !important;
        color: #f1f5f9 !important;
    }

    .dark #pos_table tbody tr.product_row .pos_line_total_text {
        color: #34d399 !important;
    }

    .dark .oil_change_panel .oil_change_card {
        background: #1e293b !important;
        border-color: #334155 !important;
    }

    .dark .oil_change_panel label {
        color: #94a3b8 !important;
    }

    .dark .oil_change_panel strong {
        color: #f1f5f9 !important;
    }

    .dark .oil_change_panel .oil_field_addon {
        background: #0f172a !important;
        border-color: #334155 !important;
    }

    .dark #pos_table tbody tr.product_row .pos_remove_row {
        background: #3b1f2a !important;
        color: #fb7185 !important;
    }

    .dark #pos_table tbody tr.product_row .pos_remove_row:hover {
        background: #be123c !important;
        color: #fff !important;
    }

    .dark .pos_empty_hint {
        color: #64748b !important;
    }
</style>
