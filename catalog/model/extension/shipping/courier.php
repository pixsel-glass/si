<?php
class ModelExtensionShippingCourier extends Model {
	function getQuote($address) {
		$this->load->language('extension/shipping/courier');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('shipping_courier_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('shipping_courier_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$quote_data = array();

			$cost = $this->config->get('shipping_courier_cost');

			$quote_data['courier'] = array(
				'code'         => 'courier.courier',
				'title'        => $this->language->get('text_description'),
				'cost'         => $cost,
				'tax_class_id' => 0,
				// 'text'         => $this->currency->format(0.00, $this->session->data['currency'])
				// 'text'         => '<img src="image/catalog/courier_logo_logo.png" alt="image/catalog/courier_logo_logo.png" class="courier-logo-img">'
			);

			$method_data = array(
				'code'       => 'courier',
				'title'      => $this->language->get('text_title'),
				'quote'      => $quote_data,
				'sort_order' => $this->config->get('shipping_courier_sort_order'),
				'error'      => false
			);
		}

		return $method_data;
	}
}