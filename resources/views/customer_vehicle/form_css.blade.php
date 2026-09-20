<style type="text/css">
    /* Travels with the popup, because it is loaded into pages that do not share
       a stylesheet. Only what an inline style cannot express lives here: focus
       rings and the dark theme, which has to win over the inline light palette
       in customer_vehicle.create / customer_vehicle.edit. */
    .vehicle_form_modal input:focus,
    .vehicle_form_modal textarea:focus {
        outline: none;
        border-color: #646EE4 !important;
        box-shadow: 0 0 0 3px rgba(100, 110, 228, .14) !important;
    }

    /* The theme paints .btn-primary with !important, so the popup's own accent
       has to be forced here rather than left on the inline style. */
    .vehicle_form_modal .btn-primary {
        background: #646EE4 !important;
        border-color: #646EE4 !important;
        color: #fff !important;
    }

    .vehicle_form_modal .btn-primary:hover,
    .vehicle_form_modal .btn-primary:focus {
        background: #4d57cf !important;
        border-color: #4d57cf !important;
    }

    .dark .vehicle_form_modal .modal-body {
        background: #1e293b !important;
    }

    .dark .vehicle_form_modal .modal-footer {
        background: #1e293b !important;
        border-top-color: #334155 !important;
    }

    .dark .vehicle_form_modal label {
        color: #94a3b8 !important;
    }

    .dark .vehicle_form_modal input,
    .dark .vehicle_form_modal textarea {
        background: #0f172a !important;
        border-color: #334155 !important;
        color: #f1f5f9 !important;
    }
</style>
