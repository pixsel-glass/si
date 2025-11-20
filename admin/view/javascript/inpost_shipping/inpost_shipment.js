function renderSwitchers() {
    $('.js-switch').each(function() {
        if (typeof $(this).data('switchery') === 'undefined') {
            new Switchery(this, { size: 'small' });
        }
    });
}

function setShippingOptions() {
    let apiDeliveryServices = JSON.parse($('input[id="input-api-services"]').val());
    let apiShippingMethods = JSON.parse($('input[id="input-api-shipping-methods"]').val());
    let service = $('#inpostCreateNewShipmentModal #inpostDeliveryService').val();
    $('select#inpostShippingMethod option').each(function () { $(this).remove() });

    let offeredSelection = $('#inpostShippingMethod').data('offer');

    for (let methodKey in apiDeliveryServices[service]['shippingMethods']) {
        let isDisabled = apiDeliveryServices[service]['shippingMethods'][methodKey] ? '' : 'disabled';
        let preSelected = '';
        if (methodKey === offeredSelection) {
            preSelected = 'selected="selected"';
        }
        if (isDisabled) {
            preSelected = '';
        }
        $('select#inpostShippingMethod').append(
            '<option value="'+methodKey+'" '+isDisabled+' '+preSelected+'>'+apiShippingMethods[methodKey].description+'</option>'
        );
    }
}

function setDefaultSizeTemplate() {
    let service = $('#inpostCreateNewShipmentModal #inpostDeliveryService').val();
    let availableServices = [
        'inpost_locker_standard',
        'inpost_locker_allegro',
        'inpost_locker_pass_thru',
        'inpost_courier_c2c',
    ];

    if (availableServices.includes(service)) {
        $('.predefined-dimensions').css('display', 'block');
        $('#inpostParcelLocker').parent().parent().css('display', 'block');
    } else {
        $('.predefined-dimensions').css('display', 'none');
        $('#inpostParcelLocker').parent().parent().css('display', 'none');
    }

    if (availableServices.includes(service) && $('#inpostPredefinedDimensions').is(':checked')) {
        $('.sizeTemplateWrapper').css('display', 'block');

        if (service !== 'inpost_courier_c2c') {
            $('#inpostTemplate option[value="size_d"]').css('display', 'none');
        } else {
            $('#inpostTemplate option[value="size_d"]').css('display', 'block');
        }
    } else {
        $('.sizeTemplateWrapper').css('display', 'none');
    }

    showPredefinedDimensions();
}

function showCodValue() {
    if ($('#inpostCod').is(':checked')) {
        $('.cod-total').css('display', 'block');
    } else {
        $('.cod-total').css('display', 'none');
    }
}

function showPredefinedDimensions() {
    let service = $('#inpostCreateNewShipmentModal #inpostDeliveryService').val();
    let availableServices = [
        'inpost_locker_standard',
        'inpost_locker_allegro',
        'inpost_locker_pass_thru',
        'inpost_courier_c2c',
    ];

    if (availableServices.includes(service) && $('#inpostPredefinedDimensions').is(':checked')) {
        $('.sizeTemplateWrapper').css('display', 'block');
        $('.dimensions').each(function() {
            $(this).css('display', 'none');
        });
    } else {
        $('.sizeTemplateWrapper').css('display', 'none');
        $('.dimensions').each(function() {
            $(this).css('display', 'block');
        });
    }
}

$(document).on('change', '#inpostDeliveryService', function() {
    setDefaultSizeTemplate();
    setShippingOptions();
});

$(document).on('change', '#inpostCod', function() {
    showCodValue();
});

$(document).on('change', '#inpostPredefinedDimensions', function() {
    showPredefinedDimensions();
});

$(document).ready(function () {
    $('head').append('<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>');
});

$(document).on('click', '#button-close-create-shipment-modal', function() {
    $('#inpostCreateNewShipmentModal').modal('hide');
});

$(document).on('click', '#button-submit-create-shipment-form', function() {
    let token = $('#userToken').val();
    let postData = $('#form-new-shipment').serializeObject();
    $.post('index.php?route=extension/shipping/inpost_shipping/createShipmentAction&user_token='+token, postData, function(res) {
        if (res.error) {
            alert(res.error.errorType + "\n" + res.error.errorDescription);
        }

        if (res.success) {
            alert('Shipment created successfully');
            location.reload();
        }
    });
});

$(document).on('click', '.print-label', function(e) {
    let token = $('#userToken').val();
    let id = $(this).parent().parent().data('id');
    $.get('index.php?route=extension/shipping/inpost_shipping/getPrintLabelModal&user_token='+token, {id: id}, function(res) {
        if (res.error) {
            alert(res.error);
            return;
        }

        $('body').append(res.printLabelModal);
        $('#inpostPrintLabelModal').modal('show');
    });
});

$(document).on('click', '#button-submit-print-label-form', function() {
    let token = $('#userToken').val();
    let id = $(this).data('id');
    let format = $('#inpostPrintLabelModal [name="labelFormat"]:checked').val();
    let type = $('#inpostPrintLabelModal [name="labelType"]:checked').val();
    $.get('index.php?route=extension/shipping/inpost_shipping/printLabel&user_token='+token, {id: id, format: format, type: type}, function(res) {
        if (typeof res.error !== 'undefined') {
            alert(res.details);
            return;
        }

        if (typeof res[0] !== 'undefined') {
            if (typeof res[0].details == 'object') {
                alert(res[0].message);
                return;
            }
            alert(res[0].details);
            return;
        }

        let splitUrl = res.labelUrl.split('/');
        let filename = splitUrl[splitUrl.length - 1];

        downloadFile(res.labelUrl, filename);

        $('#inpostPrintLabelModal').modal('hide');
    });
});

$(document).on('click', '.create-dispatch-order', function(e) {
    let token = $('#userToken').val();
    let id = $(this).parent().parent().data('id');
    $.get('index.php?route=extension/shipping/inpost_shipping/getDispatchOrderModal&user_token='+token, {id: id}, function(res) {
        if (res.error) {
            alert(res.error);
            return;
        }

        $('body').append(res.dispatchOrderModal);
        $('#inpostDispatchOrderModal').modal('show');
    });
});

$(document).on('click', '#button-submit-dispatch-order-form', function() {
    let token = $('#userToken').val();
    let id = $(this).data('id');
    let point = $('#inpostDispatchOrderModal [name="dispatchPoint"]').val();
    $.post('index.php?route=extension/shipping/inpost_shipping/dispatchOrderAction&user_token='+token, {id: id, point: point}, function(res) {
        if (res.error) {
            alert(res.error);
            return;
        }

        if (res.success) {
            alert(res.message);
            location.reload();
        }
    });
});

$(document).on('click', '.print-dispatch-order', function() {
    let token = $('#userToken').val();
    let id = $(this).parent().parent().data('id');
    $.get('index.php?route=extension/shipping/inpost_shipping/printDispatchLabel&user_token='+token, {id: id}, function(res) {
        if (typeof res.error !== 'undefined') {
            alert(res.details);
            return;
        }

        if (typeof res[0] !== 'undefined') {
            alert(res[0].details);
            return;
        }

        let splitUrl = res.labelUrl.split('/');
        let filename = splitUrl[splitUrl.length - 1];

        downloadFile(res.labelUrl, filename);
    });
});

$(document).on('click', '#button-close-print-label-modal', function() {
    $('#inpostPrintLabelModal').modal('hide');
});

$(document).on('click', '#button-close-dispatch-order-modal', function() {
    $('#inpostDispatchOrderModal').modal('hide');
});

$(document).on('click', '#button-create-pickup-point', function() {
    let token = $('#userToken').val();
    location.href = location.protocol + '//' + location.host + location.pathname + '?route=extension/shipping/inpost_shipping&user_token='+token+'&tab=pickup-points';
});

function downloadFile(path, filename) {
    const anchor = document.createElement('a');
    anchor.href = path;
    anchor.target = '_blank';
    anchor.download = filename;
    document.body.appendChild(anchor);
    anchor.click();
    document.body.removeChild(anchor);
}

$(document).on('click', '.return-shipment', function() {
    const anchor = document.createElement('a');
    anchor.target = '_blank';
    anchor.href = $('#returnsUrl').val();
    document.body.appendChild(anchor);
    anchor.click();
    document.body.removeChild(anchor);
});

$(document).on('click', '.view-order', function () {
    let idOrder = $(this).data('order');
    let userToken = $('#userToken').val();

    location.href = 'index.php?route=sale/order/info&order_id=' + idOrder + '&user_token=' + userToken + '#tab-inpost';
    return;
});

$(document).on('click', '.sync-tn', function() {
    let parentTd = $(this).parent();
    let userToken = $('#userToken').val();
    let id = $(this).parent().parent().data('id');

    $.post('index.php?route=extension/shipping/inpost_shipping/syncTrackingNumber&user_token='+userToken, {'id': id}, function (res) {
        if (res.error) {
            if (res.error_details.length !== 0) {
                alert(res.error + "\n" + res.error_details);
                return
            }
            alert(res.error);
            return
        }

        if (res.success) {
            parentTd.html(res.tracking_number);
        }
    });
});

$(document).on('hidden.bs.modal', function (e) {
    let id = $(e.target).attr('id');
    $('#'+id).remove();
});

$.fn.serializeObject = function() {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};