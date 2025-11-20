let Inpost = {
    baseUri: 'index.php?route=extension/shipping/inpost_shipping',
    token: '',
    shippingMethods: '',
    deliveryServices: '',
    init: function(e) {
        this.token = $('#input-user-token').val();
        this.shippingMethods = JSON.parse($('#input-shipping-methods').val());
        this.deliveryServices = JSON.parse($('#input-delivery-services').val());
        console.log('Inpost script initialized.');
    },
    presetOrganizationDetails: function() {
        $.get(this.baseUri+'/loadOrganizationDetails&user_token='+this.token, {}, function(response) {
            for (let key in response) {
                let value = response[key];
                if (value !== null) {
                    $('[name="shipping_inpost_shipping_settings['+key+']').val(value);
                }
            }
        });
    },
    setShippingOptions: function () {
        let service = $('#inpostDeliveryServiceModal #inpostDeliveryService').val();
        $('select#inpostShippingMethod option').each(function () { $(this).remove() });

        let offeredSelection = $('#inpostShippingMethod').data('offer');

        for (let methodKey in this.deliveryServices[service]['shippingMethods']) {
            let isDisabled = this.deliveryServices[service]['shippingMethods'][methodKey] ? '' : 'disabled';
            let preSelected = '';
            if (methodKey === offeredSelection) {
                preSelected = 'selected="selected"';
            }
            if (isDisabled) {
                preSelected = '';
            }
            $('select#inpostShippingMethod').append(
                '<option value="'+methodKey+'" '+isDisabled+' '+preSelected+'>'+this.shippingMethods[methodKey].description+'</option>'
            );
        }
    },
    setWeekendDelivery: function () {
        let service = $('#inpostDeliveryServiceModal #inpostDeliveryService').val();
        for (let index in this.deliveryServices[service]['additional_services']) {
            let additionalServiceData = this.deliveryServices[service]['additional_services'][index];
            let weekendDelivery = false;
            if (additionalServiceData.id === 'end_of_week_collection') {
                weekendDelivery = true;
            }

            let weekendDeliverySelector = $('#inpostWeekendDelivery');
            if (weekendDelivery) {
                weekendDeliverySelector.parent().parent().css('display', 'block');
            } else {
                weekendDeliverySelector.parent().parent().css('display', 'none');
            }
        }
    },
    setDefaultSizeTemplate: function() {
        let service = $('#inpostDeliveryServiceModal #inpostDeliveryService').val();
        let availableServices = [
            'inpost_locker_standard',
            'inpost_locker_allegro',
            'inpost_locker_pass_thru',
            'inpost_courier_c2c',
        ];

        if (availableServices.includes(service)) {
            $('.sizeTemplateWrapper').css('display', 'block');
            $('.dimensionsWrapper').css('display', 'none');

            if (service !== 'inpost_courier_c2c') {
                $('#inpostDefaultSizeTemplate option[value="size_d"]').css('display', 'none');
            } else {
                $('#inpostDefaultSizeTemplate option[value="size_d"]').css('display', 'block');
            }
        } else {
            $('.sizeTemplateWrapper').css('display', 'none');
            $('.dimensionsWrapper').css('display', 'block');
        }
    },
    handlePickupPointFormSubmit: function() {
        let that = this;
        let formData = $('#form-pickup-point').serializeObject();
        if (formData.pickupId === "0") {
            that.createPickupPoint(formData);
        } else {
            that.editPickupPoint(formData);
        }
    },
    createPickupPoint: function (formData) {
        let that = this;
        $.post(this.baseUri+'/savePickupPoint&user_token='+this.token, formData, function(response) {
            $('.query-loader').removeClass('query-loader');
            if (response.error) {
                // alert(response.error_message);
                that.showErrorMessage(response.error_message);
            }
            if (response.success) {
                // alert(response.message);
                that.showSuccessMessage(response.message);
                if ($('#pickup-points-wrapper table').length === 0) {
                    $.get(that.baseUri+'/retrievePickupPointsView&user_token='+that.token, {}, function (response) {
                        $('#tab-pickup-points h3').addClass('hidden');
                        $('#tab-pickup-points').prepend(response.pickupPointsHtml);
                    });
                } else {
                    $('#pickup-points-wrapper table').append(response.pickupPointHtml);
                }

                that.closePickupPointsModal();
                that.renderPickupPointsOptions();
            }
        });
    },
    editPickupPoint: function (formData) {
        let that = this;
        $.post(this.baseUri+'/savePickupPoint&user_token='+this.token, formData, function(response) {
            $('.query-loader').removeClass('query-loader');
            if (response.error) {
                that.showErrorMessage(response.error_message);
            }
            if (response.success) {
                that.showSuccessMessage(response.message);
                $('#pickup-points-wrapper table tr[data-id="'+formData.pickupId+'"]').html($(response.pickupPointHtml).html());
                that.closePickupPointsModal();
            }
        });
    },
    getEmptyPickupPointsModal: function() {
        let that = this;
        $.get(this.baseUri+'/getPickupPointsModal&user_token='+this.token, {}, function (response) {
            $('body').append(response.pickupPointsModal);
            $('#inpostPickupPointModal').modal('show');
            that.renderSwitchers();
        });
    },
    getPickupPointsModalById: function(id) {
        let that = this;
        $.get(this.baseUri+'/getPickupPointsModal&user_token='+this.token, { "id_pickup_point": id }, function (response) {
            $('body').append(response.pickupPointsModal);
            $('#inpostPickupPointModal').modal('show');
            that.renderSwitchers();
        });
    },
    closePickupPointsModal: function () {
        $('#inpostPickupPointModal').modal('hide');
        setTimeout(function() { $('#inpostPickupPointModal').remove() }, 200);
    },
    deletePickupPoint: function (id) {
        let that = this;
        $.post(this.baseUri+'/deletePickupPoint&user_token='+this.token, { "id_pickup_point": id }, function(response) {
            if (response.error) {
                that.showErrorMessage(response.error_message);
            }

            if (response.success) {
                that.showSuccessMessage(response.message);
                $('#pickup-points-wrapper tr[data-id="'+id+'"]').remove();

                if ($('#pickup-points-wrapper tbody tr').length === 0) {
                    $('#pickup-points-wrapper').remove();
                    $('#tab-pickup-points h3').removeClass('hidden');
                }
                that.renderPickupPointsOptions();
            }
        });
    },
    renderPickupPointsOptions: function() {
        $.get(this.baseUri+'/getPickupPointsOptions&user_token='+this.token, {}, function(response) {
            $('#tab-shipment-method #input-default-pickup-point option').each(function() {
                $(this).remove();
            });

            $('#tab-shipment-method #input-default-pickup-point').append(response.pickupPointsOptions);
        });
    },
    syncPickupPoints: function() {
        let that = this;
        $.get(this.baseUri+'/syncPickupPoints&user_token='+this.token, {}, function(response) {
            if (response.error) {
                that.showErrorMessage(response.message);
            }

            if (response.success) {
                that.showSuccessMessage(response.message);
                location.reload();
            }
        });
    },
    handleShippingMethodFormSubmit: function() {
        let that = this;
        this.removeValidationAlerts();
        let formData = $('#form-shipping-method').serializeObject();
        if (formData.shippingMethodId === "0") {
            that.createShippingMethod(formData);
        } else {
            that.editShippingMethod(formData);
        }
    },
    removeValidationAlerts: function() {
        $('#inpostShippingMethodModal .has-form-error').each(function() {
            $(this).removeClass('has-form-error');
        });
        $('#inpostShippingMethodModal .alert-danger').each(function() {
            $(this).removeClass('alert-danger');
        });
    },
    createShippingMethod: function (formData) {
        let that = this;
        $.post(this.baseUri+'/saveShippingMethod&user_token='+this.token, formData, function(response) {
            $('.query-loader').removeClass('query-loader');
            if (response.error) {
                that.showErrorMessage(response.error_message);
                $('#inpostShippingMethodModal .form-group.required').each(function() {
                    $formGroup = $(this);
                    if ($formGroup.find('input').val() === '') {
                        $formGroup.addClass('alert-danger');
                        let id = $formGroup.parent().parent().attr('id');
                        $('#inpostShippingConfiguration a[role="button"][href="#'+id+'"]').addClass('has-form-error');
                    }
                });
            }
            if (response.success) {
                that.showSuccessMessage(response.message);
                if ($('#shipping-methods-wrapper table').length === 0) {
                    $.get(that.baseUri+'/retrieveShippingMethodsView&user_token='+that.token, {}, function (response) {
                        $('#tab-settings h3').addClass('hidden');
                        $('#tab-settings').prepend(response.shippingMethodsHtml);
                    });
                } else {
                    $('#shipping-methods-wrapper table').append(response.shippingMethodHtml);
                }

                that.closeShippingMethodModal();
            }
        });
    },
    editShippingMethod: function (formData) {
        let that = this;
        $.post(this.baseUri+'/saveShippingMethod&user_token='+this.token, formData, function(response) {
            $('.query-loader').removeClass('query-loader');
            if (response.error) {
                that.showErrorMessage(response.error_message);
            }
            if (response.success) {
                that.showSuccessMessage(response.message);
                $('#shipping-methods-wrapper table tr[data-id="'+formData.shippingMethodId+'"]').html($(response.shippingMethodHtml).html());
                that.closeShippingMethodModal();
            }
        });
    },
    getEmptyShippingMethodModal: function() {
        let that = this;
        $.get(this.baseUri+'/getShippingMethodModal&user_token='+this.token, {}, function (response) {
            $('body').append(response.shippingMethodModal);
            $('#inpostShippingMethodModal').modal('show');
            that.renderSwitchers();
            $('#inpostShippingGeneral #input-status').val('1').change();
        });
    },
    getShippingMethodModalById: function(id) {
        let that = this;
        $.get(this.baseUri+'/getShippingMethodModal&user_token='+this.token, { "id_shipping_method": id }, function (response) {
            $('body').append(response.shippingMethodModal);
            $('#inpostShippingMethodModal').modal('show');
            that.renderSwitchers();
        });
    },
    closeShippingMethodModal: function () {
        $('#inpostShippingMethodModal').modal('hide');
        setTimeout(function() { $('#inpostShippingMethodModal').remove() }, 200);
    },
    deleteShippingMethod: function (id) {
        let that = this;
        $.post(this.baseUri+'/deleteShippingMethod&user_token='+this.token, { "id_shipping_method": id }, function(response) {
            if (response.error) {
                that.showErrorMessage(response.error_message);
            }

            if (response.success) {
                that.showSuccessMessage(response.message);
                $('#shipping-methods-wrapper tr[data-id="'+id+'"]').remove();

                if ($('#shipping-methods-wrapper tbody tr').length === 0) {
                    $('#shipping-methods-wrapper').remove();
                    $('#tab-settings h3').removeClass('hidden');
                }
            }
        });
    },
    handleDeliveryServiceFormSubmit: function() {
        let that = this;
        let formData = $('#form-delivery-service').serializeObject();
        if (formData.deliveryServiceId === "0") {
            that.createDeliveryService(formData);
        } else {
            that.editDeliveryService(formData);
        }
    },
    createDeliveryService: function (formData) {
        let that = this;
        $.post(this.baseUri+'/saveDeliveryService&user_token='+this.token, formData, function(response) {
            $('.query-loader').removeClass('query-loader');
            if (response.error) {
                that.showErrorMessage(response.error_message);
            }
            if (response.success) {
                that.showSuccessMessage(response.message);
                if ($('#delivery-services-wrapper table').length === 0) {
                    $.get(that.baseUri+'/retrieveDeliveryServicesView&user_token='+that.token, {}, function (response) {
                        $('#tab-delivery-services h3').addClass('hidden');
                        $('#tab-delivery-services').prepend(response.deliveryServicesHtml);
                    });
                } else {
                    $('#delivery-services-wrapper table').append(response.deliveryServiceHtml);
                }

                that.closeDeliveryServiceModal();
                setTimeout(function() {
                    $('#tab-settings').click();
                    $('#button-new-shipping-method').trigger('click');
                    let nameInterval =  setInterval(function () {
                        if ($('#inpostShippingMethodModal input[name="name"]').length != 0) {
                            $('#inpostShippingMethodModal input[name="name"]').val(formData.carrierName);
                            let idDeliveryService = $('#delivery-services-wrapper table tbody tr:last-child').data('id');
                            $('#inpostShippingMethodModal select[name="fk_delivery_service"] option[value="'+idDeliveryService+'"]').prop('selected', true);
                            clearInterval();
                        }
                    }, 25);
                }, 400);
            }
        });
    },
    editDeliveryService: function (formData) {
        let that = this;
        $.post(this.baseUri+'/saveDeliveryService&user_token='+this.token, formData, function(response) {
            $('.query-loader').removeClass('query-loader');
            if (response.error) {
                that.showErrorMessage(response.error_message);
            }
            if (response.success) {
                that.showSuccessMessage(response.message);
                $('#delivery-services-wrapper table tr[data-id="'+formData.deliveryServiceId+'"]').html($(response.deliveryServiceHtml).html());
                that.closeDeliveryServiceModal();
            }
        });
    },
    getEmptyDeliveryServiceModal: function() {
        let that = this;
        $.get(this.baseUri+'/getDeliveryServiceModal&user_token='+this.token, {}, function (response) {
            $('body').append(response.deliveryServiceModal);
            $('#inpostDeliveryServiceModal').modal('show');
            that.renderSwitchers();
        });
    },
    getDeliveryServiceModalById: function(id) {
        let that = this;
        $.get(this.baseUri+'/getDeliveryServiceModal&user_token='+this.token, { "id_delivery_service": id }, function (response) {
            $('body').append(response.deliveryServiceModal);
            $('#inpostDeliveryServiceModal').modal('show');
            that.setShippingOptions();
            that.setWeekendDelivery();
            that.setDefaultSizeTemplate();
            that.renderSwitchers();
        });
    },
    closeDeliveryServiceModal: function () {
        $('#inpostDeliveryServiceModal').modal('hide');
        setTimeout(function() { $('#inpostDeliveryServiceModal').remove() }, 200);
    },
    deleteDeliveryService: function (id) {
        let that = this;
        $.post(this.baseUri+'/deleteDeliveryService&user_token='+this.token, { "id_delivery_service": id }, function(response) {
            if (response.error) {
                that.showErrorMessage(response.error_message);
            }

            if (response.success) {
                that.showSuccessMessage(response.message);
                $('#delivery-services-wrapper tr[data-id="'+id+'"]').remove();

                if ($('#delivery-services-wrapper tbody tr').length === 0) {
                    $('#delivery-services-wrapper').remove();
                    $('#tab-delivery-services h3').removeClass('hidden');
                }
            }
        });
    },
    fillCarrierName: function () {
        let opt = $('#inpostDeliveryService').val();
        if (opt.length === 0) {
            alert('Please choose a delivery service.');
            return;
        }
        let name = $('#inpostDeliveryService option[value="'+opt+'"]').text();
        $('#inpostCarrierName').val(name);
    },
    renderSwitchers: function() {
        $('.js-switch').each(function() {
            if (typeof $(this).data('switchery') === 'undefined') {
                new Switchery(this, { size: 'small' });
            }
        });
        // var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        // elems.forEach(function (html) {
        //     console.log(html.attributes);
        //     // if (html.dataset['switchery'] === 'true') {
        //     //     return;
        //     // }
        //     var switchery = new Switchery(html, { size: 'small' });
        // });
    },
    appendSweetAlert: function() {
        $('head').append('<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>');
    },
    showErrorMessage: function(message) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: message
        });
    },
    showSuccessMessage: function(message) {
        Swal.fire({
            icon: 'success',
            title: 'Done!',
            text: message
        });
    },
    setPoint: function(point) {
        let target = $('#inpostGeoWidgetModal').attr('target');
        $('#input-default-'+target).val(point.name);
        $('#inpostGeoWidgetModal').modal('hide');
    },
    handleViewOrderStatuses: function (type) {
        if ($('#input-shipping-order-status-update-'+type).is(':checked')) {
            $('.order-status-'+type).css('display', 'block');
        } else {
            $('.order-status-'+type).css('display', 'none');
        }
    },
    checkActiveTab: function() {
        let currentUrl = new URL(location.href);
        let params = new URLSearchParams(currentUrl.search);

        if (params.has('tab')) {
            let activeTabSelector = 'a[href="#tab-' + params.get('tab') + '"]';
            $(activeTabSelector).click();
            let newUrl = location.href.replace('&tab='+params.get('tab'), '');
            history.pushState('', '', newUrl);
        }
    }
}

$(document).ready(function () {
    Inpost.init();
    Inpost.renderSwitchers();
    Inpost.appendSweetAlert();
    Inpost.checkActiveTab();
});

$(document).on('click', '#load-organization-data', function(e) {
    e.preventDefault();
    Inpost.presetOrganizationDetails();

    return false;
});

$(document).on('change', '#inpostDeliveryService', function (e) {
    Inpost.setShippingOptions();
    Inpost.setWeekendDelivery();
    Inpost.setDefaultSizeTemplate();
});

$(document).on('click', '#button-submit-pickup-points-form', function(e) {
    $(this).addClass('query-loader');
    Inpost.handlePickupPointFormSubmit();
});

$(document).on('click', '.edit-pickup-point', function(e){
   let id = $(this).parent().parent().data('id');
   Inpost.getPickupPointsModalById(id);
});

$(document).on('click', '#button-new-pickup-point', function(e){
   Inpost.getEmptyPickupPointsModal();
});

$(document).on('click', '#button-close-pickup-points-modal', function(e) {
    e.preventDefault();
    Inpost.closePickupPointsModal();
});

$(document).on('click', '.delete-pickup-point', function (e) {
    if (confirm('Delete pickup point?')) {
        let id = $(this).parent().parent().data('id');
        Inpost.deletePickupPoint(id);
    }
});

$(document).on('click', '#inpostPickupPointsSync', function() {
    Inpost.syncPickupPoints();
});

$(document).on('click', '#button-submit-shipping-method-form', function(e) {
    $(this).addClass('query-loader');
    Inpost.handleShippingMethodFormSubmit();
})

$(document).on('click', '.edit-shipping-method', function(e){
    let id = $(this).parent().parent().data('id');
    Inpost.getShippingMethodModalById(id);
});

$(document).on('click', '#button-new-shipping-method', function(e){
    Inpost.getEmptyShippingMethodModal();
});

$(document).on('click', '#button-close-shipping-method-modal', function(e) {
    e.preventDefault();
    Inpost.closeShippingMethodModal();
});

$(document).on('click', '.delete-shipping-method', function (e) {
    if (confirm('Delete shipping method?')) {
        let id = $(this).parent().parent().data('id');
        Inpost.deleteShippingMethod(id);
    }
});

$(document).on('click', '#button-submit-delivery-service-form', function(e) {
    $(this).addClass('query-loader');
    Inpost.handleDeliveryServiceFormSubmit();
})

$(document).on('click', '.edit-delivery-service', function(e){
    let id = $(this).parent().parent().data('id');
    Inpost.getDeliveryServiceModalById(id);
});

$(document).on('click', '#button-new-delivery-service', function(e){
    Inpost.getEmptyDeliveryServiceModal();
});

$(document).on('click', '#button-close-delivery-service-modal', function(e) {
    e.preventDefault();
    Inpost.closeDeliveryServiceModal();
});

$(document).on('click', '.delete-delivery-service', function (e) {
    if (confirm('Delete delivery service?')) {
        let id = $(this).parent().parent().data('id');
        Inpost.deleteDeliveryService(id);
    }
});

$(document).on('click', '#inpostFillCarrierName', function (e) {
    Inpost.fillCarrierName();
});

$(document).on('hidden.bs.modal', function (e) {
    let id = $(e.target).attr('id');

    if (id === 'inpostGeoWidgetModal') {
        return;
    }

    $('#'+id).remove();

    if (id === 'modal-image' && $('#inpostShippingMethodModal').length > 0) {
        $('body').addClass('modal-open');
    }
});

$(document).on('click', '#input-default-parcel-locker', function(e) {
    $('#inpostGeoWidgetModal').modal('show');
    $('#inpostGeoWidgetModal').on('shown.bs.modal', function(e){
        $('#inpostGeoWidgetModal').attr('target', 'parcel-locker');
    });
});

$(document).on('click', '#input-default-pop', function(e) {
    $('#inpostGeoWidgetModal').modal('show');
    $('#inpostGeoWidgetModal').on('shown.bs.modal', function(e) {
        $('#inpostGeoWidgetModal').attr('target', 'pop');
    });
});

function afterPointSelected(point) {
    Inpost.setPoint(point);
}

$(document).on('change', '#input-shipping-order-status-update-onlabel', function(e) {
    Inpost.handleViewOrderStatuses('onlabel');
});

$(document).on('change', '#input-shipping-order-status-update-ondelivery', function(e) {
    Inpost.handleViewOrderStatuses('ondelivery');
});

$(document).on('input', '#input-quick-returns', function() {
    let baseUri = 'https://szybkiezwroty.pl/{name}#navigate-buttons';
    let href = baseUri.replace('{name}', $(this).val());

    $('#link-quick-returns').attr('href', href);
});

// Jquery Helper: serializeObject()
// $.fn.serializeObject=function(){var e=this,i={},t={},a={validate:/^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,key:/[a-zA-Z0-9_]+|(?=\[\])/g,push:/^$/,fixed:/^\d+$/,named:/^[a-zA-Z0-9_]+$/};return this.build=function(e,i,t){return e[i]=t,e},this.push_counter=function(e){return void 0===t[e]&&(t[e]=0),t[e]++},$.each($(this).serializeArray(),function(){if(a.validate.test(this.name)){for(var t,n=this.name.match(a.key),u=this.value,h=this.name;void 0!==(t=n.pop());)h=h.replace(RegExp("\\["+t+"\\]$"),""),t.match(a.push)?u=e.build([],e.push_counter(h),u):t.match(a.fixed)?u=e.build([],t,u):t.match(a.named)&&(u=e.build({},t,u));i=$.extend(!0,i,u)}}),i};
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