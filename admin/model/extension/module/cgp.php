<?php
class ModelExtensionModuleCgp extends Model { // Customer Group Price	
	public function getCustomerGroupSettings() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_group cg LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cg.sort_order ASC, cgd.name ASC");
		$customer_group_settings = $this->config->get('cgp_customer_group_settings');
		
		$results = array();
		
		foreach($query->rows as $row)
		{
			$results[$row['customer_group_id']] = array
			(
				'customer_group_id' => $row['customer_group_id'],
				'name' => $row['name'],
				'show_as_reference_on_product_page' => true,
				'sort_order' => 1,
				'use_custom_style' => false,
				'text_color' => '#000000',
				'apply_style_to_name' => false,
				'apply_style_to_text' => false
			);
		}
		
		if($customer_group_settings)
		{
			foreach($query->rows as $row)
			{
				foreach($customer_group_settings as $customer_group_setting)
				{
					if($customer_group_setting['customer_group_id'] == $row['customer_group_id'])
					{
						$results[$row['customer_group_id']] = array
						(
							'customer_group_id' => $row['customer_group_id'],
							'name' => $row['name'],
							'show_as_reference_on_product_page' => isset($customer_group_setting['show_as_reference_on_product_page']) ? $customer_group_setting['show_as_reference_on_product_page'] : false,
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
		
		foreach ($results as $key => $result) {
			$sort_order[$key]  = $result['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $results);
		
		return $results;
	}
}
?>