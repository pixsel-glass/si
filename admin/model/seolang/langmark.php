<?php
/* All rights reserved belong to the module, the module developers https://support.opencartadmin.com */
// https://support.opencartadmin.com © 2011-2023 All Rights Reserved
// Distribution, without the author's consent is prohibited
// Commercial license
class ModelSeolangLangmark extends Model {
	public function getAll($table) {
		$query = $this->db->query("SELECT " . $table . "_id FROM " . DB_PREFIX . $table);
		if ($query->rows) {
			return $query->rows;
		} else {
			return false;
		}
	}

	public function getRecord($table, $record_id, $store_id) {
		$query = $this->db->query("SELECT " . $table . "_id FROM " . DB_PREFIX . $table . "_to_store WHERE " . $table . "_id = '" . (int)$record_id . "' AND store_id = '" . (int)$store_id . "'");
		if ($query->rows) {
			return $query->rows;
		} else {
			return false;
		}
	}

	public function addRecord($table, $record_id, $store_id) {
		$query = $this->db->query("INSERT INTO " . DB_PREFIX . $table . "_to_store SET " . $table . "_id = '" . (int)$record_id . "', store_id = '" . (int)$store_id . "'");
	}

	public function getLayoutRouteAll() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_route WHERE store_id=0");
		if ($query->rows) {
			return $query->rows;
		} else {
			return false;
		}
	}

	public function getLayout($layout_id, $store_id) {
		$query = $this->db->query("SELECT layout_id FROM " . DB_PREFIX . "layout_route WHERE layout_id = '" . (int)$layout_id . "' AND store_id = '" . (int)$store_id . "'");
		if ($query->rows) {
			return $query->rows;
		} else {
			return false;
		}
	}

	public function addLayout($layout, $store_id) {
		$query = $this->db->query(
			"INSERT INTO " . DB_PREFIX . "layout_route SET
				layout_id = '" . (int)$layout['layout_id'] . "',
				route = '" . $layout['route'] . "',
				store_id = '" . (int)$store_id . "'"
		);
	}
}

if (!function_exists('print_my')) {
	function print_my($data) {
		print_r('<pre>');
		print_r($data);
		print_r('</pre>');
	}
}

if (!function_exists('printmy')) {
	function printmy($data) {
		print_r('<pre>');
		print_r($data);
		print_r('</pre>');
	}
}
