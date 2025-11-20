$(document).ready(function () {
    if ($('input[type=radio][name=smartbill_custom_checkout]').length) {
        if ($('input[name=smartbill_custom_checkout]:checked').val() === '0') {
            $('.smartbill-checkout-table').hide();
        }
        $('input[type=radio][name=smartbill_custom_checkout]').change(function () {
            if ($('input[name=smartbill_custom_checkout]:checked').val() === '0') {
                $('.smartbill-checkout-table').hide();
            } else {
                $('.smartbill-checkout-table').show();
            }
        });
    }

    if ($('input[type=radio][name=smartbill_sync_stock]').length) {
        if ($('input[type=radio][name=smartbill_sync_stock]:checked').val() == '1') {
            $('.smrt-hide-sync-settings').show();
        } else {
            $('.smrt-hide-sync-settings').hide();
        }

        $('input[type=radio][name=smartbill_sync_stock]').change(function () {
            if (this.value == '1') {
                $('.smrt-hide-sync-settings').show();
            } else {
                $('.smrt-hide-sync-settings').hide();
            }
        });
    }

    if ($('input[type=radio][name=smartbill_send_mail_with_document]').length) {
        if ($('input[name=smartbill_send_mail_with_document]:checked').val() === '0') {
            $('.issue-document').hide();
        }
        $('input[type=radio][name=smartbill_send_mail_with_document]').change(function () {
            if ($('input[name=smartbill_send_mail_with_document]:checked').val() === '0') {
                $('.issue-document').hide();
            } else {
                $('.issue-document').show();
            }
        });
    }

    if ($('input[type=radio][name=smartbill_save_stock_history]').length) {
        if ($('input[type=radio][name=smartbill_save_stock_history]:checked').val() == '1') {
            $('#smartbill-download-sync-stock-history').show();
        } else {
            $('#smartbill-download-sync-stock-history').hide();
        }

        $('input[type=radio][name=smartbill_save_stock_history]').change(function () {
            if (this.value == '1') {
                $('#smartbill-download-sync-stock-history').show();
            } else {
                $('#smartbill-download-sync-stock-history').hide();
            }
        });
    }

    $('.smrt_url').click((e) => {
        navigator.clipboard.writeText($('.smrt_url').text()).then(() => {
            Toastify({
                text: "URL-ul a fost copiat cu succes!", duration: 3000, newWindow: false, close: true, gravity: "top", position: 'center', backgroundColor: "#00A14B", stopOnFocus: true,
            }).showToast()
        });
    });

    //hide setting order status
    if ($('input[type=radio][name=smartbill_automatically_issue_document]').length) {
        if ($('input[name=smartbill_automatically_issue_document]:checked').val() === '0') {
            $('.order_status').hide();
        }
        $('input[type=radio][name=smartbill_automatically_issue_document]').change(function () {
            if ($('input[name=smartbill_automatically_issue_document]:checked').val() === '0') {
                $('.order_status').hide();
            } else {
                $('.order_status').show();
            }
        });
    }

    if ($('input[type=radio][name=smartbill_issue_with_due_date]').length) {
        if ($('input[name=smartbill_issue_with_due_date]:checked').val() === '0') {
            $('.smartbill_due_days').hide();
        }
        $('input[type=radio][name=smartbill_issue_with_due_date]').change(function () {
            if ($('input[name=smartbill_issue_with_due_date]:checked').val() === '0') {
                $('.smartbill_due_days').hide();
            } else {
                $('.smartbill_due_days').show();
            }
        });
    }

    if ($('input[type=radio][name=smartbill_show_delivery_days]').length) {
        if ($('input[name=smartbill_show_delivery_days]:checked').val() === '0') {
            $('.smartbill_delivery_days').hide();
        }
        $('input[type=radio][name=smartbill_show_delivery_days]').change(function () {
            if ($('input[name=smartbill_show_delivery_days]:checked').val() === '0') {
                $('.smartbill_delivery_days').hide();
            } else {
                $('.smartbill_delivery_days').show();
            }
        });
    }

    if ($('input[type=radio][name=smartbill_order_include_transport]').length) {
        if ($('input[name=smartbill_order_include_transport]:checked').val() === '0') {
            $('.smartbill_shipping_settings').hide();
        }
        $('input[type=radio][name=smartbill_order_include_transport]').change(function () {
            if ($('input[name=smartbill_order_include_transport]:checked').val() === '0') {
                $('.smartbill_shipping_settings').hide();
            } else {
                $('.smartbill_shipping_settings').show();
            }
        });
    }

    if ($('input[type=radio][name=smartbill_add_delegate_data]').length) {
        if ($('input[name=smartbill_add_delegate_data]:checked').val() === '0') {
            $('.smartbill_issuer_settings').hide();
        }
        $('input[type=radio][name=smartbill_add_delegate_data]').change(function () {
            if ($('input[name=smartbill_add_delegate_data]:checked').val() === '0') {
                $('.smartbill_issuer_settings').hide();
            } else {
                $('.smartbill_issuer_settings').show();
            }
        });
    }

    if ($('#smartbill_mail').length) {
        $('#smartbill_mail').on('click', function (e) {
            e.preventDefault();
            var api_call = $(this).attr('href');
            call_mail_doc(api_call);
        });
    }

    if ($('#smartbill-download-sync-stock-history').length) {
        $('#smartbill-download-sync-stock-history').on('click', function (e) {
            e.preventDefault();
            var api_call = $(this).attr('href');
            var request = new XMLHttpRequest();
            request.open('POST', api_call, true);
            request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            request.responseType = 'blob';
            request.onload = function () {
                if (request.status === 200 && request.response.size != 0) {
                    var disposition = request.getResponseHeader('content-disposition');
                    var matches = /"([^"]*)"/.exec(disposition);
                    var filename = (matches != null && matches[1] ? matches[1] : 'smartbill_sincronizare_stocuri.zip');
                    var blob = new Blob([request.response], { type: 'application/octetstream' });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    alert("Eroare! Fisier inexistend!")
                }
            };

            request.send('content=' + content);
        });
    }

    if ($('#smartbill-manually-sync-stock').length) {
        $('#smartbill-manually-sync-stock').on('click', function (e) {
            e.preventDefault();
            var warehouse = $("#smartbill_used_stock").find(":selected").val();
            var info_message = document.createElement("div");
            if ('' == warehouse || 'fara-gestiune' == warehouse) {
                Toastify({
                    text: "Este necesara selectarea gestiunii din care vor fi preluate stocurile.", duration: -1, newWindow: false, close: true, gravity: "top", position: 'center', backgroundColor: "#EF4136", stopOnFocus: true,
                }).showToast();
            } else {
                info_message.innerHTML = "Stocurile produselor din Opencart <strong>vor fi actualizate</strong> cu stocurile produselor din <strong>gestiunea " + warehouse + "</strong>."
                swal({
                    title: 'Atentie!',
                    content: info_message,
                    icon: 'warning',
                    buttons: ['Renunta', 'Actualizeaza stocuri']
                }).then(function (result) {
                    if (!result) {
                        return false;
                    };
                    info_message = document.createElement("div");
                    info_message.innerHTML = "Va rugam asteptati finalizarea preluarii...";

                    swal({
                        title: "Se incarca!",
                        content: info_message,
                        icon: 'info',
                        buttons: [false, false]
                    });

                    var api_call = $('#smartbill-manually-sync-stock').attr('href') + '&warehouse=' + warehouse;
                    $.ajax({
                        url: api_call,
                        success: function (response) {
                            try {
                                response = JSON.parse(response);
                                info_message = document.createElement("div")
                                info_message.innerHTML = response.data;
                                if (undefined != response.data) {
                                    swal.close();
                                    swal({
                                        title: "",
                                        content: info_message,
                                        icon: response.icon,
                                        buttons: ['Am inteles', 'Descarca istoric']
                                    }).then(function (result) {
                                        if (!result) {
                                            return false;
                                        };
                                        swal({
                                            title: "Descarcarea documentului va incepe curand…",
                                            content: document.createElement("div"),
                                            icon: 'info',
                                            buttons: [false, false]
                                        });
                                        // manual_stoc_update
                                        api_call = $('#smartbill-manually-sync-stock').attr('href').replace('manual_stoc_update', 'download_manual_stock_history');
                                        var request = new XMLHttpRequest();
                                        request.open('POST', api_call, true);
                                        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                                        request.responseType = 'blob';
                                        request.onload = function () {
                                            if (request.status === 200 && request.response.size != 0) {
                                                var disposition = request.getResponseHeader('content-disposition');
                                                var matches = /"([^"]*)"/.exec(disposition);
                                                var filename = (matches != null && matches[1] ? matches[1] : 'smartbill_sincronizare_stocuri.zip');
                                                var blob = new Blob([request.response], { type: 'application/octetstream' });
                                                var link = document.createElement('a');
                                                link.href = window.URL.createObjectURL(blob);
                                                link.download = filename;
                                                document.body.appendChild(link);
                                                link.click();
                                                document.body.removeChild(link);
                                                swal.close();
                                            } else {
                                                Toastify({
                                                    text: "Eroare! " + error, duration: -1, newWindow: false, close: true, gravity: "top", position: 'center', backgroundColor: "#EF4136", stopOnFocus: true,
                                                }).showToast();
                                                swal.close();
                                            }
                                        };

                                        request.send('content=' + content);


                                    });
                                } else {
                                    throw response.error;
                                }

                            } catch (error) {
                                Toastify({
                                    text: "Eroare! " + error, duration: -1, newWindow: false, close: true, gravity: "top", position: 'center', backgroundColor: "#EF4136", stopOnFocus: true,
                                }).showToast();
                                swal.close();
                            }
                        },
                        error: function (error) {
                            Toastify({
                                text: "Eroare! " + error, duration: -1, newWindow: false, close: true, gravity: "top", position: 'center', backgroundColor: "#EF4136", stopOnFocus: true,
                            }).showToast();
                            swal.close();
                        }
                    });

                });
            }
        });
    }


    // if in order info
    if ($('#smartbill_issue').length) {
        $('#smartbill_issue').on('click', function (e) {
            e.preventDefault();

            $(this).addClass('disabled');
            var loading = '<span class="btn pull-right" id="smrt_loading"><i class="fa fa-gear"></i></span>';
            $(this).parent().append(loading);
            $('.smrt-doc').hide();

            var api_call = $(this).attr('href');

            if ($(this).hasClass('reissue')) {
                var attention_text = document.createElement("div");
                attention_text.innerHTML = "Doriti reemiterea documentului in SmartBill Cloud?<br/>Va recomandam sa anulati sau sa stergeti documentul emis anterior in SmartBill Cloud inainte de reemitere.";
                swal({
                    title: "Atentie!",
                    content: attention_text,
                    icon: 'warning',
                    buttons: [true, true]
                }).then(function (result) {
                    if (!result) {
                        $('#smartbill_issue').removeClass('disabled');
                        $('#smrt_loading').remove();
                        $('.smrt-doc').show();
                        return false;
                    }
                    call_issue_doc(api_call);
                });
            } else {
                call_issue_doc(api_call);
            }
        });
    }

    function IsJsonString(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }
    function call_mail_doc(api_call) {
        var info_text = document.createElement("div");
        info_text.innerHTML = "Va rugam asteptati raspunsul serverului...";
        swal({
            title: 'Se incarca!',
            content: info_text,
            icon: 'info',
            buttons: [false, false]
        });
        $.ajax({
            url: api_call,
            success: function (data) {
                if (IsJsonString(data)) {
                    data = JSON.parse(data);
                } else {
                    var new_data = {};
                    new_data.message = data;
                    new_data.error = 'Not JSON';
                    data = new_data;
                }
                if (typeof data.error !== 'undefined' && !data.status) {
                    if (data.message == '') {
                        data.message = 'Verificati setarile modulului.';
                    }
                    Toastify({
                        text: "Eroare! " + data.message.replace("Smart Bill", "SmartBill").replace("SmartBill Cloud", "SmartBill"), duration: -1, newWindow: false, close: true, gravity: "top", position: 'center', backgroundColor: "#EF4136", stopOnFocus: true,
                    }).showToast()
                } else {
                    Toastify({
                        text: data.message.replace("Smart Bill", "SmartBill").replace("SmartBill Cloud", "SmartBill"), duration: 3000, newWindow: false, close: true, gravity: "top", position: 'center', backgroundColor: "#00A14B", stopOnFocus: true,
                    }).showToast();
                }
                swal.close();
            },
            error: function (data) {
                Toastify({
                    text: "Eroare! " + data.responseText.replace("Smart Bill", "SmartBill").replace("SmartBill Cloud", "SmartBill"), duration: -1, newWindow: false, close: true, gravity: "top", position: 'center', backgroundColor: "#EF4136", stopOnFocus: true,
                }).showToast();
                swal.close();

            }
        });
    }

    function call_issue_doc(api_call) {
        var info_text = document.createElement("div");
        info_text.innerHTML = "Va rugam asteptati raspunsul serverului...";
        swal({
            title: 'Se incarca!',
            content: info_text,
            icon: 'info',
            buttons: [false, false]
        });
        $.ajax({
            url: api_call,
            success: function (data) {
                $('#smartbill_issue').removeClass('disabled');
                $('#smrt_loading').remove();
                if (IsJsonString(data)) {
                    data = JSON.parse(data);
                } else {
                    var new_data = {};
                    new_data.message = data;
                    new_data.error = 'Not JSON';
                    data = new_data;
                }
                if (typeof data.error !== 'undefined' && !data.status) {
                    if (data.message == '') {
                        data.message = 'Verificati setarile modulului.';
                    }
                    if (data.message == '408') {
                        data.message = "A aparut o eroare la contactarea serverului SmartBill.Acceseaza cloud.smartbill.com, anuleaza factura pentru comanda #" + data.number + " si incearca din nou facturarea comenzii din magazinul online.";
                    }

                    Toastify({
                        text: "Eroare! " + data.message.replace("Smart Bill", "SmartBill").replace("SmartBill Cloud", "SmartBill"), duration: -1, newWindow: false, close: true, gravity: "top", position: 'center', backgroundColor: "#EF4136", stopOnFocus: true,
                    }).showToast();

                } else {
                    action = "emis";
                    if (!$('a.smrt-doc').length) {
                        var smartbill_mail_link = document.createElement('a');
                        smartbill_mail_link.href = $('#smartbill_issue')[0].href.replace('/smartbill_document', "/smartbill_document/send_mail");
                        smartbill_mail_link.className = "btn pull-right btn-info";
                        smartbill_mail_link.innerHTML = 'Trimite factura clientului';
                        smartbill_mail_link.id = "smartbill_mail";
                        $('#smartbill_issue').after(smartbill_mail_link);
                        $('#smartbill_mail').on('click', function (e) {
                            e.preventDefault();
                            var api_call = $(this).attr('href');
                            call_mail_doc(api_call);
                        });
                        var smartbill_link = document.createElement('a');
                        smartbill_link.href = data.documentUrl;
                        smartbill_link.target = "_blank";
                        smartbill_link.className = "btn pull-right smrt-doc";
                        smartbill_link.innerHTML = 'Deschide document';
                        $('#smartbill_issue').after(smartbill_link);
                    } else {
                        action = "remis";
                        $('a.smrt-doc').attr('href', data.documentUrl).show();
                    }
                    $('#smartbill_issue').addClass('reissue').html('Reemite document');

                    Toastify({
                        text: "Documentul a fost " + action + " cu succes: " + data.series + " " + data.number + ".", duration: 3000, newWindow: false, close: true, gravity: "top", position: 'center', backgroundColor: "#00A14B", stopOnFocus: true,
                    }).showToast();
                }
                swal.close();
            },
            error: function (data) {
                $('#smartbill_issue').removeClass('disabled');
                $('#smrt_loading').remove();
                $('a.smrt-doc').show();

                Toastify({
                    text: "Eroare! " + data.responseText.replace("Smart Bill", "SmartBill").replace("SmartBill Cloud", "SmartBill"), duration: -1, newWindow: false, close: true, gravity: "top", position: 'center', backgroundColor: "#EF4136", stopOnFocus: true,
                }).showToast();
                swal.close();

            }
        });
    }

    // if in settings
    if ($('select[name="smartbill_document_type"]').length) {
        $('select[name="smartbill_document_type"]').on('change', function (e) {
            var $form = $(this).closest('.panel-body');
            switch ($(this).val()) {
                case '1':
                    $form.find('.series-estimate').show();
                    $form.find('.series-invoice').hide();
                    break;

                default:
                    $form.find('.series-estimate').hide();
                    $form.find('.series-invoice').show();
                    break;
            }
        }).trigger('change');
    }

    // if in order list
    if (typeof smartbill_documents != 'undefined') {
        var show_column = false;
        var smart_docs = [];
        smartbill_documents.forEach(function (i) {
            if (i['smartbill_document_url']) {
                show_column = true;
                smart_docs[i['order_id']] = i['smartbill_document_url']
            }
        });
        if (show_column) {
            $('#form-order table thead > tr:first-child').append('<td class="text-right">SmartBill</td>');
            $('#form-order table tbody > tr').each(function (ind) {
                var order_id = $(this).find('td:nth-child(2)').text();
                var row = '<td class="text-right"></td>';
                if (typeof smart_docs[order_id] != 'undefined') {
                    row = '<td class="text-right"><a target="_blank" href="' + smart_docs[order_id] + '" class="btn btn-primary" title="Vezi document SmartBill"><i class="fa fa-file-text"></i></a></td>';
                }
                $(this).append(row);
            });
        }
    }
});
