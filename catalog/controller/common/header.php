<?php

// *	@source		See SOURCE.txt for source and other copyright.

// *	@license	GNU General Public License version 3; see LICENSE.txt



class ControllerCommonHeader extends Controller {

	public function index() {

		$data['current_currency'] = $this->session->data['currency'];
		$data['current_language'] = $this->language->get('code');

		// setcookie('languagesw', null, -1, '/', $this->host);
		// setcookie('languagesw', $data['current_language'], time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
		/*if (!isset($_COOKIE['lm_language_id'])) {
			$this->load->model('localisation/language');

			$languages = $this->model_localisation_language->getLanguages();
			
			$language_code = $this->language->get('code');
			if ($language_code == 'ro') {
				$language_code = 'ro-ro';
			} elseif ($language_code == 'hu') {
				$language_code = 'hu-hu';
			} elseif ($language_code == 'en') {
				$language_code = 'en-gb';
			}

			// setcookie('language', $language_code, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);

			// $this->config->set('config_language', $language_code);
			// $this->session->data['language'] = $language_code;

			// $language_id = 0;

			foreach ($languages as $language) {
				if ($language['code'] == $language_code) {
					$language_id = $language['language_id'];
					// setcookie('lm_language_id', $language_id, time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
					// $this->config->set('config_language_id', $language_id);
					file_put_contents('./system/uploads/language_id_'.$this->session->getId().'.txt', $language_id);
					file_put_contents('./system/uploads/language_code_'.$this->session->getId().'.txt', $language_code);
					break;
				}
			}			
		}*/

		// Analytics
		$this->load->model('setting/extension');


		$data['analytics'] = array();


		if ($this->customer->isLogged()) {
			$this->load->model('account/customer');
			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

			if ($customer_info['dsc_status'] == 1) {
				if (isset($this->session->data['dsc_language_id']) && isset($this->session->data['dsc_language_code'])) {
					$data['dsc_language_id'] = $this->session->data['dsc_language_id'];
					$data['dsc_language_code'] = $this->session->data['dsc_language_code'];
					
					unset($this->session->data['dsc_language_id']);
					unset($this->session->data['dsc_language_code']);
				} else {
					$data['dsc_language_id'] = '';
					$data['dsc_language_code'] = '';
				}

				// $this->session->data['currency'] = $customer_info['dsc_currency'];
			} else {
				$data['dsc_language_id'] = '';
				$data['dsc_language_code'] = '';

				// $this->session->data['currency'] = 'PLN';
			}
		} else {
			$data['dsc_language_id'] = '';
			$data['dsc_language_code'] = '';

			// $this->session->data['currency'] = 'PLN';
		}

		if (isset($this->session->data['old_currency'])) {
			$this->session->data['currency'] = $this->session->data['old_currency'];
			unset($this->session->data['old_currency']);
		}

		// echo $this->session->data['language'];
		// echo $this->config->get('config_language_id');

		$analytics = $this->model_setting_extension->getExtensions('analytics');



		foreach ($analytics as $analytic) {

			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {

				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));

			}

		}



		if ($this->request->server['HTTPS']) {

			$server = $this->config->get('config_ssl');

		} else {

			$server = $this->config->get('config_url');

		}



		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {

			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');

		}



		$data['title'] = $this->document->getTitle();



		$data['base'] = $server;

		$data['description'] = $this->document->getDescription();

		$data['keywords'] = $this->document->getKeywords();

		$data['links'] = $this->document->getLinks();

		$data['robots'] = $this->document->getRobots();

		$data['styles'] = $this->document->getStyles();

		$data['scripts'] = $this->document->getScripts('header');

		$data['lang'] = $this->language->get('code');

		$data['direction'] = $this->language->get('direction');



		$data['name'] = $this->config->get('config_name');



		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {

			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');

		} else {

			$data['logo'] = '';

		}



		$this->load->language('common/header');





		$host = isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')) ? HTTPS_SERVER : HTTP_SERVER;

		if ($this->request->server['REQUEST_URI'] == '/') {

			$data['og_url'] = $this->url->link('common/home');

		} else {

			$data['og_url'] = $host . substr($this->request->server['REQUEST_URI'], 1, (strlen($this->request->server['REQUEST_URI'])-1));

		}



		$data['og_image'] = $this->document->getOgImage();







		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');
			$data['total_wishlist'] = $this->model_account_wishlist->getTotalWishlist();
		} else {
			$data['total_wishlist'] = isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0;
		}



		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));

		$data['firstname'] = ($this->customer->isLogged()) ? $this->customer->getFirstName() : '';


		$data['customer_group_name'] = '';
		$data['customer_bg'] = '';
		$data['type_customer'] = 0;

		if($this->customer->isLogged()){

			$data['type_customer'] = !empty($this->customer->getCustomerType()) ? $this->customer->getCustomerType() : 0;

			if($this->customer->getCustomerType() == 2){
				$data['customer_bg'] = '#484ACB';
			} else {
				$data['customer_bg'] = '#dc3545';
			}

			$this->load->model('account/customer');

			$customer_group_id = $this->customer->getGroupId();
			$customer_id = $this->customer->getId();

			if ($customer_group_id) {
				$this->load->model('account/customer');
				$customer_info = $this->model_account_customer->getCustomer($customer_id);

				if($this->customer->getCustomerType() == 2){
					$data['customer_group_name'] = $this->language->get('text_worker');
				} else {
					// if(!empty($customer_info['company_name'])){
					//	$data['customer_group_name'] = mb_substr($customer_info['company_name'], 0, 10, 'UTF-8');
					//} else {
						$this->load->model('account/customer_group');
						$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);

						$data['customer_group_name'] = mb_substr($customer_group_info['name'], 0, 10, 'UTF-8');
					//}
				}
			}
		}


		$data['home'] = $this->url->link('common/home');

		$data['wishlist'] = $this->url->link('account/wishlist', '', true);

		$data['logged'] = $this->customer->isLogged();

		$data['account'] = $this->url->link('account/account', '', true);

		$data['register'] = $this->url->link('account/register', '', true);

		$data['login'] = $this->url->link('account/login', '', true);

		$data['order'] = $this->url->link('account/order', '', true);

		$data['transaction'] = $this->url->link('account/transaction', '', true);

		$data['download'] = $this->url->link('account/download', '', true);

		$data['logout'] = $this->url->link('account/logout', '', true);

		$data['shopping_cart'] = $this->url->link('checkout/cart');

		$data['checkout'] = $this->url->link('checkout/checkout', '', true);

		$data['contact'] = $this->url->link('information/contact');

		$data['telephone'] = $this->config->get('config_telephone');


		if ($data['type_customer'] == 2) {
			$data['manager_list_link'] = $this->url->link('account/manager_list');

			$data['manager_buh_link'] = $this->url->link('account/manager_buh');
		}



		$data['language'] = $this->load->controller('common/language');

		$data['currency'] = $this->load->controller('common/currency');

		if ($this->config->get('configblog_blog_menu')) {

			$data['blog_menu'] = $this->load->controller('blog/menu');

		} else {

			$data['blog_menu'] = '';

		}

		$data['search'] = $this->load->controller('common/search');

		$data['cart'] = $this->load->controller('common/cart');

		$data['menu'] = $this->load->controller('common/menu');

		return $this->load->view('common/header', $data);

	}

}

