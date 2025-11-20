<?php

namespace Cart;

final class Tax {

	private $tax_rates = array();

	function get_tax_inform() {

		$data = array();
		
		$vat = 1;
		$dsc_status = 0;
		if ($this->customer->isLogged() && $this->customer->getId() > 0) {
			$customer_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$this->customer->getId() . "'");
			$dsc_status = $customer_info->row['dsc_status'];
			$vat = $customer_info->row['dsc_vat'];
		}

		if ($dsc_status == 1) {
			if ($vat == 0) {
				$data['pixsel_tax_status'] = 0;
			} else {
				$data['pixsel_tax_status'] = $this->config->get('module_pixsel_price_tax_on');
			}
		} else {
			$data['pixsel_tax_status'] = $this->config->get('module_pixsel_price_tax_on');
		}
		
		$data['rate'] = $this->config->get('module_pixsel_price_tax_rate');
		$data['znak'] = $this->config->get('module_pixsel_price_tax_znak');

		// $current_lang = $this->config->get('config_language');
		$current_lang = (isset($_COOKIE['lm_language_code']) ? $_COOKIE['lm_language_code'] : $this->config->get('config_language'));
		$lang_with = $this->config->get('module_pixsel_price_tax_names_with');
		$lang_without = $this->config->get('module_pixsel_price_tax_names_without');

		$data['lang_with'] = $lang_with[$current_lang];
		$data['lang_without'] = $lang_without[$current_lang];

		return $data;
	}

	function calc_tax($price) {
		$rate = $this->config->get('module_pixsel_price_tax_rate');
		$znak = $this->config->get('module_pixsel_price_tax_znak');

		$tax_amount_plus = $price * ($rate / 100);
		$tax_amount_minus = $rate / 100 + 1;

		//if ($vat == 0) {
		//	return $price;
		//} else {
			if ($znak == 'plus') {
				$final_price = $price + $tax_amount_plus;
			} elseif ($znak == 'minus') {
				$final_price = $price / $tax_amount_minus;
			} else {
				return $price;
			}
		//}

		return $final_price;
	}

	public function __construct($registry) {

		$this->config = $registry->get('config');

		$this->db = $registry->get('db');

		$this->customer = $registry->get('customer');

	}



	public function unsetRates() {

		$this->tax_rates = array();

	}



	public function setShippingAddress($country_id, $zone_id) {

		$tax_query = $this->db->query("SELECT tr1.tax_class_id, tr2.tax_rate_id, tr2.name, tr2.rate, tr2.type, tr1.priority FROM " . DB_PREFIX . "tax_rule tr1 LEFT JOIN " . DB_PREFIX . "tax_rate tr2 ON (tr1.tax_rate_id = tr2.tax_rate_id) INNER JOIN " . DB_PREFIX . "tax_rate_to_customer_group tr2cg ON (tr2.tax_rate_id = tr2cg.tax_rate_id) LEFT JOIN " . DB_PREFIX . "zone_to_geo_zone z2gz ON (tr2.geo_zone_id = z2gz.geo_zone_id) LEFT JOIN " . DB_PREFIX . "geo_zone gz ON (tr2.geo_zone_id = gz.geo_zone_id) WHERE tr1.based = 'shipping' AND tr2cg.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND z2gz.country_id = '" . (int)$country_id . "' AND (z2gz.zone_id = '0' OR z2gz.zone_id = '" . (int)$zone_id . "') ORDER BY tr1.priority ASC");



		foreach ($tax_query->rows as $result) {

			$this->tax_rates[$result['tax_class_id']][$result['tax_rate_id']] = array(

				'tax_rate_id' => $result['tax_rate_id'],

				'name'        => $result['name'],

				'rate'        => $result['rate'],

				'type'        => $result['type'],

				'priority'    => $result['priority']

			);

		}

	}



	public function setPaymentAddress($country_id, $zone_id) {

		$tax_query = $this->db->query("SELECT tr1.tax_class_id, tr2.tax_rate_id, tr2.name, tr2.rate, tr2.type, tr1.priority FROM " . DB_PREFIX . "tax_rule tr1 LEFT JOIN " . DB_PREFIX . "tax_rate tr2 ON (tr1.tax_rate_id = tr2.tax_rate_id) INNER JOIN " . DB_PREFIX . "tax_rate_to_customer_group tr2cg ON (tr2.tax_rate_id = tr2cg.tax_rate_id) LEFT JOIN " . DB_PREFIX . "zone_to_geo_zone z2gz ON (tr2.geo_zone_id = z2gz.geo_zone_id) LEFT JOIN " . DB_PREFIX . "geo_zone gz ON (tr2.geo_zone_id = gz.geo_zone_id) WHERE tr1.based = 'payment' AND tr2cg.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND z2gz.country_id = '" . (int)$country_id . "' AND (z2gz.zone_id = '0' OR z2gz.zone_id = '" . (int)$zone_id . "') ORDER BY tr1.priority ASC");



		foreach ($tax_query->rows as $result) {

			$this->tax_rates[$result['tax_class_id']][$result['tax_rate_id']] = array(

				'tax_rate_id' => $result['tax_rate_id'],

				'name'        => $result['name'],

				'rate'        => $result['rate'],

				'type'        => $result['type'],

				'priority'    => $result['priority']

			);

		}

	}



	public function setStoreAddress($country_id, $zone_id) {

		$tax_query = $this->db->query("SELECT tr1.tax_class_id, tr2.tax_rate_id, tr2.name, tr2.rate, tr2.type, tr1.priority FROM " . DB_PREFIX . "tax_rule tr1 LEFT JOIN " . DB_PREFIX . "tax_rate tr2 ON (tr1.tax_rate_id = tr2.tax_rate_id) INNER JOIN " . DB_PREFIX . "tax_rate_to_customer_group tr2cg ON (tr2.tax_rate_id = tr2cg.tax_rate_id) LEFT JOIN " . DB_PREFIX . "zone_to_geo_zone z2gz ON (tr2.geo_zone_id = z2gz.geo_zone_id) LEFT JOIN " . DB_PREFIX . "geo_zone gz ON (tr2.geo_zone_id = gz.geo_zone_id) WHERE tr1.based = 'store' AND tr2cg.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND z2gz.country_id = '" . (int)$country_id . "' AND (z2gz.zone_id = '0' OR z2gz.zone_id = '" . (int)$zone_id . "') ORDER BY tr1.priority ASC");



		foreach ($tax_query->rows as $result) {

			$this->tax_rates[$result['tax_class_id']][$result['tax_rate_id']] = array(

				'tax_rate_id' => $result['tax_rate_id'],

				'name'        => $result['name'],

				'rate'        => $result['rate'],

				'type'        => $result['type'],

				'priority'    => $result['priority']

			);

		}

	}



	public function calculate($value, $tax_class_id, $calculate = true) {

		if ($tax_class_id && $calculate) {

			$amount = 0;



			$tax_rates = $this->getRates($value, $tax_class_id);



			foreach ($tax_rates as $tax_rate) {

				if ($calculate != 'P' && $calculate != 'F') {

					$amount += $tax_rate['amount'];

				} elseif ($tax_rate['type'] == $calculate) {

					$amount += $tax_rate['amount'];

				}

			}



			return $value + $amount;

		} else {

			return $value;

		}

	}



	public function getTax($value, $tax_class_id) {

		$amount = 0;



		$tax_rates = $this->getRates($value, $tax_class_id);



		foreach ($tax_rates as $tax_rate) {

			$amount += $tax_rate['amount'];

		}



		return $amount;

	}



	public function getRateName($tax_rate_id) {

		$tax_query = $this->db->query("SELECT name FROM " . DB_PREFIX . "tax_rate WHERE tax_rate_id = '" . (int)$tax_rate_id . "'");



		if ($tax_query->num_rows) {

			return $tax_query->row['name'];

		} else {

			return false;

		}

	}



	public function getRates($value, $tax_class_id) {

		$tax_rate_data = array();



		if (isset($this->tax_rates[$tax_class_id])) {

			foreach ($this->tax_rates[$tax_class_id] as $tax_rate) {

				if (isset($tax_rate_data[$tax_rate['tax_rate_id']])) {

					$amount = $tax_rate_data[$tax_rate['tax_rate_id']]['amount'];

				} else {

					$amount = 0;

				}



				if ($tax_rate['type'] == 'F') {

					$amount += $tax_rate['rate'];

				} elseif ($tax_rate['type'] == 'P') {

					$amount += ($value / 100 * $tax_rate['rate']);

				}



				$tax_rate_data[$tax_rate['tax_rate_id']] = array(

					'tax_rate_id' => $tax_rate['tax_rate_id'],

					'name'        => $tax_rate['name'],

					'rate'        => $tax_rate['rate'],

					'type'        => $tax_rate['type'],

					'amount'      => $amount

				);

			}

		}



		return $tax_rate_data;

	}

}

