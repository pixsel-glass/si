<?php
class ControllerStartupLogin extends Controller {
	public function index() {
		$route = isset($this->request->get['route']) ? $this->request->get['route'] : '';

		$ignore = array(
			'common/login',
			'common/forgotten',
			'common/reset',
			'extension/module/my_sklad/updateMySkladAll',
			'extension/module/my_sklad/updateCurrency',
			'extension/module/my_sklad/createWebhook',
			'extension/module/my_sklad/updateWebhook',
			'extension/module/my_sklad/updateOrderTtn',
			'extension/module/my_sklad/removeDemand',
			'extension/module/my_sklad/orderMySklad',
			'extension/module/my_sklad/updateMySkladCategories',
			'extension/module/seo_url_generator/actionMassGenerateCnURL',

			'extension/smartbill_document',
			'extension/smartbill_document/index',
			'extension/smartbill_document/send_mail'
		);

		// User
		$this->registry->set('user', new Cart\User($this->registry));

		if (!$this->user->isLogged() && !in_array($route, $ignore)) {
			return new Action('common/login');
		}

		if (isset($this->request->get['route'])) {
			$ignore = array(
				'common/login',
				'common/logout',
				'common/forgotten',
				'common/reset',
				'error/not_found',
				'error/permission',
				'extension/module/my_sklad/updateMySkladAll',
				'extension/module/my_sklad/updateCurrency',
				'extension/module/my_sklad/createWebhook',
				'extension/module/my_sklad/updateWebhook',
				'extension/module/my_sklad/updateOrderTtn',
				'extension/module/my_sklad/removeDemand',
				'extension/module/my_sklad/orderMySklad',
				'extension/module/my_sklad/updateMySkladCategories',
				'extension/module/seo_url_generator/actionMassGenerateCnURL',

				'extension/smartbill_document',
				'extension/smartbill_document/index',
				'extension/smartbill_document/send_mail'
			);

			if (!in_array($route, $ignore) && (!isset($this->request->get['user_token']) || !isset($this->session->data['user_token']) || ($this->request->get['user_token'] != $this->session->data['user_token']))) {
				return new Action('common/login');
			}
		} else {
			if (!isset($this->request->get['user_token']) || !isset($this->session->data['user_token']) || ($this->request->get['user_token'] != $this->session->data['user_token'])) {
				return new Action('common/login');
			}
		}
	}
}
