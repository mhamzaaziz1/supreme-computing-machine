<div class="row">
    <div class="col-md-12">
        <div id="customer_analytics_data_div">
            <div class="text-center">
                <i class="fa fa-refresh fa-spin fa-fw"></i> @lang('messages.loading')
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Load customer analytics when the tab is shown
        $('a[href="#advance_analytics_tab"]').on('shown.bs.tab', function (e) {
            loadCustomerAnalytics();
        });

        // Also load it immediately if we're already on the tab
        if ($('#advance_analytics_tab').hasClass('active')) {
            loadCustomerAnalytics();
        }
    });

    function loadCustomerAnalytics() {
        // Get current date range (last 30 days by default)
        var start = moment().subtract(30, 'days').format('YYYY-MM-DD');
        var end = moment().format('YYYY-MM-DD');
        var customer_id = {{ $contact->id }};

        // Show loading indicator
        $('#customer_analytics_data_div').html('<div class="text-center"><i class="fa fa-refresh fa-spin fa-fw"></i> @lang("messages.loading")</div>');

        // Load analytics data
        $.ajax({
            url: "{{ action([\App\Http\Controllers\ReportController::class, 'getCustomerAdvanceAnalytics']) }}",
            data: {
                start_date: start,
                end_date: end,
                customer_ids: [customer_id]
            },
            dataType: 'html',
            timeout: 120000, // 2 minute timeout
            success: function(result) {
                if (!result || result.trim() === '') {
                    $('#customer_analytics_data_div').html('<div class="alert alert-warning">No data returned from server. Please try again.</div>');
                    return;
                }

                try {
                    $('#customer_analytics_data_div').html(result);
                    __currency_convert_recursively($('#customer_analytics_data_div'));

                    // Ensure scripts in the loaded content are executed
                    // This is needed because the charts are initialized in the loaded content
                    var scripts = $('#customer_analytics_data_div script');
                    if (scripts.length > 0) {
                        for (var i = 0; i < scripts.length; i++) {
                            try {
                                eval(scripts[i].innerHTML);
                            } catch (scriptError) {
                                console.error("Error executing script:", scriptError);
                            }
                        }
                    }
                } catch (renderError) {
                    console.error("Error rendering analytics:", renderError);
                    $('#customer_analytics_data_div').html('<div class="alert alert-danger">Error rendering analytics: ' + renderError.message + '</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error loading analytics:", error, "Status:", status);
                var errorMessage = error;
                if (status === 'timeout') {
                    errorMessage = 'Request timed out. The analytics data may be too large or the server is busy.';
                } else if (xhr.responseText) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        // If we can't parse the JSON, just use the raw response text
                        if (xhr.responseText.length < 100) {
                            errorMessage = xhr.responseText;
                        }
                    }
                }
                $('#customer_analytics_data_div').html('<div class="alert alert-danger">Error loading analytics: ' + errorMessage + '</div>');
            }
        });
    }
</script>
