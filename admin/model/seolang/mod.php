<?php
/* All rights reserved belong to the module, the module developers https://support.opencartadmin.com */
// https://support.opencartadmin.com © 2011-2023 All Rights Reserved
// Distribution, without the author's consent is prohibited
// Commercial license
class ModelSeoLangMod extends Model
{
	public function getModId($code) {
		if (SC_VERSION > 15) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "modification WHERE code = '" . $this->db->escape($code)."'");
			if ($query->row) {
				return $query->row;
			} else {
				return false;
			}
		} else {
			$mod_mod['status'] = false;
			if (is_dir(DIR_SYSTEM . "../vqmod/xml")) {
		    	if (file_exists(DIR_SYSTEM . "../vqmod/xml/" . $code . ".ocmod.xml")) {
		    		$mod_mod['status'] = true;
		    	} else {
		    		$mod_mod['status'] = false;
		    	}
		    }
		    return $mod_mod;
		}
	}

}
