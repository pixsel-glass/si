<?php
class ControllerApiCustomer extends Controller {
	public function index() {
		$this->load->language('api/customer');

		// Delete past customer in case there is an error
		unset($this->session->data['customer']);

		$json = array();

		if (!isset($this->session->data['api_id'])) {
			$json['error']['warning'] = $this->language->get('error_permission');
		} else {
			// Add keys for missing post vars
			$keys = array(
				'customer_id',
				'customer_group_id',
				'firstname',
				'lastname',
				'email',
				'telephone',
			);

			foreach ($keys as $key) {
				if (!isset($this->request->post[$key])) {
					$this->request->post[$key] = '';
				}
			}

			// Customer
			if ($this->request->post['customer_id']) {
				$this->load->model('account/customer');

				$customer_info = $this->model_account_customer->getCustomer($this->request->post['customer_id']);

				// if (!$customer_info || !$this->customer->login($customer_info['email'], '', true)) {
				//	$json['error']['warning'] = $this->language->get('error_customer');
				// }
			}

			if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
				$json['error']['firstname'] = $this->language->get('error_firstname');
			}

			if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
				$json['error']['lastname'] = $this->language->get('error_lastname');
			}

			if ((utf8_strlen($this->request->post['email']) > 96) || (!filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL))) {
				$json['error']['email'] = $this->language->get('error_email');
			}

			if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
				$json['error']['telephone'] = $this->language->get('error_telephone');
			}

			// Customer Group
			if (is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
				$customer_group_id = $this->request->post['customer_group_id'];
			} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}

			// Custom field validation
			$this->load->model('account/custom_field');

			$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

			foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'account') { 
					if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
						$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
					} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
						$json['error']['custom_field' . $custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
					}
				}
			}

			if (!$json) {
				$this->session->data['customer'] = array(
					'customer_id'       => $this->request->post['customer_id'],
					'customer_group_id' => $customer_group_id,
					'firstname'         => $this->request->post['firstname'],
					'lastname'          => $this->request->post['lastname'],
					'email'             => $this->request->post['email'],
					'telephone'         => $this->request->post['telephone'],
					'custom_field'      => isset($this->request->post['custom_field']) ? $this->request->post['custom_field'] : array()
				);

				$json['success'] = $this->language->get('text_success');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function add() {
		$this->load->language('api/customer');

		$this->load->model('account/customer');

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->get['marykey'])) {
			$this->request->post['api'] = $this->request->get['marykey'];
			
			$this->model_account_customer->addCustomer($this->request->post);

			$json['success'] = $this->language->get('text_success');

			$this->response->setOutput(json_encode($json));
		}

		$json['error'] = $this->language->get('error_permission');

		$this->response->setOutput(json_encode($json));
	}

	public function edit() {
		$this->load->language('api/customer');

		$this->load->model('account/customer');

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->get['marykey'])) {
			$this->request->post['api'] = $this->request->get['marykey'];

			$this->model_account_customer->editCustomer($this->request->get['customer_id'], $this->request->post);
			
			$json['success'] = $this->language->get('text_success');

			$this->response->setOutput(json_encode($json));
		}

		$json['error'] = $this->language->get('error_permission');

		$this->response->setOutput(json_encode($json));
	}

	public function editDefault() {
		$this->load->language('api/customer');

		$this->load->model('account/customer');

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->get['marykey'])) {
			$this->request->post['api'] = $this->request->get['marykey'];

			if (isset($this->request->post['company_nip_inf'])) {
				$this->request->post['dsc_nip'] = $this->request->post['company_nip_inf'];
			}
			
			$this->model_account_customer->editDefaultSettingCustomer($this->request->get['customer_id'], $this->request->post);
			
			$json['success'] = $this->language->get('text_success');

			$this->response->setOutput(json_encode($json));
		}

		$json['error'] = $this->language->get('error_permission');

		$this->response->setOutput(json_encode($json));
	}

	public function editPassword() {
		$this->load->language('api/customer');

		$this->load->model('account/customer');

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->get['marykey'])) {
			$this->model_account_customer->editPasswordApi($this->request->post['email'], $this->request->post['password']);
			
			$json['success'] = $this->language->get('text_success');

			$this->response->setOutput(json_encode($json));
		}

		$json['error'] = $this->language->get('error_permission');

		$this->response->setOutput(json_encode($json));
	}

	public function delete() {
		$this->load->language('api/customer');

		$this->load->model('account/customer');

		if (isset($this->request->get['customer_id']) && isset($this->request->get['marykey'])) {
			$this->model_account_customer->deleteCustomer($this->request->get['customer_id']);
			
			$json['success'] = $this->language->get('text_success');

			$this->response->setOutput(json_encode($json));
		}

		$json['error'] = $this->language->get('error_permission');

		$this->response->setOutput(json_encode($json));
	}
}
