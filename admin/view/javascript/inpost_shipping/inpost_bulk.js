$(document).on('click', '.bulk-generate-link', function (e) {
    e.preventDefault();
});

$(document).on('click', '.select-all', function (e) {
    e.preventDefault();
    $('.shipments-table tbody .checkbox input').each(function () {
        if(!$(this).is(':checked')) {
            $(this).click();
        }
    });

    return false;
});

$(document).on('click', '.unselect-all', function (e) {
    e.preventDefault();
    $('.shipments-table tbody .checkbox input').each(function () {
        if($(this).is(':checked')) {
            $(this).click();
        }
    });

    return false;
});

$(document).on('click', '#inpost-labels', function(e) {
    let selected = [];
    let userToken = $('#userToken').val();
    if ($('.shipments-table').length !== 0) {
        $('.shipments-table tbody .checkbox input:checked').each(function () {
            selected.push($(this).val());
        });

        selected = selected.join(',');
    }

    if ($('#form-order').length !== 0) {
        $('#form-order table tbody tr td:first-child input:checked').each(function () {
            selected.push($(this).val());
        })
    }

    $.get('index.php?route=extension/shipping/inpost_shipping/getBulkPrintLabelModal&user_token='+userToken, {id: selected}, function(res) {
        if (res.error) {
            alert(res.error);
            return;
        }

        $('body').append(res.printLabelModal);
        $('#inpostPrintLabelModal').modal('show');
    });
});

$(document).on('click', '#button-submit-print-label-form-bulk', function (e) {
    let userToken = $('#userToken').val();
    let ids = $(this).data('id');
    let format = $('#inpostPrintLabelModal [name="labelFormat"]:checked').val();
    let type = $('#inpostPrintLabelModal [name="labelType"]:checked').val();
    $.get('index.php?route=extension/shipping/inpost_shipping/bulkPrintLabels&user_token='+userToken, { ids: ids, format: format, type: type }, function(res) {
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

$(document).on('click', '#inpost-print-dispatch', function (e) {
    let userToken = $('#userToken').val();
    let selected = [];
    $('.shipments-table tbody .checkbox input:checked').each(function(){
        selected.push($(this).val());
    });

    selected = selected.join(',');

    $.get('index.php?route=extension/shipping/inpost_shipping/bulkPrintDispatchLabel&user_token='+userToken, { ids: selected }, function(res) {
        if (typeof res.error !== 'undefined') {
            alert(res.error);
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

$(document).on('click', '#inpost-create-dispatch', function(e) {
    let selected = [];
    let userToken = $('#userToken').val();
    $('.shipments-table tbody .checkbox input:checked').each(function(){
        selected.push($(this).val());
    });

    selected = selected.join(',');
    $.get('index.php?route=extension/shipping/inpost_shipping/getBulkDispatchOrderModal&user_token='+userToken, {id: selected}, function(res) {
        if (res.error) {
            alert(res.error);
            return;
        }

        $('body').append(res.dispatchOrderModal);
        $('#inpostDispatchOrderModal').modal('show');
    });
});

$(document).on('click', '#button-submit-dispatch-order-form-bulk', function (e) {
    let userToken = $('#userToken').val();
    let ids = $(this).data('id');
    let point = $('#inpostDispatchOrderModal [name="dispatchPoint"]').val();
    $.post('index.php?route=extension/shipping/inpost_shipping/bulkDispatchOrderAction&user_token='+userToken, { ids: ids, point: point }, function(res) {
        if (typeof res.error !== 'undefined') {
            alert(res.error);
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

        if (res.success) {
            alert(res.message);
            location.reload();
        }
    });
});

$(document).on('click', '#inpost-shipments', function () {
    let userToken = $('#userToken').val();
    let selected = [];
    $('#form-order table tbody tr td:first-child input:checked').each(function() {
        selected.push($(this).val());
    });

    if (selected.length !== 0) {
        $.post('index.php?route=extension/shipping/inpost_shipping/bulkCreateShipment&user_token='+userToken, {ids: selected}, function(res) {
            for (let i in res) {
                let r = res[i];
                if (r.error) {
                    $('#content > .container-fluid').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> #'+r.id+' - '+r.error+'<div>');
                }

                if (r.success) {
                    $('#content > .container-fluid').prepend('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> '+r.message+'<div>');
                }
            }

            $('#form-order table tbody tr td:first-child input:checked').each(function() {
                $(this).removeAttr('checked');
            });
        });
    }
});