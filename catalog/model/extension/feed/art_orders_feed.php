<?php
/*
@author	Artem Serbulenko
@link	https://cmsshop.com.ua
@link	https://opencartforum.com/profile/762296-bn174uk/
@email 	serfbots@gmail.com
*/   
class ModelExtensionFeedArtOrdersFeed extends Model {

	public function ordersFeed($data = array()) {

		$sql = "SELECT DISTINCT o.order_id AS order_id, o.*, op.* FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_product` AS op ON (op.order_id = o.order_id)";

		if (!empty($data['filter_order_status'])) {
			$implode = array();

			$order_statuses = explode(',', $data['filter_order_status']);

			foreach ($order_statuses as $order_status_id) {
				$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
			}

			if ($implode) {
				$sql .= " WHERE (" . implode(" OR ", $implode) . ")";
			}
		} else {
			$sql .= " WHERE o.order_status_id > '-1'";
		}

		$date = $this->config->get('feed_art_orders_feed_date');

		if (empty($date)) {
			$date = 7;
		}
		
		$sql .= ' AND o.date_added >= "' . date('Y-m-d', strtotime('-' . (int)$date . ' days', strtotime(date("Y-m-d"))))  . '"';
		$sql .= ' ORDER BY o.order_id DESC';

		$results = $this->db->query($sql);
		
		return $results->rows;
	}

	public function orderStatus($order_status_id) {

		$sql = "SELECT name FROM `" . DB_PREFIX . "order_status` WHERE `order_status_id` = '" . $order_status_id . "' AND `language_id` = '" . (int)$this->config->get('config_language_id') . "'";

		$results = $this->db->query($sql);
		
		return $results->row;
	}	
}