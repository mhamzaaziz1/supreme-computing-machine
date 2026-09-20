<script type="text/javascript">
    //Oil change point behaviour for the POS screen.
    //
    //The vehicle + mileage panel is rendered for every location, because the
    //cashier can switch locations from the header without reloading. This keeps
    //the panel (and its "required" rule) in step with whichever location is
    //selected, so a vehicle is only demanded at an oil change point.
    $(function () {
        var $panel = $('#oil_change_panel');

        if (!$panel.length) {
            return;
        }

        var vehicle_create_url =
            "{{ action([\App\Http\Controllers\CustomerVehicleController::class, 'create'], ['contact_id' => ':contact_id']) }}";
        var vehicle_dropdown_url = '/customer-vehicles-dropdown/';

        function current_location_is_oil_change_point() {
            var $select = $('select#select_location_id');

            if ($select.length) {
                return $select.find(':selected').data('is_oil_change_point') == 1;
            }

            //Single location businesses have no dropdown: fall back to the
            //value the screen was rendered with.
            return $panel.data('default_is_oil_change_point') == 1;
        }

        function toggle_oil_change_panel() {
            var is_oil_change_point = current_location_is_oil_change_point();
            var $vehicle = $('#customer_vehicle_id');

            $panel.toggle(is_oil_change_point);

            if (is_oil_change_point) {
                //jQuery validate reads `required` off the element, and skips
                //anything hidden, so toggling the attribute is all it takes.
                $vehicle.attr('required', 'required');
            } else {
                //Clear the values too, so a sale made at a non oil change
                //point never carries a stale vehicle or mileage reading.
                $vehicle.removeAttr('required').val('');
                $('#previous_mileage, #oil_change_mileage, #next_mileage').val('');
                $panel.find('label.error').remove();
            }
        }

        toggle_oil_change_panel();

        //reset_pos_form() in pos.js runs on location change and clears the
        //form, so re-apply the panel state after it.
        $(document).on('change', 'select#select_location_id', function () {
            setTimeout(toggle_oil_change_panel, 0);
        });

        function reload_vehicles(customer_id, select_id) {
            var $vehicle = $('#customer_vehicle_id');

            $vehicle.empty().append(
                $('<option>').val('').text("{{ __('lang_v1.select_vehicle') }}")
            );

            if (!customer_id) {
                return;
            }

            $.ajax({
                method: 'GET',
                url: vehicle_dropdown_url + customer_id,
                dataType: 'json',
                success: function (result) {
                    $.each(result, function (key, value) {
                        $vehicle.append($('<option>').val(key).text(value));
                    });

                    if (select_id) {
                        $vehicle.val(select_id).change();
                    }
                },
            });
        }

        //Add vehicle popup. The customer owns the vehicle, so one has to be
        //picked first.
        $(document).on('click', '.add_new_vehicle', function () {
            var customer_id = $('#customer_id').val();

            if (!customer_id) {
                toastr.error("{{ __('lang_v1.please_select_customer_first') }}");
                if ($('#customer_id').data('select2')) {
                    $('#customer_id').select2('open');
                }
                return;
            }

            $('div.vehicle_modal')
                .load(vehicle_create_url.replace(':contact_id', customer_id), function () {
                    $(this).modal('show');
                });
        });

        //Saved from the popup: refresh the dropdown and select the new vehicle
        //straight away, so the cashier never has to re-pick it.
        $(document).on('submit', 'form#vehicle_add_form', function (e) {
            e.preventDefault();

            var $form = $(this);
            var $submit = $form.find('button[type="submit"]');

            $.ajax({
                method: 'POST',
                url: $form.attr('action'),
                dataType: 'json',
                data: $form.serialize(),
                success: function (result) {
                    if (result.success) {
                        $('div.vehicle_modal').modal('hide');
                        toastr.success(result.msg);
                        reload_vehicles($('#customer_id').val(), result.id);
                    } else {
                        toastr.error(result.msg);
                        $submit.removeAttr('disabled');
                    }
                },
                error: function () {
                    toastr.error("{{ __('messages.something_went_wrong') }}");
                    $submit.removeAttr('disabled');
                },
            });
        });

        $(document).on('hidden.bs.modal', 'div.vehicle_modal', function () {
            $(this).empty();
        });
    });
</script>
