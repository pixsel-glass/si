<?php
class ControllerExtensionModuleFramethemeFTQview extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/frametheme/ft_qview');
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

		if (isset($ft_settings['t1_fastorder_status']) && !empty($ft_settings['t1_fastorder_status'])) {
			$data['fastorder_status'] = $ft_settings['t1_fastorder_status'];
		} else {
			$data['fastorder_status'] = false;
		}

		if (isset($ft_settings['t1_stikers']) && !empty($ft_settings['t1_stikers'])) {
			$ft_stikers = $ft_settings['t1_stikers'];
		} else {
			$ft_stikers = array();
		}

    if (isset($ft_settings['t1_catalog_mode']) && !empty($ft_settings['t1_catalog_mode'])) {
      $data['catalog_mode'] = $ft_settings['t1_catalog_mode'];
    } else {
      $data['catalog_mode'] = false;
    }

    if (isset($ft_settings['t1_catalog_mode_hide_price']) && !empty($ft_settings['t1_catalog_mode_hide_price'])) {
      $data['hide_price'] = $ft_settings['t1_catalog_mode_hide_price'];
    } else {
      $data['hide_price'] = false;
    }

    if (isset($ft_settings['t1_wishlist_status']) && !empty($ft_settings['t1_wishlist_status'])) {
      $data['wishlist_status'] = $ft_settings['t1_wishlist_status'];
    } else {
      $data['wishlist_status'] = false;
    }

    if (isset($ft_settings['t1_compare_status']) && !empty($ft_settings['t1_compare_status'])) {
      $data['compare_status'] = $ft_settings['t1_compare_status'];
    } else {
      $data['compare_status'] = false;
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
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			$data['model'] = $product_info['model'];
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];

			$delimiter = '<!--more-->';
			$split_description = explode($delimiter, html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8'), 2);

			if (count($split_description) > 1) {
				$data['description'] = html_entity_decode($split_description[0], ENT_QUOTES, 'UTF-8');
			} else {
				$data['description'] = utf8_substr(trim(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8'))), 0, 320) . '...';
			}


			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {
				$data['stock'] = $product_info['quantity'];
			} else {
				$data['stock'] = $this->language->get('text_instock');
			}

			$data['quantity'] = $product_info['quantity'];

			$this->load->model('tool/image');

			// $data['thumb_holder'] = $this->model_tool_image->resize('src_holder.png', 300, 300);
			$data['thumb_holder'] = $this->model_tool_image->resize('src_holder.png', 300, 169);
			$data['thumb_width'] = 300;
			//$data['thumb_height'] = 300;
			$data['thumb_height'] = 169;
			$data['popup_width'] = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width');
			$data['popup_height'] = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height');

			if ($product_info['image']) {
				$data['popup'] = $this->model_tool_image->resizeWc($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
			} else {
				$data['popup'] = '';
			}



			if ($product_info['image']) {
				// $data['thumb'] = $this->model_tool_image->resize($product_info['image'], 300, 300);
				$data['thumb'] = $this->model_tool_image->resizeWc($product_info['image'], 300, 169);
				if  ($hd_imgs) {
					// $data['thumb2x'] = $this->model_tool_image->resizeWc($product_info['image'], 300*2, 300*2);
					// $data['thumb3x'] = $this->model_tool_image->resizeWc($product_info['image'], 300*3, 300*3);
					// $data['thumb4x'] = $this->model_tool_image->resizeWc($product_info['image'], 300*4, 300*4);
					$data['thumb2x'] = $this->model_tool_image->resizeWc($product_info['image'], 300*2, 169*2);
					$data['thumb3x'] = $this->model_tool_image->resizeWc($product_info['image'], 300*3, 169*3);
					$data['thumb4x'] = $this->model_tool_image->resizeWc($product_info['image'], 300*4, 169*4);					
				}
			} else {
				$data['thumb'] = '';
			}

			$data['images'] = array();

			$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);

			foreach ($results as $result) {
				$data['images'][] = array(
					'popup' => $this->model_tool_image->resizeWc($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),

					// 'thumb' 	=> $this->model_tool_image->resize($result['image'], 300, 300),
					// 'thumb2x' => $hd_imgs ? $this->model_tool_image->resize($result['image'], 300*2, 300*2) : NULL,
					// 'thumb3x' => $hd_imgs ? $this->model_tool_image->resize($result['image'], 300*3, 300*3) : NULL,
					// 'thumb4x' => $hd_imgs ? $this->model_tool_image->resize($result['image'], 300*4, 300*4) : NULL
					'thumb' 	=> $this->model_tool_image->resizeWc($result['image'], 300, 169),
					'thumb2x' => $hd_imgs ? $this->model_tool_image->resizeWc($result['image'], 300*2, 169*2) : NULL,
					'thumb3x' => $hd_imgs ? $this->model_tool_image->resizeWc($result['image'], 300*3, 169*3) : NULL,
					'thumb4x' => $hd_imgs ? $this->model_tool_image->resizeWc($result['image'], 300*4, 169*4) : NULL					
				);
			}



			$data['stickers'] = array();

			if ($product_info['price'] && $product_info['special'] && $ft_stikers['special']['status']) {
				$data['stickers']['special'] = array(
					'text'  => round(100 - ($product_info['special'] / $product_info['price']) * 100) * (-1) . '%',
					'class' => 'badge stiker-special'
				);
			}

			if ($product_info['upc'] && $ft_stikers['upc']['status']) {
				$data['stickers']['upc'] = array(
					'text'  => $product_info['upc'],
					'class' => 'badge stiker-upc'
				);
			}

			if ($product_info['ean'] && $ft_stikers['ean']['status']) {
				$data['stickers']['ean'] = array(
					'text'  => $product_info['ean'],
					'class' => 'badge stiker-ean'
				);
			}

			if ($product_info['jan'] && $ft_stikers['jan']['status']) {
				$data['stickers']['jan'] = array(
					'text'  => $product_info['jan'],
					'class' => 'badge stiker-jan'
				);
			}

			if ($product_info['isbn'] && $ft_stikers['isbn']['status']) {
				$data['stickers']['isbn'] = array(
					'text'  => $product_info['isbn'],
					'class' => 'badge stiker-isbn'
				);
			}

			if ($product_info['mpn'] && $ft_stikers['mpn']['status']) {
				$data['stickers']['mpn'] = array(
					'text'  => $product_info['mpn'],
					'class' => 'badge stiker-mpn'
				);
			}






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
							'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
							'image2x'                 => $hd_imgs ? $this->model_tool_image->resize($option_value['image'], 50*2, 50*2) : NULL,
							'image3x'                 => $hd_imgs ? $this->model_tool_image->resize($option_value['image'], 50*3, 50*3) : NULL,
							'image4x'                 => $hd_imgs ? $this->model_tool_image->resize($option_value['image'], 50*4, 50*4) : NULL,
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

			$data['review_status'] = $this->config->get('config_review_status');


			function ft_plural($number, $text_arr) {
				$cases = array (2, 0, 1, 1, 1, 2);
				$text = $number . ' ' . $text_arr[ ($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)] ];
				return $text;
			}

			if ((int)$product_info['reviews'] > 0) {
				$data['reviews'] = ft_plural((int)$product_info['reviews'],array($this->language->get('text_reviews_1'),$this->language->get('text_reviews_2'),$this->language->get('text_reviews_3')));
				$data['reviews_link'] = '#';
			} else {
				$data['reviews'] = $this->language->get('text_no_reviews');
				$data['reviews_link'] = '';
			}

			$data['rating'] = (int)$product_info['rating'];

			$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);

			$data['recurrings'] = $this->model_catalog_product->getProfiles($this->request->get['product_id']);

			$this->model_catalog_product->updateViewed($this->request->get['product_id']);

		}

		$this->response->setOutput($this->load->view('extension/module/frametheme/ft_qview', $data));
	}

}
