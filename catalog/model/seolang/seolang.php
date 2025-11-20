<?php
/* All rights reserved belong to the module, the module developers https://support.opencartadmin.com */
// https://support.opencartadmin.com © 2011-2023 All Rights Reserved
// Distribution, without the author's consent is prohibited
// Commercial license
if (!class_exists('ModelSeoLangSeoLang', false)) {
	class ModelSeoLangSeoLang extends Model {
		public function getSettings($store_id = 0) {
			$setting_data = array();

			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting_seolang WHERE store_id = '" . (int)$store_id . "'");

			foreach ($query->rows as $result) {
				if (!$result['serialized']) {
					$setting_data = $result['value'];
				} else {
					$setting_data = json_decode($result['value'], true);
				}
				$this->config->set('seolang_' . $result['codekey'] . $store_id, $setting_data);
			}
		}

		public function getSetting($code, $store_id = 0) {

			if (!$this->config->get('seolang_' . $code . $store_id)) {
				$setting_data = array();

				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting_seolang WHERE store_id = '" . (int)$store_id . "' AND `codekey` = '" . $this->db->escape($code) . "'");

				foreach ($query->rows as $result) {
					if (!$result['serialized']) {
						$setting_data = $result['value'];
					} else {
						$setting_data = json_decode($result['value'], true);
					}
				}
				$this->config->set('seolang_' . $code . $store_id, $setting_data);
				return $setting_data;
			} else {
				return $this->config->get('seolang_' . $code . $store_id);
			}
		}

		public function editSetting($code, $data, $store_id = 0) {
			$this->db->query("DELETE FROM `" . DB_PREFIX . "setting_seolang` WHERE store_id = '" . (int)$store_id . "' AND `codekey` = '" . $this->db->escape($code) . "'");

			foreach ($data as $key => $value) {
				if ($code == $key) {
					if (!is_array($value)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "setting_seolang SET store_id = '" . (int)$store_id . "', `codekey` = '" . $this->db->escape($code) . "', `value` = '" . $this->db->escape($value) . "'");
					} else {
						$this->db->query("INSERT INTO " . DB_PREFIX . "setting_seolang SET store_id = '" . (int)$store_id . "', `codekey` = '" . $this->db->escape($code) . "', `value` = '" . $this->db->escape(json_encode($value, true)) . "', serialized = '1'");
					}
				}
			}
		}

		public function deleteSetting($code, $store_id = 0) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "setting_seolang WHERE store_id = '" . (int)$store_id . "' AND `codekey` = '" . $this->db->escape($code) . "'");
		}

		public function getSettingValue($key, $store_id = 0) {
			$query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting_seolang WHERE store_id = '" . (int)$store_id . "' AND `codekey` = '" . $this->db->escape($key) . "'");

			if ($query->num_rows) {
				return $query->row['value'];
			} else {
				return null;
			}
		}

		public function editSettingValue($code = '', $key = '', $value = '', $store_id = 0) {
			if (!is_array($value)) {
				$this->db->query("UPDATE " . DB_PREFIX . "setting_seolang SET `value` = '" . $this->db->escape($value) . "', serialized = '0'  WHERE `codekey` = '" . $this->db->escape($code) . "' AND store_id = '" . (int)$store_id . "'");
			} else {
				$this->db->query("UPDATE " . DB_PREFIX . "setting_seolang SET `value` = '" . $this->db->escape(json_encode($value)) . "', serialized = '1' WHERE `codekey` = '" . $this->db->escape($code) . "' AND store_id = '" . (int)$store_id . "'");
			}
		}

		public function getLayouts() {
			if (!$this->config->get('seolang_layouts_id')) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout");
				if ($query->num_rows) {

					foreach ($query->rows as $result) {
						$layout_data[$result['layout_id']] = $result['layout_id'];
					}
					$this->config->set('seolang_layouts_id', $layout_data);
					return $layout_data;
				} else {
					return null;
				}
			} else {
				return $this->config->get('seolang_layouts_id');
			}
		}

		public function cont($cont) {
			$cont = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$cont);
			$file  = DIR_CATALOG . 'controller/' .  $cont . '.php';
			if (file_exists($file)) {
				$this->cont_loading($cont, $file);
			} else {
				$file  = DIR_APPLICATION . 'controller/' . $cont . '.php';
				if (file_exists($file)) {
					$this->cont_loading($cont, $file);
				}
			}
		}

		public function control($cont) {
			$controller_name = '';
			$cont = preg_replace('/[^a-zA-Z0-9_\/]/', '', (string)$cont);
			$file = DIR_APPLICATION . 'controller/' .  $cont . '.php';
			$class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $cont);
			if (function_exists('modification')) {
				$file = modification($file);
			}
			if (file_exists($file)) {
				include_once($file);
				$controller_name = 'controller_' . str_replace('/', '_', $cont);
				$this->registry->set($controller_name, new $class($this->registry));
			}
			return $controller_name;
		}

		public function cont_loading($cont, $file) {
			$class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $cont);
			if (function_exists('modification')) {
				$file = modification($file);
			}
			include_once($file);
			$this->registry->set('controller_' . str_replace('/', '_', $cont), new $class($this->registry));
		}


		public function loadlibrary($library) {
			$file = DIR_SYSTEM . 'library/' . $library . '.php';
			$original_file = $file;
			if (function_exists('modification')) {
				$file = modification($file);
			}
			if (class_exists('VQMod', false)) {
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
			if (file_exists($file)) {
				require_once($file);
			} else {
				//trigger_error('Error: Could not load library ' . $file . '!');
				//exit();
			}
		}
	}
}
if (!function_exists('printmy')) {
    function printmy($data) {
        print_r('<pre>');
        print_r($data);
        print_r('</pre>');
    }
}

