<?php
class ControllerCommonLoginModal extends Controller {
	private $error = array();

	public function index() {
		if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$this->load->language('account/register');
			$this->load->language('account/login');
			$this->load->language('common/login_modal');

			$data['text_login'] = $this->language->get('text_login');
			$data['entry_email'] = $this->language->get('entry_email');
			$data['entry_password'] = $this->language->get('entry_password');
			$data['text_register'] = $this->language->get('text_register');
			$data['text_forgotten'] = $this->language->get('text_forgotten');
			$data['button_login'] = $this->language->get('button_login');

			$data['register'] = $this->url->link('account/register', '', true);
			$data['forgotten'] = $this->url->link('account/forgotten', '', true);

			$this->load->model('localisation/country');
			$this->load->model('localisation/zone');
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

			$this->response->setOutput($this->load->view('common/login_modal', $data));
		} else {
		  $this->response->redirect($this->url->link('error/not_found', '', true));
		}
  	}

  	public function register() {
		if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$this->load->language('account/register');
			$this->load->language('account/login');
			$this->load->language('common/login_modal');

			$data['text_login'] = $this->language->get('text_login');
			$data['entry_email'] = $this->language->get('entry_email');
			$data['entry_password'] = $this->language->get('entry_password');
			$data['text_register'] = $this->language->get('text_register');
			$data['text_forgotten'] = $this->language->get('text_forgotten');
			$data['button_login'] = $this->language->get('button_login');

			$data['register'] = $this->url->link('account/register', '', true);
			$data['forgotten'] = $this->url->link('account/forgotten', '', true);
			
	        $this->load->model('localisation/country');
	        $this->load->model('localisation/zone');
	        // $countries = $this->model_localisation_country->getCountries();
			$this->load->model('checkout/onepcheckout');
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
	        $data['countries'] = $countrys;

			$this->load->model('account/customer_group');

			$data['customer_groups'] = $this->model_account_customer_group->getCustomerGroups();

			$this->response->setOutput($this->load->view('common/register_modal', $data));
		} else {
		  $this->response->redirect($this->url->link('error/not_found', '', true));
		}
  	}

	public function login_validate($data = array()) {
		if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$this->load->language('checkout/checkout');

			$json = array();
			$this->load->model('account/customer');

			if ($this->customer->isLogged()) {
				$json['islogged'] = true;
			}else if(isset($this->request->post)) {
				if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
					$json['error'] = $this->language->get('error_login');
				}
				$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
				if ($customer_info && !$customer_info['status']) {
					$json['error'] = $this->language->get('error_approved');
				}
			} else {
				$json['error'] = $this->language->get('error_warning');
			}

			if(!$json) {
				$json['success'] = true;
				unset($this->session->data['guest']);
				$this->load->model('account/address');

				if ($this->config->get('config_tax_customer') == 'payment') {
					$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
				}

				if ($this->config->get('config_tax_customer') == 'shipping') {
					$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
				}

				$this->load->model('account/activity');

				$activity_data = array(
					'customer_id' => $this->customer->getId(),
					'name'        => $this->customer->getFirstName() . ' ' . $this->customer->getLastName()
				);

				$this->load->model('account/customer');
				$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

				if ($customer_info['dsc_status'] == 1) {
					if (isset($this->session->data['dsc_language'])) {
						if ($this->session->data['dsc_language'] == 'en-gb') {
							$json['language_id'] = 2;
						} else {
							$lang_id = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE code = '" . $this->session->data['dsc_language'] . "'")->row;
							$json['language_id'] = $lang_id['language_id'];
						}
					}
				}

				$this->model_account_activity->addActivity('login', $activity_data);
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		} else {
		  $this->response->redirect($this->url->link('error/not_found', '', true));
		}
	}

	public function register_validate() {
		if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

			$this->load->language('account/register');
			$this->load->model('account/customer');

			$json = array();

			if(isset($this->request->post['customer_type']) && ($this->request->post['customer_type'] == 3)){
				if ($this->model_account_customer->getTotalCustomersByNIP($this->request->post['company_nip'])) {
					// $json['error'] = $this->language->get('error_exists_nip');
				}

				if ((utf8_strlen(trim($this->request->post['pl_company_name'])) < 1) || (utf8_strlen(trim($this->request->post['pl_company_name'])) > 100)) {
					$json['error'] = $this->language->get('error_company_name');
				}
			} else {

				if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
					$json['error'] = $this->language->get('error_firstname');
				}

				if(isset($this->request->post['customer_type']) && ($this->request->post['customer_type'] == 1)){
					if ((utf8_strlen(trim($this->request->post['company_name'])) < 1) || (utf8_strlen(trim($this->request->post['company_name'])) > 100)) {
					//	$json['error'] = $this->language->get('error_company_name');
					}

					if ((utf8_strlen(trim($this->request->post['company_nip'])) < 1) || (utf8_strlen(trim($this->request->post['company_nip'])) > 100)) {
						if ((utf8_strlen(trim($this->request->post['company_vatcode'])) < 1) || (utf8_strlen(trim($this->request->post['company_vatcode'])) > 100)) {
							$json['error'] = $this->language->get('error_company_nip');
						}
					}

					if ((utf8_strlen(trim($this->request->post['company_vatcode'])) < 1) || (utf8_strlen(trim($this->request->post['company_vatcode'])) > 100)) {
						if ((utf8_strlen(trim($this->request->post['company_nip'])) < 1) || (utf8_strlen(trim($this->request->post['company_nip'])) > 100)) {
							$json['error'] = $this->language->get('error_company_vatcode');
						}
					}
				}

				if(!isset($this->request->post['country_id']) || (isset($this->request->post['country_id']) && ($this->request->post['country_id'] == 0))){
					$json['error'] = $this->language->get('error_country_id');
				}

				if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
					$json['error'] = $this->language->get('error_email');
				}

				if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
					$json['error'] = $this->language->get('error_exists');
				}

				if ((utf8_strlen($this->request->post['telephone']) < 7) || (utf8_strlen($this->request->post['telephone']) > 32)) {
					$json['error'] = $this->language->get('error_telephone');
				}

				if ($this->model_account_customer->getTotalCustomersByTelephone($this->request->post['telephone'])) {
					$json['error'] = $this->language->get('error_exists_telephone');
				}

				if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
				//	$json['error'] = $this->language->get('error_password');
				}
			}

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');
				if ($captcha) {
					$json['captcha'] = $captcha;
				}
			}

			if (!$json){
				$this->load->language('common/login_modal');

				$customer_data = array(
					'customer_type'		=> (isset($this->request->post['customer_type'])) ? $this->request->post['customer_type'] : '',
					'company_name'		=> (isset($this->request->post['company_name'])) ? $this->request->post['company_name'] : $this->request->post['pl_company_name'],
					'pl_company_name'	=> (isset($this->request->post['pl_company_name'])) ? $this->request->post['pl_company_name'] : $this->request->post['company_name'],
					'company_nip'		=> (isset($this->request->post['company_nip'])) ? $this->request->post['company_nip'] : '',
					'company_vatcode'	=> (isset($this->request->post['company_nip'])) ? $this->request->post['company_vatcode'] : '',
					'country_id'		=> (isset($this->request->post['country_id'])) ? $this->request->post['country_id'] : '',
					'email'				=> (isset($this->request->post['email'])) ? $this->request->post['email'] : '',
					'telephone'			=> (isset($this->request->post['telephone'])) ? $this->request->post['telephone'] : '',
					'firstname'			=> (isset($this->request->post['firstname'])) ? $this->request->post['firstname'] : '',
					'lastname'			=> (isset($this->request->post['lastname'])) ? $this->request->post['lastname'] : '',
					'company'			=> (isset($this->request->post['company'])) ? $this->request->post['company'] : '',
					'address_1'			=> (isset($this->request->post['address_1'])) ? $this->request->post['address_1'] : '',
					'address_2'			=> (isset($this->request->post['address_2'])) ? $this->request->post['address_2'] : '',
					'city'				=> (isset($this->request->post['city'])) ? $this->request->post['city'] : '',
					'postcode'			=> (isset($this->request->post['postcode'])) ? $this->request->post['postcode'] : '',
					'password'			=> (isset($this->request->post['password'])) ? $this->request->post['password'] : '',
				);

				$customer_id = $this->model_account_customer->addCustomer($customer_data);

				// Clear any previous login attempts for unregistered accounts.
				$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);

				$this->customer->login($this->request->post['email'], $this->request->post['password']);

				unset($this->session->data['guest']);

				$client_info = $this->model_account_customer->getCustomer($customer_id);

				$json['success'] = $this->language->get('text_success_register');
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));

		} else {
		  $this->response->redirect($this->url->link('error/not_found', '', true));
		}
	}

	public function validate_register() {
		if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

			$this->load->language('account/register');
			$this->load->model('account/customer');
			$this->load->language('common/login_modal');
			$json = array();

			if(isset($this->request->post['customer_type']) && ($this->request->post['customer_type'] == 3)){
				if ($this->model_account_customer->getTotalCustomersByNIP($this->request->post['company_nip'])) {
					$json['error'] = $this->language->get('error_exists_nip');
				}

				if ((utf8_strlen(trim($this->request->post['pl_company_name'])) < 1) || (utf8_strlen(trim($this->request->post['pl_company_name'])) > 100)) {
					$json['error'] = $this->language->get('error_company_name');
				}
			} else {

				if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
					$json['error'] = $this->language->get('error_firstname');
				}

				if(isset($this->request->post['customer_type']) && ($this->request->post['customer_type'] == 1)){
					if ((utf8_strlen(trim($this->request->post['company_name'])) < 1) || (utf8_strlen(trim($this->request->post['company_name'])) > 100)) {
				//		$json['error'] = $this->language->get('error_company_name');
					}

					if ((utf8_strlen(trim($this->request->post['company_nip'])) < 1) || (utf8_strlen(trim($this->request->post['company_nip'])) > 100)) {
						// $json['error'] = $this->language->get('error_company_nip');
					}
				}

				if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
					$json['error'] = $this->language->get('error_email');
				}

				if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
					$json['error'] = $this->language->get('error_exists');
				}

				if ((utf8_strlen($this->request->post['telephone']) < 7) || (utf8_strlen($this->request->post['telephone']) > 32)) {
					$json['error'] = $this->language->get('error_telephone');
				}

				if ($this->model_account_customer->getTotalCustomersByTelephone($this->request->post['telephone'])) {
					$json['error'] = $this->language->get('error_exists_telephone');
				}

				if ((utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
				//	$json['error'] = $this->language->get('error_password');
				}
			}

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');
				if ($captcha) {
					$json['captcha'] = $captcha;
				}
			}

			if (!$json){
				$this->load->language('common/login_modal');

				/*$customer_data = array(
					'customer_type'		=> (isset($this->request->post['customer_type'])) ? $this->request->post['customer_type'] : '',
					'company_name'		=> (isset($this->request->post['company_name'])) ? $this->request->post['company_name'] : '',
					'company_nip'		=> (isset($this->request->post['company_nip'])) ? $this->request->post['company_nip'] : '',
					'company_vatcode'	=> (isset($this->request->post['company_vatcode'])) ? $this->request->post['company_vatcode'] : '',
					'country_id'		=> (isset($this->request->post['country_id'])) ? $this->request->post['country_id'] : '0',
					'email'				=> (isset($this->request->post['email'])) ? $this->request->post['email'] : '',
					'telephone'			=> (isset($this->request->post['telephone'])) ? $this->request->post['telephone'] : '',
					'firstname'			=> (isset($this->request->post['firstname'])) ? $this->request->post['firstname'] : '',
					'lastname'			=> (isset($this->request->post['lastname'])) ? $this->request->post['lastname'] : '',
					'company'			=> (isset($this->request->post['company'])) ? $this->request->post['company'] : '',
					'address_1'			=> (isset($this->request->post['address_1'])) ? $this->request->post['address_1'] : '',
					'address_2'			=> (isset($this->request->post['address_2'])) ? $this->request->post['address_2'] : '',
					'city'				=> (isset($this->request->post['city'])) ? $this->request->post['city'] : '',
					'postcode'			=> (isset($this->request->post['postcode'])) ? $this->request->post['postcode'] : '',
					'password'			=> (isset($this->request->post['password'])) ? $this->request->post['password'] : '',
				);*/
				if (!isset($this->request->post['country_id'])) {
					$this->request->post['country_id'] = 0;
				}

				$customer_data = $this->request->post;

				$customer_id = $this->model_account_customer->addCustomer($customer_data);

				$client_info = $this->model_account_customer->getCustomer($customer_id);

				$json['client_info'] = $client_info['firstname'] . ' ' . $client_info['lastname'] . ' ' . $client_info['telephone'];

				$json['client_id'] = $client_info['customer_id'];

				$json['success'] = $this->language->get('text_success_register');
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));

		} else {
		  $this->response->redirect($this->url->link('error/not_found', '', true));
		}
	}

	public function recover_password() {
		if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

			$this->load->language('account/forgotten');
			$this->load->model('account/customer');

			$json = array();

			if (!isset($this->request->post['email']) || (empty($this->request->post['email']))) {
				$json['error'] = $this->language->get('error_email');
			} elseif (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
				$json['error'] = $this->language->get('error_email');
			}

			if (!$json){
				// Check if customer has been approved.
				$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
				if ($customer_info && !$customer_info['status']) {
					$json['error'] = $this->language->get('error_approved');
				}
			}

			if (!$json){
				$this->model_account_customer->editCode($this->request->post['email'], token(40));

				$json['success'] = $this->language->get('text_success');
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));

		} else {
		  $this->response->redirect($this->url->link('error/not_found', '', true));
		}
	}
}
?>
