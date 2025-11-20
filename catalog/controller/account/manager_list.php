<?php

// *	@source		See SOURCE.txt for source and other copyright.

// *	@license	GNU General Public License version 3; see LICENSE.txt



class ControllerAccountManagerlist extends Controller {

	public function index() {

		if (!$this->customer->isLogged()) {

			$this->session->data['redirect'] = $this->url->link('account/order', '', true);



			$this->response->redirect($this->url->link('account/login', '', true));

		}



		$this->load->language('account/order');



		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->setRobots('noindex,follow');



		$url = '';



		if (isset($this->request->get['page'])) {

			$url .= '&page=' . $this->request->get['page'];

		}



		$data['breadcrumbs'] = array();



		$data['breadcrumbs'][] = array(

			'text' => $this->language->get('text_home'),

			'href' => $this->url->link('common/home')

		);



		$data['breadcrumbs'][] = array(

			'text' => $this->language->get('text_account'),

			'href' => $this->url->link('account/account', '', true)

		);



		$data['breadcrumbs'][] = array(

			'text' => $this->language->get('heading_title'),

			'href' => $this->url->link('account/order', $url, true)

		);

		$tax_data = $this->tax->get_tax_inform();

		if (!empty($tax_data)) {
			$data = array_merge($data, $tax_data);
		}

		if (isset($this->request->get['page'])) {

			$page = (int)$this->request->get['page'];

		} else {

			$page = 1;

		}



		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();



		$data['orders'] = array();



		$this->load->model('account/order');
		$this->load->model('account/customer');
		$this->load->model('checkout/onepcheckout');

		$countries = $this->model_checkout_onepcheckout->getCountryDeliveries();

		$order_total = $this->model_account_order->getManagerTotalOrders();



		$results = $this->model_account_order->getManagerOrders(($page - 1) * 10, 10);

		foreach ($results as $result) {
			$country = '';
			foreach ($countries as $c) {
				if ($c['country_delivery_id'] == $result['payment_country_id']) {
					$country = $c['name'];
					break;
				}
			}

			$product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);

			$voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);

			$client_info = $this->model_account_customer->getCustomer($result['customer_id']);

			$order_quantity = 0;
			$order_quantity_query = $this->db->query("SELECT quantity FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . $result['order_id'] . "'");
			foreach ($order_quantity_query->rows as $oq) {
				$order_quantity = $order_quantity + $oq['quantity'];
			}

			if (!empty($client_info)) {
				if ($client_info['customer_type'] == 1 || $client_info['customer_type'] == 3) {
					// $customer_name = $client_info['company_name'] . '<br>' . $result['firstname'] . ' ' . $result['lastname'];
					$customer_name = ($client_info['company_name'] ? $client_info['company_name'] . '<br>' . $result['firstname'] . ' ' . $result['lastname'] : $client_info['pl_company_name'] . '<br>' . $result['firstname'] . ' ' . $result['lastname']);
				} else {
					$customer_name = $result['firstname'] . ' ' . $result['lastname'];
				}
			} else {
				$customer_name = $result['firstname'] . ' ' . $result['lastname'];
			}

			$this->load->model('setting/extension');
			$payments = $this->model_setting_extension->getInstalled('payment');
			$pname = array();
	        foreach ($payments as $payment) {
	        	$this->load->language('extension/payment/' . $payment);
	        	$pname[$payment] = $this->language->get('text_title');
	        }
			if ($result['payed'] > 0 && !empty($result['payed_with'])) {
				$payed_with = $pname[$result['payed_with']];
			} else {
				$payed_with = '';
			}

			$totals = $this->model_account_order->getOrderTotals($result['order_id']);

			//foreach ($totals as $total) {
			//	if ($total['code'] == 'shipping' && $total['value'] > 0) {
			//		$result['total'] = $result['total'] + str_replace(",", ".", $total['value']);
			//	}
			//}

			$data['orders'][] = array(

				'order_id'			    => $result['order_id'],

				'name'			        => $customer_name,

				'status'			    => $result['status'],

				'order_status_id'	    => $result['order_status_id'],

				'country_id'			=> $result['shipping_country_id'],

				// 'country'				=> $result['shipping_country'],

				'country'				=> $country,

				'customer_id'			=> $result['customer_id'],

				// 'date_added'			=> date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_added'			=> date("d.m.Y", strtotime($result['date_added'])) . '<br> ' . date("H:i:s", strtotime($result['date_added'])),

				// 'products'			    => ($product_total + $voucher_total),
				'products'			    => $order_quantity,

				'total'			        => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),

				'tax_total'		        => $this->currency->format($this->tax->calc_tax($result['total']), $result['currency_code'], $result['currency_value']),

				'view'			        => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], true),

				'pdf_link'			    => $result['infakt_pdf'],

	            'infakt_need'           => $result['infakt_need'],

	            'infakt_no'             => $result['infakt_no'],

	            'infakt_pdf'            => $result['infakt_pdf'],

	            'infakt_number'         => $result['infakt_number'],

	            'infakt_language'       => $result['infakt_language'],

	            'infakt_currency'       => $result['infakt_currency'],

	            'infakt_pmethod'        => $result['infakt_pmethod'],

	            'infakt_pmethod_ba'     => $result['infakt_pmethod_ba'],

	            'infakt_pmethod_bn'     => $result['infakt_pmethod_bn'],

	            'infakt_vat'            => $result['infakt_vat'],

	            'infakt_nip'            => $result['infakt_nip'],

	            'infakt_privat_faktyre' => $result['infakt_privat_faktyre'],

	            'infakt_vatcode' 		=> $result['infakt_vatcode'],

	            'order_my_sklad'		=> $result['order_my_sklad'],

				'order_my_sklad_no'		=> $result['order_my_sklad_no'],

	            'acode'					=> (isset($client_info['customer_my_sklad']) ? $client_info['customer_my_sklad'] : ''),

				'payed'					=> $result['payed'],

				'payed_with'			=> $payed_with,

			);

		}


		$data['pixsel_tax_status'] = $this->config->get('module_pixsel_price_tax_on');
		$data['lang_with'] = $this->config->get('module_pixsel_price_tax_names_with')[$this->session->data['language']];
		$data['lang_without'] = $this->config->get('module_pixsel_price_tax_names_without')[$this->session->data['language']];


		$pagination = new Pagination();

		$pagination->total = $order_total;

		$pagination->page = $page;

		$pagination->limit = 10;

		$pagination->url = $this->url->link('account/manager_list', 'page={page}', true);



		$data['pagination'] = $pagination->render();



		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($order_total - 10)) ? $order_total : ((($page - 1) * 10) + 10), $order_total, ceil($order_total / 10));



		$data['continue'] = $this->url->link('account/account', '', true);



		$data['column_left'] = $this->load->controller('common/column_left');

		$data['column_right'] = $this->load->controller('common/column_right');

		$data['content_top'] = $this->load->controller('common/content_top');

		$data['content_bottom'] = $this->load->controller('common/content_bottom');

		$data['footer'] = $this->load->controller('common/footer');

		$data['header'] = $this->load->controller('common/header');



		$this->response->setOutput($this->load->view('account/manager_list', $data));

	}



	public function list() {

		$this->load->language('account/order');

		if (isset($this->request->get['client_id'])) {

			$client_id = $this->request->get['client_id'];

		} else {

			return new Action('error/not_found');

		}


		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['pixsel_tax_status'] = $this->config->get('module_pixsel_price_tax_on');
		$data['lang_with'] = $this->config->get('module_pixsel_price_tax_names_with')[$this->session->data['language']];
		$data['lang_without'] = $this->config->get('module_pixsel_price_tax_names_without')[$this->session->data['language']];


	
		$data['orders'] = array();

	
		$this->load->model('account/order');
		$this->load->model('account/customer');


		$order_total = $this->model_account_order->getManagerDynamicTotalOrders($client_id);



		$results = $this->model_account_order->getManagerDynamicOrders($client_id);

		foreach ($results as $result) {
			$this->load->model('checkout/onepcheckout');

			$countries = $this->model_checkout_onepcheckout->getCountryDeliveries();
			$country = '';
			foreach ($countries as $c) {
				if ($c['country_delivery_id'] == $result['payment_country_id']) {
					$country = $c['name'];
					break;
				}
			}

			$product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);

			$voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);


			$client_info = $this->model_account_customer->getCustomer($result['customer_id']);

			$order_quantity = 0;
			$order_quantity_query = $this->db->query("SELECT quantity FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . $result['order_id'] . "'");
			foreach ($order_quantity_query->rows as $oq) {
				$order_quantity = $order_quantity + $oq['quantity'];
			}


			if (!empty($client_info)) {
				if ($client_info['customer_type'] == 1 || $client_info['customer_type'] == 3) {
					// $customer_name = $client_info['company_name'] . '<br>' . $result['firstname'] . ' ' . $result['lastname'];
					$customer_name = ($client_info['company_name'] ? $client_info['company_name'] . '<br>' . $result['firstname'] . ' ' . $result['lastname'] : $client_info['pl_company_name'] . '<br>' . $result['firstname'] . ' ' . $result['lastname']);
				} else {
					$customer_name = $result['firstname'] . ' ' . $result['lastname'];
				}
			} else {
				$customer_name = $result['firstname'] . ' ' . $result['lastname'];
			}

			$this->load->model('setting/extension');
			$payments = $this->model_setting_extension->getInstalled('payment');
			$pname = array();
	        foreach ($payments as $payment) {
	        	$this->load->language('extension/payment/' . $payment);
	        	$pname[$payment] = $this->language->get('text_title');
	        }
			if ($result['payed'] > 0 && !empty($result['payed_with'])) {
				$payed_with = $pname[$result['payed_with']];
			} else {
				$payed_with = '';
			}

			$totals = $this->model_account_order->getOrderTotals($result['order_id']);

			foreach ($totals as $total) {
				if ($total['code'] == 'shipping' && $total['value'] > 0) {
					$result['total'] = $result['total'] + $total['value'];
				}
			}

			$data['orders'][] = array(

				'order_id'			    => $result['order_id'],

				'name'			        => $customer_name,

				'status'			    => $result['status'],

				'order_status_id'	    => $result['order_status_id'],

				'country_id'			=> $result['shipping_country_id'],

				// 'country'				=> $result['shipping_country'],

				'country'				=> $country,

				'customer_id'			=> $result['customer_id'],

				// 'date_added'			=> date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_added'			=> date("d.m.Y", strtotime($result['date_added'])) . '<br> ' . date("H:i:s", strtotime($result['date_added'])),

				// 'products'			    => ($product_total + $voucher_total),
				'products'			    => $order_quantity,

				'total'			        => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),

				'tax_total'		        => $this->currency->format($this->tax->calc_tax($result['total']), $result['currency_code'], $result['currency_value']),

				'view'			        => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], true),

				'pdf_link'			    => $result['infakt_pdf'],

	            'infakt_need'           => $result['infakt_need'],

	            'infakt_no'             => $result['infakt_no'],

	            'infakt_pdf'            => $result['infakt_pdf'],

	            'infakt_number'         => $result['infakt_number'],

	            'infakt_language'       => $result['infakt_language'],

	            'infakt_currency'       => $result['infakt_currency'],

	            'infakt_pmethod'        => $result['infakt_pmethod'],

	            'infakt_pmethod_ba'     => $result['infakt_pmethod_ba'],

	            'infakt_pmethod_bn'     => $result['infakt_pmethod_bn'],

	            'infakt_vat'            => $result['infakt_vat'],

	            'infakt_nip'            => $result['infakt_nip'],

	            'infakt_privat_faktyre' => $result['infakt_privat_faktyre'],

	            'infakt_vatcode' 		=> $result['infakt_vatcode'],

	            'order_my_sklad'		=> $result['order_my_sklad'],
	            
				'order_my_sklad_no'		=> $result['order_my_sklad_no'],

	            'acode'					=> (isset($client_info['customer_my_sklad']) ? $client_info['customer_my_sklad'] : ''),

				'payed'					=> $result['payed'],

				'payed_with'			=> $payed_with,


			);

		}



		/*$pagination = new Pagination();

		$pagination->total = $order_total;

		$pagination->page = $page;

		$pagination->limit = 10;

		$pagination->url = $this->url->link('account/manager_list', 'page={page}', true);



		$data['pagination'] = $pagination->render();



		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($order_total - 10)) ? $order_total : ((($page - 1) * 10) + 10), $order_total, ceil($order_total / 10));*/


		$this->response->setOutput($this->load->view('account/manager_order_list', $data));

	}

	public function search() {

		$this->load->language('account/order');

		if (isset($this->request->get['order_id'])) {

			$order_id = $this->request->get['order_id'];

		} else {

			return new Action('error/not_found');

		}


		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

	
		$data['orders'] = array();

	
		$this->load->model('account/order');
		$this->load->model('account/customer');


		$data['pixsel_tax_status'] = $this->config->get('module_pixsel_price_tax_on');
		$data['lang_with'] = $this->config->get('module_pixsel_price_tax_names_with')[$this->session->data['language']];
		$data['lang_without'] = $this->config->get('module_pixsel_price_tax_names_without')[$this->session->data['language']];


		$order_total = $this->model_account_order->getManagerSearchTotalOrders($order_id);



		$results = $this->model_account_order->getManagerSearchOrders($order_id);

		foreach ($results as $result) {
			$this->load->model('checkout/onepcheckout');

			$countries = $this->model_checkout_onepcheckout->getCountryDeliveries();
			$country = '';
			foreach ($countries as $c) {
				if ($c['country_delivery_id'] == $result['payment_country_id']) {
					$country = $c['name'];
					break;
				}
			}

			$product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);

			$voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);

			$client_info = $this->model_account_customer->getCustomer($result['customer_id']);

			$order_quantity = 0;
			$order_quantity_query = $this->db->query("SELECT quantity FROM `" . DB_PREFIX . "order_product` WHERE `order_id` = '" . $result['order_id'] . "'");
			foreach ($order_quantity_query->rows as $oq) {
				$order_quantity = $order_quantity + $oq['quantity'];
			}


			if (!empty($client_info)) {
				if ($client_info['customer_type'] == 1 || $client_info['customer_type'] == 3) {
					// $customer_name = $client_info['company_name'] . '<br>' . $result['firstname'] . ' ' . $result['lastname'];
					$customer_name = ($client_info['pl_company_name'] ? $client_info['pl_company_name'] . '<br>' . $result['firstname'] . ' ' . $result['lastname'] : $client_info['company_name'] . '<br>' . $result['firstname'] . ' ' . $result['lastname']);
				} else {
					$customer_name = $result['firstname'] . ' ' . $result['lastname'];
				}
			} else {
				$customer_name = $result['firstname'] . ' ' . $result['lastname'];
			}

			$this->load->model('setting/extension');
			$payments = $this->model_setting_extension->getInstalled('payment');
			$pname = array();
	        foreach ($payments as $payment) {
	        	$this->load->language('extension/payment/' . $payment);
	        	$pname[$payment] = $this->language->get('text_title');
	        }
			if ($result['payed'] > 0 && !empty($result['payed_with'])) {
				$payed_with = $pname[$result['payed_with']];
			} else {
				$payed_with = '';
			}

			$totals = $this->model_account_order->getOrderTotals($result['order_id']);

			foreach ($totals as $total) {
				if ($total['code'] == 'shipping' && $total['value'] > 0) {
					$result['total'] = $result['total'] + $total['value'];
				}
			}

			$data['orders'][] = array(

				'order_id'			    => $result['order_id'],

				'name'			        => $customer_name,

				'status'			    => $result['status'],

				'order_status_id'	    => $result['order_status_id'],

				'country_id'			=> $result['shipping_country_id'],

				// 'country'				=> $result['shipping_country'],

				'country'				=> $country,

				'customer_id'			=> $result['customer_id'],

				// 'date_added'			=> date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_added'			=> date("d.m.Y", strtotime($result['date_added'])) . '<br> ' . date("H:i:s", strtotime($result['date_added'])),

				// 'products'			    => ($product_total + $voucher_total),
				'products'			    => $order_quantity,

				'total'			        => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),

				'tax_total'		        => $this->currency->format($this->tax->calc_tax($result['total']), $result['currency_code'], $result['currency_value']),

				'view'			        => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], true),

				'pdf_link'			    => $result['infakt_pdf'],

	            'infakt_need'           => $result['infakt_need'],

	            'infakt_no'             => $result['infakt_no'],

	            'infakt_pdf'            => $result['infakt_pdf'],

	            'infakt_number'         => $result['infakt_number'],

	            'infakt_language'       => $result['infakt_language'],

	            'infakt_currency'       => $result['infakt_currency'],

	            'infakt_pmethod'        => $result['infakt_pmethod'],

	            'infakt_pmethod_ba'     => $result['infakt_pmethod_ba'],

	            'infakt_pmethod_bn'     => $result['infakt_pmethod_bn'],

	            'infakt_vat'            => $result['infakt_vat'],

	            'infakt_nip'            => $result['infakt_nip'],

	            'infakt_privat_faktyre' => $result['infakt_privat_faktyre'],

	            'infakt_vatcode' 		=> $result['infakt_vatcode'],

	            'order_my_sklad'		=> $result['order_my_sklad'],

	            'order_my_sklad_no'		=> $result['order_my_sklad_no'],

	            'acode'					=> (isset($client_info['customer_my_sklad']) ? $client_info['customer_my_sklad'] : ''),

				'payed'					=> $result['payed'],

				'payed_with'			=> $payed_with,


			);

		}

		$this->response->setOutput($this->load->view('account/manager_order_list', $data));

	}



	public function info() {

		$this->load->language('account/order');



		if (isset($this->request->get['order_id'])) {

			$order_id = $this->request->get['order_id'];

		} else {

			$order_id = 0;

		}



		if (!$this->customer->isLogged()) {

			$this->session->data['redirect'] = $this->url->link('account/order/info', 'order_id=' . $order_id, true);



			$this->response->redirect($this->url->link('account/login', '', true));

		}



		$this->load->model('account/order');



		$order_info = $this->model_account_order->getManagerOrder($order_id);



		if ($order_info) {

			$this->document->setTitle($this->language->get('text_order'));



			$url = '';



			if (isset($this->request->get['page'])) {

				$url .= '&page=' . $this->request->get['page'];

			}



			$data['breadcrumbs'] = array();



			$data['breadcrumbs'][] = array(

				'text' => $this->language->get('text_home'),

				'href' => $this->url->link('common/home')

			);



			$data['breadcrumbs'][] = array(

				'text' => $this->language->get('text_account'),

				'href' => $this->url->link('account/account', '', true)

			);



			$data['breadcrumbs'][] = array(

				'text' => $this->language->get('heading_title'),

				'href' => $this->url->link('account/order', $url, true)

			);



			$data['breadcrumbs'][] = array(

				'text' => $this->language->get('text_order'),

				'href' => $this->url->link('account/order/info', 'order_id=' . $this->request->get['order_id'] . $url, true)

			);

			$tax_data = $this->tax->get_tax_inform();

			if (!empty($tax_data)) {
				$data = array_merge($data, $tax_data);
			}

			if (isset($this->session->data['error'])) {

				$data['error_warning'] = $this->session->data['error'];



				unset($this->session->data['error']);

			} else {

				$data['error_warning'] = '';

			}



			if (isset($this->session->data['success'])) {

				$data['success'] = $this->session->data['success'];



				unset($this->session->data['success']);

			} else {

				$data['success'] = '';

			}



			if ($order_info['invoice_no']) {

				$data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];

			} else {

				$data['invoice_no'] = '';

			}



			$data['order_id'] = (int)$this->request->get['order_id'];

			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));



			if ($order_info['payment_address_format']) {

				$format = $order_info['payment_address_format'];

			} else {

				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';

			}



			$find = array(

				'{firstname}',

				'{lastname}',

				'{company}',

				'{address_1}',

				'{address_2}',

				'{city}',

				'{postcode}',

				'{zone}',

				'{zone_code}',

				'{country}'

			);



			$replace = array(

				'firstname' => $order_info['payment_firstname'],

				'lastname'  => $order_info['payment_lastname'],

				'company'   => $order_info['payment_company'],

				'address_1' => $order_info['payment_address_1'],

				'address_2' => $order_info['payment_address_2'],

				'city'      => $order_info['payment_city'],

				'postcode'  => $order_info['payment_postcode'],

				'zone'      => $order_info['payment_zone'],

				'zone_code' => $order_info['payment_zone_code'],

				'country'   => $order_info['payment_country']

			);



			$data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));



			$data['payment_method'] = $order_info['payment_method'];



			if ($order_info['shipping_address_format']) {

				$format = $order_info['shipping_address_format'];

			} else {

				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';

			}



			$find = array(

				'{firstname}',

				'{lastname}',

				'{company}',

				'{address_1}',

				'{address_2}',

				'{city}',

				'{postcode}',

				'{zone}',

				'{zone_code}',

				'{country}'

			);



			$replace = array(

				'firstname' => $order_info['shipping_firstname'],

				'lastname'  => $order_info['shipping_lastname'],

				'company'   => $order_info['shipping_company'],

				'address_1' => $order_info['shipping_address_1'],

				'address_2' => $order_info['shipping_address_2'],

				'city'      => $order_info['shipping_city'],

				'postcode'  => $order_info['shipping_postcode'],

				'zone'      => $order_info['shipping_zone'],

				'zone_code' => $order_info['shipping_zone_code'],

				'country'   => $order_info['shipping_country']

			);
	
			$data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));


			if ($order_info['shipping_code'] == 'inpost_shipping_1.inpost_shipping_1_6') {
				$data['shipping_method'] = strip_tags($order_info['shipping_method']) . ' InPost';
			} else if ($order_info['shipping_code'] == 'inpost_shipping_2.inpost_shipping_2_6') {
				$data['shipping_method'] = strip_tags($order_info['shipping_method']) . ' InPost';
			} else if ($order_info['shipping_code'] == 'pickup.pickup') {
				$data['shipping_method'] = $this->language->get('text_pickup') . ': ' . $order_info['shipping_method'];
			} else {
				$data['shipping_method'] = $order_info['shipping_method'];
			}


			$this->load->model('catalog/product');

			$this->load->model('tool/upload');


			// Additional info
            if ($order_info['infakt_privat_faktyre']) {
                $data['infakt_privat_faktyre'] = $order_info['infakt_privat_faktyre'];
            } else {
                $data['infakt_privat_faktyre'] = 0;
            }

			if ($order_info['infakt_need']) {
                $data['infakt_need'] = $order_info['infakt_need'];
            } else {
                $data['infakt_need'] = '';
            }

            if ($order_info['infakt_vat']) {
                $data['infakt_vat'] = $order_info['infakt_vat'];
            } else {
                $data['infakt_vat'] = '';
            }

            if ($order_info['infakt_nip']) {
                $data['infakt_nip'] = $order_info['infakt_nip'];
            } else {
                $data['infakt_nip'] = '';
            }

            if ($order_info['infakt_vatcode']) {
                $data['infakt_vatcode'] = $order_info['infakt_vatcode'];
            } else {
                $data['infakt_vatcode'] = '';
            }

            if ($order_info['telephone']) {
                $data['telephone'] = $order_info['telephone'];
            } else {
                $data['telephone'] = '';
            }

            if ($order_info['email']) {
                $data['email'] = $order_info['email'];
            } else {
                $data['email'] = '';
            }

            if ($order_info['payed']) {
                $data['payed'] = $order_info['payed'];
            } else {
                $data['payed'] = '';
            }

			$this->load->model('account/customer_group');

			$customer_group_info = $this->model_account_customer_group->getCustomerGroup($order_info['customer_group_id']);

			if ($customer_group_info) {
				$data['customer_group'] = $customer_group_info['name'];
			} else {
				$data['customer_group'] = '';
			}
			$data['customer_group_id'] = $order_info['customer_group_id'];

			$this->load->model('checkout/onepcheckout');
			$countries = $this->model_checkout_onepcheckout->getCountryDeliveries();
			$country = '';
			foreach ($countries as $c) {
				if ($c['country_delivery_id'] == $order_info['shipping_country_id']) {
					$country = $c['name'];
					break;
				}
			}

			// $data['order_country'] = $order_info['shipping_country'];
			$data['order_country'] = $country;

			//OPC
			$data['opc_not_call_me'] = $order_info['opc_not_call_me'];
			$fc_customer_id = $order_info['fc_customer_id'];
			$this->load->model('account/customer');
			if ($fc_customer_id != 0) {
				$data['fc'] = 1;
				$customer_info = $this->model_account_customer->getCustomer($fc_customer_id);
				if ($customer_info['customer_type'] == 2) {
					$data['fc'] = 1;
					$data['fc_firstname'] = $customer_info['firstname'];
					$data['fc_lastname'] = $customer_info['lastname'];
				} else {
					$data['fc'] = 0;
				}
			} else {
				$data['fc'] = 0;
			}

			// Products

			$data['products'] = array();



			$products = $this->model_account_order->getOrderProducts($this->request->get['order_id']);


			foreach ($products as $product) {

				$option_data = array();



				$options = $this->model_account_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);

				$this->load->model('catalog/option');
				$language_id = (int)$this->config->get('config_language_id');
				foreach ($options as $option) {
					$option_name = $option['name'];
					if (!empty($option['product_option_id'])) {
						$po = $this->db->query("SELECT option_id 
							FROM " . DB_PREFIX . "product_option 
							WHERE product_option_id = " . (int)$option['product_option_id'] . " LIMIT 1")->row;
				
						if (!empty($po['option_id'])) {
							$od = $this->db->query("SELECT name 
								FROM " . DB_PREFIX . "option_description 
								WHERE option_id = " . (int)$po['option_id'] . " 
								  AND language_id = " . $language_id . " LIMIT 1")->row;
				
							if (!empty($od['name'])) {
								$option_name = $od['name'];
							}
						}
					}
				
					if ($option['type'] === 'file') {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
						$value = $upload_info ? $upload_info['name'] : '';
					} elseif (!empty($option['product_option_value_id'])) {
						$pov = $this->db->query("SELECT option_value_id 
							FROM " . DB_PREFIX . "product_option_value 
							WHERE product_option_value_id = " . (int)$option['product_option_value_id'] . " LIMIT 1")->row;
				
						if (!empty($pov['option_value_id'])) {
							$ovd = $this->db->query("SELECT name 
								FROM " . DB_PREFIX . "option_value_description 
								WHERE option_value_id = " . (int)$pov['option_value_id'] . " 
								  AND language_id = " . $language_id . " LIMIT 1")->row;
				
							$value = !empty($ovd['name']) ? $ovd['name'] : $option['value'];
						} else {
							$value = $option['value'];
						}
					} else {
						$value = $option['value'];
					}
				
					$option_data[] = array(
						'name'       => $option_name,
						'pixsel_sku' => isset($option['pixsel_sku']) ? $option['pixsel_sku'] : null,
						'value'      => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
						'type'       => $option['type'],
						'product_option_id'        => $option['product_option_id'],
						'product_option_value_id'  => $option['product_option_value_id']
					);
				}

				/*foreach ($options as $option) {

					if ($option['type'] != 'file') {

						$value = $option['value'];

					} else {

						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);



						if ($upload_info) {

							$value = $upload_info['name'];

						} else {

							$value = '';

						}

					}



					$option_data[] = array(

						'name'  => $option['name'],
						'pixsel_sku' => $option['pixsel_sku'],

						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)

					);

				}*/



				$product_info = $this->model_catalog_product->getProduct($product['product_id']);



				if ($product_info) {

					$reorder = $this->url->link('account/order/reorder', 'order_id=' . $order_id . '&order_product_id=' . $product['order_product_id'], true);

				} else {

					$reorder = '';

				}

				if ($this->customer->isLogged() && $data['pixsel_tax_status'] || !$this->config->get('config_customer_price') && $data['pixsel_tax_status']) {
					$tax_price = $this->currency->format($this->tax->calc_tax($product['price']) + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value'], false);
				} else {
					$tax_price = false;
				}

				if ($this->customer->isLogged() && $data['pixsel_tax_status'] || !$this->config->get('config_customer_price') && $data['pixsel_tax_status']) {
					$tax_total = $this->currency->format($this->tax->calc_tax($product['total']) + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'], false);
				} else {
					$tax_total = false;
				}

				// roznica
				$price_roznica = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product['product_id'] . "' AND customer_group_id='1'");
				$roznica = $price_roznica->row['price'];

				$data['products'][] = array(

					'name'     => $product_info['name'],

					'model'    => $product['model'],

					'option'   => $option_data,

					'quantity' => $product['quantity'],

					'tax_price'    => $tax_price,
					'tax_total'    => $tax_total,

					'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),

					'rprice'   => $this->currency->format($roznica + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'rtotal'   => $this->currency->format($roznica * $product['quantity'], $order_info['currency_code'], $order_info['currency_value']),

					'reorder'  => $reorder,

					'return'   => $this->url->link('account/return/add', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], true)

				);

			}



			// Voucher

			$data['vouchers'] = array();



			$vouchers = $this->model_account_order->getOrderVouchers($this->request->get['order_id']);



			foreach ($vouchers as $voucher) {

				$data['vouchers'][] = array(

					'description' => $voucher['description'],

					'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])

				);

			}



			// Totals

			$data['totals'] = array();



			$totals = $this->model_account_order->getOrderTotals($this->request->get['order_id']);



			foreach ($totals as $total) {
				$code = $total['code'];
				
				$lang_key = 'text_' . $code;
				$translated_title = $this->language->get($lang_key);
			
				if ($translated_title == $lang_key || empty($translated_title)) {
					$translated_title = htmlspecialchars(
						preg_replace('#<img\b[^>]*\/?>#i', '', html_entity_decode($total['title']))
					);
				}
			
				if ($code == 'shipping') {
					//if ($total['value'] > 0) {
						if ($order_info['shipping_code'] == 'inpost_shipping_1.inpost_shipping_1_6') {
							$ship_name = ' InPost';
						} else if ($order_info['shipping_code'] == 'inpost_shipping_2.inpost_shipping_2_6') {
							$ship_name = ' InPost';
						} else {
							$ship_name = '';
						}
						$data['totals'][] = array(
							// 'title'     => $translated_title . '' . $ship_name,
							'title'     => $this->language->get('text_shipping'),
							'code'      => $code,
							'text'      => $this->currency->format($total['value'], $order_info['currency_code']),
							'cleantext' => $total['value'],
							'tax_text'  => $this->currency->format(
								$this->tax->calc_tax($total['value']),
								$order_info['currency_code']
							),
						);
					//}
				} else {
					$data['totals'][] = array(
						'title'     => $translated_title,
						'code'      => $code,
						'text'      => $this->currency->format($total['value'], $order_info['currency_code']),
						'tax_text'  => $this->currency->format(
							$this->tax->calc_tax($total['value']),
							$order_info['currency_code']
						),
					);
				}
			}



			$data['comment'] = nl2br($order_info['comment']);



			// History

			$data['histories'] = array();



			$results = $this->model_account_order->getOrderHistories($this->request->get['order_id']);



			foreach ($results as $result) {

				$data['histories'][] = array(

					'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),

					'status'     => $result['status'],

					'comment'    => $result['notify'] ? nl2br($result['comment']) : ''

				);

			}


			$data['pixsel_tax_status'] = $this->config->get('module_pixsel_price_tax_on');
			$data['lang_with'] = $this->config->get('module_pixsel_price_tax_names_with')[$this->session->data['language']];
			$data['lang_without'] = $this->config->get('module_pixsel_price_tax_names_without')[$this->session->data['language']];


			$data['continue'] = $this->url->link('account/order', '', true);



			$data['column_left'] = $this->load->controller('common/column_left');

			$data['column_right'] = $this->load->controller('common/column_right');

			$data['content_top'] = $this->load->controller('common/content_top');

			$data['content_bottom'] = $this->load->controller('common/content_bottom');

			$data['footer'] = $this->load->controller('common/footer');

			$data['header'] = $this->load->controller('common/header');



			$this->response->setOutput($this->load->view('account/order_info_modal', $data));

		} else {

			return new Action('error/not_found');

		}

	}



	public function edit() {

		$this->load->language('account/order');



		if (isset($this->request->get['order_id'])) {

			$order_id = $this->request->get['order_id'];

		} else {

			return new Action('error/not_found');

		}


		$this->load->model('account/order');


		$this->load->model('setting/extension');

		$data['pixsel_tax_status'] = $this->config->get('module_pixsel_price_tax_on');
		$data['lang_with'] = $this->config->get('module_pixsel_price_tax_names_with')[$this->session->data['language']];
		$data['lang_without'] = $this->config->get('module_pixsel_price_tax_names_without')[$this->session->data['language']];

		$order_info = $this->model_account_order->getManagerOrder($order_id);

		if ($order_info) {

			// Inpost

			$data['inpostSandbox'] = $this->config->get('shipping_inpost_shipping_geowidget_sandbox_checkout');
			$data['geoWidgetToken'] = $data['inpostSandbox'] ? $this->config->get('shipping_inpost_shipping_geowidget_sandbox_api_key') : $this->config->get('shipping_inpost_shipping_geowidget_api_key');
      		// Inpost

			$data['order_id'] = $order_id;

	        $payments = $this->model_setting_extension->getInstalled('payment');
	        $shipping = $this->model_setting_extension->getInstalled('shipping');

	        $data['order_payment_method'] = $order_info['payment_code'];
	        $data['order_shipping_method'] = $order_info['shipping_code'];
	        $data['shipping_city'] = $order_info['shipping_city'];
	        $data['shipping_address_1'] = $order_info['shipping_address_1'];
	        $data['shipping_postcode'] = $order_info['shipping_postcode'];
	        $data['order_nip'] = $order_info['infakt_vatcode'];

			$data['payed'] = $order_info['payed'];

			if ($order_info['customer_id']) {
				$this->load->model('account/customer');
				$client_info = $this->model_account_customer->getCustomer($order_info['customer_id']);

				if (!empty($client_info)) {
					if ($client_info['customer_type'] == 1 || $client_info['customer_type'] == 3) {
						// $data['customer_name'] = $client_info['company_name'] . '<br>' . $order_info['firstname'] . ' ' . $order_info['lastname'];
						// $data['customer_name_wbr'] = $client_info['company_name'] . ' | ' . $order_info['firstname'] . ' ' . $order_info['lastname'];
						$data['customer_name'] = (!empty($client_info['company_name']) ? $client_info['company_name'] : $client_info['pl_company_name']) . '<br>' . $order_info['firstname'] . ' ' . $order_info['lastname'];
						$data['customer_name_wbr'] = (!empty($client_info['company_name']) ? $client_info['company_name'] : $client_info['pl_company_name']) . ' | ' . $order_info['firstname'] . ' ' . $order_info['lastname'];
					} else {
						$data['customer_name'] = $order_info['firstname'] . ' ' . $order_info['lastname'];
						$data['customer_name_wbr'] = $data['customer_name'];
					}
				} else {
					$data['customer_name'] = $order_info['firstname'] . ' ' . $order_info['lastname'];
					$data['customer_name_wbr'] = $data['customer_name'];
				}
			} else {
				$data['customer'] = '';

				$data['customer_name'] = $order_info['firstname'] . ' ' . $order_info['lastname'];
				$data['customer_name_wbr'] = $data['customer_name'];
			}
			$data['customer_group_id'] = $order_info['customer_group_id'];


	        $data['payment_methods'] = array();
			$pname = array();
	        /*foreach ($payments as $payment) {
				$this->load->language('extension/payment/' . $payment);
				$pname[$payment] = $this->language->get('text_title'); // $this->language->get('text_title');
				$data['payment_methods'][] = array(
					'code' => $payment,
					'name' => $this->language->get('text_title') // $this->language->get('text_title') // $this->language->get('heading_title')
				);
	        }*/
			$pmn_results = $this->model_setting_extension->getExtensions('payment');
			$payment_address = array(
				'country_id' => $order_info['payment_country_id'],
				'zone_id'    => $order_info['payment_zone_id'],
				'postcode'   => $order_info['payment_postcode'],
				'city'       => $order_info['payment_city'],
				'address_1'  => $order_info['payment_address_1'],
				'address_2'  => $order_info['payment_address_2']
			);
			foreach ($pmn_results as $presult) {
				if ($this->config->get('payment_' . $presult['code'] . '_status')) {
		
					$this->load->model('extension/payment/' . $presult['code']);
	
					$pmethod = $this->{'model_extension_payment_' . $presult['code']}->getMethod($payment_address, 0);
	
					if ($pmethod) {
						$pmethod['code'] = $presult['code'];
						$pmethod_data[] = $pmethod;
						$pname[$presult['code']] = $pmethod['title'];
					}
				}
			}
			$data['payment_methods'] = $pmethod_data;

			if ($order_info['payed'] > 0 && !empty($order_info['payed_with'])) {
				$data['payed_with'] = $pname[$order_info['payed_with']];
			} else {
				$data['payed_with'] = '';
			}


	        $data['country_id'] = $order_info['shipping_country_id'];
	        //$this->load->model('localisation/country');
			$this->load->model('checkout/onepcheckout');
	        $this->load->model('localisation/zone');
	        //$countries = $this->model_localisation_country->getCountries();
			$countries = $this->model_checkout_onepcheckout->getCountryDeliveries();
	        $countrys = array();
	        foreach($countries as $country) {
	            $zone = $this->model_localisation_zone->getZonesZoneByCountryId($country['country_id']);
	            $countrys[] = array(
	                'country_id'  => $country['country_id'],
					'country_delivery_id'  => $country['country_delivery_id'],
	                'name'        => $country['name'],
	                'geo_zone_id' => $zone['geo_zone_id']
	            );
	        }
			/*$data['countries'] = $this->model_checkout_onepcheckout->getCountryDeliveries();
			$country_info = $this->model_localisation_country->getCountry($country_id);	
			if ($country_info) {
				$this->load->model('localisation/zone');
				$data['zones'] = $this->model_localisation_zone->getZonesByCountryId($country_id);
			}*/
	        $data['countries'] = $countrys;


	        $data['shipping_methods'] = array();
			$shipping_address = array(
				'country_id' => $order_info['shipping_country_id'],
				'zone_id'    => $order_info['shipping_zone_id'],
				'postcode'   => $order_info['shipping_postcode'],
				'city'       => $order_info['shipping_city'],
				'address_1'  => $order_info['shipping_address_1'],
				'address_2'  => $order_info['shipping_address_2']
			);	        
			$shp_results = $this->model_setting_extension->getExtensions('shipping');

			foreach ($shp_results as $sresult) {
				if ($this->config->get('shipping_' . $sresult['code'] . '_status')) {
					$this->load->model('extension/shipping/' . $sresult['code']);

					$quote = $this->{'model_extension_shipping_' . $sresult['code']}->getQuote($shipping_address);

					if($sresult['code'] == 'easyship'){
						foreach($quote['easyship0']['quote'] as $val_quote){
							$smethod_data[] = array(
								'code'       => $val_quote['code'],
								'image'      => $val_quote['image'],
								'title'      => $val_quote['title'],
								'quote'      => $val_quote,
								'sort_order' => $val_quote['sort_order'],
								'error'      => $val_quote['error']
							);
						}
						continue;
					}

					if ($quote){
						$smethod_data[] = array(
							'code'       => $sresult['code'],
							'title'      => $quote['title'],
							'quote'      => $quote['quote'],
							'sort_order' => $quote['sort_order'],
							'error'      => $quote['error']
						);
					}
				}
			}
			$data['shipping_methods'] = $smethod_data;

	        //foreach ($shipping as $ship) {			

			
			//foreach ($shipping as $ship) {
	            
	        // Inpost
	        /*if ($this->config->get('shipping_inpost_shipping_status') && $ship == 'inpost_shipping') {
	            $this->load->model('extension/shipping/inpost_shipping');
	            $inpostQuotes = $this->model_extension_shipping_inpost_shipping->getInpostQuotes(['country_id' => 170, 'zone_id' => 6]);
	            foreach ($inpostQuotes as $quote) {
	                $this->load->language('extension/shipping/' . $quote['code'] . '_6');
	                $data['shipping_methods'][] = array(
	                    'code' => $quote['quote'][$quote['code'] . '_6']['code'],
	                    'name' => $quote['title'] //$this->language->get('text_title') // $quote['title'] . ' InPost'
	                );
	            }
	        } else {
	        // Inpost

	            $this->load->language('extension/shipping/' . $ship);
	            if ($ship == 'pickup') {
	            	$ship = 'pickup.pickup';
	            	$shipname = $this->language->get('text_subtitle') . ': ' . $this->language->get('text_title');
	            } else {
	            	$shipname = $this->language->get('text_title');
	            }
	            $data['shipping_methods'][] = array(
	                'code' => $ship,
	                'name' =>$this->language->get('text_title') //  $shipname // $this->language->get('heading_title')
	            );
	            }
	        }

	        $data['inpostParcelLocker'] = !empty($order_info['parcelLocker']) ? $order_info['parcelLocker'] : '';
            if (!empty($order_info['parcelLocker'])) {
            	$data['parcelAddressLocker'] = $order_info['shipping_city'] . ', ' . $order_info['shipping_address_1'] . ', ' . $order_info['parcelLocker'];
            }*/


			$this->load->model('catalog/product');


			// Products

			$data['products'] = array();



			$products = $this->model_account_order->getOrderProducts($this->request->get['order_id']);



			foreach ($products as $product) {

				$option_data = array();



				$options = $this->model_account_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);

				$this->load->model('catalog/option');
				$language_id = (int)$this->config->get('config_language_id');
				foreach ($options as $option) {
					$option_name = $option['name'];
					if (!empty($option['product_option_id'])) {
						$po = $this->db->query("SELECT option_id 
							FROM " . DB_PREFIX . "product_option 
							WHERE product_option_id = " . (int)$option['product_option_id'] . " LIMIT 1")->row;
				
						if (!empty($po['option_id'])) {
							$od = $this->db->query("SELECT name 
								FROM " . DB_PREFIX . "option_description 
								WHERE option_id = " . (int)$po['option_id'] . " 
								  AND language_id = " . $language_id . " LIMIT 1")->row;
				
							if (!empty($od['name'])) {
								$option_name = $od['name'];
							}
						}
					}
				
					if ($option['type'] === 'file') {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
						$value = $upload_info ? $upload_info['name'] : '';
					} elseif (!empty($option['product_option_value_id'])) {
						$pov = $this->db->query("SELECT option_value_id 
							FROM " . DB_PREFIX . "product_option_value 
							WHERE product_option_value_id = " . (int)$option['product_option_value_id'] . " LIMIT 1")->row;
				
						if (!empty($pov['option_value_id'])) {
							$ovd = $this->db->query("SELECT name 
								FROM " . DB_PREFIX . "option_value_description 
								WHERE option_value_id = " . (int)$pov['option_value_id'] . " 
								  AND language_id = " . $language_id . " LIMIT 1")->row;
				
							$value = !empty($ovd['name']) ? $ovd['name'] : $option['value'];
						} else {
							$value = $option['value'];
						}
					} else {
						$value = $option['value'];
					}
				
					$option_data[] = array(
						'name'       => $option_name,
						'pixsel_sku' => isset($option['pixsel_sku']) ? $option['pixsel_sku'] : null,
						'value'      => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
						'type'       => $option['type'],
						'product_option_id'        => $option['product_option_id'],
						'product_option_value_id'  => $option['product_option_value_id']
					);
				}

				/*foreach ($options as $option) {

					$value = $option['value'];


					$option_data[] = array(

						'product_option_value_id'  => $option['product_option_value_id'],
						'name'  => $option['name'],
						'pixsel_sku' => $option['pixsel_sku'],

					    'product_option_value_id' => $option['product_option_value_id'],

						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)

					);

				}*/



				$product_info = $this->model_catalog_product->getProduct($product['product_id']);



				if ($product_info) {

					$reorder = $this->url->link('account/order/reorder', 'order_id=' . $order_id . '&order_product_id=' . $product['order_product_id'], true);

				} else {

					$reorder = '';

				}

				if ($this->customer->isLogged() && $data['pixsel_tax_status'] || !$this->config->get('config_customer_price') && $data['pixsel_tax_status']) {
					$tax_price = $this->currency->format($this->tax->calc_tax($product['price']) + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value'], false);
				} else {
					$tax_price = false;
				}

				if ($this->customer->isLogged() && $data['pixsel_tax_status'] || !$this->config->get('config_customer_price') && $data['pixsel_tax_status']) {
					$tax_total = $this->currency->format($this->tax->calc_tax($product['total']) + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'], false);
				} else {
					$tax_total = false;
				}

				// roznica
				$price_roznica = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product['product_id'] . "' AND customer_group_id='1'");
				$roznica = $price_roznica->row['price'];

				$data['products'][] = array(

					'order_product_id' => $product['order_product_id'],
					'product_id'       => $product['product_id'],
					'name'    	 	   => $product_info['name'],
					'model'    		   => $product['model'],
					'option'   		   => $option_data,
					'quantity'		   => $product['quantity'],
					
					'tax_price'    	   => $this->currency->format($this->tax->calc_tax($product['price']), $order_info['currency_code'], $order_info['currency_value']),
					'tax_total'  	   => $this->currency->format($this->tax->calc_tax($product['total']), $order_info['currency_code'], $order_info['currency_value']),

					'price'    		   => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'    		   => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),

					'rprice'    	   => $this->currency->format($roznica + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'rtotal'    	   => $this->currency->format($roznica * $product['quantity'], $order_info['currency_code'], $order_info['currency_value']),

					'href'     		   => $this->url->link('catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $product['product_id'], true)

				);

			}



			// Voucher

			$data['vouchers'] = array();



			$vouchers = $this->model_account_order->getOrderVouchers($this->request->get['order_id']);



			foreach ($vouchers as $voucher) {

				$data['vouchers'][] = array(

					'description' => $voucher['description'],

					'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])

				);

			}



			// Totals

			$data['totals'] = array();



			$totals = $this->model_account_order->getOrderTotals($this->request->get['order_id']);



			foreach ($totals as $total) {
				$code = $total['code'];
				
				$lang_key = 'text_' . $code;
				$translated_title = $this->language->get($lang_key);
			
				if ($translated_title == $lang_key || empty($translated_title)) {
					$translated_title = htmlspecialchars(
						preg_replace('#<img\b[^>]*\/?>#i', '', html_entity_decode($total['title']))
					);
				}
			
				if ($code == 'shipping') {
					if ($order_info['shipping_code'] == 'inpost_shipping_1.inpost_shipping_1_6') {
						$ship_name = ' InPost';
					} else if ($order_info['shipping_code'] == 'inpost_shipping_2.inpost_shipping_2_6') {
						$ship_name = ' InPost';
					} else {
						$ship_name = '';
					}
					$data['totals'][] = array(
						// 'title'     => $translated_title . '' . $ship_name,
						'title'     => $this->language->get('text_shipping'),
						'code'      => $code,
						'text'      => $this->currency->format($total['value'], $order_info['currency_code']),
						'cleantext' => $total['value'],
						'tax_text'  => $this->currency->format(
							$this->tax->calc_tax($total['value']),
							$order_info['currency_code']
						),
					);
				} else {
					$data['totals'][] = array(
						'title'     => $translated_title,
						'code'      => $code,
						'text'      => $this->currency->format($total['value'], $order_info['currency_code']),
						'tax_text'  => $this->currency->format(
							$this->tax->calc_tax($total['value']),
							$order_info['currency_code']
						),
					);
				}
			}



			$data['comment'] = nl2br($order_info['comment']);



			$this->response->setOutput($this->load->view('account/order_edit_modal', $data));

		} else {

			return new Action('error/not_found');

		}

	}
    public function editOrder() {
        $json = [];

        $order_id = $this->request->get['order_id'];
        $customer_id = $this->request->post['customer_id'];
        $payment_code = $this->request->post['payment_method'];    
        $customer_new_name = $this->request->post['customer_new_name'];

		$payed = $this->request->post['payed'];

        if ($customer_id == 0) {
            $oinfo_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE order_id = '" . (int)$order_id . "'");
            $oinfo = $oinfo_query->row;
            $customer_id = $oinfo['customer_id'];
        }

        if ($order_id) {
	        if (!empty($customer_id) && $customer_id != '0') {
		        $this->load->model('account/customer');
		        $customer_info = $this->model_account_customer->getCustomer($customer_id);
		        $customer_group_id = $customer_info['customer_group_id'];

		        if ($customer_info) {
		        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($customer_info['firstname']) . "', lastname = '" . $this->db->escape($customer_info['lastname']) . "', email = '" . $this->db->escape($customer_info['email']) . "', telephone = '" . $this->db->escape($customer_info['telephone']) . "', customer_group_id = '" . (int)$customer_info['customer_group_id'] . "' WHERE order_id = '" . (int)$order_id . "'");
		        }
	        } else if (!empty($customer_new_name)) {
		        $nameParts = explode(' ', $customer_new_name, 2);
		        $firstName = $nameParts[0];
		        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

		        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET customer_id = 0, firstname = '" . $this->db->escape($firstName) . "', lastname = '" . $this->db->escape($lastName) . "', customer_group_id = 1 WHERE order_id = '" . (int)$order_id . "'");
	        }

			if ($customer_group_id == 0) {
				$customer_group_id = 1;
			}

	        // shipping
	        $shipping_code = $this->request->post['shipping_method'];
	        $this->load->model('localisation/country');
	        $this->load->model('localisation/zone');
	        $shipping_country_id = $this->request->post['country_id'];
	        $shipping_country_arr = $this->model_localisation_country->getCountry($shipping_country_id);
	        $shipping_country_name = $shipping_country_arr['name'];
	        $shipping_city = $this->request->post['city'];
	        $shipping_address_1 = $this->request->post['address_1'];
	        $shipping_postcode = $this->request->post['postcode'];
	        $parcelLocker = $this->request->post['parcelLocker'];
	        $shipping_code_arr = explode(".", $shipping_code);
	        /*if (isset($shipping_code_arr[1])) {
	            $this->load->language('extension/shipping/' . $shipping_code_arr[1]);
	            $shipping_method = $this->language->get('text_title');
	        } else {
	            $this->load->language('extension/shipping/' . $shipping_code);
	            $shipping_method = $this->language->get('text_title');
	        }*/
			$shipping_method = '';
			$settings = $this->config->get('shipping_easyship');
			foreach ((array)$settings as $sg_code => $group) {

				if ($group['status'] == 'off'){
					continue;
				}
				foreach ($group['shipping_methods'] as $code => $method) {
					$shcode = $sg_code . '.' . $code;
					if ($shcode == $shipping_code) {
						$shipping_method = $method['name'][$this->config->get('config_language_id')];
						break 2;
					}
				}
			}

	        $nip = $this->request->post['order_nip'];

	        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET shipping_code = '" . $this->db->escape($shipping_code) . "', shipping_method = '" . $this->db->escape($shipping_method) . "', shipping_city = '" . $this->db->escape($shipping_city) . "', shipping_address_1 = '" . $this->db->escape($shipping_address_1) . "', shipping_postcode = '" . $this->db->escape($shipping_postcode) . "', payment_city = '" . $this->db->escape($shipping_city) . "', payment_address_1 = '" . $this->db->escape($shipping_address_1) . "', payment_postcode = '" . $this->db->escape($shipping_postcode) . "', parcelLocker = '" . $this->db->escape($parcelLocker) . "', shipping_country_id = '" . $this->db->escape($shipping_country_id) . "', shipping_country = '" . $this->db->escape($shipping_country_name) . "', payment_country_id = '" . $this->db->escape($shipping_country_id) . "', payment_country = '" . $this->db->escape($shipping_country_name) . "', infakt_nip = '" . $this->db->escape($nip) . "', infakt_vatcode = '" . $this->db->escape($nip) . "', payed = '" . (int)$payed . "' WHERE order_id = '" . (int)$order_id . "'");
	        // shipping
	        
	        // payment
	        $this->load->language('extension/payment/' . $payment_code);
	        $payment_method = $this->language->get('text_title');
	        $this->db->query("UPDATE `" . DB_PREFIX . "order` SET payment_code = '" . $this->db->escape($payment_code) . "', payment_method = '" . $this->db->escape($payment_method) . "' WHERE order_id = '" . (int)$order_id . "'");
	        // payment

	        // Обновляем товары согласно группы клиента
	        $ototal = 0;
	        $products_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
	        $order_products = $products_query->rows;
	        foreach ($order_products as $order_product) {
	            $product_id = $order_product['product_id'];
	            $order_product_id = $order_product['order_product_id'];
	            $quantity = $order_product['quantity'];
	            $price_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . $customer_group_id . "'");
	            $new_price = $price_query->row['price'];
	            $new_total = $new_price*$quantity;
	            $ototal = $ototal + $new_total;

	            $this->db->query("UPDATE " . DB_PREFIX . "order_product SET price = '" . $new_price . "', total = '" . $new_total . "' WHERE product_id = '" . (int)$product_id . "' AND order_id = '" . $order_id . "' AND order_product_id = '" . $order_product_id . "'");
	        }
	        if ($ototal > 0) {
	        	$this->db->query("UPDATE " . DB_PREFIX . "order SET total = '" . $ototal . "' WHERE order_id = '" . $order_id . "'");
	            $this->db->query("UPDATE " . DB_PREFIX . "order_total SET value = '" . $ototal . "' WHERE order_id = '" . $order_id . "' AND code = 'sub_total'");
	        }
	        /// Обновляем товары согласно группы клиента

			// Уведомление в Telegram
			// $manager = $this->customer->getFirstname() . ' ' . $this->customer->getLastname();
			// $this->load->controller('extension/module/telnotification/getEditOrderData', array('order_id' => $order_id, 'editedby' => $manager));

	        $json['success'] = 'Замовлення успішно оновлено';
        } else {
        	$json['error'] = 'ID замовлення не зазначено';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }



	public function editInfakt() {
		if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

			$this->load->model('account/order');

			$this->load->language('account/order');

			if (isset($this->request->get['order_id'])) {
				$order_id = (int)$this->request->get['order_id'];
			} else {
				return new Action('error/not_found');
			}

			$order_info = $this->model_account_order->getManagerOrder($order_id);			

			$data = array();

			$data['order_id'] = $order_id;

            if ($order_info['infakt_need']) {
                $data['infakt_need'] = $order_info['infakt_need'];
            } else {
                $data['infakt_need'] = '';
            }

            if ($order_info['infakt_no']) {
                $data['infakt_no'] = $order_info['infakt_no'];
            } else {
                $data['infakt_no'] = '';
            }

            if ($order_info['infakt_pdf']) {
                $data['infakt_pdf'] = $order_info['infakt_pdf'];
            } else {
                $data['infakt_pdf'] = '';
            }

            if ($order_info['infakt_number']) {
                $data['infakt_number'] = $order_info['infakt_number'];
            } else {
                $data['infakt_number'] = '';
            }

            if ($order_info['infakt_language']) {
                $data['infakt_language'] = $order_info['infakt_language'];
            } else {
                $data['infakt_language'] = '';
            }

            if ($order_info['infakt_currency']) {
                $data['infakt_currency'] = $order_info['infakt_currency'];
            } else {
                $data['infakt_currency'] = '';
            }

            if ($order_info['infakt_pmethod']) {
                $data['infakt_pmethod'] = $order_info['infakt_pmethod'];
            } else {
                $data['infakt_pmethod'] = '';
            }

            if ($order_info['infakt_pmethod_ba']) {
                $data['infakt_pmethod_ba'] = $order_info['infakt_pmethod_ba'];
            } else {
                $data['infakt_pmethod_ba'] = '';
            }

            if ($order_info['infakt_pmethod_bn']) {
                $data['infakt_pmethod_bn'] = $order_info['infakt_pmethod_bn'];
            } else {
                $data['infakt_pmethod_bn'] = '';
            }

            if ($order_info['infakt_vat']) {
                $data['infakt_vat'] = $order_info['infakt_vat'];
            } else {
                $data['infakt_vat'] = '';
            }

            if ($order_info['infakt_nip']) {
                $data['infakt_nip'] = $order_info['infakt_nip'];
            } else {
                $data['infakt_nip'] = '';
            }

            if ($order_info['country_id']) {
                $data['country_id'] = $order_info['country_id'];
            } else {
                $data['country_id'] = '';
            }


			$this->response->setOutput($this->load->view('account/infakt_set_modal', $data));
		} else {
		  $this->response->redirect($this->url->link('error/not_found', '', true));
		}
  	}


	public function bezvat() {

		$this->load->language('account/order');



		if (isset($this->request->get['order_id'])) {

			$order_id = $this->request->get['order_id'];

		} else {

			$order_id = 0;

		}



		if (!$this->customer->isLogged()) {

			$this->session->data['redirect'] = $this->url->link('account/order/info', 'order_id=' . $order_id, true);



			$this->response->redirect($this->url->link('account/login', '', true));

		}



		$this->load->model('account/order');



		$order_info = $this->model_account_order->getManagerOrder($order_id);



		if ($order_info) {

			$this->document->setTitle($this->language->get('text_order'));



			$url = '';



			if (isset($this->request->get['page'])) {

				$url .= '&page=' . $this->request->get['page'];

			}



			$data['breadcrumbs'] = array();



			$data['breadcrumbs'][] = array(

				'text' => $this->language->get('text_home'),

				'href' => $this->url->link('common/home')

			);



			$data['breadcrumbs'][] = array(

				'text' => $this->language->get('text_account'),

				'href' => $this->url->link('account/account', '', true)

			);



			$data['breadcrumbs'][] = array(

				'text' => $this->language->get('heading_title'),

				'href' => $this->url->link('account/order', $url, true)

			);



			$data['breadcrumbs'][] = array(

				'text' => $this->language->get('text_order'),

				'href' => $this->url->link('account/order/info', 'order_id=' . $this->request->get['order_id'] . $url, true)

			);



			$data['order_id'] = (int)$this->request->get['order_id'];

			$data['country_id'] = (int)$order_info['country_id'];

			$data['infakt_vat'] = $order_info['infakt_vat'];




			$this->response->setOutput($this->load->view('account/bezvat_modal', $data));

		} else {

			return new Action('error/not_found');

		}

	}


	public function orderStatusModal() {

		$this->load->language('account/order');



		if (isset($this->request->get['order_id'])) {

			$order_id = $this->request->get['order_id'];

		} else {

			$order_id = 0;

		}



		if (!$this->customer->isLogged()) {

			// $this->session->data['redirect'] = $this->url->link('account/manager_list', 'order_id=' . $order_id, true);



			$this->response->redirect($this->url->link('account/login', '', true));

		}



		$this->load->model('account/order');


		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();



		$order_info = $this->model_account_order->getManagerOrder($order_id);



		if ($order_info) {

			$this->document->setTitle($this->language->get('text_order'));



			$url = '';



			if (isset($this->request->get['page'])) {

				$url .= '&page=' . $this->request->get['page'];

			}



			$data['breadcrumbs'] = array();



			$data['breadcrumbs'][] = array(

				'text' => $this->language->get('text_home'),

				'href' => $this->url->link('common/home')

			);



			$data['breadcrumbs'][] = array(

				'text' => $this->language->get('text_account'),

				'href' => $this->url->link('account/account', '', true)

			);



			$data['breadcrumbs'][] = array(

				'text' => $this->language->get('heading_title'),

				'href' => $this->url->link('account/order', $url, true)

			);



			$data['breadcrumbs'][] = array(

				'text' => $this->language->get('text_order'),

				'href' => $this->url->link('account/order/info', 'order_id=' . $this->request->get['order_id'] . $url, true)

			);



			$data['order_id'] = (int)$this->request->get['order_id'];

			$data['order_status_id'] = (int)$order_info['order_status_id'];


			$this->response->setOutput($this->load->view('account/ostatus_modal', $data));

		} else {

			return new Action('error/not_found');

		}

	}


    public function updateInfakt() {
        if (isset($this->request->post['infakt_language']) && isset($this->request->post['infakt_currency']) && isset($this->request->post['infakt_pmethod']) && isset($this->request->post['infakt_vat'])) {
                        
	        $order_id = $this->request->post['order_id'];
                        
            $infakt_language = $this->request->post['infakt_language'];
            $infakt_currency = $this->request->post['infakt_currency'];
            $infakt_pmethod = $this->request->post['infakt_pmethod'];
            $infakt_vat = $this->request->post['infakt_vat'];

            if ($infakt_pmethod == 'transfer') {
                $infakt_pmethod_ba = 'PL24160000031804560830000003';
                $infakt_pmethod_bn = 'Paribas Bank';
            } else if ($infakt_pmethod == 'transfer_eur') {
                $infakt_pmethod == 'transfer';
                $infakt_pmethod_ba = 'PL37160014621804560830000006';
                $infakt_pmethod_bn = 'Paribas Bank';
            } else {
                $infakt_pmethod_ba = 'null';
                $infakt_pmethod_bn = 'null';
            }

            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET infakt_language = '" . $infakt_language . "', infakt_currency = '" . $infakt_currency . "', infakt_pmethod = '" . $infakt_pmethod . "', infakt_pmethod_ba = '" . $infakt_pmethod_ba . "', infakt_pmethod_bn = '" . $infakt_pmethod_bn . "', infakt_vat = '" . $infakt_vat . "' WHERE order_id = '" . (int)$order_id . "'");

            // echo $infakt_vat;
            $this->response->addHeader('Content-Type: application/json');
            $json = array('success' => 'success');
            echo json_encode($json);
        } else {
        	return new Action('error/not_found');
        }
    }

    public function updateBezvat() {
        if (isset($this->request->post['order_id']) && isset($this->request->post['vat'])) {
                        
	        $order_id = $this->request->post['order_id'];
	        $vat = $this->request->post['vat'];
                        
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET infakt_vat = '" . $vat . "' WHERE order_id = '" . (int)$order_id . "'");

            $this->response->addHeader('Content-Type: application/json');
            $json = array('success' => 'success');
            echo json_encode($json);
        } else {
        	return new Action('error/not_found');
        }
    }


	public function createInfaktno() {
    	set_time_limit(30000);

        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);

        $this->load->model('account/customer');
        $this->load->model('catalog/product');
        $this->load->model('checkout/order');

		if (isset($this->request->get['order_id'])) {
			$order_id = (int)$this->request->get['order_id'];
		} else {
			return new Action('error/not_found');
		}

        $order_info = $this->model_checkout_order->getOrder($order_id);
                    
		if ($order_info['customer_id'] != 0) {
        	$customer_info = $this->model_account_customer->getCustomer($order_info['customer_id']);
        }

        // LOG
        file_put_contents(DIR_LOGS . "infakt/infakt.log", PHP_EOL . '' . date("d-m-Y") . 'START', FILE_APPEND);
                    
        if ($order_info) {
        	if (!empty($order_info['infakt_pmethod'])) {
            	$payment_method = $order_info['infakt_pmethod'];
                if ($payment_method == 'transfer_eur') {
                	$payment_method = 'transfer';
                }
                $bank_account = ( !empty($order_info['infakt_pmethod_ba']) ? $order_info['infakt_pmethod_ba'] : 'null' );
                $bank_name = ( !empty($order_info['infakt_pmethod_bn']) ? $order_info['infakt_pmethod_bn'] : 'null' );
            } else {
            	if ($order_info['payment_code'] == 'cod') {
                	$payment_method = 'delivery';
                    $bank_account = null;
                    $bank_name = null;
                } else if ($order_info['payment_code'] == 'przelewy24') {
                	$payment_method = 'przelewy24';
                    $bank_account = null;
                    $bank_name = null;
                } else if ($order_info['payment_code'] == 'bank_transfer' && $order_info['currency_code'] == 'EUR') {
                	$payment_method = 'transfer';
                    $bank_account = 'PL37160014621804560830000006';
                    $bank_name = 'Paribas Bank';
                } else if ($order_info['payment_code'] == 'bank_transfer') {
                	$payment_method = 'transfer';
                    $bank_account = 'PL24160000031804560830000003';
                    $bank_name = 'Paribas Bank';
                } else if ($order_info['payment_code'] == 'przelewycard24') {
                	$payment_method = 'card';
                    $bank_account = null;
					$bank_name = null;
                } else if ($order_info['payment_code'] == 'przelewygapay24') {
                	$payment_method = 'przelewy24';
                    $bank_account = null;
                    $bank_name = null;
                } else if ($order_info['payment_code'] == 'przelewyblik24') {
                	$payment_method = 'przelewy24';
                    $bank_account = null;
                    $bank_name = null;
                } else if ($order_info['payment_code'] == 'cheque') {
                	$payment_method = 'cash';
                    $bank_account = null;
                    $bank_name = null;
                } else {
                	$payment_method = 'cash';
                    $bank_account = null;
                    $bank_name = null;
                }
            }

            if ($order_info['infakt_language'] == 'en') {
                $language_id = 2;
            	$szt = 'pcs';
            } else {
                $language_id = 5;
            	$szt = 'szt';
            }

            $products = $this->model_checkout_order->getOrderProducts($order_id);
            $products_demand = array();
            foreach ($products as $product) {
                $option_data = array();
                     
                $option_data = $this->model_checkout_order->getOrderOptions($order_id, $product['order_product_id']);
                                    
                $option_data_out = array();
                $opts =  $this->model_catalog_product->getProductOptions($product['product_id']);
                foreach ($option_data as $option) {
                    foreach ($opts as $key => $value) {
                        foreach ($value['product_option_value'] as $key2 => $value2) {
                            if($option['product_option_value_id'] == $value2['product_option_value_id']) {
                            	// echo $option['product_option_value_id'] ." == " .$value2['product_option_value_id']; exit;
                                // $oq1 = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = '" . (int)$option['product_option_id'] . "'");
                                $oq1 = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "'");
                                $oq2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value_description WHERE option_value_id = '" . (int)$oq1->row['option_value_id'] . "' AND language_id = '".$language_id."'");
                                $oname = $oq2->row['name'];
                            	$code = $value2['pixsel_sku'];
                        	}
                    	}
                    }
                    $value = $option['value'];

                    $option_data_out[] = array(
                        'name'  => $option['name'],
                        'code'  => $code,
                    	'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value) . ' (' . $code . ')',
                    );

                    $queryzpo = $this->model_catalog_product->getProduct($product['product_id'], $language_id);
                    if($code != '') {
                        if ($order_info['infakt_vat'] == 0) {
                            $products_demand[] = array(
                                'name'       => $product['name'] . ', ' . ucfirst($oname) . ' (' . $code . ')',
                                'unit'  => $szt,
                                'quantity'   => $product['quantity'],
                                'gross_price'  => ( (float)$this->currency->format($this->tax->calc_tax($product['price']), $order_info['infakt_currency'])*$product['quantity'])*100,
                                'net_price'  => ( (float)$this->currency->format($this->tax->calc_tax($product['price']), $order_info['infakt_currency'])*$product['quantity'])*100,
                            	'tax_symbol' => '0'
                        	);
                        } else {
                            $products_demand[] = array(
                                'name'       => $product['name'] . ', ' . ucfirst($oname) . ' (' . $code . ')',
                                'unit'  => $szt,
                                'quantity'   => $product['quantity'],
                                'gross_price'  => ($this->tax->calculate($product['price'], $product['tax'], $this->config->get('config_tax'))*$product['quantity'])*100,
                            	'tax_symbol' => $this->config->get('module_pixsel_price_tax_rate')
                        	);
                    	}
                	}
            	}
            }

            $curl = curl_init();

            if ( $order_info['shipping_country_id'] != 170 &&  !empty($order_info['infakt_vatcode']) ) {
            	$data_create = array(
                	"invoice" => array(
                    	"currency" => ( !empty($order_info['infakt_currency']) ? $order_info['infakt_currency'] : 'PLN' ),
                        "locale" => ( !empty($order_info['infakt_language']) ? $order_info['infakt_language'] : 'pl' ),
                        "payment_method" => $payment_method,
                        "bank_account" => $bank_account,
                        "bank_name" => $bank_name,
						"sale_type" => "merchandise",
                        "client_company_name" => ( !empty(utf8_encode(html_entity_decode($customer_info['company_name']))) ? html_entity_decode($customer_info['company_name']) :  $order_info['firstname'] . ' ' . $order_info['lastname'] ),
                        "nip" => str_replace(" ", "", trim($order_info['infakt_vatcode'])),
                        "services" => $products_demand,
                        "notes" => 'Numer zamówienia ' . $order_info['order_id']
                    )
                );
            } else {
	            if ( !empty($order_info['infakt_nip']) || $order_info['infakt_privat_faktyre'] == 1 ) {
	                if (!empty($order_info['infakt_nip'])) {
	                    $curlHandler = curl_init();
	                    curl_setopt($curlHandler, CURLOPT_ENCODING, 'gzip');
	                    curl_setopt($curlHandler, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	                    curl_setopt($curlHandler, CURLOPT_SSL_VERIFYHOST, false);
	                    curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);
	                    curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
	                    curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, true);
	                    curl_setopt($curlHandler, CURLOPT_USERAGENT, 'Opera 10.00');
	                    curl_setopt($curlHandler, CURLOPT_URL, 'https://api.infakt.pl/v3/clients.json?q[nip_eq]=' . str_replace(" ", "", trim($order_info['infakt_nip'])));
	                    curl_setopt($curlHandler, CURLOPT_HTTPHEADER, array(
	                        'Content-Type: application/json',
	                    	'X-inFakt-ApiKey: d5c2a4613d6bb085e7e138ebf180a4ad4d50f331')
	                    );
	                    curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, "GET");
	                    $result_client_arr = curl_exec($curlHandler);
	                    curl_close($curlHandler);
	                    $result_client = json_decode($result_client_arr, true);

	                    if (isset($result_client['entities'][0])) {
	                    	$company_name = $result_client['entities'][0]['company_name'];
	                    } else {
	                    	$company_name = '';
	                	}
	                } else {
	                	$company_name = '';
	                }

			        // LOG
			        file_put_contents(DIR_LOGS . "infakt/infakt.log", PHP_EOL . 'Type: 1', FILE_APPEND);

	                sleep(2);

	                $data_create = array(
	                    "invoice" => array(
	                        "currency" => ( !empty($order_info['infakt_currency']) ? $order_info['infakt_currency'] : 'PLN' ),
	                        "locale" => ( !empty($order_info['infakt_language']) ? $order_info['infakt_language'] : 'pl' ),
	                        "payment_method" => $payment_method,
	                        "bank_account" => $bank_account,
	                        "bank_name" => $bank_name,
							"sale_type" => "merchandise",
	                        "client_company_name" => ( !empty(utf8_encode(html_entity_decode($company_name))) ? html_entity_decode($company_name) :  $order_info['firstname'] . ' ' . $order_info['lastname'] ),
	                        "nip" => str_replace(" ", "", trim($order_info['infakt_nip'])),
	                        "services" => $products_demand,
	                    	"notes" => 'Numer zamówienia ' . $order_info['order_id']
	                	)
	                );

	                // print_r($data_create); exit;
	            } else if ( !empty($customer_info['company_nip']) && $customer_info['company_nip'] != 0 ) {
	            	$curlHandler = curl_init();
	                curl_setopt($curlHandler, CURLOPT_ENCODING, 'gzip');
	                curl_setopt($curlHandler, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	                curl_setopt($curlHandler, CURLOPT_SSL_VERIFYHOST, false);
	                curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);
	                curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
	                curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, true);
	                curl_setopt($curlHandler, CURLOPT_USERAGENT, 'Opera 10.00');
	                curl_setopt($curlHandler, CURLOPT_URL, 'https://api.infakt.pl/v3/clients.json?q[nip_eq]=' . str_replace(" ", "", trim($customer_info['company_nip'])));
	                curl_setopt($curlHandler, CURLOPT_HTTPHEADER, array(
	                	'Content-Type: application/json',
	                    'X-inFakt-ApiKey: d5c2a4613d6bb085e7e138ebf180a4ad4d50f331')
	                );
	                curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, "GET");
	                $result_client_arr = curl_exec($curlHandler);
	                curl_close($curlHandler);
	                $result_client = json_decode($result_client_arr, true);

			        // LOG
			        file_put_contents(DIR_LOGS . "infakt/infakt.log", PHP_EOL . 'Type: 2', FILE_APPEND);

	                sleep(3);

	                if (isset($result_client['entities'][0])) {
	                	$company_name = $result_client['entities'][0]['company_name'];

	                    $data_create = array(
	                        "invoice" => array(
	                            "currency" => ( !empty($order_info['infakt_currency']) ? $order_info['infakt_currency'] : 'PLN' ),
	                            "locale" => ( !empty($order_info['infakt_language']) ? $order_info['infakt_language'] : 'pl' ),
	                            "payment_method" => $payment_method,
	                            "bank_account" => $bank_account,
								"sale_type" => "merchandise",
	                            "bank_name" => $bank_name,
	                            "client_company_name" => ( !empty(utf8_encode(html_entity_decode($company_name))) ? html_entity_decode($company_name) :  $order_info['firstname'] . ' ' . $order_info['lastname'] ),
	                            "nip" => str_replace(" ", "", trim($customer_info['company_nip'])),
	                            "services" => $products_demand,
	                        	"notes" => 'Numer zamówienia ' . $order_info['order_id']
	                    	)
	                	);
	                } else {
	                    $data_create = array(
	                        "invoice" => array(
	                            "currency" => ( !empty($order_info['infakt_currency']) ? $order_info['infakt_currency'] : 'PLN' ),
	                            "locale" => ( !empty($order_info['infakt_language']) ? $order_info['infakt_language'] : 'pl' ),
	                            "payment_method" => $payment_method,
	                            "bank_account" => $bank_account,
								"sale_type" => "merchandise",
	                            "bank_name" => $bank_name,
	                            "client_company_name" => $order_info['firstname'] . ' ' . $order_info['lastname'],
	                            "services" => $products_demand,
	                        	"notes" => 'Numer zamówienia ' . $order_info['order_id']
	                    	)
	                	);
	            	}
	            } else {
	                $data_create = array(
	                    "invoice" => array(
	                        "currency" => ( !empty($order_info['infakt_currency']) ? $order_info['infakt_currency'] : 'PLN' ),
	                        "locale" => ( !empty($order_info['infakt_language']) ? $order_info['infakt_language'] : 'pl' ),
	                        "payment_method" => $payment_method,
	                        "bank_account" => $bank_account,
	                        "bank_name" => $bank_name,
							"sale_type" => "merchandise",
	                        "client_company_name" => $order_info['firstname'] . ' ' . $order_info['lastname'],
	                        "services" => $products_demand,
	                    	"notes" => 'Numer zamówienia ' . $order_info['order_id']
	                	)
	            	);
	            }
	        }

            if ($data_create['invoice']['bank_account'] == 'null') {
            	unset($data_create['invoice']['bank_account']);
            }
            if ($data_create['invoice']['bank_name'] == 'null') {
            	unset($data_create['invoice']['bank_name']);
            }

            $curl_headers = [
                'Content-Type: application/json',
            	'X-inFakt-ApiKey: d5c2a4613d6bb085e7e138ebf180a4ad4d50f331'
            ];

            curl_setopt($curl, CURLOPT_URL, 'https://api.infakt.pl/v3/async/invoices.json');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_ENCODING, '');
            curl_setopt($curl, CURLOPT_MAXREDIRS, 0);
            curl_setopt($curl, CURLOPT_TIMEOUT, 100);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data_create, JSON_UNESCAPED_UNICODE));
            curl_setopt($curl, CURLOPT_HTTPHEADER, $curl_headers);
            $response = curl_exec($curl);
	        // LOG
	        file_put_contents(DIR_LOGS . "infakt/infakt.log", PHP_EOL . 'RESULT REFERENCE: ' . print_r($response, true), FILE_APPEND);
            curl_close($curl);
            $invoice_number = json_decode($response, true)["invoice_task_reference_number"];

            sleep(4);

            /*$curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.infakt.pl/v3/async/invoices/status/'.$invoice_number.'.json',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
               	CURLOPT_HTTPHEADER => array(
                	'X-inFakt-ApiKey: d5c2a4613d6bb085e7e138ebf180a4ad4d50f331'
            	),
            ));
            $response_status = curl_exec($curl);
	        // LOG
	        file_put_contents(DIR_LOGS . "infakt/infakt.log", PHP_EOL . 'RESULT UUID: ' . print_r($response_status, true), FILE_APPEND);
            curl_close($curl);
            $response_status_arr = json_decode($response_status, true);*/
			$uidcreated = false;
			// if (!$uidcreated) {
				// sleep(3);
			while (!$uidcreated) {
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://api.infakt.pl/v3/async/invoices/status/'.$invoice_number.'.json',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
					   CURLOPT_HTTPHEADER => array(
						'X-inFakt-ApiKey: d5c2a4613d6bb085e7e138ebf180a4ad4d50f331'
					),
				));
				$response_status = curl_exec($curl);
				// LOG
				file_put_contents(DIR_LOGS . "infakt/infakt.log", PHP_EOL . 'RESULT UUID: ' . print_r($response_status, true), FILE_APPEND);
				curl_close($curl);
				$response_status_arr = json_decode($response_status, true);

				if ($response_status_arr["processing_code"] == 201) {
					$uidcreated = true;
				} else {
					$uidcreated = false;
				}
			}

			if (isset($response_status_arr["invoice_uuid"])) {
				$invoice_uuid = $response_status_arr["invoice_uuid"];
			} else {
				return false;
			}

            sleep(3);

            $curl = curl_init();
            $pdf_lang = ( !empty($order_info['infakt_language']) ? $order_info['infakt_language'] : 'en' );
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.infakt.pl/api/v3/invoices/' . $invoice_uuid . '/pdf.json',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS =>'{
                    "document_type": "original",
                	"locale": "' . $pdf_lang . '"
                }',
                CURLOPT_HTTPHEADER => array(
                    'X-inFakt-ApiKey: d5c2a4613d6bb085e7e138ebf180a4ad4d50f331',
                	'Content-Type: application/json'
            	),
            ));
            $response_pdf = curl_exec($curl);
	        // LOG
	        // file_put_contents(DIR_LOGS . "infakt/infakt.log", PHP_EOL . 'RESULT REFERENCE: ' . print_r($response_pdf, true), FILE_APPEND);
            curl_close($curl);
            // file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/pdfinfakt/' . $invoice_uuid . '.pdf', $response_pdf);
            $pdf_link = 'https://' . $_SERVER['SERVER_NAME'] . '/pdfinfakt/' . $invoice_uuid . '.pdf';

            sleep(3);

            $curl = curl_init();
            curl_setopt_array($curl, array(
            	CURLOPT_URL => 'https://api.infakt.pl/api/v3/invoices/' . $invoice_uuid . '.json',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                	'X-inFakt-ApiKey: d5c2a4613d6bb085e7e138ebf180a4ad4d50f331'
                ),
            ));
            $response_info = curl_exec($curl);
	        // LOG
	        // file_put_contents(DIR_LOGS . "infakt/infakt.log", PHP_EOL . 'RESULT REFERENCE: ' . print_r($response_info, true), FILE_APPEND);
            curl_close($curl);
            $infakt_client_id = json_decode($response_info, true)["client_id"];
            $invoice_numb = json_decode($response_info, true)["number"];

            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET infakt_no = '" . $invoice_uuid . "', infakt_pdf = '" . $pdf_link . "', infakt_number = '" . $invoice_numb . "' WHERE order_id = '" . (int)$order_id . "'");
                        
            $infakt_arr = array();
            $infakt_arr['infakt_no'] = $invoice_uuid;
            $infakt_arr['infakt_number'] = $invoice_numb;
            $infakt_arr['infakt_pdf'] = $pdf_link;

            if(!empty($order_info['infakt_nip']) || !empty($customer_info['company_nip'])) {}else {
            	if (!empty($infakt_client_id) && $infakt_client_id != 0 && $infakt_client_id != '') {
					$curl = curl_init();
					curl_setopt_array($curl, array(
					  	CURLOPT_URL => 'https://api.infakt.pl/api/v3/clients/' . $infakt_client_id . '.json',
					  	CURLOPT_RETURNTRANSFER => true,
					  	CURLOPT_ENCODING => '',
					  	CURLOPT_MAXREDIRS => 10,
					  	CURLOPT_TIMEOUT => 0,
					  	CURLOPT_FOLLOWLOCATION => true,
					  	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  	CURLOPT_CUSTOMREQUEST => 'DELETE',
		                CURLOPT_HTTPHEADER => array(
		                	'X-inFakt-ApiKey: d5c2a4613d6bb085e7e138ebf180a4ad4d50f331'
		                ),
					));
					$response = curl_exec($curl);
					curl_close($curl);
				}
            }
            
            $this->response->addHeader('Content-Type: application/json');
        	echo json_encode($infakt_arr);
		} else {
    	}
	}

	public function createInfaktPdf() {
		if (isset($this->request->get['order_id'])) {
			$order_id = (int)$this->request->get['order_id'];
		} else {
			return new Action('error/not_found');
		}

		$this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($order_id);

        $invoice_uuid = $order_info['infakt_no'];
                    
        $curl = curl_init();
        $pdf_lang = ( !empty($order_info['infakt_language']) ? $order_info['infakt_language'] : 'pl' );
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.infakt.pl/api/v3/invoices/' . $invoice_uuid . '/pdf.json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS =>'{
                "document_type": "original",
              	"locale": "' . $pdf_lang . '"
            }',
            CURLOPT_HTTPHEADER => array(
                'X-inFakt-ApiKey: d5c2a4613d6bb085e7e138ebf180a4ad4d50f331',
             	'Content-Type: application/json'
           	),
        ));
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // $response_pdf =
        // $filename = $invoice_uuid . '.pdf';
		// header("Content-Type: application/pdf; name=\"{$filename}\"");
		// header("Content-Transfer-Encoding: binary"); 
		header('Content-type: application/pdf');
        echo curl_exec($curl);
        curl_close($curl);
        // file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/pdfinfakt/' . $invoice_uuid . '.pdf', $response_pdf);
        // $pdf_link = 'https://' . $_SERVER['SERVER_NAME'] . '/pdfinfakt/' . $invoice_uuid . '.pdf';
    }

    public function createInfaktLink() {
        if (isset($this->request->get['infakt_uuid'])) {
            $infakt_uuid = $this->request->get['infakt_uuid'];

            $curl = curl_init();
            curl_setopt_array($curl, array(
	            CURLOPT_URL => 'https://api.infakt.pl/v3/invoices/'.$infakt_uuid.'/share_links.json',
    	        CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => array(
                	'X-inFakt-ApiKey: d5c2a4613d6bb085e7e138ebf180a4ad4d50f331'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
                                            
            if (isset(json_decode($response, true)['error'])) {
            	$curl = curl_init();
                curl_setopt_array($curl, array(
                	CURLOPT_URL => 'https://api.infakt.pl/v3/invoices/'.$infakt_uuid.'/share_links.json',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                    	'X-inFakt-ApiKey: d5c2a4613d6bb085e7e138ebf180a4ad4d50f331'
                    ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);                        
            }
                        
            $this->response->addHeader('Content-Type: application/json');
            echo $response;
        } else {
        	$this->response->addHeader('Content-Type: application/json');
            $json = array('error' => 'error');
            echo json_decode(json);                    
        }
    }

    public function removeInfakt() {
        if (isset($this->request->get['order_id']) && isset($this->request->get['infakt_uuid'])) {
                        
            $order_id = $this->request->get['order_id'];
            $infakt_uuid = $this->request->get['infakt_uuid'];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.infakt.pl/v3/invoices/' . $infakt_uuid . '.json',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'DELETE',
                CURLOPT_HTTPHEADER => array(
                    'X-inFakt-ApiKey: d5c2a4613d6bb085e7e138ebf180a4ad4d50f331'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);

            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET infakt_no = '', infakt_pdf = '', infakt_number = '' WHERE order_id = '" . (int)$order_id . "'");
                                            
            $this->response->addHeader('Content-Type: application/json');
            $json = array('success' => 'success');
            echo json_encode($json);
        } else {
            $this->response->addHeader('Content-Type: application/json');
            $json = array('error' => 'error');
            echo json_encode($json);
        }
    }

    // MS
public function getProduct($id = '') {
    $this->load->model('setting/setting');

    $client = new httpMoyskladClient();

    $login = $this->config->get('module_my_sklad_login');
    $password = $this->config->get('module_my_sklad_password');
    $url = $this->config->get('module_my_sklad_url');

    $answer = $client->makeRequest(httpMoyskladClient::METHOD_GET, $login, $password, $url, '/entity/product/'.$id);
    
    return $answer;
  }

  public function getSklads($id = '') {
    $this->load->model('setting/setting');

    $client = new httpMoyskladClient();

    $login = $this->config->get('module_my_sklad_login');
    $password = $this->config->get('module_my_sklad_password');
    $url = $this->config->get('module_my_sklad_url');

    $answer = $client->makeRequest(httpMoyskladClient::METHOD_GET, $login, $password, $url, '/entity/store/'.$id);

    return $answer;
  }

  public function getOrganization() {
    $this->load->model('setting/setting');

    $client = new httpMoyskladClient();

    $login = $this->config->get('module_my_sklad_login');
    $password = $this->config->get('module_my_sklad_password');
    $url = $this->config->get('module_my_sklad_url');

    $answer = $client->makeRequest(httpMoyskladClient::METHOD_GET, $login, $password, $url, '/entity/organization/');
    
    return $answer;
  }

  public function getCustomEntities() {
    $this->load->model('setting/setting');

    $client = new httpMoyskladClient();

    $login = $this->config->get('module_my_sklad_login');
    $password = $this->config->get('module_my_sklad_password');
    $url = $this->config->get('module_my_sklad_url');

    $answer = $client->makeRequest(httpMoyskladClient::METHOD_GET, $login, $password, $url, '/context/companysettings/metadata');
  
    return $answer;
  }

  public function getDemandAttributesById($id = '') {
    $this->load->model('setting/setting');

    $client = new httpMoyskladClient();

    $login = $this->config->get('module_my_sklad_login');
    $password = $this->config->get('module_my_sklad_password');
    $url = $this->config->get('module_my_sklad_url');

    $answer = $client->makeRequest(httpMoyskladClient::METHOD_GET, $login, $password, $url, '/entity/demand/metadata/attributes/' . $id);

    return $answer;    
  }

  public function getAgents($id = '') {
    $this->load->model('setting/setting');

    $client = new httpMoyskladClient();

    $login = $this->config->get('module_my_sklad_login');
    $password = $this->config->get('module_my_sklad_password');
    $url = $this->config->get('module_my_sklad_url');
  
    $answer = $client->makeRequest(httpMoyskladClient::METHOD_GET, $login, $password, $url, '/entity/counterparty/' . $id);
  
    return $answer;
  }
  public function orderMySklad() {
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    set_time_limit (60);
    ini_set('memory_limit', '5512M');
    ignore_user_abort(true);

    if (isset($this->request->post['order_id'])) {
      $order_id = $this->request->post['order_id'];
      
      if (isset($this->request->post['manager'])) {
        $manager = $this->request->post['manager'];
      } else {
        $manager = '';
      }
      $date_demand = date("Y-m-d H:i:s");
      $this->db->query("UPDATE " . DB_PREFIX . "order SET order_my_sklad_btc = '" . $date_demand . ' ' . $manager . "' WHERE order_id = '" . (int)$order_id . "'");
    } else {
      $order_id = 0;
      exit;
    }

    $this->load->language('extension/module/my_sklad');

    $this->load->model('checkout/order');
    $this->load->model('catalog/product');

    $this->load->model('extension/module/pixsel_parser');
    $pixsel_box_price = $this->model_extension_module_pixsel_parser->getPixselBoxPrices();

    $order_info = $this->model_checkout_order->getOrder($order_id);
    $name = '';
    $products = $this->model_checkout_order->getOrderProducts($order_id);

    $products_sklad = array();

    $overheads = 0;

    foreach ($products as $product) {
      $option_data = array();
 
      $option_data = $this->model_checkout_order->getOrderOptions($order_id, $product['order_product_id']);
                
      // $code_error = '';
      $option_data_out = array();
      $opts =  $this->model_catalog_product->getProductOptions($product['product_id']);
      foreach ($option_data as $option) {
        foreach ($opts as $key => $value) {
          foreach ($value['product_option_value'] as $key2 => $value2) {
            if($option['product_option_value_id'] == $value2['product_option_value_id']) {
              $code = $value2['pixsel_sku'];

              $pixsel_price_material = $value2['pixsel_price_material'];
              $pixsel_box = $this->getPixselBox($order_id, $product['product_id'], $value2['product_option_value_id']);
              // if (isset($pixsel_box['quantity']) && $pixsel_box['quantity'] > 0) {
                $pixsel_box_cost = $pixsel_box_price[$pixsel_box['name']]*$this->config->get('module_pixsel_price_rate');
              // } else {
              //  $pixsel_box_cost = 0;
              // }
            }
          }
        }
        $value = $option['value'];

        $option_data_out[] = array(
          'name'  => $option['name'],
          'code'  => $code,
          'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value) . ' (' . $code . ')',
        );

        $queryzpo = $this->model_catalog_product->getProduct($product['product_id']);
        if($code != '') {
          $products_sklad[] = array(
            'product_id' => $product['product_id'],
            'name'       => $product['name'] . ' - ' . $code,
            'model'      => $product['model'],
            'sku'        => $queryzpo['sku'],
            'option'     => $option_data_out,
            'quantity'   => $product['quantity'],
            // 'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax'], $this->config->get('config_tax')), $this->session->data['currency']),
            // 'total'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax'], $this->config->get('config_tax')) * $product['quantity'], $this->session->data['currency']),
            'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax'], $this->config->get('config_tax')), $order_info['currency_code']),
            'total'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax'], $this->config->get('config_tax')) * $product['quantity'], $order_info['currency_code']),
            'code'       => $code,
          );

          $overheads += (($pixsel_price_material*$product['quantity'])+$pixsel_box_cost);
        }        
      }
    }

    $order_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
    $coupon = '';
    foreach ($order_query->rows as $key => $value) {
      if($value['code'] == 'coupon') {
        $coupon = $value['title'] . ' - ' . $this->language->get('lang_module_entry_discount') . ' ' . abs(round($value['value'])) . ' ' . $this->currency->getSymbolRight($order_info['currency_code']);
      }
    }
    
    //if (isset($this->session->data['user_id'])) {
    //  $order_user = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . (int)$this->session->data['user_id'] . "'");
    //} else if (isset($this->request->post['cuser_id'])) {
    //  $order_user = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . $this->request->post['cuser_id'] . "'");
    //}
    $order_user = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . $this->customer->getId() . "'");

    $username = $order_user->row['firstname'] . ' ' . $order_user->row['lastname'];
    
    $info = ' 
' . strip_tags($order_info['shipping_method']) . ' 
' . $order_info['shipping_city'] . ', ' . $order_info['shipping_address_1'] . ' 

' . $order_info['payment_method'] . '
' . $this->currency->format($order_info['total'], $this->session->data['currency']) . '

' . $order_info['firstname'] . ' ' . $order_info['lastname'] . ' 
' . $order_info['telephone'];

    $info_array = array(
      'name'           => $order_info['firstname'] . ' ' . $order_info['lastname'],
      'phone'          => $order_info['telephone'],
      'email'          => $order_info['email'],
      'infakt_vat'     => $order_info['infakt_vat'],
      'country_id'     => $order_info['country_id'],
      'comment'        => $order_info['comment'],
	  'payed' 	       => $order_info['payed'],
      'adress_comment' => $order_info['payment_address_1'],
      'agent_code'     => ((!isset($this->request->post['agent_code']) || empty($this->request->post['agent_code'])) ? $this->config->get('module_my_sklad_customer_code') : $this->request->post['agent_code']),
      'overheads'      => ($overheads*100),
      'infakt_number'  => $order_info['infakt_number']
    );

    $file = $_SERVER['DOCUMENT_ROOT'] . '/pdfstickers/order_' . $order_id . '.pdf';
    if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/pdfstickers')) {
      mkdir($_SERVER['DOCUMENT_ROOT'] . '/pdfstickers', 0777, true);
    }    
    if(!is_file($file)) {
      $this->load->controller('pdfexcelimport/pdfexcelimport/small_pdf', $order_id);
    }
    $pdflink = 'https://' . $_SERVER['SERVER_NAME'] . '/pdfstickers/order_' . $order_id . '.pdf';

    $infaktlink = $order_info['infakt_pdf'];

    $result = $this->demandMySklad($products_sklad, $order_id, $name, $coupon, $username, $info, $info_array, $pdflink, $infaktlink);
    $result_arr = json_decode(json_encode($result), true);
    if (!isset($result_arr['error'])) {
      $this->updateCodeOrder($order_id, $result_arr['meta']['uuidHref'], $result_arr['name']);
    }

    file_put_contents(DIR_LOGS . 'ms_logs/0_orderMySklad_' . $order_id . '.txt', print_r($result, true) . '' . PHP_EOL, FILE_APPEND);

    echo json_encode($result);
  }

  private function demandMySklad($products, $order_id, $name, $coupon, $username = '', $info = '', $info_array = array(), $pdflink = '', $infaktlink = '') {
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    set_time_limit (60);
    ini_set('memory_limit', '5512M');

	$this->load->model('checkout/order');
    $order_info = $this->model_checkout_order->getOrder($order_id);

    $this->load->language('extension/module/my_sklad');

    $login = $this->config->get('module_my_sklad_login');
    $password = $this->config->get('module_my_sklad_password');
    $url = $this->config->get('module_my_sklad_url');    

    $mc_products = array();
    for ($i=0; $i < 6000; $i = $i + 1000) { 
      $params = array('limit' => 1000, 'offset' => $i);

      $client = new httpMoyskladClient();
      $mc_products_data = $client->makeRequest(httpMoyskladClient::METHOD_GET, $login, $password, $url, '/entity/product', $params);

      foreach ($mc_products_data->rows as $key => $value) {
        $uuid = $value->meta->uuidHref;
        $ars = explode('id=', $uuid);
        $mc_products[$ars[1]] = $value->externalCode;
      }
    }

    $creator = $this->getDemandAttributesById($this->config->get('module_my_sklad_creator_web'));
    $sticker = $this->getDemandAttributesById($this->config->get('module_my_sklad_sticker'));
    $direction_list = $this->config->get('module_my_sklad_direction_list_code');
    $direction_arr = json_decode(json_encode($this->getCustomEntities()), true);
    $direction_list_id = '';
    $direction_list_name = '';
    for ($cdr = 0; $cdr < count($direction_arr['customEntities']); $cdr++) {
      if ($direction_arr['customEntities'][$cdr]['entityMeta']['href'] == $direction_list) {
        $direction_list_id = str_replace('https://api.moysklad.ru/api/remap/1.2/entity/customentity/', '', $direction_arr['customEntities'][$cdr]['entityMeta']['href']);
        $direction_list_name = $direction_arr['customEntities'][$cdr]['name'];
      }
    }    
    $direction = $this->config->get('module_my_sklad_direction_code');
    $organization = $this->getOrganization();
    $agent = array();
    $agent_code = $this->config->get('module_my_sklad_customer_code');
    $store = $this->getSklads($this->config->get('module_my_sklad_warehouse_code'));
	
	if ($order_info['currency_code'] == 'EUR') {
		$currency = '547f65f5-7988-11ef-0a80-0ccf00205707';
	} else {
		$currency = $this->config->get('module_my_sklad_currency_code');
	}
    
	$status = $this->config->get('module_my_sklad_status_demand');

	if ($info_array['payed'] == 1) {
		$status = '459ab07f-168e-11ee-0a80-13ab001500e6';
	}

    if($info_array['agent_code'] != '') {
      $agents = array();

      $client = new httpMoyskladClient();
      $agents_data = $client->makeRequest(httpMoyskladClient::METHOD_GET, $login, $password, $url, '/entity/counterparty?filter=externalCode=' . $info_array['agent_code']);

      if (!empty($agents_data->rows)) {
        foreach ($agents_data->rows as $key => $value) {
          $uuid = $value->meta->uuidHref;
          $ars = explode('id=', $uuid);
          $agents[$ars[1]] = $value->externalCode;
        }      
      
        if($keyfound = array_search($info_array['agent_code'], $agents)) {
          $agent_new = $this->getAgents($keyfound);
          if(!isset($agent_new->errors)) {
            $agent = $agent_new;
          }
        }
      } else {
        $client = new httpMoyskladClient();
        $agents_data = $client->makeRequest(httpMoyskladClient::METHOD_GET, $login, $password, $url, '/entity/counterparty?filter=externalCode=' . $agent_code);

        foreach ($agents_data->rows as $key => $value) {
          $uuid = $value->meta->uuidHref;
          $ars = explode('id=', $uuid);
          $agents[$ars[1]] = $value->externalCode;
        }      
      
        if($keyfound = array_search($agent_code, $agents)) {
          $agent_new = $this->getAgents($keyfound);
          if(!isset($agent_new->errors)) {
            $agent = $agent_new;
          }
        }
      }
    } else {
        $client = new httpMoyskladClient();
        $agents_data = $client->makeRequest(httpMoyskladClient::METHOD_GET, $login, $password, $url, '/entity/counterparty?filter=externalCode=' . $agent_code);

        foreach ($agents_data->rows as $key => $value) {
          $uuid = $value->meta->uuidHref;
          $ars = explode('id=', $uuid);
          $agents[$ars[1]] = $value->externalCode;
        }      
      
        if($keyfound = array_search($agent_code, $agents)) {
          $agent_new = $this->getAgents($keyfound);
          if(!isset($agent_new->errors)) {
            $agent = $agent_new;
          }
        }
    }
	//echo $agent->meta->href;
	// echo $agent->meta->metadataHref;
    // print_r($agent);
    //exit;

    $positions = array();
    $products_names = '';
    foreach ($products as $key => $value) {
      if($value['code'] != '' && $keyfound = array_search($value['code'], $mc_products)) { 
        $product = $this->getProduct($keyfound);
      }
      $positions_current['quantity'] = (float)$value['quantity']; 
      
      if ($info_array['infakt_vat'] == 1) {
        $positions_current['price'] = $this->tax->calc_tax((float)$value['price']) * 100; 
        $positions_current['vat'] = 23;

        $vatEnabled = true;
        $vatIncluded = false;

        $bezvat = false;
      } else {
        $positions_current['price'] = $this->tax->calc_tax((float)$value['price']) * 100; 

        $vatEnabled = false;
        $vatIncluded = false;

        $bezvat = true;
      }

      $positions_current['assortment']['meta'] = $product->meta;
      $positions[] = $positions_current;

      $products_names .= $value['quantity'] . ' x ' . $value['sku'] . ' - ' . $value['name'] . ' ' . $value['option'][0]['value'] . '
'; 

      $date_ch  = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date("m"), date("d"), date("Y")));
    }

	// print_r($positions_current); exit;
    
    if (!empty($info_array['infakt_number'])) { 
      $description_g = $this->language->get('lang_module_entry_request'). '' . $order_id . ' ' . $coupon . ' 
Faktura ' . $info_array['infakt_number'] . ' 
' . $info . ' 

' . strip_tags($info_array['comment']);
} else {
      $description_g = $this->language->get('lang_module_entry_request'). '' . $order_id . ' ' . $coupon . '  
' . $info . ' 

' . strip_tags($info_array['comment']);
}

if ($info_array['country_id'] == 170 && $info_array['infakt_vat'] == 0) {
	$info_array['infakt_number'] = 'Без фактуры';
}

    $params = array(
      'organization' => array(
        'meta' => array(
          'href' => $organization->rows[0]->meta->href,
          'metadataHref' => $organization->rows[0]->meta->metadataHref,
          'type' => "organization",
          'mediaType' => "application/json"
        )
      ),
      'agent' => array(
        'meta' => array(
          'href' => $agent->meta->href,
          'metadataHref' => $agent->meta->metadataHref,
          'type' => "counterparty",
          'mediaType' => "application/json"
        )
      ),
      'store' => array(
        'meta' => array(
          'href' => $store->meta->href,
          'type' => "store",
          'mediaType' => "application/json"
        )
      ),
      'state' => array(
        'meta' => array(
          'href' => 'https://api.moysklad.ru/api/remap/1.2/entity/demand/metadata/states/' . $status,
          'type' => "state",
          'mediaType' => "application/json"
        )
      ),
      'rate' => array(
        'currency' => array(
          'meta' => array(
            'href' => 'https://api.moysklad.ru/api/remap/1.2/entity/currency/' . $currency,
            'metadataHref' => 'https://api.moysklad.ru/api/remap/1.2/entity/currency/metadata',
            'type' => "currency",
            'mediaType' => "application/json"
          )
        )
      ),
      'attributes' => array(
        array(
          'meta' => array(
            'href' => $creator->meta->href,
            'type' => "attributemetadata",
            'mediaType' => "application/json"
          ),
          'id' => $creator->id,
          "name" => $creator->name,
          "type" => "text",
          "required" => "false",
          "value" => $username,
        ),
        array(
          'meta' => array(
            'href' => $this->config->get('module_my_sklad_direction_list_code'),
            'type' => "attributemetadata",
            'mediaType' => "application/json"
          ),
          'id' => $direction_list_id,
          "name" => $direction_list_name,
          "type" => "customentity",
          "required" => "true",
          "value" => array(
            'meta' => array(
             'href' => $direction,
             'type' => "customentity",
             'mediaType' => "application/json"
            )
          ),
        ),
        /*array(
          'meta' => array(
            'href' => $sticker->meta->href,
            'type' => "attributemetadata",
            'mediaType' => "application/json"
          ),
          'id' => $sticker->id,
          "name" => $sticker->name,
          "type" => "link",
          "required" => "false",
          "value" => $pdflink,
        ),*/
        array(
          'meta' => array(
            'href' => 'https://api.moysklad.ru/api/remap/1.2/entity/demand/metadata/attributes/fe8c72a2-17a7-11ef-0a80-03e2000176fa',
            'type' => "attributemetadata",
            'mediaType' => "application/json"
          ),
          'id' => 'fe8c72a2-17a7-11ef-0a80-03e2000176fa',
          "name" => 'Infakt',
          "type" => "text",
          "required" => "false",
          "value" => $info_array['infakt_number'],
        ),
        array(
          'meta' => array(
            'href' => 'https://api.moysklad.ru/api/remap/1.2/entity/demand/metadata/attributes/8d3506f2-17a8-11ef-0a80-00df0001b4a3',
            'type' => "attributemetadata",
            'mediaType' => "application/json"
          ),
          'id' => '8d3506f2-17a8-11ef-0a80-00df0001b4a3',
          "name" => 'Faktura PDF',
          "type" => "link",
          "required" => "false",
          "value" => $infaktlink,
        ),
        array(
          'meta' => array(
            'href' => 'https://api.moysklad.ru/api/remap/1.2/entity/demand/metadata/attributes/1acab28d-2a31-11ef-0a80-170d000c0e56',
            'type' => "attributemetadata",
            'mediaType' => "application/json"
          ),
          'id' => '1acab28d-2a31-11ef-0a80-170d000c0e56',
          "name" => 'Bez VAT',
          "type" => "boolean",
          "required" => "false",
          "value" => $bezvat,
        ),
        array(
			'meta' => array(
			  'href' => 'https://api.moysklad.ru/api/remap/1.2/entity/demand/metadata/attributes/02094919-7b7e-11ef-0a80-1071001e2900',
			  'type' => "attributemetadata",
			  'mediaType' => "application/json"
			),
			'id' => '02094919-7b7e-11ef-0a80-1071001e2900',
			"name" => 'Source',
			"type" => "text",
			"required" => "false",
			"value" => "pixsel.pl",
		),
      ),
      'overhead' => array(
        'sum' => $info_array['overheads'],
        'distribution' => "weight"
      ),
      'description' => $description_g, 
      'applicable' => false,
      'positions' => $positions,
      "vatEnabled" => $vatEnabled,
      "vatIncluded" => $vatIncluded,
    );

    $client = new httpMoyskladClient();
    $add_data = $client->makeRequest(httpMoyskladClient::METHOD_POST, $login, $password, $url, '/entity/demand', $params);

    file_put_contents(DIR_LOGS . 'ms_logs/0_demandMySklad_' . $order_id . '.txt', print_r($params, true) . '' . PHP_EOL, FILE_APPEND);

    return $add_data;
  }

  public function demandCronUpdateMySklad() {
	// $this->load->language('extension/module/my_sklad');

	// $orders_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE (date_added BETWEEN '" . date('Y-m-d 00:00:01', strtotime($date_start)) . "' AND '" . date('Y-m-d 23:59:59', strtotime($date_end)) . "') AND (`infakt_number`<>'') AND `infakt_number` IS NOT NULL");
	$orders_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE date_added >= NOW() - INTERVAL '1' DAY AND (`infakt_number`<>'') AND `infakt_number` IS NOT NULL");
	$orders = $orders_query->rows;
	for ($o = 0; $o < count($orders); $o++) {
		// echo $orders[$o]['order_id']."<br>";
		$this->demandUpdateMySklad($orders[$o]['order_id'],'Cron');
	}
  }

  public function demandUpdateMySklad($oid = null, $mn = null) {
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    set_time_limit (60);
    ini_set('memory_limit', '5512M');

    $login = $this->config->get('module_my_sklad_login');
    $password = $this->config->get('module_my_sklad_password');
    $url = $this->config->get('module_my_sklad_url');  

	if ($mn == 'Cron') {
		$language_code = $this->config->get('config_language');
    }

    $this->load->language('extension/module/my_sklad');

    $this->load->model('checkout/order');
    $this->load->model('catalog/product');

    $this->load->model('extension/module/pixsel_parser');
    $pixsel_box_price = $this->model_extension_module_pixsel_parser->getPixselBoxPrices();

	if (isset($this->request->post['order_id']) && isset($this->request->post['manager'])) {
    	$order_id = $this->request->post['order_id'];
    	$manager = $this->request->post['manager'];
	} else if ($oid && $mn) {
    	$order_id = $oid;
    	$manager = $mn;
	} else {
		exit;
	}

    $order_info = $this->model_checkout_order->getOrder($order_id);
    $name = '';
    $products = $this->model_checkout_order->getOrderProducts($order_id);

    $products_sklad = array();

    $overheads = 0;

    foreach ($products as $product) {
      $option_data = array();
 
      $option_data = $this->model_checkout_order->getOrderOptions($order_id, $product['order_product_id']);

      $option_data_out = array();
      $opts =  $this->model_catalog_product->getProductOptions($product['product_id']);
      foreach ($option_data as $option) {
        foreach ($opts as $key => $value) {
          foreach ($value['product_option_value'] as $key2 => $value2) {
            if($option['product_option_value_id'] == $value2['product_option_value_id']) {
              $pixsel_price_material = $value2['pixsel_price_material'];
              $pixsel_box = $this->getPixselBox($order_id, $product['product_id'], $value2['product_option_value_id']);
              // if ($pixsel_box['quantity'] > 0) {
                $pixsel_box_cost = ($pixsel_box_price[$pixsel_box['name']]*$this->config->get('module_pixsel_price_rate'))*$pixsel_box['quantity'];
              // } else {
              //  $pixsel_box_cost = 0;
              // }
            }
          }
        }

        $overheads += (($pixsel_price_material*$product['quantity'])+$pixsel_box_cost);
      }        
    }

    if ($order_info['infakt_vat'] == 1) {
        $bezvat = false;
    } else {
 	   $bezvat = true;
    }


	// UPDATE COMMENT

    $order_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
    $coupon = '';
    foreach ($order_query->rows as $key => $value) {
      if($value['code'] == 'coupon') {
        $coupon = $value['title'] . ' - ' . $this->language->get('lang_module_entry_discount') . ' ' . abs(round($value['value'])) . ' ' . $this->currency->getSymbolRight($order_info['currency_code']);
      }
    }
        
    $info = ' 
' . strip_tags($order_info['shipping_method']) . ' 
' . $order_info['shipping_city'] . ', ' . $order_info['shipping_address_1'] . ' 

' . $order_info['payment_method'] . '
' . $this->currency->format($order_info['total'], $order_info['currency_code']) . '

' . $order_info['firstname'] . ' ' . $order_info['lastname'] . ' 
' . $order_info['telephone'];

      $description_g = $this->language->get('lang_module_entry_request'). '' . $order_id . ' ' . $coupon . ' 
Faktura ' . $order_info['infakt_number'] . ' 
' . $info . ' 

' . strip_tags($order_info['comment']);

// UPDATE COMMENT

    $orderMsIDArr = explode("id=", $order_info['order_my_sklad']);

    if (isset($orderMsIDArr[1])) {
      $orderMsID = $orderMsIDArr[1];

      $params = array(
	      'attributes' => array(
	        array(
	          'meta' => array(
	            'href' => 'https://api.moysklad.ru/api/remap/1.2/entity/demand/metadata/attributes/fe8c72a2-17a7-11ef-0a80-03e2000176fa',
	            'type' => "attributemetadata",
	            'mediaType' => "application/json"
	          ),
	          'id' => 'fe8c72a2-17a7-11ef-0a80-03e2000176fa',
	          "name" => 'Infakt',
	          "type" => "text",
	          "required" => "false",
	          "value" => $order_info['infakt_number'],
	        ),
	        array(
	          'meta' => array(
	            'href' => 'https://api.moysklad.ru/api/remap/1.2/entity/demand/metadata/attributes/8d3506f2-17a8-11ef-0a80-00df0001b4a3',
	            'type' => "attributemetadata",
	            'mediaType' => "application/json"
	          ),
	          'id' => '8d3506f2-17a8-11ef-0a80-00df0001b4a3',
	          "name" => 'Faktura PDF',
	          "type" => "link",
	          "required" => "false",
	          "value" => $order_info['infakt_pdf'],
	        ),
	        array(
	          'meta' => array(
	            'href' => 'https://api.moysklad.ru/api/remap/1.2/entity/demand/metadata/attributes/1acab28d-2a31-11ef-0a80-170d000c0e56',
	            'type' => "attributemetadata",
	            'mediaType' => "application/json"
	          ),
	          'id' => '1acab28d-2a31-11ef-0a80-170d000c0e56',
	          "name" => 'Bez VAT',
	          "type" => "boolean",
	          "required" => "false",
	          "value" => $bezvat,
	        ),
	      ),
        'overhead' => array(
          'sum' => $overheads*100,
          'distribution' => "weight"
        ),
        'description' => $description_g,
      );

      $client = new httpMoyskladClient();
      $result = $client->makeRequest(httpMoyskladClient::METHOD_PUT, $login, $password, $url, '/entity/demand/' . $orderMsID, $params);

      $result_arr = json_decode(json_encode($result), true);
      if (!isset($result_arr['error'])) {
        $date_box = date("Y-m-d H:i:s");
        // $this->db->query("UPDATE " . DB_PREFIX . "order SET order_my_sklad_box = '" . $date_box . ' ' . $manager . "' WHERE order_id = '" . (int)$order_id . "'");
      }

      echo json_encode($result);
    } else {
      $result = array('error' => 'error');
      echo json_encode($result);
    }

    file_put_contents(DIR_LOGS . 'ms_logs/0_demandUpdateMySklad_' . $order_id . '.txt', print_r($result, true) . '' . PHP_EOL, FILE_APPEND);
  }

  private function updateCodeOrder($order_id, $code, $name) {
    file_put_contents(DIR_LOGS . 'ms_logs/0_updateCodeOrder_' . $order_id . '.txt', $code . '' . PHP_EOL, FILE_APPEND);
    if (!empty($order_id) && $order_id != 0 && !empty($code)) {
      $this->db->query("UPDATE " . DB_PREFIX . "order SET `order_my_sklad` = '" . $code . "', `order_my_sklad_no` = '" . $name . "' WHERE order_id = '" . (int)$order_id . "'");
    }
  }


    public function removeMs() {
	    $login = $this->config->get('module_my_sklad_login');
	    $password = $this->config->get('module_my_sklad_password');
	    $url = $this->config->get('module_my_sklad_url');

	    $order_id = $this->request->post['order_id'];

	    $url_source = $this->request->post['url'];
	    $url_arr = explode('id=', $url_source);
	    $id = $url_arr[1];

	    $client = new httpMoyskladClient();
	    $result_delete = $client->makeRequest(httpMoyskladClient::METHOD_DELETE, $login, $password, $url, '/entity/demand/' . $id);
	    $result_delete_arr = json_decode(json_encode($result_delete), true);
	    
	    if (!isset($result_delete_arr['error'])) {
	      file_put_contents(DIR_LOGS . 'ms_logs/2_removeOrderMySklad.txt', print_r($result_delete_arr, true) . '' . PHP_EOL, FILE_APPEND);

	      $this->db->query("UPDATE " . DB_PREFIX . "order SET `order_my_sklad` = '', `order_my_sklad_no` = '',  `order_my_sklad_btc` = '',  `order_my_sklad_box` = '',  `order_my_sklad_ttn` = '',  `order_my_sklad_ttn_date` = '' WHERE `order_id`='" . $order_id . "'");
	      echo json_encode(array('success' => 'sucess'));
	    } else {
	      print_r($result_delete);
	    }
    }

  public function getProducts($data = array()) {
    $language_upload = (!empty($this->config->get('module_my_sklad_language')) ? $this->config->get('module_my_sklad_language') : (int)$this->config->get('config_language_id'));
    
    $sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . $language_upload . "'";

    if (isset($data['filter_category']) && !is_null($data['filter_category'])) {
      preg_match('/(.*)(WHERE pd\.language_id.*)/', $sql, $sql_crutch_matches);
    if (isset($sql_crutch_matches[2])) {
    $sql = $sql_crutch_matches[1] . " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)" . $sql_crutch_matches[2];
    } else {
      $data['filter_category'] = null;
      }
    }
    
    if (!empty($data['filter_name'])) {
      $sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";

      $model_arr = explode('-', trim(preg_replace('/\s+/', ' ',$data['filter_name'])));
      if (isset($model_arr[1])) {
        $model_sea = $model_arr[0].'-'.$model_arr[1];
        $sql .= " OR LCASE(p.model) LIKE '%" . $this->db->escape(utf8_strtolower($model_sea)) . "%'";
        $sql .= " OR LCASE(p.sku) LIKE '%" . $this->db->escape(utf8_strtolower($model_sea)) . "%'";
      } else {
        $sql .= " OR LCASE(p.model) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
        $sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
      }
    }

    if (!empty($data['filter_model'])) {
      $sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
    }
    
    if (isset($data['filter_category']) && !is_null($data['filter_category'])) {
      if (!empty($data['filter_category']) && !empty($data['filter_sub_category'])) {
        $implode_data = array();
        
        $this->load->model('catalog/category');
        
        $categories = $this->model_catalog_category->getCategoriesChildren($data['filter_category']);
        
        foreach ($categories as $category) {
          $implode_data[] = "p2c.category_id = '" . (int)$category['category_id'] . "'";
        }
        
        $sql .= " AND (" . implode(' OR ', $implode_data) . ")";
      } else {
        if ((int)$data['filter_category'] > 0) {
          $sql .= " AND p2c.category_id = '" . (int)$data['filter_category'] . "'";
        } else {
          $sql .= " AND p2c.category_id IS NULL";
        }
      }
    }
  
    if (isset($data['filter_manufacturer_id']) && !is_null($data['filter_manufacturer_id'])) {
      $sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
    }

    if (!empty($data['filter_price'])) {
      $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
    }
    
    if (isset($data['filter_price_min']) && !is_null($data['filter_price_min'])) {
      $sql .= " AND p.price >= '" . (float)$data['filter_price_min'] . "'";
    }
    
    if (isset($data['filter_price_max']) && !is_null($data['filter_price_max'])) {
      $sql .= " AND p.price <= '" . (float)$data['filter_price_max'] . "'";
    }

    if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '') {
      $sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
    }
    
    if (isset($data['filter_quantity_min']) && !is_null($data['filter_quantity_min'])) {
      $sql .= " AND p.quantity >= '" . (int)$data['filter_quantity_min'] . "'";
    }
    
    if (isset($data['filter_quantity_max']) && !is_null($data['filter_quantity_max'])) {
      $sql .= " AND p.quantity <= '" . (int)$data['filter_quantity_max'] . "'";
    }

    if (isset($data['filter_status']) && $data['filter_status'] !== '') {
      $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
    }
    
    if (isset($data['filter_noindex']) && $data['filter_noindex'] !== '') {
      $sql .= " AND p.noindex = '" . (int)$data['filter_noindex'] . "'";
    }

    $sql .= " GROUP BY p.product_id";

    $sort_data = array(
      'pd.name',
      'p.model',
      'p.price',
      'p.quantity',
      'p.status',
      'p.noindex',
      'p.sort_order'
    );

    if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
      $sql .= " ORDER BY " . $data['sort'];
    } else {
      $sql .= " ORDER BY pd.name";
    }

    if (isset($data['order']) && ($data['order'] == 'DESC')) {
      $sql .= " DESC";
    } else {
      $sql .= " ASC";
    }

    if (isset($data['start']) || isset($data['limit'])) {
      if ($data['start'] < 0) {
        $data['start'] = 0;
      }

      if ($data['limit'] < 1) {
        $data['limit'] = 20;
      }

      $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
    }

    $query = $this->db->query($sql);

    return $query->rows;
  }

  private function getPixselBox($order_id, $product_id, $product_option_value_id) {
    
    $boxQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_box WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_id . "' AND product_option_value_id ='" . (int)$product_option_value_id . "'");
    if($boxQuery->num_rows) {
      return [
        'order_box_id' => $boxQuery->row['order_box_id'],
        'pixsel_box_id' => $boxQuery->row['pixsel_box_id'],
        'product_option_value_id' => $boxQuery->row['product_option_value_id'],
        'name' => $boxQuery->row['name'],
        'quantity' => $boxQuery->row['quantity'],
      ];
    }
    return false;
  }


    public function orderStatusChange() {
        if (isset($this->request->post['order_id']) && isset($this->request->post['status_id'])) {
                        
	        $order_id = $this->request->post['order_id'];
	        $status_id = $this->request->post['status_id'];
                        
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . $status_id . "' WHERE order_id = '" . (int)$order_id . "'");

            // Update order status on Autofolie
            // $clientAutofolie = new httpAutofolieClient();
            $onewinfo = $this->db->query("SELECT * FROM oc_order WHERE order_id = '" . $order_id . "'")->row;
            // if (!empty($onewinfo['autofolie']) && $onewinfo['autofolie'] > 0) {
		    //    $clientAutofolie->orderApi('addOrderHistoryApi', '&autofolie_order_id=' . $onewinfo['autofolie'] . '&order_status_id=' . (int)$status_id . '&payed=' . $onewinfo['payed'] . '&payed_with=' . $onewinfo['payed_with'], array());
            //}
			/*if ($status_id == 16) {
				$autofolie = $onewinfo['autofolie'];
				$dataAf = array(
					'order_id' => $autofolie,
				);
				
				if (!empty($onewinfo['autofolie']) && $onewinfo['autofolie'] > 0) {
					$clientAutofolie = new httpAutofolieClient();
					$clientAutofolie->orderApi('removeApi', '', $dataAf);
		
					$this->db->query("UPDATE " . DB_PREFIX . "order SET autofolie_remove = '1' WHERE order_id = '" . (int)$order_id . "'");
				}
			}*/

            $this->load->controller('extension/module/telnotification/getHistoryData', array('order_id'=>$order_id,'comment'=>''));

            $this->response->addHeader('Content-Type: application/json');
            $json = array('success' => 'success');
            echo json_encode($json);
        } else {
        	return new Action('error/not_found');
        }
    }

    public function removeCustomer() {
        if (isset($this->request->get['customer_id'])) {
                        
	        $customer_id = $this->request->get['customer_id'];
            
            if ($customer_id > 0) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "customerdel SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

				$this->db->query("DELETE FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

				$this->db->query("DELETE FROM " . DB_PREFIX . "customer_activity WHERE customer_id = '" . (int)$customer_id . "'");

				$this->db->query("DELETE FROM " . DB_PREFIX . "customer_affiliate WHERE customer_id = '" . (int)$customer_id . "'");

				$this->db->query("DELETE FROM " . DB_PREFIX . "customer_approval WHERE customer_id = '" . (int)$customer_id . "'");

		 		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_history WHERE customer_id = '" . (int)$customer_id . "'");

				$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");

				$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");

				$this->db->query("DELETE FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$customer_id . "'");

				$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");

	            $this->response->addHeader('Content-Type: application/json');
	            $json = array('success' => 'success');
	            echo json_encode($json);
	        } else {
	        	return new Action('error/not_found');	
	        }
        } else {
        	return new Action('error/not_found');
        }
    }


	public function autocomplete() {

		$json = array();



		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_email'])) {

			if (isset($this->request->get['filter_name'])) {

				$filter_name = $this->request->get['filter_name'];

			} else {

				$filter_name = '';

			}



			if (isset($this->request->get['filter_email'])) {

				$filter_email = $this->request->get['filter_email'];

			} else {

				$filter_email = '';

			}



			if (isset($this->request->get['filter_affiliate'])) {

				$filter_affiliate = $this->request->get['filter_affiliate'];

			} else {

				$filter_affiliate = '';

			}



			$this->load->model('account/customer');



			$filter_data = array(

				'filter_name'      => $filter_name,

				'filter_email'     => $filter_email,

				'filter_affiliate' => $filter_affiliate,

				'start'            => 0,

				'limit'            => $this->config->get('config_limit_autocomplete')

			);



			$results = $this->model_account_customer->getCustomers($filter_data);

			foreach ($results as $result) {

				if (!empty($result)) {
					if ($result['customer_type'] == 1 && !empty($result['company_name'])) {
						$customer_name = $result['company_name'] . ' | ' . $result['firstname'] . ' ' . $result['lastname'];
					} else {
						$customer_name = $result['firstname'] . ' ' . $result['lastname'];
					}
				} else {
					$customer_name = $result['firstname'] . ' ' . $result['lastname'];
				}

				$json[] = array(

					'customer_id'       => $result['customer_id'],

					'customer_group_id' => $result['customer_group_id'],

					'name'              => $customer_name,

					'customer_group'    => $result['customer_group'],

					'customer_name'		=> $customer_name,

					'firstname'         => $customer_name,

					'lastname'          => $result['lastname'],

					'email'             => $result['email'],

					'telephone'         => $result['telephone'],

					'custom_field'      => json_decode($result['custom_field'], true),

				);

			}

		}



		$sort_order = array();



		foreach ($json as $key => $value) {

			$sort_order[$key] = $value['name'];

		}



		array_multisort($sort_order, SORT_ASC, $json);



		$this->response->addHeader('Content-Type: application/json');

		$this->response->setOutput(json_encode($json));

	}



        public function pixselAddProduct() {
	        $json = array();

	        $orderId = $this->request->get['order_id'];
	        $productId = $this->request->post['product_id'];
	        $quantity = $this->request->post['quantity'];
	        $options = $this->request->post['options'];


	        if (!$productId || !$quantity) {
	        $json['error'] = 'Помилка: необхідні параметри не вказані.';
	        }else {

	        $productOptionValueId = reset($options);

	        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$orderId . "' AND product_id = '" . (int)$productId . "'");
	        $productOptionValueId = reset($options);
	        $productOptionValueQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$productId . "' AND product_option_value_id = '" . (int)$productOptionValueId . "'");
	        $orderOptionQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE product_option_value_id = '" . (int)$productOptionValueId . "' AND order_id = '" . (int)$orderId . "'");

	        if($query->num_rows && $orderOptionQuery->num_rows){
	        $json['error'] = 'Товар вже існує в замовленні.';
	        }else{

	        $data_product = array();
	        $data_product['order_id'] = $orderId;
	        $data_product['product_id'] = $productId;
	        $data_product['quantity'] = $quantity;

	        $customerGroupQuery = $this->db->query("SELECT customer_group_id FROM " . DB_PREFIX . "order WHERE order_id = '" . (int)$orderId . "'");
	        $customerGroupId = $customerGroupQuery->row['customer_group_id'];

	        $productDiscountQuery = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$productId . "' AND customer_group_id = '" . (int)$customerGroupId . "' ORDER BY quantity ASC, priority ASC, price ASC LIMIT 1");
	        if ($productDiscountQuery->num_rows) {
	        $data_product['price'] = $productDiscountQuery->row['price'];
	        }else{
	        $data_product['price'] = 0;
	        }

	        $languageId = $this->config->get('config_language_id');
	        // $productQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$productId . "'");
	        $productDescriptionQuery = $this->db->query("SELECT name FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$productId . "' AND language_id = '" . (int)$languageId . "'");

	        $data_product['name'] = $productDescriptionQuery->row['name'];
	        $data_product['total'] = $data_product['price'] * $data_product['quantity'];
	        $data_product['tax'] = 0;
	        $data_product['reward'] = 0;


	        $data_product['model'] = $productOptionValueQuery->row['pixsel_sku'];


	        $this->db->query("INSERT INTO " . DB_PREFIX . "order_product
	        SET order_id = '" . (int)$data_product['order_id'] . "',
	        product_id = '" . (int)$data_product['product_id'] . "',
	        quantity = '" . (int)$data_product['quantity'] . "',
	        price = '" . (float)$data_product['price'] . "',
	        name = '" . $this->db->escape($data_product['name']) . "',
	        total = '" . (float)$data_product['total'] . "',
	        tax = '" . (float)$data_product['tax'] . "',
	        reward = '" . (int)$data_product['reward'] . "',
	        model = '" . $this->db->escape($data_product['model']) . "'");


	        $orderProductId = $this->db->getLastId();

	        foreach ($options as $productOptionId => $optionValueId) {

	        $optionId = $productOptionValueQuery->row['option_id'];
	        $optionValueId = $productOptionValueQuery->row['option_value_id'];
	        $optionType = $this->db->query("SELECT type FROM " . DB_PREFIX . "option WHERE option_id = '" . (int)$optionId . "'")->row['type'];
	        $optionName = $this->db->query("SELECT name FROM " . DB_PREFIX . "option_description WHERE option_id = '" . (int)$optionId . "' AND language_id = '" . (int)$languageId . "'")->row['name'];
	        $optionValue = $this->db->query("SELECT name FROM " . DB_PREFIX . "option_value_description WHERE option_id = '" . (int)$optionId . "'AND option_value_id = '". (int)$optionValueId ."' AND language_id = '" . (int)$languageId . "'")->row['name'];

	        $this->db->query("INSERT INTO " . DB_PREFIX . "order_option
	        SET order_id = '" . (int)$orderId . "',
	        order_product_id = '" . (int)$orderProductId . "',
	        product_option_id = '" . (int)$productOptionId . "',
	        product_option_value_id = '" . (int)$productOptionValueId . "',
	        name = '" . $this->db->escape($optionName) . "',
	        value = '" . $this->db->escape($optionValue) . "',
	        type = '" . $this->db->escape($optionType) . "'");
	        }

	        $this->pixselAddOrderBox($orderId, $productId, $productOptionValueQuery->row['product_option_value_id']);

	        // Обновляем товары согласно группы клиента
	        $this->load->model('account/customer');
	        $customer_id = $this->db->query("SELECT customer_id FROM " . DB_PREFIX . "order WHERE order_id = '" . (int)$orderId . "'")->row['customer_id'];
	        $customer_info = $this->model_account_customer->getCustomer($customer_id);
	        $customer_group_id = $customer_info['customer_group_id'];
	        $order_id = $orderId;
	        $ototal = 0;
	        $products_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
	        $order_products = $products_query->rows;
	        foreach ($order_products as $order_product) {
	            $product_id = $order_product['product_id'];
	            $order_product_id = $order_product['order_product_id'];
	            $quantity = $order_product['quantity'];
	            $price_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . $customer_group_id . "'");
	            $new_price = $price_query->row['price'];
	            $new_total = $new_price*$quantity;
	            $ototal = $ototal + $new_total;
	        }
	        if ($ototal > 0) {
	            $this->db->query("UPDATE " . DB_PREFIX . "order SET total = '" . $ototal . "' WHERE order_id = '" . $order_id . "'");
	            // $this->db->query("UPDATE " . DB_PREFIX . "order_total SET value = '" . $ototal . "' WHERE order_id = '" . $order_id . "' AND code = 'sub_total'");
	        }
	        /// Обновляем товары согласно группы клиента

	        $this->recalculateTotals($orderId);
	        $json['success'] = 'Товар успішно додано до замовлення.';
	        }
	        }

	        $this->response->addHeader('Content-Type: application/json');
	        $this->response->setOutput(json_encode($json));
        }


        public function pixselEditProduct() {
	        $json = array();

	        $orderId = $this->request->get['order_id'];
	        $productId = $this->request->post['product_id'];
	        $options = $this->request->post['options'];


	        if (!$productId) {
	        	$json['error'] = 'Помилка: необхідні параметри не вказані.';
	        } else {

				$productOptionValueId = reset($options);

				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$orderId . "' AND product_id = '" . (int)$productId . "'");
				$queryOrder = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE order_id = '" . (int)$orderId . "'");
				$productOptionValueId = reset($options);
				$productOptionValueQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$productId . "' AND product_option_value_id = '" . (int)$productOptionValueId . "'");

				if($query->num_rows){
					$orderProductId = $query->row['order_product_id'];
					
					$data_product = array();
					$data_product['order_id'] = $orderId;
					$data_product['product_id'] = $productId;

					// $languageId = $this->config->get('config_language_id');
					$languageId = $queryOrder->row['language_id'];

					$data_product['model'] = $productOptionValueQuery->row['pixsel_sku'];

					$this->db->query("UPDATE " . DB_PREFIX . "order_product SET model = '" . $this->db->escape($data_product['model']) . "' WHERE order_id = '" . (int)$data_product['order_id'] . "' AND product_id = '" . (int)$data_product['product_id'] . "'");

					foreach ($options as $productOptionId => $optionValueId) {
						$optionId = $productOptionValueQuery->row['option_id'];
						$optionValueId = $productOptionValueQuery->row['option_value_id'];
						$optionType = $this->db->query("SELECT type FROM " . DB_PREFIX . "option WHERE option_id = '" . (int)$optionId . "'")->row['type'];
						$optionName = $this->db->query("SELECT name FROM " . DB_PREFIX . "option_description WHERE option_id = '" . (int)$optionId . "' AND language_id = '" . (int)$languageId . "'")->row['name'];
						$optionValue = $this->db->query("SELECT name FROM " . DB_PREFIX . "option_value_description WHERE option_id = '" . (int)$optionId . "'AND option_value_id = '". (int)$optionValueId ."' AND language_id = '" . (int)$languageId . "'")->row['name'];

						$this->db->query("UPDATE " . DB_PREFIX . "order_option SET product_option_id = '" . (int)$productOptionId . "', product_option_value_id = '" . (int)$productOptionValueId . "', name = '" . $this->db->escape($optionName) . "', value = '" . $this->db->escape($optionValue) . "', type = '" . $this->db->escape($optionType) . "' WHERE order_id = '" . (int)$orderId . "' AND order_product_id = '" . (int)$orderProductId . "'");
					}

					$json['success'] = 'Success update';
				}
	        }

	        $this->response->addHeader('Content-Type: application/json');
	        $this->response->setOutput(json_encode($json));
        }



        private function pixselAddOrderBox($order_id, $product_id, $product_option_value_id) {
        $productQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
        $boxName = $productQuery->row['pixsel_box'];

        $boxQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "pixsel_box WHERE pixsel_box = '" . $this->db->escape($boxName) . "'");
        $boxId = isset($boxQuery->row['pixsel_box_id']) ? $boxQuery->row['pixsel_box_id'] : '';

        $checkQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_box WHERE order_id = '" . (int)$order_id . "'AND product_option_value_id = '". (int)$product_option_value_id ."' AND product_id = '" . (int)$product_id . "'");
        if ($checkQuery->num_rows == 0 && $boxId) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_box (product_id, order_id, product_option_value_id, pixsel_box_id, name) VALUES ('" . (int)$product_id . "', '" . (int)$order_id . "','". (int)$product_option_value_id ."', '" . (int)$boxId . "', '" . $this->db->escape($boxName) . "')");
        }
        }

        public function setQtyBox(){

        $json = array();
        $order_box_id = $this->request->post['order_box_id'];
        $quantity = $this->request->post['qty'];

        if($order_box_id && isset($this->request->post['qty'])){
        $this->db->query("UPDATE `" . DB_PREFIX . "order_box` SET quantity = '" . $this->db->escape($quantity) . "' WHERE order_box_id = '" . $this->db->escape($order_box_id) . "'");
        $json['success'] = 'Кількість оновлено.';
        }else{
        $json['error'] = 'Помилка';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        }

        public function changePixselBoxOrder() {

        $json = array();
        $pixsel_box_id = $this->request->post['pixsel_box_id'];
        $product_id = $this->request->post['product_id'];
        $order_id = $this->request->post['order_id'];
        $product_option_value_id = $this->request->post['product_option_value_id'];

        if($pixsel_box_id && $product_id && $order_id && $product_option_value_id){
        if($this->getPixselBox($order_id, $product_id, $product_option_value_id)){
        $boxQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "pixsel_box WHERE pixsel_box_id = '" . $this->db->escape($pixsel_box_id) . "'");
        $boxName = $boxQuery->row['pixsel_box'];
        $this->db->query("UPDATE `" . DB_PREFIX . "order_box` SET name = '" . $this->db->escape($boxName) . "', pixsel_box_id = '" . (int)$pixsel_box_id . "' WHERE order_id = '".(int)$order_id."' AND product_id = '" . (int)$product_id . "' AND product_option_value_id = '" . (int)$product_option_value_id . "'");

        }else{
        $boxQuery = $this->db->query("SELECT * FROM " . DB_PREFIX . "pixsel_box WHERE pixsel_box_id = '" . $this->db->escape($pixsel_box_id) . "'");
        $boxName = $boxQuery->row['pixsel_box'];
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_box (product_id, order_id, product_option_value_id, pixsel_box_id, name) VALUES ('" . (int)$product_id . "', '" . (int)$order_id . "', '". (int)$product_option_value_id ."' , '" . (int)$pixsel_box_id . "', '" . $this->db->escape($boxName) . "')");
        }
        $json['success'] = 'ok';
        }else{
        $json['error'] = 'Error';
        }


        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        }


        private function recalculateTotals($order_id) {
        // Підрахунок суми товарів і податків
        $product_total = 0;
        $product_tax = 0;
        $product_query = $this->db->query("SELECT price, quantity, tax FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
        foreach ($product_query->rows as $product) {
        $product_total += $product['price'] * $product['quantity'];
        $product_tax += $product['tax'] * $product['quantity']; // Податок множимо на кількість товару
        }

        // Оновлення sub_total
        $this->db->query("UPDATE " . DB_PREFIX . "order_total SET value = '" . (float)$product_total . "' WHERE order_id = '" . (int)$order_id . "' AND code = 'sub_total'");

        // Оновлення tax
        // $this->db->query("UPDATE " . DB_PREFIX . "order_total SET value = '" . (float)$product_tax . "' WHERE order_id = '" . (int)$order_id . "' AND code = 'tax'");

        // Оновлення total
        // Спершу отримуємо інші суми (не sub_total і tax), щоб додати до загальної суми
        $other_totals_query = $this->db->query("SELECT value FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "' AND code NOT IN ('sub_total', 'tax', 'total')");
        $other_totals = 0;
        foreach ($other_totals_query->rows as $total) {
        $other_totals += $total['value'];
        }

        $new_total = $product_total + $product_tax + $other_totals;
        $this->db->query("UPDATE " . DB_PREFIX . "order_total SET value = '" . (float)$new_total . "' WHERE order_id = '" . (int)$order_id . "' AND code = 'total'");
        }

        public function changeQtyProduct() {
        $json = array();

        $order_id = $this->request->get['order_id'];
        $order_product_id = $this->request->get['order_product_id'];
        $quantity = $this->request->get['quantity'];


        if($order_id && $quantity && $order_product_id){

        $price = $this->db->query("SELECT price FROM " . DB_PREFIX . "order_product WHERE order_product_id = '" . (int)$order_product_id . "'")->row['price'];
        $total = $price * $quantity;

        $this->db->query("UPDATE " . DB_PREFIX . "order_product SET total = '" . (float)$total . "', quantity = '". (int)$quantity ."' WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '". (int)$order_product_id ."'");

        // Обновляем товары согласно группы клиента
        $this->load->model('account/customer');
        $customer_id = $this->db->query("SELECT customer_id FROM " . DB_PREFIX . "order WHERE order_id = '" . (int)$order_id . "'")->row['customer_id'];
        $customer_info = $this->model_account_customer->getCustomer($customer_id);
        $customer_group_id = $customer_info['customer_group_id'];
        $ototal = 0;
        $products_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
        $order_products = $products_query->rows;
        foreach ($order_products as $order_product) {
            $product_id = $order_product['product_id'];
            $order_product_id = $order_product['order_product_id'];
            $quantity = $order_product['quantity'];
            $price_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . $customer_group_id . "'");
            $new_price = $price_query->row['price'];
            $new_total = $new_price*$quantity;
            $ototal = $ototal + $new_total;
        }
        if ($ototal > 0) {
            $this->db->query("UPDATE " . DB_PREFIX . "order SET total = '" . $ototal . "' WHERE order_id = '" . $order_id . "'");
            // $this->db->query("UPDATE " . DB_PREFIX . "order_total SET value = '" . $ototal . "' WHERE order_id = '" . $order_id . "' AND code = 'sub_total'");
        }
        /// Обновляем товары согласно группы клиента

        $this->recalculateTotals($order_id);
        $json['success'] = 'ok';
        }else{
        $json['error'] = 'Error';
        }


        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        }

        public function pixselDeleteProduct() {
        $json = [];

        $order_id = $this->request->get['order_id'];
        $product_id = $this->request->get['product_id'];
        $order_product_id = $this->request->get['order_product_id'];

        if ($order_id && $order_product_id) {

        $this->db->query("DELETE FROM `" . DB_PREFIX . "order_option` WHERE order_product_id = '" . (int)$order_product_id . "'");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "order_product` WHERE order_product_id = '" . (int)$order_product_id . "'");

        $orderOptionQuery = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_option` WHERE order_product_id = '" . (int)$order_product_id . "'");

        if($orderOptionQuery->num_rows){

        $this->db->query("DELETE FROM `" . DB_PREFIX . "order_box` WHERE product_option_value_id ='". $orderOptionQuery->row['product_option_value_id'] ."' AND product_id = '" . (int)$product_id . "' AND order_id = '" . (int)$order_id . "'");
        }

        // Обновляем товары согласно группы клиента
        $this->load->model('account/customer');
        $customer_id = $this->db->query("SELECT customer_id FROM " . DB_PREFIX . "order WHERE order_id = '" . (int)$order_id . "'")->row['customer_id'];
        $customer_info = $this->model_account_customer->getCustomer($customer_id);
        $customer_group_id = $customer_info['customer_group_id'];
        $ototal = 0;
        $products_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
        $order_products = $products_query->rows;
        foreach ($order_products as $order_product) {
            $product_id = $order_product['product_id'];
            $order_product_id = $order_product['order_product_id'];
            $quantity = $order_product['quantity'];
            $price_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . $customer_group_id . "'");
            $new_price = $price_query->row['price'];
            $new_total = $new_price*$quantity;
            $ototal = $ototal + $new_total;
        }
        if ($ototal > 0) {
            $this->db->query("UPDATE " . DB_PREFIX . "order SET total = '" . $ototal . "' WHERE order_id = '" . $order_id . "'");
            // $this->db->query("UPDATE " . DB_PREFIX . "order_total SET value = '" . $ototal . "' WHERE order_id = '" . $order_id . "' AND code = 'sub_total'");
        }
        /// Обновляем товары согласно группы клиента

        $json['success'] = 'Товар видалено з замовлення';
        $this->recalculateTotals($order_id);
        } else {
        $json['error'] = 'Не вказано ID товару або замовлення';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        }
        /* pixsel end */


	public function editAutocomplete() {
		$json = array();

		if (isset($this->request->get['product_id'])) {
			$product_id = $this->request->get['product_id'];

			$this->load->model('catalog/product');
			$this->load->model('catalog/option');

			$results = $this->model_catalog_product->getProduct($product_id);

			foreach ($results as $result) {
				$option_data = array();

				$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);

				foreach ($product_options as $product_option) {
					$option_info = $this->model_catalog_option->getOption($product_option['option_id']);

					if ($option_info) {
						$product_option_value_data = array();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

							if ($option_value_info) {
								$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $product_option_value['price_prefix']
								);
							}
						}

						$option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $product_option['value'],
							'required'             => $product_option['required']
						);
					}
				}

				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')) . ' | ' . $result['model'],
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => $result['price']
				);
			}
		}
		// print_r($json);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	/*public function orderAf(){

        $json = array();

        $order_id = $this->request->post['order_id'];

		$order_info = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . $order_id . "'")->row;

        if($order_info['autofolie'] > 0) {
			$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");

			$order_products_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "'");

			$order_options_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_option` WHERE order_id = '" . (int)$order_id . "'");

			$order_totals_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "'");

			$order_data = $order_query->row; // Основная информация о заказе
			$order_data['products'] = [];
			
			foreach ($order_products_query->rows as $product) {
				$product_id = $product['order_product_id'];

				$product_options = array_filter($order_options_query->rows, function ($option) use ($product_id) {
					return $option['order_product_id'] == $product_id;
				});

				$product['option'] = array_values($product_options); // array_values сбрасывает индексы
				$order_data['products'][] = $product;
			}
			$order_data['totals'] = $order_totals_query->rows;

			// Post order to Autofolie.pl
			$order_data['order_id'] = $order_info['autofolie'];
			if ($order_data['autofolie_remove'] > 0) {
				$order_data['order_status_id'] = 31;
			} else {
				$order_data['order_status_id'] = $order_info['autofolie_status'];
			}
			$order_data['autofolie_order_id'] = $order_info['autofolie'];

			$json['pixsel'] = $order_id;
			$json['autofolie'] = $order_info['autofolie'];

			$this->db->query("UPDATE " . DB_PREFIX . "order SET autofolie_remove = '0' WHERE order_id = '" . (int)$order_id . "'");

			$clientAutofolie = new httpAutofolieClient();
			$clientAutofolie->orderApi('editApi', '&autofolie_order_id=' . $order_info['autofolie'], $order_data);
        } else {
			$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");

			$order_products_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "'");

			$order_options_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_option` WHERE order_id = '" . (int)$order_id . "'");

			$order_totals_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "'");

			$order_data = $order_query->row; // Основная информация о заказе
			
			$order_data['products'] = [];
			
			foreach ($order_products_query->rows as $product) {
				$product_id = $product['order_product_id'];

				$product_options = array_filter($order_options_query->rows, function ($option) use ($product_id) {
					return $option['order_product_id'] == $product_id;
				});

				$product['option'] = array_values($product_options); // array_values сбрасывает индексы
				$order_data['products'][] = $product;
			}
			$order_data['totals'] = $order_totals_query->rows;

			// Post order to Autofolie.pl
			$clientAutofolie = new httpAutofolieClient();
			$order_data['pixsel_order_id'] = $order_id;
			$autofolieResult = $clientAutofolie->orderApi('addApi', '', $order_data);
			$autofolie = json_decode($autofolieResult, true);
			$json['pixsel'] = $order_id;
			$json['autofolie'] = $autofolie['order_id'];
			if (!empty($autofolie)) {
				$this->db->query("UPDATE " . DB_PREFIX . "order SET autofolie = '" . $autofolie['order_id'] . "', autofolie_remove = '0' WHERE order_id = '" . (int)$order_id . "'");
			}
		}

		$json['success'] = 'Success';
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

	public function orderAfRemove(){

        $json = array();

        $order_id = $this->request->post['order_id'];
		$autofolie = $this->request->post['autofolie'];
		$data = array(
			'order_id' => $autofolie,
		);

		$clientAutofolie = new httpAutofolieClient();
		$clientAutofolie->orderApi('removeApi', '', $data);

		// $this->db->query("UPDATE " . DB_PREFIX . "order SET autofolie = '' WHERE order_id = '" . (int)$order_id . "'");

		$this->db->query("UPDATE " . DB_PREFIX . "order SET autofolie_remove = '1' WHERE order_id = '" . (int)$order_id . "'");

		$json['success'] = 'Success';
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
	}*/

}
