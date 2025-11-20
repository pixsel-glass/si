<?php
/* All rights reserved belong to the module, the module developers https://support.opencartadmin.com */
// https://support.opencartadmin.com © 2011-2023 All Rights Reserved
// Distribution, without the author's consent is prohibited
// Commercial license
if (!class_exists('ControllerSeoLangMovaMova', false)) {
	class ControllerSeoLangMovaMova extends Controller {
		private $error = array();
		protected $data;
		private $controller_main;
		private $widget = 'mova';

		public function __construct($registry) {
			parent::__construct($registry);
			$controller_main_str = 'seolang/seolang';
			$this->load->model($controller_main_str);
			$this->controller_main = $this->model_seolang_seolang->control($controller_main_str);
		}

		public function install_ocmod_settings($this_data) {
			$this->data = $this_data;
			if (!$this->data['thisis']->validate()) return $this->data;

			if (!isset($this->data['seolang_settings']['widget_mova_status'])) {
				$this->data['seolang_settings']['widget_mova_status'] = false;
			}
			if (!isset($this->data['seolang_settings']['cache_diff'])) {
				$this->data['seolang_settings']['cache_diff'] = false;
			}
			if (SC_VERSION > 23) {
				if (!isset($this->data['seolang_settings']['mova_equal'])) {
					$this->data['seolang_settings']['mova_equal'] = false;
				}

				if (!isset($this->data['seolang_settings']['mova_onekeyword'])) {
					$this->data['seolang_settings']['mova_onekeyword'] = false;
				}
			}

			$this->data['widgets_ocmod'][] =
				array(
					'file' => DIR_APPLICATION . 'controller/seolang/mova/mova.ocmod.xml',
					'name' => $this->language->get('ocmod_seolang_mova_name'),
					'id' => $this->language->get('ocmod_seolang_mova_name'),
					'mod' => $this->language->get('ocmod_seolang_mova_mod'),
					'version' => $this->language->get('seolang_version'),
					'author' => $this->language->get('ocmod_seolang_author'),
					'link' => $this->language->get('ocmod_seolang_link'),
					'html' => $this->language->get('ocmod_seolang_mova_html'),
					'status' => 0,
					'switch' => array('all' => array($this->data['seolang_settings']['status'], $this->data['seolang_settings']['widget_mova_status']))
				);


			$this->data['widgets_ocmod'][] =
				array(
					'file' => DIR_APPLICATION . 'controller/seolang/mova/mova.cache.ocmod.xml',
					'name' => $this->language->get('ocmod_seolang_mova_cache_name'),
					'id' => $this->language->get('ocmod_seolang_mova_cache_name'),
					'mod' => $this->language->get('ocmod_seolang_mova_cache_mod'),
					'version' => $this->language->get('seolang_version'),
					'author' => $this->language->get('ocmod_seolang_author'),
					'link' => $this->language->get('ocmod_seolang_link'),
					'html' => $this->language->get('ocmod_seolang_mova_cache_html'),
					'status' => 0,
					'switch' => array(
						'all' => array($this->data['seolang_settings']['status'], $this->data['seolang_settings']['widget_mova_status']),
						'any' => array($this->data['seolang_settings']['status'], $this->data['seolang_settings']['cache_diff'], $this->data['seolang_settings']['widget_mova_status'])
					),

				);

			if (SC_VERSION > 23) {
				$this->data['widgets_ocmod'][] =
					array(
						'file' => DIR_APPLICATION . 'controller/seolang/mova/mova.equal.ocmod.xml',
						'name' => $this->language->get('ocmod_seolang_mova_equal_name'),
						'id' => $this->language->get('ocmod_seolang_mova_equal_name'),
						'mod' => $this->language->get('ocmod_seolang_mova_equal_mod'),
						'version' => $this->language->get('seolang_version'),
						'author' => $this->language->get('ocmod_seolang_author'),
						'link' => $this->language->get('ocmod_seolang_link'),
						'html' => $this->language->get('ocmod_seolang_mova_equal_html'),
						'status' => 0,
						'switch' => array(
							'all' => array($this->data['seolang_settings']['status'], $this->data['seolang_settings']['widget_mova_status']),
							'any' => array($this->data['seolang_settings']['status'], $this->data['seolang_settings']['mova_equal'], $this->data['seolang_settings']['widget_mova_status'])
						),

					);
			}

			if (SC_VERSION > 23) {
				$this->data['widgets_ocmod'][] =
					array(
						'file' => DIR_APPLICATION . 'controller/seolang/mova/mova.onekeyword.ocmod.xml',
						'name' => $this->language->get('ocmod_seolang_mova_onekeyword_name'),
						'id' => $this->language->get('ocmod_seolang_mova_onekeyword_name'),
						'mod' => $this->language->get('ocmod_seolang_mova_onekeyword_mod'),
						'version' => $this->language->get('seolang_version'),
						'author' => $this->language->get('ocmod_seolang_author'),
						'link' => $this->language->get('ocmod_seolang_link'),
						'html' => $this->language->get('ocmod_seolang_mova_onekeyword_html'),
						'status' => 0,
						'switch' => array(
							'all' => array($this->data['seolang_settings']['status'], $this->data['seolang_settings']['widget_mova_status']),
							'any' => array($this->data['seolang_settings']['status'], $this->data['seolang_settings']['mova_onekeyword'], $this->data['seolang_settings']['widget_mova_status'])
						),

					);
			}


			return $this->data;
		}

		public function create_table() {

			if (!$this->data['thisis']->validate()) return;
			$sql[] = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "seolang_mova` (
						`mova_id` INT(11) NOT NULL AUTO_INCREMENT ,
						`customer_id` INT(11) NOT NULL DEFAULT '0' ,
						`product_id` INT(11) NOT NULL,
						`hash` VARCHAR(128) NOT NULL ,
						`date_mova` DATETIME NOT NULL ,
						UNIQUE `mova_id` (`mova_id`),
						INDEX `customer_id` (`customer_id`),
						INDEX `product_id` (`product_id`),
						INDEX `hash` (`hash`),
						INDEX `date_mova` (`date_mova`)
						) ENGINE = MyISAM CHARSET=utf8 COLLATE utf8_general_ci;";

			foreach ($sql as $qsql) {
				$query = $this->db->query($qsql);
			}
		}

		public function createTables($this_data) {
			$this->data = $this_data;
			if (!$this->data['thisis']->validate()) return $this->data;

			//$this->create_table();
			//$this->data['html'] .= '<br>' . $this->language->get('text_seolang_mova_install_db');
			return $this->data;
		}



		private function get_main($method) {

			if (method_exists($this->registry->get($this->controller_main), $method)) {
				$controller_main = $this->controller_main;
				$main = $this->$controller_main->$method();
				$main_data = $main->get_data();
				$this->data = array_merge($this->data, $main_data);
			}
			return $this->data;
		}

		private function prefix() {
			$pull = array();
			$length = 3;
			while (count($pull) < $length) {
				$pull = array_merge($pull, range('0', '9'));
			}
			shuffle($pull);
			$this->data['prefix'] = substr(implode($pull), 0, $length);
		}

		public function add_multi($this_data) {
			$this->data = $this_data;
			if (!$this->data['thisis']->validate()) return;

			/*
        // Get main controller methods
        $this->data['thisis']->load_get_layouts();
        $main_data = $this->data['thisis']->get_data();
        $this->data = array_merge($this->data, $main_data);
        // or
        $this->data = $this->get_main('load_get_layouts');
        */

			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->data['thisis']->validate()) {

				if (isset($this->request->post['widget_name']) && $this->request->post['widget_name'] != '') {
					parse_str(str_replace('&amp;', '&', $this->request->post['myform']), $post_form);
					$settings_widget = $post_form['seolang_settings_' . $this->data['store_id']]['multi'][$this->request->post['widget_name']];
					$settings_widget_data = $post_form[$this->request->post['widget_name']];
					$settings_widget_data['cmswidget'] = '';
				} else {
					$post_form = array();
					$settings_widget = array();
					$settings_widget_data = array();
				}

				$this->prefix();
				// No repeat

				$this->data['multi_name'] = 'MOVA-' . $this->data['prefix'];

				if (!empty($settings_widget)) {
					$settings_array = $settings_widget;
					$settings_array['name'] = $this->data['multi_name'];
				} else {

					$this->data['seolang_settings_store']['multi'] = array();

					if (empty($this->data['seolang_settings_store']['multi'])) {

						$settings_array = array(
							'name' => $this->data['multi_name'],
							'widget' => $this->widget,
							'status' => true
						);
					}
				}

				$settings_widget_data = $this->load_settings_mova($settings_widget_data);

				$this->data['seolang_settings_store']['multi'] = array($this->data['multi_name'] => $settings_array);

				$this->data['multi'] = $this->data['seolang_settings_store']['multi'][$this->data['multi_name']];

				$this->data['seolang_settings_store_widgets_data'] = array($this->data['multi_name'] => $settings_widget_data);


				if (!isset($this->data['seolang_settings_store_widgets_data'][$this->data['multi_name']]['cookie_auto']) || $this->data['seolang_settings_store_widgets_data'][$this->data['multi_name']]['cookie_auto'] == '') {
					$this->data['seolang_settings_store_widgets_data'][$this->data['multi_name']]['cookie_auto'] = 'languageauto';
					$this->data['seolang_settings_store_widgets_data'][$this->data['multi_name']]['cookie_auto_days'] = '30';
				}

				$this->data['template'] = 'seolang/' . $this->widget . '/multi';
				$this->data['add_multi'] = true;
			}
			return $this->data;
		}

		public function load_language($this_data) {
			$this->data = $this_data;

			$this->language->load('seolang/' . $this->widget . '/' . $this->widget);

			$this->data['entry_seolang_tab_other'] = $this->language->get('entry_seolang_tab_other');
			$this->data['entry_seolang_title_loading'] = $this->language->get('entry_seolang_title_loading');
			$this->data['entry_seolang_scripts'] = $this->language->get('entry_seolang_scripts');
			$this->data['entry_seolang_js'] = $this->language->get('entry_seolang_js');
			$this->data['entry_seolang_css'] = $this->language->get('entry_seolang_css');
			$this->data['entry_seolang_ajax'] = $this->language->get('entry_seolang_ajax');
			$this->data['entry_seolang_ajax_delay'] = $this->language->get('entry_seolang_ajax_delay');
			$this->data['entry_seolang_ajax_events'] = $this->language->get('entry_seolang_ajax_events');
			$this->data['entry_seolang_ajax_loader'] = $this->language->get('entry_seolang_ajax_loader');
			$this->data['entry_seolang_remove_vars'] = $this->language->get('entry_seolang_remove_vars');
			$this->data['entry_seolang_output'] = $this->language->get('entry_seolang_output');
			$this->data['entry_seolang_controller'] = $this->language->get('entry_seolang_controller');
			$this->data['entry_seolang_number'] = $this->language->get('entry_seolang_number');
			$this->data['entry_seolang_number_storage'] = $this->language->get('entry_seolang_number_storage');
			$this->data['entry_seolang_image_x'] = $this->language->get('entry_seolang_image_x');
			$this->data['entry_seolang_image_y'] = $this->language->get('entry_seolang_image_y');
			$this->data['entry_seolang_ajax_events_delay'] = $this->language->get('entry_seolang_ajax_events_delay');
			$this->data['entry_seolang_landing_category'] = $this->language->get('entry_seolang_landing_category');
			$this->data['entry_seolang_ajax_events_status'] = $this->language->get('entry_seolang_ajax_events_status');
			$this->data['entry_seolang_template'] = $this->language->get('entry_seolang_template');
			$this->data['entry_seolang_code'] = $this->language->get('entry_seolang_code');
			$this->data['entry_seolang_landing_widget'] = $this->language->get('entry_seolang_landing_widget');
			$this->data['entry_seolang_cookie_life'] = $this->language->get('entry_seolang_cookie_life');
			$this->data['entry_seolang_db_life'] = $this->language->get('entry_seolang_db_life');
			$this->data['entry_seolang_storage_change'] = $this->language->get('entry_seolang_storage_change');

			$this->data['entry_seolang_langswitch_replace'] = $this->language->get('entry_seolang_langswitch_replace');
			$this->data['entry_seolang_autoredirect'] = $this->language->get('entry_seolang_autoredirect');
			$this->data['entry_seolang_bots_status'] = $this->language->get('entry_seolang_bots_status');
			$this->data['entry_seolang_redirect'] = $this->language->get('entry_seolang_redirect');
			$this->data['entry_seolang_autoredirect_langs_ex'] = $this->language->get('entry_seolang_autoredirect_langs_ex');
			$this->data['entry_seolang_mova_widget_title'] = $this->language->get('entry_seolang_mova_widget_title');
			$this->data['entry_seolang_mova_widget_title_status'] = $this->language->get('entry_seolang_mova_widget_title_status');
			$this->data['entry_seolang_mova_widget_footer_status'] = $this->language->get('entry_seolang_mova_widget_footer_status');
			$this->data['entry_seolang_mova_widget_html'] = $this->language->get('entry_seolang_mova_widget_html');

			$this->data['entry_seolang_popup'] = $this->language->get('entry_seolang_popup');

			$this->data['entry_seolang_mova_widget_lm_text_close'] = $this->language->get('entry_seolang_mova_widget_lm_text_close');
			$this->data['entry_seolang_mova_widget_code_custom'] = $this->language->get('entry_seolang_mova_widget_code_custom');
			$this->data['entry_seolang_mova_widget_lang_name'] = $this->language->get('entry_seolang_mova_widget_lang_name');

			$this->data['entry_seolang_widget_mova_anchor'] = $this->language->get('entry_seolang_widget_mova_anchor');
			$this->data['entry_seolang_widget_mova_doc_ready'] = $this->language->get('entry_seolang_widget_mova_doc_ready');
			$this->data['entry_seolang_widget_mova_reserved'] = $this->language->get('entry_seolang_widget_mova_reserved');
			$this->data['entry_hreflang_switcher_stores'] = $this->language->get('entry_hreflang_switcher_stores');


			$this->data['text_seolang_mova_bots'] = $this->language->get('text_seolang_mova_bots');
			$this->data['entry_seolang_mova_bots'] = $this->language->get('entry_seolang_mova_bots');


			$this->data['entry_seolang_mova_ex_gets'] = $this->language->get('entry_seolang_mova_ex_gets');
			$this->data['entry_seolang_ex_gets_status'] = $this->language->get('entry_seolang_ex_gets_status');
			$this->data['text_seolang_mova_ex_gets'] = $this->language->get('text_seolang_mova_ex_gets');


			$this->data['text_help_seolang_ajax'] = $this->language->get('text_help_seolang_ajax');
			$this->data['text_seolang_remove_vars'] = $this->language->get('text_seolang_remove_vars');
			return $this->data;
		}

		public function save_settings($this_data) {
			if (!$this->data['thisis']->validate()) return $this->data;
			$this->data = $this_data;

			if (!empty($this->request->post['seolang_settings_' . $this->data['store_id']]['multi']) && isset($this->request->post['seolang_settings']['status'])) {
				foreach ($this->request->post['seolang_settings_' . $this->data['store_id']]['multi'] as $multi_name => $multi_array) {
					$this->model_seolang_seolang->editSetting($multi_name, $this->request->post, $this->data['store_id']);
				}
			}
			return $this->data;
		}


		private function load_settings_mova($settings_widget_data) {

			$language_code_old = $this->config->get('config_admin_language');
			$language_id_old = $this->data['languages'][$this->config->get('config_admin_language')]['language_id'];
			$language_old = $this->registry->get('language');
			foreach ($this->data['languages'] as $code => $lang) {

				$this->data['thisis']->switchLanguage($lang['language_id'], $code, $this->data);

				if (!isset($settings_widget_data['title'][$lang['language_id']]) || $settings_widget_data['title'][$lang['language_id']] == '') {
					$settings_widget_data['title'][$lang['language_id']] = $this->language->get('text_seolang_title');
				}
			}

			$this->data['thisis']->switchLanguage($language_id_old, $language_code_old, $this->data);
			$this->registry->set('language', $language_old);

			if (!isset($settings_widget_data['anchor'])) {
				if (SC_VERSION < 20) {
					//$settings_widget_data['anchor'] = "$('#cmswidget-'+cmswidget).remove();" . PHP_EOL . "$('#language').html(data);";
				}
			}

			if (!isset($this->data['seolang_settings']['widget_mova_status'])) {
				$this->data['seolang_settings']['widget_mova_status'] = true;
			}

			$settings_widget_data['anchor_templates'] = array(
				$this->language->get('entry_seolang_widget_mova_anchor_templates_default') => "$('#cmswidget-'+cmswidget).remove();" . PHP_EOL . "$('#language').html(data);",
				$this->language->get('entry_seolang_widget_mova_anchor_templates_html') => "$('#cmswidget-'+cmswidget).remove();" . PHP_EOL . "$('" . $this->language->get('text_seolang_widget_mova_anchor_templates_selector') . "').html(data);",
				$this->language->get('entry_seolang_widget_mova_anchor_templates_clear') => ''
			);

			return $settings_widget_data;
		}

		public function load_settings($this_data) {
			$this->data = $this_data;

			$this->prefix();
			if (!empty($this->data['seolang_settings_store']['multi'])) {

				foreach ($this->data['seolang_settings_store']['multi'] as $multi) {
					$settings_widget_data = $this->model_seolang_seolang->getSetting($multi['name'], $this->data['store_id']);
					$settings_widget_data = $this->load_settings_mova($settings_widget_data);

					// Merge settings all widgets
					if (isset($this->data['seolang_settings_store_widgets_data'][$multi['name']])) {
						$settings_widget_data = array_merge($settings_widget_data, $this->data['seolang_settings_store_widgets_data'][$multi['name']]);
					}
					$this->data['seolang_settings_store_widgets_data'][$multi['name']] = $settings_widget_data;
				}
			}
			if (isset($this->data['seolang_settings_store_widgets_data'])) {
				foreach ($this->data['seolang_settings_store_widgets_data'] as $name => $multi) {
					if (!isset($this->data['seolang_settings_store_widgets_data'][$name]['cookie_auto']) || $this->data['seolang_settings_store_widgets_data'][$name]['cookie_auto'] == '') {
						$this->data['seolang_settings_store_widgets_data'][$name]['cookie_auto'] = 'languageauto';
					}
					if (!isset($this->data['seolang_settings_store_widgets_data'][$name]['cookie_auto_days']) || $this->data['seolang_settings_store_widgets_data'][$name]['cookie_auto_days'] == '') {
						$this->data['seolang_settings_store_widgets_data'][$name]['cookie_auto_days'] = '30';
					}
				}
			}


			if (!isset($this->data['widget'])) {
				$this->data['widget'] = $this->widget;
			}

			if (!isset($this->data['seolang_settings']['widget_' . $this->widget . '_status'])) {
				$this->data['seolang_settings']['widget_' . $this->widget . '_status'] = true;
			}


			if (!isset($this->data['seolang_settings']['bots']) || $this->data['seolang_settings']['bots'] == '') {

				$this->data['seolang_settings']['bots'] =
					'Inspection' . PHP_EOL .
					'Google-InspectionTool' . PHP_EOL .
					'Googlebot' . PHP_EOL .
					'adsbot' . PHP_EOL .
					'bingbot' . PHP_EOL .
					'msnbot' . PHP_EOL .
					'google.com/bot' . PHP_EOL .
					'msn.com/msnbot' . PHP_EOL .
					'APIs-Google' . PHP_EOL .
					'AdsBot-Google' . PHP_EOL .
					'Mediapartners-Google' . PHP_EOL .
					'AdsBot-Google-Mobile-Apps' . PHP_EOL .
					'FeedFetcher-Google' . PHP_EOL .
					'Google-Read-Aloud' . PHP_EOL .
					'DuplexWeb-Google' . PHP_EOL .
					'googleweblight' . PHP_EOL .
					'Storebot-Google' . PHP_EOL .
					'Lighthouse' . PHP_EOL .
					'google page speed' . PHP_EOL .
					'MSNBot' . PHP_EOL .
					'AdIdxBot' . PHP_EOL .
					'BingPreview' . PHP_EOL .
					'Yahoo! Slurp' . PHP_EOL .
					'#StackRambler' . PHP_EOL .
					'#Mail.RU_Bot' . PHP_EOL .
					'#yandex.com/bots';
			} else {
				if (stripos($this->data['seolang_settings']['bots'], 'Inspection') === false) {
					$this->data['seolang_settings']['bots'] =
						'Inspection' . PHP_EOL .
						'Google-InspectionTool' . PHP_EOL . $this->data['seolang_settings']['bots'];
				}
			}

			if (!isset($this->data['seolang_settings']['ex_gets']) || $this->data['seolang_settings']['ex_gets'] == '') {
				$this->data['seolang_settings']['ex_gets'] =
					'gclid' . PHP_EOL .
					'utm_source' . PHP_EOL .
					'utm_campaign' . PHP_EOL .
					'utm_medium' . PHP_EOL .
					'utm_term' . PHP_EOL .
					'yclid';
			}



			return $this->data;
		}

		public function load_tab_install($this_data) {
			$this->data = $this_data;


			if (!isset($this->data['html_tab_install'])) {
				$this->data['html_tab_install'] = '';
			}

			$this->data['template'] = 'seolang/' . $this->widget . '/tab_install';
			$this->data['thisis']->set_data($this->data);

			$this->data['thisis']->load_view();

			$main_data = $this->data['thisis']->get_data();
			$this->data['html_tab_install'] .= $main_data['html'];

			return $this->data;
		}


		public function load_tab_options($this_data) {
			$this->data = $this_data;

			if (!isset($this->data['html_tab_options'])) {
				$this->data['html_tab_options'] = '';
			}
			$this->data['widget'] = $this->widget;
			if (!isset($this->data['seolang_settings']['widget_' . $this->widget . '_status'])) {
				$this->data['seolang_settings']['widget_' . $this->widget . '_status'] = true;
			}
			$this->data['template'] = 'seolang/' . $this->widget . '/tab_options';
			$this->data['thisis']->set_data($this->data);

			$this->data['thisis']->load_view();

			$main_data = $this->data['thisis']->get_data();
			$this->data['html_tab_options'] .= $main_data['html'];

			return $this->data;
		}

		public function load_tab_menu($this_data) {
			$this->data = $this_data;

			if (!isset($this->data['html_tab_menu'])) {
				$this->data['html_tab_menu'] = '';
			}
			$this->data['widget'] = $this->widget;
			if (!isset($this->data['seolang_settings']['widget_' . $this->widget . '_status'])) {
				$this->data['seolang_settings']['widget_' . $this->widget . '_status'] = true;
			}
			$this->data['template'] = 'seolang/' . $this->widget . '/tab_menu';
			$this->data['thisis']->set_data($this->data);

			$this->data['thisis']->load_view();

			$main_data = $this->data['thisis']->get_data();
			$this->data['html_tab_menu'] .= $main_data['html'];

			return $this->data;
		}




		public function load_tab_service($this_data) {
			$this->data = $this_data;


			if (!isset($this->data['html_tab_service'])) {
				$this->data['html_tab_service'] = '';
			}

			$this->data['template'] = 'seolang/' . $this->widget . '/tab_service';
			$this->data['thisis']->set_data($this->data);

			$this->data['thisis']->load_view();

			$main_data = $this->data['thisis']->get_data();
			$this->data['html_tab_service'] .= $main_data['html'];

			return $this->data;
		}

		public function load_tab_doc($this_data) {
			$this->data = $this_data;


			if (!isset($this->data['html_tab_doc'])) {
				$this->data['html_tab_doc'] = '';
			}

			$this->data['template'] = 'seolang/' . $this->widget . '/tab_doc';
			$this->data['thisis']->set_data($this->data);

			$this->data['thisis']->load_view();

			$main_data = $this->data['thisis']->get_data();
			$this->data['html_tab_doc'] .= $main_data['html'];

			return $this->data;
		}

		private function get_theme_folder() {

			if (SC_VERSION > 21 && !$this->config->get('config_template') || $this->config->get('config_template') == '') {
				if (SC_VERSION > 23) {
					$theme_folder = $this->config->get('theme_' . $this->config->get('config_theme') . '_directory');
				} else {
					$theme_folder = $this->config->get($this->config->get('config_theme') . '_directory');
				}
				return str_replace('..', '', $theme_folder);
			} else {
				return $this->config->get('config_template');
			}
		}


		private function get_template_files($theme_folder, $filter_name) {
			$files_result = array();
			$template_info  = pathinfo($filter_name);

			if (isset($template_info['extension']) && $template_info['extension'] != '') {
				$filter_name = $template_info['filename'];
			}
			if (SC_VERSION > 23) {
				$ext = '{twig,TWIG}';
			} else {
				$ext = '{tpl,TPL}';
			}
			if (isset($this->request->post['langswitch_replace']) && (int)$this->request->post['langswitch_replace'] == 1) {
				$directory = DIR_CATALOG . '/view/theme/' . $theme_folder . '/template/agootemplates/record';
			} else {
				$directory = DIR_CATALOG . '/view/theme/' . $theme_folder . '/template/agootemplates/widgets/langmark';
			}

			$files = glob($directory . '/' . $filter_name . '*.' . $ext, GLOB_BRACE);

			if (!$files) {
				$files = array();
			}
			foreach ($files as $result) {
				$files_result[$result] = $theme_folder;
			}

			return $files_result;
		}

		public function autotemplate() {
			$json = array();
			$files_default = $files_theme = $files_result =  array();

			if (isset($this->request->get['filter_name'])) {

				$filter_name = htmlspecialchars(strip_tags(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8')), ENT_COMPAT, 'UTF-8');

				$theme_folder = $this->get_theme_folder();

				$files_theme = $this->get_template_files($theme_folder, $filter_name);

				if (!$files_theme) {
					$files_theme = array();
				}

				if ($theme_folder != 'default') {
					$theme_folder = 'default';
					$files_default = $this->get_template_files($theme_folder, $filter_name);
				}
				if (!$files_default) {
					$files_default = array();
				}
				$files = array_merge($files_default, $files_theme);

				foreach ($files as $result => $theme) {
					$template_info  = pathinfo($result);
					$name = $template_info['filename'] . '.' . $template_info['extension'];
					$files_result[$name] = $theme;
				}


				foreach ($files_result as $result => $theme) {
					$template_info  = pathinfo($result);
					$name = $template_info['filename'] . '.' . $template_info['extension'];
					if (SC_VERSION > 15) {
						$three_nbsp = '&nbsp;&nbsp;&nbsp;';
					} else {
						$three_nbsp = '';
					}
					$json[] = array(
						'label' => $name . $three_nbsp . ' > ' . $three_nbsp . $theme,
						'name' => $name
					);
				}
			}

			$sort_order = array();

			foreach ($json as $key => $value) {
				$sort_order[$key] = $value['name'];
			}

			array_multisort($sort_order, SORT_ASC, $json);

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}

		public function autocookie() {
			$json = array();

			if (isset($this->request->get['filter_name'])) {
				$filter_name = htmlspecialchars(strip_tags(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8')), ENT_COMPAT, 'UTF-8');


				if (isset($this->request->post['cookie_auto'])) {
					$post_cookie_auto = htmlspecialchars(strip_tags(html_entity_decode($this->request->post['cookie_auto'], ENT_QUOTES, 'UTF-8')), ENT_COMPAT, 'UTF-8');
				}

				$cookie_auto = array($post_cookie_auto, 'language');

				foreach ($cookie_auto as $result) {
					$json[] = array(
						'name' => $result
					);
				}
			}

			$sort_order = array();

			foreach ($json as $key => $value) {
				$sort_order[$key] = $value['name'];
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}
}
