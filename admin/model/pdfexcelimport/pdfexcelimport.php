<?php
class ModelPdfexcelimportPdfexcelimport extends Model {

	public function getPriceSettings($prices_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "prices p WHERE p.prices_id = '" . (int)$prices_id . "'");

		return $query->row;
	}

	public function editPriceSettings($prices_id,  $data){
		$line = '';
		$count = 0;
		foreach ($data as $key => $value) {
			if($count!=0){
				$line .= ',';
			}
			$line .= $key."='".$value."'";
			$count = 1;
		}

        $query = $this->db->query("UPDATE " . DB_PREFIX . "prices SET ".$line." WHERE prices_id = '" . (int)$prices_id . "'");
	}

	public function addPriceSettings($prices_id,  $data){
		$line_keys = '(prices_id';
		$line_values = '("'.$prices_id.'"';
		foreach ($data as $key => $value) {
			$line_keys .= ',';
			$line_values .= ',';

			$line_keys .= $key;
			$line_values .= "'".$value."'";
		}
		$line_keys .= ')';
		$line_values .= ')';
		
        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "prices ".$line_keys." VALUES ".$line_values."");
	}
}