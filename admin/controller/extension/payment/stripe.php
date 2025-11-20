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

//namespace Opencart\Admin\Controller\Extension\Stripe\Payment;
//class Stripe extends \Opencart\System\Engine\Controller {

class ControllerExtensionPaymentStripe extends Controller { 
	
	private $type = 'payment';
	private $name = 'stripe';
	
	//==============================================================================
	// index()
	//==============================================================================
	public function index() {
		$data = array(
			'type'			=> $this->type,
			'name'			=> $this->name,
			'autobackup'	=> false,
			'save_type'		=> 'keepediting',
			'permission'	=> $this->hasPermission('modify'),
		);
		
		$this->loadSettings($data);
		
		// extension-specific
		if (!empty($this->request->get['error'])) {
			echo '<h3 class="alert alert-danger text-center" style="padding: 25px; margin: 0;">' . $this->request->get['error'] . '</h3>';
		} elseif (!empty($this->session->data['connect_success'])) {
			echo '<h3 class="alert alert-success text-center" style="padding: 25px; margin: 0;">' . $this->session->data['connect_success'] . '</h3>';
			unset($this->session->data['connect_success']);
		}
		
		if (empty($data['saved'])) {
			$data['save_type'] = 'reload';
		}
		
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $this->name . "_customer` (
				`customer_id` int(11) NOT NULL,
				`stripe_customer_id` varchar(18) NOT NULL,
				`transaction_mode` varchar(4) NOT NULL DEFAULT 'live',
				PRIMARY KEY (`customer_id`, `stripe_customer_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
		");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . $this->name . "_customer WHERE customer_id = 0");
		
		$transaction_mode_column = false;
		$database_table_query = $this->db->query("DESCRIBE " . DB_PREFIX . $this->name . "_customer");
		foreach ($database_table_query->rows as $column) {
			if ($column['Field'] == 'transaction_mode') {
				$transaction_mode_column = true;
			}
		}
		if (!$transaction_mode_column) {
			$this->db->query("ALTER TABLE " . DB_PREFIX . $this->name . "_customer ADD transaction_mode varchar(4) NOT NULL DEFAULT 'live'");
		}
		
		// 1.5 quick buy button changes
		if (version_compare(VERSION, '2.0', '<')) {
			$quick_buy_script = DIR_CATALOG . 'view/javascript/quick_buy.js';
			$file_contents = file_get_contents($quick_buy_script);
			file_put_contents($quick_buy_script, str_replace(array('collapse-shipping-address', '#product :input'), array('shipping-address', '.product-info :input'), $file_contents));
		}
		
		//------------------------------------------------------------------------------
		// Check for Stripe Checkout data (Pro-specific)
		//------------------------------------------------------------------------------
		if (!empty($this->request->get['order_id']) && !empty($this->request->get['checkout'])) {
			$order_id = $this->request->get['order_id'];
			
			if (!empty($this->request->get['order_status_id'])) {
				$order_status_id = $this->request->get['order_status_id'];
			} else {
				$order_status_id = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = " . (int)$order_id)->row['order_status_id'];
			}
			
			$comment = 'Stripe payment of ' . $this->request->get['checkout'] . ' completed via "Create a Charge" tab';
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = " . (int)$order_id . ", order_status_id = " . (int)$order_status_id . ", notify = 0, comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = " . (int)$order_status_id . " WHERE order_id = " . (int)$order_id);
		}
		
		if (isset($this->request->get['checkout'])) {
			$this->session->data['checkout_success'] = $this->request->get['checkout'];
			$token = (version_compare(VERSION, '3.0', '<')) ? 'token=' . $data['token'] : 'user_token=' . $data['token'];
			$this->response->redirect(str_replace('&amp;', '&', $this->url->link($data['extension_route'], $token, 'SSL')));
		}
		
		if (isset($this->session->data['checkout_success'])) {
			echo '<h3 class="alert alert-success text-center" style="padding: 25px; margin: 0;">Your payment of ' . $this->session->data['checkout_success'] . ' completed successfully!</h3>';
			unset($this->session->data['checkout_success']);
		}
		
		//------------------------------------------------------------------------------
		// Data Arrays
		//------------------------------------------------------------------------------
		$config_language = ($this->config->get('config_language_admin')) ? $this->config->get('config_language_admin') : $this->config->get('config_language');
		$data['language_array'] = array($config_language => '');
		$data['language_flags'] = array();
		$this->load->model('localisation/language');
		foreach ($this->model_localisation_language->getLanguages() as $language) {
			$data['language_array'][$language['code']] = $language['name'];
			if (version_compare(VERSION, '2.2', '<')) {
				$data['language_flags'][$language['code']] = 'view/image/flags/' . $language['image'];
			} elseif (empty($language['extension'])) {
				$data['language_flags'][$language['code']] = 'language/' . $language['code'] . '/' . $language['code'] . '.png';
			} else {
				$data['language_flags'][$language['code']] = '../extension/' . $language['extension'] . '/admin/language/' . $language['code'] . '/' . $language['code'] . '.png';
			}
		}
		
		$data['order_status_array'] = array(0 => $data['text_ignore']);
		$this->load->model('localisation/order_status');
		foreach ($this->model_localisation_order_status->getOrderStatuses() as $order_status) {
			$data['order_status_array'][$order_status['order_status_id']] = $order_status['name'];
		}
		
		$data['customer_group_array'] = array(0 => $data['text_guests']);
		$this->load->model((version_compare(VERSION, '2.1', '<') ? 'sale' : 'customer') . '/customer_group');
		foreach ($this->{'model_' . (version_compare(VERSION, '2.1', '<') ? 'sale' : 'customer') . '_customer_group'}->getCustomerGroups() as $customer_group) {
			$data['customer_group_array'][$customer_group['customer_group_id']] = $customer_group['name'];
		}
		
		$data['geo_zone_array'] = array(0 => $data['text_everywhere_else']);
		$this->load->model('localisation/geo_zone');
		foreach ($this->model_localisation_geo_zone->getGeoZones() as $geo_zone) {
			$data['geo_zone_array'][$geo_zone['geo_zone_id']] = $geo_zone['name'];
		}
		
		$data['store_array'] = array(0 => $this->config->get('config_name'));
		$store_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY name");
		foreach ($store_query->rows as $store) {
			$data['store_array'][$store['store_id']] = $store['name'];
		}
		
		$data['currency_array'] = array($this->config->get('config_currency') => '');
		$this->load->model('localisation/currency');
		foreach ($this->model_localisation_currency->getCurrencies() as $currency) {
			$data['currency_array'][$currency['code']] = $currency['code'];
		}
		
		// Get subscription products
		$data['subscription_products'] = array();
		
		if (!empty($data['saved']['subscriptions']) &&
			!empty($data['saved']['transaction_mode']) &&
			!empty($data['saved'][$data['saved']['transaction_mode'] . '_access_token'])
		) {
			$plan_response = $this->curlRequest('GET', 'plans', array('count' => 100));
			
			if (!empty($plan_response['error'])) {
				$this->log->write('STRIPE ERROR: ' . $plan_response['error']['message']);
			} else {
				$plans = $plan_response['data'];
				
				while (!empty($plan_response['has_more'])) {
					$plan_response = $this->curlRequest('GET', 'plans', array('count' => 100, 'starting_after' => $plans[count($plans) - 1]['id']));
					if (empty($plan_response['error'])) {
						$plans = array_merge($plans, $plan_response['data']);
					}
				}
				
				foreach ($plans as $plan) {
					$decimal_factor = (in_array(strtoupper($plan['currency']), array('BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','UGX','VND','VUV','XAF','XOF','XPF'))) ? 1 : 100;
					$decimal_factor = (in_array(strtoupper($plan['currency']), array('BHD','JOD','KWD','OMR','TND'))) ? 1000 : $decimal_factor;
					
					$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = " . (int)$this->config->get('config_language_id') . ") WHERE p.location = '" . $this->db->escape($plan['id']) . "'");
					
					foreach ($product_query->rows as $product) {
						$data['subscription_products'][] = array(
							'product_id'	=> $product['product_id'],
							'name'			=> $product['name'],
							'price'			=> $this->currency->format($product['price'], $this->config->get('config_currency')),
							'location'		=> $product['location'],
							'plan'			=> $plan['nickname'],
							'interval'		=> $plan['interval_count'] . ' ' . $plan['interval'] . ($plan['interval_count'] > 1 ? 's' : ''),
							'charge'		=> $this->currency->format($plan['amount'] / $decimal_factor, strtoupper($plan['currency']), 1, strtoupper($plan['currency'])),
						);
					}
				}
			}
		}
		
		//------------------------------------------------------------------------------
		// Extensions Settings
		//------------------------------------------------------------------------------
		$data['settings'] = array();
		
		$data['settings'][] = array(
			'type'		=> 'tabs',
			'tabs'		=> array('extension_settings', 'order_statuses', 'restrictions', 'stripe_settings', 'checkout_options', 'other_payment_methods', 'subscription_products', 'create_a_charge'),
		);
		$data['settings'][] = array(
			'key'		=> 'extension_settings',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'status',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_enabled'], 0 => $data['text_disabled']),
			'default'	=> 1,
		);
		$data['settings'][] = array(
			'key'		=> 'check_for_updates',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'sort_order',
			'type'		=> 'text',
			'default'	=> 1,
			'class'		=> 'short',
		);
		$data['settings'][] = array(
			'key'		=> 'title',
			'type'		=> 'multilingual_text',
			'default'	=> 'Credit / Debit Card',
		);
		
		if (version_compare(VERSION, '4.0', '<')) {
			$data['settings'][] = array(
				'key'		=> 'terms',
				'type'		=> 'multilingual_text',
			);
		}
		
		// Payment Form
		$data['settings'][] = array(
			'key'		=> 'payment_form',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'instructions',
			'type'		=> 'multilingual_textarea',
		);
		$data['settings'][] = array(
			'key'		=> 'payment_form_style',
			'type'		=> 'select',
			'options'	=> array('tabs' => $data['text_tabs'], 'accordion_radio' => $data['text_accordion_with_radio'], 'accordion' => $data['text_accordion_without_radio']),
			'default'	=> 'tabs',
		);
		$data['settings'][] = array(
			'key'		=> 'payment_form_default',
			'type'		=> 'select',
			'options'	=> array('collapsed' => $data['text_collapsed'], 'expanded' => $data['text_expanded']),
			'default'	=> 'expanded',
		);
		$data['settings'][] = array(
			'key'		=> 'accordion_space_choices',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'button_text',
			'type'		=> 'multilingual_text',
			'default'	=> 'Confirm Order',
		);
		$data['settings'][] = array(
			'key'		=> 'button_class',
			'type'		=> 'text',
			'default'	=> 'btn btn-primary',
		);
		$data['settings'][] = array(
			'key'		=> 'button_styling',
			'type'		=> 'text',
		);
		$data['settings'][] = array(
			'key'		=> 'additional_css',
			'type'		=> 'textarea',
			'default'	=> '#payment { background: white; }',
		);
		
		// Payment Form Theme
		$data['settings'][] = array(
			'key'		=> 'payment_form_theme',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'theme',
			'type'		=> 'select',
			'options'	=> array('stripe' => $data['text_stripe_theme'], 'night' => $data['text_night'], 'flat' => $data['text_flat'], 'none' => $data['text_none']),
		);
		$data['settings'][] = array(
			'key'		=> 'theme_labels',
			'type'		=> 'select',
			'options'	=> array('above' => $data['text_above'], 'floating' => $data['text_floating']),
		);
		$data['settings'][] = array(
			'key'		=> 'theme_variables',
			'type'		=> 'textarea',
		);
		$data['settings'][] = array(
			'key'		=> 'theme_rules',
			'type'		=> 'textarea',
		);
		
		// Payment Page Text
		$data['settings'][] = array(
			'key'		=> 'payment_page_text',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'text_please_wait',
			'type'		=> 'multilingual_text',
			'default'	=> 'Please wait...',
		);
		$data['settings'][] = array(
			'key'		=> 'text_validating_payment_info',
			'type'		=> 'multilingual_text',
			'default'	=> 'Validating payment info...',
		);
		$data['settings'][] = array(
			'key'		=> 'text_processing_payment',
			'type'		=> 'multilingual_text',
			'default'	=> 'Processing payment...',
		);
		$data['settings'][] = array(
			'key'		=> 'text_finalizing_order',
			'type'		=> 'multilingual_text',
			'default'	=> 'Finalizing order...',
		);
		
		// Cards Page Text (Pro-specific)
		$data['settings'][] = array(
			'key'		=> 'cards_page_text',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'cards_page_heading',
			'type'		=> 'multilingual_text',
			'default'	=> 'Your Stored Cards',
		);
		$data['settings'][] = array(
			'key'		=> 'cards_page_none',
			'type'		=> 'multilingual_text',
			'default'	=> 'You have no stored cards.',
		);
		$data['settings'][] = array(
			'key'		=> 'cards_page_default_card',
			'type'		=> 'multilingual_text',
			'default'	=> 'Default Card',
		);
		$data['settings'][] = array(
			'key'		=> 'cards_page_ending_in',
			'type'		=> 'multilingual_text',
			'default'	=> 'ending in',
		);
		$data['settings'][] = array(
			'key'		=> 'cards_page_make_default',
			'type'		=> 'multilingual_text',
			'default'	=> 'Make Default',
		);
		$data['settings'][] = array(
			'key'		=> 'cards_page_delete',
			'type'		=> 'multilingual_text',
			'default'	=> 'Delete',
		);
		$data['settings'][] = array(
			'key'		=> 'cards_page_confirm',
			'type'		=> 'multilingual_text',
			'default'	=> 'This operation cannot be undone. Continue?',
		);
		$data['settings'][] = array(
			'key'		=> 'cards_page_add_card',
			'type'		=> 'multilingual_text',
			'default'	=> 'Add New Card',
		);
		$data['settings'][] = array(
			'key'		=> 'cards_page_card_name',
			'type'		=> 'multilingual_text',
			'default'	=> 'Name on Card:',
		);
		$data['settings'][] = array(
			'key'		=> 'cards_page_card_details',
			'type'		=> 'multilingual_text',
			'default'	=> 'Card Details:',
		);
		$data['settings'][] = array(
			'key'		=> 'cards_page_card_address',
			'type'		=> 'multilingual_text',
			'default'	=> 'Card Address:',
		);
		$data['settings'][] = array(
			'key'		=> 'cards_page_success',
			'type'		=> 'multilingual_text',
			'default'	=> 'Success!',
		);
		
		// Subscriptions Page Text (Pro-specific)
		$data['settings'][] = array(
			'key'		=> 'subscriptions_page_text',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'subscriptions_page_heading',
			'type'		=> 'multilingual_text',
			'default'	=> 'Your Subscriptions',
		);
		$data['settings'][] = array(
			'key'		=> 'subscriptions_page_message',
			'type'		=> 'multilingual_text',
			'default'	=> '<h4>Subscriptions will be charged using your default card. The shipping address on the subscription will be your default address, which you can change <a href="index.php?route=account/address">on this page</a>.</h4>',
		);
		$data['settings'][] = array(
			'key'		=> 'subscriptions_page_none',
			'type'		=> 'multilingual_text',
			'default'	=> 'You have no subscriptions.',
		);
		$data['settings'][] = array(
			'key'		=> 'subscriptions_page_trial',
			'type'		=> 'multilingual_text',
			'default'	=> 'Trial End:',
		);
		$data['settings'][] = array(
			'key'		=> 'subscriptions_page_last',
			'type'		=> 'multilingual_text',
			'default'	=> 'Last Invoice:',
		);
		$data['settings'][] = array(
			'key'		=> 'subscriptions_page_next',
			'type'		=> 'multilingual_text',
			'default'	=> 'Next Invoice:',
		);
		$data['settings'][] = array(
			'key'		=> 'subscriptions_page_cycles',
			'type'		=> 'multilingual_text',
			'default'	=> 'Subscription Charges Remaining:',
		);
		$data['settings'][] = array(
			'key'		=> 'subscriptions_page_charge',
			'type'		=> 'multilingual_text',
			'default'	=> 'Additional Charge:',
		);
		$data['settings'][] = array(
			'key'		=> 'subscriptions_page_pause',
			'type'		=> 'multilingual_text',
			'default'	=> 'Pause',
		);
		$data['settings'][] = array(
			'key'		=> 'subscriptions_page_weeks',
			'type'		=> 'multilingual_text',
			'default'	=> 'weeks',
		);
		$data['settings'][] = array(
			'key'		=> 'subscriptions_page_paused_until',
			'type'		=> 'multilingual_text',
			'default'	=> 'Paused Until:',
		);
		$data['settings'][] = array(
			'key'		=> 'subscriptions_page_unpause',
			'type'		=> 'multilingual_text',
			'default'	=> 'Unpause',
		);
		$data['settings'][] = array(
			'key'		=> 'subscriptions_page_cancel',
			'type'		=> 'multilingual_text',
			'default'	=> 'Cancel',
		);
		$data['settings'][] = array(
			'key'		=> 'subscriptions_page_confirm',
			'type'		=> 'multilingual_text',
			'default'	=> 'Please type CANCEL to confirm that you want to cancel this subscription.',
		);
		
		//------------------------------------------------------------------------------
		// Order Statuses
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'key'		=> 'order_statuses',
			'type'		=> 'tab',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info text-center pad-bottom-sm">' . $data['help_order_statuses'] . '</div>',
		);
		$data['settings'][] = array(
			'key'		=> 'order_statuses',
			'type'		=> 'heading',
		);
		
		$processing_status_id = $this->config->get('config_processing_status');
		$processing_status_id = $processing_status_id[0];
		
		foreach (array('initial', 'success', 'authorize', 'mismatch', 'error', 'review', 'elevated', 'highest', 'street', 'postcode', 'cvc', 'refund', 'partial') as $order_status) { // Pro-specific
			if ($order_status == 'success') {
				$default_status = ($processing_status_id) ? $processing_status_id : $this->config->get('config_order_status_id');
			} elseif ($order_status == 'initial' || $order_status == 'authorize') {
				$default_status = 1;
			} elseif ($order_status == 'error') {
				$default_status = 10;
			} else {
				$default_status = 0;
			}
			
			$data['settings'][] = array(
				'key'		=> $order_status . '_status_id',
				'type'		=> 'select',
				'options'	=> $data['order_status_array'],
				'default'	=> $default_status,
			);
		}
		
		//------------------------------------------------------------------------------
		// Restrictions
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'key'		=> 'restrictions',
			'type'		=> 'tab',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info text-center pad-bottom-sm">' . $data['help_restrictions'] . '</div>',
		);
		$data['settings'][] = array(
			'key'		=> 'restrictions',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'min_total',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 50px !important'),
			'default'	=> '0.50',
		);
		$data['settings'][] = array(
			'key'		=> 'max_total',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 50px !important'),
		);
		$data['settings'][] = array(
			'key'		=> 'stores',
			'type'		=> 'checkboxes',
			'options'	=> $data['store_array'],
			'default'	=> array_keys($data['store_array']),
		);
		$data['settings'][] = array(
			'key'		=> 'geo_zones',
			'type'		=> 'checkboxes',
			'options'	=> $data['geo_zone_array'],
			'default'	=> array_keys($data['geo_zone_array']),
		);
		$data['settings'][] = array(
			'key'		=> 'customer_groups',
			'type'		=> 'checkboxes',
			'options'	=> $data['customer_group_array'],
			'default'	=> array_keys($data['customer_group_array']),
		);
		
		// Currency Settings
		$data['settings'][] = array(
			'key'		=> 'currency_settings',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info text-center pad-bottom">' . $data['help_currency_settings'] . '</div>',
		);
		foreach ($data['currency_array'] as $code => $title) {
			$data['settings'][] = array(
				'key'		=> 'currencies_' . $code,
				'title'		=> str_replace('[currency]', $code, $data['entry_currencies']),
				'type'		=> 'select',
				'options'	=> array_merge(array(0 => $data['text_currency_disabled']), $data['currency_array']),
				'default'	=> $this->config->get('config_currency'),
			);
		}
		
		//------------------------------------------------------------------------------
		// Stripe Settings
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'key'		=> 'stripe_settings',
			'type'		=> 'tab',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info text-center pad-bottom-sm">' . $data['help_stripe_settings'] . '</div>',
		);
		
		// Connect with Stripe
		$data['settings'][] = array(
			'key'		=> 'connect_with_stripe',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'title'		=> '',
			'content'	=> '<a href="index.php?route=' . $data['extension_route'] . '/launch&token=' . $data['token'] . '">' . $data['connect_with_stripe_image'] . '</a><br><br>',
		);
		
		$manual_connection_popup = '
			<br>
			<div>If you are having connection issues, try <a style="color: #1e91cf" onclick="connectManually()">connecting manually</a>.</div>
			<div id="manual-connection-modal" class="modal fade" style="float: none">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title">Connect Manually</h4>
							<button type="button" class="close btn-close" data-dismiss="modal" data-bs-dismiss="modal"></button>
						</div>
						<div class="modal-body">
							<ol><li><a target="_blank" href="index.php?route=' . $data['extension_route'] . '/launch&mode=manual&token=' . $data['token'] . '">Click here</a> to open the Stripe connection page in a separate window.</li>
								<li>On the connection page, log into your Stripe account.</li>
								<li>After successfully logging in, you will be shown the OAuth info needed.</li>
								<li>Copy and paste each piece of info into the appropriate field below.</li>
							</ol>
							<br>
							<div id="manual-connection-fields" style="text-align: right">
								<p>Account ID: <input type="text" class="form-control" name="account_id" style="width: 340px !important" value="' . (!empty($data['saved']['account_id']) ? $data['saved']['account_id'] : '') . '" /></p>
								<p>Refresh Token: <input type="text" class="form-control" name="refresh_token" style="width: 340px !important" value="' . (!empty($data['saved']['refresh_token']) ? $data['saved']['refresh_token'] : '') . '" /></p>
								<p>Live Publishable Key: <input type="text" class="form-control" name="live_publishable_key" style="width: 340px !important" value="' . (!empty($data['saved']['live_publishable_key']) ? $data['saved']['live_publishable_key'] : '') . '" /></p>
								<p>Live Access Token: <input type="text" class="form-control" name="live_access_token" style="width: 340px !important" value="' . (!empty($data['saved']['live_access_token']) ? $data['saved']['live_access_token'] : '') . '" /></p>
								<p>Test Publishable Key: <input type="text" class="form-control" name="test_publishable_key" style="width: 340px !important" value="' . (!empty($data['saved']['test_publishable_key']) ? $data['saved']['test_publishable_key'] : '') . '" /></p>
								<p>Test Access Token: <input type="text" class="form-control" name="test_access_token" style="width: 340px !important" value="' . (!empty($data['saved']['test_access_token']) ? $data['saved']['test_access_token'] : '') . '" /></p>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default btn-light" data-dismiss="modal" data-bs-dismiss="modal"><i class="fa fa-times pad-right-sm"></i> ' . $data['button_close'] . '</button>
							<button type="button" class="btn btn-primary" onclick="saveSettings($(this)); setTimeout(function(){ location.reload(); }, 1000);"><i class="fa fa-save pad-right-sm"></i> ' . $data['button_save'] . '</button>
						</div>
					</div>
				</div>
			</div>
			<script>
				function connectManually() {
					$("#manual-connection-modal").modal("show");
				}
			</script>
		';
		
		if (!empty($data['saved']['account_id']) && !empty($data['saved']['refresh_token']) && !empty($data['saved']['live_publishable_key']) && !empty($data['saved']['live_access_token']) && !empty($data['saved']['test_publishable_key']) && !empty($data['saved']['test_access_token'])) {
			$data['settings'][] = array(
				'key'		=> 'connection_status',
				'type'		=> 'html',
				'content'	=> '<div style="color: #080; margin-top: 7px;">' . $data['text_connected'] . ' &nbsp; (' . $data['saved']['account_id'] . ')</div>' . $manual_connection_popup,
			);
		} else {
			$data['settings'][] = array(
				'key'		=> 'connection_status',
				'type'		=> 'html',
				'content'	=> '<div style="color: #C00; margin-top: 7px;">' . $data['text_not_connected'] . '</div>' . $manual_connection_popup,
			);
		}
		
		$webhook_url = str_replace('http:', 'https:', HTTP_CATALOG) . 'index.php?route=' . $data['extension_route'] . '/webhook&key=' . md5($this->config->get('config_encryption'));
		$data['settings'][] = array(
			'key'		=> 'webhook_url',
			'type'		=> 'html',
			'content'	=> '<input type="text" class="form-control" readonly="readonly" onclick="this.select()" style="background: #F5F5F5; cursor: pointer; font-family: monospace; width: 100% !important;" value="' . $webhook_url . '" />',
			'after'		=> '<br><br><div class="text-info">' . $data['help_webhook_url'] . '</div>',
		);
		
		// Stripe Settings
		$data['settings'][] = array(
			'key'		=> 'stripe_settings',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'transaction_mode',
			'type'		=> 'select',
			'options'	=> array('test' => $data['text_test'], 'live' => $data['text_live']),
		);
		$data['settings'][] = array(
			'key'		=> 'charge_mode',
			'type'		=> 'select',
			'options'	=> array('authorize' => $data['text_authorize'], 'capture' => $data['text_capture'], 'fraud' => $data['text_fraud_authorize']),
			'default'	=> 'capture',
		);
		$data['settings'][] = array(
			'key'		=> 'transaction_description',
			'type'		=> 'text',
			'default'	=> '[store]: Order #[order_id] ([email])',
		);
		$data['settings'][] = array(
			'key'		=> 'attempts',
			'type'		=> 'text',
			'default'	=> '5',
			'class'		=> 'short',
		);
		$data['settings'][] = array(
			'key'		=> 'attempts_exceeded',
			'type'		=> 'multilingual_text',
			'default'	=> 'Your card has been declined.',
		);
		$data['settings'][] = array(
			'key'		=> 'send_customer_data',
			'type'		=> 'select',
			'options'	=> array('never' => $data['text_never'], 'always' => $data['text_always']),
			'default'	=> 'never',
		);
		$data['settings'][] = array(
			'key'		=> 'advanced_error_handling',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_enabled'], 0 => $data['text_disabled']),
			'default'	=> 1,
		);
		
		// Notification Settings
		$data['settings'][] = array(
			'key'		=> 'notification_settings',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'always_send_receipts',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'decline_code_emails',
			'type'		=> 'text',
			'default'	=> $this->config->get('config_email'),
		);
		$data['settings'][] = array(
			'key'		=> 'subscription_fail_emails',
			'type'		=> 'text',
			'default'	=> $this->config->get('config_email'),
		);
		$data['settings'][] = array(
			'key'		=> 'uncaptured_emails',
			'type'		=> 'text',
			'default'	=> $this->config->get('config_email'),
		);
		$data['settings'][] = array(
			'key'		=> 'uncaptured_check',
			'type'		=> 'hidden',
		);
		
		//------------------------------------------------------------------------------
		// Checkout Options
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'key'		=> 'checkout_options',
			'type'		=> 'tab',
		);
		
		// Pop-up Payment Form
		$data['settings'][] = array(
			'key'		=> 'popup_payment_form',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="alert alert-info">' . $data['help_popup_payment_form'] . '</div>',
		);
		$data['settings'][] = array(
			'key'		=> 'use_popup',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'popup_button_text',
			'type'		=> 'multilingual_text',
			'default'	=> 'Proceed to Payment',
		);
		
		// Stripe Checkout
		$data['settings'][] = array(
			'key'		=> 'stripe_checkout',
			'type'		=> 'heading',
			'attributes'=> array('style' => 'margin-top: 75px !important'),
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="alert alert-info">' . $data['help_stripe_checkout_info'] . '</div>',
		);
		$data['settings'][] = array(
			'key'		=> 'checkout',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'checkout_billing_address',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'checkout_phone_number',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'checkout_total',
			'type'		=> 'multilingual_text',
			'default'	=> 'Total',
		);
		$data['settings'][] = array(
			'key'		=> 'checkout_no_order_id',
			'type'		=> 'multilingual_text',
			'default'	=> 'Please fill in all required fields before attempting payment.',
		);
		
		$webhooks_html = '';
		$stores = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY store_id ASC")->rows;
		
		foreach ($stores as $store) {
			$webhook_url = str_replace('http:', 'https:', $store['url']) . 'index.php?route=' . $data['extension_route'] . '/webhook&key=' . md5($this->config->get('config_encryption'));
			$webhooks_html .= '<input type="text" class="form-control" readonly="readonly" onclick="this.select()" style="background: #F5F5F5; cursor: pointer; font-family: monospace; width: 100% !important;" value="' . $webhook_url . '" /><br><br>';
		}
		
		if ($webhooks_html) {
			$data['settings'][] = array(
				'key'		=> 'additional_webhook_urls',
				'type'		=> 'html',
				'content'	=> $webhooks_html,
			);
		}
		
		// Express Checkout Button
		$data['settings'][] = array(
			'key'		=> 'express_checkout_button',
			'type'		=> 'heading',
			'attributes'=> array('style' => 'margin-top: 75px !important'),
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="alert alert-info">' . $data['help_express_checkout_button'] . '</div>',
		);
		
		$express_payment_methods = array(
			'amazon_pay'	=> $data['text_amazon_pay'],
			'apple_pay'		=> $data['text_apple_pay'],
			'google_pay'	=> $data['text_google_pay'],
			'klarna'		=> $data['text_klarna'],
			'link'			=> $data['text_stripe_link'],
		);
		
		$data['settings'][] = array(
			'key'		=> 'express_payment_methods',
			'type'		=> 'checkboxes',
			'options'	=> $express_payment_methods,
			'default'	=> array_keys($express_payment_methods),
		);
		
		$data['settings'][] = array(
			'key'		=> 'express_alignment',
			'type'		=> 'select',
			'options'	=> array('left' => $data['text_left'], 'right' => $data['text_right']),
			'default'	=> 'right',
		);
		$data['settings'][] = array(
			'key'		=> 'express_button_height',
			'type'		=> 'text',
			'class'		=> 'short',
		);
		$data['settings'][] = array(
			'key'		=> 'express_width',
			'type'		=> 'text',
			'class'		=> 'short',
		);
		$data['settings'][] = array(
			'key'		=> 'express_margins',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 150px !important'),
			'default'	=> '15px 0px 15px 0px',
		);
		$data['settings'][] = array(
			'key'		=> 'express_max_columns',
			'type'		=> 'text',
			'class'		=> 'short',
		);
		$data['settings'][] = array(
			'key'		=> 'express_max_rows',
			'type'		=> 'text',
			'class'		=> 'short',
		);
		$data['settings'][] = array(
			'key'		=> 'express_autocreate_account',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'express_autologin',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		
		// Quick Buy Button
		$data['settings'][] = array(
			'key'		=> 'quick_buy_button',
			'type'		=> 'heading',
			'attributes'=> array('style' => 'margin-top: 75px !important'),
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="alert alert-info">' . $data['help_quick_buy_button'] . '</div>',
		);
		$data['settings'][] = array(
			'key'		=> 'quick_buy_html',
			'type'		=> 'textarea',
			'default'	=> '<div class=&quot;text-right&quot;>' . "\n" . '<a class=&quot;btn btn-success&quot; style=&quot;margin-top: 15px&quot;>[button_text]</a>' . "\n" . '</div>',
		);
		$data['settings'][] = array(
			'key'		=> 'quick_buy_text',
			'type'		=> 'multilingual_text',
			'default'	=> 'Quick Buy',
		);
		$data['settings'][] = array(
			'key'		=> 'quick_buy_address_text',
			'type'		=> 'multilingual_text',
			'default'	=> 'Enter Your Shipping Address',
		);
		$data['settings'][] = array(
			'key'		=> 'quick_buy_shipping_text',
			'type'		=> 'multilingual_text',
			'default'	=> 'Choose Your Shipping Method',
		);
		$data['settings'][] = array(
			'key'		=> 'quick_buy_geozone_error',
			'type'		=> 'multilingual_text',
			'default'	=> 'Sorry, this payment option is not available for your location.',
		);
		
		//------------------------------------------------------------------------------
		// Other Payment Methods
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'key'		=> 'other_payment_methods',
			'type'		=> 'tab',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info text-center pad-bottom-sm">' . $data['help_other_payment_methods'] . '</div>',
		);
		$data['settings'][] = array(
			'key'		=> 'other_payment_methods',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'payment_methods_setup',
			'type'		=> 'html',
			'content'	=> $data['help_payment_methods_setup'],
		);
		$data['settings'][] = array(
			'key'		=> 'applepay',
			'type'		=> 'html',
			'content'	=> $data['text_applepay'] . '
				<br><br>
				<a class="btn btn-primary" onclick="connectApplePay($(this))">' . $data['button_reconnect_apple_pay'] . '</a>
				<script>
					function connectApplePay(element) {
						$.ajax({
							url: "index.php?route=' . $data['extension_route'] . '/applepay&token=' . $data['token'] . '",
							beforeSend: function() {
								element.attr("disabled", "disabled").html("' . $data['standard_please_wait'] . '");
							},
							success: function(data) {
								console.log(data);
								alert(data);
							},
							complete: function() {
								element.removeAttr("disabled").removeClass("disabled").html("' . $data['button_reconnect_apple_pay'] . '");
							},
							error: function(xhr, status, error) {
								alert(xhr.responseText ? xhr.responseText : error);
							},
						});
					}
				</script>
			',
		);
		$data['settings'][] = array(
			'key'		=> 'buy_now_pay_later_messaging',
			'type'		=> 'html',
			'content'	=> $data['help_buy_now_pay_later_messaging'],
		);
		$data['settings'][] = array(
			'key'		=> 'delayed_payment_emails',
			'type'		=> 'text',
			'default'	=> $this->config->get('config_email'),
		);
		$data['settings'][] = array(
			'key'		=> 'override_extension_title',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'error_page',
			'type'		=> 'multilingual_textarea',
			'attributes'=> array('style' => 'font-family: monospace; height: 180px; width: 600px !important'),
			'default'	=> '
[header]
<div class="container" style="font-size: 18px; min-height: 600px; text-align: center;">
	<div style="color: red; margin: 20px">
		<b>Error:</b> [error]
	</div>
	<a href="' . str_replace('http://', 'https://', HTTP_CATALOG) . 'index.php?route=checkout/checkout">
		Return to checkout
	</a>
</div>
[footer]
			',
		);
		
		//------------------------------------------------------------------------------
		// Subscription Products
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'key'		=> 'subscription_products',
			'type'		=> 'tab',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info pad-left pad-bottom-sm">' . $data['help_subscription_products'] . '</div>',
		);
		$data['settings'][] = array(
			'key'		=> 'subscription_products',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'subscriptions',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'subscription_order_report',
			'type'		=> 'html',
			'content'	=> '<a class="btn btn-primary" href="index.php?route=' . $data['extension_route'] . '/subscriptionOrderReport&token=' . $data['token'] . '">' . $data['button_view_report'] . '</a>',
		);
		$data['settings'][] = array(
			'key'		=> 'text_to_be_charged',
			'type'		=> 'multilingual_text',
			'default'	=> 'To Be Charged Later',
		);
		$data['settings'][] = array(
			'key'		=> 'prevent_guests',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'text_customer_required',
			'type'		=> 'multilingual_text',
			'default'	=> 'Error: You must create a customer account to purchase a subscription product.',
		);
		$data['settings'][] = array(
			'key'		=> 'order_address',
			'type'		=> 'select',
			'options'	=> array('stripe' => $data['text_stripe_address'], 'opencart' => $data['text_opencart_address'], 'original' => $data['text_original_address']),
			'default'	=> 'original',
		);
		
		// Advanced Subscription Settings
		$data['settings'][] = array(
			'key'		=> 'advanced_subscription_settings',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'include_shipping',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'merge_subscriptions',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'manage_subscriptions',
			'type'		=> 'select',
			'options'	=> array(
				''					=> $data['text_no'],
				'cancel'			=> $data['text_cancel_only'],
				'pause'				=> $data['text_pause_only'],
				'cancel_and_pause'	=> $data['text_cancel_and_pause'],
				'customer_portal'	=> $data['text_use_customer_portal'],
			),
			'default'	=> '',
		);
		$data['settings'][] = array(
			'key'		=> 'pause_options',
			'type'		=> 'text',
			'class'		=> 'medium',
		);
		$data['settings'][] = array(
			'key'		=> 'transfer_subscriptions',
			'type'		=> 'html',
			'content'	=> '
				<a class="btn btn-primary" onclick="$(this).hide().next().show()">' . $data['button_transfer_subscriptions'] . '</a>
				<div id="transfer-subscriptions" style="display: none">
					' . $data['text_old_pricing_plan_id'] . ' &nbsp; <input type="text" id="old-plan" class="form-control pad-bottom-sm" /><br>
					' . $data['text_new_pricing_plan_id'] . ' <input type="text" id="new-plan" class="form-control pad-bottom-sm" /><br>
					<a class="btn btn-primary" onclick="transferSubscriptions($(this))">' . $data['button_transfer'] . '</a>
				</div>
				<script>
					function transferSubscriptions(element) {
						if (!confirm("' . $data['standard_confirm'] . '")) {
							return;
						}
						$.ajax({
							type: "POST",
							url: "index.php?route=' . $data['extension_route'] . '/transferSubscriptions&token=' . $data['token'] . '",
							data: {old: $("#old-plan").val(), new: $("#new-plan").val()},
							beforeSend: function() {
								element.attr("disabled", "disabled").html("' . $data['standard_please_wait'] . '");
							},
							success: function(data) {
								console.log(data);
								alert(data);
							},
							complete: function() {
								element.removeAttr("disabled").removeClass("disabled").html("' . $data['button_transfer'] . '");
							},
							error: function(xhr, status, error) {
								alert(xhr.responseText ? xhr.responseText : error);
							},
						});
					}
				</script>
			',
		);
		
		// Current Subscription Products
		$data['settings'][] = array(
			'key'		=> 'current_subscriptions',
			'type'		=> 'heading',
		);
		$subscription_products_table = '
			<style type="text/css">
				#current-subscription-products {
					user-select: text;
				}
			</style>
			<div class="form-group row">
				<label class="control-label col-sm-3 text-end">' . str_replace('[transaction_mode]', ucwords(isset($data['saved']['transaction_mode']) ? $data['saved']['transaction_mode'] : 'test'), $data['entry_current_subscriptions']) . '</label>
				<div class="col-sm-9">
					<table id="current-subscription-products" class="table table-stripe table-bordered">
						<thead>
							<tr>
								<td colspan="3" style="text-align: center">' . $data['text_thead_opencart'] . '</td>
								<td colspan="3" style="text-align: center">' . $data['text_thead_stripe'] . '</td>
							</tr>
							<tr>
								<td class="left">' . $data['text_product_name'] . '</td>
								<td class="left">' . $data['text_product_price'] . '</td>
								<td class="left">' . $data['text_location_plan_id'] . '</td>
								<td class="left">' . $data['text_plan_name'] . '</td>
								<td class="left">' . $data['text_plan_interval'] . '</td>
								<td class="left">' . $data['text_plan_charge'] . '</td>
							</tr>
						</thead>
		';
		if (empty($data['subscription_products'])) {
			$subscription_products_table .= '
				<tr><td class="center" colspan="6">' . $data['text_no_subscription_products'] . '</td></tr>
				<tr><td class="center" colspan="6">' . $data['text_create_one_by_entering'] . '</td></tr>
			';
		}
		foreach ($data['subscription_products'] as $product) {
			$highlight = ($product['price'] == $product['charge']) ? '' : 'style="background: #FDD"';
			$subscription_products_table .= '
				<tr>
					<td class="left"><a target="_blank" href="index.php?route=catalog/product/edit&amp;product_id=' . $product['product_id'] . '&amp;token=' . $data['token'] . '">' . $product['name'] . '</a></td>
					<td class="left" ' . $highlight . '>' . $product['price'] . '</td>
					<td class="left">' . $product['location'] . '</td>
					<td class="left">' . $product['plan'] . '</td>
					<td class="left">' . $product['interval'] . '</td>
					<td class="left" ' . $highlight . '>' . $product['charge'] . '</td>
				</tr>
			';
		}
		$subscription_products_table .= '</table></div></div><br>';
		
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> $subscription_products_table,
		);
		
		// Map Options to Subscriptions (Pro-specific)
		$data['settings'][] = array(
			'key'		=> 'map_options',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info">' . $data['help_map_options'] . '</div>',
		);
		
		$table = 'subscription_options';
		$sortby = 'option_name';
		$data['settings'][] = array(
			'key'		=> $table,
			'type'		=> 'table_start',
			'columns'	=> array('action', 'option_name', 'option_value', 'plan_id', 'currency', 'start_date', 'cycles'),
		);
		foreach ($this->getTableRowNumbers($data, $table, $sortby) as $num => $rules) {
			$prefix = $table . '_' . $num . '_';
			$data['settings'][] = array(
				'type'		=> 'row_start',
			);
			$data['settings'][] = array(
				'key'		=> 'delete',
				'type'		=> 'button',
			);
			$data['settings'][] = array(
				'type'		=> 'column',
			);
			$data['settings'][] = array(
				'key'		=> $prefix . 'option_name',
				'type'		=> 'text',
			);
			$data['settings'][] = array(
				'type'		=> 'column',
			);
			$data['settings'][] = array(
				'key'		=> $prefix . 'option_value',
				'type'		=> 'text',
			);
			$data['settings'][] = array(
				'type'		=> 'column',
			);
			$data['settings'][] = array(
				'key'		=> $prefix . 'plan_id',
				'type'		=> 'text',
			);
			$data['settings'][] = array(
				'type'		=> 'column',
			);
			$data['settings'][] = array(
				'key'		=> $prefix . 'currency',
				'type'		=> 'select',
				'options'	=> array_merge(array(0 => $data['text_all']), $data['currency_array']),
			);
			$data['settings'][] = array(
				'type'		=> 'column',
			);
			$data['settings'][] = array(
				'key'		=> $prefix . 'start_date',
				'type'		=> 'text',
				'attributes'=> array('placeholder' => 'YYYY-MM-DD', 'style' => 'width: 100px !important', 'maxlength' => '10'),
			);
			$data['settings'][] = array(
				'type'		=> 'column',
			);
			$data['settings'][] = array(
				'key'		=> $prefix . 'cycles',
				'type'		=> 'text',
				'attributes'=> array('style' => 'width: 50px !important'),
			);
			$data['settings'][] = array(
				'type'		=> 'row_end',
			);
		}
		$data['settings'][] = array(
			'type'		=> 'table_end',
			'buttons'	=> 'add_row',
			'text'		=> 'button_add_mapping',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<br />',
		);
		
		// Map Recurring Profiles to Subscriptions (Pro-specific)
		$data['settings'][] = array(
			'key'		=> 'map_recurring_profiles',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info">' . $data['help_map_recurring_profiles'] . '</div>',
		);
		
		$subscriptions = array(0 => $data['standard_select']);
		
		if (version_compare(VERSION, '1.5.6', '<')) {
			// profiles do not exist
		} elseif (version_compare(VERSION, '2.0', '<')) {
			$this->load->model('catalog/profile');
			foreach ($this->model_catalog_profile->getProfiles() as $profile) {
				$subscriptions[$profile['profile_id']] = $profile['name'];
			}
		} elseif (version_compare(VERSION, '4.0', '<')) {
			$this->load->model('catalog/recurring');
			foreach ($this->model_catalog_recurring->getRecurrings() as $recurring) {
				$subscriptions[$recurring['recurring_id']] = $recurring['name'];
			}
		} else {
			$this->load->model('catalog/subscription_plan');
			foreach ($this->model_catalog_subscription_plan->getSubscriptionPlans() as $subscription_plan) {
				$subscriptions[$subscription_plan['subscription_plan_id']] = $subscription_plan['name'];
			}
		}
		
		$table = 'subscription_profiles';
		$sortby = 'profile_name';
		$data['settings'][] = array(
			'key'		=> $table,
			'type'		=> 'table_start',
			'columns'	=> array('action', 'profile_name', 'recurring_or_subscription', 'plan_id', 'currency', 'start_date', 'cycles'),
		);
		foreach ($this->getTableRowNumbers($data, $table, $sortby) as $num => $rules) {
			$prefix = $table . '_' . $num . '_';
			
			$default_id = 0;
			$profile_name = (!empty($data['saved'][$prefix . 'profile_name'])) ? $data['saved'][$prefix . 'profile_name'] : '';
			
			if (version_compare(VERSION, '1.5.6', '<')) {
				continue;
			} elseif (version_compare(VERSION, '2.0', '<')) {
				$profile_recurring_subscription = 'profile';
			} elseif (version_compare(VERSION, '4.0', '<')) {
				$profile_recurring_subscription = 'recurring';
			} else {
				$profile_recurring_subscription = 'subscription_plan';
			}
			
			$subscription_query = $this->db->query("SELECT * FROM " . DB_PREFIX . $profile_recurring_subscription . "_description WHERE `name` = '" . $this->db->escape($profile_name) . "'");
			if ($subscription_query->num_rows) {
				$default_id = $subscription_query->row[$profile_recurring_subscription . '_id'];
			}
			
			$data['settings'][] = array(
				'type'		=> 'row_start',
			);
			$data['settings'][] = array(
				'key'		=> 'delete',
				'type'		=> 'button',
			);
			$data['settings'][] = array(
				'type'		=> 'column',
			);
			$data['settings'][] = array(
				'key'		=> $prefix . 'profile_name',
				'type'		=> 'text',
			);
			$data['settings'][] = array(
				'type'		=> 'column',
			);
			$data['settings'][] = array(
				'key'		=> $prefix . 'recurring_or_subscription_id',
				'type'		=> 'select',
				'options'	=> $subscriptions,
				'default'	=> $default_id,
			);
			$data['settings'][] = array(
				'type'		=> 'column',
			);
			$data['settings'][] = array(
				'key'		=> $prefix . 'plan_id',
				'type'		=> 'text',
			);
			$data['settings'][] = array(
				'type'		=> 'column',
			);
			$data['settings'][] = array(
				'key'		=> $prefix . 'currency',
				'type'		=> 'select',
				'options'	=> array_merge(array(0 => $data['text_all']), $data['currency_array']),
			);
			$data['settings'][] = array(
				'type'		=> 'column',
			);
			$data['settings'][] = array(
				'key'		=> $prefix . 'start_date',
				'type'		=> 'text',
				'attributes'=> array('placeholder' => 'YYYY-MM-DD', 'style' => 'width: 100px !important', 'maxlength' => '10'),
			);
			$data['settings'][] = array(
				'type'		=> 'column',
			);
			$data['settings'][] = array(
				'key'		=> $prefix . 'cycles',
				'type'		=> 'text',
				'attributes'=> array('style' => 'width: 50px !important'),
			);
			$data['settings'][] = array(
				'type'		=> 'row_end',
			);
		}
		$data['settings'][] = array(
			'type'		=> 'table_end',
			'buttons'	=> 'add_row',
			'text'		=> 'button_add_mapping',
		);
		
		//------------------------------------------------------------------------------
		// Create a Charge
		//------------------------------------------------------------------------------
		// Pro-specific
		$data['settings'][] = array(
			'key'		=> 'create_a_charge',
			'type'		=> 'tab',
		);
		
		$settings = $data['saved'];
		$language = $this->config->get('config_language');
		
		ob_start();
		if (version_compare(VERSION, '4.0', '<')) {
			$filepath = DIR_APPLICATION . 'view/template/extension/' . $this->type . '/' . $this->name . '_card_form.twig';
			include_once(class_exists('VQMod') ? \VQMod::modCheck(modification($filepath)) : modification($filepath));
		} elseif (defined('DIR_EXTENSION')) {
			$filepath = DIR_EXTENSION . $this->name . '/admin/view/template/' . $this->type . '/' . $this->name . '_card_form.twig';
			include_once(class_exists('VQMod') ? \VQMod::modCheck($filepath) : $filepath);
		}
		$template_contents = ob_get_clean();
		
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> $template_contents,
		);
		
		//------------------------------------------------------------------------------
		// end settings
		//------------------------------------------------------------------------------
		
		$this->document->setTitle($data['heading_title']);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		if (version_compare(VERSION, '4.0', '<')) {
			$template_file = DIR_TEMPLATE . 'extension/' . $this->type . '/' . $this->name . '.twig';
		} elseif (defined('DIR_EXTENSION')) {
			$template_file = DIR_EXTENSION . $this->name . '/admin/view/template/' . $this->type . '/' . $this->name . '.twig';
		}
		
		if (is_file($template_file)) {
			extract($data);
			
			ob_start();
			if (version_compare(VERSION, '4.0', '<')) {
				require(class_exists('VQMod') ? \VQMod::modCheck(modification($template_file)) : modification($template_file));
			} else {
				require(class_exists('VQMod') ? \VQMod::modCheck($template_file) : $template_file);
			}
			$output = ob_get_clean();
			
			if (version_compare(VERSION, '3.0', '>=')) {
				$output = str_replace(array('&token=', '&amp;token='), '&user_token=', $output);
			}
			
			if (version_compare(VERSION, '4.0', '>=')) {
				$separator = (version_compare(VERSION, '4.0.2.0', '<')) ? '|' : '.';
				$output = str_replace($data['extension_route'] . '/', $data['extension_route'] . $separator, $output);
			}
			
			echo $output;
		} else {
			echo 'Error loading template file: ' . $template_file;
		}
	}
	
	//==============================================================================
	// Helper functions
	//==============================================================================
	private function hasPermission($permission) {
		if (version_compare(VERSION, '2.3', '<')) {
			return $this->user->hasPermission($permission, $this->type . '/' . $this->name);
		} elseif (version_compare(VERSION, '4.0', '<')) {
			return $this->user->hasPermission($permission, 'extension/' . $this->type . '/' . $this->name);
		} else {
			return $this->user->hasPermission($permission, 'extension/' . $this->name . '/' . $this->type . '/' . $this->name);
		}
	}
	
	private function loadLanguage($path) {
		$_ = array();
		$language = array();
		if (version_compare(VERSION, '2.2', '<')) {
			$admin_language = $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE `code` = '" . $this->db->escape($this->config->get('config_admin_language')) . "'")->row['directory'];
		} elseif (version_compare(VERSION, '4.0', '<')) {
			$admin_language = $this->config->get('config_admin_language');
		} else {
			$admin_language = $this->config->get('config_language_admin');
		}
		foreach (array('english', 'en-gb', $admin_language) as $directory) {
			$file = DIR_LANGUAGE . $directory . '/' . $directory . '.php';
			if (file_exists($file)) require(class_exists('VQMod') ? \VQMod::modCheck($file) : $file);
			$file = DIR_LANGUAGE . $directory . '/default.php';
			if (file_exists($file)) require(class_exists('VQMod') ? \VQMod::modCheck($file) : $file);
			$file = DIR_LANGUAGE . $directory . '/' . $path . '.php';
			if (file_exists($file)) require(class_exists('VQMod') ? \VQMod::modCheck($file) : $file);
			$file = DIR_LANGUAGE . $directory . '/extension/' . $path . '.php';
			if (file_exists($file)) require(class_exists('VQMod') ? \VQMod::modCheck($file) : $file);
			if (defined('DIR_EXTENSION')) {
				$file = DIR_EXTENSION . 'opencart/admin/language/' . $directory . '/' . $path . '.php';
				if (file_exists($file)) require(class_exists('VQMod') ? \VQMod::modCheck($file) : $file);
				$explode = explode('/', $path);
				$file = DIR_EXTENSION . $explode[1] . '/admin/language/' . $directory . '/' . $path . '.php';
				if (file_exists($file)) require(class_exists('VQMod') ? \VQMod::modCheck($file) : $file);
				$file = DIR_EXTENSION . $this->name . '/admin/language/' . $directory . '/' . $path . '.php';
				if (file_exists($file)) require(class_exists('VQMod') ? \VQMod::modCheck($file) : $file);
			}
			$language = array_merge($language, $_);
		}
		return $language;
	}
	
	private function getTableRowNumbers(&$data, $table, $sorting) {
		$groups = array();
		$rules = array();
		
		foreach ($data['saved'] as $key => $setting) {
			if (preg_match('/' . $table . '_(\d+)_' . $sorting . '/', $key, $matches)) {
				$groups[$setting][] = $matches[1];
			}
			if (preg_match('/' . $table . '_(\d+)_rule_(\d+)_type/', $key, $matches)) {
				$rules[$matches[1]][$setting . $matches[2]] = $matches[2];
			}
		}
		
		if (empty($groups)) $groups = array('' => array('1'));
		ksort($groups, defined('SORT_NATURAL') ? SORT_NATURAL : SORT_REGULAR);
		
		foreach ($rules as $key => $rule) {
			ksort($rules[$key], defined('SORT_NATURAL') ? SORT_NATURAL : SORT_REGULAR);
		}
		
		$data['used_rows'][$table] = array();
		$rows = array();
		foreach ($groups as $group) {
			foreach ($group as $num) {
				$data['used_rows'][preg_replace('/module_(\d+)_/', '', $table)][] = $num;
				$rows[$num] = (empty($rules[$num])) ? array() : $rules[$num];
			}
		}
		sort($data['used_rows'][$table]);
		
		return $rows;
	}
	
	//==============================================================================
	// loadSettings()
	//==============================================================================
	private $encryption_key = '';
	
	public function loadSettings(&$data) {
		$backup_type = (empty($data)) ? 'manual' : 'auto';
		if ($backup_type == 'manual' && !$this->hasPermission('modify')) {
			return;
		}
		
		$this->cache->delete($this->name);
		unset($this->session->data[$this->name]);
		$code = (version_compare(VERSION, '3.0', '<') ? '' : $this->type . '_') . $this->name;
		
		// Set URL data
		$data['token'] = $this->session->data[version_compare(VERSION, '3.0', '<') ? 'token' : 'user_token'];
		$data['exit'] = $this->url->link((version_compare(VERSION, '3.0', '<') ? 'extension' : 'marketplace') . '/' . (version_compare(VERSION, '2.3', '<') ? '' : 'extension&type=') . $this->type . '&token=' . $data['token'], '', 'SSL');
		$data['extension_route'] = 'extension/' . (version_compare(VERSION, '4.0', '<') ? '' : $this->name . '/') . $this->type . '/' . $this->name;
		
		// Load saved settings
		$data['saved'] = array();
		$settings_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `key` ASC");
		
		foreach ($settings_query->rows as $setting) {
			$key = str_replace($code . '_', '', $setting['key']);
			$value = $setting['value'];
			if ($setting['serialized']) {
				$value = (version_compare(VERSION, '2.1', '<')) ? unserialize($setting['value']) : json_decode($setting['value'], true);
			}
			
			$data['saved'][$key] = $value;
			
			if (is_array($value)) {
				foreach ($value as $num => $value_array) {
					foreach ($value_array as $k => $v) {
						$data['saved'][$key . '_' . $num . '_' . $k] = $v;
					}
				}
			}
		}
		
		// Load language and run standard checks
		$data = array_merge($data, $this->loadLanguage($this->type . '/' . $this->name));
		
		if (ini_get('max_input_vars') && ((ini_get('max_input_vars') - count($data['saved'])) < 50)) {
			$data['warning'] = $data['standard_max_input_vars'];
		}
		
		// Modify files according to OpenCart version
		if ($this->type == 'total') {
			if (version_compare(VERSION, '2.2', '<')) {
				$filepath = DIR_CATALOG . 'model/' . $this->type . '/' . $this->name . '.php';
				file_put_contents($filepath, str_replace('public function getTotal($total) {', 'public function getTotal(&$total_data, &$order_total, &$taxes) {' . "\n\t\t" . '$total = array("totals" => &$total_data, "total" => &$order_total, "taxes" => &$taxes);', file_get_contents($filepath)));
			} elseif (defined('DIR_EXTENSION')) {
				$filepath = DIR_EXTENSION . $this->name . '/catalog/model/' . $this->type . '/' . $this->name . '.php';
				file_put_contents($filepath, str_replace('public function getTotal($total_input) {', 'public function getTotal(&$total_data, &$taxes, &$order_total) {', file_get_contents($filepath)));
			}
		}
		
		if (version_compare(VERSION, '2.3', '>=')) {
			$filepaths = array(
				DIR_APPLICATION . 'controller/' . $this->type . '/' . $this->name . '.php',
				DIR_CATALOG . 'controller/' . $this->type . '/' . $this->name . '.php',
				DIR_CATALOG . 'model/' . $this->type . '/' . $this->name . '.php',
			);
			foreach ($filepaths as $filepath) {
				if (file_exists($filepath)) {
					rename($filepath, str_replace('.php', '.php-OLD', $filepath));
				}
			}
		}
		
		if (version_compare(VERSION, '4.0', '>=')) {
			$extension_install_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension_install WHERE `code` = '" . $this->db->escape($this->name) . "'");
			if ($extension_install_query->row['version'] == 'unlicensed') {
				$this->db->query("UPDATE " . DB_PREFIX . "extension_install SET version = '" . $this->db->escape($data['version']) . "' WHERE `code` = '" . $this->db->escape($this->name) . "'");
			}
		}
		
		// Set save type and skip auto-backup if not needed
		if (!empty($data['saved']['autosave'])) {
			$data['save_type'] = 'auto';
		}
		
		if ($backup_type == 'auto' && empty($data['autobackup'])) {
			return;
		}
		
		// Create settings auto-backup file
		$manual_filepath = DIR_LOGS . $this->name . $this->encryption_key . '.backup';
		$auto_filepath = DIR_LOGS . $this->name . $this->encryption_key . '.autobackup';
		$filepath = ($backup_type == 'auto') ? $auto_filepath : $manual_filepath;
		if (file_exists($filepath)) unlink($filepath);
		
		file_put_contents($filepath, 'SETTING	NUMBER	SUB-SETTING	SUB-NUMBER	SUB-SUB-SETTING	VALUE' . "\n", FILE_APPEND|LOCK_EX);
		
		foreach ($data['saved'] as $key => $value) {
			if (is_array($value)) continue;
			
			$parts = explode('|', preg_replace(array('/_(\d+)_/', '/_(\d+)/'), array('|$1|', '|$1'), $key));
			
			$line = '';
			for ($i = 0; $i < 5; $i++) {
				$line .= (isset($parts[$i]) ? $parts[$i] : '') . "\t";
			}
			$line .= str_replace(array("\t", "\n"), array('    ', '\n'), $value) . "\n";
			
			file_put_contents($filepath, $line, FILE_APPEND|LOCK_EX);
		}
		
		$data['autobackup_time'] = date('Y-M-d @ g:i a');
		$data['backup_time'] = (file_exists($manual_filepath)) ? date('Y-M-d @ g:i a', filemtime($manual_filepath)) : '';
		
		if ($backup_type == 'manual') {
			echo $data['autobackup_time'];
		}
	}
	
	//==============================================================================
	// saveSettings()
	//==============================================================================
	public function saveSettings() {
		if (!$this->hasPermission('modify')) {
			echo 'PermissionError';
			return;
		}
		
		$this->cache->delete($this->name);
		unset($this->session->data[$this->name]);
		$code = (version_compare(VERSION, '3.0', '<') ? '' : $this->type . '_') . $this->name;
		
		if ($this->request->get['saving'] == 'manual') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($code) . "' AND `key` != '" . $this->db->escape($this->name . '_module') . "'");
		}
		
		$module_id = 0;
		$modules = array();
		$module_instance = false;
		
		foreach ($this->request->post as $key => $value) {
			if (strpos($key, 'module_') === 0) {
				$parts = explode('_', $key, 3);
				$module_id = $parts[1];
				$modules[$parts[1]][$parts[2]] = $value;
				if ($parts[2] == 'module_id') $module_instance = true;
			} else {
				$key = (version_compare(VERSION, '3.0', '<') ? '' : $this->type . '_') . $this->name . '_' . $key;
				
				if ($this->request->get['saving'] == 'auto') {
					$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($code) . "' AND `key` = '" . $this->db->escape($key) . "'");
				}
				
				$this->db->query("
					INSERT INTO " . DB_PREFIX . "setting SET
					`store_id` = 0,
					`code` = '" . $this->db->escape($code) . "',
					`key` = '" . $this->db->escape($key) . "',
					`value` = '" . $this->db->escape(stripslashes(is_array($value) ? implode(';', $value) : $value)) . "',
					`serialized` = 0
				");
			}
		}
		
		foreach ($modules as $module_id => $module) {
			$module_code = (version_compare(VERSION, '4.0', '<')) ? $this->name : $this->name . '.' . $this->name;
			if (!$module_id) {
				$this->db->query("
					INSERT INTO " . DB_PREFIX . "module SET
					`name` = '" . $this->db->escape($module['name']) . "',
					`code` = '" . $this->db->escape($module_code) . "',
					`setting` = ''
				");
				$module_id = $this->db->getLastId();
				$module['module_id'] = $module_id;
			}
			$module_settings = (version_compare(VERSION, '2.1', '<')) ? serialize($module) : json_encode($module);
			$this->db->query("
				UPDATE " . DB_PREFIX . "module SET
				`name` = '" . $this->db->escape($module['name']) . "',
				`code` = '" . $this->db->escape($module_code) . "',
				`setting` = '" . $this->db->escape($module_settings) . "'
				WHERE module_id = " . (int)$module_id . "
			");
		}
	}
	
	//==============================================================================
	// deleteSetting()
	//==============================================================================
	public function deleteSetting() {
		if (!$this->hasPermission('modify')) {
			echo 'PermissionError';
			return;
		}
		$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : $this->type . '_';
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($prefix . $this->name) . "' AND `key` = '" . $this->db->escape($prefix . $this->name . '_' . str_replace('[]', '', $this->request->get['setting'])) . "'");
	}
	
	//==============================================================================
	// checkVersion()
	//==============================================================================
	public function checkVersion() {
		$data = $this->loadLanguage($this->type . '/' . $this->name);
		
		$curl = curl_init('https://www.getclearthinking.com/downloads/checkVersion?extension=' . urlencode($data['heading_title']));
		curl_setopt_array($curl, array(
			CURLOPT_CONNECTTIMEOUT	=> 10,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_TIMEOUT			=> 10,
		));
		$response = curl_exec($curl);
		curl_close($curl);
		
		echo $response;
	}
	
	//==============================================================================
	// update()
	//==============================================================================
	public function update() {
		$data = $this->loadLanguage($this->type . '/' . $this->name);
		
		$curl = curl_init('https://www.getclearthinking.com/downloads/update?extension=' . urlencode($data['heading_title']) . '&domain=' . $this->request->server['HTTP_HOST'] . '&key=' . $this->request->post['license_key']);
		curl_setopt_array($curl, array(
			CURLOPT_CONNECTTIMEOUT	=> 10,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_TIMEOUT			=> 10,
		));
		$response = curl_exec($curl);
		curl_close($curl);
		
		if (strpos($response, '<i') === 0) {
			echo $response;
			return;
		}
		
		$first_zip = DIR_DOWNLOAD . 'clearthinking.zip';
		$file = fopen($first_zip, 'w+');
		fwrite($file, $response);
		fclose($file);
		
		$temp_directory = DIR_DOWNLOAD . 'clearthinking/';
		$zip = new \ZipArchive();

		if ($zip->open($first_zip) === true) {
			$zip->extractTo($temp_directory);
			$zip->close();
		} else {
			echo 'Invalid zip archive';
			return;
		}
		
		@unlink($first_zip);
		
		if (version_compare(VERSION, '2.0', '<')) {
			$second_zip = $temp_directory . 'OpenCart 1.5 Versions.zip';
		} elseif (version_compare(VERSION, '2.3', '<')) {
			$second_zip = $temp_directory . 'OpenCart 2.0-2.2 Versions.ocmod.zip';
		} elseif (version_compare(VERSION, '4.0', '<')) {
			$second_zip = $temp_directory . 'OpenCart 2.3-3.0 Versions.ocmod.zip';
		} else {
			$second_zip = $temp_directory . 'OpenCart 4 Versions.zip';
		}
		
		$zip = new \ZipArchive();
		
		if (version_compare(VERSION, '4.0', '<')) {
			if ($zip->open($second_zip) === true) {
				$admin_directory = basename(DIR_APPLICATION);
				
				for ($i = 0; $i < $zip->numFiles; $i++) {
					$filepath = str_replace(array('upload/', 'admin/'), array('', $admin_directory . '/'), $zip->getNameIndex($i));
					
					if (strpos($filepath, '.txt')) {
						continue;
					}
					
					if ($filepath === 'install.xml') {
						$xml = $zip->getFromIndex($i);
						
						foreach (array('name', 'code', 'version', 'author', 'link') as $tag) {
							$first_explosion = explode('<' . $tag . '>', $xml);
							$second_explosion = explode('</' . $tag . '>', $first_explosion[1]);
							${'xml_'.$tag} = $second_explosion[0];
						}
						
						$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE code = '" . $this->db->escape($xml_code) . "'");
						
						$this->db->query("INSERT INTO " . DB_PREFIX . "modification SET code = '" . $this->db->escape($xml_code) . "', name = '" . $this->db->escape($xml_name) . "', author = '" . $this->db->escape($xml_author) . "', version = '" . $this->db->escape($xml_version) . "', link = '" . $this->db->escape($xml_link) . "', xml = '" . $this->db->escape($xml) . "', status = 1, date_added = NOW()");
						
						continue;
					}
					
					$full_filepath = DIR_APPLICATION . '../' . $filepath;
					
					if (!strpos($filepath, '.')) {
						if (!is_dir($full_filepath)) {
							mkdir($full_filepath, 0777);
						}
						continue;
					}
					
					file_put_contents($full_filepath, $zip->getFromIndex($i));
				}
				
				$zip->close();
			} else {
				echo 'Invalid zip archive';
				return;
			}
		} else {
			if ($zip->open($second_zip) === true) {
				$zip->extractTo($temp_directory);
				$zip->close();
			} else {
				echo 'Invalid zip archive';
				return;
			}
			
			$third_zip = $temp_directory . $this->name . '.ocmod.zip';
			$zip = new \ZipArchive();
			
			if ($zip->open($third_zip) === true) {
				$zip->extractTo(DIR_EXTENSION . $this->name . '/');
				$zip->close();
			} else {
				echo 'Invalid zip archive';
				return;
			}
			
			@unlink($third_zip);
		}
		
		@array_map('unlink', array_filter((array)glob($temp_directory . '*')));
		@rmdir($temp_directory);
		
		echo 'success';
	}
	
	//==============================================================================
	// capture()
	//==============================================================================
	public function capture() {
		if (!$this->hasPermission('modify')) {
			echo 'PermissionError';
			return;
		}
		
		$payment_intent_response = $this->curlRequest('GET', 'payment_intents/' . $this->request->post['payment_intent_id']);
		
		if (!empty($payment_intent_response['error'])) {
			echo 'Error: ' . $payment_intent_response['error']['message'];
			return;
		}
		
		$amount = str_replace(',', '.', $this->request->post['amount']);
		$decimal_factor = (in_array(strtoupper($payment_intent_response['currency']), array('BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','UGX','VND','VUV','XAF','XOF','XPF'))) ? 1 : 100;
		$three_decimal_factor = (in_array(strtoupper($payment_intent_response['currency']), array('BHD','JOD','KWD','OMR','TND'))) ? 10 : 1;
		
		$capture_response = $this->curlRequest('POST', 'payment_intents/' . $this->request->post['payment_intent_id'] . '/capture', array('amount_to_capture' => round($amount * $decimal_factor) * $three_decimal_factor));
		
		if (!empty($capture_response['error'])) {
			echo 'Error: ' . $capture_response['error']['message'];
		}
		
		if (empty($capture_response['error']) || strpos($capture_response['error']['message'], 'has already been captured')) {
			$fee = '';
			$charge = $capture_response['charges']['data'][0];
			$charge_currency = strtoupper($charge['currency']);
			
			if (!empty($charge['balance_transaction'])) {
				$balance_transaction = $this->curlRequest('GET', 'balance_transactions/' . $charge['balance_transaction']);
				
				$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : $this->type . '_';
				$mapped_currency = $this->config->get($prefix . $this->name . '_currencies_' . $charge_currency);
				
				if ($mapped_currency) {
					$transaction_decimal_factor = (in_array($mapped_currency, array('BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','UGX','VND','VUV','XAF','XOF','XPF'))) ? 1 : 100;
					$transaction_decimal_factor = (in_array($mapped_currency, array('BHD','JOD','KWD','OMR','TND'))) ? 1000 : $transaction_decimal_factor;
					$fee = ', fee ' . $this->currency->format($balance_transaction['fee'] / $transaction_decimal_factor, $charge_currency, 1);
				}
			}
			
			$this->db->query("UPDATE " . DB_PREFIX . "order_history SET `comment` = REPLACE(`comment`, '<span>No &nbsp;</span> <a', 'Yes (" . $this->currency->format($amount, $charge_currency, 1) . " captured" . $fee . ") <a style=\"display: none\"') WHERE `comment` LIKE '%capture($(this), %, \'" . $this->db->escape($this->request->post['payment_intent_id']) . "\')%'");
		}
	}
	
	//==============================================================================
	// refund()
	//==============================================================================
	public function refund() {
		if (!$this->hasPermission('modify')) {
			echo 'PermissionError';
			return;
		}
		
		$charge_response = $this->curlRequest('GET', 'charges/' . $this->request->post['charge_id']);
		
		if (!empty($charge_response['error'])) {
			echo 'Error: ' . $charge_response['error']['message'];
			return;
		}
		
		$amount = str_replace(',', '.', $this->request->post['amount']);
		$decimal_factor = (in_array(strtoupper($charge_response['currency']), array('BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','UGX','VND','VUV','XAF','XOF','XPF'))) ? 1 : 100;
		$three_decimal_factor = (in_array(strtoupper($charge_response['currency']), array('BHD','JOD','KWD','OMR','TND'))) ? 10 : 1;
		
		$curl_data = array(
			'amount'	=> round($amount * $decimal_factor) * $three_decimal_factor,
			'metadata'	=> array(
				'time'		=> time(),
			),
		);
		
		$refund_response = $this->curlRequest('POST', 'charges/' . $this->request->post['charge_id'] . '/refunds', $curl_data);
		
		if (!empty($refund_response['error'])) {
			if (!empty($charge_response['payment_intent'])) {
				$cancel_response = $this->curlRequest('POST', 'payment_intents/' . $charge_response['payment_intent'] . '/cancel');
				
				if (!empty($cancel_response['error'])) {
					echo 'Error: ' . $cancel_response['error']['message'];
					return;
				}
			} else {
				echo 'Error: ' . $refund_response['error']['message'];
				return;
			}
		}
	}
	
	//==============================================================================
	// stripeCheckout()
	//==============================================================================
	public function stripeCheckout() {
		if (!$this->hasPermission('modify')) {
			echo 'PermissionError';
			return;
		}
		
		$settings = array('autobackup' => false);
		$this->loadSettings($settings);
		$settings = $settings['saved'];
		
		// Get currency settings
		$amount = str_replace(',', '.', $this->request->post['amount']);
		$currency = $this->request->post['currency'];
		$main_currency = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `key` = 'config_currency' AND store_id = 0 ORDER BY setting_id DESC LIMIT 1")->row['value'];
		$decimal_factor = (in_array($settings['currencies_' . $currency], array('BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','UGX','VND','VUV','XAF','XOF','XPF'))) ? 1 : 100;
		$three_decimal_factor = (in_array($settings['currencies_' . $currency], array('BHD','JOD','KWD','OMR','TND'))) ? 10 : 1;
		
		// Set other checkout data
		$order_id = (int)$this->request->post['order_id'];
		$order_info = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = " . (int)$order_id)->row;
		
		$customer_id = (!empty($order_info['customer_id'])) ? $order_info['customer_id'] : 0;
		$stripe_customer_id = '';
		
		if ($customer_id) {
			$stripe_customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . $this->name . "_customer WHERE customer_id = " . (int)$customer_id);
			if ($stripe_customer_query->num_rows) {
				$stripe_customer_id = $stripe_customer_query->row['stripe_customer_id'];
			}
		}
		
		// Set URLs
		if (version_compare(VERSION, '4.0', '<')) {
			$separator = '/';
		} elseif (version_compare(VERSION, '4.0.2.0', '<')) {
			$separator = '|';
		} else {
			$separator = '.';
		}
		
		$current_url = 'http' . (!empty($server['HTTPS']) && $server['HTTPS'] !== 'off' ? 's' : '') . '://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
		$current_url = str_replace($separator . 'stripeCheckout', '', html_entity_decode($current_url, ENT_QUOTES, 'UTF-8'));
		
		if (!empty($this->request->post['cancel_url'])) {
			$cancel_url = $this->request->post['cancel_url'];
		} else {
			$cancel_url = $current_url;
		}
		
		if (!empty($this->request->post['success_url'])) {
			$success_url = $this->request->post['success_url'];
		} else {
			$success_url = $current_url . '&checkout=' . number_format($amount, 2) . ' ' . $currency;
			if (!empty($order_id)) {
				$success_url .= '&order_id=' . (int)$order_id;
			}
			if (!empty($this->request->post['order_status'])) {
				$success_url .= '&order_status_id=' . (int)$this->request->post['order_status'];
			}
		}
		
		// Create Stripe Checkout session
		$checkout_data = array(
			'mode'					=> 'payment',
			'client_reference_id'	=> $order_id,
			'line_items'			=> array(array(
				'price_data'	=> array(
					'currency'		=> strtolower($currency),
					'unit_amount'	=> round($decimal_factor * $amount) * $three_decimal_factor,
					'product_data'	=> array(
						'name'		=> 'Total',
						'images'	=> array(),
					),
				),
				'quantity'		=> 1,
			)),
			'cancel_url'			=> $cancel_url,
			'success_url'			=> $success_url,
			'payment_intent_data'	=> array(
				'description'	=> $this->request->post['description'],
				'metadata'		=> array(
					'Store'			=> substr($this->config->get('config_name'), 0, 200),
					'Order ID'		=> $order_id,
				),
			),
		);
		
		if ($stripe_customer_id) {
			$checkout_data['customer'] = $stripe_customer_id;
		}
		
		$checkout_session = $this->curlRequest('POST', 'checkout/sessions', $checkout_data);
		
		if (!empty($checkout_session['error'])) {
			echo $checkout_session['error']['message'];
		} else {
			echo 'success:' . $checkout_session['id'];
		}
	}
	
	//==============================================================================
	// launch()
	//==============================================================================
	public function launch() {
		$this->createDomainAssociationFile();
		
		$this->session->data['stripe_encryption_key'] = md5(rand());
		$mode = (!empty($this->request->get['mode']) && $this->request->get['mode'] == 'manual') ? '&mode=manual' : '';
		
		if (empty($this->request->server['HTTP_REFERER'])) {
			$s = $this->request->server;
			if (version_compare(VERSION, '4.0', '<')) {
				$separator = '/';
			} elseif (version_compare(VERSION, '4.0.2.0', '<')) {
				$separator = '|';
			} else {
				$separator = '.';
			}
			$this->request->server['HTTP_REFERER'] = 'http' . (!empty($s['HTTPS']) && $s['HTTPS'] !== 'off' ? 's' : '') . '://' . $s['HTTP_HOST'] . str_replace($separator . 'launch', '', $s['REQUEST_URI']);
		}
		
		$this->response->redirect('https://www.getclearthinking.com/index.php?route=stripe_connect&origin=' . base64_encode($this->request->server['HTTP_REFERER']) . '&version=' . VERSION . '&key=' . $this->session->data['stripe_encryption_key'] . $mode);
	}
	
	//==============================================================================
	// createDomainAssociationFile()
	//==============================================================================
	private function createDomainAssociationFile() {
		$well_known_directory = DIR_APPLICATION . '../.well-known/';
		if (!is_dir($well_known_directory)) {
			mkdir($well_known_directory);
		}
		
		if (!is_file($well_known_directory . 'apple-developer-merchantid-domain-association')) {
			$curl = curl_init('https://stripe.com/files/apple-pay/apple-developer-merchantid-domain-association');
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_TIMEOUT, 10);
			
			$domain_association_file = @curl_exec($curl);
			curl_close($curl);
			
			@file_put_contents($well_known_directory . 'apple-developer-merchantid-domain-association', $domain_association_file);
		}
	}
	
	//==============================================================================
	// applepay()
	//==============================================================================
	public function applepay() {
		// Check for account_id
		$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : $this->type . '_';
		$account_id = $this->config->get($prefix . $this->name . '_account_id');
		
		if (empty($account_id)) {
			echo 'The extension is not yet connected to your Stripe account.';
			return;
		}
		
		// Register this domain for Apple Pay
		$this->createDomainAssociationFile();
		
		$url = 'https://www.getclearthinking.com/index.php?route=stripe_connect/applepay&account=' . $account_id;
		
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_REFERER, $this->request->server['HTTP_REFERER']);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		$response = curl_exec($curl);
		curl_close($curl);
		
		echo $response;
	}
	
	//==============================================================================
	// connect()
	//==============================================================================
	public function connect() {
		if (!$this->hasPermission('modify')) {
			echo 'PermissionError';
			return;
		}
		
		$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : $this->type . '_';
		$code = $prefix . $this->name;
		
		if (!empty($this->request->get['info']) && !empty($this->session->data['stripe_encryption_key'])) {
			$decrypted_data = openssl_decrypt($this->request->get['info'], 'AES-256-CBC', $this->session->data['stripe_encryption_key']);
			unset($this->session->data['stripe_encryption_key']);
			$api_info = json_decode(base64_decode($decrypted_data), true);
		} else {
			$api_info = $this->request->post;
		}
		
		foreach ($api_info as $key => $value) {
			if (!in_array($key, array('account_id', 'refresh_token', 'live_publishable_key', 'live_access_token', 'test_publishable_key', 'test_access_token'))) {
				continue;
			}
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($prefix . $this->name) . "' AND `key` = '" . $this->db->escape($prefix . $this->name . '_' . $key) . "'");
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "setting SET
				`store_id` = 0,
				`code` = '" . $this->db->escape($code) . "',
				`key` = '" . $this->db->escape($code . '_' . $key) . "',
				`value` = '" . $this->db->escape($value) . "',
				`serialized` = 0
			");
		}
		
		$this->session->data['connect_success'] = 'Connection complete!';
		
		$token = (version_compare(VERSION, '3.0', '<')) ? 'token=' . $this->session->data['token'] : 'user_token=' . $this->session->data['user_token'];
		$extension_route = (version_compare(VERSION, '4.0', '<')) ? 'extension/' . $this->type . '/' . $this->name : 'extension/' . $this->name . '/' . $this->type . '/' . $this->name;
		
		$this->response->redirect(str_replace('&amp;', '&', $this->url->link($extension_route, $token, 'SSL')));
	}
	
	//==============================================================================
	// subscriptionOrderReport()
	//==============================================================================
	public function subscriptionOrderReport() {
		if (!$this->hasPermission('access')) {
			echo 'PermissionError';
			return;
		}
		
		$data = $this->loadLanguage($this->type . '/' . $this->name);
		
		// Set up search parameters
		if (!empty($this->request->get['order_status_id'])) {
			$order_status_id = $this->request->get['order_status_id'];
			$order_status_sql = "o.order_status_id = " . (int)$this->request->get['order_status_id'];
		} else {
			$order_status_id = 0;
			$order_status_sql = "o.order_status_id > 0";
		}
		
		if (!empty($this->request->get['start'])) {
			$start_date = $this->request->get['start'];
			$start_sql = " AND DATE(o.date_added) >= '" . $this->db->escape($start_date) . "'";
		} else {
			$start_date = '';
			$start_sql = "";
		}
		
		if (!empty($this->request->get['end'])) {
			$end_date = $this->request->get['end'];
			$end_sql = " AND DATE(o.date_added) <= '" . $this->db->escape($end_date) . "'";
		} else {
			$end_date = '';
			$end_sql = "";
		}
		
		// Get order statuses
		$order_statuses = array();
		$order_statuses_html = '<option value="0">' . $data['text_all_statuses'] . '</option>';
		
		$order_status_list = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE language_id = " . (int)$this->config->get('config_language_id') . " ORDER BY name ASC")->rows;
		foreach ($order_status_list as $order_status) {
			$order_statuses[$order_status['order_status_id']] = $order_status['name'];
			$selected = ($order_status['order_status_id'] == $order_status_id) ? 'selected="selected"' : '';
			$order_statuses_html .= '<option ' . $selected . ' value="' . $order_status['order_status_id'] . '">' . $order_status['name'] . '</option>';
		}
		
		// Set version-specific variables
		if (version_compare(VERSION, '2.1', '<')) {
			$token = '&token=' . $this->request->get['token'];
			$extension_route = 'extension/' . $this->type . '/' . $this->name . '/';
			$customer_url = 'index.php?route=sale/customer/edit' . $token;
			$order_url = 'index.php?route=sale/order/info&token=' . $this->session->data['token'] . $token;
		} elseif (version_compare(VERSION, '3.0', '<')) {
			$token = '&token=' . $this->request->get['token'];
			$extension_route = 'extension/' . $this->type . '/' . $this->name . '/';
			$customer_url = 'index.php?route=customer/customer/edit' . $token;
			$order_url = 'index.php?route=sale/order/info&token=' . $this->session->data['token'] . $token;
		} elseif (version_compare(VERSION, '4.0', '<')) {
			$token = '&user_token=' . $this->request->get['user_token'];
			$extension_route = 'extension/' . $this->type . '/' . $this->name . '/';
			$customer_url = 'index.php?route=customer/customer/edit' . $token;
			$order_url = 'index.php?route=sale/order/info' . $token;
		} else {
			$token = '&user_token=' . $this->request->get['user_token'];
			$separator = (version_compare(VERSION, '4.0.2.0', '<')) ? '|' : '.';
			$extension_route = 'extension/' . $this->name . '/' . $this->type . '/' . $this->name . $separator;
			$customer_url = 'index.php?route=customer/customer' . $separator . 'form' . $token;
			$order_url = 'index.php?route=sale/order' . $separator . 'info' . $token;
		}
		
		// Get order data
		$order_history_query = $this->db->query("SELECT *, o.date_added AS date_added, os.name AS order_status FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX. "order_history oh ON (o.order_id = oh.order_id) LEFT JOIN " . DB_PREFIX . "order_status os ON (os.order_status_id = o.order_status_id) WHERE oh.comment LIKE '%Subscribed to Plan%' AND " . $order_status_sql . $start_sql . $end_sql . " GROUP BY o.order_id ORDER BY o.order_id DESC");
		
		// Generate HTML
		$output = '
			<div id="content" style="padding: 20px">
				<div class="page-header" style="margin-top: 0">
					<h1>' . $data['heading_subscription_order_report'] . '</h1>
					<a class="btn btn-default btn-light" style="float: right" onclick="history.back()"><i class="fa fa-reply pad-right-sm"></i> ' . $data['button_back'] . '</a>
				</div>
				<div style="margin: 25px; text-align: center;">
					' . $data['text_order_statuses'] . ' <select id="order_status_id" class="form-control" style="display: inline-block; margin-right: 25px; width: auto;">' . $order_statuses_html . '</select>
					' . $data['text_starting_date'] . ' <input type="text" id="start" class="form-control" style="display: inline-block; margin-right: 25px; width: 110px;" value="' . $start_date . '" placeholder="YYYY-MM-DD" />
					' . $data['text_ending_date'] . ' <input type="text" id="end" class="form-control" style="display: inline-block; margin-right: 25px; width: 110px;" value="' . $end_date . '" placeholder="YYYY-MM-DD" />
					<button class="btn btn-primary" onclick="filterReport()">' . $data['button_filter'] . '</button>
					<script>
						function filterReport() {
							url = "index.php?route=' . $extension_route . 'subscriptionOrderReport' . $token . '";
							' . ($order_status_id ? 'url += "&order_status_id=" + document.getElementById("order_status_id").value;' : '') . '
							url += "&start=" + document.getElementById("start").value;
							url += "&end=" + document.getElementById("end").value;
							location = url;
						}
					</script>
				</div>
				<table class="table table-bordered" style="background: white; width: 100%;">
					<thead>
						<tr>
							<td class="text-right">' . $data['column_order_id'] . '</td>
							<td class="text-left">' . $data['column_customer'] . '</td>
							<td class="text-left">' . $data['column_status'] . '</td>
							<td class="text-right">' . $data['column_total'] . '</td>
							<td class="text-left">' . $data['column_date_added'] . '</td>
							<td class="text-left">' . $data['column_date_modified'] . '</td>
							<td class="text-right">' . $data['column_action'] . '</td>
						</tr>
					</thead>
					<tbody>
		';
		
		foreach ($order_history_query->rows as $row) {
			if (version_compare(VERSION, '2.0', '<')) {
				$view_button = '>' . $data['text_view'];
			} elseif (version_compare(VERSION, '3.0', '<')) {
				$view_button = 'title="' . $data['button_view'] . '"><i class="fa fa-eye"></i>';
			}
			
			if (!empty($row['customer_id'])) {
				$customer = '<a href="' . $customer_url . '&customer_id=' . $row['customer_id'] . '">' . $row['firstname'] . ' ' . $row['lastname'] . '</a>';
			} else {
				$customer = $row['firstname'] . ' ' . $row['lastname'];
			}
			
			$output .= '
				<tr>
						<td class="text-right">' . $row['order_id'] . '</td>
						<td class="text-left">' . $customer . '</td>
						<td class="text-left">' . $row['order_status'] . '</td>
						<td class="text-right">' . $this->currency->format($row['total'], $row['currency_code']) . '</td>
						<td class="text-left">' . $row['date_added'] . '</td>
						<td class="text-left">' . $row['date_modified'] . '</td>
						<td class="text-right"><a class="btn btn-primary" href="' . $order_url . '&order_id=' . $row['order_id'] . '" data-toggle="tooltip" data-bs-toggle="tooltip" ' . $view_button . '</a></td>
				</tr>
			';
		}
		
		$output .= '</tbody></table></div>';
		
		echo $this->load->controller('common/header');
		if (version_compare(VERSION, '2.0', '<')) {
			echo '<style type="text/css">td { border: 1px solid #DDD; padding: 10px 20px; }</style>';
		} else {
			echo $this->load->controller('common/column_left');
		}
		echo $output;
		echo $this->load->controller('common/footer');
	}
	
	//==============================================================================
	// transferSubscriptions()
	//==============================================================================
	public function transferSubscriptions() {
		if (!$this->hasPermission('modify')) {
			echo 'PermissionError';
			return;
		}
		
		$errors = 0;
		$success = 0;
		$subscriptions = array();
		
		$curl_data = array(
			'price'				=> $this->request->post['old'],
			'limit'				=> 100,
		);
		
		$subscriptions_response = array('has_more' => true);
		
		while (!empty($subscriptions_response['has_more'])) {
			$subscriptions_response = $this->curlRequest('GET', 'subscriptions', $curl_data);
			if (empty($subscriptions_response['error'])) {
				$subscriptions = array_merge($subscriptions, $subscriptions_response['data']);
				$curl_data['starting_after'] = $subscriptions[count($subscriptions) - 1]['id'];
			}
		}
		
		foreach ($subscriptions as $subscription) {
			$item_id = '';
			$item_quantity = 0;
			
			foreach ($subscription['items']['data'] as $item) {
				if (!empty($item['price']['id']) && $item['price']['id'] == $this->request->post['old']) {
					$item_id = $item['id'];
					$item_quantity = $item['quantity'];
				}
			}
			
			if (empty($item_id)) continue;
			
			$curl_data = array(
				'proration_behavior'	=> 'none',
				'items'					=> array(
					array(
						'id'		=> $item_id,
						'price'		=> $this->request->post['new'],
						'quantity'	=> $item_quantity,
					),
				),
			);
			
			$update_response = $this->curlRequest('POST', 'subscriptions/' . $subscription['id'], $curl_data);
			
			if (!empty($update_response['error'])) {
				$this->log->write('STRIPE SUBSCRIPTION TRANSFER ERROR: ' . $update_response['error']['message']);
				$errors++;
			} else {
				$success++;
			}
		}
		
		echo $success . " subscription(s) successfully transferred\n\n" . $errors . " error(s) occurred\n\n(Errors can be viewed in the OpenCart error log)";
	}
	
	//==============================================================================
	// curlRequest()
	//==============================================================================
	private function curlRequest($request, $api, $data = array()) {
		if (version_compare(VERSION, '4.0', '<')) {
			$model_file = DIR_CATALOG . 'model/extension/' . $this->type . '/' . $this->name . '.php';
			require_once(class_exists('VQMod') ? \VQMod::modCheck(modification($model_file)) : modification($model_file));
			$frontend_model = new ModelExtensionPaymentStripe($this->registry);
		} elseif (defined('DIR_EXTENSION')) {
			$model_file = DIR_EXTENSION . $this->name . '/catalog/model/' . $this->type . '/' . $this->name . '.php';
			require_once(class_exists('VQMod') ? \VQMod::modCheck($model_file) : $model_file);
			$frontend_model = new \Opencart\Catalog\Model\Extension\Stripe\Payment\Stripe($this->registry);
		}
		
		return $frontend_model->curlRequest($request, $api, $data);
	}
}
?>