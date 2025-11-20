<?php
class ControllerExtensionModuleGconsent extends Controller {

	private $error = array();
	private $token_var;
	private $extension_var;
	private $prefix;
	private $confirm = false;

	public function __construct($registry) {
		parent::__construct($registry);
		$this->token_var = (version_compare(VERSION, '3.0', '>=')) ? 'user_token' : 'token';
		$this->extension_var = (version_compare(VERSION, '3.0', '>=')) ? 'marketplace' : 'extension';
		$this->prefix = (version_compare(VERSION, '3.0', '>=')) ? 'module_' : '';
	}

	public function index() {
		$data = $this->load->language('extension/module/gconsent');

		$heading_title = $this->language->get('heading_title');
		$this->document->setTitle($heading_title);

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if ($this->confirm) {
				$this->model_setting_setting->editSetting($this->prefix . 'gconsent', $this->request->post);
				$this->session->data['success'] = $this->language->get('text_success');
			}

			if (isset($this->request->post['apply'])) {
				$this->response->redirect($this->url->link('extension/module/gconsent', $this->token_var . '=' . $this->session->data[$this->token_var], true));
			} else {
				$this->response->redirect($this->url->link($this->extension_var . '/extension', $this->token_var . '=' . $this->session->data[$this->token_var] . '&type=module', true));
			}
		}

		$this->document->addStyle('view/javascript/summernote/summernote.css');
		$this->document->addScript('view/javascript/summernote/summernote.js');
		$this->document->addScript('view/javascript/summernote/opencart.js');

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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token_var . '=' . $this->session->data[$this->token_var], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link($this->extension_var . '/extension', $this->token_var . '=' . $this->session->data[$this->token_var] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $heading_title,
			'href' => $this->url->link('extension/module/gconsent', $this->token_var . '=' . $this->session->data[$this->token_var], true)
		);

		$data['prefix'] = $this->prefix;
		$data['token_var'] = $this->token_var;
		$data[$this->token_var] = $this->session->data[$this->token_var];
		$data['action'] = $this->url->link('extension/module/gconsent', $this->token_var . '=' . $this->session->data[$this->token_var], true);
		$data['cancel'] = $this->url->link($this->extension_var . '/extension', $this->token_var . '=' . $this->session->data[$this->token_var] . '&type=module', true);

		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
		$data['languages'] = $languages;

		if (isset($this->request->post[$this->prefix . 'gconsent_status'])) {
			$data[$this->prefix . 'gconsent_status'] = $this->request->post[$this->prefix . 'gconsent_status'];
		} else {
			$data[$this->prefix . 'gconsent_status'] = $this->config->get($this->prefix . 'gconsent_status');
		}
		if (isset($this->request->post[$this->prefix . 'gconsent_position'])) {
			$data[$this->prefix . 'gconsent_position'] = $this->request->post[$this->prefix . 'gconsent_position'];
		} else {
			$data[$this->prefix . 'gconsent_position'] = $this->config->get($this->prefix . 'gconsent_position');
		}
		if (isset($this->request->post[$this->prefix . 'gconsent_settings'])) {
			$data[$this->prefix . 'gconsent_settings'] = $this->request->post[$this->prefix . 'gconsent_settings'];
		} else {
			$data[$this->prefix . 'gconsent_settings'] = $this->config->get($this->prefix . 'gconsent_settings');
		}
		
		if (isset($this->request->post[$this->prefix . 'gconsent_background'])) {
			$data[$this->prefix . 'gconsent_background'] = $this->request->post[$this->prefix . 'gconsent_background'];
		} elseif ($this->config->get($this->prefix . 'gconsent_background')) {
			$data[$this->prefix . 'gconsent_background'] = $this->config->get($this->prefix . 'gconsent_background');
		} else {
			$data[$this->prefix . 'gconsent_background'] = '#ffffff';
		}
		if (isset($this->request->post[$this->prefix . 'gconsent_color'])) {
			$data[$this->prefix . 'gconsent_color'] = $this->request->post[$this->prefix . 'gconsent_color'];
		} elseif ($this->config->get($this->prefix . 'gconsent_color')) {
			$data[$this->prefix . 'gconsent_color'] = $this->config->get($this->prefix . 'gconsent_color');
		} else {
			$data[$this->prefix . 'gconsent_color'] = '#444444';
		}
		if (isset($this->request->post[$this->prefix . 'gconsent_agree_color'])) {
			$data[$this->prefix . 'gconsent_agree_color'] = $this->request->post[$this->prefix . 'gconsent_agree_color'];
		} elseif ($this->config->get($this->prefix . 'gconsent_agree_color')) {
			$data[$this->prefix . 'gconsent_agree_color'] = $this->config->get($this->prefix . 'gconsent_agree_color');
		} else {
			$data[$this->prefix . 'gconsent_agree_color'] = '#007bff';
		}
		if (isset($this->request->post[$this->prefix . 'gconsent_dis_color'])) {
			$data[$this->prefix . 'gconsent_dis_color'] = $this->request->post[$this->prefix . 'gconsent_dis_color'];
		} elseif ($this->config->get($this->prefix . 'gconsent_dis_color')) {
			$data[$this->prefix . 'gconsent_dis_color'] = $this->config->get($this->prefix . 'gconsent_dis_color');
		} else {
			$data[$this->prefix . 'gconsent_dis_color'] = '#ff7171';
		}
		if (isset($this->request->post[$this->prefix . 'gconsent_set_color'])) {
			$data[$this->prefix . 'gconsent_set_color'] = $this->request->post[$this->prefix . 'gconsent_set_color'];
		} elseif ($this->config->get($this->prefix . 'gconsent_set_color')) {
			$data[$this->prefix . 'gconsent_set_color'] = $this->config->get($this->prefix . 'gconsent_set_color');
		} else {
			$data[$this->prefix . 'gconsent_set_color'] = '#dfe4eb';
		}
		if (isset($this->request->post[$this->prefix . 'gconsent_title'])) {
			$data[$this->prefix . 'gconsent_title'] = $this->request->post[$this->prefix . 'gconsent_title'];
		} else {
			$data[$this->prefix . 'gconsent_title'] = $this->config->get($this->prefix . 'gconsent_title');
		}
		if (isset($this->request->post[$this->prefix . 'gconsent_content'])) {
			$data[$this->prefix . 'gconsent_content'] = $this->request->post[$this->prefix . 'gconsent_content'];
		} else {
			$data[$this->prefix . 'gconsent_content'] = $this->config->get($this->prefix . 'gconsent_content');
		}
		if (isset($this->request->post[$this->prefix . 'gconsent_button'])) {
			$data[$this->prefix . 'gconsent_button'] = $this->request->post[$this->prefix . 'gconsent_button'];
		} else {
			$data[$this->prefix . 'gconsent_button'] = $this->config->get($this->prefix . 'gconsent_button');
		}
		if (isset($this->request->post[$this->prefix . 'gconsent_button_dis'])) {
			$data[$this->prefix . 'gconsent_button_dis'] = $this->request->post[$this->prefix . 'gconsent_button_dis'];
		} else {
			$data[$this->prefix . 'gconsent_button_dis'] = $this->config->get($this->prefix . 'gconsent_button_dis');
		}
		if (isset($this->request->post[$this->prefix . 'gconsent_button_set'])) {
			$data[$this->prefix . 'gconsent_button_set'] = $this->request->post[$this->prefix . 'gconsent_button_set'];
		} else {
			$data[$this->prefix . 'gconsent_button_set'] = $this->config->get($this->prefix . 'gconsent_button_set');
		}
		if (isset($this->request->post[$this->prefix . 'gconsent_button_save'])) {
			$data[$this->prefix . 'gconsent_button_save'] = $this->request->post[$this->prefix . 'gconsent_button_save'];
		} else {
			$data[$this->prefix . 'gconsent_button_save'] = $this->config->get($this->prefix . 'gconsent_button_save');
		}
		if (isset($this->request->post[$this->prefix . 'gconsent_license'])) {
			$data[$this->prefix . 'gconsent_license'] = $this->request->post[$this->prefix . 'gconsent_license'];
		} else {
			$data[$this->prefix . 'gconsent_license'] = $this->config->get($this->prefix . 'gconsent_license');
		}

		if (isset($this->request->post[$this->prefix . 'gconsent_title_cookie'])) {
			$data[$this->prefix . 'gconsent_title_cookie'] = $this->request->post[$this->prefix . 'gconsent_title_cookie'];
		} else {
			$data[$this->prefix . 'gconsent_title_cookie'] = $this->config->get($this->prefix . 'gconsent_title_cookie');
		}
		if (isset($this->request->post[$this->prefix . 'gconsent_desc_cookie'])) {
			$data[$this->prefix . 'gconsent_desc_cookie'] = $this->request->post[$this->prefix . 'gconsent_desc_cookie'];
		} else {
			$data[$this->prefix . 'gconsent_desc_cookie'] = $this->config->get($this->prefix . 'gconsent_desc_cookie');
		}

		if (isset($this->request->post[$this->prefix . 'gconsent_title_func'])) {
			$data[$this->prefix . 'gconsent_title_func'] = $this->request->post[$this->prefix . 'gconsent_title_func'];
		} else {
			$data[$this->prefix . 'gconsent_title_func'] = $this->config->get($this->prefix . 'gconsent_title_func');
		}
		if (isset($this->request->post[$this->prefix . 'gconsent_desc_func'])) {
			$data[$this->prefix . 'gconsent_desc_func'] = $this->request->post[$this->prefix . 'gconsent_desc_func'];
		} else {
			$data[$this->prefix . 'gconsent_desc_func'] = $this->config->get($this->prefix . 'gconsent_desc_func');
		}

		if (isset($this->request->post[$this->prefix . 'gconsent_title_track'])) {
			$data[$this->prefix . 'gconsent_title_track'] = $this->request->post[$this->prefix . 'gconsent_title_track'];
		} else {
			$data[$this->prefix . 'gconsent_title_track'] = $this->config->get($this->prefix . 'gconsent_title_track');
		}
		if (isset($this->request->post[$this->prefix . 'gconsent_desc_track'])) {
			$data[$this->prefix . 'gconsent_desc_track'] = $this->request->post[$this->prefix . 'gconsent_desc_track'];
		} else {
			$data[$this->prefix . 'gconsent_desc_track'] = $this->config->get($this->prefix . 'gconsent_desc_track');
		}

		if (isset($this->request->post[$this->prefix . 'gconsent_title_ads'])) {
			$data[$this->prefix . 'gconsent_title_ads'] = $this->request->post[$this->prefix . 'gconsent_title_ads'];
		} else {
			$data[$this->prefix . 'gconsent_title_ads'] = $this->config->get($this->prefix . 'gconsent_title_ads');
		}
		if (isset($this->request->post[$this->prefix . 'gconsent_desc_ads'])) {
			$data[$this->prefix . 'gconsent_desc_ads'] = $this->request->post[$this->prefix . 'gconsent_desc_ads'];
		} else {
			$data[$this->prefix . 'gconsent_desc_ads'] = $this->config->get($this->prefix . 'gconsent_desc_ads');
		}

		$data['settings_data'] = ['backdrop','close','disagree','notitle','minimal','delay','nobot','hide_descr_mob'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/gconsent', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/gconsent')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		/*if (!empty($this->request->post[$this->prefix.'gconsent_license']) && !empty($_SERVER['SERVER_NAME'])) {   
			$domain = preg_replace('/^www\./', '', $_SERVER['SERVER_NAME']);
			if ($this->request->server['HTTPS']) {
				$server = HTTPS_SERVER;
			} else {
				$server = HTTP_SERVER;
			}
			$parse_domain = parse_url($server);
			$config_domain = preg_replace('/^www\./', '', $parse_domain['host']);
			if ($domain == $config_domain) {
				if (filter_input(INPUT_POST, $this->prefix.'gconsent_license', FILTER_SANITIZE_SPECIAL_CHARS)!=hash('sha256', 'gconsent'.$domain.base64_decode('RGFSeU5hMw=='))) {
					$this->error['warning'] = $this->language->get('error_license3');
				} else {
					$this->confirm = true;
				}
			} else {
				$this->error['warning'] = $this->language->get('error_license2') . ' ' . $domain . ' (' .$config_domain . ')';
			}
		} else {
			$this->error['warning'] = $this->language->get('error_license1');
		}*/

		$this->confirm = true;

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}
}