<?php
/*
@author	Artem Serbulenko
@link	https://cmsshop.com.ua
@link	https://opencartforum.com/profile/762296-bn174uk/
@email 	serfbots@gmail.com
*/  
class ControllerExtensionFeedArtOrdersFeed extends Controller {
	private $error = array();

	public function index() {

		$status = $this->config->get('feed_art_orders_feed_status');
		$token = $this->config->get('feed_art_orders_feed_token');
		$copy_order = $this->config->get('feed_art_orders_feed_orders');

		if ($status && isset($this->request->get['token']) && $token == $this->request->get['token']) {
			$this->load->model('extension/feed/art_orders_feed');

			$output = '';

			$results = $this->model_extension_feed_art_orders_feed->ordersFeed();

			$orders_array = array();
			foreach ($results as $orders => $order) {
				$orders_array[$order['order_id']][] = $order;
			}	

			foreach ($orders_array as $key => $value) {
				$value = $this->sortField($value);
				$orders_array[$key] = $value;
			}

			$xml_orders = array();
			$orders_val = array();
			$not_xml_order = array(
				'fax',
				'custom_field',
				'payment_custom_field',
				'payment_address_format',
				'payment_firstname',
				'payment_lastname',
				'payment_company',
				'payment_address_1',
				'payment_address_2',
				'payment_city',
				'payment_postcode',
				'payment_country',
				'payment_country_id',
				'payment_zone',
				'payment_zone_id',
				'payment_method',
				'payment_code',
				'shipping_firstname',
				'shipping_lastname',
				'shipping_company',
				'shipping_address_1',
				'shipping_address_2',
				'shipping_city',
				'shipping_postcode',
				'shipping_country',
				'shipping_country_id',
				'shipping_zone',
				'shipping_zone_id',
				'shipping_method',
				'shipping_code',
				'shipping_custom_field',
				'shipping_address_format',
				'tracking',
				'language_id',
				'marketing_id',
				'commission',
				'affiliate_id',
				'comment',
				'forwarded_ip',
				'user_agent',
				'accept_language',
				'ip',
				'currency_code',
				'currency_value',
				'currency_id',
				'email',
				'customer_id',
				'customer_group_id',
				'invoice_no',
				'invoice_prefix',
				'novaposhta_cn_number',
				'novaposhta_cn_ref',
				'store_id',
				'store_name',
				'store_url',
				'total',
				// 'date_added',
				'date_modified',
				'telephone'
			);

			$not_xml_product = array(
				'name',
				'price',
				'tax',
				'reward',
				'url'
			);

			$xml = '<?xml version="1.0" encoding="utf-8"?><orders date="' . date('Y-m-d H:i') . '">';

			foreach ($orders_array as $orders => $order) {
				$order_product_key = array();
				$order_id = '';
				foreach ($order as $key => $value) {
					if ($key == 0) {
						$xml_orders[$value['order_id']] = '<order id="' . $value['order_id'] . '">';
						$orders_val[$value['order_id']] = '<order>';
						$order_id = $value['order_id'];
						
						foreach ($value as $order_key => $item) {
							if ($order_key == 'order_product_id') {
								break;
							}

							if (array_search($order_key,$not_xml_order) === false) {
								if ($order_key == 'order_status_id') {
									$osid = (strip_tags($item) != 0) ? $this->model_extension_feed_art_orders_feed->orderStatus(strip_tags($item)) : $this->model_extension_feed_art_orders_feed->orderStatus('1');

									$xml_orders[$order_id] .= "<" . $order_key . ">" . strip_tags($osid['name']). "</" . $order_key . ">";
									$orders_val[$order_id] .= "<" . $order_key . ">" . strip_tags($osid['name']). "</" . $order_key . ">";

									unset($value[$order_key]);
								} else if ($order_key == 'firstname') {
									$cu_query = $this->db->query("SELECT * FROM oc_customer WHERE customer_id = '" . $value['customer_id'] . "'");

									$xml_orders[$order_id] .= "<" . $order_key . ">" . strip_tags($cu_query->row['company_name']). "</" . $order_key . ">";
									$orders_val[$order_id] .= "<" . $order_key . ">" . strip_tags($cu_query->row['company_name']). "</" . $order_key . ">";
								} else if ($order_key == 'lastname') {
									$xml_orders[$order_id] .= "<" . $order_key . ">" . strip_tags($value['firstname']) . ' ' . strip_tags($value['lastname']). "</" . $order_key . ">";
									$orders_val[$order_id] .= "<" . $order_key . ">" . strip_tags($value['firstname']) . ' ' . strip_tags($value['lastname']). "</" . $order_key . ">";

									unset($value['firstname']);
									unset($value['lastname']);
								} else {
									$xml_orders[$order_id] .= "<" . $order_key . ">" . strip_tags($item). "</" . $order_key . ">";
									$orders_val[$order_id] .= "<" . $order_key . ">" . strip_tags($item). "</" . $order_key . ">";
									
									unset($value[$order_key]);
								}
							}
							
						}
						$order_product_key = array_keys($value);
					}

					if ($key == 0) {
						$xml_orders[$order_id] .= '<products>';
					}

					if (!empty($value)) {
						$xml_orders[$order_id] .= '<product id="' . $value['product_id'] . '">';
						$orders_val[$order_id] .= '<product id="' . $value['product_id'] . '">';
						foreach ($value as $product_key => $item) {
							if (array_search($product_key,$order_product_key) !== false && array_search($product_key,$not_xml_product) === false) {
								$xml_orders[$order_id] .= "<".$product_key.">" . strip_tags($item). "</".$product_key.">";
								$orders_val[$order_id] .= "<".$product_key.">" . strip_tags($item). "</".$product_key.">";
							}
						}

						// $xml_orders[$order_id] .= "<url>" . $this->url->link('product/product', 'product_id=' . $value['product_id']) . "</url>";
						// $orders_val[$order_id] .= "<url>" . $this->url->link('product/product', 'product_id=' . $value['product_id']) . "</url>";

						$xml_orders[$order_id] .= '</product>';
						$orders_val[$order_id] .= '</product>';
					}
					if ($key == (count($order) - 1)) {
						$xml_orders[$order_id] .= '</products>';
						$xml_orders[$order_id] .= '</order>';
						$orders_val[$order_id] .= '</products>';
						$orders_val[$order_id] .= '</order>';
					}
				}	
			}
			
			if ($copy_order) {
				$last = null;
				foreach ($orders_val as $key => $order_val) {
				    if ($order_val === $last) {
				        unset($orders_val[$key]);
				    } else {
				    	$last = $order_val;
				    }
				}	
			}

			foreach ($orders_val as $key => $value) {
				$xml .= $xml_orders[$key];
			}

			$xml .= '</orders>';
			$this->response->addHeader('Content-Type: application/xml; charset=utf-8');
	        $this->response->setOutput($xml);
        } else {
			$this->load->language('error/not_found');
			
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

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

	protected function sortField($sort) {
		for ($i = 0; $i < count($sort); $i++) {
           	for ($j = $i + 1; $j < count($sort); $j++) {
               	if ($sort[$i]['order_product_id'] > $sort[$j]['order_product_id']) {
                   $temp = $sort[$j];
                   $sort[$j] = $sort[$i];
                   $sort[$i] = $temp;
           		}
          	}         
       	}
       	return $sort;
	}
}