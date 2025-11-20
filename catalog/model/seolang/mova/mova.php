<?php
/* All rights reserved belong to the module, the module developers https://support.opencartadmin.com */
// https://support.opencartadmin.com © 2011-2023 All Rights Reserved
// Distribution, without the author's consent is prohibited
// Commercial license
if (!class_exists('ModelSeoLangMovaMova', false)) {
    class ModelSeoLangMovaMova extends Model {
        public function getCustomerOrder($customer_id, $order_status_array, $product_id) {
            if (!is_array($order_status_array)) {
                $order_status_array = array();
            }
            if (!empty($order_status_array)) {
                // safe, array_filter return int becouse product_id is only int for generation string from array it is not known what 
                $order_status_str = implode(',', array_filter($order_status_array, function ($v) {
                    return (int) $v;
                }));

                $sql = "SELECT
					op.product_id as product_id
					FROM `" . DB_PREFIX . "order` o
					LEFT JOIN `" . DB_PREFIX . "order_product` op ON (o.order_id = op.order_id)
					WHERE
					o.customer_id = '" . (int) $customer_id . "'
					AND o.order_status_id IN (" . $this->db->escape($order_status_str) . ")";
                if ($product_id > 0) {
                    $sql_product = " AND op.product_id = '" . (int) $product_id . "'";
                } else {
                    $sql_product = '';
                }

                $query = $this->db->query($sql . $sql_product . "LIMIT 1");

                if ($query->rows) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }
}
