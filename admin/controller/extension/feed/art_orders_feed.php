<?php
/*
@author	Artem Serbulenko
@link	http://cmsshop.com.ua
@link	https://opencartforum.com/profile/762296-bn174uk/
@email 	serfbots@gmail.com
*/
class ControllerExtensionFeedArtOrdersFeed extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/feed/art_orders_feed');

		$this->document->setTitle(strip_tags($this->language->get('heading_title')));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('feed_art_orders_feed', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true));
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/feed/art_orders_feed', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/feed/art_orders_feed', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'] . '&type=feed', true);

		$data['user_token'] = $this->session->data['user_token'];

		$token = $this->config->get('feed_art_orders_feed_token');

		$data['data_feed'] = HTTP_CATALOG . 'index.php?route=extension/feed/art_orders_feed&token='.$token;

		$data_mas = array(
			'status',
			'token',
			'date',
			'orders',
		);

		foreach ($data_mas as $key => $value) {
			if (isset($this->request->post[$value])) {
				$data['feed_art_orders_feed_'.$value] = $this->request->post['feed_art_orders_feed_'.$value];
			} else {
				$data['feed_art_orders_feed_'.$value] = $this->config->get('feed_art_orders_feed_'.$value);
			}
			$data['entry_'.$value] = $this->language->get('entry_'.$value);
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/feed/art_orders_feed', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/feed/art_orders_feed')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
