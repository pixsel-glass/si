<?php
/* All rights reserved belong to the module, the module developers https://support.opencartadmin.com */
// https://support.opencartadmin.com © 2011-2023 All Rights Reserved
// Distribution, without the author's consent is prohibited
// Commercial license
if (!class_exists('ControllerSeolangLangmark', false)) {
	class ControllerSeolangLangmark extends Controller {
		private $error = array();
		protected $data;
		protected $template;
		protected $children;
		protected $url_link_ssl = true;
		protected $template_engine;
		protected $html;
		protected $protocol;

		public function __construct($registry) {
			parent::__construct($registry);
			if (version_compare(phpversion(), '5.3.0', '<') == true) {
				exit('PHP5.3+ Required');
			}
			if (!defined('SC_VERSION')) define('SC_VERSION', (int)substr(str_replace('.', '', VERSION), 0, 2));
			if (SC_VERSION > 23) {
				$this->data['token_name'] = 'user_token';
			} else {
				$this->data['token_name'] = 'token';
			}
			if (isset($this->session->data[$this->data['token_name']])) {
				$this->data['token'] = $this->session->data[$this->data['token_name']];
			} else {
				$this->data['token'] = '';
			}

			if ((isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1')) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) == 'on'))) {
				$this->protocol = 'https';
				$this->url_link_ssl = true;
				$this->admin_server = HTTPS_SERVER;
			} else {
				$this->protocol = 'http';
				if (SC_VERSION < 20) {
					$this->url_link_ssl = 'NONSSL';
				} else {
					$this->url_link_ssl = false;
				}
				$this->admin_server = HTTP_SERVER;
			}
			if (SC_VERSION > 23) {
				$this->template_engine = $this->config->get('template_engine');
			}


			if ($this->protocol == 'https') {
				$config_url_0 = HTTPS_CATALOG;
			} else {
				$config_url_0 = HTTP_CATALOG;
			}

			$this->load->model('setting/store');

			$this->data['stores'][0] = array(
				'store_id' => 0,
				'name'     => $this->config->get('config_name'),
				'url' => $config_url_0
			);

			$stores = $this->model_setting_store->getStores();

			if ($this->config->get('config_admin_language')) {
				$this->data['config_admin_language'] = $this->config->get('config_admin_language');
			} else {
				$this->data['config_admin_language'] = '';
			}

			foreach ($stores as $result) {
				if ($this->protocol == 'https' && isset($result['ssl']) && $result['ssl'] != '') {
					$store_url = $result['ssl'];
				} else {
					$store_url = $result['url'];
				}

				$this->data['stores'][$result['store_id']] = array(
					'store_id' => $result['store_id'],
					'name' => $result['name'],
					'url' => $store_url
				);
			}
		}

		public function index() {
			$this->load_start();
			$this->load_session_token();
			$this->load_language_get();
			$this->load_model();
			$this->load_version();
			$this->load_setTitle();
			$this->save_settings();
			$this->load_settings();
			$this->load_scripts();
			$this->load_url_link();
			$this->load_get_languages();
			$this->load_get_currencies();
			$this->load_get_layouts();
			$this->load_messages();
			$this->load_menu();
			$this->load_set();
			$this->load_set_get_pagination();
			$this->load_set_hreflang_switcher();
			$this->load_set_shortcodes();
			$this->load_set_desc_type();
			$this->load_set_ex_multilang_route();
			$this->load_set_ex_multilang_uri();
			$this->load_set_ex_url_route();
			$this->load_set_ex_url_amp();
			$this->load_set_ex_url_uri();
			$this->load_set_use_link_status();
			$this->load_view_settings();
			$this->load_view();
			$this->load_view_output();
		}

		private function load_get_stores() {

			$this->load->model('setting/store');

			if (isset($this->request->get['store_id'])) {
				$this->data['store_id'] = (int) $this->request->get['store_id'];
			} else {
				if (isset($this->request->post['store_id'])) {
					$this->data['store_id'] = (int) $this->request->post['store_id'];
				} else {
					$this->data['store_id'] = 0;
				}
			}

			$flag_store = false;
			foreach ($this->data['stores'] as $store) {
				if ($store['store_id'] == $this->data['store_id']) {
					$flag_store = true;
				}
			}
			if (!$flag_store) $this->data['store_id'] = 0;
		}

		private function load_ssl_domen() {
			if ((isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1')) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https') || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) == 'on'))) {
				$this->url_link_ssl = true;
			} else {
				if (SC_VERSION < 20) {
					$this->url_link_ssl = 'NONSSL';
				} else {
					$this->url_link_ssl = false;
				}
			}
		}


		private function load_start() {
			$this->config->set('blog_work', true);
			$this->load_ssl_domen();
			$this->load_get_stores();
		}

		private function load_session_token() {
			if (SC_VERSION > 23) {
				$this->data['token_name'] = 'user_token';
			} else {
				$this->data['token_name'] = 'token';
			}
			$this->data['token'] = $this->session->data[$this->data['token_name']];
		}

		private function load_language_get() {

			$this->data['language'] = $this->language;

			$this->language->load('localisation/currency');
			$this->language->load('setting/store');
			$this->language->load('seolang/seolang');
			$this->language->load('seolang/langmark');


			$this->data['heading_title'] = $this->language->get('heading_title');

			$this->data['language_heading_dev'] = $this->language->get('heading_dev');
			$this->data['language_text_success'] = $this->language->get('text_success');

			$this->data['heading_dev'] = $this->language->get('heading_dev');
			$this->data['entry_xdefault_status'] = $this->language->get('entry_xdefault_status');


			$this->data['tab_options'] = $this->language->get('tab_options');
			$this->data['tab_pagination'] = $this->language->get('tab_pagination');
			$this->data['tab_main'] = $this->language->get('tab_main');
			$this->data['tab_ex'] = $this->language->get('tab_ex');
			$this->data['tab_other'] = $this->language->get('tab_other');
			$this->data['tab_general'] = $this->language->get('tab_general');
			$this->data['tab_list'] = $this->language->get('tab_list');
			$this->data['entry_add'] = $this->language->get('entry_add');
			$this->data['entry_name'] = $this->language->get('entry_name');
			$this->data['entry_install_update'] = $this->language->get('entry_install_update');
			$this->data['entry_widget_status'] = $this->language->get('entry_widget_status');
			$this->data['entry_lang_default'] = $this->language->get('entry_lang_default');
			$this->data['entry_prefix'] = $this->language->get('entry_prefix');
			$this->data['entry_prefix_main'] = $this->language->get('entry_prefix_main');
			$this->data['entry_main_prefix_status'] = $this->language->get('entry_main_prefix_status');
			$this->data['entry_main_title'] = $this->language->get('entry_main_title');
			$this->data['entry_main_description'] = $this->language->get('entry_main_description');
			$this->data['entry_main_keywords'] = $this->language->get('entry_main_keywords');
			$this->data['entry_store_id_related'] = $this->language->get('entry_store_id_related');

			$this->data['entry_redirect_new'] = $this->language->get('entry_redirect_new');
			$this->data['entry_redirect_code'] = $this->language->get('entry_redirect_code');			

			$this->data['ico_seolang'] = $this->language->get('ico_seolang');
			$this->data['tab_text_seolang_menu'] = $this->language->get('tab_text_seolang_menu');
			$this->data['heading_title_seolang'] = $this->language->get('heading_title_seolang');
			$this->data['seolang_version'] = $this->language->get('seolang_version');
			$this->data['entry_seolang_widget_status'] = $this->language->get('entry_seolang_widget_status');

			$this->data['url_store_id_repated_text'] = $this->language->get('url_store_id_repated_text');



			$this->data['entry_shortcodes'] = $this->language->get('entry_shortcodes');
			
			$this->data['entry_multi_sort'] = $this->language->get('entry_multi_sort');
			$this->data['entry_hreflang'] = $this->language->get('entry_hreflang');
			$this->data['entry_languages'] = $this->language->get('entry_languages');
			$this->data['entry_access'] = $this->language->get('entry_access');

			$this->data['entry_prefix_switcher'] = $this->language->get('entry_prefix_switcher');
			$this->data['entry_prefix_switcher_stores'] = $this->language->get('entry_prefix_switcher_stores');
			$this->data['entry_hreflang_switcher'] = $this->language->get('entry_hreflang_switcher');
			$this->data['entry_langmark_template'] = $this->language->get('entry_langmark_template');
			$this->data['entry_layout'] = $this->language->get('entry_layout');
			$this->data['entry_position'] = $this->language->get('entry_position');
			$this->data['entry_status'] = $this->language->get('entry_status');
			$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
			$this->data['entry_copy_rules'] = $this->language->get('entry_copy_rules');
			$this->data['entry_hreflang_switcher_stores'] = $this->language->get('entry_hreflang_switcher_stores');
			
			$this->data['button_save'] = $this->language->get('button_save');
			$this->data['button_cancel'] = $this->language->get('button_cancel');
			$this->data['button_add_module'] = $this->language->get('button_add_module');
			$this->data['button_remove'] = $this->language->get('button_remove');

			$this->data['text_enabled'] = $this->language->get('text_enabled');
			$this->data['text_disabled'] = $this->language->get('text_disabled');
			$this->data['text_content_top'] = $this->language->get('text_content_top');
			$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');
			$this->data['text_column_left'] = $this->language->get('text_column_left');
			$this->data['text_column_right'] = $this->language->get('text_column_right');
			$this->data['text_multi_empty'] = $this->language->get('text_multi_empty');


			$this->data['text_shortcodes_in'] = $this->language->get('text_shortcodes_in');
			$this->data['text_shortcodes_out'] = $this->language->get('text_shortcodes_out');
			$this->data['text_shortcodes_action'] = $this->language->get('text_shortcodes_action');

			$this->data['url_text_seolang'] = $this->language->get('url_text_seolang');
			$this->data['url_modules_text'] = $this->language->get('url_modules_text');
			$this->data['url_langmark_text'] = $this->language->get('url_langmark_text');
			$this->data['url_record_text'] = $this->language->get('url_record_text');
			$this->data['url_fields_text'] = $this->language->get('url_fields_text');
			$this->data['url_comment_text'] = $this->language->get('url_comment_text');
			$this->data['url_create_text'] = $this->language->get('url_create_text');
			$this->data['url_delete_text'] = $this->language->get('url_delete_text');
		}

		private function load_model() {
			$this->load->model('setting/setting');
			$this->load->model('localisation/language');
			$this->load->model('localisation/currency');
			$this->load->model('design/layout');
			$this->load->model('seolang/langmark');
		}

		private function load_version() {
			$this->data['oc_version'] = str_pad(str_replace('.', '', VERSION), 7, '0');
			$this->data['blog_version']       = '*';
			$this->data['blog_version_model'] = '*';
			$settings_admin = $this->model_setting_setting->getSetting('ascp_version', 'ascp_version');
			foreach ($settings_admin as $key => $value) {
				$this->data['blog_version'] = $value;
			}
			$settings_admin_model = $this->model_setting_setting->getSetting('ascp_version_model', 'ascp_version_model');
			foreach ($settings_admin_model as $key => $value) {
				$this->data['blog_version_model'] = $value;
			}
			$this->data['blog_version'] = $this->data['blog_version'] . ' ' . $this->data['blog_version_model'];


			$this->data['langmark_version_text'] = $this->language->get('langmark_version');
			$this->data['langmark_version'] = '*';
			$settings_admin = $this->model_setting_setting->getSetting('asc_langmark_version', 'asc_langmark_version');
			foreach ($settings_admin as $key => $value) {
				$this->data['langmark_version'] = $value;
			}

			if ($this->data['langmark_version'] != $this->data['langmark_version_text']) {
				$this->data['text_update'] = $this->language->get('text_update_text');
			}
		}

		private function load_menu() {
			$this->data['agoo_menu'] = '';
			return;
			$this->cont('agooa/adminmenu');
			$this->data['agoo_menu'] = $this->controller_agooa_adminmenu->index();
		}

		private function load_setTitle() {
			$this->document->setTitle(strip_tags($this->data['heading_title']));
		}

		private function save_settings() {
			if ($this->request->server['REQUEST_METHOD'] == 'POST') {

				$this->data['seolang_langmark_settings'] = $this->config->get('seolang_langmark_settings');
				if (!is_array($this->data['seolang_langmark_settings'])) {
					$this->data['seolang_langmark_settings'] = array();
				}
				if ($this->validate() && !empty($this->request->post['asc_langmark_' . $this->data['store_id']]['multi'])) {
					$this->cache->delete('langmark');
					$this->cache->delete('html');


					foreach ($this->request->post['asc_langmark_' . $this->data['store_id']]['multi'] as $multi_name => $multi_array) {
						if (isset($this->request->post['asc_langmark_' . $this->data['store_id']]['multi'][$multi_name])) {
							unset($this->request->post['asc_langmark_' . $this->data['store_id']]['multi'][$multi_name]);
							if (isset($multi_array['name'])) {
								/*
	            			if (!empty($multi_array['shortcodes'])) {
		            			foreach ($multi_array['shortcodes'] as $sc_num => $sc_array) {
		            				$multi_array['shortcodes'][$sc_num]['in'] = str_replace(array("\r\n", "\r", "\n", PHP_EOL), '{{BR}}', $multi_array['shortcodes'][$sc_num]['in']);
		            				$multi_array['shortcodes'][$sc_num]['out'] = str_replace(array("\r\n", "\r", "\n", PHP_EOL), '{{BR}}', $multi_array['shortcodes'][$sc_num]['out']);
		            			}
	            			}
                            */
								$this->request->post['asc_langmark_' . $this->data['store_id']]['multi'][$multi_array['name']] = $multi_array;
							}
						}
					}

					$data['asc_langmark_' . $this->data['store_id']]['asc_langmark_' . $this->data['store_id']] = $this->request->post['asc_langmark_' . $this->data['store_id']];

					if (!isset($data['asc_langmark_' . $this->data['store_id']]['asc_langmark_' . $this->data['store_id']]['multi'])) {
						$data['asc_langmark_' . $this->data['store_id']]['asc_langmark_' . $this->data['store_id']]['multi'] = array();
					}

					$multi = $data['asc_langmark_' . $this->data['store_id']]['asc_langmark_' . $this->data['store_id']]['multi'];

					if (is_array($multi)) {
						uasort($multi, function ($a, $b) {
		
							if (!isset($a['multi_sort'])) $a['multi_sort'] = '';
							if (!isset($b['multi_sort'])) $b['multi_sort'] = '';
		
							if ($a['multi_sort'] == '') $a['multi_sort'] = 1000;
							if ($b['multi_sort'] == '') $b['multi_sort'] = 1000;
		
							if ($a['multi_sort'] >= $b['multi_sort']) {
								return 1;
							} else {
								return -1;
							}
						});
					}
					$data['asc_langmark_' . $this->data['store_id']]['asc_langmark_' . $this->data['store_id']]['multi'] = $multi;
					

					$this->model_setting_setting->editSetting('asc_langmark_' . $this->data['store_id'], $data['asc_langmark_' . $this->data['store_id']]);

					$data['seolang_langmark_settings']['seolang_langmark_settings'] = array_merge($this->data['seolang_langmark_settings'], $this->request->post['seolang_langmark_settings']);
					$this->model_setting_setting->editSetting('seolang_langmark_settings', $data['seolang_langmark_settings']);


					$this->session->data['success'] = $this->language->get('text_success');
					if (SC_VERSION < 20) {
						$this->redirect(str_replace('&amp;', '&', $this->url->link('seolang/langmark', $this->data['token_name'] . '=' . $this->data['token'] . '&store_id=' . $this->data['store_id'], $this->url_link_ssl)));
					} else {
						$this->response->redirect(str_replace('&amp;', '&', $this->url->link('seolang/langmark', $this->data['token_name'] . '=' . $this->data['token'] . '&store_id=' . $this->data['store_id'], $this->url_link_ssl)));
					}
				} else {
					$this->request->post = array();
					$this->session->data['lm_error_warning'] = $this->language->get('text_error');
					$this->data['lm_error_warning'] = $this->language->get('text_error');
				}
			}
		}

		private function load_settings() {
			$this->data['modules'] = array();

			if (isset($this->request->post['langmark_module'])) {
				$this->data['modules'] = $this->request->post['langmark_module'];
			} elseif ($this->config->get('langmark_module')) {
				$this->data['modules'] = $this->config->get('langmark_module');
			}

			if (isset($this->request->post['asc_langmark_' . $this->data['store_id']])) {
				$this->data['asc_langmark'] = $this->request->post['asc_langmark_' . $this->data['store_id']];
			} else {
				$this->data['asc_langmark'] = $this->config->get('asc_langmark_' . $this->data['store_id']);
			}

			if (!isset($this->data['asc_langmark']['multi'])) {
				$this->data['asc_langmark']['multi'] = array();
			}

			$multi = $this->data['asc_langmark']['multi'];

			if (is_array($multi)) {
				uasort($multi, function ($a, $b) {

					if (!isset($a['multi_sort'])) $a['multi_sort'] = '';
					if (!isset($b['multi_sort'])) $b['multi_sort'] = '';

					if ($a['multi_sort'] == '') $a['multi_sort'] = 1000;
					if ($b['multi_sort'] == '') $b['multi_sort'] = 1000;

					if ($a['multi_sort'] >= $b['multi_sort']) {
						return 1;
					} else {
						return -1;
					}
				});
			}

			$this->data['asc_langmark']['multi'] = $multi;
			
			
			$this->data['asc_langmark']['redirect_codes'] = array('301', '302');
			

			if (isset($this->request->post['seolang_langmark_settings'])) {
				$this->data['seolang_langmark_settings'] = $this->request->post['seolang_langmark_settings'];
			} else {
				$this->data['seolang_langmark_settings'] = $this->config->get('seolang_langmark_settings');
			}
		}

		private function load_scripts_css() {
			if (file_exists(DIR_APPLICATION . 'view/stylesheet/seolang/seolang.css')) {
				$this->document->addStyle('view/stylesheet/seolang/seolang.css?v=' . $this->data['seolang_version']);
			}
			if (file_exists(DIR_APPLICATION . 'view/stylesheet/seolang/icons.css')) {
				$this->document->addStyle('view/stylesheet/seolang/icons.css?v=' . $this->data['seolang_version']);
			}
		}

		private function load_scripts() {
			$this->load_scripts_css();

			if (file_exists(DIR_APPLICATION . 'view/javascript/jquery/tabs.js')) {
				$this->document->addScript('view/javascript/jquery/tabs.js');
			} else {
				if (file_exists(DIR_APPLICATION . 'view/javascript/seolang/tabs.js')) {
					$this->document->addScript('view/javascript/seolang/tabs.js?v=' . $this->data['seolang_version']);
				}
			}
			if (SC_VERSION < 20) {
				$this->document->addStyle('view/javascript/seolang/bootstrap/css/bootstrap.css?v=' . $this->data['seolang_version']);
				$this->document->addStyle('view/javascript/seolang/font-awesome/css/font-awesome.css?v=' . $this->data['seolang_version']);
			}
		}

		private function load_url_link() {
			$this->data['url_seolang'] = str_replace('&amp;', '&', $this->url->link('seolang/langmark', $this->data['token_name'] . '=' . $this->data['token'] . '&store_id=' . $this->data['store_id'], $this->url_link_ssl));

			$this->data['url_langmark'] = str_replace('&amp;', '&', $this->url->link('seolang/langmark', $this->data['token_name'] . '=' . $this->data['token'] . '&store_id=' . $this->data['store_id'], $this->url_link_ssl));
			$this->data['url_create'] = str_replace('&amp;', '&', $this->url->link('seolang/langmark/createtables', $this->data['token_name'] . '=' . $this->data['token'] . '&store_id=' . $this->data['store_id'], $this->url_link_ssl));
			$this->data['url_add_multi'] = str_replace('&amp;', '&', $this->url->link('seolang/langmark/add_multi', $this->data['token_name'] . '=' . $this->data['token'] . '&store_id=' . $this->data['store_id'], $this->url_link_ssl));

			$this->data['url_store_id_related'] = str_replace('&amp;', '&', $this->url->link('seolang/langmark/storeidrelated', $this->data['token_name'] . '=' . $this->data['token'] . '&store_id=' . $this->data['store_id'], $this->url_link_ssl));

			$this->data['url_delete'] = str_replace('&amp;', '&', $this->url->link('seolang/langmark/deletesettings', $this->data['token_name'] . '=' . $this->data['token'] . '&store_id=' . $this->data['store_id'], $this->url_link_ssl));
			$this->data['url_options'] = str_replace('&amp;', '&', $this->url->link('seolang/langmark', $this->data['token_name'] . '=' . $this->data['token'] . '&store_id=' . $this->data['store_id'], $this->url_link_ssl));
			$this->data['url_schemes'] = str_replace('&amp;', '&', $this->url->link('seolang/langmark/schemes', $this->data['token_name'] . '=' . $this->data['token'] . '&store_id=' . $this->data['store_id'], $this->url_link_ssl));
			$this->data['url_widgets'] = str_replace('&amp;', '&', $this->url->link('seolang/langmark/widgets', $this->data['token_name'] . '=' . $this->data['token'] . '&store_id=' . $this->data['store_id'], $this->url_link_ssl));
			$this->data['action'] = str_replace('&amp;', '&', $this->url->link('seolang/langmark', $this->data['token_name'] . '=' . $this->data['token'] . '&store_id=' . $this->data['store_id'], $this->url_link_ssl));


			$this->data['url_modules'] = str_replace('&amp;', '&', $this->url->link('extension/module', $this->data['token_name'] . '=' . $this->data['token'], $this->url_link_ssl));
			$this->data['cancel'] = str_replace('&amp;', '&', $this->url->link('extension/module', $this->data['token_name'] . '=' . $this->data['token'], $this->url_link_ssl));

			$this->data['url_backup'] = str_replace('&amp;', '&', $this->url->link('seolang/langmark/lm_backup', $this->data['token_name'] . '=' . $this->data['token'], $this->url_link_ssl));
			$this->data['url_restore'] = str_replace('&amp;', '&', $this->url->link('seolang/langmark/lm_restore', $this->data['token_name'] . '=' . $this->data['token'], $this->url_link_ssl));

			$this->data['url_seolang_seolang_options'] = str_replace('&amp;', '&', $this->url->link('seolang/seolang', $this->data['token_name'] . '=' . $this->session->data[$this->data['token_name']] . '&store_id=' . $this->data['store_id'], $this->url_link_ssl));
			$this->data['url_seolang_seolang_adapter'] = str_replace('&amp;', '&', $this->url->link('seolang/adapter', $this->data['token_name'] . '=' . $this->session->data[$this->data['token_name']] . '&store_id=' . $this->data['store_id'], $this->url_link_ssl));
		}

		private function load_get_languages() {
			$this->data['languages'] = $this->model_localisation_language->getLanguages();

			foreach ($this->data['languages'] as $code => $language) {

				if (!isset($language['image']) || SC_VERSION > 21) {
					$this->data['languages'][$code]['image'] = 'language/' . $code . '/' . $code . '.png';
				} else {
					$this->data['languages'][$code]['image'] = 'view/image/flags/' . $language['image'];
				}
				if (!file_exists(DIR_APPLICATION . $this->data['languages'][$code]['image'])) {
					$this->data['languages'][$code]['image'] = 'view/image/seocms/sc_1x1.png';
				}
			}

			$this->data['config_language_id'] = $this->config->get('config_language_id');
		}

		private function load_get_currencies() {
			if (isset($this->request->get['sort'])) {
				$sort = $this->request->get['sort'];
			} else {
				$sort = 'title';
			}

			if (isset($this->request->get['order'])) {
				$order = $this->request->get['order'];
			} else {
				$order = 'ASC';
			}

			$data = array(
				'sort'  => $sort,
				'order' => $order,
			);
			$results = $this->model_localisation_currency->getCurrencies($data);

			foreach ($results as $result) {
				$this->data['currencies'][] = array(
					'currency_id'   => $result['currency_id'],
					'title'         => $result['title'] . (($result['code'] == $this->config->get('config_currency')) ? $this->language->get('text_default') : null),
					'code'          => $result['code'],
					'value'         => $result['value'],
					'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified']))
				);
			}
		}

		private function load_get_layouts() {
			$this->data['layouts'] = $this->model_design_layout->getLayouts();
		}

		private function load_messages() {

            if ((int)substr(str_replace('.', '', VERSION), 0, 3) > 300) {
                unset($this->language->data['lm_error_warning']);
             }

			if (isset($this->error['lm_warning'])) {
				$this->data['lm_error_warning'] = $this->error['lm_warning'];
			} else {
				$this->data['lm_error_warning'] = '';
			}
			if (isset($this->session->data['success'])) {
				$this->data['success'] = $this->session->data['success'];
				unset($this->session->data['success']);
			} else {
				$this->data['success'] = '';
			}

			if (isset($this->session->data['lm_error_warning'])) {
				$this->data['lm_error_warning'] = $this->session->data['lm_error_warning'];
				unset($this->session->data['lm_error_warning']);
			} else {
				unset($this->session->data['lm_error_warning']);
				unset($this->data['lm_error_warning']);
			}


			if (isset($this->session->data['success'])) {
				$this->data['session_success'] = $this->session->data['success'];
			}
		}

		private function load_set_get_pagination() {
			if (!isset($this->data['asc_langmark']['get_pagination'])) {
				$this->data['asc_langmark']['get_pagination'] = 'tracking';
			}
		}


		private function load_set_hreflang_switcher() {
			foreach ($this->data['languages'] as $code => $language) {
				if (!isset($this->data['asc_langmark']['hreflang_switcher'][$language['code']])) {
					$this->data['asc_langmark']['hreflang_switcher'][$language['code']] = true;
				}
				if (!isset($this->data['asc_langmark']['prefix_switcher'][$language['code']])) {
					$this->data['asc_langmark']['prefix_switcher'][$language['code']] = true;
				}
			}
		}

		private function load_set_shortcodes() {

			if (!empty($this->data['asc_langmark']['multi'])) {
				foreach ($this->data['asc_langmark']['multi'] as $name => $multi) {
					/*
				if (isset($multi['shortcodes'])) {
				    foreach ($multi['shortcodes'] as $sc_num => $sc_array) {
						$multi['shortcodes'][$sc_num]['in'] = str_replace('{{BR}}', PHP_EOL, $multi['shortcodes'][$sc_num]['in']);
						$multi['shortcodes'][$sc_num]['out'] = str_replace('{{BR}}', PHP_EOL, $multi['shortcodes'][$sc_num]['out']);
					}

				    $this->data['asc_langmark']['multi'][$name]['shortcodes'] = $multi['shortcodes'];
				}
                */
					/*
				if (!isset($this->data['asc_langmark']['multi'][$name]['shortcodes']) ) {
					 $this->data['asc_langmark']['multi'][$name]['shortcodes'] =
					 array( '0' =>
					 		array(	'in' => '%%TO_REPLACE%%',
					 				'out' => 'REPLACE_THEM'
					 			 ),
							'1' =>
					 		array(	'in' => '%%TO_REPLACE%%',
					 				'out' => 'REPLACE_THEM'
					 			 )
					 );
				}
				*/
				}
			}
		}





		private function load_set_desc_type() {
			if (isset($this->request->post['asc_langmark_' . $this->data['store_id']]['desc_type'])) {
				foreach ($this->request->post['asc_langmark_' . $this->data['store_id']]['desc_type'] as $type_id => $desc_type) {
					if ($desc_type['title'] == '') {
						$this->request->post['asc_langmark_' . $this->data['store_id']]['desc_type'][$desc_type['type_id']]['title'] = 'Type-' . $desc_type['type_id'];
					}

					if (!isset($desc_type['vars']) || $desc_type['vars'] == '') {
						$this->request->post['asc_langmark_' . $this->data['store_id']]['desc_type'][$desc_type['type_id']]['vars'] = 'description';
					}

					if ($type_id != $desc_type['type_id']) {
						unset($this->request->post['asc_langmark_' . $this->data['store_id']]['desc_type'][$type_id]);
						$this->request->post['asc_langmark_' . $this->data['store_id']]['desc_type'][$desc_type['type_id']] = $desc_type;
					}
				}
			}

			if (SC_VERSION > 22) {
				$ext = '';
			} else {
				$ext = '.tpl';	
			}
			if (!isset($this->data['asc_langmark']['desc_type']) || empty($this->data['asc_langmark']['desc_type'])) {
				$this->data['asc_langmark']['desc_type'] =
					array(
						'1' =>
						array(
							'type_id' => '1',
							'title' => 'product/category' . $ext,
							'vars' => 'description' . PHP_EOL . '#categories' . PHP_EOL . '#description2'
						),
						'2' =>
						array(
							'type_id' => '2',
							'title' =>  'product/manufacturer_info' . $ext,
							'vars' => 'description'
						),
						'3' =>
						array(
							'type_id' => '3',
							'title' => 'information/information' . $ext,
							'vars' => 'description'
						)
					);
			}
			if (isset($this->data['asc_langmark']['desc_type'])) {
				foreach ($this->data['asc_langmark']['desc_type'] as $type_id => $desc_type) {
					if (!isset($desc_type['vars']) || $desc_type['vars'] == '') {
						$this->data['asc_langmark']['desc_type'][$desc_type['type_id']]['vars'] = 'description';
					}
				}
			}
		}

		private function load_set_ex_multilang_route() {
			if (!isset($this->data['asc_langmark']['ex_multilang_route'])) {
				$this->data['asc_langmark']['ex_multilang_route'] = "api/" . PHP_EOL . "common/simple_connector" . PHP_EOL . "search" . PHP_EOL . "assets" . PHP_EOL . "captcha";
			} else {
				$this->data['asc_langmark']['ex_multilang_route'] = str_ireplace('|', PHP_EOL, $this->data['asc_langmark']['ex_multilang_route']);
			}
		}

		private function load_set_ex_multilang_uri() {
			if (!isset($this->data['asc_langmark']['ex_multilang_uri'])) {
				$this->data['asc_langmark']['ex_multilang_uri'] = '';
			} else {
				$this->data['asc_langmark']['ex_multilang_uri'] = str_ireplace('|', PHP_EOL, $this->data['asc_langmark']['ex_multilang_uri']);
			}
		}

		private function load_set_ex_url_route() {
			if (!isset($this->data['asc_langmark']['ex_url_route'])) {
				$this->data['asc_langmark']['ex_url_route'] = "api/" . PHP_EOL . "common/simple_connector" . PHP_EOL . "assets" . PHP_EOL . "captcha";
			} else {
				$this->data['asc_langmark']['ex_url_route'] = str_ireplace('|', PHP_EOL, $this->data['asc_langmark']['ex_url_route']);
			}
		}
		private function load_set_ex_url_amp() {
			if (!isset($this->data['asc_langmark']['ex_url_amp'])) {
				$this->data['asc_langmark']['ex_url_amp'] = "feed" . PHP_EOL . "sitemap";
			} else {
				$this->data['asc_langmark']['ex_url_amp'] = str_ireplace('|', PHP_EOL, $this->data['asc_langmark']['ex_url_amp']);
			}
		}
		private function load_set_ex_url_uri() {
			if (!isset($this->data['asc_langmark']['ex_url_uri'])) {
				$this->data['asc_langmark']['ex_url_uri'] = '';
			} else {
				$this->data['asc_langmark']['ex_url_uri'] = str_ireplace('|', PHP_EOL, $this->data['asc_langmark']['ex_url_uri']);
			}
		}

		private function load_set_use_link_status() {
			if (!isset($this->data['asc_langmark']['use_link_status'])) {
				$this->data['asc_langmark']['use_link_status'] = true;
			}
			if (!isset($this->data['asc_langmark']['cache_diff'])) {
				$this->data['asc_langmark']['cache_diff'] = true;
			}
			if (!isset($this->data['asc_langmark']['commonhome_status'])) {
				$this->data['asc_langmark']['commonhome_status'] = true;
			}
		}

		private function load_set() {
			if (!isset($this->data['asc_langmark']['access'])) {
				$this->data['asc_langmark']['access'] = true;
			}
			if (!isset($this->data['asc_langmark']['cache_diff'])) {
				$this->data['asc_langmark']['cache_diff'] = true;
			}


			if (!isset($this->data['asc_langmark']['commonhome_status'])) {
				$this->data['asc_langmark']['commonhome_status'] = true;
			}
			if (!isset($this->data['asc_langmark']['two_status'])) {
				$this->data['asc_langmark']['two_status'] = true;
			}



			$this->data['asc_langmark']['jazz'] = false;
		}


		private function load_view_settings() {
			$this->template = 'seolang/langmark';

			if (SC_VERSION < 20) {
				$this->data['column_left'] = '';
			} else {
				if (SC_VERSION > 23) {
					$this->config->set('template_engine', $this->template_engine);
				}

				$this->data['header'] = $this->load->controller('common/header');
				$this->data['footer'] = $this->load->controller('common/footer');
				$this->data['column_left'] = $this->load->controller('common/column_left');
			}

			return $this->data;
		}
		private function load_view() {
			if (SC_VERSION < 30) {
				$this->template = $this->template . '.tpl';
			}

			if (SC_VERSION < 20) {
				$this->children = array(
					'common/header',
					'common/footer'
				);
				$this->html = $this->render();
			} else {

				if (SC_VERSION > 23) {
					$this->config->set('template_engine', 'template');
				}

				$this->html = $this->load->view($this->template, $this->data);

				if (SC_VERSION > 23) {
					$this->config->set('template_engine', $this->template_engine);
				}
			}

			return $this->html;
		}

		private function load_view_output() {
			$this->response->setOutput($this->html);
		}
		/***************************************/
		public function cont($cont) {
			$file  = DIR_CATALOG . 'controller/' . $cont . '.php';
			if (file_exists($file)) {
				$this->cont_loading($cont, $file);
			} else {
				$file  = DIR_APPLICATION . 'controller/' . $cont . '.php';
				if (file_exists($file)) {
					$this->cont_loading($cont, $file);
				} else {
					trigger_error('Error: Could not load controller ' . $cont . '!');
					exit();
				}
			}
		}
		private function cont_loading($cont, $file) {
			$class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $cont);
			include_once($file);
			$this->registry->set('controller_' . str_replace('/', '_', $cont), new $class($this->registry));
		}
		/***************************************/
		private function validate() {
			$this->language->load('seolang/langmark');
			if (!$this->user->hasPermission('modify', 'seolang/langmark')) {
				$this->error['lm_warning'] = $this->language->get('error_permission');
			}
			if (!$this->error) {
				return true;
			} else {
				$this->request->post = array();
				return false;
			}
		}
		/***************************************/
		public function deletesettings() {
			if (($this->request->server['REQUEST_METHOD'] == 'GET') && $this->validate()) {

				$this->load_ssl_domen();
				$this->load_get_stores();
				$this->load_model();
				$this->load_get_languages();
				$this->load_session_token();
				$this->load_language_get();

				$html = '';

				$this->model_setting_setting->deleteSetting('asc_langmark_' . $this->data['store_id']);
				$this->model_setting_setting->deleteSetting('asc_langmark_version');

				$html = $this->language->get('text_success');

				$this->response->setOutput($html);
			} else {

				$html = $this->language->get('error_permission');

				$this->response->setOutput($html);
			}
		}

		public function storeidrelated() {
			if (($this->request->server['REQUEST_METHOD'] == 'GET') && $this->validate()) {

				$this->load_ssl_domen();
				$this->load_get_stores();
				$this->load_model();
				$this->load_get_languages();
				$this->load_session_token();
				$this->load_language_get();

				$html = '';

				$categories = $this->model_seolang_langmark->getAll('category');

				foreach ($categories as $num => $category) {
					$category_exists = $this->model_seolang_langmark->getRecord('category', $category['category_id'], $this->data['store_id']);
					if (!$category_exists) {
						$this->model_seolang_langmark->addRecord('category', $category['category_id'], $this->data['store_id']);
					}
				}

				$products = $this->model_seolang_langmark->getAll('product');

				foreach ($products as $num => $product) {
					$product_exists = $this->model_seolang_langmark->getRecord('product', $product['product_id'], $this->data['store_id']);
					if (!$product_exists) {
						$this->model_seolang_langmark->addRecord('product', $product['product_id'], $this->data['store_id']);
					}
				}

				$informations = $this->model_seolang_langmark->getAll('information');

				foreach ($informations as $num => $information) {
					$information_exists = $this->model_seolang_langmark->getRecord('information', $information['information_id'], $this->data['store_id']);
					if (!$information_exists) {
						$this->model_seolang_langmark->addRecord('information', $information['information_id'], $this->data['store_id']);
					}
				}

				$manufacturers = $this->model_seolang_langmark->getAll('manufacturer');
				if (!empty($manufacturers)) {
					foreach ($manufacturers as $num => $manufacturer) {
						$manufacturer_exists = $this->model_seolang_langmark->getRecord('manufacturer', $manufacturer['manufacturer_id'], $this->data['store_id']);
						if (!$manufacturer_exists) {
							$this->model_seolang_langmark->addRecord('manufacturer', $manufacturer['manufacturer_id'], $this->data['store_id']);
						}
					}
				}

				if (SC_VERSION < 20) {
					$layouts = $this->model_seolang_langmark->getLayoutRouteAll();
					foreach ($layouts as $num => $layout) {
						$layout_exists = $this->model_seolang_langmark->getLayout($layout['layout_id'], $this->data['store_id']);
						if (!$layout_exists) {
							$this->model_seolang_langmark->addLayout($layout, $this->data['store_id']);
						}
					}
				}


				$html = $this->language->get('text_success');

				$this->response->setOutput($html);
			} else {

				$html = $this->language->get('error_permission');

				$this->response->setOutput($html);
			}
		}
		public function load_language() {

			$this->language->load('localisation/currency');
			$this->language->load('setting/store');
			$this->language->load('seolang/seolang');

			$this->data = $this->method_widgets(__FUNCTION__, $this->data);

			$this->data['language'] = $this->language;
			return $this;
		}
		public function switchLanguage($language_id, $language_code, $this_data) {
			$this->data = $this_data;

			$this->config->set('config_admin_language', $language_code);

			if (SC_VERSION > 21) {
				$language_construct = $language_code;
			} else {
				$language_construct = $this->data['languages'][$language_code]['directory'];
			}

			$language = new Language($language_construct);

			if (SC_VERSION > 15) {
				if (SC_VERSION > 21) {
					$language->load($language_code);
				} else {
					$language->load('default');
					$language->load($language_construct);
				}
			} else {
				$language->load($this->data['languages'][$language_code]['filename']);
			}

			$this->registry->set('language', $language);

			$this->load_language_get($this->data);
		}

		public function createTables() {
			if (($this->request->server['REQUEST_METHOD'] == 'GET') && $this->validate()) {

				$this->load_ssl_domen();
				$this->load_get_stores();
				$this->load_model();
				$this->load_get_languages();
				$this->load_session_token();
				$this->load_language_get();

				$html = '';

				$this->data['langmark_version'] = $this->language->get('langmark_version');

				$setting_version = array(
					'asc_langmark_version' => $this->data['langmark_version']
				);

				$this->model_setting_setting->editSetting('asc_langmark_version', $setting_version);

				if (SC_VERSION > 23) {
					$msql = "SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE `query` = 'common/home' AND `keyword` != ''";
					$query = $this->db->query($msql);
					if (count($query->rows) > 0) {
						$msql = "DELETE FROM `" . DB_PREFIX . "seo_url` WHERE `query` = 'common/home' AND `keyword` != ''";
						$query = $this->db->query($msql);
					}

					$msql = "SELECT * FROM `" . DB_PREFIX . "language` WHERE `code` = 'en-gb'";
					$query = $this->db->query($msql);
					if (count($query->rows) > 0) {
						$english_language_id = $query->row['language_id'];

						$msql = "SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE `query` = 'product/search' AND `language_id` = '" . $english_language_id . "'";
						$query = $this->db->query($msql);

						if (count($query->rows) < 1) {
							$msql = "INSERT INTO `" . DB_PREFIX . "seo_url` (`store_id`, `language_id`, `query`, `keyword`) VALUES  (0, " . $english_language_id . ", 'product/search', 'en_search')";
							$query = $this->db->query($msql);
						}
					}
				}

				$msql = "SELECT * FROM `" . DB_PREFIX . "layout_route` WHERE `route`='product/search'";
				$query = $this->db->query($msql);
				if (count($query->rows) <= 0) {
					$msql = "INSERT INTO `" . DB_PREFIX . "layout` (`name`) VALUES  ('Search');";
					$query = $this->db->query($msql);
					$msql = "INSERT INTO `" . DB_PREFIX . "layout_route` (`route`, `layout_id`) VALUES  ('product/search'," . $this->db->getLastId() . ");";
					$query = $this->db->query($msql);
				}


				if ($this->config->get('config_seo_url_type') != 'seo_url') {
					$devider = true;
				} else {
					$devider = false;
				}

				if (!$this->config->get('asc_langmark_' . $this->data['store_id']) && !is_array($this->config->get('asc_langmark_' . $this->data['store_id']))) {

					/* Structure array multi
				[multi] => Array
		        (
		            [name] => Array
		                (
		                    [name] =>
		                    [prefix] =>
		                    [language_id] =>
		                    [currency] =>
		                    [prefix_switcher] =>
		                    [prefix_switcher_stores] =>
		                    [hreflang] =>
		                    [hreflang_switcher] =>
		                    ...
		                )
				)
				*/

					foreach ($this->data['stores'] as $store) {
						if ($this->data['store_id'] == $store['store_id']) {
							$domen_store = str_ireplace(array('http://', 'https://', '//'), array('', '', ''), trim($store['url']));




							$aoptions = array(
								'switch' => true,
								'cache_widgets' => false,
								'pagination' => true,
								'pagination_prefix' => 'page',
								'hreflang_status' => true,
								'url_close_slash' => false,
								'description_status' => true,
								'xdefault_status' => true,
								'currency_switch' => true,
								'ex_multilang_route' => "api/" . PHP_EOL . "common/simple_connector" . PHP_EOL . "assets" . PHP_EOL . "captcha" . PHP_EOL . "module/language",
								'ex_multilang_uri' => "=product/live_options" . PHP_EOL . "=product/search" . PHP_EOL . "=journal3/product" . PHP_EOL . "popup",
								'ex_url_route' => "api/" . PHP_EOL . "common/simple_connector" . PHP_EOL . "assets" . PHP_EOL . "captcha" . PHP_EOL . "module/language",
								'ex_url_uri' => ""
							);


							$language_code_old = $this->config->get('config_admin_language');
							$language_id_old = $this->data['languages'][$this->config->get('config_admin_language')]['language_id'];
							$language_old = $this->registry->get('language');

							foreach ($this->data['languages'] as $language) {

								$prefix = substr($language['code'], 0, 2);

								$hreflang = $prefix;

								if (substr($this->config->get('config_language'), 0, 2) == $prefix) {
									$prefix_main = $language['language_id'];
								} else {
									$prefix_main = false;
								}


								if (substr($this->config->get('config_language'), 0, 2) == $prefix) {
									$prefix = '';
								}

								if ($hreflang == 'ua') $hreflang = 'uk';
								if ($prefix == 'uk') $prefix = 'ua';

								$this->switchLanguage($language['language_id'], $language['code'], $this->data);
								$pagination_title = $this->language->get('text_pagination_title');

								$aoptions['multi'][$language['name']] =
									array(
										'name' => $language['name'],
										'language_id' => $language['language_id'],
										'prefix' => $domen_store . $prefix,
										'prefix_switcher' => true,
										'hreflang' => $hreflang,
										'hreflang_switcher' => true,
										'currency' => '',
										'prefix_main' => $prefix_main,
										'pagination_title' => $pagination_title
									);
							}

							$this->switchLanguage($language_id_old, $language_code_old, $this->data);
							$this->registry->set('language', $language_old);

							$settings = array(
								'asc_langmark_' . $this->data['store_id'] => $aoptions
							);
							$this->model_setting_setting->editSetting('asc_langmark_' . $this->data['store_id'], $settings);
						}
					}
					$html .= $this->language->get('text_install_ok');
				} else {
					/*
				$data['asc_langmark_' . $this->data['store_id']] = $this->config->get('asc_langmark_' . $this->data['store_id']);

				foreach ($data['asc_langmark_' . $this->data['store_id']]['prefix'] as $code => $value) {
					if (strpos($value, $this->domen) === false) {
				    	$data['asc_langmark_' . $this->data['store_id']]['prefix'][$code] = $this->domen . $value;
					}
				}

				$settings = Array(
					'asc_langmark_' . $this->data['store_id'] => $data['asc_langmark_' . $this->data['store_id']]
				);

				$this->model_setting_setting->editSetting('asc_langmark_' . $this->data['store_id'], $settings);
	            */
					$html .= $this->language->get('text_install_already');
				}
			} else {
				$html = $this->language->get('error_permission');
			}

			$this->response->setOutput($html);
		}

		public function add_multi() {
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

				$this->load_ssl_domen();
				$this->load_get_stores();
				$this->load_model();
				$this->load_get_languages();
				$this->load_session_token();
				$this->load_language_get();
				$this->data['ajax_add'] = true;
				$html = '';
				foreach ($this->data['stores'] as $store) {
					if ($this->data['store_id'] == $store['store_id']) {
						$domen_store = str_ireplace(array('http://', 'https://', '//'), array('', '', ''), trim($store['url']));
					}
				}

				$pull = array();
				$length = 2;
				while (count($pull) < $length) {
					$pull = array_merge($pull, range('a', 'z'));
				}
				shuffle($pull);
				$prefix_region = substr(implode($pull), 0, $length);
				$prefix = $prefix_region . '/';
				$this->data['multi_name_row'] = $this->request->post['multi_name_row'];

				if (empty($this->data['asc_langmark']['multi'])) {
					$this->data['asc_langmark']['multi'] =
						array(
							'Region-' . $prefix_region  =>
							array(
								'name' => 'Region-' . $prefix_region,
								'prefix' => $domen_store . $prefix,
								'prefix_switcher' => true,
								'hreflang_switcher' => true
							)
						);
				}

				$this->template = 'seolang/langmark_multi';

				$this->load_view();
			} else {
				$this->html = $this->language->get('error_permission');
			}

			$this->load_view_output();
		}
		private function lm_json_error() {

			$error = json_last_error();

			if ($error != JSON_ERROR_NONE) {
				switch ($error) {
					case JSON_ERROR_NONE:

						break;
					case JSON_ERROR_DEPTH:
						$this->log->write('Langmark: Maximum stack depth reached');
						break;
					case JSON_ERROR_STATE_MISMATCH:
						$this->log->write('Langmark: Incorrect discharges or mode mismatch');
						break;
					case JSON_ERROR_CTRL_CHAR:
						$this->log->write('Langmark: Invalid control character');
						break;
					case JSON_ERROR_SYNTAX:
						$this->log->write('Langmark: Syntax error, incorrect JSON');
						break;
					case JSON_ERROR_UTF8:
						$this->log->write('Langmark found you have an error: Incorrect UTF-8 characters, possibly incorrectly encoded at ' . $_SERVER['REQUEST_URI']);
						break;
					default:
						$this->log->write('Langmark: Unknow error');
						break;
				}
				return true;
			} else {
				return false;
			}
		}
		public function lm_restore() {
			$this->load_model();
			$this->load_get_stores();
			$this->load_language_get();
			$this->load_url_link();
			$this->load_settings();

			$content['success'] = false;
			if ($this->user->hasPermission('modify', 'seolang/langmark')) {

				if (!empty($this->request->files['file']['name'])) {
					if (substr($this->request->files['file']['name'], -5) != '.json') {
						$content['success'] = false;
						$content['text'] = $this->language->get('text_lm_error_filetype');
					} else {
						if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
							$content['success'] = false;
							$content['text'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
						} else {
							$content['success'] = true;
							$content['text'] = $this->language->get('text_lm_restore_success');

							$content_file = file_get_contents($this->request->files['file']['tmp_name']);

							$lm_array_settings = array();
							$lm_array_settings['asc_langmark_' . (int)$this->request->get['store_id']] = (array)json_decode($content_file, JSON_OBJECT_AS_ARRAY);

							if ($this->lm_json_error()) {
								$content['success'] = false;
								$content['text'] = $this->language->get('text_lm_json_error');
							}

							if (!isset($lm_array_settings['asc_langmark_' . (int)$this->request->get['store_id']]['multi'])) {
								$content['success'] = false;
								$content['text'] = $this->language->get('text_lm_settings_no_format');
							}

							if ($content['success']) {
								$this->model_setting_setting->editSetting('asc_langmark_' . (int)$this->request->get['store_id'], (array)$lm_array_settings);
							}
						}
					}
				} else {
					$content['success'] = false;
					$content['text'] = $this->language->get('error_upload');
				}
			} else {
				$content['text'] = $this->language->get('text_lm_restore_access');
				$content['success'] = false;
			}

			$this->response->setOutput(json_encode($content));
		}

		public function lm_backup() {
			$this->load_get_stores();
			$this->load_language_get();
			$this->load_url_link();
			$this->load_settings();

			if ($this->user->hasPermission('modify', 'seolang/langmark')) {

				if (!isset($this->request->get['lm_backup'])) {
					$this->response->addheader('Pragma: public');
					$this->response->addheader('Expires: 0');
					$this->response->addheader('Content-Description: File Transfer');
					$this->response->addheader('Content-Type: application/octet-stream');
					$this->response->addheader('Content-Disposition: attachment; filename="langmark_backup_' . (int)$this->request->get['store_id'] . '_' . $this->lm_get_theme_folder() . '_' . trim($this->lm_host, '/') . '_' . date('d-m-Y_H-i', time()) . '.json"');
					$this->response->addheader('Content-Transfer-Encoding: binary');
					$content = $this->config->get('asc_langmark_' . (int)$this->request->get['store_id']);
				} else {
					$content['text'] = $this->language->get('text_lm_backup_success');
					$content['success'] = true;
				}
			} else {
				$content['text'] = $this->language->get('text_lm_backup_access');
				$content['success'] = false;
			}
			$this->response->setOutput(json_encode($content));
		}

		private function lm_get_theme_folder() {
			if (SC_VERSION > 21 && !$this->config->get('config_template') || $this->config->get('config_template') == '') {
				if (SC_VERSION > 23) {
					$theme_folder = $this->config->get('theme_' . $this->config->get('config_theme') . '_directory');
				} else {
					$theme_folder = $this->config->get($this->config->get('config_theme') . '_directory');
				}
				return $theme_folder;
			} else {
				return $this->config->get('config_template');
			}
		}
		/***************************************/
	}
	if (!function_exists('printmy')) {
		function printmy($data) {
			print_r('<pre>');
			print_r($data);
			print_r('</pre>');
		}
	}
}
