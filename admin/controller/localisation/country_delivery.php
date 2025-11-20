<?php
class ControllerLocalisationCountryDelivery extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('localisation/country_delivery');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/country_delivery');

		$this->getList();
	}

	public function add() {
		$this->load->language('localisation/country_delivery');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/country_delivery');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_country_delivery->addCountryDelivery($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('localisation/country_delivery', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('localisation/country_delivery');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/country_delivery');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_country_delivery->editCountryDelivery($this->request->get['country_delivery_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('localisation/country_delivery', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('localisation/country_delivery');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/country_delivery');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $country_delivery_id) {
				$this->model_localisation_country_delivery->deleteCountryDelivery($country_delivery_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('localisation/country_delivery', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('localisation/country_delivery', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('localisation/country_delivery/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('localisation/country_delivery/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['countries'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$country_total = $this->model_localisation_country_delivery->getTotalCountryDeliveries();

		$results = $this->model_localisation_country_delivery->getCountryDeliveries($filter_data);

		foreach ($results as $result) {
			//максимальна вартість по доставці для країни в списку
			$costs=[];
			$free_deliveries=[];
			foreach(json_decode($result['shipping_methods'], true) as $shipping_method) {
				$costs[]=$shipping_method['cost'];
				$free_deliveries[]=$shipping_method['free_delivaery'];
			}
			$cost=0;
			if(!empty($costs)) {
				$cost = max($costs);
			}
			$free_delivery=0;
			if(!empty($free_deliveries)) {
				$free_delivery = max($free_deliveries);
			}

			$data['country_deliveries'][] = array(
				'country_delivery_id' 	=> $result['country_delivery_id'],
				'country_id' 			=> $result['country_id'],
				'country_name'			=> $result['country_name'],
				'name'       			=> $result['name'],
				'cost'					=> $cost,
				'free_delivery'					=> $free_delivery,
				'sort_order'			=> $result['sort_order'],
				'status'					=> $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       			=> $this->url->link('localisation/country_delivery/edit', 'user_token=' . $this->session->data['user_token'] . '&country_delivery_id=' . $result['country_delivery_id'] . $url, true)
			);
		}


		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('localisation/country_delivery', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $country_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('localisation/country_delivery', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($country_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($country_total - $this->config->get('config_limit_admin'))) ? $country_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $country_total, ceil($country_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('localisation/country_delivery_list', $data));
	}

	protected function getForm() {
		$this->load->language('localisation/country_delivery');
		$this->load->model('localisation/language');
		$this->load->model('localisation/country');
		$this->load->model('localisation/country_delivery');
		$this->load->model('setting/extension');

		$data['text_form'] = !isset($this->request->get['country_delivery_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = [];
		}

		if (isset($this->error['country'])) {
			$data['error_country'] = $this->error['country'];
		} else {
			$data['error_country'] = '';
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = [];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('localisation/country_delivery', 'user_token=' . $this->session->data['user_token'] . $url, true)
		];

		if (!isset($this->request->get['country_delivery_id'])) {
			$data['action'] = $this->url->link('localisation/country_delivery/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('localisation/country_delivery/edit', 'user_token=' . $this->session->data['user_token'] . '&country_delivery_id=' . $this->request->get['country_delivery_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('localisation/country_delivery', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['country_delivery_id']) && $this->request->server['REQUEST_METHOD'] != 'POST') {
			$country_delivery_info = $this->model_localisation_country_delivery->getCountryDelivery($this->request->get['country_delivery_id']);
			$country_delivery_description = $this->model_localisation_country_delivery->getCountryDeliveryDescriptions($this->request->get['country_delivery_id']);
		} else {
			$country_delivery_info = [];
			$country_delivery_description = [];
		}

		if (isset($this->request->post['country_id'])) {
			$data['country_id'] = $this->request->post['country_id'];
		} elseif (!empty($country_delivery_info)) {
			$data['country_id'] = $country_delivery_info['country_id'];
		} else {
			$data['country_id'] = '';
		}

		if (isset($this->request->post['cost'])) {
			$data['cost'] = $this->request->post['cost'];
		} elseif (!empty($country_delivery_info)) {
			$data['cost'] = $country_delivery_info['cost'];
		} else {
			$data['cost'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($country_delivery_info)) {
			$data['status'] = $country_delivery_info['status'];
		} else {
			$data['status'] = '1';
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($country_delivery_info)) {
			$data['sort_order'] = $country_delivery_info['sort_order'];
		} else {
			$data['sort_order'] = '0';
		}

		if (isset($this->request->post['shipping_methods'])) {
			$data['shipping_methods_selected'] = $this->request->post['shipping_methods'];
		} elseif (!empty($country_delivery_info['shipping_methods'])) {
			$data['shipping_methods_selected'] = json_decode($country_delivery_info['shipping_methods'], true);
		} else {
			$data['shipping_methods_selected'] = [];
		}

		if (isset($this->request->post['payment_methods'])) {
			$data['payment_methods_selected'] = $this->request->post['payment_methods'];
		} elseif (!empty($country_delivery_info['payment_methods'])) {
			$data['payment_methods_selected'] = json_decode($country_delivery_info['payment_methods'], true);
		} else {
			$data['payment_methods_selected'] = [];
		}

		if (isset($this->request->post['country_delivery_description'])) {
			$data['country_delivery_description'] = $this->request->post['country_delivery_description'];
		} else {
			$data['country_delivery_description'] = $country_delivery_description;
		}

		$data['languages'] = $this->model_localisation_language->getLanguages();
		$data['countries'] = $this->model_localisation_country->getCountries();

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('user/api');

		$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

		$session = new Session($this->config->get('session_engine'), $this->registry);
		$session->start();

		$this->model_user_api->deleteApiSessionBySessionId($session->getId());
		$this->model_user_api->addApiSession($api_info['api_id'], $session->getId(), $this->request->server['REMOTE_ADDR']);

		$session->data['api_id'] = $api_info['api_id'];
		$api_token = $session->getId();

		$shipping_methods = $this->getShippingMethodsFromFrontend($api_token);

		$data['shipping_methods'] = $shipping_methods;


		$installedPayments = $this->model_setting_extension->getInstalled('payment');

		$data['payment_methods'] = array();
		foreach ($installedPayments as $extension) {
			$status = $this->config->get('payment_' . $extension . '_status');
			if ($status) {
				$this->load->language('extension/payment/' . $extension);
				$data['payment_methods'][] = array(
					'code' => $extension,
					'name' => $this->language->get('heading_title'),
				);
			}
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('localisation/country_delivery_form', $data));
	}

	protected function getShippingMethodsFromFrontend($api_token) {
		$curl = curl_init();

		$url = HTTP_CATALOG . 'index.php?route=api/opc_api/shippingMethods&api_token=' . $api_token;

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			)
		));

		$response = curl_exec($curl);
		if (curl_errno($curl)) {
			curl_close($curl);
		return array();
	}

	curl_close($curl);

	$json = json_decode($response, true);

	if (isset($json['smethods'])) {
		return $json['smethods'];
	}

	return array(); // пусто или ошибка
}



	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'localisation/country_delivery')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (empty($this->request->post['country_id'])) {
			$this->error['country'] = $this->language->get('error_country');
		}

		foreach ($this->request->post['country_delivery_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'localisation/country_delivery')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country_delivery->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}