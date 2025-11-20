//==============================================================================
// Stripe Payment Gateway Pro v2025-6-09
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
// 
// All code within this file is copyright Clear Thinking, LLC.
// You may not copy or reuse code within this file without written permission.
//==============================================================================

var expressCheckoutRoute;
var expressCheckoutSettings;

$(document).ready(function(){
	if ($('#express-checkout-script').attr('data-oc') < 4) {
		expressCheckoutRoute = 'extension/express_checkout/';
	} else {
		expressCheckoutRoute = 'extension/stripe/extension/express_checkout.';
	}
	
	$.ajax({
		type: 'POST',
		url: 'index.php?route=' + expressCheckoutRoute.slice(0, -1),
		data: $('#product input[type="text"], #product input[type="hidden"], #product input[type="radio"]:checked, #product input[type="checkbox"]:checked, #product select, #product textarea'),
		dataType: 'json',
		success: function(json) {
			if (json.error_message) {
				alert(json.error_message);
			} else if (json.account_id) {
				expressCheckoutSettings = json;
				$('#express-checkout-script').after('<div id="express-checkout-element" style="display: none; margin: ' + expressCheckoutSettings.margins + '; width: ' + expressCheckoutSettings.width + '; ' + (expressCheckoutSettings.alignment == 'right' ? 'float: right;' : '') + '"></div>');
				loadExpressCheckout();
			}
		},
		error: function(xhr, status, error) {
			console.log(xhr.responseText ? xhr.responseText : error);
		}
	});
});

function loadExpressCheckout() {
	$.getScript('https://js.stripe.com/v3/', function() {
		var stripe = Stripe(expressCheckoutSettings.publishable_key, {betas: ['server_side_confirmation_beta_1', 'link_default_integration_beta_1'], stripeAccount: expressCheckoutSettings.account_id, apiVersion: '2022-08-01;server_side_confirmation_beta=v1'});
		
		var elements = stripe.elements({
			mode: 'payment',
			amount: expressCheckoutSettings.amount,
			currency: expressCheckoutSettings.currency,
			capture_method: 'manual',
		});
		
		var expressCheckoutOptions = {
			billingAddressRequired: true,
			buttonHeight: expressCheckoutSettings.button_height,
			emailRequired: true,
			layout: {
				maxColumns: expressCheckoutSettings.max_columns,
				maxRows: expressCheckoutSettings.max_rows,
			},
			lineItems: expressCheckoutSettings.line_items,
			phoneNumberRequired: true,
			paymentMethods: {},
		}
		
		expressCheckoutOptions.paymentMethods.amazonPay = (expressCheckoutSettings.payment_methods.includes('amazon_pay')) ? 'auto' : 'never';
		expressCheckoutOptions.paymentMethods.applePay = (expressCheckoutSettings.payment_methods.includes('apple_pay')) ? 'auto' : 'never';
		expressCheckoutOptions.paymentMethods.googlePay = (expressCheckoutSettings.payment_methods.includes('google_pay')) ? 'auto' : 'never';
		expressCheckoutOptions.paymentMethods.klarna = (expressCheckoutSettings.payment_methods.includes('klarna')) ? 'auto' : 'never';
		expressCheckoutOptions.paymentMethods.link = (expressCheckoutSettings.payment_methods.includes('link')) ? 'auto' : 'never';
		
		if (expressCheckoutSettings.requires_shipping) {
			expressCheckoutOptions.shippingAddressRequired = true;
			expressCheckoutOptions.shippingRates = [{id: 'tbd', displayName: 'TBD', amount: 0}];
		}
		
		var expressCheckoutElement = elements.create('expressCheckout', expressCheckoutOptions);
		
		expressCheckoutElement.mount('#express-checkout-element');
		
		expressCheckoutElement.on('ready', ({availablePaymentMethods}) => {
			if (!availablePaymentMethods) {
				$('#express-checkout-element').remove();
			} else {
				$('#express-checkout-element').show();
			}
		});
		
		var paymentType;
		
		expressCheckoutElement.on('click', (event) => {
			paymentType = event.expressPaymentType;
			
			$.ajax({
				type: 'POST',
				url: 'index.php?route=' + expressCheckoutRoute + 'loadExpressCheckout',
				data: $('#product input[type="text"], #product input[type="hidden"], #product input[type="radio"]:checked, #product input[type="checkbox"]:checked, #product select, #product textarea'),
				dataType: 'json',
				beforeSend: function() {
					console.log(event);
				},
				success: function(json) {
					console.log(json);
					if (json.error_message) {
						alert(json.error_message);
					} else {
						if (!$('#express-checkout-script').attr('data-stop-cart-reload')) {
							var separator = ($('#express-checkout-script').attr('data-oc') < 4) ? '/' : '.';
							$('#cart').load('index.php?route=common/cart' + separator + 'info');
						}
						
						if (json.amount) {
							elements.update({amount: json.amount});
						}
						event.resolve({lineItems: json.line_items});
					}
				},
				error: function(xhr, status, error) {
					alert(xhr.responseText ? xhr.responseText : error);
				}
			});
		});
		
		expressCheckoutElement.on('shippingaddresschange', async (event) => {
			$.ajax({
				type: 'POST',
				url: 'index.php?route=' + expressCheckoutRoute + 'getShippingRates',
				data: {address: event.address, payment_type: paymentType},
				dataType: 'json',
				beforeSend: function() {
					console.log(event);
				},
				success: function(json) {
					console.log(json);
					if (json.error_message) {
						event.reject();
					} else {
						elements.update({amount: json.amount})
						event.resolve({
							lineItems: json.line_items,
							shippingRates: json.shipping_rates,
						});
					}
				},
				error: function(xhr, status, error) {
					alert(xhr.responseText ? xhr.responseText : error);
				}
			});
		});
		
		expressCheckoutElement.on('shippingratechange', async (event) => {
			$.ajax({
				type: 'POST',
				url: 'index.php?route=' + expressCheckoutRoute + 'updateLineItems',
				data: {shipping_rate: event.shippingRate},
				dataType: 'json',
				beforeSend: function() {
					console.log(event);
				},
				success: function(json) {
					console.log(json);
					elements.update({amount: json.amount})
					event.resolve({
						lineItems: json.line_items,
					});
				},
				error: function(xhr, status, error) {
					alert(xhr.responseText ? xhr.responseText : error);
				}
			});
		});
		
		expressCheckoutElement.on('confirm', async (event) => {
			const {error: submitError} = await elements.submit();
			if (submitError) {
				alert(submitError);
				return;
			}
			
			const {confirmationError, confirmationToken} = await stripe.createConfirmationToken({elements});
			if (confirmationError) {
				alert(confirmationError);
				return;
			}
			
			$.ajax({
				type: 'POST',
				url: 'index.php?route=' + expressCheckoutRoute + 'createPaymentIntent',
				data: {event: event, confirmation_token: confirmationToken.id, current_page: location.href},
				dataType: 'json',
				beforeSend: function() {
					console.log(event);
					$('#express-checkout-element').html('<div class="alert alert-warning"><i class="fa fa-spinner fa-spin"></i> &nbsp; ' + expressCheckoutSettings.finalizing_order_text + '</div>');
				},
				success: function(json) {
					console.log(json);
					if (json.error_message) {
						$('#express-checkout-element').html('<div class="alert alert-danger">' + json.error_message + '</div>');
					} else if (json.status == 'requires_action') {
						stripe.handleNextAction({
							clientSecret: json.client_secret,
						}).then(function(result) {
							if (result.error) {
								console.log(result.error);
								$('#express-checkout-element').html('<div class="alert alert-danger">' + result.error.message + '</div>');
							} else {
								finalizePayment();
							}
						});
					} else {
						finalizePayment();
					}
				},
				error: function(xhr, status, error) {
					alert(xhr.responseText ? xhr.responseText : error);
				}
			});
		});
	});
}

function finalizePayment() {
	$.ajax({
		type: 'POST',
		url: 'index.php?route=' + expressCheckoutRoute + 'finalizePayment',
		data: {},
		dataType: 'json',
		success: function(json) {
			console.log(json);
			if (json.error_message) {
				$('#express-checkout-element').html('<div class="alert alert-danger">' + json.error_message + '</div>');
			} else {
				location = expressCheckoutSettings.checkout_success_url;
			}
		},
		error: function(xhr, status, error) {
			alert(xhr.responseText ? xhr.responseText : error);
		}
	});
}
