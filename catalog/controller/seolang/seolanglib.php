<?php
/* All rights reserved belong to the module, the module developers https://support.opencartadmin.com */
// https://support.opencartadmin.com © 2011-2023 All Rights Reserved
// Distribution, without the author's consent is prohibited
// Commercial license
if (!class_exists('ControllerSeoLangSeoLanglib', false)) {
	class ControllerSeoLangSeoLanglib extends Controller {
		protected $data;
		protected $api_lm = false;
		protected $route;
		protected $server_protocol = 'HTTP/1.1';

		public function __construct($registry) {
			parent::__construct($registry);
            if (version_compare(phpversion(), '5.3.0', '<') == true) {
                exit('PHP 5.3+ Required');
            }
            if (!defined('VERSION')) {
                exit('Where const VERSION?');
            }
			if (!defined('SC_VERSION')) {
				define('SC_VERSION', (int)substr(str_replace('.', '', VERSION), 0, 2));
			}

			if (is_object($this->model_seolang_seolang)) {
				$this->model_seolang_seolang->getSettings($this->config->get('config_store_id'));

				if (!$this->config->get('seolang_seolang_settings_' . $this->config->get('config_store_id'))) {
					$this->data['seolang_settings_' . $this->config->get('config_store_id')] = $this->model_seolang_seolang->getSetting('seolang_settings_' . $this->config->get('config_store_id'), $this->config->get('config_store_id'));
					$this->config->set('seolang_seolang_settings_' . $this->config->get('config_store_id'), $this->data['seolang_settings_' . $this->config->get('config_store_id')]);
				}
				if (!$this->config->get('seolang_seolang_settings')) {
					$this->data['seolang_settings'] = $this->model_seolang_seolang->getSetting('seolang_settings');
					$this->config->set('seolang_seolang_settings', $this->data['seolang_settings']);
				}

				if (isset($this->data['seolang_settings']['landing_category_id']) && $this->data['seolang_settings']['landing_category_id'] != '') {
					$this->registry->set('landing_category_id', $this->data['seolang_settings']['landing_category_id']);
				}
				// For once call in position & enabled/disabled in admin list modules
				if (SC_VERSION > 23) {
					$this->config->set('module_seolang_status', false);
				}

				if (SC_VERSION < 20) {
					if (!$this->config->get('seolang_layouts_id')) {
						$this->config->set('seolang_layouts_id', $this->model_seolang_seolang->getLayouts());
					}
				}
			}
		}

		private function lm_api() {

			if (!$this->api_lm) {
				if (isset($this->registry->get('request')->get['route'])) {
					$this->route = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$this->registry->get('request')->get['route']);
				} else {
					$this->route = 'common/home';
				}
				if (isset($this->route) && stripos($this->route, 'seolang/seolanglib') !== false) {
					$this->api_lm = false;
					if (isset($this->registry->get('request')->server['SERVER_PROTOCOL']) && $this->registry->get('request')->server['SERVER_PROTOCOL'] != '') {
						$this->server_protocol = $this->registry->get('request')->server['SERVER_PROTOCOL'];
					} else {
						$this->server_protocol = 'HTTP/1.1';
					}
					header($this->server_protocol . ' ' . '404 Not Found');
					header('Status: 404 Not Found');
					exit();
				}
				$this->api_lm = true;
			}
		}



		public function getModule($mudule_id) {
			$this->lm_api();
			
			if ($this->config->get('seolang_seolang_settings_' . $this->config->get('config_store_id'))) {
				$seolang_settings_store = $this->config->get('seolang_seolang_settings_' . $this->config->get('config_store_id'));
			} else {
				$seolang_settings_store = $this->model_seolang_seolang->getSetting('seolang_settings_' . $this->config->get('config_store_id'), $this->config->get('config_store_id'));
				$this->config->set('seolang_seolang_settings_store', $seolang_settings_store);
			}
			
			if (isset($seolang_settings_store['multi'][$mudule_id])) {
				if (SC_VERSION > 20) {
					$settings['setting'] = json_encode($seolang_settings_store['multi'][$mudule_id]);
				} else {
					$settings['setting'] = serialize($seolang_settings_store['multi'][$mudule_id]);
				}
			} else {
				$settings = false;
			}
			return $settings;
		}

		public function getLayoutModules($layout_id, $position, $data) {
			$this->lm_api();
			if (!isset($position)) {
				$position  = false;
			}

			if ($this->config->get('seolang_seolang_settings_' . $this->config->get('config_store_id'))) {
				$seolang_settings_store = $this->config->get('seolang_seolang_settings_' . $this->config->get('config_store_id'));
			} else {
				$seolang_settings_store = $this->model_seolang_seolang->getSetting('seolang_settings_' . $this->config->get('config_store_id'), $this->config->get('config_store_id'));
				$this->config->set('seolang_seolang_settings_store', $seolang_settings_store);
			}
			if (isset($seolang_settings_store['multi'])) {
				$modules = $seolang_settings_store['multi'];
			}

			$request_url = ltrim($this->request->server['REQUEST_URI'], '/');

			if (isset($modules) && $modules && is_array($modules)) {
				foreach ($modules as $num => $module) {
					if (isset($module['uri']) && trim($module['uri']) != '') {
						if (isset($module['uri_template'])) {
							$url_status = $module['uri_template'];
						} else {
							$url_status = false;
						}
						if ($url_status == 1) {
							$pos = utf8_strpos($request_url, trim($module['uri']));
							if ($pos === false) {
							} else {
								$modules[$num]['layout_id'] = $layout_id;
							}
						} else {
							if (trim($module['uri']) == $request_url) {
								$modules[$num]['layout_id'] = $layout_id;
							}
						}
					} else {
						if (isset($module['layout_id']) && is_array($module['layout_id'])) {
							if ($module['status'] > 0) {
								if ($layout_id) {
									foreach ($module['layout_id'] as $key => $value) {
										if ($value == $layout_id) {
											$modules[$num]['layout_id'] = $layout_id;
										}
									}
								}
							}
						}
					}
				}
			}

			if (isset($modules) && is_array($modules) && !empty($modules)) {
				foreach ($modules as $num => $module) {
					if (isset($module['position'])) {
						if ($position == $module['position']) {
							if (isset($module['layout_id']) && !is_array($module['layout_id'])) {
								if ($layout_id == $module['layout_id'] && $module['status']) {
									$module['code'] = 'seolang.seolang|' . $module['name'];
									$data[] = $module;
								}
							}
						}
					}
				}
			}

			if (is_array($data)) usort($data, 'modules_sort');

			return $data;
		}


		public function config_15() {
			$this->lm_api();
			if ($this->config->get('seolang_seolang_settings_' . $this->config->get('config_store_id'))) {
				$seolang_settings_store = $this->config->get('seolang_seolang_settings_' . $this->config->get('config_store_id'));
			} else {
				$seolang_settings_store = $this->model_seolang_seolang->getSetting('seolang_settings_' . $this->config->get('config_store_id'), $this->config->get('config_store_id'));
				$this->config->set('seolang_settings_store', $seolang_settings_store);
			}

			$modules = $seolang_settings_store['multi'];

			$modules_new = array();

			$request_url = ltrim($this->request->server['REQUEST_URI'], '/');
			$i = 0;
			if ($modules && is_array($modules)) {
				foreach ($modules as $num => $module) {

					if (isset($module['uri']) && trim($module['uri']) != '') {

						$uri_flag = false;

						if (isset($module['uri_template'])) {
							$url_status = $module['uri_template'];
						} else {
							$url_status = false;
						}

						if ($url_status == 1) {
							$pos = utf8_strpos($request_url, trim($module['uri']));
							if ($pos !== false) {
								$uri_flag = true;
							}
						} else {
							if (trim($module['uri']) == $request_url) {
								$uri_flag = true;
							}
						}

						if ($uri_flag && $module['status'] > 0) {

							foreach ($this->config->get('seolang_layouts_id') as $key => $layout_id) {
								$modules_new[$i] = $module;
								$modules_new[$i]['layout_id'] = $layout_id;
								$i++;
							}
						}
					} else {
						if (isset($module['layout_id']) && is_array($module['layout_id'])) {
							if ($module['status'] > 0) {

								foreach ($module['layout_id'] as $key => $layout_id) {
									$modules_new[$i] = $module;
									$modules_new[$i]['layout_id'] = $layout_id;
									$i++;
								}
							}
						}
					}
				}
			}

			return $modules_new;
		}

		public function get_theme_folder() {
			$this->lm_api();
			$theme_folder = '';
			if (SC_VERSION > 21 && !$this->config->get('config_template') || $this->config->get('config_template') == '') {
				if (SC_VERSION > 23) {
					$theme_folder = $this->config->get('theme_' . $this->config->get('config_theme') . '_directory');
				} else {
					$theme_folder = $this->config->get($this->config->get('config_theme') . '_directory');
				}

				$theme_folder = $theme_folder ? str_replace('..', '', $theme_folder): '';

				return $theme_folder;
			} else {
				return $this->config->get('config_template');
			}
		}

		public function template($name = '') {
			$this->lm_api();
			if ($name != '') {
				if (SC_VERSION > 23) {
					$ext = '.twig';
				} else {
					$ext = '.tpl';
				}
				if (file_exists(DIR_TEMPLATE . $this->get_theme_folder() . '/template/' . $name . $ext)) {
					if (SC_VERSION > 23) {
						$template = $name;
					} else {
						if (SC_VERSION < 23 && SC_VERSION != 22) {
							$template = $this->get_theme_folder() . '/template/' . $name . $ext;
						} else {
							$template = $name . $ext;
						}
					}
				} else {
					if (SC_VERSION > 23) {
						$template = $name;
					} else {
						if (SC_VERSION < 23 && SC_VERSION != 22) {
							$template = 'default/template/' . $name . $ext;
						} else {
							$template = $name . $ext;
						}
					}
				}
				$template = $template ? str_replace('..', '', $template): '';

				return $template;
			} else {
				return '';
			}
		}

		public function content($template, $data, $twig = true) {
			$this->lm_api();
			$this->template = $template;
			$this->data = $data;

			if (SC_VERSION < 20) {
				$content = $this->render();
			} else {

				if (SC_VERSION > 23 && !$twig) {
					$template_engine = $this->config->get('template_engine');
					$this->config->set('template_engine', 'template');
					$template_directory = $this->registry->get('config')->get('template_directory');
					if (!file_exists(DIR_TEMPLATE . $template_directory . $this->template)) {
						$this->registry->get('config')->set('template_directory', 'default/template/');
					}
				}

				$content = $this->load->view($this->template, $this->data);

				if (SC_VERSION > 23 && !$twig) {
					$this->config->set('template_engine', $template_engine);
					$this->registry->get('config')->set('template_directory', $template_directory);
				}
			}

			return $content;
		}

		public function cont($cont) {
			$this->lm_api();
			//$this->seocmslib_api();
			$cont = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$cont);
			if (!defined('DIR_CATALOG')) {
				$dir_catalog = DIR_APPLICATION;
			} else {
				$dir_catalog = DIR_CATALOG;
			}
			$file  = $dir_catalog . 'controller/' . str_replace('..', '', $cont) . '.php';
			$original_file = $file;
			if (function_exists('modification')) {
				$file = modification($file);
			}

			if (class_exists('VQMod', false)) {
				if (!isset(VQMod::$_virtualMFP)) {
					if (VQMod::$directorySeparator) {
						if (strpos($file, 'vq2-') !== FALSE) {
						} else {
							if (version_compare(VQMod::$_vqversion, '2.5.0', '<')) {
								//trigger_error("You are using an old VQMod version '".VQMod::$_vqversion."', please upgrade your VQMod!");
								//exit;
							}
							if ($original_file != $file) {
								$file = VQMod::modCheck($file, $original_file);
							} else {
								$file = VQMod::modCheck($original_file);
							}
						}
					}
				}
			}

			if (file_exists($file)) {
				$this->cont_loading($cont, $file);
				return true;
			} else {
				$file  = DIR_APPLICATION . 'controller/' . str_replace('..', '', $cont) . '.php';
				if (file_exists($file)) {
					$this->cont_loading($cont, $file);
					return true;
				} else {
					return false;
				}
			}
		}

		private function cont_loading($cont, $file) {
			$class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $cont);
			include_once($file);
			$this->registry->set('controller_' . str_replace('/', '_', $cont), new $class($this->registry));
		}
	}
}

if (!function_exists('modules_sort')) {
	function modules_sort($a, $b) {
		if ($a['sort_order'] == '') $a['sort_order'] = 1000;
		if ($b['sort_order'] == '') $b['sort_order'] = 1000;
		if ($a['sort_order'] > $b['sort_order']) return 1;
		else return -1;
	}
}

if (!function_exists('printmy')) {
	function printmy($data) {
		print_r('<pre>');
		print_r($data);
		print_r('</pre>');
	}
}
