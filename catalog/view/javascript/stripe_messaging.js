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

$(document).ready(function(){
	if ($('#payment-method-messaging-script').attr('data-oc') < 4) {
		var stripeRoute = 'extension/payment/stripe/';
	} else {
		var stripeRoute = 'extension/stripe/payment/stripe.';
	}
	
	$.ajax({
		type: 'POST',
		url: 'index.php?route=' + stripeRoute + 'loadPaymentMethodMessaging',
		data: $('#product :input'),
		dataType: 'json',
		success: function(json) {
			loadPaymentMethodMessaging(json);
		},
		error: function(xhr, status, error) {
			console.log(xhr.responseText ? xhr.responseText : error);
		}
	});
});

function loadPaymentMethodMessaging(stripeSettings) {
	$('#payment-method-messaging-script').after('<div id="payment-method-messaging-element" style="margin: 15px 0"></div>');
	
	$.getScript('https://js.stripe.com/v3/', function() {
		var stripe = Stripe(stripeSettings.publishable_key, {stripeAccount: stripeSettings.account_id});
		
		var elements = stripe.elements();
		
		var PaymentMessageElement = elements.create('paymentMethodMessaging', {
			amount: stripeSettings.amount,
			currency: stripeSettings.currency,
			countryCode: stripeSettings.country_code,
		});
		
		PaymentMessageElement.mount('#payment-method-messaging-element');
	});
}