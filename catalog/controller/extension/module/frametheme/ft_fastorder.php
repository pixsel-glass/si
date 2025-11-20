<?php

class ControllerExtensionModuleFramethemeFTFastorder extends Controller {

	private $error = array();

	public function index() {
		$this->load->language('extension/module/frametheme/ft_qoptions');

		$this->load->language('extension/module/frametheme/ft_global');

		$this->load->language('extension/module/frametheme/ft_fastorder');

		$this->load->model('setting/setting');

		$ft_settings = array();

		$ft_settings = $this->model_setting_setting->getSetting('theme_frame', $this->config->get('config_store_id'));

		$language_id = $this->config->get('config_language_id');

		if (isset($ft_settings['t1_high_definition_imgs']) && $ft_settings['t1_high_definition_imgs']){
			$hd_imgs = $ft_settings['t1_high_definition_imgs'];
		} else {
			$hd_imgs = false;
		}

		if (isset($ft_settings['t1_fastorder_phone_mask']) && $ft_settings['t1_fastorder_phone_mask']) {
			$data['phone_mask'] = $ft_settings['t1_fastorder_phone_mask'];
		} else {
			$data['phone_mask'] = '';
		}

		$tax_data = $this->tax->get_tax_inform();

		if (!empty($tax_data)) {
			$data = array_merge($data, $tax_data);
		}

		if (isset($ft_settings['t1_fastorder_quantity_status']) && $ft_settings['t1_fastorder_quantity_status']) {
			$data['fastorder_quantity_status'] = $ft_settings['t1_fastorder_quantity_status'];
		} else {
			$data['fastorder_quantity_status'] = false;
		}

		$data['theme_dir'] = $this->config->get('theme_frame_directory');

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['customer_name'])) {
			$data['customer_name'] = $this->request->post['customer_name'];
		} else {
			$data['customer_name'] = $this->customer->getFirstName();
		}

		if (isset($this->request->post['customer_email'])) {
			$data['customer_email'] = $this->request->post['customer_email'];
		} else {
			$data['customer_email'] = $this->customer->getEmail();
		}

		if (isset($this->request->post['customer_telephone'])) {
			$data['customer_telephone'] = $this->request->post['customer_telephone'];
		} else {
			$data['customer_telephone'] = $this->customer->getTelephone();
		}

		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('guest', (array)$this->config->get('config_captcha_page'))) {
			$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
		} else {
			$data['captcha'] = '';
		}

		$data['show_phone'] = true;

		$data['show_mail'] = false;

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		$data['product_isset'] = false;

		if ($product_info) {

			$data['product_isset'] = true;

			$data['heading_title'] = $product_info['name'];

			$data['product_href'] = $this->url->link('product/product&product_id=' . $product_info['product_id'], '', true);

			$data['product_id'] = (int)$this->request->get['product_id'];

			$data['model'] = $product_info['model'];


			$this->load->model('tool/image');

			$data['thumb_holder'] = $this->model_tool_image->resize('src_holder.png', 220, 220);

			if ($product_info['image']) {

				$data['thumb']   = $this->model_tool_image->resize($product_info['image'], 220, 220);

				$data['thumb2x'] = $hd_imgs ? $this->model_tool_image->resize($product_info['image'], 220*2, 220*2) : NULL;

				$data['thumb3x'] = $hd_imgs ? $this->model_tool_image->resize($product_info['image'], 220*3, 220*3) : NULL;

				$data['thumb4x'] = $hd_imgs ? $this->model_tool_image->resize($product_info['image'], 220*4, 220*4) : NULL;

			} else {
				$data['thumb'] = '';
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['price'] = false;
			}

			if ($this->customer->isLogged() && $data['pixsel_tax_status'] || !$this->config->get('config_customer_price') && $data['pixsel_tax_status']) {
				$data['tax_price'] = $this->currency->format($this->tax->calc_tax($product_info['price']), $this->session->data['currency'], 0, false);
			} else {
				$data['tax_price'] = false;
			}

			$data['retail_price'] = ($this->config->get('config_customer_group_id') != 1 ? round($product_info['retail_price']) : false);

			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['special'] = false;
			}

			$data['price_value'] = $product_info['price'];
         $data['special_value'] = $product_info['special'];

         $var_currency_autocalc = array();
         $currency_code_autocalc = $this->session->data['currency'];
         $var_currency_autocalc['value'] = $this->currency->getValue($currency_code_autocalc);
         $var_currency_autocalc['symbol_left'] = $this->currency->getSymbolLeft($currency_code_autocalc);
         $var_currency_autocalc['symbol_right'] = $this->currency->getSymbolRight($currency_code_autocalc);
         $var_currency_autocalc['decimals'] = $this->currency->getDecimalPlace($currency_code_autocalc);
         $var_currency_autocalc['decimal_point'] = $this->language->get('decimal_point');
         $var_currency_autocalc['thousand_point'] = $this->language->get('thousand_point');
         $data['currency_autocalc'] = $var_currency_autocalc;

			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
			} else {
				$data['tax'] = false;
			}

			if ($product_info['minimum']) {
				$data['minimum'] = $product_info['minimum'];
			} else {
				$data['minimum'] = 1;
			}

			$data['options'] = array();

			foreach ($this->model_catalog_product->getProductOptions($product_id) as $option) {

				$product_option_value_data = array();

				foreach ($option['product_option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
						} else {
							$price = false;
						}
						$product_option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'option_status'           => $option_value['option_status'],
							'image'                   => $this->model_tool_image->resize($option_value['image'], 64, 64),
							'image2x'                 => $hd_imgs ? $this->model_tool_image->resize($option_value['image'], 64*2, 64*2) : NULL,
							'image3x'                 => $hd_imgs ? $this->model_tool_image->resize($option_value['image'], 64*3, 64*3) : NULL,
							'image4x'                 => $hd_imgs ? $this->model_tool_image->resize($option_value['image'], 64*4, 64*4) : NULL,
							'price'                   => $price,
							'price_prefix'            => $option_value['price_prefix']
						);
					}
				}

				$data['options'][] = array(
					'product_option_id'    => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $option['option_id'],
					'name'                 => $option['name'],
					'type'                 => $option['type'],
					'value'                => $option['value'],
					'required'             => $option['required']
				);
			}
		}

		$data['lang_with'] = $this->config->get('module_pixsel_price_tax_names_with')[$this->session->data['language']];
		$data['lang_without'] = $this->config->get('module_pixsel_price_tax_names_without')[$this->session->data['language']];

		$this->response->setOutput($this->load->view('extension/module/frametheme/ft_fastorder', $data));

	}

	public function fastorder() {


		$this->load->language('extension/module/frametheme/ft_fastorder');

		$this->load->model('setting/setting');

		$this->language->load('product/product');

		$this->load->model('tool/image');
		$this->load->model('catalog/product');
		$this->load->model('account/customer');

		$ft_settings = array();

		$ft_settings = $this->model_setting_setting->getSetting('theme_frame', $this->config->get('config_store_id'));

		$language_id = $this->config->get('config_language_id');

		if (isset($ft_settings['t1_fastorder_mail']) && $ft_settings['t1_fastorder_mail']) {
			$recipient_mail = $ft_settings['t1_fastorder_mail'];
		} else {
			$recipient_mail = $this->config->get('config_email');
		}

		$show_phone = true;

		$show_email = false;

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate($show_phone, $show_email)) {

			$order_data = array();

			$lang_id = $this->config->get('config_language_id');

			$product_id = $this->request->post['product_id'];

			if (isset($this->request->post['product_id'])) {
				$data['product_id'] = $this->request->post['product_id'];
			} else {
				$data['product_id'] = '';
			}

			$product_info = $this->model_catalog_product->getProduct($product_id);

			$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
			$order_data['store_id'] = $this->config->get('config_store_id');
			$order_data['store_name'] = $this->config->get('config_name');

			if ($order_data['store_id']) {
				$order_data['store_url'] = $this->config->get('config_url');
			} else {
				if ($this->request->server['HTTPS']) {
					$order_data['store_url'] = HTTPS_SERVER;
				} else {
					$order_data['store_url'] = HTTP_SERVER;
				}
			}

			if ($this->customer->isLogged()) {
				$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
				$order_data['customer_id'] = $this->customer->getId();
				$order_data['customer_group_id'] = $customer_info['customer_group_id'];
			} else {
				$order_data['customer_id'] = 0;
				$order_data['customer_group_id'] = $this->customer->getGroupId();
			}

			if (isset($this->request->post['f_name'])) {
				$order_data['name_fastorder'] = $this->request->post['f_name'];
			} else {
				$order_data['name_fastorder'] = '';
			}

			$order_data['firstname'] = $order_data['shipping_firstname'] = $order_data['payment_firstname'] = $order_data['name_fastorder'];
			$order_data['lastname'] = '';

			if (isset($this->request->post['f_email'])) {
				$order_data['email'] = (isset($this->request->post['f_email']) && !empty($this->request->post['f_email'])) ? $this->request->post['f_email'] : 'empty'.time().'@localhost.net';
			} else {
				$order_data['email'] = 'empty'.time().'@localhost.net';
			}

			if (isset($this->request->post['f_phone'])) {
				$order_data['telephone'] = $this->request->post['f_phone'];
			} else {
				$order_data['telephone'] = '';
			}

			if (isset($this->request->post['f_comment'])) {
				$order_data['comment'] = $this->request->post['f_comment'];
			} else {
				$order_data['comment'] = '';
			}

			$order_data['custom_field'] = array();
			$order_data['fax'] = '';
			$order_data['payment_lastname'] = '';
			$order_data['payment_company'] = '';
			$order_data['payment_address_1'] = '';
			$order_data['payment_address_2'] = '';
			$order_data['payment_city'] = '';
			$order_data['payment_postcode'] = '';
			$order_data['payment_country'] = '';
			$order_data['payment_country_id'] = '';
			$order_data['payment_zone'] = '';
			$order_data['payment_zone_id'] = '';
			$order_data['payment_address_format'] = '';
			$order_data['payment_custom_field'] = array();
			$order_data['payment_method'] = '';
			$order_data['payment_code'] = '';

			$order_data['shipping_lastname'] = '';
			$order_data['shipping_company'] = '';
			$order_data['shipping_address_1'] = '';
			$order_data['shipping_address_2'] = '';
			$order_data['shipping_city'] = '';
			$order_data['shipping_postcode'] = '';
			$order_data['shipping_country'] = '';
			$order_data['shipping_country_id'] = '';
			$order_data['shipping_zone'] = '';
			$order_data['shipping_zone_id'] = '';
			$order_data['shipping_address_format'] = '';
			$order_data['shipping_custom_field'] = array();
			$order_data['shipping_method'] = '';
			$order_data['shipping_code'] = '';

			$order_data['affiliate_id'] = 0;
			$order_data['commission'] = 0;
			$order_data['marketing_id'] = 0;
			$order_data['tracking'] = '';

			$order_data['language_id'] = $lang_id;
			$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
			$order_data['currency_code'] = $this->session->data['currency'];
			$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
			$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

			if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
			} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
			} else {
				$order_data['forwarded_ip'] = '';
			}

			if (isset($this->request->server['HTTP_USER_AGENT'])) {
				$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
			} else {
				$order_data['user_agent'] = '';
			}

			if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
				$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
			} else {
				$order_data['accept_language'] = '';
			}

			$order_data['tax_class_id_total'] = $product_info['tax_class_id'];
			$order_data['config_tax'] = $this->config->get('config_tax');
			$order_data['price_shipping_value']	= '';
			$order_data['shipping_title'] = '';

			if (isset($this->request->post['f_quantity'])) {
				$order_data['quantity'] = $this->request->post['f_quantity'];
			} else {
				$order_data['quantity'] = 1;
			}

			$this->load->model('catalog/product');

			$product_info = $this->model_catalog_product->getProduct($product_id);

			$order_data['total'] = $product_info['price'] * $order_data['quantity'];

			if(isset($this->request->post['option'])){
				$option = $this->request->post['option'];
			} else {
				$option = array();
			}

			if(!empty($option)){
				$product_options = $this->getProductsOptionsFastorder($product_id, $option);
			}

			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = $order_data['total'];

			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);

			$this->load->model('extension/total/total');

			$this->model_extension_total_total->getTotal($total_data);

			$order_data['totals'] = $totals;

			$order_data['products'] = array();

			if($product_info){
				$order_data['products'][] = array(
					'product_id' 		=> $product_id,
					'name'       		=> $product_info['name'],
					'model'      		=> $product_info['model'],
					'option'     		=> $product_options,
					'quantity'   		=> $order_data['quantity'],
					'subtract'   		=> $product_info['subtract'],
					'price'      		=> $product_info['price'],
					'total'      		=> $order_data['total'],
					'tax'        		=> $this->tax->getTax($product_info['price'], $product_info['tax_class_id']),
					'reward'     		=> $product_info['reward'],
					'currency_code' 	=> $order_data['currency_code'],
					'currency_value' 	=> $order_data['currency_value'],
					'product_image' 	=> $product_info['image'],
				);
			}

			$this->load->model('checkout/order');

			$this->load->model('checkout/order');

			$order_id = $this->model_checkout_order->addOrder($order_data);

			$this->model_checkout_order->addOrderHistory($order_id, 21);

			$json['success'] = sprintf($this->language->get('text_success_fastorder'), $order_id);


		}


		if (isset($this->error['name'])) {
			$json['error']['name'] = $this->error['name'];
		}

		if (isset($this->error['phone'])) {
			$json['error']['phone'] = $this->error['phone'];
		}

		if (isset($this->error['email'])) {
			$json['error']['email'] = $this->error['email'];
		}

		if (isset($this->error['captcha'])) {
			$json['error']['captcha'] = $this->error['captcha'];
		}

		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}


	private function getProductsOptionsFastorder($prod_id, $option_fast) {
		$product_id = $prod_id;
		if (isset($option_fast)) {
			$options = $option_fast;
		} else {
			$options = array();
		}

		$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
			WHERE p.product_id = '" . (int)$product_id . "'
			AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			AND p.date_available <= NOW() AND p.status = '1'");

		$option_data = array();
			foreach ($options as $product_option_id => $value) {
				$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po
				LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id)
				LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id)
				WHERE po.product_option_id = '" . (int)$product_option_id . "'
				AND po.product_id = '" . (int)$product_id . "'
				AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

				if ($option_query->num_rows) {
					if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {
						$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

						if ($option_value_query->num_rows) {

							$option_data[] = array(
								'product_option_id'       => $product_option_id,
								'product_option_value_id' => $value,
								'option_id'               => $option_query->row['option_id'],
								'option_value_id'         => $option_value_query->row['option_value_id'],
								'name'                    => $option_query->row['name'],
								'value'                   => $option_value_query->row['name'],
								'type'                    => $option_query->row['type'],
							);
						}
					} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
						foreach ($value as $product_option_value_id) {
							$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

								if ($option_value_query->num_rows) {
									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $product_option_value_id,
										'option_id'               => $option_query->row['option_id'],
										'option_value_id'         => $option_value_query->row['option_value_id'],
										'name'                    => $option_query->row['name'],
										'value'                   => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
									);
								}
						}
					} elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
						$option_data[] = array(
							'product_option_id'       => $product_option_id,
							'product_option_value_id' => '',
							'option_id'               => $option_query->row['option_id'],
							'option_value_id'         => '',
							'name'                    => $option_query->row['name'],
							'value'                   => $value,
							'type'                    => $option_query->row['type'],
						);
					}
				}
			}
		return $option_data;
	}


	protected function validate($show_phone, $show_email) {

		if ((utf8_strlen($this->request->post['f_name']) < 2) || (utf8_strlen($this->request->post['f_name']) > 32)) {

			$this->error['name'] = $this->language->get('error_name');

		}

		// if (!filter_var($this->request->post['f_email'], FILTER_VALIDATE_EMAIL) && $show_email) {

			// $this->error['email'] = $this->language->get('error_email');

		// }


		if (!$this->request->post['f_phone'] && $show_phone) {
			$this->error['phone'] = $this->language->get('error_phone');
		}

		// Captcha

		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('guest', (array)$this->config->get('config_captcha_page'))) {
			$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

			if ($captcha) {
				$this->error['captcha'] = $captcha;
			}
		}

		return !$this->error;
	}

}

