<?php
class ModelExtensionFeedMerchantFeed extends Model {
    public function getTotalProduct($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store pts ON(p.product_id = pts.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category ptc ON(p.product_id = ptc.product_id) WHERE";
		
		$sql .= " p.status = 1 AND pd.language_id = '" . (int)$data['language_id'] . "' AND pts.store_id = '" . (int)$data['store_id'] . "'";
		
		if(!$data['product_stock']) {
			$sql .= " AND p.quantity > 0";
		}
		
		if(!empty($data['product_category'])) {
			$category_array = implode(",", $data['product_category']);
			
			$sql .= " AND ptc.category_id IN(" . $this->db->escape($category_array) . ")";
		}
		
		if(!empty($data['manufacturer_category'])) {
			$manufacturer_array = implode(",", $data['manufacturer_category']);
			
			$sql .= " AND p.manufacturer_id IN(" . $this->db->escape($manufacturer_array) . ")";
		}	
		
		if(!empty($data['product_black_list'])) {
			$black_list = implode(",", $data['product_black_list']);
			
			$sql .= " AND p.product_id NOT IN(" . $this->db->escape($black_list) . ")";
		}

		$query = $this->db->query($sql);
		
		if($query->num_rows) {
			return $query->row['total'];
		} else {
			return false;
		}
	}
	
	public function getProducts($data = array()) {
		$product_data = array();
		
		$sql = "SELECT DISTINCT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store pts ON(p.product_id = pts.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category ptc ON(p.product_id = ptc.product_id) WHERE";
		
		$sql .= " p.status = 1 AND pd.language_id = '" . (int)$data['language_id'] . "' AND pts.store_id = '" . (int)$data['store_id'] . "'";
		
		if(!$data['product_stock']) {
			$sql .= " AND p.quantity > 0";
		}
		
		if(!empty($data['product_category'])) {
			$category_array = implode(",", $data['product_category']);
			
			$sql .= " AND ptc.category_id IN(" . $this->db->escape($category_array) . ")";
		}
		
		if(!empty($data['manufacturer_category'])) {
			$manufacturer_array = implode(",", $data['manufacturer_category']);
			
			$sql .= " AND p.manufacturer_id IN(" . $this->db->escape($manufacturer_array) . ")";
		}	
		
		if(!empty($data['product_black_list'])) {
			$black_list = implode(",", $data['product_black_list']);
			
			$sql .= " AND p.product_id NOT IN(" . $this->db->escape($black_list) . ")";
		}	
		
		if(!empty($data['start']) && !empty($data['limit'])) {			
			$sql .= " LIMIT " . $data['start'] .', ' . $data['limit'];
		} elseif(empty($data['start']) && !empty($data['limit'])) {
			$sql .= " LIMIT 0, 10";
		}

		$query = $this->db->query($sql);

		foreach($query->rows as $product) {
			$product_size = array();
			$sizes = array();
			
			if($data['option_id'] || $data['attribute_id']) {
				$product_size = $this->getSizes($product['product_id'], $data);
			}
			
			if($product_size) {				
				$sizes = explode('/', $product_size);
				
				$i = 1;
				
				foreach($sizes as $size) {
					$product_data[$product['product_id'] . '_' . $i] = $this->getProduct($product['product_id'], $data, $size, $i);
					
					$i++;
				}
			} else {
				$product_data[$product['product_id']] = $this->getProduct($product['product_id'], $data);
			}
		}

		return $product_data;
	}
	
	public function getProduct($product_id, $data, $size = '', $i = '') {
		
		$part = '';
		
		if($data['product_gtin']) {
			$part .= 'p.' . $this->db->escape($data['product_gtin']) . ',';
		}
		
		if($data['product_mpn']) {
			$part .= 'p.' . $this->db->escape($data['product_mpn']) . ',';
		}
		
		$part .= " (SELECT m.name FROM " . DB_PREFIX . "manufacturer m WHERE m.manufacturer_id = p.manufacturer_id) AS manufacturer,";

		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, " . $part . " (SELECT m.name FROM " . DB_PREFIX . "manufacturer m WHERE m.manufacturer_id = p.manufacturer_id) AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT ptc2.category_id FROM " . DB_PREFIX . "product_to_category ptc2 WHERE ptc2.product_id = p.product_id ORDER BY ptc2.category_id DESC LIMIT 1) AS category_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$data['language_id']. "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {		
			if($i) {
				$i = '-' . $i;
			}
			
			return array(
					'product_id'       => $query->row['product_id'] . $i,
					'name'             => $query->row['name'] . ' ' . $size,
					'description'      => $query->row['description'],
					'meta_description' => $query->row['meta_description'],
					'category'	       => $this->getPathCategory($query->row['category_id'], $data['language_id']),
					'category_id'	   => $query->row['category_id'],
					'model'            => $query->row['model'],
					'quantity'         => $query->row['quantity'],
					'image'            => $query->row['image'],
					'manufacturer'     => $query->row['manufacturer'],
					'stock_status_id'  => $query->row['stock_status_id'],
					'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
					'special'          => $this->getSpecial($query->row['product_id']),
					'images'           => $this->getAddImage($query->row['product_id']),
					'gtin'         	   => $data['product_gtin'] ? $query->row[$data['product_gtin']] : '',
					'mpn'         	   => $data['product_mpn'] ? $query->row[$data['product_mpn']] : '',
					'size'         	   => $size,
				);
		} else {
			return false;
		}
	}
	
	private function getSizes($product_id, $data) {
		$sizes = '';
		
		if($data['option_id']) {
			$query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT ovd.name ORDER BY ovd.name SEPARATOR '/') AS size FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON(pov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.option_id = '" . (int)$data['option_id'] . "' AND ovd.language_id = '" . (int)$data['language_id']. "'");
			
			$sizes = $query->row['size'];
		} elseif($data['attribute_id']) {
			$query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT text ORDER BY text SEPARATOR '/') AS size FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$data['attribute_id'] . "' AND product_id = '" . (int)$product_id . "'");
			
			$sizes = $query->row['size'];
		}
		
		return $sizes;
	}
	
	private function getSpecial($product_id) {
		$query = $this->db->query("SELECT price, date_start, date_end FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");
		
		return $query->row;
	}
	
	private function getPathCategory($category_id, $language_id) {
		
		$query = $this->db->query("SELECT GROUP_CONCAT(cd1.name ORDER BY level SEPARATOR ' > ') AS category FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) WHERE cp.category_id = '" . (int)$category_id . "' AND cd1.language_id = '" . (int)$language_id . "' GROUP BY cp.category_id");

		if(isset($query->row['category'])) {
			return $query->row['category'];
		} else {
			return '';
		}
	}
	
	private function getAddImage($product_id) {
		$query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' LIMIT 10");
		
		return $query->rows;
	}
	
	private function fixRepleace($string) {
        $string = str_replace("&amp;lt;", "&lt;", $string);
        $string = str_replace("&amp;gt;", "&gt;", $string);
        $string = str_replace("&amp;quot;", "&quot;", $string);
        $string = str_replace("&amp;amp;", "&amp;", $string);
        $string = str_replace("&", "&amp;", $string);
        $string = str_replace("<", "&lt;", $string);
        $string = str_replace(">", "&gt;", $string);
        
		return $string;
    }
	
	public function getReviewsProduct() {
		$reviews_array = array();
		
		$this->load->model('catalog/product');
		
		$quary = $this->db->query("SELECT * FROM " . DB_PREFIX . "review WHERE `status` = '1'");
		
		foreach($quary->rows as $review) {
			$product_info = $this->model_catalog_product->getProduct($review['product_id']);
			
			if($product_info) {
				$reviews_array[] = array(
					'review_id'		=> $review['review_id'],
					'product_id'	=> $review['product_id'],
					'count'			=> $this->getCountReviewsByProductId($review['product_id']),
					'author'		=> $review['author'],
					'text'			=> $review['text'],
					//'minus'			=> $review['minus'],
					//'plus'			=> $review['plus'],
					'rating'		=> $review['rating'],
					'customer_id'	=> $review['customer_id'],
					'date_added'	=> $review['date_added'],
					'product_model'	=> $product_info['model'],
					'product_brand'	=> $product_info['manufacturer'],
					'product_name'	=> $product_info['name']
				);
			}
		}
		
		return $reviews_array;
	}
	
	private function getCountReviewsByProductId($product_id) {
		$query = $this->db->query("SELECT COUNT(*) AS count FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "'");
		
		return $query->row['count'];
	}
}