<?php
class ModelExtensionModuleCgp extends Model { // Customer Group Price
	public function getCustomerGroupPrice($product_id, $customer_group_id) {
		$query = "SELECT ps.price FROM " . DB_PREFIX . "product_special ps "
		. " LEFT JOIN " . DB_PREFIX . "customer_group cg "
		. " ON cg.customer_group_id = ps.customer_group_id "
		. " WHERE ps.product_id = '" . (int)$product_id . "' "
		. " AND ps.customer_group_id = '" . (int)$customer_group_id . "' "
		. " AND ((date_start = '0000-00-00' OR date_start = '' OR date_start < NOW()) "
		. " AND (date_end = '0000-00-00' OR date_end = '' OR date_end > NOW())) "
		. " ORDER BY priority ASC, price ASC LIMIT 1 ";
		
		$result = $this->db->query($query);
		
		if(count($result->rows) > 0)	
			return $result->row['price'];
		
		$query = "SELECT pd.price FROM " . DB_PREFIX . "product_discount pd "
		. " LEFT JOIN " . DB_PREFIX . "customer_group cg "
		. " ON cg.customer_group_id = pd.customer_group_id "
		. " WHERE pd.product_id = '" . (int)$product_id . "' "
		. " AND pd.customer_group_id = '" . (int)$customer_group_id . "' "
		. " AND quantity = 1 AND ((date_start = '0000-00-00' OR date_start = '' OR date_start < NOW()) "
		. " AND (date_end = '0000-00-00' OR date_end = '' OR date_end > NOW())) "
		. " ORDER BY quantity ASC, priority ASC, price ASC LIMIT 1 ";
		
		$result = $this->db->query($query);
		
		if(count($result->rows) > 0)	
			return $result->row['price'];
			
		return false;
	}
	
	public function getOtherCustomerGroupPrices($product_id, $current_customer_group_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group cg LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cg.sort_order ASC, cgd.name ASC");
		$customer_group_settings = $this->config->get('cgp_customer_group_settings');
		
		$results = array();

		foreach($customer_group_settings as &$customer_group_setting)
		{
			$show_as_reference_on_product_page = isset($customer_group_setting['show_as_reference_on_product_page']) ? $customer_group_setting['show_as_reference_on_product_page'] : false;
			
			if($show_as_reference_on_product_page)
			{
				foreach($query->rows as $row)
				{
					if($customer_group_setting['customer_group_id'] == $row['customer_group_id']
					&& $customer_group_setting['customer_group_id'] != $current_customer_group_id)
					{
						$results[] = array
						(
							'customer_group_id' => $row['customer_group_id'],
							'name' => $row['name'],
							'price' => $this->getCustomerGroupPrice($product_id, $row['customer_group_id']),
							'show_as_reference_on_product_page' => $show_as_reference_on_product_page,
							'sort_order' => $customer_group_setting['sort_order'],
							'use_custom_style' => isset($customer_group_setting['use_custom_style']) ? $customer_group_setting['use_custom_style'] : false,
							'text_color' => $customer_group_setting['text_color'],
							'apply_style_to_name' => isset($customer_group_setting['apply_style_to_name']) ? $customer_group_setting['apply_style_to_name'] : false,
							'apply_style_to_text' => isset($customer_group_setting['apply_style_to_text']) ? $customer_group_setting['apply_style_to_text'] : false
						);
					}
				}
			}
		}
		
		$sort_order = array();
		
		foreach ($results as $key => $result) {
			$sort_order[$key]  = $result['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $results);
		
		return $results;
	}
}
?>