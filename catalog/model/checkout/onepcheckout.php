<?php
class ModelCheckoutOnepcheckout extends Model {

	public function getCountryDelivery($country_delivery_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country_delivery WHERE country_delivery_id = '" . (int)$country_delivery_id . "' ORDER BY sort_order ASC");
		return $query->row;
	}

	public function getCountryId($country_delivery_id) {
		$query = $this->db->query("SELECT country_id FROM " . DB_PREFIX . "country_delivery WHERE country_delivery_id = '" . (int)$country_delivery_id . "'");

		if ($query->num_rows) {
			return $query->row['country_delivery_id'];
		} else {
			return false;
		}
	}

	public function getCountryDeliveries($data = array()) {
		$sql = "SELECT cd.*, cdd.name, c.name AS country_name FROM " . DB_PREFIX . "country_delivery cd
		LEFT JOIN " . DB_PREFIX . "country_delivery_description cdd ON (cd.country_delivery_id = cdd.country_delivery_id)
		LEFT JOIN " . DB_PREFIX . "country c ON (cd.country_id = c.country_id)
		WHERE cdd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sql .= " ORDER BY cd.sort_order ASC, cdd.name ASC";

		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getCustomers($data = array()) {
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id)";

		$sql .= " WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$implode = array();

		if (!empty($data['client_name_tel'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname, ' ', REPLACE(c.telephone, ' ', ''), ' ', c.company_name) LIKE '%" . $this->db->escape($data['client_name_tel']) . "%'";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'name',
			'c.email',
			'customer_group',
			'c.status',
			'c.ip',
			'c.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getRelatedProducts($product_ids) {
		$this->load->model('catalog/product');

		$product_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related pr
			LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id)
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
			WHERE pr.product_id IN (" . $product_ids . ")
			AND p.status = '1'
			AND p.quantity > 0
			AND p.date_available <= NOW()
			AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		foreach ($query->rows as $result) {
			$product_data[$result['related_id']] = $this->model_catalog_product->getProduct($result['related_id']);
		}

		return $product_data;
	}

	public function addAbandonedOrder($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "opc_abandoned_order SET store_id = '" . (int)$data['store_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', language_id = '" . (int)$this->config->get('config_language_id') . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', products = '" . $this->db->escape(json_encode($data['products'])) . "', date_added = NOW()");

		return $this->db->getLastId();
	}

	public function editAbandonedOrder($abandoned_id, $data) {
		if ($abandoned_id && $this->abandonedOrderExists($abandoned_id)) {
			$this->db->query("UPDATE " . DB_PREFIX . "opc_abandoned_order SET language_id = '" . (int)$this->config->get('config_language_id') . "', email = '" . $this->db->escape($data['email']) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', products = '" . $this->db->escape(json_encode($data['products'])) . "' WHERE abandoned_id = '" . (int)$abandoned_id . "'");
		} else {
			$this->db->query("INSERT INTO " . DB_PREFIX . "opc_abandoned_order SET store_id = '" . (int)$data['store_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', language_id = '" . (int)$this->config->get('config_language_id') . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', products = '" . $this->db->escape(json_encode($data['products'])) . "', date_added = NOW()");
			$abandoned_id = $this->db->getLastId();
		}

		return $abandoned_id;
	}

	public function abandonedOrderExists($abandoned_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "opc_abandoned_order WHERE abandoned_id = '" . (int)$abandoned_id . "'");
		return ($query->row['total'] > 0);
	}

	public function removeAbandonedOrder($abandoned_id) {
		if ($abandoned_id && $this->abandonedOrderExists($abandoned_id)) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "opc_abandoned_order WHERE abandoned_id = '" . (int)$abandoned_id . "'");
		}
	}

	public function getCustomField($custom_field_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "opc_custom_field` cf LEFT JOIN `" . DB_PREFIX . "opc_custom_field_description` cfd ON (cf.custom_field_id = cfd.custom_field_id) WHERE cf.status = '1' AND cf.custom_field_id = '" . (int)$custom_field_id . "' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getCustomFields($location, $customer_group_id = 0) {
		$custom_field_data = array();

		if (!$customer_group_id) {
			$custom_field_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "opc_custom_field` cf LEFT JOIN `" . DB_PREFIX . "opc_custom_field_description` cfd ON (cf.custom_field_id = cfd.custom_field_id) WHERE cf.status = '1' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cf.status = '1' AND cf.location = '" . $this->db->escape($location) . "' ORDER BY cf.sort_order ASC");
		} else {
			$custom_field_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "opc_custom_field_customer_group` cfcg LEFT JOIN `" . DB_PREFIX . "opc_custom_field` cf ON (cfcg.custom_field_id = cf.custom_field_id) LEFT JOIN `" . DB_PREFIX . "opc_custom_field_description` cfd ON (cf.custom_field_id = cfd.custom_field_id) WHERE cf.status = '1' AND cf.location = '" . $this->db->escape($location) . "' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cfcg.customer_group_id = '" . (int)$customer_group_id . "' ORDER BY cf.sort_order ASC");
		}

		foreach ($custom_field_query->rows as $custom_field) {
			$custom_field_value_data = array();

			if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio' || $custom_field['type'] == 'checkbox') {
				$custom_field_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "opc_custom_field_value cfv LEFT JOIN " . DB_PREFIX . "opc_custom_field_value_description cfvd ON (cfv.custom_field_value_id = cfvd.custom_field_value_id) WHERE cfv.custom_field_id = '" . (int)$custom_field['custom_field_id'] . "' AND cfvd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cfv.sort_order ASC");

				foreach ($custom_field_value_query->rows as $custom_field_value) {
					$custom_field_value_data[] = array(
						'custom_field_value_id' => $custom_field_value['custom_field_value_id'],
						'name'                  => $custom_field_value['name']
					);
				}
			}

			$custom_field_data[$custom_field['custom_field_id']] = array(
				'custom_field_id'    => $custom_field['custom_field_id'],
				'custom_field_value' => $custom_field_value_data,
				'name'               => $custom_field['name'],
				'text_error'         => trim($custom_field['text_error']),
				'type'               => $custom_field['type'],
				'value'              => $custom_field['value'],
				'validation'         => $custom_field['validation'],
				'location'           => $custom_field['location'],
				'required'           => empty($custom_field['required']) || $custom_field['required'] == 0 ? false : true,
				'sort_order'         => $custom_field['sort_order']
			);
		}

		return $custom_field_data;
	}

	public function getCustomFieldValue($custom_field_value_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "opc_custom_field_value cfv LEFT JOIN " . DB_PREFIX . "opc_custom_field_value_description cfvd ON (cfv.custom_field_value_id = cfvd.custom_field_value_id) WHERE cfv.custom_field_value_id = '" . (int)$custom_field_value_id . "' AND cfvd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getCustomFieldValues($custom_field_id) {
		$custom_field_value_data = array();

		$custom_field_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "opc_custom_field_value cfv LEFT JOIN " . DB_PREFIX . "opc_custom_field_value_description cfvd ON (cfv.custom_field_value_id = cfvd.custom_field_value_id) WHERE cfv.custom_field_id = '" . (int)$custom_field_id . "' AND cfvd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cfv.sort_order ASC");

		foreach ($custom_field_value_query->rows as $custom_field_value) {
			$custom_field_value_data[$custom_field_value['custom_field_value_id']] = array(
				'custom_field_value_id' => $custom_field_value['custom_field_value_id'],
				'name'                  => $custom_field_value['name']
			);
		}

		return $custom_field_value_data;
	}
}