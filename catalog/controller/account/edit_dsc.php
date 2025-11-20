<?php
class ControllerAccountEditDsc extends Controller {

	private $error = array();

	public function index() {

		if (!$this->customer->isLogged()) {

			$this->session->data['redirect'] = $this->url->link('account/edit_dsc', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));

		}

		$this->load->language('account/edit_dsc');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->setRobots('noindex,follow');

		$this->load->model('account/customer');

		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');


		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->session->data['dsc_language_id'] = $this->request->post['language_id'];
			$this->session->data['dsc_language_code'] = $this->request->post['dsc_language'];
			$this->session->data['currency'] = $this->request->post['dsc_currency'];

			$this->model_account_customer->editDefaultSettingCustomer($this->customer->getId(), $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('account/account', '', true));
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
			'text' => $this->language->get('text_edit_dsc'),
			'href' => $this->url->link('account/edit_dsc', '', true)
		);


		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = '';
		}

		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['confirm'])) {
			$data['error_confirm'] = $this->error['confirm'];
		} else {
			$data['error_confirm'] = '';
		}

		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}

		$data['action'] = $this->url->link('account/edit_dsc', '', true);

		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
		}

		$this->document->addStyle('catalog/view/theme/default/stylesheet/shippingdata.css');

		// MAIN INFO
		if (isset($this->request->post['customer_type'])) {
			$data['customer_type'] = $this->request->post['customer_type'];
		} elseif (!empty($customer_info)) {
			$data['customer_type'] = (!empty($customer_info['customer_type']) ? $customer_info['customer_type'] : '');
		} else {
			$data['customer_type'] = 0;
		}
		if (isset($this->request->post['company_name'])) {
			$data['company_name'] = $this->request->post['company_name'];
		} elseif (!empty($customer_info)) {
			$data['company_name'] = (!empty($customer_info['company_name']) ? $customer_info['company_name'] : '');
		} else {
			$data['company_name'] = '';
		}
		if (isset($this->request->post['company_nip'])) {
			$data['company_nip'] = $this->request->post['company_nip'];
		} elseif (!empty($customer_info)) {
			$data['company_nip'] = (!empty($customer_info['company_nip']) ? $customer_info['company_nip'] : '');
		} else {
			$data['company_nip'] = '';
		}
		if (isset($this->request->post['company_vatcode'])) {
			$data['company_vatcode'] = $this->request->post['company_vatcode'];
		} elseif (!empty($customer_info)) {
			$data['company_vatcode'] = (!empty($customer_info['company_vatcode']) ? $customer_info['company_vatcode'] : '');
		} else {
			$data['company_vatcode'] = '';
		}
		if (isset($this->request->post['pl_company_name'])) {
			$data['pl_company_name'] = $this->request->post['pl_company_name'];
		} elseif (!empty($customer_info)) {
			$data['pl_company_name'] = (!empty($customer_info['pl_company_name']) ? $customer_info['pl_company_name'] : '');
		} else {
			$data['pl_company_name'] = '';
		}
		if (isset($this->request->post['company_region'])) {
			$data['company_region'] = $this->request->post['company_region'];
		} elseif (!empty($customer_info)) {
			$data['company_region'] = (!empty($customer_info['company_region']) ? $customer_info['company_region'] : '');
		} else {
			$data['company_region'] = '';
		}
		if (isset($this->request->post['company_address'])) {
			$data['company_address'] = $this->request->post['company_address'];
		} elseif (!empty($customer_info)) {
			$data['company_address'] = (!empty($customer_info['company_address']) ? $customer_info['company_address'] : '');
		} else {
			$data['company_address'] = '';
		}
		if (isset($this->request->post['company_zip'])) {
			$data['company_zip'] = $this->request->post['company_zip'];
		} elseif (!empty($customer_info)) {
			$data['company_zip'] = (!empty($customer_info['company_zip']) ? $customer_info['company_zip'] : '');
		} else {
			$data['company_zip'] = '';
		}
		if (isset($this->request->post['invoice_banks'])) {
			$data['invoice_banks'] = $this->request->post['invoice_banks'];
		} elseif (!empty($customer_info)) {
			$data['invoice_banks'] = json_decode($customer_info['invoice_banks'], true);
		} else {
			$data['invoice_banks'] = '';
		}

		if (isset($this->request->post['country_id'])) {
			$data['country_id'] = $this->request->post['country_id'];
		} elseif (!empty($customer_info)) {
			$data['country_id'] = ( (!empty($customer_info['country_id']) && $customer_info['country_id'] != 0) ? $customer_info['country_id'] : '');
		} else {
			$data['country_id'] = '';
		}

		if (isset($this->request->post['firstname'])) {
			$data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($customer_info)) {
			$data['firstname'] = (!empty($customer_info['firstname']) ? $customer_info['firstname'] : '');
		} else {
			$data['firstname'] = $this->config->get('firstname');
		}

		if (isset($this->request->post['lastname'])) {
			$data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($customer_info)) {
			$data['lastname'] = (!empty($customer_info['lastname']) ? $customer_info['lastname'] : '');
		} else {
			$data['lastname'] = $this->config->get('lastname');
		}

		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($customer_info)) {
			$data['telephone'] = (!empty($customer_info['telephone']) ? $customer_info['telephone'] : '');
		} else {
			$data['telephone'] = $this->config->get('telephone');
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($customer_info)) {
			$data['email'] = (!empty($customer_info['email']) ? $customer_info['email'] : '');
		} else {
			$data['email'] = $this->config->get('email');
		}

		if (isset($this->request->post['company_nip'])) {
			$data['company_nip'] = $this->request->post['company_nip'];
		} elseif (!empty($customer_info)) {
			$data['company_nip'] = (!empty($customer_info['company_nip']) ? $customer_info['company_nip'] : '');
		} else {
			$data['company_nip'] = '';
		}

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} elseif (!empty($customer_info)) {
			$data['password'] = (!empty($customer_info['password']) ? $customer_info['password'] : '');
		} else {
			$data['password'] = '';
		}

		if (isset($this->request->post['confirm'])) {
			$data['confirm'] = $this->request->post['confirm'];
		} elseif (!empty($customer_info)) {
			$data['confirm'] = (!empty($customer_info['confirm']) ? $customer_info['confirm'] : '');
		} else {
			$data['confirm'] = '';
		}

		// DEFAULT INFO
		if (isset($this->request->post['dsc_status'])) {
			$data['dsc_status'] = $this->request->post['dsc_status'];
		} elseif (!empty($customer_info)) {
			$data['dsc_status'] = (!empty($customer_info['dsc_status']) ? $customer_info['dsc_status'] : 0);
		} else {
			$data['dsc_status'] = $this->config->get('dsc_status');
		}

		if (isset($this->request->post['dsc_firstname'])) {
			$data['dsc_firstname'] = $this->request->post['dsc_firstname'];
		} elseif (!empty($customer_info)) {
			$data['dsc_firstname'] = (!empty($customer_info['dsc_firstname']) ? $customer_info['dsc_firstname'] : $customer_info['firstname']);
		} else {
			$data['dsc_firstname'] = $this->config->get('dsc_firstname');
		}

		if (isset($this->request->post['dsc_lastname'])) {
			$data['dsc_lastname'] = $this->request->post['dsc_lastname'];
		} elseif (!empty($customer_info)) {
			$data['dsc_lastname'] = (!empty($customer_info['dsc_lastname']) ? $customer_info['dsc_lastname'] : $customer_info['lastname']);
		} else {
			$data['dsc_lastname'] = $this->config->get('dsc_lastname');
		}

		if (isset($this->request->post['dsc_telephone'])) {
			$data['dsc_telephone'] = $this->request->post['dsc_telephone'];
		} elseif (!empty($customer_info)) {
			$data['dsc_telephone'] = (!empty($customer_info['dsc_telephone']) ? $customer_info['dsc_telephone'] : $customer_info['telephone']);
		} else {
			$data['dsc_telephone'] = $this->config->get('dsc_telephone');
		}

		if (isset($this->request->post['dsc_payment_method'])) {
			$data['dsc_payment_method'] = $this->request->post['dsc_payment_method'];
		} elseif (!empty($customer_info)) {
			$data['dsc_payment_method'] = $customer_info['dsc_payment_method'];
		} else {
			$data['dsc_payment_method'] = $this->config->get('dsc_payment_method');
		}

		if (isset($this->request->post['dsc_shipping_method'])) {
			$data['dsc_shipping_method'] = $this->request->post['dsc_shipping_method'];
		} elseif (!empty($customer_info)) {
			$data['dsc_shipping_method'] = $customer_info['dsc_shipping_method'];
		} else {
			$data['dsc_shipping_method'] = $this->config->get('dsc_shipping_method');
		}

		if (isset($this->request->post['parcelLocker'])) {
			$data['parcelLocker'] = $this->request->post['parcelLocker'];
		} elseif (!empty($customer_info)) {
			$data['parcelLocker'] = $customer_info['dsc_parcelLocker'];
		} else {
			$data['parcelLocker'] = '';
		}

		if (isset($this->request->post['dsc_vat'])) {
			$data['dsc_vat'] = $this->request->post['dsc_vat'];
		} elseif (!empty($customer_info)) {
			$data['dsc_vat'] = (isset($customer_info['dsc_vat']) ? $customer_info['dsc_vat'] : 1);
		} else {
			$data['dsc_vat'] = 1;
		}

		if (isset($this->request->post['dsc_faktyre'])) {
			$data['dsc_faktyre'] = $this->request->post['dsc_faktyre'];
		} elseif (!empty($customer_info)) {
			$data['dsc_faktyre'] = (isset($customer_info['dsc_faktyre']) ? $customer_info['dsc_faktyre'] : 0);
		} else {
			$data['dsc_faktyre'] = 0;
		}
		if (isset($this->request->post['dsc_privat_faktyre'])) {
			$data['dsc_privat_faktyre'] = $this->request->post['dsc_privat_faktyre'];
		} elseif (!empty($customer_info)) {
			$data['dsc_privat_faktyre'] = (isset($customer_info['dsc_privat_faktyre']) ? $customer_info['dsc_privat_faktyre'] : 0);
		} else {
			$data['dsc_privat_faktyre'] = 0;
		}
		if (isset($this->request->post['dsc_nip'])) {
			$data['dsc_nip'] = $this->request->post['dsc_nip'];
		} elseif (!empty($customer_info)) {
			$data['dsc_nip'] = (!empty($customer_info['dsc_nip']) ? $customer_info['dsc_nip'] : (!empty($customer_info['company_nip']) ? $customer_info['company_nip'] : ''));
		} else {
			$data['dsc_nip'] = '';
		}
		if (isset($this->request->post['dsc_vatcode'])) {
			$data['dsc_vatcode'] = $this->request->post['dsc_vatcode'];
		} elseif (!empty($customer_info)) {
			$data['dsc_vatcode'] = (!empty($customer_info['dsc_vatcode']) ? $customer_info['dsc_vatcode'] : (!empty($customer_info['company_vatcode']) ? $customer_info['company_vatcode'] : ''));
		} else {
			$data['dsc_vatcode'] = '';
		}


		if (isset($this->request->post['dsc_currency'])) {
			$data['dsc_currency'] = $this->request->post['dsc_currency'];
		} elseif (!empty($customer_info)) {
			$data['dsc_currency'] = $customer_info['dsc_currency'];
		} else {
			$data['dsc_currency'] = '';
		}
		$this->load->model('localisation/currency');
		$data['currencies'] = array();
		$results_currency = $this->model_localisation_currency->getCurrencies();
		foreach ($results_currency as $result_currency) {
			if ($result_currency['status']) {
				$data['currencies'][] = array(
					'currency_id'  => $result_currency['currency_id'],
					'title'        => $result_currency['title'],
					'code'         => $result_currency['code'],
					'symbol_left'  => $result_currency['symbol_left'],
					'symbol_right' => $result_currency['symbol_right']
				);
			}
		}
		rsort($data['currencies']);

		if (isset($this->request->post['dsc_language'])) {
			$data['dsc_language'] = $this->request->post['dsc_language'];
		} elseif (!empty($customer_info)) {
			$data['dsc_language'] = ( !empty($customer_info['dsc_language']) ? $customer_info['dsc_language'] : $this->session->data['language'] );
		} else {
			$data['dsc_language'] = '';
		}
		if (isset($this->request->post['language_id'])) {
			$data['language_id'] = $this->request->post['language_id'];
		} elseif (!empty($customer_info)) {
			$data['language_id'] = ( !empty($customer_info['language_id']) ? $customer_info['language_id'] : '5' );
		} else {
			$data['language_id'] = '5';
		}
		$this->load->model('localisation/language');
		$data['languages'] = array();
		$results_language = $this->model_localisation_language->getLanguages();
		foreach ($results_language as $result_language) {
			if ($result_language['status']) {
				$data['languages'][] = array(
					'language_id'  => $result_language['language_id'],
					'name'	       => $result_language['name'],
					'code'         => $result_language['code'],
				);
			}
		}
		rsort($data['languages']);

		if (isset($this->request->post['parcelAddressLocker'])) {
			$data['parcelAddressLocker'] = html_entity_decode($this->request->post['parcelAddressLocker'], ENT_QUOTES, 'UTF-8');
			$data['parcelAddressValLocker'] = htmlentities($this->request->post['parcelAddressLocker']);
		} elseif (!empty($customer_info)) {
			$data['parcelAddressLocker'] = html_entity_decode($customer_info['dsc_parcelAddressLocker'], ENT_QUOTES, 'UTF-8');
			$data['parcelAddressValLocker'] = htmlentities($customer_info['dsc_parcelAddressLocker']);
		} else {
			$data['parcelAddressLocker'] = '';
		}

		if (isset($this->request->post['dsc_postcode'])) {
			$data['dsc_postcode'] = $this->request->post['dsc_postcode'];
		} elseif (!empty($customer_info)) {
			$data['dsc_postcode'] = (!empty($customer_info['dsc_postcode']) ? $customer_info['dsc_postcode'] : '');
		} else {
			$data['dsc_postcode'] = '';
		}

		if (isset($this->request->post['dsc_country'])) {
			$data['dsc_country'] = $this->request->post['dsc_country'];
		} elseif (!empty($customer_info)) {
			$data['dsc_country'] = $customer_info['dsc_country'];
		} else {
			$data['dsc_country'] = $this->config->get('dsc_country');
		}

		if (isset($this->request->post['dsc_city'])) {
			$data['dsc_city'] = $this->request->post['dsc_city'];
		} elseif (!empty($customer_info)) {
			$data['dsc_city'] = $customer_info['dsc_city'];
		} else {
			$data['dsc_city'] = $this->config->get('dsc_city');
		}

		if (isset($this->request->post['dsc_address_1'])) {
			$data['dsc_address_1'] = $this->request->post['dsc_address_1'];
		} elseif (!empty($customer_info)) {
			$data['dsc_address_1'] = $customer_info['dsc_address_1'];
		} else {
			$data['dsc_address_1'] = $this->config->get('dsc_address_1');
		}

		if (isset($this->request->post['dsc_opc_not_call_me'])) {
			$data['dsc_opc_not_call_me'] = $this->request->post['dsc_opc_not_call_me'];
		} elseif (!empty($customer_info)) {
			$data['dsc_opc_not_call_me'] = $customer_info['dsc_opc_not_call_me'];
		} else {
			$data['dsc_opc_not_call_me'] = $this->config->get('dsc_opc_not_call_me');
		}

		$data['payment_address'] = array('country_id' => $this->config->get('config_country_id'), 'zone_id' => $this->config->get('config_zone_id'), 'firstname' => '', 'lastname' => '', 'company' => '', 'address_1' => '');

		$total = 100;

		// Payment Methods
		$data['payment_methods'] = array();

		$this->load->model('setting/extension');

		$presults = $this->model_setting_extension->getExtensions('payment');

		foreach ($presults as $presult) {
			if ($this->config->get('payment_' . $presult['code'] . '_status')) {
				// $this->load->language('extension/payment/' . $result['code']);

				$this->load->model('extension/payment/' . $presult['code']);
	
				$pmethod = $this->{'model_extension_payment_' . $presult['code']}->getMethod(array('country_id' => $data['country_id']), 0);

				// $method_data = array();

				// $method_data = $pmethod;

				/*$method_data = array(
					'code'        => $result['code'],
					'title'       => $this->language->get('text_title'),
					'terms'       => '',
					'sort_order'  => $this->config->get('payment_' . $result['code'] . '_sort_order'),
					'geo_zone_id' => $this->config->get('payment_' . $result['code'] . '_geo_zone_id')
				);*/


				$data['payment_methods'][$presult['code']] = $pmethod;
			}
		}

		$sort_order = array();

		foreach ($data['payment_methods'] as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $data['payment_methods']);


		$data['shipping_address'] = array('country_id' => $this->config->get('config_country_id'), 'zone_id' => $this->config->get('config_zone_id'), 'firstname' => '', 'lastname' => '', 'company' => '', 'address_1' => '');

		$shipping_methods = array();

		$this->load->model('setting/extension');

		$shipping_results = $this->model_setting_extension->getExtensions('shipping');

		foreach ($shipping_results as $result) {
			if ($this->config->get('shipping_' . $result['code'] . '_status')) {
				$this->load->model('extension/shipping/' . $result['code']);

				$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($data['shipping_address']);

				if($result['code'] == 'easyship'){
					foreach($quote['easyship0']['quote'] as $val_quote){
						$shipping_methods[] = array(
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
					$shipping_methods[] = array(
						'code'        => $result['code'],
						'title'       => $quote['title'],
						'quote'       => $quote['quote'],
						'sort_order'  => $quote['sort_order'],
						'error'       => $quote['error'],
						'geo_zone_id' => $this->config->get('shipping_' . $result['code'] . '_geo_zone_id')
					);
				}
			}
		}

		$data['shipping_methods'] = $shipping_methods;

		// $countries = $this->model_localisation_country->getCountries();
		$this->load->model('checkout/onepcheckout');
		$countries = $this->model_checkout_onepcheckout->getCountryDeliveries();
		$countrys = array();
		foreach($countries as $country) {
			$zone = $this->model_localisation_zone->getZonesZoneByCountryId($country['country_id']);
			$countrys[] = array(
				'country_id'  => $country['country_id'],
				'country_delivery_id'  => $country['country_delivery_id'],
				'name'		  => $country['name'],
				'geo_zone_id' => $zone['geo_zone_id']
			);
		}
		$data['countries'] = $countrys;

		$data['back'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/edit_dsc', $data));
	}

	public function saveDsc() {
		if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$json = array();

			$this->load->model('account/customer');
			$this->load->language('account/edit_dsc');
			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
				if (isset($this->request->post['customer_id'])) {
					$customer_id = (int)$this->request->post['customer_id'];
				}

				$this->model_account_customer->editDefaultSettingCustomer($customer_id, $this->request->post);

				$json['success'] = $this->language->get('text_success');

				$client_info = $this->model_account_customer->getCustomer($customer_id);

				$json['client_info'] = $client_info['firstname'] . ' ' . $client_info['lastname'] . ' ' . $client_info['telephone'];

				$json['client_id'] = $client_info['customer_id'];

				$this->session->data['old_client_id'] = 0;

				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
			}
		} else {
		  $this->response->redirect($this->url->link('error/not_found', '', true));
		}
	}

	public function editInfoDsc() {
		if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			if (!$this->customer->isLogged()) {
				if($this->customer->getCustomerType() != 2){
					return;
				}
			}

			$this->load->model('account/customer');
			$this->load->model('account/customer_group');

			$this->load->model('localisation/country');
			$this->load->model('localisation/zone');

			$this->load->language('account/edit_dsc');

		    $data['customer_groups'] = $this->model_account_customer_group->getCustomerGroups();

			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
			} else {
				$data['error_warning'] = '';
			}

			if (isset($this->error['firstname'])) {
				$data['error_firstname'] = $this->error['firstname'];
			} else {
				$data['error_firstname'] = '';
			}

			if (isset($this->error['lastname'])) {
				$data['error_lastname'] = $this->error['lastname'];
			} else {
				$data['error_lastname'] = '';
			}

			if (isset($this->error['email'])) {
				$data['error_email'] = $this->error['email'];
			} else {
				$data['error_email'] = '';
			}

			if (isset($this->error['password'])) {
				$data['error_password'] = $this->error['password'];
			} else {
				$data['error_password'] = '';
			}

			if (isset($this->error['confirm'])) {
				$data['error_confirm'] = $this->error['confirm'];
			} else {
				$data['error_confirm'] = '';
			}

			if (isset($this->error['telephone'])) {
				$data['error_telephone'] = $this->error['telephone'];
			} else {
				$data['error_telephone'] = '';
			}

			$data['action'] = $this->url->link('account/edit_dsc/editInfoDsc', '', true);

			if (isset($this->request->get['customer_id'])) {
				$customer_id = (int)$this->request->get['customer_id'];
				$data['customer_id'] = (int)$this->request->get['customer_id'];
			} else {
				$customer_id = 0;
				$data['customer_id'] = 0;
			}
			if ($this->request->server['REQUEST_METHOD'] != 'POST') {
				$customer_info = $this->model_account_customer->getCustomer($customer_id);
			}

			$this->document->addStyle('catalog/view/theme/default/stylesheet/shippingdata.css');

			// MAIN INFO
			if (isset($this->request->post['customer_group_id'])) {
				$data['customer_group_id'] = $this->request->post['customer_group_id'];
			} elseif (!empty($customer_info)) {
				$data['customer_group_id'] = $customer_info['customer_group_id'];
			} else {
				$data['customer_group_id'] = $this->config->get('config_customer_group_id');
			}
			if (isset($this->request->post['country_id'])) {
				$data['country_id'] = $this->request->post['country_id'];
			} elseif (!empty($customer_info)) {
				$data['country_id'] = $customer_info['country_id'];
			} else {
				$data['country_id'] = 0;
			}
			if (isset($this->request->post['customer_type'])) {
				$data['customer_type'] = $this->request->post['customer_type'];
			} elseif (!empty($customer_info)) {
				$data['customer_type'] = (!empty($customer_info['customer_type']) ? $customer_info['customer_type'] : '');
			} else {
				$data['customer_type'] = 0;
			}
			if (isset($this->request->post['company_name'])) {
				$data['company_name'] = $this->request->post['company_name'];
			} elseif (!empty($customer_info)) {
				$data['company_name'] = (!empty($customer_info['company_name']) ? $customer_info['company_name'] : '');
			} else {
				$data['company_name'] = '';
			}
			if (isset($this->request->post['company_nip'])) {
				$data['company_nip'] = $this->request->post['company_nip'];
			} elseif (!empty($customer_info)) {
				$data['company_nip'] = (!empty($customer_info['company_nip']) ? $customer_info['company_nip'] : '');
			} else {
				$data['company_nip'] = '';
			}
			if (isset($this->request->post['company_vatcode'])) {
				$data['company_vatcode'] = $this->request->post['company_vatcode'];
			} elseif (!empty($customer_info)) {
				$data['company_vatcode'] = (!empty($customer_info['company_vatcode']) ? $customer_info['company_vatcode'] : '');
			} else {
				$data['company_vatcode'] = '';
			}
			if (isset($this->request->post['pl_company_name'])) {
				$data['pl_company_name'] = $this->request->post['pl_company_name'];
			} elseif (!empty($customer_info)) {
				$data['pl_company_name'] = (!empty($customer_info['pl_company_name']) ? $customer_info['pl_company_name'] : '');
			} else {
				$data['pl_company_name'] = '';
			}
			if (isset($this->request->post['company_region'])) {
				$data['company_region'] = $this->request->post['company_region'];
			} elseif (!empty($customer_info)) {
				$data['company_region'] = (!empty($customer_info['company_region']) ? $customer_info['company_region'] : '');
			} else {
				$data['company_region'] = '';
			}
			if (isset($this->request->post['company_address'])) {
				$data['company_address'] = $this->request->post['company_address'];
			} elseif (!empty($customer_info)) {
				$data['company_address'] = (!empty($customer_info['company_address']) ? $customer_info['company_address'] : '');
			} else {
				$data['company_address'] = '';
			}
			if (isset($this->request->post['company_zip'])) {
				$data['company_zip'] = $this->request->post['company_zip'];
			} elseif (!empty($customer_info)) {
				$data['company_zip'] = (!empty($customer_info['company_zip']) ? $customer_info['company_zip'] : '');
			} else {
				$data['company_zip'] = '';
			}
			if (isset($this->request->post['invoice_banks'])) {
				$data['invoice_banks'] = $this->request->post['invoice_banks'];
			} elseif (!empty($customer_info)) {
				$data['invoice_banks'] = json_decode($customer_info['invoice_banks'], true);
			} else {
				$data['invoice_banks'] = '';
			}

			if (isset($this->request->post['firstname'])) {
				$data['firstname'] = $this->request->post['firstname'];
			} elseif (!empty($customer_info)) {
				$data['firstname'] = (!empty($customer_info['firstname']) ? $customer_info['firstname'] : '');
			} else {
				$data['firstname'] = $this->config->get('firstname');
			}

			if (isset($this->request->post['lastname'])) {
				$data['lastname'] = $this->request->post['lastname'];
			} elseif (!empty($customer_info)) {
				$data['lastname'] = (!empty($customer_info['lastname']) ? $customer_info['lastname'] : '');
			} else {
				$data['lastname'] = $this->config->get('lastname');
			}

			if (isset($this->request->post['telephone'])) {
				$data['telephone'] = $this->request->post['telephone'];
			} elseif (!empty($customer_info)) {
				$data['telephone'] = (!empty($customer_info['telephone']) ? $customer_info['telephone'] : '');
			} else {
				$data['telephone'] = $this->config->get('telephone');
			}

			if (isset($this->request->post['email'])) {
				$data['email'] = $this->request->post['email'];
			} elseif (!empty($customer_info)) {
				$data['email'] = (!empty($customer_info['email']) ? $customer_info['email'] : '');
			} else {
				$data['email'] = $this->config->get('email');
			}

			if (isset($this->request->post['password'])) {
				$data['password'] = $this->request->post['password'];
			} elseif (!empty($customer_info)) {
				$data['password'] = (!empty($customer_info['password']) ? $customer_info['password'] : '');
			} else {
				$data['password'] = '';
			}

			if (isset($this->request->post['confirm'])) {
				$data['confirm'] = $this->request->post['confirm'];
			} elseif (!empty($customer_info)) {
				$data['confirm'] = (!empty($customer_info['confirm']) ? $customer_info['confirm'] : '');
			} else {
				$data['confirm'] = '';
			}

			// DEFAULT INFO
			if (isset($this->request->post['dsc_status'])) {
				$data['dsc_status'] = $this->request->post['dsc_status'];
			} elseif (!empty($customer_info)) {
				$data['dsc_status'] = (!empty($customer_info['dsc_status']) ? $customer_info['dsc_status'] : 0);
			} else {
				$data['dsc_status'] = $this->config->get('dsc_status');
			}

			if (isset($this->request->post['dsc_firstname'])) {
				$data['dsc_firstname'] = $this->request->post['dsc_firstname'];
			} elseif (!empty($customer_info)) {
				$data['dsc_firstname'] = (!empty($customer_info['dsc_firstname']) ? $customer_info['dsc_firstname'] : $customer_info['firstname']);
			} else {
				$data['dsc_firstname'] = $this->config->get('dsc_firstname');
			}

			if (isset($this->request->post['dsc_lastname'])) {
				$data['dsc_lastname'] = $this->request->post['dsc_lastname'];
			} elseif (!empty($customer_info)) {
				$data['dsc_lastname'] = (!empty($customer_info['dsc_lastname']) ? $customer_info['dsc_lastname'] : $customer_info['lastname']);
			} else {
				$data['dsc_lastname'] = $this->config->get('dsc_lastname');
			}

			if (isset($this->request->post['dsc_telephone'])) {
				$data['dsc_telephone'] = $this->request->post['dsc_telephone'];
			} elseif (!empty($customer_info)) {
				$data['dsc_telephone'] = (!empty($customer_info['dsc_telephone']) ? $customer_info['dsc_telephone'] : $customer_info['telephone']);
			} else {
				$data['dsc_telephone'] = $this->config->get('dsc_telephone');
			}

			if (isset($this->request->post['dsc_payment_method'])) {
				$data['dsc_payment_method'] = $this->request->post['dsc_payment_method'];
			} elseif (!empty($customer_info)) {
				$data['dsc_payment_method'] = $customer_info['dsc_payment_method'];
			} else {
				$data['dsc_payment_method'] = $this->config->get('dsc_payment_method');
			}

			if (isset($this->request->post['dsc_shipping_method'])) {
				$data['dsc_shipping_method'] = $this->request->post['dsc_shipping_method'];
			} elseif (!empty($customer_info)) {
				$data['dsc_shipping_method'] = $customer_info['dsc_shipping_method'];
			} else {
				$data['dsc_shipping_method'] = $this->config->get('dsc_shipping_method');
			}

			if (isset($this->request->post['parcelLocker'])) {
				$data['parcelLocker'] = $this->request->post['parcelLocker'];
			} elseif (!empty($customer_info)) {
				$data['parcelLocker'] = $customer_info['dsc_parcelLocker'];
			} else {
				$data['parcelLocker'] = '';
			}

			if (isset($this->request->post['dsc_vat'])) {
				$data['dsc_vat'] = $this->request->post['dsc_vat'];
			} elseif (!empty($customer_info)) {
				$data['dsc_vat'] = (isset($customer_info['dsc_vat']) ? $customer_info['dsc_vat'] : 1);
			} else {
				$data['dsc_vat'] = 1;
			}

			if (isset($this->request->post['dsc_faktyre'])) {
				$data['dsc_faktyre'] = $this->request->post['dsc_faktyre'];
			} elseif (!empty($customer_info)) {
				$data['dsc_faktyre'] = (isset($customer_info['dsc_faktyre']) ? $customer_info['dsc_faktyre'] : 0);
			} else {
				$data['dsc_faktyre'] = 0;
			}
			if (isset($this->request->post['dsc_privat_faktyre'])) {
				$data['dsc_privat_faktyre'] = $this->request->post['dsc_privat_faktyre'];
			} elseif (!empty($customer_info)) {
				$data['dsc_privat_faktyre'] = (isset($customer_info['dsc_privat_faktyre']) ? $customer_info['dsc_privat_faktyre'] : 0);
			} else {
				$data['dsc_privat_faktyre'] = 0;
			}
			if (isset($this->request->post['dsc_nip'])) {
				$data['dsc_nip'] = $this->request->post['dsc_nip'];
			} elseif (!empty($customer_info)) {
				$data['dsc_nip'] = (!empty($customer_info['dsc_nip']) ? $customer_info['dsc_nip'] : (!empty($customer_info['company_nip']) ? $customer_info['company_nip'] : ''));
			} else {
				$data['dsc_nip'] = '';
			}
			if (isset($this->request->post['dsc_vatcode'])) {
				$data['dsc_vatcode'] = $this->request->post['dsc_vatcode'];
			} elseif (!empty($customer_info)) {
				$data['dsc_vatcode'] = (!empty($customer_info['dsc_vatcode']) ? $customer_info['dsc_vatcode'] : (!empty($customer_info['company_vatcode']) ? $customer_info['company_vatcode'] : ''));
			} else {
				$data['dsc_vatcode'] = '';
			}

			if (isset($this->request->post['dsc_currency'])) {
				$data['dsc_currency'] = $this->request->post['dsc_currency'];
			} elseif (!empty($customer_info)) {
				$data['dsc_currency'] = $customer_info['dsc_currency'];
			} else {
				$data['dsc_currency'] = '';
			}
			$this->load->model('localisation/currency');
			$data['currencies'] = array();
			$results_currency = $this->model_localisation_currency->getCurrencies();
			foreach ($results_currency as $result_currency) {
				if ($result_currency['status']) {
					$data['currencies'][] = array(
						'currency_id'  => $result_currency['currency_id'],
						'title'        => $result_currency['title'],
						'code'         => $result_currency['code'],
						'symbol_left'  => $result_currency['symbol_left'],
						'symbol_right' => $result_currency['symbol_right']
					);
				}
			}
			rsort($data['currencies']);

			if (isset($this->request->post['dsc_language'])) {
				$data['dsc_language'] = $this->request->post['dsc_language'];
			} elseif (!empty($customer_info)) {
				$data['dsc_language'] = ( !empty($customer_info['dsc_language']) ? $customer_info['dsc_language'] : $this->session->data['language'] );
			} else {
				$data['dsc_language'] = '';
			}
			if (isset($this->request->post['language_id'])) {
				$data['language_id'] = $this->request->post['language_id'];
			} elseif (!empty($customer_info)) {
				$data['language_id'] = ( !empty($customer_info['language_id']) ? $customer_info['language_id'] : '5' );
			} else {
				$data['language_id'] = '5';
			}
			$this->load->model('localisation/language');
			$data['languages'] = array();
			$results_language = $this->model_localisation_language->getLanguages();
			foreach ($results_language as $result_language) {
				if ($result_language['status']) {
					$data['languages'][] = array(
						'language_id'  => $result_language['language_id'],
						'name'	       => $result_language['name'],
						'code'         => $result_language['code'],
					);
				}
			}
			rsort($data['languages']);

			if (isset($this->request->post['parcelAddressLocker'])) {
				$data['parcelAddressLocker'] = html_entity_decode($this->request->post['parcelAddressLocker'], ENT_QUOTES, 'UTF-8');
				$data['parcelAddressValLocker'] = htmlentities($this->request->post['parcelAddressLocker']);
			} elseif (!empty($customer_info)) {
				$data['parcelAddressLocker'] = html_entity_decode($customer_info['dsc_parcelAddressLocker'], ENT_QUOTES, 'UTF-8');
				$data['parcelAddressValLocker'] = htmlentities($customer_info['dsc_parcelAddressLocker']);
			} else {
				$data['parcelAddressLocker'] = '';
			}

			if (isset($this->request->post['dsc_postcode'])) {
				$data['dsc_postcode'] = $this->request->post['dsc_postcode'];
			} elseif (!empty($customer_info)) {
				$data['dsc_postcode'] = (!empty($customer_info['dsc_postcode']) ? $customer_info['dsc_postcode'] : '');
			} else {
				$data['dsc_postcode'] = '';
			}

			if (isset($this->request->post['dsc_country'])) {
				$data['dsc_country'] = $this->request->post['dsc_country'];
			} elseif (!empty($customer_info)) {
				$data['dsc_country'] = $customer_info['dsc_country'];
			} else {
				$data['dsc_country'] = $this->config->get('dsc_country');
			}

			if (isset($this->request->post['dsc_city'])) {
				$data['dsc_city'] = $this->request->post['dsc_city'];
			} elseif (!empty($customer_info)) {
				$data['dsc_city'] = $customer_info['dsc_city'];
			} else {
				$data['dsc_city'] = $this->config->get('dsc_city');
			}

			if (isset($this->request->post['dsc_address_1'])) {
				$data['dsc_address_1'] = $this->request->post['dsc_address_1'];
			} elseif (!empty($customer_info)) {
				$data['dsc_address_1'] = $customer_info['dsc_address_1'];
			} else {
				$data['dsc_address_1'] = $this->config->get('dsc_address_1');
			}

			if (isset($this->request->post['dsc_opc_not_call_me'])) {
				$data['dsc_opc_not_call_me'] = $this->request->post['dsc_opc_not_call_me'];
			} elseif (!empty($customer_info)) {
				$data['dsc_opc_not_call_me'] = $customer_info['dsc_opc_not_call_me'];
			} else {
				$data['dsc_opc_not_call_me'] = $this->config->get('dsc_opc_not_call_me');
			}

			$data['payment_address'] = array('country_id' => $this->config->get('config_country_id'), 'zone_id' => $this->config->get('config_zone_id'), 'firstname' => '', 'lastname' => '', 'company' => '', 'address_1' => '');

			if (isset($this->request->post['ms_code'])) {
				$data['ms_code'] = $this->request->post['ms_code'];
			} elseif (!empty($customer_info)) {
				$data['ms_code'] = (!empty($customer_info['customer_my_sklad']) ? $customer_info['customer_my_sklad'] : '');
			} else {
				$data['ms_code'] = '';
			}

			$total = 100;

			// Payment Methods
			$data['payment_methods'] = array();

			$this->load->model('setting/extension');

			$presults = $this->model_setting_extension->getExtensions('payment');

			foreach ($presults as $presult) {
				if ($this->config->get('payment_' . $presult['code'] . '_status')) {
					// $this->load->language('extension/payment/' . $result['code']);

					$this->load->model('extension/payment/' . $presult['code']);
	
					$pmethod = $this->{'model_extension_payment_' . $presult['code']}->getMethod(array('country_id' => $data['country_id']), 0);

					$method_data = array();

					/*$method_data = array(
						'code'       => $result['code'],
						'title'      => $this->language->get('text_title'),
						'terms'      => '',
						'sort_order' => $this->config->get('payment_cod_sort_order'),
						'geo_zone_id' => $this->config->get('payment_' . $result['code'] . '_geo_zone_id')
					);*/
					$method_data = $pmethod;

					$data['payment_methods'][$presult['code']] = $method_data;
				}
			}

			$sort_order = array();

			foreach ($data['payment_methods'] as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $data['payment_methods']);


			$data['shipping_address'] = array('country_id' => $this->config->get('config_country_id'), 'zone_id' => $this->config->get('config_zone_id'), 'firstname' => '', 'lastname' => '', 'company' => '', 'address_1' => '');

			$shipping_methods = array();

			$this->load->model('setting/extension');

			$shipping_results = $this->model_setting_extension->getExtensions('shipping');

			foreach ($shipping_results as $result) {
				if ($this->config->get('shipping_' . $result['code'] . '_status')) {
					$this->load->model('extension/shipping/' . $result['code']);
	
					$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote($data['shipping_address']);
	
					if($result['code'] == 'easyship'){
						foreach($quote['easyship0']['quote'] as $val_quote){
							$shipping_methods[] = array(
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
						$shipping_methods[] = array(
							'code'        => $result['code'],
							'title'       => $quote['title'],
							'quote'       => $quote['quote'],
							'sort_order'  => $quote['sort_order'],
							'error'       => $quote['error'],
							'geo_zone_id' => $this->config->get('shipping_' . $result['code'] . '_geo_zone_id')
						);
					}
				}
			}
	
			$data['shipping_methods'] = $shipping_methods;

			// $countries = $this->model_localisation_country->getCountries();
			$this->load->model('checkout/onepcheckout');
			$countries = $this->model_checkout_onepcheckout->getCountryDeliveries();
			$countrys = array();
			foreach($countries as $country) {
				$zone = $this->model_localisation_zone->getZonesZoneByCountryId($country['country_id']);
				$countrys[] = array(
					'country_id'  => $country['country_id'],
					'country_delivery_id'  => $country['country_delivery_id'],
					'name'		  => $country['name'],
					'geo_zone_id' => $zone['geo_zone_id']
				);
			}
			$data['countries'] = $countrys;

			$this->response->setOutput($this->load->view('account/edit_dsc_modal', $data));
		} else {
		  $this->response->redirect($this->url->link('error/not_found', '', true));
		}
  	}

	protected function validate() {
		// VALIDATE MAIN
		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 255)) {
			$this->error['firstname'] = $this->language->get('error_firstname');
		}

		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 255)) {
			$this->error['lastname'] = $this->language->get('error_lastname');
		}

		if ((utf8_strlen($this->request->post['email']) < 3) || (utf8_strlen($this->request->post['email']) > 32)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		//if (!empty($this->request->post['password']) && ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40))) {
		//	$this->error['password'] = $this->language->get('error_password');

		//	if ($this->request->post['confirm'] != $this->request->post['password']) {
		//		$this->error['confirm'] = $this->language->get('error_confirm');
		//	}
		//}
		
		/*if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
			$this->error['password'] = $this->language->get('error_password');
		}
		if ($this->request->post['confirm'] != $this->request->post['password']) {
			$this->error['confirm'] = $this->language->get('error_confirm');
		}*/
		if (!empty($this->request->post['password'])) {
			if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
			//	$this->error['password'] = $this->language->get('error_password');
			}

			if ($this->request->post['password'] != $this->request->post['confirm']) {
				$this->error['confirm'] = $this->language->get('error_confirm');
			}
		}

		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('telephone');
		}

		// VALIDATE DEFAULT
		if ($this->request->post['dsc_status']) {
			if ((utf8_strlen(trim($this->request->post['dsc_firstname'])) < 1) || (utf8_strlen(trim($this->request->post['dsc_firstname'])) > 255)) {
				$this->error['firstname'] = $this->language->get('error_firstname');
			}

			if ((utf8_strlen(trim($this->request->post['dsc_lastname'])) < 1) || (utf8_strlen(trim($this->request->post['dsc_lastname'])) > 255)) {
				$this->error['lastname'] = $this->language->get('error_lastname');
			}

			if ((utf8_strlen($this->request->post['dsc_telephone']) < 3) || (utf8_strlen($this->request->post['dsc_telephone']) > 32)) {
				$this->error['telephone'] = $this->language->get('error_telephone');
			}
		}

		return !$this->error;
	}

}
