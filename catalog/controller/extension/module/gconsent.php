<?php
class ControllerExtensionModuleGconsent extends Controller {

	private $error = array();
	private $prefix;

	public function __construct($registry) {
		parent::__construct($registry);
		$this->prefix = (version_compare(VERSION, '3.0', '>=')) ? 'module_' : '';
	}

	public function index() {
		if ($this->config->get($this->prefix . 'gconsent_status') && !isset($this->request->cookie['gmc_user_consent'])) {

			$data = $this->load->language('extension/module/gconsent');

			$data['settings'] = (array)$this->config->get($this->prefix . 'gconsent_settings');

			if (!empty($data['settings']) && in_array('nobot', $data['settings']) && $this->bot_detect()) return;

			$this->document->addStyle('catalog/view/javascript/jquery/gconsent/gconsent.css?v=1.0.3');
			$this->document->addScript('catalog/view/javascript/jquery/gconsent/gconsent.js');

			$data['background'] = $this->config->get($this->prefix . 'gconsent_background');
			$data['color'] = $this->config->get($this->prefix . 'gconsent_color');
			$data['agree_color'] = $this->config->get($this->prefix . 'gconsent_agree_color');
			$data['dis_color'] = $this->config->get($this->prefix . 'gconsent_dis_color');
			$data['set_color'] = $this->config->get($this->prefix . 'gconsent_set_color');

			$data['title'] = html_entity_decode($this->config->get($this->prefix . 'gconsent_title')[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
			$data['description'] = html_entity_decode($this->config->get($this->prefix . 'gconsent_content')[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
			$data['button_agree'] = html_entity_decode($this->config->get($this->prefix . 'gconsent_button')[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
			$data['button_disagree'] = html_entity_decode($this->config->get($this->prefix . 'gconsent_button_dis')[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
			$data['button_settings'] = html_entity_decode($this->config->get($this->prefix . 'gconsent_button_set')[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
			$data['button_save'] = html_entity_decode($this->config->get($this->prefix . 'gconsent_button_save')[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');

			$data['title_cookie'] = html_entity_decode($this->config->get($this->prefix . 'gconsent_title_cookie')[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
			$data['title_func'] = html_entity_decode($this->config->get($this->prefix . 'gconsent_title_func')[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
			$data['title_track'] = html_entity_decode($this->config->get($this->prefix . 'gconsent_title_track')[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
			$data['title_ads'] = html_entity_decode($this->config->get($this->prefix . 'gconsent_title_ads')[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');

			$data['desc_cookie'] = html_entity_decode($this->config->get($this->prefix . 'gconsent_desc_cookie')[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
			$data['desc_func'] = html_entity_decode($this->config->get($this->prefix . 'gconsent_desc_func')[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
			$data['desc_track'] = html_entity_decode($this->config->get($this->prefix . 'gconsent_desc_track')[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
			$data['desc_ads'] = html_entity_decode($this->config->get($this->prefix . 'gconsent_desc_ads')[$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');

			if (!empty($data['settings']) && in_array('hide_descr_mob', $data['settings']) && $this->mobile_detect()) {
				$data['hide_descr'] = true;
			} else {
				$data['hide_descr'] = false;
			}

			$data['position'] = $this->config->get($this->prefix . 'gconsent_position');

			return $this->load->view('extension/module/gconsent', $data);

		}
	}

	private function bot_detect() {
		if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT'])) {
			$is_bot = true;
		} else {
			$is_bot = false;
		}
		return $is_bot;
	}

	private function mobile_detect() {
		return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}

}