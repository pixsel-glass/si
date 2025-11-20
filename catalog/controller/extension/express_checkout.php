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

//namespace Opencart\Catalog\Controller\Extension\Stripe\Extension;
//class ExpressCheckout extends \Opencart\System\Engine\Controller {

class ControllerExtensionExpressCheckout extends Controller {
	
	private $extension = 'stripe';
	private $type = 'extension';
	private $name = 'express_checkout';
	
	//==============================================================================
	// index()
	//==============================================================================
	public function index() {
		// Check restrictions
		$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : 'payment_';
		$language = (!empty($this->session->data['language'])) ? $this->session->data['language'] : $this->config->get('config_language');
		$settings = $this->getSettings();
		
		if (!array_intersect(array($this->config->get('config_store_id')), explode(';', $settings['stores'])) ||
			!array_intersect(array((int)$this->customer->getGroupId()), explode(';', $settings['customer_groups'])) ||
			empty($settings['currencies_' . $this->session->data['currency']])
		) {
			echo json_encode(array());
			return;
		}
		
		// Check for subscription products
		$subscription_plans = $this->load->controller('extension/payment/stripe/getSubscriptionPlans');
		
		if (!empty($subscription_plans)) {
			echo json_encode(array());
			return;
		}
		
		// Calculate amount
		$amount = 0;
		$requires_shipping = false;
		
		if ($this->cart->hasProducts()) {
			$amount = $this->cart->getTotal();
			$requires_shipping = $this->cart->hasShipping();
		}
		
		if (!empty($this->request->post['product_id'])) {
			$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$this->request->post['product_id']);
			if (!empty($product_query->row['price'])) {
				$amount += $product_query->row['price'];
			}
			if (!empty($product_query->row['shipping'])) {
				$requires_shipping = true;
			}
		}
		
		if (empty($amount)) {
			echo json_encode(array());
			return;
		}
		
		// Set address and payment method into session data
		if ($this->customer->isLogged()) {
			$customer = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = " . (int)$this->customer->getId())->row;
			
			if (version_compare(VERSION, '4.0', '>=')) {
				$default_address_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE customer_id = " . (int)$customer['customer_id'] . " AND `default` = 1");
				$customer['address_id'] = ($default_address_query->num_rows) ? $default_address_query->row['address_id'] : 0;
			}
			
			if ($customer['address_id']) {
				$address = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = " . (int)$customer['address_id'])->row;
				
				if (!empty($address['country_id'])) {
					if (version_compare(VERSION, '4.1', '<')) {
						$country_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE country_id = " . (int)$address['country_id']);
						$zone_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE zone_id = " . (int)$address['zone_id']);
					} else {
						$country_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country c LEFT JOIN " . DB_PREFIX . "country_description cd ON (cd.country_id = c.country_id) WHERE c.country_id = " . (int)$address['country_id'] . " AND cd.language_id = " . (int)$this->config->get('config_language_id'));
						$zone_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone z LEFT JOIN " . DB_PREFIX . "zone_description zd ON (zd.zone_id = z.zone_id) WHERE z.zone_id = " . (int)$address['zone_id'] . " AND zd.language_id = " . (int)$this->config->get('config_language_id'));
					}
					
					if (version_compare(VERSION, '4.0', '<')) {
						$address_format = (!empty($country_query->row['address_format'])) ? $country_query->row['address_format'] : '';
					} else {
						$address_format_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address_format WHERE address_format_id = " . (int)$country_query->row['address_format_id']);
						$address_format = (!empty($address_format_query->row['address_format'])) ? $address_format_query->row['address_format'] : '';
					}
					
					$session_address = array(
						'firstname'			=> $customer['firstname'],
						'lastname'			=> $customer['lastname'],
						'company'			=> $address['company'],
						'address_1'			=> $address['address_1'],
						'address_2'			=> $address['address_2'],
						'postcode'			=> $address['postcode'],
						'city'				=> $address['city'],
						'zone_id'			=> (!empty($zone_query->row['zone_id'])) ? $zone_query->row['zone_id'] : 0,
						'zone'				=> (!empty($zone_query->row['name'])) ? $zone_query->row['name'] : '',
						'zone_code'			=> (!empty($zone_query->row['code'])) ? $zone_query->row['code'] : '',
						'country_id'		=> (!empty($country_query->row['country_id'])) ? $country_query->row['country_id'] : 0,
						'country'			=> (!empty($country_query->row['name'])) ? $country_query->row['name'] : '',
						'iso_code_2'		=> (!empty($country_query->row['iso_code_2'])) ? $country_query->row['iso_code_2'] : '',
						'iso_code_3'		=> (!empty($country_query->row['iso_code_3'])) ? $country_query->row['iso_code_3'] : '',
						'address_format'	=> $address_format,
					);
					
					$this->session->data['payment_address'] = $session_address;
					
					if ($requires_shipping) {
						$this->session->data['shipping_address'] = $session_address;
					} else {
						unset($this->session->data['shipping_address']);
					}
				}
			}
		}
		
		if (version_compare(VERSION, '4.0', '<')) {
			$this->session->data['payment_method'] = array(
				'code'	=> $this->extension,
				'title'	=> html_entity_decode($settings['title_' . $language], ENT_QUOTES, 'UTF-8'),
			);
		} else {
			$this->session->data['payment_method'] = array(
				'code'	=> $this->extension . '.' . $this->extension,
				'name'	=> html_entity_decode($settings['title_' . $language], ENT_QUOTES, 'UTF-8'),
			);
		}
		
		// Check geo zones
		if (!empty($this->session->data['shipping_address']['country_id']) && !empty($this->session->data['shipping_address']['zone_id'])) {
			$current_geozones = array();
			$geozones = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE country_id = " . (int)$this->session->data['shipping_address']['country_id'] . " AND (zone_id = 0 OR zone_id = " . (int)$this->session->data['shipping_address']['zone_id'] . ")");
			foreach ($geozones->rows as $geozone) $current_geozones[] = $geozone['geo_zone_id'];
			if (empty($current_geozones)) $current_geozones = array(0);
			
			if (!array_intersect($current_geozones, explode(';', $settings['geo_zones']))) {
				echo json_encode(array());
				return;
			}
		}
		
		// Return output
		$this->load->model($settings['extension_route']);
		$order_info = $this->{'model_' . str_replace('/', '_', $settings['extension_route'])}->getOrderInfo();
		
		$json = array(
			'account_id'			=> $settings['account_id'],
			'alignment'				=> $settings['express_alignment'],
			'amount'				=> $this->formatAmount($amount, $settings['currencies_' . $this->session->data['currency']]),
			'button_height'			=> ($settings['express_button_height'] >= 40 && $settings['express_button_height'] <= 55) ? (int)$settings['express_button_height'] : 44,
			'checkout_success_url'	=> $this->url->link('checkout/success', version_compare(VERSION, '4.0', '<') ? '' : 'language=' . $language, 'SSL'),
			'currency'				=> strtolower($settings['currencies_' . $this->session->data['currency']]),
			'finalizing_order_text'	=> html_entity_decode($settings['text_finalizing_order_' . $language], ENT_QUOTES, 'UTF-8'),
			'line_items'			=> $this->getLineItems($settings, $order_info),
			'margins'				=> (!empty($settings['express_margins'])) ? $settings['express_margins'] : '0px',
			'max_columns'			=> (int)$settings['express_max_columns'],
			'max_rows'				=> (int)$settings['express_max_rows'],
			'payment_methods'		=> explode(';', $settings['express_payment_methods']),
			'publishable_key'		=> $settings[$settings['transaction_mode'] . '_publishable_key'],
			'requires_shipping'		=> (bool)$requires_shipping,
			'width'					=> (!empty($settings['express_width'])) ? (int)$settings['express_width'] . 'px' : '100%',
		);
		
		echo json_encode($json);
	}
	
	//==============================================================================
	// loadExpressCheckout()
	//==============================================================================
	public function loadExpressCheckout() {
		// Check if product is out of stock
		$this->load->language('checkout/cart');
		$stock_error = str_replace('marked with *** ', '', $this->language->get('error_stock'));
		
		if (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) {
			echo json_encode(array('error_message' => $stock_error));
			return;
		}
		
		// Add product to cart
		$settings = $this->getSettings();
		
		if (!empty($this->request->post)) {
			// Check if product is already in cart
			$already_in_cart = false;
			
			foreach ($this->cart->getProducts() as $product) {
				if ($product['product_id'] == $this->request->post['product_id']) {
					$already_in_cart = true;
				}
			}
			
			// Add item if not in cart
			$error_message = '';
			
			if (!$already_in_cart) {
				$quantity = (!empty($this->request->post['quantity'])) ? $this->request->post['quantity'] : 1;
				$options = (!empty($this->request->post['option'])) ? array_filter($this->request->post['option']) : array();
				
				$options_error = '';
				$this->load->model('catalog/product');
				
				if (version_compare(VERSION, '4.0', '<')) {
					$product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);
				} else {
					$product_options = $this->model_catalog_product->getOptions($this->request->post['product_id']);
				}
				
				foreach ($product_options as $product_option) {
					if ($product_option['required'] && empty($options[$product_option['product_option_id']])) {
						$options_error = sprintf($this->language->get('error_required'), $product_option['name']);
					}
				}
				
				if ($options_error) {
					$error_message = $options_error;
				} else {
					$this->cart->add($this->request->post['product_id'], $quantity, $options);
					
					if (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) {
						$error_message = $stock_error;
					}
				}
			}
		}
		
		// Check for subscription products
		$subscription_plans = $this->load->controller('extension/payment/stripe/getSubscriptionPlans');
		
		if (!empty($subscription_plans)) {
			echo json_encode(array('error_message' => 'This product cannot be purchased using this button. Please proceed to checkout.'));
			return;
		}
		
		// Return output
		if ($error_message) {
			echo json_encode(array('error_message' => $error_message));
		} else {
			$this->load->model($settings['extension_route']);
			$order_info = $this->{'model_' . str_replace('/', '_', $settings['extension_route'])}->getOrderInfo();
			
			$output = array(
				'amount'			=> $this->formatAmount($order_info['total'], $settings['currencies_' . $order_info['currency_code']]),
				'line_items'		=> $this->getLineItems($settings, $order_info),
			);
			
			echo json_encode($output);
		}
	}
	
	//==============================================================================
	// getShippingRates()
	//==============================================================================
	public function getShippingRates() {
		unset($this->session->data['order_id']);
		unset($this->session->data['shipping_method']);
		unset($this->session->data['shipping_methods']);
		
		// Get address data
		if (version_compare(VERSION, '4.1', '<')) {
			$country_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE iso_code_2 = '" . $this->db->escape($this->request->post['address']['country']) . "'");
		} else {
			$country_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country c LEFT JOIN " . DB_PREFIX . "country_description cd ON (cd.country_id = c.country_id) WHERE c.iso_code_2 = '" . $this->db->escape($this->request->post['address']['country']) . "' AND cd.language_id = " . (int)$this->config->get('config_language_id'));
		}
		
		if ($country_query->num_rows) {
			$country_id = $country_query->row['country_id'];
			$country_name = $country_query->row['name'];
			$iso_code_2 = $country_query->row['iso_code_2'];
			$iso_code_3 = $country_query->row['iso_code_3'];
			
			if (version_compare(VERSION, '4.0', '<')) {
				$address_format = (!empty($country_query->row['address_format'])) ? $country_query->row['address_format'] : '';
			} else {
				$address_format_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address_format WHERE address_format_id = " . (int)$country_query->row['address_format_id']);
				$address_format = (!empty($address_format_query->row['address_format'])) ? $address_format_query->row['address_format'] : '';
			}
		} else {
			$country_id = 0;
			$country_name = '';
			$iso_code_2 = '';
			$iso_code_3 = '';
			$address_format = '';
		}
		
		if ($country_id == 222) {
			$zone = $this->getUkZone($this->request->post['address']['postal_code']);
		} elseif (version_compare(VERSION, '4.1', '<')) {
			$zone = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = " . (int)$country_id . " AND `code` = '" . $this->db->escape($this->request->post['address']['state']) . "'")->row;
		} else {
			$zone = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone z LEFT JOIN " . DB_PREFIX . "zone_description zd ON (zd.zone_id = z.zone_id) WHERE z.country_id = " . (int)$country_id . " AND z.code = '" . $this->db->escape($this->request->post['address']['state']) . "' AND zd.language_id = " . (int)$this->config->get('config_language_id'))->row;
		}
		
		$zone_id = (!empty($zone['zone_id'])) ? $zone['zone_id'] : 0;
		$zone_name = (!empty($zone['name'])) ? $zone['name'] : '';
		$zone_code = (!empty($zone['code'])) ? $zone['code'] : '';
		
		$address = array(
			'firstname'			=> '',
			'lastname'			=> '',
			'company'			=> '',
			'address_1'			=> '',
			'address_2'			=> '',
			'postcode'			=> $this->request->post['address']['postal_code'],
			'city'				=> $this->request->post['address']['city'],
			'zone_id'			=> $zone_id,
			'zone'				=> $zone_name,
			'zone_code'			=> $zone_code,
			'country_id'		=> $country_id,
			'country'			=> $country_name,
			'iso_code_2'		=> $iso_code_2,
			'iso_code_3'		=> $iso_code_3,
			'address_format'	=> $address_format,
		);
		
		$this->session->data['shipping_address'] = $address;
		
		// Get shipping rates
		if (version_compare(VERSION, '2.0', '<')) {
			$prefix = '';
			$this->load->model('setting/extension');
			$results = $this->model_setting_extension->getExtensions('shipping');
		} elseif (version_compare(VERSION, '3.0', '<')) {
			$prefix = '';
			$this->load->model('extension/extension');
			$results = $this->model_extension_extension->getExtensions('shipping');
		} else {
			$prefix = 'shipping_';
			$this->load->model('setting/extension');
			if (version_compare(VERSION, '4.0', '<')) {
				$results = $this->model_setting_extension->getExtensions('shipping');
			} else {
				$results = $this->model_setting_extension->getExtensionsByType('shipping');
			}
		}
		
		$quote_data = array();
		
		foreach ($results as $result) {
			if (!$this->config->get($prefix . $result['code'] . '_status')) continue;
			
			if (version_compare(VERSION, '2.3', '<')) {
				$this->load->model('shipping/' . $result['code']);
				$quote = $this->{'model_shipping_' . $result['code']}->getQuote($address);
			} elseif (version_compare(VERSION, '4.0', '<')) {
				$this->load->model('extension/shipping/' . $result['code']);
				$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($address);
			} else {
				$this->load->model('extension/' . $result['extension'] . '/shipping/' . $result['code']);
				$quote = $this->{'model_extension_' . $result['extension'] . '_shipping_' . $result['code']}->getQuote($address);
			}
			
			if (empty($quote)) continue;
			
			$quote_data[$result['code']] = array(
				'title'			=> (isset($quote['title'])) ? $quote['title'] : $quote['name'],
				'name'			=> (isset($quote['title'])) ? $quote['title'] : $quote['name'],
				'quote'			=> $quote['quote'],
				'sort_order'	=> $quote['sort_order'],
				'error'			=> $quote['error'],
			);
		}
		
		$sort_order = array();
		foreach ($quote_data as $key => $value) $sort_order[$key] = $value['sort_order'];
		array_multisort($sort_order, SORT_ASC, $quote_data);
		
		$this->session->data['shipping_methods'] = $quote_data;
		
		// Format and return shipping rates
		$settings = $this->getSettings();
		
		$output = array();
		$currency = $settings['currencies_' . $this->session->data['currency']];
		
		foreach ($quote_data as $quote) {
			foreach ($quote['quote'] as $rate) {
				$display_name = (isset($rate['name'])) ? $rate['name'] : $rate['title'];
				
				if (empty($this->request->post['payment_type']) || $this->request->post['payment_type'] != 'google_pay') {
					$display_name = strip_tags($display_name);
				}
				
				$output['shipping_rates'][] = array(
					'id'			=> $rate['code'],
					'amount'		=> $this->formatAmount($rate['cost'], $currency),
					'displayName'	=> $display_name,
				);
			}
		}
		
		if (empty($output['shipping_rates'])) {
			$this->load->language('checkout/checkout');
			$no_shipping = sprintf($this->language->get('error_no_shipping'), '');
			$no_shipping = strip_tags($no_shipping);
			
			echo json_encode(array('error_message' => $no_shipping));
			return;
		}
		
		$output['shipping_rates'] = array_slice($output['shipping_rates'], 0, 9);
		
		$this->setShippingRate($output['shipping_rates'][0]['id']);
		
		// Set other output data
		$this->load->model($settings['extension_route']);
		$order_info = $this->{'model_' . str_replace('/', '_', $settings['extension_route'])}->getOrderInfo();
		
		$output['amount'] = $this->formatAmount($order_info['total'], $currency);
		$output['line_items'] = $this->getLineItems($settings, $order_info);
		
		// Return output
		echo json_encode($output);
	}
	
	//==============================================================================
	// setShippingRate()
	//==============================================================================
	private function setShippingRate($shipping_rate_id) {
		if ($shipping_rate_id == 'tbd') {
			return;
		}
		
		$shipping = explode('.', $shipping_rate_id);
		$shipping_method = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
		
		if (version_compare(VERSION, '4.0', '<')) {
			$this->session->data['shipping_method'] = $shipping_method;
		} elseif (version_compare(VERSION, '4.0.2.0', '<')) {
			$this->session->data['shipping_method'] = $shipping_rate_id;
		} else {
			$this->session->data['shipping_method'] = $shipping_method;
		}
	}
	
	//==============================================================================
	// updateLineItems()
	//==============================================================================
	public function updateLineItems() {
		$this->setShippingRate($this->request->post['shipping_rate']['id']);
		
		$settings = $this->getSettings();
		
		$this->load->model($settings['extension_route']);
		$order_info = $this->{'model_' . str_replace('/', '_', $settings['extension_route'])}->getOrderInfo();
		
		$output = array(
			'amount'		=> $this->formatAmount($order_info['total'], $settings['currencies_' . $order_info['currency_code']]),
			'line_items'	=> $this->getLineItems($settings, $order_info),
		);
		
		echo json_encode($output);
	}
	
	//==============================================================================
	// createPaymentIntent()
	//==============================================================================
	public function createPaymentIntent() {
		$settings = $this->getSettings();
		$event = $this->request->post['event'];
		
		// Check if customer already has an account if attempting to auto-create one
		if ($settings['express_autocreate_account'] && !$settings['express_autologin'] && !$this->customer->isLogged() && !empty($event['billingDetails']['email'])) {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LCASE(email) = '" . $this->db->escape(strtolower($event['billingDetails']['email'])) . "'");
			
			if ($customer_query->num_rows) {
				if (!empty($this->request->post['current_page'])) {
					$this->session->data['redirect'] = $this->request->post['current_page'];
				}
				
				$this->load->language('account/register');
				$language = (!empty($this->session->data['language'])) ? $this->session->data['language'] : $this->config->get('config_language');
				$account_link = $this->url->link('account/login', version_compare(VERSION, '4.0', '<') ? '' : 'language=' . $language, 'SSL');
				$error_message = $this->language->get('error_exists') . ' ' . sprintf($this->language->get('text_account_already'), $account_link);
				
				echo json_encode(array('error_message' => $error_message));
				return;
			}
		}
		
		// Set up order data
		$this->load->model($settings['extension_route']);
		$order_info = $this->{'model_' . str_replace('/', '_', $settings['extension_route'])}->getOrderInfo();
		
		if (!empty($event['billingDetails']['name'])) {
			$billing_name = explode(' ', $event['billingDetails']['name']);
		} else {
			$billing_name = array('', '');
		}
		
		if (!empty($event['shippingAddress']['name'])) {
			$shipping_name = explode(' ', $event['shippingAddress']['name']);
		} else {
			$shipping_name = array('', '');
		}
		
		$order_info['email'] = (!empty($event['billingDetails']['email'])) ? $event['billingDetails']['email'] : '';
		$order_info['telephone'] = (!empty($event['billingDetails']['phone'])) ? $event['billingDetails']['phone'] : '';
		$order_info['firstname'] = $billing_name[0];
		$order_info['lastname'] = (!empty($billing_name[1])) ? $billing_name[1] : '';
		$order_info['payment_firstname'] = $order_info['firstname'];
		$order_info['payment_lastname'] = $order_info['lastname'];
		$order_info['shipping_firstname'] = $shipping_name[0];
		$order_info['shipping_lastname'] = (!empty($shipping_name[1])) ? $shipping_name[1] : '';
		
		foreach (array('payment', 'shipping') as $address_type) {
			if ($address_type == 'payment' && empty($event['billingDetails']['address']) || $address_type == 'shipping' && empty($event['shippingAddress']['address'])) {
				continue;
			}
			
			$address = ($address_type == 'payment') ? $event['billingDetails']['address'] : $event['shippingAddress']['address'];
			
			if (version_compare(VERSION, '4.1', '<')) {
				$country_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE iso_code_2 = '" . $this->db->escape($address['country']) . "'");
			} else {
				$country_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country c LEFT JOIN " . DB_PREFIX . "country_description cd ON (cd.country_id = c.country_id) WHERE c.iso_code_2 = '" . $this->db->escape($address['country']) . "' AND cd.language_id = " . (int)$this->config->get('config_language_id'));
			}
			
			$country_id = (!empty($country_query->row['country_id'])) ? $country_query->row['country_id'] : 0;
			$country_name = (!empty($country_query->row['name'])) ? $country_query->row['name'] : '';
			
			if ($country_id == 222) {
				$zone = $this->getUkZone($address['postal_code']);
			} elseif (version_compare(VERSION, '4.1', '<')) {
				$zone = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = " . (int)$country_id . " AND (`code` = '" . $this->db->escape($address['state']) . "' OR `name` = '" . $this->db->escape($address['state']) . "')")->row;
			} else {
				$zone = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone z LEFT JOIN " . DB_PREFIX . "zone_description zd ON (zd.zone_id = z.zone_id) WHERE z.country_id = " . (int)$country_id . " AND (z.code = '" . $this->db->escape($address['state']) . "' OR zd.name = '" . $this->db->escape($address['state']) . "') AND zd.language_id = " . (int)$this->config->get('config_language_id'))->row;
			}
			
			$zone_id = (!empty($zone['zone_id'])) ? $zone['zone_id'] : 0;
			$zone_name = (!empty($zone['name'])) ? $zone['name'] : '';
			
			$order_info[$address_type . '_address_1'] = (!empty($address['line1'])) ? $address['line1'] : '';
			$order_info[$address_type . '_address_2'] = (!empty($address['line2'])) ? $address['line2'] : '';
			$order_info[$address_type . '_city'] = $address['city'];
			$order_info[$address_type . '_postcode'] = $address['postal_code'];
			$order_info[$address_type . '_zone'] = $zone_name;
			$order_info[$address_type . '_zone_id'] = $zone_id;
			$order_info[$address_type . '_country'] = $country_name;
			$order_info[$address_type . '_country_id'] = $country_id;
		}
		
		// Change customer_id and customer_group_id if set to automatically log the customer in
		if ($settings['express_autologin']) {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LCASE(email) = '" . $this->db->escape(strtolower($order_info['email'])) . "'");
			
			if ($customer_query->num_rows) {
				$order_info['customer_id'] = $customer_query->row['customer_id'];
				$order_info['customer_group_id'] = $customer_query->row['customer_group_id'];
			}
		}
		
		// Auto-create customer account for guests if enabled
		$new_customer = array();
		$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LCASE(email) = '" . $this->db->escape(strtolower($order_info['email'])) . "'");
		
		if ($settings['express_autocreate_account'] && !$customer_query->num_rows) {
			// Create customer
			$new_customer = array(
				'customer_group_id'	=> $order_info['customer_group_id'],
				'firstname'			=> $order_info['firstname'],
				'lastname'			=> $order_info['lastname'],
				'email'				=> $order_info['email'],
				'telephone'			=> $order_info['telephone'],
				'password'			=> substr(bin2hex(random_bytes(32)), 0, 32),
				'custom_field'		=> '',
				'newsletter'		=> 0,
				'status'			=> 1,
			);
			
			$this->load->model('account/customer');
			$order_info['customer_id'] = $this->model_account_customer->addCustomer($new_customer);
			
			// Create address(es)
			$new_address = array(
				'customer_id'	=> $order_info['customer_id'],
				'firstname'		=> $order_info['firstname'],
				'lastname'		=> $order_info['lastname'],
				'company'		=> '',
				'address_1'		=> $order_info['payment_address_1'],
				'address_2'		=> $order_info['payment_address_2'],
				'city'			=> $order_info['payment_city'],
				'postcode'		=> $order_info['payment_postcode'],
				'zone_id'		=> $order_info['payment_zone_id'],
				'country_id'	=> $order_info['payment_country_id'],
				'custom_field'	=> '',
				'default'		=> 1,
			);
			
			$this->load->model('account/address');
			$order_info['payment_address_id'] = $this->model_account_address->addAddress($order_info['customer_id'], $new_address);
			
			if (version_compare(VERSION, '4.0', '<')) {
				$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = " . (int)$order_info['payment_address_id'] . " WHERE customer_id = " . (int)$order_info['customer_id']);
			}
			
			if (!empty($order_info['shipping_address_1']) && $order_info['shipping_address_1'] != $order_info['payment_address_1']) {
				$new_address = array(
					'customer_id'	=> $order_info['customer_id'],
					'firstname'		=> $order_info['firstname'],
					'lastname'		=> $order_info['lastname'],
					'company'		=> '',
					'address_1'		=> $order_info['shipping_address_1'],
					'address_2'		=> $order_info['shipping_address_2'],
					'city'			=> $order_info['shipping_city'],
					'postcode'		=> $order_info['shipping_postcode'],
					'zone_id'		=> $order_info['shipping_zone_id'],
					'country_id'	=> $order_info['shipping_country_id'],
					'custom_field'	=> '',
					'default'		=> 0,
				);
				
				$this->load->model('account/address');
				$order_info['shipping_address_id'] = $this->model_account_address->addAddress($order_info['customer_id'], $new_address);
			}
			
			// Send forgotten password email
			$this->request->post['email'] = $new_customer['email'];
			$forgotten_password_url = str_replace('http://', 'https://', HTTP_SERVER) . 'index.php?route=account/forgotten' . (version_compare(VERSION, '4.0', '<') ? '' : '.confirm');
			
			$curl = curl_init($forgotten_password_url);
			curl_setopt_array($curl, array(
				CURLOPT_CONNECTTIMEOUT	=> 5,
				CURLOPT_POST			=> true,
				CURLOPT_POSTFIELDS		=> 'email=' . $new_customer['email'],
				CURLOPT_RETURNTRANSFER	=> true,
				CURLOPT_TIMEOUT			=> 5,
			));
			curl_exec($curl);
			curl_close($curl);
		}
		
		// Create order
		unset($order_info['products']);
		$this->session->data['order_id'] = $this->{'model_' . str_replace('/', '_', $settings['extension_route'])}->createOrder($order_info);
		$order_info = $this->{'model_' . str_replace('/', '_', $settings['extension_route'])}->getOrderInfo();
		
		// Check for shipping on the order
		if ($this->cart->hasShipping()) {
			$shipping_line = false;
			
			foreach ($order_info['line_items'] as $line_item) {
				if ($line_item['code'] == 'shipping') {
					$shipping_line = true;
				}
			}
			
			if (!$shipping_line) {
				$this->load->language('checkout/checkout');
				$no_shipping = sprintf($this->language->get('error_no_shipping'), '');
				$no_shipping = strip_tags($no_shipping);
				
				echo json_encode(array('error_message' => $no_shipping));
				return;
			}
		}
		
		// Add order history note
		$payment_type = (!empty($event['expressPaymentType'])) ? ucwords(str_replace('_', ' ', $event['expressPaymentType'])) : '';
		$comment = ($payment_type == 'Link' ? 'Stripe ' : '') . $payment_type . ' payment initiated using the Express Checkout button';
		$order_status_id = (!empty($settings['initial_status_id'])) ? $settings['initial_status_id'] : $settings['success_status_id'];
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = " . (int)$order_info['order_id'] . ", order_status_id = " . (int)$order_status_id . ", notify = 0, comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
		
		// Get Stripe customer_id
		$stripe_customer_id = '';
		
		if ($order_info['customer_id']) {
			$customer_id_query = $this->db->query("SELECT * FROM " . DB_PREFIX . $this->extension . "_customer WHERE customer_id = " . (int)$order_info['customer_id'] . " AND transaction_mode = '" . $this->db->escape($settings['transaction_mode']) . "'");
			
			if ($customer_id_query->num_rows) {
				$customer_response = $this->curlRequest('GET', 'customers/' . $customer_id_query->row['stripe_customer_id']);
				
				if (!empty($customer_response['error']) || !empty($customer_response['deleted'])) {
					$this->db->query("DELETE FROM " . DB_PREFIX . $this->extension . "_customer WHERE stripe_customer_id = '" . $this->db->escape($customer_id_query->row['stripe_customer_id']) . "' AND transaction_mode = '" . $this->db->escape($settings['transaction_mode']) . "'");
				} else {
					$stripe_customer_id = $customer_id_query->row['stripe_customer_id'];
				}
			}
		}
		
		// Set up PaymentIntent data
		if (version_compare(VERSION, '4.0', '<')) {
			$separator = '/';
		} elseif (version_compare(VERSION, '4.0.2.0', '<')) {
			$separator = '|';
		} else {
			$separator = '.';
		}
		
		$currency = $settings['currencies_' . $order_info['currency_code']];
		
		$curl_data = array(
			'amount'					=> $this->formatAmount($order_info['total'], $currency),
			'automatic_payment_methods'	=> array('enabled' => 'true'),
			'capture_method'			=> 'manual',
			'confirm'					=> 'true',
			'confirmation_token'		=> $this->request->post['confirmation_token'],
			'currency'					=> strtolower($currency),
			'description'				=> $this->{'model_' . str_replace('/', '_', $settings['extension_route'])}->replaceShortcodes($settings['transaction_description'], $order_info),
			'metadata'					=> $this->{'model_' . str_replace('/', '_', $settings['extension_route'])}->metadata($order_info),
			'return_url'				=> $this->config->get('config_url') . 'index.php?route=' . $settings['extension_route'] . $separator . 'paymentComplete',
		);
		
		if ($stripe_customer_id) {
			$curl_data['customer'] = $stripe_customer_id;
		}
		
		// Create PaymentIntent
		$payment_intent = $this->curlRequest('POST', 'payment_intents', $curl_data);
		
		if (!empty($payment_intent['error'])) {
			echo json_encode(array('error_message' => $payment_intent['error']['message']));
			return;
		}
		
		$this->session->data['payment_intent_id'] = $payment_intent['id'];
		
		// Return response
		if (!empty($new_customer) || $settings['express_autologin']) {
			$this->session->data['login_after_payment_is_complete'] = $order_info['email'];
		}
		
		echo json_encode(array(
			'client_secret' => $payment_intent['client_secret'],
			'status'		=> $payment_intent['status'],
		));
	}
	
	//==============================================================================
	// finalizePayment()
	//==============================================================================
	public function finalizePayment() {
		if (version_compare(VERSION, '4.0', '<')) {
			$separator = '/';
		} elseif (version_compare(VERSION, '4.0.2.0', '<')) {
			$separator = '|';
		} else {
			$separator = '.';
		}
		
		$this->session->data['finalized_by_webhook'] = true;
		
		ob_start();
		$this->load->controller('extension/' . (version_compare(VERSION, '4.0', '<') ? '' : $this->extension . '/') . 'payment/' . $this->extension . $separator . 'finalizePayment');
		$output = ob_get_clean();
		
		if ($output) {
			echo $output;
		} else {
			if (!empty($this->session->data['login_after_payment_is_complete'])) {
				$this->customer->login($this->session->data['login_after_payment_is_complete'], '', true);
				unset($this->session->data['login_after_payment_is_complete']);
				
				if (version_compare(VERSION, '4.0', '>=')) {
					$this->session->data['customer_token'] = substr(bin2hex(random_bytes(26)), 0, 26);
				}
			}
			
			echo json_encode(array());
		}
	}
	
	//==============================================================================
	// formatAmount()
	//==============================================================================
	private function formatAmount($amount, $currency) {
		$main_currency = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `key` = 'config_currency' AND store_id = 0 ORDER BY setting_id DESC LIMIT 1")->row['value'];
		$decimal_factor = (in_array($currency, array('BIF','CLP','DJF','GNF','JPY','KMF','KRW','MGA','PYG','RWF','UGX','VND','VUV','XAF','XOF','XPF'))) ? 1 : 100;
		$three_decimal_factor = (in_array($currency, array('BHD','JOD','KWD','OMR','TND'))) ? 10 : 1;
		
		return round($decimal_factor * $this->currency->convert($amount, $main_currency, $currency)) * $three_decimal_factor;
	}
	
	//==============================================================================
	// getLineItems()
	//==============================================================================
	private function getLineItems($settings, $order_info) {
		$line_items = array();
		$currency = $settings['currencies_' . $order_info['currency_code']];
		
		foreach ($order_info['line_items'] as $line_item) {
			if ($line_item['code'] == 'total') {
				continue;
			}
			
			$line_items[] = array(
				'name'		=> strip_tags($line_item['title']),
				'amount'	=> $this->formatAmount($line_item['value'], $currency),
			);
		}
		
		return $line_items;
	}
	
	//==============================================================================
	// getUkZone()
	//==============================================================================
	private function getUkZone($postcode) {
		$uk_mapping = array('Aberdeen'=>['AB10','AB11','AB12','AB13','AB14','AB15','AB16','AB21','AB22'],'Aberdeenshire'=>['AB30','AB31','AB32','AB33','AB34','AB35','AB36','AB37','AB38'],'Anglesey'=>['LL58','LL59','LL60','LL61','LL62','LL63','LL64','LL65','LL66'],'Angus'=>['DD8','DD9','DD10','DD11'],'Argyll and Bute'=>['PA20','PA21','PA22','PA23','PA24','PA25','PA26','PA27','PA28','PA29'],'Bedfordshire'=>['LU1','LU2','LU3','LU4','LU5','LU6','LU7','MK40','MK41','MK42','MK43','MK44'],'Berkshire'=>['RG1','RG2','RG3','RG4','RG5','RG6','RG7','RG8','RG9','SL4','SL5'],'Blaenau Gwent'=>['NP13','NP22','NP23'],'Bridgend'=>['CF31','CF32','CF33','CF34','CF35'],'Bristol'=>['BS1','BS2','BS3','BS4','BS5','BS6','BS7','BS8','BS9','BS10','BS11','BS13','BS14','BS15','BS16'],'Buckinghamshire'=>['HP10','HP11','HP12','HP13','HP14','HP15','HP16','HP17','HP18','HP19','HP20','HP21','HP22','MK18'],'Caerphilly'=>['CF81','CF82','CF83','NP11','NP12'],'Cambridgeshire'=>['CB1','CB2','CB3','CB4','CB5','CB6','CB7','CB8','CB9','PE19'],'Cardiff'=>['CF1','CF2','CF3','CF5','CF10','CF11','CF14','CF15','CF23','CF24','CF30','CF91','CF95','CF99'],'Carmarthenshire'=>['SA14','SA15','SA16','SA17','SA18','SA19','SA20','SA31','SA32','SA33','SA34'],'Ceredigion'=>['SA35','SA36','SA37','SA38','SA39','SA40','SY23','SY24','SY25'],'Cheshire'=>['CH1','CH2','CH3','CH4','CH5','CH6','CH7','CH8','CW1','CW2','CW3','CW4','CW5','CW6','CW7','CW8','CW9','CW10','CW11','WA1','WA2','WA3','WA4','WA5','WA6','WA7','WA8','WA9','WA10','WA11','WA12','WA13','WA14','WA15','SK9','SK10','SK11'],'Clackmannanshire'=>['FK10','FK11','FK12','FK13','FK14'],'Conwy'=>['LL16','LL17','LL18','LL19','LL20','LL21','LL22','LL23','LL24','LL25','LL26','LL27','LL28','LL29','LL30','LL31','LL32','LL33','LL34'],'Cornwall'=>['PL10','PL11','PL12','PL13','PL14','PL15','PL16','PL17','PL18','PL19', 'PL22','PL23','PL24','PL25','PL26','PL27','PL28','PL29','PL30','PL31', 'PL32','PL33','PL34','PL35', 'TR1','TR2','TR3','TR4','TR5','TR6','TR7','TR8','TR9','TR10', 'TR11','TR12','TR13','TR14','TR15','TR16','TR17','TR18','TR19','TR20', 'TR21','TR22','TR23','TR24','TR25','TR26','TR27'],'County Antrim'=>['BT1','BT2','BT3','BT4','BT5','BT6','BT7','BT8','BT9','BT10','BT11','BT12','BT13','BT14','BT15','BT16','BT17'],'County Armagh'=>['BT60','BT61'],'County Down'=>['BT18','BT19','BT20','BT21','BT22','BT23','BT24','BT25','BT26','BT27','BT28','BT29'],'County Fermanagh'=>['BT92','BT93','BT94'],'County Londonderry'=>['BT47','BT48','BT49','BT50','BT51'],'County Tyrone'=>['BT70','BT71','BT72','BT73','BT74','BT75'],'Cumbria'=>['CA1','CA2','CA3','CA4','CA5','CA6','CA7','CA8','CA9','CA10','CA11','CA12','CA13','CA14','CA15','CA16','CA17','CA18','CA19','CA20','CA21','CA22','CA23','CA24','CA25','CA26','CA27','LA22','LA23'],'Denbighshire'=>['LL13','LL14','LL15'],'Derbyshire'=>['DE1','DE3','DE4','DE5'],'Devon'=>['EX1','EX2','EX3','EX4','EX5','EX6','EX7','EX8','EX9'],'Dorset'=>['BH1','BH2','BH3','DT1','DT2','DT3'],'Dumfries and Galloway'=>['DG1','DG2','DG3','DG4'],'Dundee'=>['DD1','DD2','DD3','DD4','DD5'],'Durham'=>['DH1','DH2','DH3','DH4'],'East Ayrshire'=>['KA1','KA2','KA3'],'East Dunbartonshire'=>['G66','G67','G68'],'East Lothian'=>['EH30','EH31','EH32','EH33','EH34'],'East Renfrewshire'=>['G77','G78'],'East Riding of Yorkshire'=>['HU1','HU2','HU3','HU4','HU5'],'East Sussex'=>['BN1','BN2','BN3','BN4','BN5'],'Edinburgh'=>['EH1','EH2','EH3','EH4','EH5','EH6','EH7','EH8','EH9','EH10','EH11','EH12','EH13','EH14'],'Essex'=>['CM1','CM2','CM3','CM4','CM5','CM6','CM7','CM8','CM9','IP1','IP2'],'Falkirk'=>['FK1','FK2','FK3','FK4','FK5','FK6','FK7','FK8'],'Fife'=>['KY1','KY2','KY3','KY4','KY5','KY6','KY7','KY8'],'Flintshire'=>['CH7','CH8','CH9'],'Glasgow'=>['G1','G2','G3','G4','G5','G6','G11','G12','G13','G14','G15'],'Gloucestershire'=>['GL1','GL2','GL3','GL4','GL5','GL6','GL7','GL8'],'Greater London'=>['E1','E2','E3','E4','E5','E6','E7','E8','E9','E10','E11','E12','E13','E14','E15','E16','E17','E18', 'EC1','EC2','EC3','EC4', 'N1','N2','N3','N4','N5','N6','N7','N8','N9','N10','N11','N12','N13','N14','N15','N16','N17','N18','N19','N20','N21','N22', 'NW1','NW2','NW3','NW4','NW5','NW6','NW7','NW8','NW9','NW10','NW11', 'SE1','SE2','SE3','SE4','SE5','SE6','SE7','SE8','SE9','SE10','SE11','SE12','SE13','SE14','SE15','SE16','SE17','SE18','SE19','SE20','SE21','SE22','SE23','SE24','SE25','SE26','SE27','SE28', 'SW1','SW2','SW3','SW4','SW5','SW6','SW7','SW8','SW9','SW10','SW11','SW12','SW13','SW14','SW15','SW16','SW17','SW18','SW19','SW20', 'W1','W2','W3','W4','W5','W6','W7','W8','W9','W10','W11','W12','W13','W14', 'WC1','WC2'], 'Greater Manchester'=>['M1','M2','M3','M4','M5','M6','M7','M8','M9','M10', 'M11','M12','M13','M14','M15','M16','M17','M18','M19','M20', 'OL1','OL2','OL3','OL4','OL5','OL6','OL7','OL8','OL9','OL10', 'OL11','OL12','OL13','OL14','OL15','OL16'],'Gwynedd'=>['LL53','LL54','LL55','LL56','LL57'],'Hampshire'=>['PO1','PO2','PO3','PO4','PO5','PO6','PO7','PO8','PO9','PO10','SO16','SO17','SO18','SO19','SO20','SO21','SO22'],'Herefordshire'=>['HR1','HR2','HR3','HR4','HR5'],'Hertfordshire'=>['AL1','AL2','AL3','AL4','AL5','AL6','AL7'],'Highlands'=>['IV1','IV2','IV3','IV4','IV5','IV6','IV7','IV8','IV9','IV10','IV11','IV12','IV30','IV31'],'Inverclyde'=>['PA12','PA13','PA14','PA15','PA16'],'Isle of Wight'=>['PO30','PO31','PO32','PO33','PO34'],'Kent'=>['CT1','CT2','CT3','CT4','CT5','CT6','CT7','CT8','DA1','DA2','DA3','DA4','DA5','DA6','DA7'],'Lancashire'=>['BB1','BB2','BB3','BB4','FY1','FY2','FY3','FY4','FY5'],'Leicestershire'=>['LE1','LE2','LE3','LE4','LE5','LE6','LE7','LE8','LE9'],'Lincolnshire'=>['LN1','LN2','LN3','LN4','LN5','LN6','LN7','LN8','LN9'],'Merseyside'=>['L1','L2','L3','L4','L5','L6','L7','L8','L9','L10','L11','L12','L13','L14','L15','L16','L17','L18','L19'],'Merthyr Tydfil'=>['CF48','CF49'],'Midlothian'=>['EH25','EH26','EH27','EH28'],'Monmouthshire'=>['NP4','NP5','NP6','NP7'],'Moray'=>['IV15','IV16','IV17'],'Neath Port Talbot'=>['SA10','SA11','SA12','SA13'],'Newport'=>['NP10','NP11','NP12','NP15'],'Norfolk'=>['NR1','NR2','NR3','NR4','NR5','NR6','NR7','NR8','NR9','NR10','NR11','NR12','NR13','NR14','NR15'],'North Ayrshire'=>['KA7','KA8','KA9'],'North Lanarkshire'=>['ML1','ML2','ML3','ML4'],'North Yorkshire'=>['YO1','YO10','YO11','YO12','YO13','YO14','YO15','YO16','YO17','YO18','YO19','YO20','YO21','YO22','YO23','YO24','YO25','YO26','YO30'],'Northamptonshire'=>['NN1','NN2','NN3','NN4','NN5','NN6','NN7','NN8','NN9'],'Northumberland'=>['NE1','NE2','NE3','NE4','NE5','NE6','NE7','NE8','NE9','NE10'],'Nottinghamshire'=>['NG1','NG2','NG3','NG4','NG5','NG6','NG7','NG8','NG9','NG10','NG11','NG12'],'Orkney Islands'=>['KW1','KW2','KW3','KW4'],'Oxfordshire'=>['OX1','OX2','OX3','OX4','OX5','OX6','OX7','OX8'],'Pembrokeshire'=>['SA71','SA72','SA73','SA74'],'Perth and Kinross'=>['PH1','PH2','PH3','PH4','PH5','PH6'],'Powys'=>['SY1','SY2','SY3','SY4','SY5'],'Renfrewshire'=>['PA1','PA2','PA3','PA4'],'Rhondda Cynon Taff'=>['CF41','CF42','CF43','CF44'],'Rutland'=>['LE15','LE16'],'Scottish Borders'=>['TD1','TD2','TD3','TD4'],'Shetland Islands'=>['ZE1','ZE2'],'Shropshire'=>['SY1','SY2','SY3'],'Somerset'=>['TA1','TA2','TA3','TA4','TA5','TA6'],'South Ayrshire'=>['KA10','KA11'],'South Lanarkshire'=>['ML5','ML6'],'South Yorkshire'=>['S1','S2','S3','S4','S5','S6','S7','S8','S9','S10','S11','S12'],'Staffordshire'=>['ST1','ST2','ST3','ST4','ST5','ST6'],'Stirling'=>['FK7','FK8','FK9'],'Suffolk'=>['IP1','IP2','IP3','IP4','IP5','IP6','IP7'],'Surrey'=>['KT1','KT2','KT3','KT4','KT5','KT6','KT7','KT8','KT9'],'Swansea'=>['SA1','SA2','SA3','SA4','SA5','SA6','SA7','SA8'],'Torfaen'=>['NP4','NP5','NP6'],'Tyne and Wear'=>['NE27','NE28','NE29','NE30','NE31','NE32'],'Vale of Glamorgan'=>['CF62','CF63','CF64'],'Warwickshire'=>['CV1','CV2','CV3','CV4','CV5','CV6','CV7'],'West Dunbartonshire'=>['G80','G81'],'West Lothian'=>['EH52','EH53'],'West Midlands'=>['B1','B2','B3','B4','B5','B6','B7','B8','B9','B10','DY1','DY2','DY3','DY4','DY5','WV1','WV2','WV3','WV4','WV5'],'West Sussex'=>['BN10','BN11','BN12','RH10','RH11','RH12','RH13'],'West Yorkshire'=>['BD1','BD2','BD3','LS1','LS2','LS3','LS4','LS5','LS6','LS7','LS8','LS9','LS10','LS11','WF1','WF2','WF3','WF4','WF5','WF6'],'Western Isles'=>['HS1','HS2','HS3','HS4'],'Wiltshire'=>['SN1','SN2','SN3','SN4','SN5','SN6','SN7','SN8'],'Worcestershire'=>['WR1','WR2','WR3','WR4','WR5','WR6','WR7'],'Wrexham'=>['LL11','LL12']);
		
		$region_name = '';
		$postal_code = (strlen($postcode) > 4) ? substr($postcode, 0, -3) : $postcode;
		$postal_code = trim(strtoupper($postal_code));
		
		foreach ($uk_mapping as $region => $postcodes) {
			if (in_array($postal_code, $postcodes)) {
				$region_name = $region;
			}
		}
		
		if (version_compare(VERSION, '4.1', '<')) {
			$zone_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = 222 AND `name` = '" . $this->db->escape($region_name) . "'");
		} else {
			$zone_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone z LEFT JOIN " . DB_PREFIX . "zone_description zd ON (zd.zone_id = z.zone_id) WHERE z.country_id = 222 AND zd.name = '" . $this->db->escape($region_name) . "' AND zd.language_id = " . (int)$this->config->get('config_language_id'));
		}
		
		return $zone_query->row;
	}
	
	//==============================================================================
	// getSettings()
	//==============================================================================
	private function getSettings() {
		//$code = (version_compare(VERSION, '3.0', '<') ? '' : $this->type . '_') . $this->name;
		$code = (version_compare(VERSION, '3.0', '<') ? '' : 'payment_') . $this->extension;
		
		$settings = array();
		$settings_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `key` ASC");
		
		foreach ($settings_query->rows as $setting) {
			$value = $setting['value'];
			if ($setting['serialized']) {
				$value = (version_compare(VERSION, '2.1', '<')) ? unserialize($setting['value']) : json_decode($setting['value'], true);
			}
			$split_key = preg_split('/_(\d+)_?/', str_replace($code . '_', '', $setting['key']), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			
				if (count($split_key) == 1)	$settings[$split_key[0]] = $value;
			elseif (count($split_key) == 2)	$settings[$split_key[0]][$split_key[1]] = $value;
			elseif (count($split_key) == 3)	$settings[$split_key[0]][$split_key[1]][$split_key[2]] = $value;
			elseif (count($split_key) == 4)	$settings[$split_key[0]][$split_key[1]][$split_key[2]][$split_key[3]] = $value;
			else 							$settings[$split_key[0]][$split_key[1]][$split_key[2]][$split_key[3]][$split_key[4]] = $value;
		}
		
		/*
		if (version_compare(VERSION, '4.0', '<')) {
			$settings['extension_route'] = 'extension/' . $this->type . '/' . $this->name;
		} else {
			$settings['extension_route'] = 'extension/' . $this->name . '/' . $this->type . '/' . $this->name;
		}
		*/
		
		if (version_compare(VERSION, '4.0', '<')) {
			$settings['extension_route'] = 'extension/payment/' . $this->extension;
		} else {
			$settings['extension_route'] = 'extension/' . $this->extension . '/payment/' . $this->extension;
		}
		
		return $settings;
	}
	
	//==============================================================================
	// curlRequest()
	//==============================================================================
	private function curlRequest($request, $api, $data = array()) {
		if (version_compare(VERSION, '4.0', '<')) {
			$this->load->model('extension/payment/' . $this->extension);
			return $this->{'model_extension_payment_'.$this->extension}->curlRequest($request, $api, $data);
		} else {
			$this->load->model('extension/' . $this->extension . '/payment/' . $this->extension);
			return $this->{'model_extension_'.$this->extension.'_payment_'.$this->extension}->curlRequest($request, $api, $data);
		}
	}
}
?>