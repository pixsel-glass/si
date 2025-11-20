<?php
class ModelLocalisationCountryDelivery extends Model {
	public function addCountryDelivery($data) {
		//хак для очистки масиву, як що значення доставки не обрано
		foreach($data['shipping_methods'] as $key => $value) {
			if(!isset($value['name'])){
				unset($data['shipping_methods'][$key]);
			}
		}
		$this->db->query("INSERT INTO " . DB_PREFIX . "country_delivery SET country_id = '" . (int)$data['country_id'] . "', cost = '" . (float)$data['cost'] . "', status = '" . (int)$data['status'] . "',sort_order = '" . (int)$data['sort_order'] . "', shipping_methods = '" . $this->db->escape(json_encode($data['shipping_methods'])) . "', payment_methods = '" . $this->db->escape(json_encode($data['payment_methods'])) . "'");

		$country_delivery_id = $this->db->getLastId();

		foreach ($data['country_delivery_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "country_delivery_description SET country_delivery_id = '" . (int)$country_delivery_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		return $country_delivery_id;
	}

	public function editCountryDelivery($country_delivery_id, $data) {

		//хак для очистки масиву, як що значення доставки не обрано
		foreach($data['shipping_methods'] as $key => $value) {
			if(!isset($value['name'])){
				unset($data['shipping_methods'][$key]);
			}
		}
		$this->db->query("UPDATE " . DB_PREFIX . "country_delivery SET country_id = '" . (int)$data['country_id'] . "', cost = '" . (float)$data['cost'] . "', status = '" . (int)$data['status'] . "', sort_order = '" . (int)$data['sort_order'] . "', shipping_methods = '" . $this->db->escape(json_encode($data['shipping_methods'])) . "', payment_methods = '" . $this->db->escape(json_encode($data['payment_methods'])) . "' WHERE country_delivery_id = '" . (int)$country_delivery_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "country_delivery_description WHERE country_delivery_id = '" . (int)$country_delivery_id . "'");

		foreach ($data['country_delivery_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "country_delivery_description SET country_delivery_id = '" . (int)$country_delivery_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}
	}

	public function deleteCountryDelivery($country_delivery_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "country_delivery WHERE country_delivery_id = '" . (int)$country_delivery_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "country_delivery_description WHERE country_delivery_id = '" . (int)$country_delivery_id . "'");
	}

	public function getCountryDelivery($country_delivery_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country_delivery WHERE country_delivery_id = '" . (int)$country_delivery_id . "'");
		return $query->row;
	}

	public function getCountryDeliveryDescriptions($country_delivery_id) {
		$country_delivery_data = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country_delivery_description WHERE country_delivery_id = '" . (int)$country_delivery_id . "'");
		foreach ($query->rows as $result) {
			$country_delivery_data[$result['language_id']] = array(
				'name' => $result['name']
			);
		}
		return $country_delivery_data;
	}

	public function getCountryDeliveries($data = array()) {
		$sql = "SELECT cd.*, cdd.name, c.name AS country_name FROM " . DB_PREFIX . "country_delivery cd
		LEFT JOIN " . DB_PREFIX . "country_delivery_description cdd ON (cd.country_delivery_id = cdd.country_delivery_id)
		LEFT JOIN " . DB_PREFIX . "country c ON (cd.country_id = c.country_id)
		WHERE cdd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sql .= " ORDER BY cdd.name ASC";

		if (isset($data['start']) || isset($data['limit'])) {
			if (!isset($data['start']) || $data['start'] < 0) {
				$data['start'] = 0;
			}
			if (!isset($data['limit']) || $data['limit'] < 1) {
				$data['limit'] = 20;
			}
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);
		return $query->rows;
	}


	public function getTotalCountryDeliveries() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "country_delivery");
		return $query->row['total'];
	}
}
