/**
 * Field-operations hooks for the legacy (Blade) POS screen.
 *
 * SellPosController@store answers a sale it will not accept with
 * {success: 0, msg, credit_hold: {...}} or {success: 0, msg, checkin: {...}}.
 * pos.js hands those payloads here; this shows the same choices the new
 * screens offer and, once resolved, calls resend(extra) with the extra
 * query-string fields (an override token, a check-in token) to append.
 */
(function ($) {
    'use strict';

    var base = (window.base_path || '') + '/ops/';

    function call(method, path, data) {
        return $.ajax({
            method: method,
            url: base + path,
            data: data ? JSON.stringify(data) : undefined,
            contentType: 'application/json',
            dataType: 'json',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'), Accept: 'application/json' },
        });
    }

    function money(n) {
        return typeof __currency_trans_from_en === 'function' ? __currency_trans_from_en(n, true) : Number(n).toFixed(2);
    }

    function esc(s) {
        return $('<div>').text(s == null ? '' : String(s)).html();
    }

    function errorOf(xhr) {
        return (xhr && xhr.responseJSON && xhr.responseJSON.message) || 'The server could not complete that.';
    }

    function modal(title, body) {
        var $m = $(
            '<div class="modal fade" tabindex="-1" role="dialog">' +
                '<div class="modal-dialog" role="document"><div class="modal-content" style="border-top:4px solid #be2a2a">' +
                '<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>' +
                '<h4 class="modal-title">' + esc(title) + '</h4></div>' +
                '<div class="modal-body">' + body + '</div>' +
                '</div></div></div>'
        );
        $m.on('hidden.bs.modal', function () {
            $m.remove();
        });
        $('body').append($m);
        $m.modal({ backdrop: 'static' });
        return $m;
    }

    window.GjOps = {
        creditHold: function (hold, resend) {
            var over = null;
            var reasons = '';
            $.each(hold.reasons || [], function (_, r) {
                if (r.code === 'over_limit') over = r;
                reasons += '<p class="text-danger">' + esc(r.message) + '</p>';
            });

            var body =
                '<table class="table table-condensed" style="margin-bottom:10px">' +
                '<tr><td>Outstanding now</td><td class="text-right">' + money(hold.outstanding) + '</td></tr>' +
                '<tr><td>This order on credit</td><td class="text-right">+ ' + money(hold.order_due) + '</td></tr>' +
                (hold.limit !== null ? '<tr><td>Credit limit</td><td class="text-right">' + money(hold.limit) + '</td></tr>' : '') +
                (over ? '<tr class="danger"><th>Over by</th><th class="text-right">' + money(over.over_by) + '</th></tr>' : '') +
                '</table>' + reasons +
                '<div class="list-group">' +
                '<a href="#" class="list-group-item gj-pay"><b>1 · Take a payment now</b><br><small>Add at least ' + money(hold.minimum_payment) + ' in the payment rows, then finalize again</small></a>' +
                '<a href="#" class="list-group-item gj-reduce"><b>2 · Reduce the order</b><br><small>Back to the cart</small></a>' +
                '<a href="#" class="list-group-item gj-override"><b>3 · Ask a manager to override</b><br><small>Manager\'s staff PIN, or send to their inbox</small></a>' +
                '</div>' +
                '<form class="gj-override-form" style="display:none">' +
                '<div class="form-group"><input type="text" class="form-control gj-reason" maxlength="190" placeholder="Why should this go through? (optional)"></div>' +
                '<div class="input-group"><input type="password" class="form-control gj-pin" inputmode="numeric" autocomplete="off" placeholder="Manager PIN">' +
                '<span class="input-group-btn"><button class="btn btn-primary" type="submit">Approve</button>' +
                '<button class="btn btn-default gj-inbox" type="button">Send to inbox</button></span></div>' +
                '</form>' +
                '<p class="gj-status text-muted" style="margin-top:10px"></p>';

            var $m = modal('This order puts ' + (hold.contact_name || 'the customer') + ' over its terms', body);
            var timer = null;
            $m.on('hidden.bs.modal', function () {
                clearInterval(timer);
            });

            var done = function (token, who) {
                clearInterval(timer);
                $m.modal('hide');
                toastr.success('Override approved by ' + who + '. Saving the sale.');
                resend('&credit_override_token=' + encodeURIComponent(token));
            };

            $m.on('click', '.gj-pay', function (e) {
                e.preventDefault();
                $m.modal('hide');
                toastr.info('Add at least ' + money(hold.minimum_payment) + ' in the payment rows, then finalize again.');
            });
            $m.on('click', '.gj-reduce', function (e) {
                e.preventDefault();
                $m.modal('hide');
            });
            $m.on('click', '.gj-override', function (e) {
                e.preventDefault();
                $m.find('.gj-override-form').show().find('.gj-pin').focus();
            });
            $m.on('submit', '.gj-override-form', function (e) {
                e.preventDefault();
                call('POST', 'credit/override', {
                    contact_id: hold.contact_id,
                    amount_due: hold.order_due,
                    pin: $m.find('.gj-pin').val(),
                    reason: $m.find('.gj-reason').val() || null,
                })
                    .done(function (r) {
                        done(r.token, r.approver);
                    })
                    .fail(function (xhr) {
                        $m.find('.gj-pin').val('');
                        $m.find('.gj-status').removeClass('text-muted').addClass('text-danger').text(errorOf(xhr));
                    });
            });
            $m.on('click', '.gj-inbox', function () {
                call('POST', 'credit/request', {
                    contact_id: hold.contact_id,
                    amount_due: hold.order_due,
                    reason: $m.find('.gj-reason').val() || null,
                })
                    .done(function (r) {
                        $m.find('.list-group, .gj-override-form').hide();
                        $m.find('.gj-status').text('Waiting for a manager to decide in their inbox…');
                        timer = setInterval(function () {
                            call('GET', 'approvals/' + r.approval_id).done(function (s) {
                                if (s.status === 'approved' && s.token) done(s.token, 'a manager');
                                if (s.status === 'rejected') {
                                    clearInterval(timer);
                                    $m.find('.gj-status').addClass('text-danger').text('A manager declined the override' + (s.note ? ': ' + s.note : '.'));
                                }
                            });
                        }, 4000);
                    })
                    .fail(function (xhr) {
                        $m.find('.gj-status').addClass('text-danger').text(errorOf(xhr));
                    });
            });
        },

        /**
         * The route is geofenced and this seller has not checked in at the
         * outlet. Take a GPS fix and check in; if that fails, ask why.
         */
        checkin: function (payload, resend) {
            var pos = null;
            var send = function (reason) {
                return call('POST', 'checkin', {
                    contact_id: payload.contact_id,
                    action: payload.action || 'place_order',
                    lat: pos ? pos.lat : null,
                    lng: pos ? pos.lng : null,
                    accuracy: pos ? pos.accuracy : null,
                    reason: reason || null,
                });
            };
            var ask = function (r) {
                var reasons = ['The outlet has moved', 'GPS is weak here', 'Order taken by phone', 'Delivering for another seller'];
                var body =
                    '<p>' + (r.distance ? 'You are about <b>' + r.distance + ' m</b> from the outlet. ' : '') + esc(r.message || 'Your location could not be confirmed.') + '</p>' +
                    '<p class="text-muted"><small>Carry on anyway — why? Your reason is recorded with this visit.</small></p>' +
                    '<form class="gj-reason-form">' +
                    $.map(reasons, function (x) {
                        return '<div class="radio"><label><input type="radio" name="gj_reason" value="' + esc(x) + '"> ' + esc(x) + '</label></div>';
                    }).join('') +
                    '<div class="form-group"><input type="text" class="form-control gj-other" maxlength="190" placeholder="Something else"></div>' +
                    '<button type="submit" class="btn btn-primary">Continue with reason</button></form>' +
                    '<p class="gj-status text-danger" style="margin-top:10px"></p>';
                var $m = modal('Check-in failed at ' + (payload.contact_name || 'this outlet'), body);
                $m.on('submit', '.gj-reason-form', function (e) {
                    e.preventDefault();
                    var reason = $m.find('.gj-other').val() || $m.find('input[name=gj_reason]:checked').val();
                    if (!reason) return;
                    send(reason)
                        .done(function (r2) {
                            if (r2.allowed) {
                                $m.modal('hide');
                                resend('&checkin_token=' + encodeURIComponent(r2.token));
                            }
                        })
                        .fail(function (xhr) {
                            $m.find('.gj-status').text(errorOf(xhr));
                        });
                });
            };
            var go = function () {
                send(null)
                    .done(function (r) {
                        if (r.allowed) resend('&checkin_token=' + encodeURIComponent(r.token));
                        else ask(r);
                    })
                    .fail(function (xhr) {
                        toastr.error(errorOf(xhr));
                    });
            };

            toastr.info('Checking your location…');
            if (!navigator.geolocation) return go();
            navigator.geolocation.getCurrentPosition(
                function (p) {
                    pos = { lat: p.coords.latitude, lng: p.coords.longitude, accuracy: Math.round(p.coords.accuracy) };
                    go();
                },
                go,
                { enableHighAccuracy: true, timeout: 12000, maximumAge: 30000 }
            );
        },
    };
})(jQuery);
