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
//class QuickBuy extends \Opencart\System\Engine\Controller {

class ControllerExtensionQuickBuy extends Controller {
	
	private $extension = 'stripe';
	private $type = 'extension';
	private $name = 'quick_buy';
	
	//==============================================================================
	// index()
	//==============================================================================
	public function index() {
		$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : 'payment_';
		$language = (!empty($this->session->data['language'])) ? $this->session->data['language'] : $this->config->get('config_language');
		
		if (!array_intersect(array($this->config->get('config_store_id')), explode(';', $this->config->get($prefix . $this->extension . '_stores'))) ||
			!array_intersect(array((int)$this->customer->getGroupId()), explode(';', $this->config->get($prefix . $this->extension . '_customer_groups'))) ||
			empty($this->config->get($prefix . $this->extension . '_currencies_' . $this->session->data['currency']))
		) {
			return;
		}
		
		if (!empty($this->session->data['shipping_address']['country_id']) && !empty($this->session->data['shipping_address']['zone_id'])) {
			$current_geozones = array();
			$geozones = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE country_id = " . (int)$this->session->data['shipping_address']['country_id'] . " AND (zone_id = 0 OR zone_id = " . (int)$this->session->data['shipping_address']['zone_id'] . ")");
			foreach ($geozones->rows as $geozone) $current_geozones[] = $geozone['geo_zone_id'];
			if (empty($current_geozones)) $current_geozones = array(0);
			
			$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : 'payment_';
			if (!array_intersect($current_geozones, explode(';', $this->config->get($prefix . $this->extension . '_geo_zones')))) {
				return;
			}
		}
		
		$button_html = $this->config->get($prefix . $this->extension . '_' . $this->name . '_html');
		$button_text = $this->config->get($prefix . $this->extension . '_' . $this->name . '_text_' . $language);
		$quick_buy_html = str_replace('[button_text]', $button_text, $button_html);
		
		echo html_entity_decode($quick_buy_html, ENT_QUOTES, 'UTF-8');
	}
	
	//==============================================================================
	// start()
	//==============================================================================
	public function start() {
		if ($this->customer->isLogged() && $this->customer->getAddressId()) {
			if (empty($this->session->data['shipping_address'])) {
				$this->session->data['shipping_address'] = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE address_id = " . (int)$this->customer->getAddressId())->row;
			}
			$customer_group_id = (int)$this->customer->getGroupId();
		} else {
			$customer_group_id = (int)$this->config->get('config_customer_group_id');
		}
		
		$this->session->data[version_compare(VERSION, '4.0', '<') ? 'guest' : 'customer']['customer_group_id'] = $customer_group_id;
		
		if (!empty($this->request->post)) {
			$already_in_cart = false;
			
			foreach ($this->cart->getProducts() as $product) {
				if ($product['product_id'] == $this->request->post['product_id']) {
					$already_in_cart = true;
				}
			}
			
			if (!$already_in_cart) {
				$quantity = (!empty($this->request->post['quantity'])) ? $this->request->post['quantity'] : 1;
				$options = (!empty($this->request->post['option'])) ? array_filter($this->request->post['option']) : array();
				
				$this->session->data[$this->name . '_quick_buy_error'] = '';
				$this->load->model('catalog/product');
				
				if (version_compare(VERSION, '4.0', '<')) {
					$product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);
				} else {
					$product_options = $this->model_catalog_product->getOptions($this->request->post['product_id']);
				}
				
				foreach ($product_options as $product_option) {
					if ($product_option['required'] && empty($options[$product_option['product_option_id']])) {
						$this->load->language('checkout/cart');
						$this->session->data[$this->name . '_quick_buy_error'] = sprintf($this->language->get('error_required'), $product_option['name']);
					}
				}
				
				if ($this->session->data[$this->name . '_quick_buy_error']) {
					echo $this->session->data[$this->name . '_quick_buy_error'];
					return;
				}
				
				$this->cart->add($this->request->post['product_id'], $this->request->post['quantity'], $options);
			}
		}
		
		if (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) {
			$this->load->language('checkout/cart');
			$this->session->data[$this->name . '_quick_buy_error'] = str_replace('marked with *** ', '', $this->language->get('error_stock'));
			echo $this->session->data[$this->name . '_quick_buy_error'];
			return;
		}
		
		if (!$this->cart->hasShipping()) {
			return;
		} else {
			$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : 'payment_';
			$language = (!empty($this->session->data['language'])) ? $this->session->data['language'] : $this->config->get('config_language');
			$text = $this->config->get($prefix . $this->extension . '_' . $this->name . '_address_text_' . $language);
			echo html_entity_decode($text, ENT_QUOTES, 'UTF-8');
		}
	}
	
	//==============================================================================
	// loadShipping()
	//==============================================================================
	public function loadShipping() {
		if (!empty($this->session->data[$this->name . '_quick_buy_error'])) {
			unset($this->session->data[$this->name . '_quick_buy_error']);
			return;
		}
		
		if (version_compare(VERSION, '4.0', '<')) {
			$this->load->controller('checkout/guest_shipping');
		} else {
			$output = $this->load->controller('checkout/shipping_address');
			$output = str_replace('<fieldset id="shipping-address" style="display: none', '<fieldset id="shipping-address" style="display: block', $output);
			echo html_entity_decode($output, ENT_QUOTES, 'UTF-8');
		}
	}
	
	//==============================================================================
	// setShippingAddress()
	//==============================================================================
	public function setShippingAddress() {
		// Check for empty fields
		$json = array();
		$data = $this->load->language('checkout/checkout');
		
		foreach (array('firstname', 'lastname', 'address_1', 'city', 'postcode', 'country_id', 'zone_id') as $field) {
			if (empty($this->request->post[$field])) {
				if ($field == 'country_id') {
					$json['error_message'] = $data['error_country'];
				} elseif ($field == 'zone_id') {
					$json['error_message'] = $data['error_zone'];
				} else {
					$json['error_message'] = $data['error_' . $field];
				}
				echo json_encode($json);
				return;
			}
		}
		
		// Set address into session data
		$this->session->data['shipping_address'] = $this->request->post;
		
		$this->load->model('localisation/country');
		$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

		if ($country_info) {
			$this->session->data['shipping_address']['country'] = $country_info['name'];
			$this->session->data['shipping_address']['iso_code_2'] = $country_info['iso_code_2'];
			$this->session->data['shipping_address']['iso_code_3'] = $country_info['iso_code_3'];
			$this->session->data['shipping_address']['address_format'] = (!empty($country_info['address_format'])) ? $country_info['address_format'] : '';
		} else {
			$this->session->data['shipping_address']['country'] = '';
			$this->session->data['shipping_address']['iso_code_2'] = '';
			$this->session->data['shipping_address']['iso_code_3'] = '';
			$this->session->data['shipping_address']['address_format'] = '';
		}

		$this->load->model('localisation/zone');
		$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);
		
		if ($zone_info) {
			$this->session->data['shipping_address']['zone'] = $zone_info['name'];
			$this->session->data['shipping_address']['zone_code'] = $zone_info['code'];
		} else {
			$this->session->data['shipping_address']['zone'] = '';
			$this->session->data['shipping_address']['zone_code'] = '';
		}
		
		if (version_compare(VERSION, '2.0', '<')) {
			$this->session->data['guest']['shipping'] = $this->session->data['shipping_address'];
		}
		
		if (version_compare(VERSION, '4.0', '>=')) {
			if (!isset($this->session->data['shipping_address']['address_id'])) {
				$this->session->data['shipping_address']['address_id'] = 0;
			}
			
			if ($this->config->get('config_checkout_payment_address')) {
				$this->session->data['payment_address'] = $this->session->data['shipping_address'];
			}
		}
		
		$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : 'payment_';
		$language = (!empty($this->session->data['language'])) ? $this->session->data['language'] : $this->config->get('config_language');
		$text = $this->config->get($prefix . $this->extension . '_' . $this->name . '_shipping_text_' . $language);
		$json['text'] = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
		
		echo json_encode($json);
	}
	
	//==============================================================================
	// getShippingRates()
	//==============================================================================
	public function getShippingRates() {
		$current_geozones = array();
		$geozones = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE country_id = " . (int)$this->session->data['shipping_address']['country_id'] . " AND (zone_id = 0 OR zone_id = " . (int)$this->session->data['shipping_address']['zone_id'] . ")");
		foreach ($geozones->rows as $geozone) $current_geozones[] = $geozone['geo_zone_id'];
		if (empty($current_geozones)) $current_geozones = array(0);
		
		$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : 'payment_';
		if (!array_intersect($current_geozones, explode(';', $this->config->get($prefix . $this->extension . '_geo_zones')))) {
			$language = (!empty($this->session->data['language'])) ? $this->session->data['language'] : $this->config->get('config_language');
			$error = $this->config->get($prefix . $this->extension . '_' . $this->name . '_geozone_error_' . $language);
			
			echo $error;
			return;
		}
		
		if (version_compare(VERSION, '4.0', '<')) {
			$this->load->controller('checkout/shipping_method');
		} else {
			$output = $this->load->controller('checkout/shipping_method');
			echo html_entity_decode($output, ENT_QUOTES, 'UTF-8');
		}
	}
	
	//==============================================================================
	// createOrder()
	//==============================================================================
	public function createOrder() {
		if (version_compare(VERSION, '4.0', '<')) {
			$extension_route = 'extension/payment/' . $this->extension;
		} else {
			$extension_route = 'extension/' . $this->extension . '/payment/' . $this->extension;
		}
		
		$this->session->data['comment'] = (!empty($this->request->post['comment'])) ? $this->request->post['comment'] : '';
		
		if (!empty($this->request->post['shipping_method'])) {
			if (version_compare(VERSION, '4.0', '<') || version_compare(VERSION, '4.0.2.0', '>=')) {
				$shipping = explode('.', $this->request->post['shipping_method']);
				$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
			} else {
				$this->session->data['shipping_method'] = $this->request->post['shipping_method'];
			}
		}
		
		$this->load->model($extension_route);
		
		unset($this->session->data['order_id']);
		$order_info = $this->{'model_' . str_replace('/', '_', $extension_route)}->getOrderInfo();
		
		unset($order_info['products']);
		$this->session->data['order_id'] = $this->{'model_' . str_replace('/', '_', $extension_route)}->createOrder($order_info);
	}
}
?>