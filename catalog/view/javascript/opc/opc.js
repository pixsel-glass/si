(function($) {
	var OnePageCheckout = function(options) {

		var self = this;
		self.$elem = $('#onepcheckout');
		self.options = options;
		self.$sfields = $('.checkout_form input[type=\'text\'], .checkout-address input[type=\'radio\'], .checkout-address select', self.$elem);
		self.clickSelectors = '.cart-list .btn, [id^="button-"],input[name="register"]';

		self.init = function() {
			self.response();
			self.initSelect2();
			self.authorization();
			// self.attachEventHandlers();
			self.saveFields();
			self.initRelatedSlider();

			if($('input[name=\'payment_method\']:checked').val() == 'portmone'){
				$('#button-register').val($('#button-register').data('text-btn-portmone'));
			} else {
				$('#button-register').val($('#button-register').data('text-btn-default'));
			}

			$(document).ajaxComplete(function( event, xhr, settings ) {
				if ( settings.url === "index.php?route=checkout/cart/remove" || settings.url === "index.php?route=checkout/cart/add" ) {
					self.opcReloadAll();
				}
			});

			var selectedOption = $('#input-payment-country-delivery option:selected');
			if (selectedOption.length && selectedOption.val() !== '') {
				var countryId = selectedOption.data('country-id');
				$('#input-hidden-country-id').val(countryId);
			} else {
				$('#input-hidden-country-id').val(0);
			}

			$('#input-payment-country-delivery').on('change', function () {
				var countryId = $(this).find('option:selected').data('country-id');
				$('#input-hidden-country-id').val(countryId);
			});

			setTimeout(function() {
				//self.shippingUpdate();

				//self.updateShippingCart();
				self.initMaskPhone();
				self.initSelect2();
				self.attachEventHandlers();

				if ($('select[name=\'country_delivery_id\']').length > 0) {
					$('select[name=\'country_delivery_id\']').trigger('change');
				}
			}, 500);
		};

		self.attachEventHandlers = function(){

			if ($('select[name=\'country_delivery_id\']').length > 0) {
				$('select[name=\'country_delivery_id\']').trigger('change');
			}

			$('input[name=\'client_name_tel\']').autocomplete({
				'source': function(request, response) {
					$.ajax({
						url: 'index.php?route=checkout/onepcheckout/autocompleteCustomer&client_name_tel=' +  encodeURIComponent(request),
						dataType: 'json',
						success: function(json) {
							response($.map(json, function(item) {
								return {
									// label: item['name'] + ' (' + item['company_name'] + ') ' +  item['telephone'],
									// label: item['name'] + ' ' +  item['telephone'],
									label: (item['company_name'] ? '' + item['company_name'] + ' |' : '') + ' ' + item['name'] + ' ' + item['telephone'],
									value: item['customer_id'],
									customer_group: item['customer_group']
								}
							}));
						}
					});
				},
				'select': function(item) {
					$('input[name=\'client_name_tel\']').val(item['label']);
					$('input[name=\'client_id\']').val(item['value']);
					$('.top-find-client').prepend('<button onclick="editClientInfo('+ item['value'] +')" class="btn btn-default pl-0 pr-0 mr-2 box-shadow-off order-0 edit-client-info d-none"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="butt" stroke-linejoin="bevel"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg></button>');
					$('.top-find-client').append('<div class="control-label opc-label opc-customer-group ml-auto d-none">'+ item['customer_group'] +'</div>');
				}
			});

			self.$elem.on('click', self.clickSelectors, function (e) {

				// e.preventDefault();
				e.stopImmediatePropagation();
				var $target = $(this);

				if ($target.hasClass('btn') && $target.closest('.cart-list').length > 0) {
					var action = $target.data('action');
					if (action === 'minus' || action === 'plus') {
						self.plusMinusQty($target, action);
					} else if (action === 'remove') {
						self.removeProduct($target.data('key'));
					}
				} else if ($target.attr('id') && $target.attr('id').startsWith('button-')) {
					var buttonId = $target.attr('id');
					if (buttonId === 'button-coupon') {
						self.handleCouponButtonClick();
					} else if (buttonId === 'button-reward') {
						self.handleRewardButtonClick();
					} else if (buttonId === 'button-voucher') {
						self.handleVoucherButtonClick();
					} else if (buttonId === 'button-add-client') {
						$('#add-retail-customer').addClass('d-none');
						self.opcNewDataClient();
						if($('input[name=\'client_id\']').val() > 0){
							$('#button-remove-client').removeClass('d-none');
							$('input[name=\'client_name_tel\']').attr('disabled','disabled');
							$('.edit-client-info').removeClass('d-none');
							$('.opc-customer-group').removeClass('d-none');
						} else {
							$('#button-remove-client').addClass('d-none');
							$('input[name=\'client_id\']').val(0);
							$('.edit-client-info').remove();
							$('.opc-customer-group').remove();
						}
					} else if (buttonId === 'button-remove-client') {
						$('input[name=\'client_name_tel\']').removeAttr('disabled').val('');
						$('input[name=\'client_id\']').val(0);
						$('.edit-client-info').remove();
						$('.opc-customer-group').remove();
						$('#add-retail-customer').removeClass('d-none');
						self.opcNewDataClient();
						$target.addClass('d-none');
					} else if (buttonId === 'button-register') {
						if ($('input[name="PLN"]').length > 0 && $('input[name="payment_method"]:checked').val() != 'bank_transfer' && $('input[name="payment_method"]:checked').val() != 'cheque') {
							$('#ft_pay_modal').modal('show');
						} else {
							self.opcValidateForm();
						}
					}
				} else if ($target.attr('name') === 'register') {
					self.customerUpdate();
				}
			});

			function hasClass(elem, className) {
			  return elem.classList.contains(className);
			}
			document.addEventListener('click', function(e) {
			  if (hasClass(e.target, 'gotopay')) {
			  	self.opcValidateForm();
			  }
			}, false);

			function checkFirma(){
				var selectedValue = $('input[name="customer_type"]:checked').val();
				if (selectedValue == 1) {
					$('.firma_fields').removeClass('d-none');
				} else {
					$('.firma_fields').addClass('d-none');
				}
			}

			$(document).on('click', '#add-retail-customer', function () {
				$('input[name=\'client_name_tel\']').val($(this).attr('data-customer-name'));
				$('input[name=\'client_id\']').val($(this).attr('data-customer-id'));
				$('#button-add-client').trigger('click');
			});

			$(document).on('change', 'input[name="customer_type"]', function () {
				checkFirma();
			});

			checkFirma();

			$(document).on('change', self.sfields, function () {
				self.saveFields();
			});

			$(document).on('change', 'select[name=\'country_delivery_id\'],select[name=\'country_id\'], select[name=\'zone_id\'], input[name=\'shipping_method\'], input[name=\'city\'], input[name=\'address_1\'], input[name=\'payment_method\']', function(e) {
				e.preventDefault();
				if (this.name == 'country_delivery_id') {

					$("select[name=\'zone_id\']").val("");
					self.initMaskPhone();
					self.getZones(this.value);

					self.updateCart();
					// self.updateTotals();

					self.shippingUpdate();
					self.paymentUpdate();

					setTimeout(function() {
						self.updateShippingCart();

						self.paymentUpdate();

						setTimeout(function() {
							self.shippingAddressUpdate();
							self.updateTotals();
							self.opcReloadAll();
						}, 600);
						//self.shippingAddressUpdate();
					}, 300);

				} else if(this.name == 'payment_method'){
					$('.payment').empty().addClass('hidden');
					$('#button-register').show();
					$('#button-confirm').remove();
					$('#opc-confirm').remove();
					if(this.value == 'portmone'){
    					$('#button-register').val($('#button-register').data('text-btn-portmone'));
					} else {
						$('#button-register').val($('#button-register').data('text-btn-default'));
					}
					self.paymentUpdate();
				} else if(this.name == 'city' || this.name == 'address_1') {
					setTimeout(function() {
						self.shippingUpdate();
						self.updateTotals();
					}, 700);
				} else if(this.name == 'shipping_method'){

					self.shippingUpdate();

					self.paymentUpdate();

					self.updateTotals();

					// console.log(this.value.indexOf('pickup'));

					// setTimeout(function() {
						if(this.name == 'shipping_method' && this.value.indexOf('inpost_shipping_2') >= 0) {
							$('.parcel-locker, .parcel-locker-selection-data').each(function(){
	           					 $(this).addClass('hidden');
	        				});

							setTimeout(function() {
								$('.opc_block_shipping_address').addClass('hidden');

	        					$('#inpostGeoWidgetModal').modal('show');
	        				}, 500);
	        			} else if(this.name == 'shipping_method' && this.value.indexOf('pickup') >= 0) {
	        				$('.opc_block_shipping_address').addClass('hidden');
						} else {
							$('.opc_block_shipping_address').removeClass('hidden');
						}
						// }, 300);
				} else {
					self.opcReloadAll();
				}
			});

			$(document).on('click', 'input[name=\'shipping_method\']', function(e) {
				if(this.name == 'shipping_method' && this.value.indexOf('inpost_shipping_2') >= 0 && this.checked) {

					$.getScript("catalog/view/javascript/inpost/inpost-geowidget.js", function(data, textStatus, jqxhr) {
					  console.log(data); //data returned
					  console.log(textStatus); //success
					  console.log(jqxhr.status); //200
					  console.log('InPost loaded');
					});

					$('.parcel-locker, .parcel-locker-selection-data').each(function(){
      					$(this).addClass('hidden');
        			});

					setTimeout(function() {
       					$('#inpostGeoWidgetModal').modal('show');
       				}, 500);
				}
			});

			var inputTimeout;
			$(document).on('input', '.cart-item-price-quantity .form-control', function () {
				var input = this;
				clearTimeout(inputTimeout);
				inputTimeout = setTimeout(function() {
					self.opcValidateQty(input);
				}, 600);
			});


			$(document).on('click', '.inpost_address', function(e) {
					$.getScript("catalog/view/javascript/inpost/inpost-geowidget.js", function(data, textStatus, jqxhr) {
					  console.log(data); //data returned
					  console.log(textStatus); //success
					  console.log(jqxhr.status); //200
					  console.log('InPost loaded');
					});

					$('.parcel-locker, .parcel-locker-selection-data').each(function(){
      					$(this).addClass('hidden');
        			});

					setTimeout(function() {
       					$('#inpostGeoWidgetModal').modal('show');
       				}, 500);
			});

			self.initMaskPhone();
			self.initDateTimePicker();
		};

		self.opcValidateForm = function(){

			// validate phone
			//if (!window.intlTelInput.instances[0].isValidNumber()) {
			//	$('[name="telephone"]').closest('.form-group').find('.control-label').addClass('error_input_checkout');
			//	$('[name="telephone"]').css('border', '1px solid red');

			//	var errorElement = $('.control-label.error_input_checkout').first();
			//	if (errorElement.length > 0) {
			//		$('html, body').animate({
			//			scrollTop: errorElement.offset().top - 120
			//		}, 'slow');
			//	}

			//	return;
			//} else {
			//	$('[name="telephone"]').closest('.form-group').find('.control-label').removeClass('error_input_checkout');
			//	$('[name="telephone"]').css('border', '1px solid #EAEDF7');
			//}

			if ($('.checkout_form input[name=\'company_nip_inf\']').length > 0) {
				var infakt_nip = $('.checkout_form input[name=\'company_nip_inf\']').val();
			} else {
				var infakt_nip = '';
			}

			if ($('.checkout_form input[name=\'company_vatcode\']').length > 0) {
				var infakt_vatcode = $('.checkout_form input[name=\'company_vatcode\']').val();
			} else {
				var infakt_vatcode = '';
			}

			var data = $('.checkout_form input[type=\'text\'], .checkout_form input[type=\'date\'], .checkout_form input[type=\'datetime-local\'], .checkout_form input[type=\'time\'], .checkout_form input[type=\'password\'], .checkout_form input[type=\'hidden\'], .checkout_form input[type=\'checkbox\']:checked,.checkout-totals input[type=\'checkbox\']:checked, .checkout_form input[type=\'radio\']:checked, .checkout_form textarea, .checkout_form select').serialize();
			data += '&_shipping_method='+ $('.checkout_form input[name=\'shipping_method\']:checked').prop('title') + '&_payment_method=' + $('.checkout_form input[name=\'payment_method\']:checked').prop('title') + '&infakt_faktyre=' + $('.checkout_form input[name=\'infakt_faktyre\']:checked').length + '&infakt_private_faktyre=' + $('.checkout_form input[name=\'infakt_private_faktyre\']:checked').length + '&infakt_nip=' + infakt_nip + '&infakt_vatcode=' + infakt_vatcode; // + '&infakt_name=' + $('.checkout_form input[name=\'company_name\']').val();

			$.ajax({
				url: 'index.php?route=checkout/onepcheckout/validate',
				type: 'post',
				data: data,
				dataType: 'json',
				beforeSend: function() {
					$('.ch-alert-danger').remove();
					$('#button-register').button('loading');
					self.loading_mask(true);
				},
				complete: function() {
					$('#button-register').button('reset');
				},
				success: function(json) {
					$('.alert:not(.opc-alert-danger),.opc-text-error').remove();
					$('.form-control').removeClass('error_input_checkout');
					$('.control-label').removeClass('error_input_checkout');

					if (json['error']) {
						self.loading_mask(false);
						for (i in json['error']) {
							console.log(i);
							if (i.includes('custom_field') || i == 'payment_country_delivery') {
								$('#input-' + i.replaceAll('_', '-')).after('<div class="opc-text-error">'+ json['error'][i] +'</div>');
								$('#input-' + i.replaceAll('_', '-')).closest('.form-group').find('.control-label').addClass('error_input_checkout');
							} else {
								$('[name="' + i + '"]').closest('.form-group').find('.control-label').after('<div class="opc-text-error">'+ json['error'][i] +'</div>');
								$('[name="' + i + '"]').closest('.form-group').find('.control-label').addClass('error_input_checkout');
							}
						}

						var arr = [];

						for (i in json['error']) {
							arr.push(json['error'][i]);
						}

						var errorElement = $('.control-label.error_input_checkout').first();

						if (errorElement.length > 0) {
							$('html, body').animate({
								scrollTop: errorElement.offset().top - 120
							}, 'slow');
						}

						var time_a = 5000;
						var index = -1;
						var timer = setInterval(function () {
						if (++index == arr.length) {
							clearInterval(timer);
						} else {
							(function (currentIndex) {
								var block_alert = $('<div class="alert ch-alert-danger alert-' + currentIndex + '"><img class="warning-icon" alt="warning-icon" src="catalog/view/javascript/opc/image/warning-icon.svg"><div class="text-modal-block">' + arr[currentIndex] + '</div><button type="button" class="close" data-dismiss="alert"></button></div>');
								$('body').append(block_alert);
								setTimeout(() => {
								$(`.ch-alert-danger.alert-${currentIndex}`).remove();
								}, time_a);
							})(index);
						}
						time_a = time_a + 1000;
						}, 10);
					}

					if (json['success']) {

						// $('#button-register').hide();
						$('.payment').empty();
						$('.payment').html(json['success']['payment']);

						if ($('.payment h2, .payment p, .payment form, .payment .proposition').length) {

							if($('input[name=\'payment_method\']:checked').val() == 'portmone'){

								$('.confirm-block').prepend($('.payment').find('.btn-primary').clone().attr('id', 'opc-confirm-portmone').attr('class', 'opc-btn opc-btn-primary w-100'));
								$('.payment').find('#button-confirm').addClass('hidden');
								$(document).on('click', '#opc-confirm-portmone', function(){
									$('#opc-confirm-portmone').button('loading');
									$('.payment .btn-primary').trigger('click');
								});
								setTimeout(function() {
									$('#opc-confirm-portmone').trigger('click');
								}, 200);
							} else {

								if($('input[name=\'payment_method\']:checked').val() == 'bank_transfer'){
									$('.payment').find('#button-confirm').trigger('click');
								} else {
									if( $('.payment').find('#button-confirm').length ){
										$('.confirm-block').prepend($('.payment').find('#button-confirm').clone().attr('id', 'opc-confirm').attr('class', 'opc-btn opc-btn-primary w-100'));
										$('.payment').find('#button-confirm').addClass('hidden');
									}

									$(document).on('click', '#opc-confirm', function(){
										$('#button-confirm').trigger('click');
										$('#opc-confirm').button('loading');
									});

									setTimeout(function() {
										$('.payment').removeClass('hidden');
										$('html, body').animate({ scrollTop: $('.payment').offset().top - document.querySelector('header').clientHeight - 50}, 250);
									}, 300);
								}
							}
							setTimeout(function() {
								self.loading_mask(false);
							}, 500);
						} else {
							$('.payment').css('display', 'none');
							setTimeout(function() {
								$('.payment #button-confirm, .payment input[type=\'submit\'], .payment button, .payment a, .payment input[type=\'button\'], .payment .btn-primary').click();
							}, 300);

							if($('.payment a').length) {
								$('.payment a')[0].click();
							}
						}
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		};


		self.opcNewDataClient = function(){
			var data = $('.checkout_form input[type=\'text\'], .checkout_form input[type=\'password\'], .checkout_form input[type=\'hidden\'], .checkout_form input[type=\'checkbox\']:checked,.checkout-totals input[type=\'checkbox\']:checked, .checkout_form input[type=\'radio\']:checked, .checkout_form textarea, .checkout_form select').serialize();
			var client_id = $('.checkout_form input[type=\'hidden\'][name=\'client_id\']');


			$.ajax({
				url: 'index.php?route=checkout/onepcheckout/customer',
				type: 'post',
				data: data,
				dataType: 'html',
				cache: false,
				beforeSend: function() {
					self.loading_mask(true);
				},
				success: function(html) {
					$('.opc_block_customer').html(html);
					self.initMaskPhone();
					self.initDateTimePicker();

					$.ajax({
						url: 'index.php?route=checkout/onepcheckout/cart',
						type: 'post',
						data: data,
						dataType: 'html',
						cache: false,
						beforeSend: function() {
							self.loading_mask(true);
						},
						success: function(html) {
							$(".table_total").html($(html).find(".table_total").html());
							$(".cart-list").html($(html).find(".cart-list").html());
							$(".opc-dop-totals").html($(html.cart).find(".opc-dop-totals").html());
							$(".panel-group").html($(html).find(".panel-group").html());
							new Function(self.options.load_script)();

							$.ajax({
								url: 'index.php?route=checkout/onepcheckout/totals',
								type: 'post',
								data: data,
								dataType: 'html',
								cache: false,
								complete: function() {
									self.loading_mask(false);
								},
								success: function(html) {
									if($(".opc-cart-weight").length){
										$(".opc-cart-weight").html($(html).find(".opc-cart-weight").html());
									}
									if($(".opc-agree").length){
										$(".opc-agree").html($(html).find(".opc-agree").html());
									}
									if($(".opc-bezvat").length){
										$(".opc-bezvat").html($(html).find(".opc-bezvat").html());
									}
									$(".table_total").html($(html).find(".table_total").html());

									setTimeout(function() {
										if($("select[name=\'country_id\']").val() != 170) {
											$('.opc-createinfakt').addClass('disabledfields');
											$('.opc-createinfakt input[type="checkbox"]').attr('disabled', 'disabled');
										} else {
											$('.opc-createinfakt').removeClass('disabledfields');
											$('.opc-createinfakt input[type="checkbox"]').removeAttr('disabled');
										}
									}, 200);
								}
							});
						}
					});
				}
			});

			$.ajax({
				url: 'index.php?route=checkout/onepcheckout/shipping_method',
				type: 'post',
				data: data,
				dataType: 'html',
				cache: false,
				beforeSend: function() {
					self.loading_mask(true);
				},
				complete: function() {
					self.loading_mask(false);
				},
				success: function(html) {
					if(html.length){
						$('.opc_block_shipping_method').html(html);
					} else {
						location = 'index.php?route=checkout/cart';
					}
				}
			});

			$.ajax({
				url: 'index.php?route=checkout/onepcheckout/payment_method',
				type: 'post',
				data: data,
				dataType: 'html',
				cache: false,
				success: function(html) {
					$('.opc_block_payment_method').html(html);
				}
			});

			$.ajax({
				url: 'index.php?route=checkout/onepcheckout/shipping_address',
				type: 'post',
				data: data,
				dataType: 'html',
				cache: false,
				success: function(html) {
					if($(html).find('.checkout-address-info .row').length){
						$('.opc_block_shipping_address').html(html);
						$('.opc_block_shipping_address').removeClass('hidden');
					} else {
						$('.opc_block_shipping_address').addClass('hidden');
					}
				}
			});

			setTimeout(function() {
				self.opcReloadAll();
			}, 500);

		};

		self.opcReloadAll = function(){
			var data = $('.checkout_form input[type=\'text\'], .checkout_form input[type=\'password\'], .checkout_form input[type=\'hidden\'], .checkout_form input[type=\'checkbox\']:checked,.checkout-totals input[type=\'checkbox\']:checked, .checkout_form input[type=\'radio\']:checked, .checkout_form textarea, .checkout_form select').serialize();

			$.ajax({
				url: 'index.php?route=checkout/onepcheckout/reloadAll',
				type: 'post',
				data: data,
				dataType: 'json',
				cache: false,
				beforeSend: function() {
					$('.ch-alert-danger').remove();
					self.loading_mask(true);
				},
				complete: function() {
					self.loading_mask(false);
				},
				success: function(json) {
					if(json['redirect']){
						location = json['redirect'];
					} else {

						for (var key in json) {
							switch (key) {
								case 'country_region':
									if($(json.customer).find('.checkout-address-info .row').length > 0){
										$(json.customer).find('.checkout-address-info .row').html(json.country_region);
									}
									if (typeof self.initSelect2 == 'function') {
										self.initSelect2();
									}
									break;
								case 'shipping_method':
									if(json.shipping_method){
										$('.opc_block_shipping_method').html(json.shipping_method);
									} else {
										$('.opc_block_shipping_method .checkout-shipping-method').empty();
										$('.opc_block_shipping_method .checkout-shipping-method').addClass('hidden');
									}
									break;
								case 'shipping_address':
									if($(json.shipping_address).find('.checkout-address-info .row').length > 0){
										$('.opc_block_shipping_address').html(json.shipping_address);
										$('.opc_block_shipping_address').removeClass('hidden');
									} else {
										$('.checkout-address-info').empty();
										$('.opc_block_shipping_address').addClass('hidden');
									}
									if (typeof self.initSelect2 == 'function') {
										self.initSelect2();
										setTimeout(function() {
											self.paymentUpdate();
										}, 300);
									}
									break;
								case 'payment_method':
									if(json.payment_method !== ''){
										$('.opc_block_payment_method').html(json.payment_method);
									} else {
										$('.opc_block_payment_method .checkout-payment-method').empty();
										$('.opc_block_payment_method .checkout-payment-method').addClass('hidden');
									}
									break;
								case 'customer':
									$('.opc_block_customer').html(json.customer);
									self.initMaskPhone();
									break;
								case 'cart':
									$(".cart-list").html($(json.cart).find(".cart-list").html());
									$(".opc-dop-totals").html($(json.cart).find(".opc-dop-totals").html());
									new Function(self.options.load_script)();
									break;
								case 'totals':
									if($(".opc-cart-weight").length){
										$(".opc-cart-weight").html($(json.totals).find(".opc-cart-weight").html());
									}
									if($(".opc-agree").length){
										$(".opc-agree").html($(json.totals).find(".opc-agree").html());
									}

									var free_ship_left_html = $(json.totals).find(".free-shipping-left").html();
									var fsPercentageMatch = false;

									if(free_ship_left_html){
										fsPercentageMatch = $(json.totals).find(".free-shipping-inner").attr('data-fsl-width');
									} else {
										$('.free-shipping-left').remove();
									}

									if (fsPercentageMatch) {
										if($(".free-shipping-left").length){
											var targetWidth = parseFloat(fsPercentageMatch);

											var currentWidth = parseFloat($(".free-ship-bar-fill").css("width"));

											$(".free-ship-bar-fill").css({ width: targetWidth + "%" });

											$('.free-ship-info').html($(free_ship_left_html).find('.free-ship-info').html());

											if(targetWidth == 100){
												$('.free-ship-progress-bar').addClass('hidden');
												$('.free-ship-info').addClass('active-free-ship');
											} else {
												$('.free-ship-progress-bar').removeClass('hidden');
												$('.free-ship-info').removeClass('active-free-ship');
											}

										} else if(free_ship_left_html) {
											$('.checkout-totals').prepend('<div class="free-shipping-left">'+ free_ship_left_html +'</div>')
										}
									}

									$(".table_total").html($(json.totals).find(".table_total").html());
									break;
								case 'related_products':
									$('[id^="tooltip"]').remove();
									if($('.opc_block_related_products').length){
										$('.opc_block_related_products').html(json.related_products);
									}
									self.initRelatedSlider();
									break;
								case 'opc_errors':
									if (Object.keys(json.opc_errors).length > 0) {
										$('.opc-alert-danger').remove();

										$.each(json.opc_errors, function(errorKey, opcError) {
											html = '<div class="alert opc-alert-danger ' + errorKey + '">';
											html += '	<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">';
											html += '		<path fill-rule="evenodd" clip-rule="evenodd" d="M9 17C13.4183 17 17 13.4183 17 9C17 4.58172 13.4183 1 9 1C4.58172 1 1 4.58172 1 9C1 13.4183 4.58172 17 9 17ZM9 18C13.9706 18 18 13.9706 18 9C18 4.02944 13.9706 0 9 0C4.02944 0 0 4.02944 0 9C0 13.9706 4.02944 18 9 18Z" fill="#d8300e"></path>';
											html += '		<path fill-rule="evenodd" clip-rule="evenodd" d="M9 4.50952C9.27614 4.50952 9.5 4.73338 9.5 5.00952V10.2151C9.5 10.4913 9.27614 10.7151 9 10.7151C8.72386 10.7151 8.5 10.4913 8.5 10.2151V5.00952C8.5 4.73338 8.72386 4.50952 9 4.50952Z" fill="#d8300e"></path>';
											html += '		<path fill-rule="evenodd" clip-rule="evenodd" d="M9 12.2985C9.27614 12.2985 9.5 12.5223 9.5 12.7985V13.6879C9.5 13.964 9.27614 14.1879 9 14.1879C8.72386 14.1879 8.5 13.964 8.5 13.6879V12.7985C8.5 12.5223 8.72386 12.2985 9 12.2985Z" fill="#d8300e"></path>';
											html += '	</svg>' + opcError + '<button type="button" class="close" data-dismiss="alert">×</button>';
											html += '</div>';

											$('#onepcheckout').before(html);
										});
									} else {
										if($('.opc-alert-danger').length){
											$('.opc-alert-danger').remove();
										}
									}

									break;

							}
						}

						self.initDateTimePicker();

					}
				}
			});
		};

		self.shippingUpdate = function(){
			// var data = $('.checkout_form input[type=\'text\'], .checkout_form input[type=\'password\'], .checkout_form input[type=\'hidden\']:not([name=\'client_id\']), .checkout_form input[type=\'checkbox\']:checked,.checkout-totals input[type=\'checkbox\']:checked, .checkout_form input[type=\'radio\']:checked, .checkout_form textarea, .checkout_form select').serialize();
			var data = $('.checkout_form input[type=\'text\'], .checkout_form input[type=\'password\'], .checkout_form input[type=\'hidden\'], .checkout_form input[type=\'checkbox\']:checked,.checkout-totals input[type=\'checkbox\']:checked, .checkout_form input[type=\'radio\']:checked, .checkout_form textarea, .checkout_form select').serialize();

			$.ajax({
				url: 'index.php?route=checkout/onepcheckout/shipping_method',
				type: 'post',
				data: data,
				dataType: 'html',
				cache: false,
				beforeSend: function() {
					// self.loading_mask(true);
				},
				complete: function() {
					self.loading_mask(false);
				},
				success: function(html) {
					if(html.length) {
						$('.opc_block_shipping_method').html(html);
					} else {
						location = 'index.php?route=checkout/cart';
					}
				}
			});
		};

		self.paymentUpdate = function(){
			var data = $('.checkout_form input[type=\'text\'], .checkout_form input[type=\'password\'], .checkout_form input[type=\'hidden\']:not([name=\'client_id\']), .checkout_form input[type=\'checkbox\']:checked,.checkout-totals input[type=\'checkbox\']:checked, .checkout_form input[type=\'radio\']:checked, .checkout_form textarea, .checkout_form select').serialize();

			$.ajax({
				url: 'index.php?route=checkout/onepcheckout/payment_method',
				type: 'post',
				data: data,
				dataType: 'html',
				cache: false,
				success: function(html) {
					$('.opc_block_payment_method').html(html);
				}
			});
		};

		self.updateCart = function(){
			var data = $('.checkout_form input[type=\'text\'], .checkout_form input[type=\'password\'], .checkout_form input[type=\'hidden\']:not([name=\'client_id\']), .checkout_form input[type=\'checkbox\']:checked, .checkout_form input[type=\'radio\']:checked, .checkout_form textarea, .checkout_form select');

			$.ajax({
				url: 'index.php?route=checkout/onepcheckout/cart',
				type: 'post',
				data: data,
				dataType: 'html',
				cache: false,
				success: function(html) {
					$(".table_total").html($(html).find(".table_total").html());
					$(".cart-list").html($(html).find(".cart-list").html());
					// $(".opc-dop-totals").html($(json.cart).find(".opc-dop-totals").html());
					$(".opc-dop-totals").html($(html.cart).find(".opc-dop-totals").html());
					$(".panel-group").html($(html).find(".panel-group").html());
					new Function(self.options.load_script)();
				}
			});
		};

		self.updateShippingCart = function(){
			setTimeout(function() {
				$.ajax({
					url: 'index.php?route=extension/module/price_product/addProductToCart',
					type: 'get',
					cache: false,
					success: function() {
						self.updateCart();

						self.updateTotals();

						self.paymentUpdate();
					}
				});
			}, 800);
		};

		self.updateTotals = function(){
			var data = $('.checkout_form input[type=\'text\'], .checkout_form input[type=\'password\'], .checkout_form input[type=\'hidden\']:not([name=\'client_id\']), .checkout_form input[type=\'checkbox\']:checked, .checkout_form input[type=\'radio\']:checked, .checkout_form textarea, .checkout_form select');

			$.ajax({
				url: 'index.php?route=checkout/onepcheckout/totals',
				type: 'post',
				data: data,
				dataType: 'html',
				cache: false,
				success: function(html) {
					if($(".opc-cart-weight").length){
						$(".opc-cart-weight").html($(html).find(".opc-cart-weight").html());
					}
					if($(".opc-agree").length){
						$(".opc-agree").html($(html).find(".opc-agree").html());
					}
					$(".table_total").html($(html).find(".table_total").html());
				}
			});
		};

		self.shippingAddressUpdate = function(){
			var data = $('.checkout_form input[type=\'text\'], .checkout_form input[type=\'password\'], .checkout_form input[type=\'hidden\']:not([name=\'client_id\']), .checkout_form input[type=\'checkbox\']:checked,.checkout-totals input[type=\'checkbox\']:checked, .checkout_form input[type=\'radio\']:checked, .checkout_form textarea, .checkout_form select').serialize();

			$.ajax({
				url: 'index.php?route=checkout/onepcheckout/shipping_address',
				type: 'post',
				data: data,
				dataType: 'html',
				cache: false,
				complete: function() {
					self.loading_mask(false);
				},
				success: function(html) {
					if($(html).find('.checkout-address-info .row').length){
						$('.opc_block_shipping_address').html(html);
						$('.opc_block_shipping_address').removeClass('hidden');
					} else {
						$('.opc_block_shipping_address').addClass('hidden');
					}
					self.initDateTimePicker();
				}
			}).done(function() {
				if (typeof self.initSelect2 == 'function') {
					self.initSelect2();
				}
			});
		};

		self.customerUpdate = function(){
			var data = $('.checkout_form input[type=\'text\'], .checkout_form input[type=\'password\'], .checkout_form input[type=\'hidden\']:not([name=\'client_id\']), .checkout_form input[type=\'checkbox\']:checked,.checkout-totals input[type=\'checkbox\']:checked, .checkout_form input[type=\'radio\']:checked, .checkout_form textarea, .checkout_form select').serialize();
			$.ajax({
				url: 'index.php?route=checkout/onepcheckout/customer',
				type: 'post',
				data: data,
				dataType: 'html',
				cache: false,
				beforeSend: function() {
					self.loading_mask(true);
				},
				complete: function() {
					self.loading_mask(false);
				},
				success: function(data) {
					$('.opc_block_customer').html(data);
					self.initMaskPhone();
					self.initDateTimePicker();
				}
			});
		};

		self.getZones = function(value){
			$.ajax({
				url: 'index.php?route=checkout/onepcheckout/country&country_id=' + value,
				dataType: 'json',
				success: function(json) {

					html = '<option value="">'+ self.options.text_select +'</option>';

					if (json['zone'] && json['zone'] != '') {
						for (i = 0; i < json['zone'].length; i++) {
							html += '<option value="' + json['zone'][i]['zone_id'] + '"';

							if (json['zone'][i]['zone_id'] == json['active_zone_id']) {
								html += ' selected="selected"';
							}

							html += '>' + json['zone'][i]['name'] + '</option>';
						}
					}
					$('select[name=\'zone_id\']').html(html);
					self.shippingUpdate();
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		};

		self.initMaskPhone111 = function(){
			// if(self.options.tel_mask.length && $("select[name=\'country_id\']").val() == 170){
			//	$("#input-opc-telephone").mask(self.options.tel_mask);
			// }
			// console.log($("select[name=\'country_id\']").val());
			setTimeout(function() {
				/*const inputd_tel = document.querySelector("#input-opc-telephone");
				var country_arr = {14:'at',21:'be',33:'bg',53:'hr',56:'cz',57:'dk',67:'ee',72:'fl',74:'fr',81:'de',84:'gr',97:'hu',103:'ie',105:'it',117:'lv',123:'lt',124:'lu',150:'nl',170:'pl',171:'pt',175:'ro',189:'sk',190:'sl',195:'es',203:'se',386:'si'};
				// console.log(country_arr[$("select[name=\'country_id\']").val()]);
				const itid_tel = window.intlTelInput(inputd_tel, {
				    allowDropdown: true,
				    autoPlaceholder: "polite",
				    fixDropdownWidth: true,
				    formatAsYouType: true,
				    // formatOnDisplay: true,
				    //initialCountry: country_arr[$("select[name=\'country_id\']").val()],
				    initialCountry: country_arr[$("input[name=\'country_id\']").val()],
				    // initialCountry: "auto",
				    nationalMode: true,
				    onlyCountries: ["at", "be", "bg", "hr", "cz", "dk", "ee", "fl", "fr", "de", "gr", "hu", "ie", "it", "lt", "lv", "lu", "nl", "pl", "pt", "ro", "sk", "sl", "se", "es", "si", "globe"],
				    placeholderNumberType: "MOBILE",
				    showFlags: true,
				    separateDialCode: false,
				    strictMode: true,
				    useFullscreenPopup: false,
				    validationNumberType: "MOBILE",
				});

				var country_arr = {14:'at',21:'be',33:'bg',53:'hr',56:'cz',57:'dk',67:'ee',72:'fl',74:'fr',81:'de',84:'gr',97:'hu',103:'ie',105:'it',117:'lv',123:'lt',124:'lu',150:'nl',170:'pl',171:'pt',175:'ro',189:'sk',190:'sl',195:'es',203:'se',386:'si'};
				window.intlTelInput.getInstance(document.querySelector("#input-opc-telephone")).setCountry(country_arr[$("input[name=\'country_id\']").val()]);*/
				$('#input-opc-telephone').mask('+999999999999');
			}, 400);
		};

		self.initMaskPhone = function(){
			setTimeout(function() {
				/*const input_tel = document.querySelector("#input-opc-telephone");
				const selectedCountryId = $("input[name='country_id']").val();

				const country_arr = {
					14:'at', 21:'be', 33:'bg', 53:'hr', 56:'cz', 57:'dk', 67:'ee', 72:'fl', 74:'fr',
					81:'de', 84:'gr', 97:'hu', 103:'ie', 105:'it', 117:'lv', 123:'lt', 124:'lu', 150:'nl',
					170:'pl', 171:'pt', 175:'ro', 189:'sk', 190:'sl', 195:'es', 203:'se', 386:'si'
				};

				const onlyCountries = Object.values(country_arr); // массив только допустимых стран

				const iti = window.intlTelInput(input_tel, {
					allowDropdown: true,
					autoPlaceholder: "polite",
					fixDropdownWidth: true,
					formatAsYouType: true,
					initialCountry: country_arr[selectedCountryId] || "auto", // если выбрана страна — ставим, иначе авто
					nationalMode: true,
					onlyCountries: ["at", "be", "bg", "hr", "cz", "dk", "ee", "fl", "fr", "de", "gr", "hu", "ie", "it", "lt", "lv", "lu", "nl", "pl", "pt", "ro", "sk", "sl", "se", "es", "si", "globe"],
					placeholderNumberType: "MOBILE",
					showFlags: true,
					separateDialCode: false,
					strictMode: true,
					useFullscreenPopup: false,
					validationNumberType: "MOBILE"
				});

				// Если страна выбрана — установить её
				if (country_arr[selectedCountryId]) {
					iti.setCountry(country_arr[selectedCountryId]);
				}*/
				$('#input-opc-telephone').mask('+999999999999');
			}, 400);
		};


		self.initDateTimePicker = function(){
			if($('#onepcheckout .date').length){
				$('.date').each(function() {
					$(this).datetimepicker({
						pickTime: false,
						minDate: new Date()
					});
				});
			}

			if($('#onepcheckout .time').length){
				$('.time').each(function() {
					$(this).datetimepicker({
						pickDate: false
					});
				});
			}

			if($('#onepcheckout .datetime').length){
				$('.datetime').each(function() {
					$(this).datetimepicker({
						pickDate: true,
						pickTime: true
					});
				});
			}
		};

		self.handleCouponButtonClick = function(){
			$.ajax({
				url: 'index.php?route=extension/total/coupon/coupon',
				type: 'post',
				data: 'coupon=' + encodeURIComponent($('input[name=\'coupon\']').val()),
				dataType: 'json',
				beforeSend: function() {
					$('input[name=\'coupon\']').attr('disabled', 'disabled');
				},
				complete: function() {
					$('input[name=\'coupon\']').removeAttr('disabled');
				},
				success: function(json) {
					$('.alert').remove();
					self.opcReloadAll();
					if (json['error']) {
						$('body').append('<div class="alert ch-alert-danger"><img class="warning-icon" alt="warning-icon" src="catalog/view/javascript/opc/image/warning-icon.svg"><div class="text-modal-block">' + json['error'] + '</div><button type="button" class="close" data-dismiss="alert"></button></div>');
					}
					if (json['success']) {
						$('body').append('<div class="alert ch-alert-success"><img class="success-icon" alt="success-icon" src="catalog/view/javascript/opc/image/success-icon.svg"><div class="text-modal-block">' + json['success'] + '</div><button type="button" class="close" data-dismiss="alert"></button></div>');
					}
				}
			});
		};

		self.handleRewardButtonClick = function(){
			$.ajax({
				url: 'index.php?route=extension/total/reward/reward',
				type: 'post',
				data: 'reward=' + encodeURIComponent($('input[name=\'reward\']').val()),
				dataType: 'json',
				beforeSend: function() {
					$('input[name=\'reward\']').attr('disabled', 'disabled');
				},
				complete: function() {
					$('input[name=\'reward\']').removeAttr('disabled');
				},
				success: function(json) {
					$('.alert').remove();

					if (json['error']) {
						$('body').append('<div class="alert ch-alert-danger"><img class="warning-icon" alt="warning-icon" src="catalog/view/javascript/opc/image/warning-icon.svg"><div class="text-modal-block">' + json['error'] + '</div><button type="button" class="close" data-dismiss="alert"></button></div>');
					}
					if (json['success']) {
						$('body').append('<div class="alert ch-alert-success"><img class="success-icon" alt="success-icon" src="catalog/view/javascript/opc/image/success-icon.svg"><div class="text-modal-block">' + json['success'] + '</div><button type="button" class="close" data-dismiss="alert"></button></div>');
					}
					self.opcReloadAll();
				}
			});
		};

		self.handleVoucherButtonClick = function(){
			$.ajax({
				url: 'index.php?route=extension/total/voucher/voucher',
				type: 'post',
				data: 'voucher=' + encodeURIComponent($('input[name=\'voucher\']').val()),
				dataType: 'json',
				beforeSend: function() {
					$('input[name=\'voucher\']').attr('disabled', 'disabled');
				},
				complete: function() {
					$('input[name=\'voucher\']').removeAttr('disabled');
				},
				success: function(json) {
					$('.alert').remove();
					self.opcReloadAll();
					if (json['error']) {
						$('body').append('<div class="alert ch-alert-danger"><img class="warning-icon" alt="warning-icon" src="catalog/view/javascript/opc/image/warning-icon.svg"><div class="text-modal-block">' + json['error'] + '</div><button type="button" class="close" data-dismiss="alert"></button></div>');
					}
					if (json['success']) {
						$('body').append('<div class="alert ch-alert-success"><img class="success-icon" alt="success-icon" src="catalog/view/javascript/opc/image/success-icon.svg"><div class="text-modal-block">' + json['success'] + '</div><button type="button" class="close" data-dismiss="alert"></button></div>');
					}
				}
			});
		};

		self.plusMinusQty = function(elem, action){
			var $parent = elem.closest('.ch-cart-quantity');

			var key = $parent.find('input').data('key');
			var minimum = parseFloat($parent.find('input').data('minimum'));
			minimum = minimum < 1 ? 1 : minimum;
			var quantity = parseFloat($parent.find('input').val().replace(/[^\d]/g, ''));

			if (quantity === '' || quantity === 0) {
				quantity = minimum;
			} else if (action === 'plus') {
				quantity += minimum;
			} else if (action === 'minus') {
				if (quantity <= minimum) {
					quantity = minimum;
				} else {
					quantity -= minimum;
				}
			}

			$parent.find('input').val(quantity).change();
			self.updateQty(key, quantity, minimum);
		};

		self.updateQty = function(key, quantity, minimum = 1){

			if(quantity >= minimum){
				$.ajax({
					url: 'index.php?route=checkout/onepcheckout/cart_edit',
					type: 'post',
					data: 'quantity[' + key + ']='+ quantity,
					dataType: 'json',
					beforeSend: function() {
						self.loading_mask(true);
					},
					complete: function() {
						self.loading_mask(false);
					},
					success: function(json) {
						self.opcReloadAll();
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		}

		self.opcValidateQty = function(elem) {
			var input = $(elem);

			var minimum = input.data('minimum');
			var value = input.val().trim();
			var key = input.data('key');

			if (/^0/.test(value)) {
				input.val(minimum);
			} else {
				var count = value.replace(/[^\d]/g, '');
				if (count === '') count = minimum;
				if (count === '0') count = minimum;
				if (count < minimum) count = minimum;
				input.val(count);
			}

			input.change();
			self.updateQty(key, count, minimum);
		};

		self.removeProduct = function(key){
			$.ajax({
				url: 'index.php?route=checkout/cart/remove',
				type: 'post',
				data: 'key=' + key,
				dataType: 'json',
				beforeSend: function() {
					self.loading_mask(true);
				},
				complete: function() {
					self.loading_mask(false);
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		};

		self.saveFields = function(){
			$.ajax({
				url: 'index.php?route=checkout/onepcheckout/save_fields',
				type: 'post',
				data: $('.checkout_form input[type=\'text\']:not([id*=\'input_pr_quantity_\']), .checkout_form input[type=\'hidden\'], .checkout_form input[type=\'checkbox\']:checked, .checkout_form input[type=\'radio\']:checked, .checkout_form textarea, .checkout_form select'),
				cache: false,
			});
		};

		self.initSelect2 = function () {
			$('.opc_block_shipping_address').find("select[data-type=select2]").each(function() {
				$(this).select2();
			});
		};

		self.authorization = function () {
			$(document).on('click', '.opc_login', function (e) {
				e.preventDefault();
				$('#ft-login-open').trigger('click');
			});
		};

		self.validateAuthorization = function () {
			$(document).on('click', '#button-login-popup', function (e) {
				e.preventDefault();
				$.ajax({
					url: 'index.php?route=checkout/onepcheckout/validate_authorization',
					type: 'post',
					data: $('#opc_authorization input'),
					dataType: 'json',
					beforeSend: function() {
						self.loading_mask(true);
					},
					complete: function() {
						self.loading_mask(false);
					},
					success: function(json) {
						$('.alert.ch-alert-danger').remove();

						if(json['islogged']){
							window.location.href="index.php?route=account/account";
						}
						if (json['error']) {
							$('body').append('<div class="alert ch-alert-danger"><img class="success-icon" alt="success-icon" src="catalog/view/javascript/opc/image/warning-icon.svg"><div class="text-modal-block">' + json['error'] + '</div><button type="button" class="close" data-dismiss="alert">&times;</button></div>');
						}

						setTimeout(function () {
							$('.ch-alert-danger').remove();
						}, 3000);

						if(json['success']){
							location.reload();
							$('#login-form-popup').modal('hide');
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});
		};

		self.addTopCartRight = function () {
			if(self.viewport().width > 991){
				if($('header').hasClass('fix-header')){
					$('.checkout-col-fix-right').css('top', document.querySelector('header').clientHeight + 30);
				} else {
					$('.checkout-col-fix-right').css('top', 115);
				}
			} else {
				$('.checkout-col-fix-right').css('top', 0);
			}
		};

		self.response = function () {
			var base = this,
			smallDelay,
			lastWindowWidth;

			lastWindowWidth = $(window).width();
			self.addTopCartRight();
			base.resizer = function () {
				if ($(window).width() !== lastWindowWidth) {

					window.clearTimeout(smallDelay);
					smallDelay = window.setTimeout(function () {
						lastWindowWidth = $(window).width();
						self.addTopCartRight();
					}, 200);
				}
			};
			$(window).resize(base.resizer);
		};

		self.loading_mask = function(action){
			if (action) {
				if(!$('.loading_mask').length){
					$('body').append('<div class="loading_mask"></div>');
				}
				$('.loading_mask').html('<div class="center-body"><div class="opc-loader-circle"></div></div>');
				$('.loading_mask').show();
			} else {
				$('.loading_mask').html('');
				$('.loading_mask').hide();
			}
		};

		self.initRelatedSlider = function(){
			var swiperCarousel = new Swiper('.carousel_related_prodcuts', {
				slidesPerView: 2,
				watchSlidesVisibility: true,
				watchSlidesProgress: true,
				watchOverflow: true,
				observer: true,
				observeParents: true,
				spaceBetween: 20,
				nested: false,
				speed: 400,
				breakpointsBase: 'container',
				grabCursor: true,
				navigation: {
					enabled: false,
				},
				scrollbar: {
				  el: '.carousel-related-scrollbar',
				  draggable: true,
				},
				on: {
					afterInit: function () {
						setTimeout(function () {
							$('.carousel_related_prodcuts').addClass('swiper-visible');
						}, 500);

					},
				},
				breakpoints: {
				  400 : {slidesPerView: 2},
				  600 : {slidesPerView: 3},
				  740: {slidesPerView: 4},
				  992: {slidesPerView: 5},
				  1200: {slidesPerView: 6}
				}
			});
		};

		self.viewport = function(){
			let e = window, a = 'inner';
			if (!('innerWidth' in window )) {
				a = 'client';
				e = document.documentElement || document.body;
			}
			return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
		}

	}

	window.OnePageCheckout = OnePageCheckout;
})(jQuery);


function editClientInfo(customer_id){
	$.ajax({
		url: 'index.php?route=account/edit_dsc/editInfoDsc&customer_id=' + customer_id,
		type: 'GET',
		dataType: 'html',
		success: function(data) {
			var html = '<div id="ft-edit-dsc-modal" class="modal fade" role="dialog">' + data + '</div>'

			$('body').append(html);

			$('#ft-edit-dsc-modal').modal('show');

			setTimeout(function() {
				/*inputmd_tel = document.querySelector(".edit-dsc-modal #input-telephone");
				var country_arr = {14:'at',21:'be',33:'bg',53:'hr',56:'cz',57:'dk',67:'ee',72:'fl',74:'fr',81:'de',84:'gr',97:'hu',103:'ie',105:'it',117:'lv',123:'lt',124:'lu',150:'nl',170:'pl',171:'pt',175:'ro',189:'sk',190:'sl',195:'es',203:'se',386:'si'};
				const itid_tel = window.intlTelInput(inputmd_tel, {
				    allowDropdown: true,
				    autoPlaceholder: "polite",
				    fixDropdownWidth: true,
				    formatAsYouType: true,
				    formatOnDisplay: true,
				   	initialCountry: "auto",
				    nationalMode: true,
				    onlyCountries: ["at", "be", "bg", "hr", "cz", "dk", "ee", "fl", "fr", "de", "gr", "hu", "ie", "it", "lt", "lv", "lu", "nl", "pl", "pt", "ro", "sk", "sl", "se", "es", "si", "globe"],
				    placeholderNumberType: "MOBILE",
				    showFlags: true,
				    separateDialCode: false,
				    strictMode: true,
				    useFullscreenPopup: false,
				    validationNumberType: "MOBILE",
				});*/
				$('.edit-dsc-modal #input-telephone').mask('+999999999999');
			}, 200);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});

	$(document).on('hide.bs.modal', '#ft-edit-dsc-modal.modal.fade', function () {
		inputmd_tel = undefined;

		$('#ft-edit-dsc-modal').remove();
	});
}

$(document).on('click', '#ft-edit-dsc-modal #save_dsc_fileds', function(e) {
	var $btn = $('#save_dsc_fileds');
   	var originalText = $btn.text();

	$.ajax({
		url: 'index.php?route=account/edit_dsc/saveDsc',
		type: 'post',
		data: $('#ft-edit-dsc-modal #form-edit-default-fileds').serialize(),
		dataType: 'json',
		beforeSend: function() {
    		$btn.text($btn.attr('data-loading'));
		},
		complete: function() {
			$btn.text(originalText);
		},
		success: function(json) {

			if (json['error']) {
				$('#ft-edit-dsc-modal #form-edit-default-fileds').prepend('<div class="alert alert-danger alert-dismissible mt-4 mb-4">' + json['error'] + ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
			}

			setTimeout(function () {
				$('#ft-edit-dsc-modal #form-edit-default-fileds .alert').remove();
			}, 3000);

			if(json['success']){

				if($('#button-add-client').length){
					$('input[name=\'client_name_tel\']').val(json['client_info']);
					$('input[name=\'client_id\']').val(json['client_id']);
					setTimeout(function () {
						$('#button-add-client').trigger('click');
					}, 500);
				}

				$('#ft-edit-dsc-modal #form-edit-default-fileds').prepend('<div class="alert alert-success alert-dismissible mt-4 mb-4">' + json['success'] + ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></div>');
				setTimeout(function () {
					$('#ft-edit-dsc-modal').modal('hide');
					$('#ft-edit-dsc-modal').remove();
				}, 500);
			}

		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});