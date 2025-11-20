<?php

// *	@source		See SOURCE.txt for source and other copyright.

// *	@license	GNU General Public License version 3; see LICENSE.txt



class ControllerProductCategory extends Controller {

	public function index() {

		$this->load->language('product/category');

		$this->document->addScript('catalog/view/theme/ft_frame/javascript/fancybox/jquery.fancybox.min.js');

		$this->document->addStyle('catalog/view/theme/ft_frame/javascript/fancybox/jquery.fancybox.min.css');

		$this->load->model('catalog/category');

		$data['type_customer'] = 0;
		$data['customer_gid'] = 0;

		if ($this->customer->isLogged()){
			$this->load->model('account/customer');

			$data['type_customer'] = !empty($this->customer->getCustomerType()) ? $this->customer->getCustomerType() : 0;
			$data['customer_gid'] = !empty($this->customer->getGroupId()) ? $this->customer->getGroupId() : 0;
		}

		$this->load->model('catalog/product');



		$this->load->model('tool/image');

		if($this->customer->isLogged()){
			$data['text_head_price'] = $this->language->get('text_your_price');
		} else {
			$data['text_head_price'] = $this->language->get('text_retail_price');
		}

		$data['text_empty'] = $this->language->get('text_empty');

		$tax_data = $this->tax->get_tax_inform();
		if (!empty($tax_data)) {
			$data = array_merge($data, $tax_data);
		}

		if($data['pixsel_tax_status']){
			$data['text_head_price'] = $data['lang_with'];
		}

        if ($this->config->get('config_noindex_disallow_params')) {

            $params = explode ("\r\n", $this->config->get('config_noindex_disallow_params'));

            if(!empty($params)) {

                $disallow_params = $params;

            }

        }



		if (isset($this->request->get['filter'])) {

			$filter = $this->request->get['filter'];

			if (!in_array('filter', $disallow_params, true) && $this->config->get('config_noindex_status')){

                $this->document->setRobots('noindex,follow');

            }

		} else {

			$filter = '';

		}



		if (isset($this->request->get['sort'])) {

			$sort = $this->request->get['sort'];

            if (!in_array('sort', $disallow_params, true) && $this->config->get('config_noindex_status')) {

                $this->document->setRobots('noindex,follow');

            }

		} else {

			$sort = 'p.sort_order';

		}



		if (isset($this->request->get['order'])) {

			$order = $this->request->get['order'];

            if (!in_array('order', $disallow_params, true) && $this->config->get('config_noindex_status')) {

                $this->document->setRobots('noindex,follow');

            }

		} else {

			$order = 'ASC';

		}



		if (isset($this->request->get['page'])) {

			$page = (int)$this->request->get['page'];

            if (!in_array('page', $disallow_params, true) && $this->config->get('config_noindex_status')) {

                $this->document->setRobots('noindex,follow');

            }

		} else {

			$page = 1;

		}



		if (isset($this->request->get['limit'])) {

			$limit = (int)$this->request->get['limit'];

            if (!in_array('limit', $disallow_params, true) && $this->config->get('config_noindex_status')) {

                $this->document->setRobots('noindex,follow');

            }

		} else {

			$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');

		}



		$data['breadcrumbs'] = array();



		$data['breadcrumbs'][] = array(

			'text' => $this->language->get('text_home'),

			'href' => $this->url->link('common/home')

		);

		$text_h1 = '';

		if (isset($this->request->get['path'])) {

			$url = '';



			if (isset($this->request->get['sort'])) {

				$url .= '&sort=' . $this->request->get['sort'];

			}



			if (isset($this->request->get['order'])) {

				$url .= '&order=' . $this->request->get['order'];

			}



			if (isset($this->request->get['limit'])) {

				$url .= '&limit=' . $this->request->get['limit'];

			}



			$path = '';



			$parts = explode('_', (string)$this->request->get['path']);



			$category_id = (int)array_pop($parts);



			foreach ($parts as $path_id) {

				if (!$path) {

					$path = (int)$path_id;

				} else {

					$path .= '_' . (int)$path_id;

				}



				$category_info = $this->model_catalog_category->getCategory($path_id);



				if ($category_info) {
					$text_h1 .= $category_info['name'] . ' ';
					$data['breadcrumbs'][] = array(

						'text' => $category_info['name'],

						'href' => $this->url->link('product/category', 'path=' . $path . $url)

					);

				}

			}



		} else {

			$category_id = 0;

		}



		$category_info = $this->model_catalog_category->getCategory($category_id);



		if ($category_info) {

			$text_h1 .= $category_info['name'];

			if ($category_info['meta_title']) {

				$this->document->setTitle($text_h1);

			} else {

				$this->document->setTitle($text_h1);

			}



			if ($category_info['noindex'] <= 0 && $this->config->get('config_noindex_status')) {

				$this->document->setRobots('noindex,follow');

			}



			if ($category_info['meta_h1']) {

				$data['heading_title'] = $text_h1;

			} else {

				$data['heading_title'] = $text_h1;

			}



			$this->document->setDescription($category_info['meta_description']);

			$this->document->setKeywords($category_info['meta_keyword']);



			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));



			// Set the last category breadcrumb

			$data['breadcrumbs'][] = array(

				'text' => $category_info['name'],

				'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'])

			);



			if ($category_info['image']) {

				$data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height'));

			} else {

				$data['thumb'] = '';

			}



			$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');

			$data['compare'] = $this->url->link('product/compare');



			$url = '';



			if (isset($this->request->get['filter'])) {

				$url .= '&filter=' . $this->request->get['filter'];

			}



			if (isset($this->request->get['sort'])) {

				$url .= '&sort=' . $this->request->get['sort'];

			}



			if (isset($this->request->get['order'])) {

				$url .= '&order=' . $this->request->get['order'];

			}



			if (isset($this->request->get['limit'])) {

				$url .= '&limit=' . $this->request->get['limit'];

			}



			$data['categories'] = array();



			$results = $this->model_catalog_category->getCategories($category_id);



			foreach ($results as $result) {

				$filter_data = array(

					'filter_category_id'  => $result['category_id'],

					'filter_sub_category' => true

				);



				$data['categories'][] = array(

					'name' => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),

					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url)

				);

			}



			$data['products'] = array();



			$filter_data = array(

				'filter_category_id' => $category_id,

				'filter_filter'      => $filter,

				'sort'               => $sort,

				'order'              => $order,

				'start'              => ($page - 1) * $limit,

				'limit'              => $limit

			);



			$product_total = $this->model_catalog_product->getTotalProducts($filter_data);



			$results = $this->model_catalog_product->getProducts($filter_data);

			$data['pimg_w'] = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width');
			$data['pimg_h'] = $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height');

			foreach ($results as $result) {

				if ($result['image']) {

					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));

				} else {

					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));

				}



				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {

					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				} else {

					$price = false;

				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {

					$retail_price = $this->currency->format($this->tax->calculate($result['retail_price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				} else {

					$retail_price = false;

				}



				if (!is_null($result['special']) && (float)$result['special'] >= 0) {

					$special = $this->currency->format($this->tax->calculate((float)$result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

					$tax_price = (float)$result['special'];

				} else {

					$special = false;

					$tax_price = (float)$result['price'];

				}



				if ($this->config->get('config_tax')) {

					$tax = $this->currency->format($tax_price, $this->session->data['currency']);

				} else {

					$tax = false;

				}



				if ($this->config->get('config_review_status')) {

					$rating = (int)$result['rating'];

				} else {

					$rating = false;

				}

				$options = array();

				foreach ($this->model_catalog_product->getProductOptions($result['product_id']) as $option) {
					$product_option_value_data = array();

					foreach ($option['product_option_value'] as $option_value) {
						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
							if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
								$option_price = $this->currency->format($this->tax->calculate($option_value['price'], $result['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $currency);
							} else {
								$option_price = false;
							}

							$product_option_value_data[] = array(
								'product_option_value_id' => $option_value['product_option_value_id'],
								'option_value_id'         => $option_value['option_value_id'],
								'name'                    => $option_value['name'],
								'image'                   => $option_value['image'] ? $this->model_tool_image->resize($option_value['image'], 50, 50) : '',
								'price'                   => $option_price,
								'quantity'                => $option_value['quantity'],
								'option_status'           => $option_value['option_status'],
								'pixsel_sku'              => $option_value['pixsel_sku'],
								'price_value'             => $this->tax->calculate($option_value['price'], $result['tax_class_id'], $this->config->get('config_tax') ? 'P' : false),
								'price_prefix'            => $option_value['price_prefix']
							);
						}
					}

					$options[] = array(
						'product_option_id'    => $option['product_option_id'],
						'product_option_value' => $product_option_value_data,
						'option_id'            => $option['option_id'],
						'name'                 => $option['name'],
						'type'                 => $option['type'],
						'value'                => $option['value'],
						'required'             => $option['required']
					);
				}

				$dop_images = array();

				$result_images = $this->model_catalog_product->getProductImages($result['product_id']);

				foreach ($result_images as $key => $result_image) {
					if($key == 3){
						break;
					}

					$image_thumb_old = $result_image['image'];
					$image_thumb_new = 'cache/' . utf8_substr($result_image['image'], 0, utf8_strrpos($result_image['image'], '.')) . '-100x56.png';
					$image_popup_old = $result_image['image'];
					$image_popup_new = 'cache/' . utf8_substr($result_image['image'], 0, utf8_strrpos($result_image['image'], '.')) . '-' . $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width') . 'x' . $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height') . '.png';

					if (!is_file(DIR_IMAGE . $image_thumb_new) || (filemtime(DIR_IMAGE . $image_thumb_old) > filemtime(DIR_IMAGE . $image_thumb_new)) || !is_file(DIR_IMAGE . $image_popup_new) || (filemtime(DIR_IMAGE . $image_popup_old) > filemtime(DIR_IMAGE . $image_popup_new))) {
						$dop_images[$key + 1] = array(
							// 'thumb' => $this->model_tool_image->resizeWc($result_image['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')),
							'thumb' => $this->model_tool_image->resizeWc($result_image['image'], '100', '56.01'),
							'popup' => $this->model_tool_image->resizeWc($result_image['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
						);
					} else {
						$dop_images[$key + 1] = array(
							// 'thumb' => $this->model_tool_image->resizeWc($result_image['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')),
							'thumb' => 'image/cachewebp/' . utf8_substr($result_image['image'], 0, utf8_strrpos($result_image['image'], '.')) . '-100x56.webp',
							'popup' => 'image/cachewebp/' . utf8_substr($result_image['image'], 0, utf8_strrpos($result_image['image'], '.')) . '-' . $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width') . 'x' . $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height') . '.webp',
						);
					}
				}

				if(!empty($dop_images)){
					$image_thumb_old = $result['image'];
					$image_thumb_new = 'cache/' . utf8_substr($result_image['image'], 0, utf8_strrpos($result_image['image'], '.')) . '-100x56.png';
					$image_popup_old = $result['image'];
					$image_popup_new = 'cache/' . utf8_substr($result_image['image'], 0, utf8_strrpos($result_image['image'], '.')) . '-' . $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width') . 'x' . $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height') . '.png';

					if ($result['image']) {
						if (!is_file(DIR_IMAGE . $image_thumb_new) || (filemtime(DIR_IMAGE . $image_thumb_old) > filemtime(DIR_IMAGE . $image_thumb_new)) || !is_file(DIR_IMAGE . $image_popup_new) || (filemtime(DIR_IMAGE . $image_popup_old) > filemtime(DIR_IMAGE . $image_popup_new))) {
							$dop_images[0] = array(
								// 'thumb' => $this->model_tool_image->resizeWc($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')),
								'thumb' => $this->model_tool_image->resizeWc($result['image'], '100', '56.01'),
								'popup' => $this->model_tool_image->resizeWc($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
							);
						} else {
							$dop_images[0] = array(
								'thumb' => 'image/cachewebp/' . utf8_substr($result['image'], 0, utf8_strrpos($result['image'], '.')) . '-100x56.webp',
								'popup' => 'image/cachewebp/' . utf8_substr($result['image'], 0, utf8_strrpos($result['image'], '.')) . '-' . $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width') . 'x' . $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height') . '.webp',
							);
						}
					} else {
						if (!is_file(DIR_IMAGE . $image_thumb_new) || (filemtime(DIR_IMAGE . $image_thumb_old) > filemtime(DIR_IMAGE . $image_thumb_new)) || !is_file(DIR_IMAGE . $image_popup_new) || (filemtime(DIR_IMAGE . $image_popup_old) > filemtime(DIR_IMAGE . $image_popup_new))) {
							$dop_images[0] = array(
								// 'thumb' => $this->model_tool_image->resizeWc($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')),
								'thumb' => $this->model_tool_image->resizeWc($result['image'], '100', '56.01'),
								'popup' => $this->model_tool_image->resizeWc('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
							);
						} else {
							$dop_images[0] = array(
								'thumb' => 'image/cachewebp/' . utf8_substr($result['image'], 0, utf8_strrpos($result['image'], '.')) . '-100x56.webp',
								'popup' => 'image/cachewebp/' . utf8_substr($result['image'], 0, utf8_strrpos($result['image'], '.')) . '-' . $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width') . 'x' . $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height') . '.webp',
							);
						}
					}

					ksort($dop_images);
				}

				if ($this->customer->isLogged() && $data['pixsel_tax_status'] || !$this->config->get('config_customer_price') && $data['pixsel_tax_status']) {
					$tax_price = $this->currency->format($this->tax->calc_tax((float)$result['price']), $this->session->data['currency'], 0, false);
				} else {
					$tax_price = false;
				}

				if($data['type_customer'] == 2 || $data['customer_gid'] == 4){
					foreach ($result['prices'] as $key => $p_result) {
						$result['prices'][$key]['price'] = $this->currency->format($this->tax->calculate($p_result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						$result['prices'][$key]['tax_price'] = $this->currency->format($this->tax->calc_tax($p_result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'], 0, false);
					}
				} else {
					$result['prices'] = [];
				}

				$data['products'][] = array(

					'dop_images'  => $dop_images,
					
					'options'	  => $options,

					'product_id'  => $result['product_id'],

					'thumb'       => $image,

					'name'        => $result['name'],

					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',

					// 'retail_price'=> ($this->config->get('config_customer_group_id') != 1 ? $this->currency->format($this->tax->calculate(round($result['retail_price']), $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'], 0, false) : false),
					'retail_price'=> ($this->config->get('config_customer_group_id') != 1 ? $retail_price : false),
					
					'prices'       => $result['prices'],
					
					'price'       => $price,
					
					'tax_price'   => $tax_price,

					'special'     => $special,

					'tax'         => $tax,

					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,

					'rating'      => $result['rating'],

					'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url)

				);

			}

			$url = '';



			if (isset($this->request->get['filter'])) {

				$url .= '&filter=' . $this->request->get['filter'];

			}



			if (isset($this->request->get['limit'])) {

				$url .= '&limit=' . $this->request->get['limit'];

			}



			$data['sorts'] = array();



			$data['sorts'][] = array(

				'text'  => $this->language->get('text_default'),

				'value' => 'p.sort_order-ASC',

				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=ASC' . $url)

			);



			$data['sorts'][] = array(

				'text'  => $this->language->get('text_name_asc'),

				'value' => 'pd.name-ASC',

				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=ASC' . $url)

			);



			$data['sorts'][] = array(

				'text'  => $this->language->get('text_name_desc'),

				'value' => 'pd.name-DESC',

				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=DESC' . $url)

			);



			$data['sorts'][] = array(

				'text'  => $this->language->get('text_price_asc'),

				'value' => 'p.price-ASC',

				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=ASC' . $url)

			);



			$data['sorts'][] = array(

				'text'  => $this->language->get('text_price_desc'),

				'value' => 'p.price-DESC',

				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=DESC' . $url)

			);



			if ($this->config->get('config_review_status')) {

				$data['sorts'][] = array(

					'text'  => $this->language->get('text_rating_desc'),

					'value' => 'rating-DESC',

					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=DESC' . $url)

				);



				$data['sorts'][] = array(

					'text'  => $this->language->get('text_rating_asc'),

					'value' => 'rating-ASC',

					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=ASC' . $url)

				);

			}



			$data['sorts'][] = array(

				'text'  => $this->language->get('text_model_asc'),

				'value' => 'p.model-ASC',

				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=ASC' . $url)

			);



			$data['sorts'][] = array(

				'text'  => $this->language->get('text_model_desc'),

				'value' => 'p.model-DESC',

				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=DESC' . $url)

			);



			$url = '';



			if (isset($this->request->get['filter'])) {

				$url .= '&filter=' . $this->request->get['filter'];

			}



			if (isset($this->request->get['sort'])) {

				$url .= '&sort=' . $this->request->get['sort'];

			}



			if (isset($this->request->get['order'])) {

				$url .= '&order=' . $this->request->get['order'];

			}



			$data['limits'] = array();



			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));



			sort($limits);



			foreach($limits as $value) {

				$data['limits'][] = array(

					'text'  => $value,

					'value' => $value,

					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=' . $value)

				);

			}



			$url = '';



			if (isset($this->request->get['filter'])) {

				$url .= '&filter=' . $this->request->get['filter'];

			}



			if (isset($this->request->get['sort'])) {

				$url .= '&sort=' . $this->request->get['sort'];

			}



			if (isset($this->request->get['order'])) {

				$url .= '&order=' . $this->request->get['order'];

			}



			if (isset($this->request->get['limit'])) {

				$url .= '&limit=' . $this->request->get['limit'];

			}



			$pagination = new Pagination();

			$pagination->total = $product_total;

			$pagination->page = $page;

			$pagination->limit = $limit;

			$pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page={page}');



			$data['pagination'] = $pagination->render();



			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));



            if (!$this->config->get('config_canonical_method')) {

                // http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html

                if ($page == 1) {

                    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id']), 'canonical');

                } elseif ($page == 2) {

                    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id']), 'prev');

                } else {

                    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page=' . ($page - 1)), 'prev');

                }



                if ($limit && ceil($product_total / $limit) > $page) {

                    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page=' . ($page + 1)), 'next');

                }

            } else {



                if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {

                    $server = $this->config->get('config_ssl');

                } else {

                    $server = $this->config->get('config_url');

                };



                $request_url = rtrim($server, '/') . $this->request->server['REQUEST_URI'];

                $canonical_url = $this->url->link('product/category', 'path=' . $category_info['category_id']);



                if (($request_url != $canonical_url) || $this->config->get('config_canonical_self')) {

                    $this->document->addLink($canonical_url, 'canonical');

                }



                if ($this->config->get('config_add_prevnext')) {



                    if ($page == 2) {

                        $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id']), 'prev');

                    } elseif ($page > 2)  {

                        $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page=' . ($page - 1)), 'prev');

                    }



                    if ($limit && ceil($product_total / $limit) > $page) {

                        $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page=' . ($page + 1)), 'next');

                    }

                }

            }



			$data['sort'] = $sort;

			$data['order'] = $order;

			$data['limit'] = $limit;



			$data['continue'] = $this->url->link('common/home');



			$data['column_left'] = $this->load->controller('common/column_left');

			$data['column_right'] = $this->load->controller('common/column_right');

			$data['content_top'] = $this->load->controller('common/content_top');

			$data['content_bottom'] = $this->load->controller('common/content_bottom');

			$data['footer'] = $this->load->controller('common/footer');

			$data['header'] = $this->load->controller('common/header');



			$this->response->setOutput($this->load->view('product/category', $data));

		} else {

			$url = '';



			if (isset($this->request->get['path'])) {

				$url .= '&path=' . $this->request->get['path'];

			}



			if (isset($this->request->get['filter'])) {

				$url .= '&filter=' . $this->request->get['filter'];

			}



			if (isset($this->request->get['sort'])) {

				$url .= '&sort=' . $this->request->get['sort'];

			}



			if (isset($this->request->get['order'])) {

				$url .= '&order=' . $this->request->get['order'];

			}



			if (isset($this->request->get['page'])) {

				$url .= '&page=' . $this->request->get['page'];

			}



			if (isset($this->request->get['limit'])) {

				$url .= '&limit=' . $this->request->get['limit'];

			}



			$data['breadcrumbs'][] = array(

				'text' => $this->language->get('text_error'),

				'href' => $this->url->link('product/category', $url)

			);



			$this->document->setTitle($this->language->get('text_error'));



			$data['continue'] = $this->url->link('common/home');



			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');



			$data['column_left'] = $this->load->controller('common/column_left');

			$data['column_right'] = $this->load->controller('common/column_right');

			$data['content_top'] = $this->load->controller('common/content_top');

			$data['content_bottom'] = $this->load->controller('common/content_bottom');

			$data['footer'] = $this->load->controller('common/footer');

			$data['header'] = $this->load->controller('common/header');



			$this->response->setOutput($this->load->view('error/not_found', $data));

		}

	}

}

