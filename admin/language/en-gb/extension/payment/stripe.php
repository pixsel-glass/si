<?php
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

$_['version'] = 'v2025-6-09';

//------------------------------------------------------------------------------
// Heading
//------------------------------------------------------------------------------
$_['heading_title']						= 'Stripe Payment Gateway Pro';
$_['text_stripe']						= '<a target="blank" href="https://dashboard.stripe.com"><svg style="margin-top: 5px" viewBox="0 0 60 25" width="60" height="25"><title>Stripe</title><path fill="#0A2540" d="M59.64 14.28h-8.06c.19 1.93 1.6 2.55 3.2 2.55 1.64 0 2.96-.37 4.05-.95v3.32a8.33 8.33 0 0 1-4.56 1.1c-4.01 0-6.83-2.5-6.83-7.48 0-4.19 2.39-7.52 6.3-7.52 3.92 0 5.96 3.28 5.96 7.5 0 .4-.04 1.26-.06 1.48zm-5.92-5.62c-1.03 0-2.17.73-2.17 2.58h4.25c0-1.85-1.07-2.58-2.08-2.58zM40.95 20.3c-1.44 0-2.32-.6-2.9-1.04l-.02 4.63-4.12.87V5.57h3.76l.08 1.02a4.7 4.7 0 0 1 3.23-1.29c2.9 0 5.62 2.6 5.62 7.4 0 5.23-2.7 7.6-5.65 7.6zM40 8.95c-.95 0-1.54.34-1.97.81l.02 6.12c.4.44.98.78 1.95.78 1.52 0 2.54-1.65 2.54-3.87 0-2.15-1.04-3.84-2.54-3.84zM28.24 5.57h4.13v14.44h-4.13V5.57zm0-4.7L32.37 0v3.36l-4.13.88V.88zm-4.32 9.35v9.79H19.8V5.57h3.7l.12 1.22c1-1.77 3.07-1.41 3.62-1.22v3.79c-.52-.17-2.29-.43-3.32.86zm-8.55 4.72c0 2.43 2.6 1.68 3.12 1.46v3.36c-.55.3-1.54.54-2.89.54a4.15 4.15 0 0 1-4.27-4.24l.01-13.17 4.02-.86v3.54h3.14V9.1h-3.13v5.85zm-4.91.7c0 2.97-2.31 4.66-5.73 4.66a11.2 11.2 0 0 1-4.46-.93v-3.93c1.38.75 3.1 1.31 4.46 1.31.92 0 1.53-.24 1.53-1C6.26 13.77 0 14.51 0 9.95 0 7.04 2.28 5.3 5.62 5.3c1.36 0 2.72.2 4.09.75v3.88a9.23 9.23 0 0 0-4.1-1.06c-.86 0-1.44.25-1.44.9 0 1.85 6.29.97 6.29 5.88z"></path></svg></a>';

$_['heading_welcome']					= 'Welcome to Clear Thinking\'s ' . $_['heading_title'] . '!';
$_['help_first_time']					= '
The extension settings all have default values that work for most installations. To start processing payments quickly, all you need to do is the following:
<br><br>
<ol>
	<li>Click "Save" in the upper-right corner to save all default settings.</li><br>
	<li>Visit the "Stripe Settings" tab, and click "Connect With Stripe". This will lead you through the connection process to link the extension to your Stripe account.</li><br>
	<li>If you plan on using non-card payment methods, you will need to (1) enter the Webhook URL into your Stripe account, which you can find under the "Connect With Stripe" button, and (2) set up the non-card payment methods you want to use in your Stripe account at <a target="_blank" href="https://dashboard.stripe.com/settings/payment_methods">https://dashboard.stripe.com/settings/payment_methods</a></li><br>
	<li>The extension is set to "Test" mode by default. Once you are ready to go live, you can change the "Transaction Mode" setting in the "Stripe Settings" tab to "Live" mode.</li><br>
	<li>If you have any questions, feel free to reach out at <a target="_blank" href="https://www.getclearthinking.com/contact">https://www.getclearthinking.com/contact</a></li>
</ol>
';

//------------------------------------------------------------------------------
// Extension Settings
//------------------------------------------------------------------------------
$_['tab_extension_settings']			= 'Extension Settings';
$_['heading_extension_settings']		= 'Extension Settings';

$_['entry_status']						= 'Status: <div class="help-text">Set the status for the extension as a whole.</div>';
$_['entry_check_for_updates']			= 'Check For Updates: <div class="help-text">Choose whether to automatically check for updates when the extension admin panel is loaded.</div>';
$_['entry_check_for_updates']			= 'Check For Updates: <div class="help-text">Choose whether to automatically check for updates when the extension admin panel is loaded.</div>';
$_['entry_sort_order']					= 'Sort Order: <div class="help-text">Enter the sort order for the extension, relative to other payment methods.</div>';
$_['entry_title']						= 'Title: <div class="help-text">Enter the title for the payment method displayed to the customer in the Payment Method area of checkout. HTML is supported.</div>';
$_['entry_terms']						= 'Terms: <div class="help-text">Optionally enter some payment terms that will display next to the title in ( and ) brackets. HTML is supported.</div>';

// Payment Form
$_['heading_payment_form']				= 'Payment Form';
$_['entry_instructions']				= 'Instructions: <div class="help-text">Optionally enter some instructions displayed to the customer above the payment form. HTML is supported.</div>';
$_['entry_payment_form_style']			= 'Payment Form Style: <div class="help-text">You can see an example of the different options <a target="_blank" href="https://b.stripecdn.com/docs-statics-srv/assets/pe_layout_example.34c1e1aea085d98973276c1c36dfe724.png">in this image</a>.</div>';
$_['text_tabs']							= 'Tabs';
$_['text_accordion_with_radio']			= 'Accordion with radio buttons';
$_['text_accordion_without_radio']		= 'Accordion without radio buttons';

$_['entry_payment_form_default']		= 'Payment Form Default Choice: <div class="help-text">Choose whether the default payment option is initially collapsed or expanded.</div>';
$_['text_collapsed']					= 'Collapsed';
$_['text_expanded']						= 'Expanded';

$_['entry_accordion_space_choices']		= 'Space Choices (Accordion Style): <div class="help-text">For the accordion style, choose whether the payment options have spacing between them.</div>';

$_['entry_button_text']					= 'Button Text: <div class="help-text">Enter the text for the order confirmation button.</div>';
$_['entry_button_class']				= 'Button Class: <div class="help-text">Enter the CSS class for buttons in your theme.</div>';
$_['entry_button_styling']				= 'Button Styling: <div class="help-text">Optionally enter extra CSS styling for the button.</div>';
$_['entry_additional_css']				= 'Additional CSS: <div class="help-text">Add any additional CSS styling here. The main form\'s selector is <code>#payment</code>, but most styling needs to be done using the Payment Form Theme settings below.</div>';

// Payment Form Theme
$_['heading_payment_form_theme']		= 'Payment Form Theme';
$_['entry_theme']						= 'Payment Form Theme: <div class="help-text">Choose the theme for the payment form. You can customize things further yourself by using the <a target="_blank" href="https://stripe.com/docs/stripe-js/appearance-api">Appearance API</a>.</div>';
$_['text_stripe_theme']					= 'Stripe';
$_['text_night']						= 'Night';
$_['text_flat']							= 'Flat';
$_['text_none']							= 'None';
$_['entry_theme_labels']				= 'Form Label Positioning: <div class="help-text">"Floating" labels will appear in the form field until clicked, and then float to above the data being entered.</div>';
$_['text_above']						= 'Above';
$_['text_floating']						= 'Floating';
$_['entry_theme_variables']				= 'Theme Variables: <div class="help-text">Warning: incorrect data in this field could break the payment form. Be sure to check the <a target="_blank" href="https://stripe.com/docs/stripe-js/appearance-api">Appearance API</a> for proper formatting.<br><br>Do not include the "variables { ... } " part of the code, just the parameters that go in that array.</div>';
$_['entry_theme_rules']					= 'Theme Rules: <div class="help-text">Warning: incorrect data in this field could break the payment form. Be sure to check the <a target="_blank" href="https://stripe.com/docs/stripe-js/appearance-api">Appearance API</a> for proper formatting.<br><br>Do not include the "rules { ... } " part of the code, just the parameters that go in that array.</div>';

// Payment Page Text
$_['heading_payment_page_text']			= 'Payment Page Text';
$_['entry_text_please_wait']			= 'Default Please Wait: <div class="help-text">HTML is supported.</div>';
$_['entry_text_validating_payment_info']= 'Validating Payment Info: <div class="help-text">HTML is supported.</div>';
$_['entry_text_processing_payment']		= 'Processing Payment: <div class="help-text">HTML is supported.</div>';
$_['entry_text_finalizing_order']		= 'Finalizing Order: <div class="help-text">HTML is supported.</div>';

// Cards Page Text
$_['heading_cards_page_text']			= 'Cards Page Text';

$_['entry_cards_page_heading']			= 'Cards Page Heading: <div class="help-text">HTML is supported.</div>';
$_['entry_cards_page_none']				= 'No Cards Message: <div class="help-text">HTML is supported.</div>';
$_['entry_cards_page_default_card']		= 'Default Card Text: <div class="help-text">HTML is supported.</div>';
$_['entry_cards_page_ending_in']		= '"ending in" Text:';
$_['entry_cards_page_make_default']		= 'Make Default Button:';
$_['entry_cards_page_delete']			= 'Delete Button:';
$_['entry_cards_page_confirm']			= 'Confirmation Message:';
$_['entry_cards_page_add_card']			= 'Add New Card Button:';
$_['entry_cards_page_card_name']		= 'Name on Card: <div class="help-text">HTML is supported.</div>';
$_['entry_cards_page_card_details']		= 'Card Details: <div class="help-text">HTML is supported.</div>';
$_['entry_cards_page_card_address']		= 'Card Address: <div class="help-text">HTML is supported.</div>';
$_['entry_cards_page_success']			= 'Success Message:';

// Subscriptions Page Text
$_['heading_subscriptions_page_text']	= 'Subscriptions Page Text';

$_['entry_subscriptions_page_heading']	= 'Subscriptions Page Heading: <div class="help-text">HTML is supported.</div>';
$_['entry_subscriptions_page_message']	= 'Default Card Message: <div class="help-text">HTML is supported.</div>';
$_['entry_subscriptions_page_none']		= 'No Subscriptions Message: <div class="help-text">HTML is supported.</div>';
$_['entry_subscriptions_page_trial']	= 'Trial End Text: <div class="help-text">HTML is supported.</div>';
$_['entry_subscriptions_page_last']		= 'Last Invoice Text: <div class="help-text">HTML is supported.</div>';
$_['entry_subscriptions_page_next']		= 'Next Invoice Text: <div class="help-text">HTML is supported.</div>';
$_['entry_subscriptions_page_cycles']	= 'Cycles Text: <div class="help-text">HTML is supported.</div>';
$_['entry_subscriptions_page_charge']	= 'Additional Charge Text: <div class="help-text">HTML is supported.</div>';
$_['entry_subscriptions_page_pause']	= 'Pause Button:';
$_['entry_subscriptions_page_weeks']	= 'Weeks Text:';
$_['entry_subscriptions_page_paused_until']	= 'Paused Until Text:';
$_['entry_subscriptions_page_unpause']	= 'Unpause Button:';
$_['entry_subscriptions_page_cancel']	= 'Cancel Button:';
$_['entry_subscriptions_page_confirm']	= 'Cancel Confirmation: <div class="help-text">Enter the text displayed to the customer to confirm their cancellation of a subscription. The customer will be required to type <b>CANCEL</b> in order to confirm their cancellation.</div>';


//------------------------------------------------------------------------------
// Order Statuses
//------------------------------------------------------------------------------
$_['tab_order_statuses']				= 'Order Statuses';
$_['heading_order_statuses']			= 'Order Statuses';
$_['help_order_statuses']				= 'Choose the order statuses set when a payment meets each condition. You can refund a payment by using the link provided in the History tab for the order.<br><br>Note: to actually <b>deny</b> payments that fail CVC or Postcode Checks, you need to enable that in the Payments > Fraud & Risk > Rules area of your Stripe admin panel.';

$_['entry_initial_status_id']			= 'Initial Status: <div class="help-text">This status will apply when the order is completed but payment is not yet verified. It is used by some non-card payment methods, and for the initial status set for all orders if you are using Stripe Checkout.</div>';
$_['entry_success_status_id']			= 'Successful Payment (Captured):';
$_['entry_authorize_status_id']			= 'Successful Payment (Authorized):';
$_['entry_mismatch_status_id']			= 'Successful Payment (Address Mismatch): <div class="help-text">This status will apply when the customer\'s shipping address does not match their billing address. It will override both of the two Successful Payment statuses set above. If you set the Charge Mode setting to "Authorize if possible fraudulent", then an address mismatch will also cause the payment to be authorized instead of captured.</div>';
$_['entry_error_status_id']				= 'Order Completion Error: <div class="help-text">This status will apply either when a non-card payment method fails, or when a card payment is completed successfully, but the order cannot be completed using the normal OpenCart order confirmation functions. This usually happens when you have entered incorrect SMTP settings in System > Settings > Mail, or you have installed modifications that affect customer orders.</div>';
$_['entry_review_status_id']			= 'Stripe Radar "Manual Review":';
$_['entry_elevated_status_id']			= 'Stripe Radar "Elevated" Risk:';
$_['entry_highest_status_id']			= 'Stripe Radar "Highest" Risk:';
$_['entry_street_status_id']			= 'Street Check Failure:';
$_['entry_postcode_status_id']			= 'Postcode Check Failure:';
$_['entry_cvc_status_id']				= 'CVC Check Failure:';
$_['entry_refund_status_id']			= 'Fully Refunded Payment:';
$_['entry_partial_status_id']			= 'Partially Refunded Payment:';

$_['text_ignore']						= '--- Ignore ---';

//------------------------------------------------------------------------------
// Restrictions
//------------------------------------------------------------------------------
$_['tab_restrictions']					= 'Restrictions';
$_['heading_restrictions']				= 'Restrictions';
$_['help_restrictions']					= 'Set the required cart total and select the eligible stores, geo zones, and customer groups for this payment method.';

$_['entry_min_total']					= 'Minimum Total: <div class="help-text">Enter the minimum order total that must be reached before this payment method becomes active. Leave blank to have no restriction.</div>';
$_['entry_max_total']					= 'Maximum Total: <div class="help-text">Enter the maximum order total that can be reached before this payment method becomes inactive. Leave blank to have no restriction.</div>';

$_['entry_stores']						= 'Store(s): <div class="help-text">Select the stores that can use this payment method.</div>';

$_['entry_geo_zones']					= 'Geo Zone(s): <div class="help-text">Select the geo zones that can use this payment method. The "Everywhere Else" checkbox applies to any locations not within a geo zone.</div>';
$_['text_everywhere_else']				= '<em>Everywhere Else</em>';

$_['entry_customer_groups']				= 'Customer Group(s): <div class="help-text">Select the customer groups that can use this payment method. The "Guests" checkbox applies to all customers not logged in to an account.</div>';
$_['text_guests']						= '<em>Guests</em>';

// Currency Settings
$_['heading_currency_settings']			= 'Currency Settings';
$_['help_currency_settings']			= 'Select the currencies that Stripe will charge in, based on the order currency. <a target="_blank" href="https://support.stripe.com/questions/which-currencies-does-stripe-support">See which currencies your country supports</a>';
$_['entry_currencies']					= 'When Orders Are In [currency], Charge In:';
$_['text_currency_disabled']			= '--- Disabled ---';

//------------------------------------------------------------------------------
// Stripe Settings
//------------------------------------------------------------------------------
$_['tab_stripe_settings']				= 'Stripe Settings';
$_['help_stripe_settings']				= 'Click "Connect with Stripe" to visit stripe.com and allow the extension to process payments via your Stripe account. (Note: payments marked as "Incomplete" in Stripe are abandoned orders.)';

// Connect with Stripe
$_['heading_connect_with_stripe']		= 'Connect with Stripe';

$_['button_connect_with_stripe']		= 'Connect with Stripe';
$_['entry_connection_status']			= 'Connection Status:';
$_['text_not_connected']				= 'Not Connected';
$_['text_connected']					= 'Connected';

$_['entry_account_id']					= 'Account ID:';
$_['entry_refresh_token']				= 'Refresh Token:';
$_['entry_live_publishable_key']		= 'Live Publishable Key:';
$_['entry_live_access_token']			= 'Live Access Token:';
$_['entry_test_publishable_key']		= 'Test Publishable Key:';
$_['entry_test_access_token']			= 'Test Access Token:';

$_['entry_webhook_url']					= 'Webhook URL:';
$_['help_webhook_url']					= 'Copy and paste this URL into your Stripe account in the <a target="_blank" href="https://dashboard.stripe.com/webhooks">Developers > Webhooks</a> area. You will need to add it separately in both Test mode and Live mode if you want it to work in both modes.<br><br>To create the webhook, click "Add Destination" on the Webhooks page, and then choose to receive "all events". Any events that the extension does not use will be ignored. If you prefer to only enable the events that the extension currently uses, <a onclick="$(\'#webhook-events\').slideToggle()">click here to display the list</a>.
<br>
<code id="webhook-events" style="display: none; margin: 15px; padding: 5px 10px;">
cash_balance.funds_available<br>
customer.created<br>
customer.deleted<br>
charge.captured<br>
charge.refunded<br>
checkout.session.completed<br>
customer.subscription.deleted<br>
payment_intent.partially_funded<br>
payment_intent.payment_failed<br>
payment_intent.succeeded<br>
invoice.payment_failed<br>
invoice.payment_succeeded
</code>
<br>
You can choose any API version for the webhook URL; the choice will not affect the extension, as it explicitly declares an API version when making API calls. The "key" for the URL is based on your store\'s Encryption Key in System > Settings > Server, so if you change that don\'t forget to also update the webhook URL in Stripe.';

// Stripe Settings
$_['heading_stripe_settings']			= 'Stripe Settings';

$_['entry_transaction_mode']			= 'Transaction Mode: <div class="help-text">Use "Test" to test payments through Stripe. For more info, visit <a href="https://stripe.com/docs/testing" target="_blank">https://stripe.com/docs/testing</a>. Use "Live" when you\'re ready to accept payments.</div>';
$_['text_test']							= 'Test';
$_['text_live']							= 'Live';

$_['entry_charge_mode']					= 'Charge Mode: <div class="help-text">Choose whether to authorize payments and manually capture them later, or to capture (i.e. fully charge) payments when orders are placed. For payments that are only Authorized, you can Capture them by using the link provided in the History tab for the order.<br><br>If you choose "Authorize if possibly fraudulent, Capture otherwise" then the extension will authorize the payment if (1) the Stripe Radar result is "review", or (2) Stripe Radar returns a risk level of "highest", or (3) your OpenCart anti-fraud extensions determine that the order might be fraudulent.</div>';
$_['text_authorize']					= 'Authorize';
$_['text_capture']						= 'Capture';
$_['text_fraud_authorize']				= 'Authorize if possibly fraudulent, Capture otherwise';

$_['entry_transaction_description']		= 'Transaction Description: <div class="help-text">Enter the text sent as the Stripe transaction description. You can use the following shortcodes to enter information about the order: [store], [order_id], [amount], [email], [name], [comment], [products]</div>';
$_['entry_attempts']					= 'Maximum Number of Allowed Attempts: <div class="help-text">Enter the maximum number of payment attempts customers are allowed to try in one session. This helps prevent fraudsters from running lots of cards through your Stripe account to check if they are valid. Leave this field blank to have no maximum limit.</div>';
$_['entry_attempts_exceeded']			= 'Error When Exceeding Allowed Attempts: <div class="help-text">Enter the error message shown when a customer continues to make payment attempts after exceeding the allowed number of attempts set above.</div>';

$_['entry_send_customer_data']			= 'Send Customer Data: <div class="help-text">Sending customer data will create a customer in Stripe when an order is processed, based on the email address for the order. If the customer uses a credit/debit card for the purchase, it will be attached to this customer, allowing you to charge them again in the future in Stripe.</div>';
$_['text_never']						= 'Never';
$_['text_always']						= 'Always';

$_['entry_advanced_error_handling']		= 'Advanced Error Handling: <div class="help-text">Enabling this will catch errors that occur outside the extension, and record those into the order history. Quick checkout extensions sometimes have issues with this, so if you encounter problems with payments completing but orders not being confirmed, try disabling this setting.</div>';

// Notification Settings
$_['heading_notification_settings']		= 'Notification Settings';
$_['entry_always_send_receipts']		= 'Always Send Receipts From Stripe: <div class="help-text">Receipts are normally only sent from Stripe if the customer\'s info is stored in Stripe, and you have enabled receipt sending in your Stripe admin panel. If you set this to "Yes", then a receipt will always be sent to customers from Stripe, no matter what your settings are in the Stripe admin panel.</div>';
$_['entry_decline_code_emails']			= 'Notify About "fraudulent" Decline Codes: <div class="help-text">If you want to notify administrators about any declined payments that have the code "fraudulent", enter their e-mail addresses here, separated by commas.</div>';
$_['entry_subscription_fail_emails']	= 'Notify About Failed Subscription Payments: <div class="help-text">If you want to notify administrators when a subscription payment fails, enter their e-mail addresses here, separated by commas.</div>';
$_['entry_uncaptured_emails']			= 'Notify About Uncaptured Payments: <div class="help-text">If you want to notify administrators about any uncaptured payments, enter their e-mail addresses here, separated by commas. The extension will send out an e-mail once a day if any uncaptured transactions are found.</div>';

//------------------------------------------------------------------------------
// Checkout Options
//------------------------------------------------------------------------------
$_['tab_checkout_options']				= 'Checkout Options';

// Pop-up Payment Form
$_['heading_popup_payment_form']		= 'Pop-up Payment Form';
$_['help_popup_payment_form']			= 'Choose whether to require the customer to click a "Proceed to Payment" button first. Once the button is clicked a pop-up will be displayed with the payment form. This is not normally needed with the default OpenCart checkout, but can help with quick checkout extensions that reload the payment form repeatedly as fields on the page change. This setting will not apply if Stripe Checkout is enabled.';

$_['entry_use_popup']					= 'Use Pop-up Payment Form:';
$_['entry_popup_button_text']			= 'Button Text: <div class="help-text">Enter the text for the button initially shown to the customer, titled "Proceed to Payment" by default. HTML is supported.</div>';

// Stripe Checkout
$_['heading_stripe_checkout']			= 'Stripe Checkout';
$_['help_stripe_checkout_info']			= 'Stripe Checkout redirects the customer to Stripe.com for them to enter payment info. This will replace the normal payment form in the checkout. You can read more about it and view a demo at <a target="_blank" href="https://stripe.com/docs/payments/checkout">https://stripe.com/docs/payments/checkout</a>';

$_['entry_checkout']					= 'Use Stripe Checkout: <div class="help-text">Choose whether to use Stripe Checkout instead of accepting the payment on your website. Once the customer is finished with their transaction, they will be redirected back to your store.<br><br>Stripe Checkout does not support negative line items at this time, so if the order contains a discount then only the order total will be shown on the payment page. <b>In OpenCart 4.0, you also need to make sure the "Session Samesite Cookie" setting in System > Settings > Server tab is set to "Lax" or "None".</b></div>';
$_['entry_checkout_billing_address']	= 'Require Billing Address: <div class="help-text">Stripe Checkout will always require the postcode for address verification, but if you want to require the full billing address on the stripe.com payment page (for example, to utilize the Street Address verification check), set this to "Yes".</div>';
$_['entry_checkout_phone_number']		= 'Require Phone Number: <div class="help-text">Enable this setting if you want Stripe Checkout to require that the customer fill in their phone number.</div>';
$_['entry_checkout_total']				= '"Total" Text: <div class="help-text">Enter the text used for "Total" on the payment page, if there is a discount on the order.</div>';
$_['entry_checkout_no_order_id']		= '"No Order ID" Error: <div class="help-text">Some quick checkouts do not properly create the order_id before loading the payment method, which can cause issues with the payment process. This error message will be shown if the order_id is not present when payment is attempted.</div>';
$_['entry_additional_webhook_urls']		= 'Additional Webhook URLs: <div class="help-text">If using a multi-store installation, you will need to enter these additional webhooks in your Stripe admin panel if you are using Stripe Checkout. You can add the URLs in <a target="_blank" href="https://dashboard.stripe.com/webhooks">Developers > Webhooks</a>. Click "Add Endpoint" and then choose to receive only this event: <code>checkout.session.completed</code></div>';

// Express Checkout Button
$_['heading_express_checkout_button']	= 'Express Checkout Button';
$_['help_express_checkout_button']		= 'The "Express Checkout" button uses Stripe\'s <a target="_blank" href="https://docs.stripe.com/elements/express-checkout-element">Express Checkout Element</a> to render Apple Pay, Amazon Pay, Google Pay, and Stripe Link buttons on the product page, cart page, or checkout page. This allows customers to make an immediate payment for the product (if on the product page) or their cart (if on a non-product page), without them needing to go through the normal checkout process.<br><br>The customer\'s billing and shipping addresses from their wallet will be used for the order. Since this also skips the normal checkout, be aware that any checkout-related customizations like payment fees, additional fields, or other modifications will also be skipped.<br><br><b>To add the Express Checkout button to the product page or cart page, embed the following code in the relevant template files where you want the button to appear:</b><br><br>
<code style="margin-left: 30px">
&lt;script id="express-checkout-script" data-oc="' . substr(VERSION, 0, 1) . '" src="' . (version_compare(VERSION, '4.0', '<') ? '' : 'extension/stripe/') . 'catalog/view/javascript/express_checkout.js"&gt;&lt;/script&gt;
</code>';

$_['entry_express_payment_methods']		= 'Payment Methods: <div class="help-text">Choose the payment methods that the Express Checkout button will show. You can see supported browsers for each payment method on <a target="_blank" href="https://docs.stripe.com/elements/express-checkout-element#supported-browsers">this Stripe documentation page</a>.</div>';
$_['text_amazon_pay']					= 'Amazon Pay';
$_['text_apple_pay']					= 'Apple Pay';
$_['text_google_pay']					= 'Google Pay';
$_['text_klarna']						= 'Klarna';
$_['text_stripe_link']					= 'Stripe Link';

$_['entry_express_alignment']			= 'Alignment:';
$_['text_left']							= 'Left';
$_['text_right']						= 'Right';

$_['entry_express_button_height']		= 'Button Height: <div class="help-text">Enter the height of the buttons in pixels. Valid values are from 40 to 55. Leave blank to use Stripe\'s default.</div>';
$_['entry_express_width']				= 'Express Checkout Width: <div class="help-text">Enter the weight of the entire Express Checkout element in pixels. The minimum is 240 pixels on most devices. Leave blank to expand to the width of the parent element.</div>';
$_['entry_express_margins']				= 'Margins: <div class="help-text">Enter the margins around the entire Express Checkout element, either as a single value like <code>15px</code> or as four CSS values like <code>10px 15px 5px 0px</code>.</div>';
$_['entry_express_max_columns']			= 'Maximum Columns: <div class="help-text">Enter the maximum number of columns that will be shown. Leave blank to have no limit.</div>';
$_['entry_express_max_rows']			= 'Maximum Rows: <div class="help-text">Enter the maximum number of rows that will be shown. Leave blank to have no limit.</div>';
$_['entry_express_autocreate_account']	= 'Auto-Create Customer Account: <div class="help-text">Choose whether a customer account with a randomized password will be automatically created for the customer if they use the Express Checkout button to make a purchase as a guest. If enabled then upon successful payment the customer will be logged into their new account and the "Forgot Your Password" email will be triggered so they can reset their password. If you do not choose to enable the "Automatically Log Customer In" setting below then existing customers will be blocked from purchasing and told to log in first.</div>';
$_['entry_express_autologin']			= 'Automatically Log Customers In: <div class="help-text">If set to "Yes" then after the payment is confirmed, the customer will be automatically logged into their OpenCart account if one exists with the e-mail address used in the Express Checkout pop-up. If you choose to enable this setting then be aware of potential security implications. While Apple, Amazon, Google, and Stripe do have a verification check in their pop-up before allowing payment, you will be relying on their security to log the customer in, instead of OpenCart\'s password system.</div>';

// Quick Buy Button
$_['heading_quick_buy_button']			= 'Quick Buy Button';

$_['help_quick_buy_button']				= 'The "Quick Buy" button uses Stripe Checkout to complete a customer\'s order, without requiring them to go through the normal checkout process. This does not require Stripe Checkout to be enabled, so you can use the normal card form in the checkout if you prefer. The advantage this has over the Express Checkout button is that customers can use any form of payment, rather than just Apple Pay, Amazon Pay, Google Pay, or Stripe Link.<br><br>Because the Quick Buy button uses Stripe Checkout, <b>make sure you have set up the webhook from the extension in your Stripe account</b>. Since this also skips the normal checkout, be aware that any checkout-related customizations like payment fees, additional fields, or other modifications will also be skipped.
<br><br>
The way the button works is as follows:
<br><br>
<ol>
	<li>If the customer clicks the button from the product page, it will add that item if it is not already in the cart, and then display a pop-up. If the customer clicks the button from the cart page, it will immediately display the pop-up.</li>
	<li>Within the pop-up, the first step will ask the customer to enter their shipping address. If the customer is logged in, it will preload their default address.</li>
	<li>Once the shipping address is entered, the customer can then choose a shipping rate from the enabled shipping methods, as usual.</li>
	<li>After the customer chooses their shipping rate, they will be redirected to Stripe Checkout to complete the payment.</li>
	<li>Upon completing the payment, the order will be created in OpenCart, and the customer will be returned to the checkout success page.</li>
</ol>
<b>To add the Quick Buy button to the product page or cart page, embed the following code in the relevant template files where you want the button to appear:</b><br><br>
<code style="margin-left: 30px">
&lt;script id="quick-buy-script" data-oc="' . substr(VERSION, 0, 1) . '" src="' . (version_compare(VERSION, '4.0', '<') ? '' : 'extension/stripe/') . 'catalog/view/javascript/quick_buy.js"&gt;&lt;/script&gt;
</code>';

$_['entry_quick_buy_html']				= 'Button HTML: <div class="help-text">Enter the HTML for the Quick Buy button. Use [button_text] in place of the button text. Make sure you use an &lt;a&gt; tag for the button itself.</div>';
$_['entry_quick_buy_text']				= 'Button Text: <div class="help-text">HTML is supported.</div>';
$_['entry_quick_buy_address_text']		= '"Enter Your Shipping Address" Text: <div class="help-text">HTML is supported.</div>';
$_['entry_quick_buy_shipping_text']		= '"Choose Your Shipping Method" Text: <div class="help-text">HTML is supported.</div>';
$_['entry_quick_buy_geozone_error']		= 'Geo Zone Error: <div class="help-text">This message will be displayed if the customer\'s shipping address is not within one of the extension\'s geo zones. HTML is supported.</div>';

//------------------------------------------------------------------------------
// Other Payment Methods
//------------------------------------------------------------------------------
$_['tab_other_payment_methods']			= 'Other Payment Methods';
$_['help_other_payment_methods']		= 'Non-card payment methods cannot be used with subscriptions at this time. These payment methods will be hidden if there is a subscription product in the cart.';
$_['heading_other_payment_methods']		= 'Other Payment Methods';

$_['entry_payment_methods_setup']		= 'Payment Methods Setup:';
$_['help_payment_methods_setup']		= 'All payment methods are controlled from your Stripe account. Keep in mind you may need to set up your Test mode payment methods separately from your Live mode payment methods. To enable them, follow these steps:<br><br>
<ol style="margin-bottom: 0">
	<li>Visit <a target="_blank" href="https://dashboard.stripe.com/settings/payment_methods">https://dashboard.stripe.com/settings/payment_methods</a></li><br>
	<li>Choose the Default profile that says "Stripe Payment Gateway configuration".</li><br>
	<li>On the following page, choose which payment methods you want to turn on.</li><br>
	<li>Make sure you add the webhook from the extension into your Stripe account, in the <a target="_blank" href="https://dashboard.stripe.com/webhooks">Developers > Webhooks</a> area.</li><br>
	<li>If you are using a multi-store installation, be sure to <b>add a webhook for each store</b> by replacing the domain in the webhook. You can also look in the Checkout Options tab (Stripe Checkout section) for a list of the additional webhooks you should add.</li><br>
	<li>You\'re done! Payment options will appear to customers during checkout based on their selected country and currency. If Stripe adds new payment options, they will automatically appear (and can be enabled) in the Payment Methods area of Stripe.</li><br>
</ol>
';

$_['entry_applepay']					= 'Apple Pay:';
$_['text_applepay']						= 'Apple Pay requires two extra steps beyond the method above, but those should happen automatically when connecting the extension to your Stripe account. You do not need to manually add your domain in Stripe, or manually upload the "domain association file" that Stripe asks you to. These two steps will happen automatically when connecting the extension.<br><br>To use Apple Pay, customers must be using Safari and have a card stored in their browser or iOS device. Your checkout page also must be https, even if you\'re in Test mode.<br><br>If Apple Pay does not seem to be working, you can use the button below to try and reconnect Apple Pay. You may need to do this step if you only enabled Apple Pay AFTER connecting the extension.';
$_['entry_reconnect_apple_pay']			= '';
$_['button_reconnect_apple_pay']		= 'Reconnect Apple Pay';

$_['entry_buy_now_pay_later_messaging']	= '"Buy Now Pay Later" Messaging:';
$_['help_buy_now_pay_later_messaging']	= 'If you offer Affirm, Afterpay, or Klarna as a payment option, you can add Stripe\'s <a target="_blank" href="https://docs.stripe.com/payments/payment-method-messaging">Payment Method Messaging</a> element to the product page and cart page by embedding the following code in the relevant template files where you want the button to appear:<br><br>
<code style="margin-left: 30px">
&lt;script id="payment-method-messaging-script" data-oc="' . substr(VERSION, 0, 1) . '" src="' . (version_compare(VERSION, '4.0', '<') ? '' : 'extension/stripe/') . 'catalog/view/javascript/stripe_messaging.js"&gt;&lt;/script&gt;
</code>';

$_['entry_delayed_payment_emails']		= 'Delayed Payment Notifications: <div class="help-text">If you want to notify administrators when a delayed payment is completed or fails, enter their e-mail addresses here, separated by commas.</div>';
$_['entry_override_extension_title']	= 'Override Extension Title: <div class="help-text">Enable this setting to override the Title of the extension on the order with the name of the payment method chosen by the customer.</div>';
$_['entry_error_page']					= 'Error Page HTML: <div class="help-text">Enter the HTML for the error page if any off-site payment authorization fails. Use [header] in place of your site header, [footer] in place of your site footer, and [error] for the error message returned by Stripe.</div>';

//------------------------------------------------------------------------------
// Subscription Products
//------------------------------------------------------------------------------
$_['tab_subscription_products']			= 'Subscription Products';
$_['help_subscription_products']		= '
<ul>
	<li>Subscription products will subscribe the customer to the associated Stripe pricing plan when they are purchased. You can associate a product with a pricing plan by entering the Stripe <b>Pricing Plan ID</b> (not the Product ID) in the "Location" field for the product.</li><br>
	<li>If the subscription has a trial period, the amount of the subscription will be taken off their original order, and a new order will be created when the subscription is actually charged to their card.</li><br>
	<li>Any time Stripe charges the subscription in the future, a corresponding order will be created in OpenCart (in Sales > Orders). You can choose whether the order uses the customer\'s default address in OpenCart or the customer\'s address in Stripe below.</li><br>
	<li>If you have a coupon set up in your Stripe account, you can map an OpenCart coupon to it by using the same coupon code and discount amount. Use the OpenCart coupon code for the <b>coupon ID</b> when creating the coupon in Stripe. When a customer purchases a subscription product and uses that coupon code, it will pass the code to Stripe to properly adjust the subscription charge.</li><br>
	<li class="text-danger">Subscriptions made with cards using 3D Secure may require the customer to return to Stripe in order to validate their payment info when the card is charged in the future. Make sure you have set up e-mails with a "Stripe-hosted link" to go out to customers, as described <a target="_blank" href="https://stripe.com/docs/billing/migration/strong-customer-authentication#3ds-payment-settings">on this page</a>.</li>
</ul>	
';

// Subscription Product Settings
$_['heading_subscription_products']		= 'Subscription Product Settings';
$_['entry_subscriptions']				= 'Enable Subscription Products:';

$_['entry_subscription_order_report']	= 'Subscription Order Report: <div class="help-text">This will display a list of all orders that were placed for subscription products.</div>';
$_['button_view_report']				= 'View Report';

$_['text_order_statuses']				= 'Order Status:';
$_['text_all_statuses']					= '--- All Statuses ---';
$_['text_starting_date']				= 'Starting Date:';
$_['text_ending_date']					= 'Ending Date:';
$_['button_filter']						= 'Filter';

$_['heading_subscription_order_report']	= 'Subscription Order Report';
$_['column_order_id']					= 'Order ID';
$_['column_customer']					= 'Customer';
$_['column_status']						= 'Status';
$_['column_total']						= 'Total';
$_['column_date_added']					= 'Date Added';
$_['column_date_modified']				= 'Date Modified';
$_['column_action']						= 'Action';

$_['entry_text_to_be_charged']			= '"To Be Charged Later" Text: <div class="help-text">This text is displayed for the line item added to the order when a subscription has a trial. The line item subtracts the subscription price out of the total, so the customer is not double-charged.</div>';
$_['entry_prevent_guests']				= 'Prevent Guests From Purchasing: <div class="help-text">If set to "Yes", only customers with accounts in OpenCart will be allowed to checkout if a subscription product is in the cart.</div>';
$_['entry_text_customer_required']		= 'Customer Required: <div class="help-text">If using the setting above, enter the text displayed when a non-logged-in customer (a guest) tries to check out with a subscription product in their cart.</div>';

$_['entry_order_address']				= 'Subscription Order Shipping Address: <div class="help-text">Choose the address to use for the shipping address on orders created when a subscription is charged. The billing address on subscription orders will always be the address on the card used for payment.<br><br>A customer\'s "Stripe address" will be changed whenever they use a stored card, store a new card, or purchase a subscription product. A customer\'s "OpenCart address" will be changed when they modify their default address in OpenCart. The "original order address" will always be the address that was used for the initial subscription order.</div>';
$_['text_stripe_address']				= 'Use Stripe address';
$_['text_opencart_address']				= 'Use OpenCart address';
$_['text_original_address']				= 'Use original order address';

// Advanced Subscription Settings
$_['heading_advanced_subscription_settings'] = 'Advanced Subscription Settings';
$_['entry_include_shipping']			= 'Include Shipping: <div class="help-text">This setting does not work with Stripe Checkout. If set to "Yes" and there is a shipping cost on the order, a Stripe invoice item for the product\'s shipping cost will be created. Every time the subscription is charged in the future, a new invoice item will be created for the following charge date, with the same shipping cost.</div>';
$_['entry_merge_subscriptions']			= 'Merge Subscriptions: <div class="help-text">This setting does not work with Stripe Checkout. If set to "Yes", when customers purchase multiple subscriptions at the same time, they will be combined into a single invoice for the customer. Do not use this setting when subscriptions have different charge periods, trial lengths, start dates, or cycles.</div>';

$_['entry_manage_subscriptions']		= 'Allow Customers to Manage Their Subscriptions: <div class="help-text">If enabled, customers will be able to manage their cards and subscriptions via an OpenCart account page. The URL for this page is:<br><br><a target="_blank" href="../index.php?route=' . (version_compare(VERSION, '4.0', '<') ? 'extension/cards' : 'extension/stripe/extension/cards') . '">' . str_replace('http://', 'https://', HTTP_CATALOG) . 'index.php?route=' . (version_compare(VERSION, '4.0', '<') ? 'extension/cards' : 'extension/stripe/extension/cards') . '</a><br><br>If you choose to use the Stripe Customer Portal, be aware that there is a cost in Stripe to use this feature. You will also need to set up the portal in Stripe first, in both <a target="_blank" href="https://dashboard.stripe.com/test/settings/billing/portal">Test mode</a> and <a target="_blank" href="https://dashboard.stripe.com/settings/billing/portal">Live mode</a></div>';
$_['text_cancel_only']					= 'Cancel Subscriptions Only';
$_['text_pause_only']					= 'Pause Subscriptions Only';
$_['text_cancel_and_pause']				= 'Cancel and Pause Subscriptions';
$_['text_use_customer_portal']			= 'Use the Stripe Customer Portal';

$_['entry_pause_options']				= 'Pause Duration Options: <div class="help-text">If you want to restrict pausing to a limited number of weeks, then enter a comma-delimited list of values in this field. For example, to offer options to pause subscriptions for 4, 8, or 12 weeks enter <code>4, 8, 12</code>. Customers will then be able to choose from these values when pausing a subscription. Leave this field blank to allow customers to pause subscriptions indefinitely.</div>';

$_['entry_transfer_subscriptions']		= 'Transfer Subscriptions: <div class="help-text">You can use this function to transfer all customers from one Pricing Plan to another Pricing Plan. No one will be left on the old Pricing Plan ID that you enter, so only use this function if you plan on discontinuing the old plan. This function will take approximately 1 second per subscription, so if you have a lot of subscriptions it may take some time to transfer them all.</div>';
$_['button_transfer_subscriptions']		= 'Transfer Subscriptions';
$_['text_old_pricing_plan_id']			= 'Old Pricing Plan ID:';
$_['text_new_pricing_plan_id']			= 'New Pricing Plan ID:';
$_['button_transfer']					= 'Transfer';

// Current Subscription Products
$_['heading_current_subscriptions']		= 'Current Subscription Products';
$_['entry_current_subscriptions']		= 'Current Subscription Products: <div class="help-text">Products with mismatching prices are highlighted. The customer will always be charged the Stripe plan price, not the OpenCart product price, so you should make sure the price in OpenCart corresponds to the price in Stripe.<br><br>Note: only plans for your Transaction Mode will be listed. You are currently set to "[transaction_mode]" mode.</div>';

$_['text_thead_opencart']				= 'OpenCart';
$_['text_thead_stripe']					= 'Stripe';
$_['text_product_name']					= 'Product Name';
$_['text_product_price']				= 'Product Price';
$_['text_location_plan_id']				= 'Location / Plan ID';
$_['text_plan_name']					= 'Plan Name';
$_['text_plan_interval']				= 'Plan Interval';
$_['text_plan_charge']					= 'Plan Charge';
$_['text_no_subscription_products']		= 'No Subscription Products';
$_['text_create_one_by_entering']		= 'Create one by entering the Stripe pricing plan ID in the "Location" field for the product';

// Map Options to Subscriptions
$_['heading_map_options']				= 'Map Options to Stripe Subscriptions';
$_['help_map_options']					= '
<ul>
	<li>If the customer has a product with the appropriate option name and option value in their cart, they will be subscribed to the corresponding plan ID. This will override the plan ID in the Location field for that product.</li><br>
	<li>The "Start Date" field lets you force a particular start date for the subscription, rather than start it immediately, or after its trial period. Leave the "Start Date" field blank to have the subscription start based on its own settings.</li><br>
	<li>The "Cycles" field lets you limit a subscription to a certain number of cycles. For example, if you enter 5 and the subscription is monthly, it will only charge the customer for the subscription 5 times, and then cancel the subscription. Leave the "Cycles" field blank to have no limitation.</li>
</ul>
';

$_['column_action']						= 'Action';
$_['column_option_name']				= 'Option Name';
$_['column_option_value']				= 'Option Value';
$_['column_currency']					= 'Currency';
$_['column_plan_id']					= 'Plan ID';
$_['column_start_date']					= 'Start Date';
$_['column_cycles']						= 'Cycles';

$_['text_all']							= '--- All ---';
$_['button_add_mapping']				= 'Add Mapping';

// Map Recurring Profiles to Subscriptions
$_['heading_map_recurring_profiles']	= 'Map ' . (version_compare(VERSION, '4.0', '<') ? 'Recurring Profiles' : 'OpenCart Subscription Plans') . ' to Stripe Subscriptions';
$_['help_map_recurring_profiles']		= '
<ul>
	<li>If the customer has a product with the selected ' . (version_compare(VERSION, '4.0', '<') ? 'recurring profile' : 'OpenCart subscription plan') . ' in their cart, they will be subscribed to the corresponding plan ID. This will override the plan ID in the Location field for that product. <b>The subscription frequency and charge amount is determined by the Stripe pricing plan, not the OpenCart settings, so make sure they match exactly</b>.</li><br>
	<li>The "Admin Reference" field is for internal notes, and will not be displayed to the customer.</li><br>
	<li>The "Start Date" field lets you force a particular start date for the subscription, rather than start it immediately, or after its trial period. Leave the "Start Date" field blank to have the subscription start based on its own settings.</li><br>
	<li>The "Cycles" field lets you limit a subscription to a certain number of cycles. For example, if you enter 5 and the subscription is monthly, it will only charge the customer for the subscription 5 times, and then cancel the subscription. Leave the "Cycles" field blank to have no limitation.</li>
</ul>
';

$_['column_profile_name']				= 'Admin Reference';
$_['column_recurring_or_subscription']	= (version_compare(VERSION, '4.0', '<')) ? 'Recurring Profile' : 'OpenCart Subscription Plan';

//------------------------------------------------------------------------------
// Create a Charge
//------------------------------------------------------------------------------
$_['tab_create_a_charge']				= 'Create a Charge';

$_['help_charge_info']					= 'After entering the charge info below, you will be redirected to Stripe to complete the payment. If you would like the customer to complete the payment, you can copy that URL and e-mail it to them.';
$_['heading_charge_info']				= 'Charge Info';

$_['entry_order_id']					= 'Order ID: <div class="help-text">This setting is optional. If filled in, an order history note will be added to the order regarding the payment. This will not work if setting your own custom Cancel URL and Success URL.</div>';
$_['entry_order_status']				= 'Order Status Change: <div class="help-text">This setting is optional. If set, and an Order ID value is set, then the order\'s status will be changed after the payment is successfully processed. This will not work if setting your own custom Cancel URL and Success URL.</div>';
$_['entry_description']					= 'Description: <div class="help-text">This setting is optional. This will be shown in your Stripe admin panel, and on the customer receipt if you have Stripe set to send them an e-mail receipt.</div>';
$_['entry_statement_descriptor']		= 'Statement Descriptor: <div class="help-text">This setting is optional. This will be shown on the customer\'s bank statement for the charge. It is a maximum of 22 characters. Note that not all banks respect the value that Stripe passes, so there is no guarantee this will be shown exactly as you\'ve written. The following characters are prohibited: < > " \'</div>';
$_['entry_cancel_url']					= 'Cancel URL: <div class="help-text">Leave this blank to return to this page. If you want to send the link to a customer, fill in this field with the URL to which they will be redirected if they cancel the payment. Make sure it is a valid URL including http or https.</div>';
$_['entry_success_url']					= 'Success URL: <div class="help-text">Leave this blank to return to this page. If you want to send the link to a customer, fill in this field with the URL to which they will be redirected if the payment is successful. Make sure it is a valid URL including http or https. Note that the "Order ID" and "Order Status Change" settings will not work if you use a custom Success URL (unless you have some logic in that page to handle the <code>&order_id=</code> and <code>&order_status_id=</code> URL parameters).</div>';
$_['entry_amount']						= 'Amount:';

$_['button_create_charge']				= 'Create Charge';

//------------------------------------------------------------------------------
// Standard Text
//------------------------------------------------------------------------------
$_['contact_url']						= 'https://www.getclearthinking.com/contact?storeurl=' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . '&version=' . VERSION;
$_['copyright']							= '<hr><div class="text-center" style="margin: 15px">' . $_['heading_title'] . ' (' . $_['version'] . ') &copy; <a target="_blank" href="' . $_['contact_url'] . '">Clear Thinking, LLC</a></div>';

$_['standard_autosaving_enabled']		= 'Auto-Saving Enabled';
$_['standard_confirm']					= 'This operation cannot be undone. Continue?';
$_['standard_error']					= '<strong>Error:</strong> You do not have permission to modify ' . $_['heading_title'] . '!';
$_['standard_max_input_vars']			= '<strong>Warning:</strong> The number of settings is close to your <code>max_input_vars</code> server value. You should enable auto-saving to avoid losing any data.';
$_['standard_please_wait']				= 'Please wait...';
$_['standard_saved']					= 'Saved!';
$_['standard_saving']					= 'Saving...';
$_['standard_select']					= '--- Select ---';
$_['standard_success']					= 'Success!';
$_['standard_testing_mode']				= "Your log is too large to open! If you need to archive it, you can download it using the button above.\n\nTo start a new log, (1) click the Clear Log button, (2) reload the admin panel page, then (3) run your test again.";

$_['standard_check_for_updates']		= 'Check For Updates';
$_['standard_contact_clear_thinking']	= 'Contact Clear Thinking';
$_['standard_error_checking']			= 'There was an error checking for the latest version.';
$_['standard_using_latest']				= 'You are using the latest version';
$_['standard_new_version']				= 'A new version is available!';
$_['standard_your_version']				= 'Your Version:';
$_['standard_latest_version']			= 'Latest Version:';
$_['standard_release_notes']			= 'View release notes';
$_['standard_continue']					= 'Continue';
$_['standard_update_warning']			= '<ul><li>Before updating, it is highly recommended to <b>back up your website files</b>.</li><br><li>To update, enter your license key below and click "Update". A license comes with 1 year of free updates, so you may not qualify for the latest version if you are beyond that period. If that is the case, you will be notified after attempting to update.</li><br><li>Updating the extension will <b>overwrite all current extension files</b>. If you have made modifications to any files, make sure you back up your edits before updating.</li><br><li>If any issues occur during or after updating, download and reinstall the extension manually.</li><br><li>If you have lost your license key or download link, you can retrieve them <a target="_blank" href="https://www.getclearthinking.com/downloads/license">on this page</a>.</li></ul><br>';
$_['standard_update']					= 'Update';
$_['standard_updating']					= 'Updating...';
$_['standard_license_key']				= 'License Key:';

$_['standard_module']					= 'Modules';
$_['standard_shipping']					= 'Shipping';
$_['standard_payment']					= 'Payments';
$_['standard_total']					= 'Order Totals';
$_['standard_feed']						= 'Feeds';

//------------------------------------------------------------------------------
// Images
//------------------------------------------------------------------------------
$_['connect_with_stripe_image'] = '<img width="190" height="33" src="data:image/png;base64,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" />';

?>