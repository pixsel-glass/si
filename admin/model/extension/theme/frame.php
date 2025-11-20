<?php 
try {function dummy_handler ($errno, $errstr, $errfile, $errline) {};}catch(Exception $e){}
class ModelExtensionThemeFrame extends Model {

	public function getLicense() {
	
    $valid = false;
    if (stripos(preg_replace('#^www\.(.+)#i', '$1', $_SERVER['HTTP_HOST']), 'pixsel.com.ua') !== false)	{
      $valid = true;
    }

		return $valid;
  }
	
	public function getFrame($code, $store_id = 0) {

		$setting_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");

    //echo '<pre>';
    //var_dump($query);
    //echo '</pre>';

		foreach ($query->rows as $result) {
			if (!$result['serialized']) {
				$setting_data[$result['key']] = $result['value'];
			} else {
				$setting_data[$result['key']] = json_decode($result['value'], true);
			}
		}

		return $setting_data;
	}

	public function editFrame($code, $data, $store_id = 0) {
		
		// if (!$this->getLicense()) {	
		// return;
		// }
		
		$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($code) . "'");

		foreach ($data as $key => $value) {
			if (!is_array($value)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(json_encode($value, true)) . "', serialized = '1'");
			}
		}
	}
}
