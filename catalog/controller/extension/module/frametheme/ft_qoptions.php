<?php

class ControllerExtensionModuleFramethemeFTQoptions extends Controller {

	private $error = array();



	public function index() {
		if (!isset($_COOKIE['lm_language_id'])) {
			$lm_language_id = (int)file_get_contents('./system/uploads/language_id_'.$this->session->getId().'.txt');
		} else {
			$lm_language_id = (int)$_COOKIE['lm_language_id'];
		}
		if (!isset($_COOKIE['lm_language_code'])) {
			$lm_language_code = (int)file_get_contents('./system/uploads/language_code_'.$this->session->getId().'.txt');
		} else {
			$lm_language_code = (int)$_COOKIE['lm_language_code'];
		}
		// $this->config->set('config_language_id', $lm_language_id);
		// $this->config->set('config_language', $lm_language_code);
		// $this->session->data['language'] = $lm_language_code;
		// $language = new Language($lm_language_code);
		// $language->load($lm_language_code);
		// $this->registry->set('language', $language);


		$this->load->language('extension/module/frametheme/ft_qoptions');

		$this->load->language('extension/module/frametheme/ft_global');

		if (isset($this->request->get['product_id'])) {

			$product_id = (int)$this->request->get['product_id'];

		} else {

			$product_id = 0;

		}



		$this->load->model('catalog/product');



		$this->load->model('setting/setting');



		$ft_settings = array();

		$ft_settings = $this->model_setting_setting->getSetting('theme_frame', $this->config->get('config_store_id'));

		$language_id = $this->config->get('config_language_id');



    	if (isset($ft_settings['t1_high_definition_imgs']) && $ft_settings['t1_high_definition_imgs']){

			$hd_imgs = $ft_settings['t1_high_definition_imgs'];

		} else {

			$hd_imgs = false;

		}



		if (isset($ft_settings['t1_buy_button_status']) && !empty($ft_settings['t1_buy_button_status'])) {

			$data['disable_btn_status'] = $ft_settings['t1_buy_button_status'];

		} else {

			$data['disable_btn_status'] = false;

		}



		if (isset($ft_settings['t1_buy_button_disabled_text'][$language_id]) && !empty($ft_settings['t1_buy_button_disabled_text'][$language_id])) {

			$data['disable_btn_text'] = $ft_settings['t1_buy_button_disabled_text'][$language_id];

		} else {

			$data['disable_btn_text'] = '';

		}







		$product_info = $this->model_catalog_product->getProduct($product_id);



		$data['product_isset'] = false;



		if ($product_info) {



			$data['product_isset'] = true;



			$data['heading_title'] = $product_info['name'];



			$data['product_href'] = $this->url->link('product/product&product_id=' . $product_info['product_id'], '', true);



			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);



			$data['product_id'] = (int)$this->request->get['product_id'];

			$data['theme_dir'] 	= $this->config->get('theme_frame_directory');



			$data['quantity'] = $product_info['quantity'];



			$this->load->model('tool/image');



			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {

				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

			} else {

				$data['price'] = false;

			}



			if ((float)$product_info['special']) {

				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

			} else {

				$data['special'] = false;

			}



			if ($this->config->get('config_tax')) {

				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);

			} else {

				$data['tax'] = false;

			}



			$discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);



			$data['discounts'] = array();



			foreach ($discounts as $discount) {

				$data['discounts'][] = array(

					'quantity' => $discount['quantity'],

					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])

				);

			}



			$data['options'] = array();

			// foreach ($this->model_catalog_product->getProductOptionsLn($this->request->get['product_id'], $lm_language_id) as $option) {
				foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) {

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



			if ($product_info['minimum']) {

				$data['minimum'] = $product_info['minimum'];

			} else {

				$data['minimum'] = 1;

			}


			$tax_data = $this->tax->get_tax_inform();
			if (!empty($tax_data)) {
				$data = array_merge($data, $tax_data);
			}


			$data['recurrings'] = $this->model_catalog_product->getProfiles($this->request->get['product_id']);



			$this->model_catalog_product->updateViewed($this->request->get['product_id']);



		}

		$data['lang_with'] = $this->config->get('module_pixsel_price_tax_names_with')[$this->session->data['language']];
		$data['lang_without'] = $this->config->get('module_pixsel_price_tax_names_without')[$this->session->data['language']];

		$this->response->setOutput($this->load->view('extension/module/frametheme/ft_qoptions', $data));

	}



}

