<?php

class ModelAccountCustomer extends Model {

	public function addCustomer($data) {

		// if (isset($data['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($data['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			if (isset($data['customer_group_id'])) {

			$customer_group_id = $data['customer_group_id'];

		} else {

			$customer_group_id = $this->config->get('config_customer_group_id');

		}



		$this->load->model('account/customer_group');



		$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);



		$this->db->query("INSERT INTO " . DB_PREFIX . "customer SET customer_group_id = '" . (int)$customer_group_id . "', store_id = '" . (int)$this->config->get('config_store_id') . "', language_id = '" . (int)$this->config->get('config_language_id') . "', firstname = '" . $this->db->escape($data['firstname']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "',  city = '" . $this->db->escape($data['city']) . "',  address_1 = '" . $this->db->escape($data['address_1']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['account']) ? json_encode($data['custom_field']['account']) : '') . "', salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', newsletter = '" . (isset($data['newsletter']) ? (int)$data['newsletter'] : 0) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', status = '" . (int)!$customer_group_info['approval'] . "', date_added = NOW()");



		$customer_id = $this->db->getLastId();



		$this->db->query("UPDATE " . DB_PREFIX . "customer SET customer_type = '" . (int)$data['customer_type'] . "', company_name = '" . (!empty($data['company_name']) ? $this->db->escape($data['company_name']) : $this->db->escape($data['pl_company_name'])) . "', company_nip = '" . $this->db->escape($data['company_nip']) . "', company_vatcode = '" . ( !empty($data['company_vatcode']) ? $this->db->escape($data['company_vatcode']) : '' ) . "', company_address = '" . $this->db->escape($data['company_address']) . "', company_country = '" . $this->db->escape($data['company_country']) . "', company_zip = '" . $this->db->escape($data['company_zip']) . "', company_region = '" . $this->db->escape($data['company_region']) . "', company_city = '" . $this->db->escape($data['company_city']) . "', pl_company_name = '" . (!empty($data['pl_company_name']) ? $this->db->escape($data['pl_company_name']) : $this->db->escape($data['company_name'])) . "', company_phone = '" . $this->db->escape($data['company_phone']) . "', company_nrreg = '" . $this->db->escape($data['company_nrreg']) . "' WHERE customer_id = '" . (int)$customer_id . "'");


		if ($customer_group_info['approval']) {

			$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_approval` SET customer_id = '" . (int)$customer_id . "', type = 'customer', date_added = NOW()");

		}



		return $customer_id;

	}



	public function editCustomer($customer_id, $data) {

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']['account']) ? json_encode($data['custom_field']['account']) : '') . "' WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET customer_type = '" . (int)$data['customer_type'] . "', company_name = '" . $this->db->escape($data['company_name']) . "', company_nip = '" . $this->db->escape($data['company_nip']) . "', company_vatcode = '" . ( !empty($data['company_nip']) ? $this->db->escape($data['company_vatcode']) : '' ) . "', company_address = '" . $this->db->escape($data['company_address']) . "', company_zip = '" . $this->db->escape($data['company_zip']) . "', company_country = '" . $this->db->escape($data['company_country']) . "', company_region = '" . $this->db->escape($data['company_region']) . "', company_city = '" . $this->db->escape($data['company_city']) . "', company_phone = '" . $this->db->escape($data['company_phone']) . "', company_nrreg = '" . $this->db->escape($data['company_nrreg']) . "', pl_company_name = '" . $this->db->escape($data['pl_company_name']) . "', invoice_banks = '" . $this->db->escape(isset($data['invoice_banks']) ? json_encode($data['invoice_banks']) : json_encode(array())) . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function editDefaultSettingCustomer($customer_id, $data) {

		if(isset($this->session->data['guest'])){
			unset($this->session->data['guest']);
		}
		if(isset($this->session->data['customer'])){
			unset($this->session->data['customer']);
		}
		if(isset($this->session->data['payment_method'])){
			unset($this->session->data['payment_method']);
		}
		if(isset($this->session->data['shipping_address'])){
			unset($this->session->data['shipping_address']);
		}
		if(isset($this->session->data['payment_address'])){
			unset($this->session->data['payment_address']);
		}
		if(isset($this->session->data['sm_address'])){
			unset($this->session->data['sm_address']);
		}

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET city = '" . $this->db->escape($data['city']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', zip = '" . $this->db->escape($data['zip']) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', dsc_status = '" . (int)$data['dsc_status']. "', dsc_firstname = '" . $this->db->escape($data['dsc_firstname']) . "', dsc_lastname = '" . $this->db->escape($data['dsc_lastname']) . "', dsc_telephone = '" . $this->db->escape($data['dsc_telephone']) . "', dsc_payment_method = '" . $this->db->escape($data['dsc_payment_method']) . "', dsc_shipping_method = '" . $this->db->escape($data['dsc_shipping_method']) . "', dsc_city = '" . $this->db->escape($data['dsc_city']) . "', dsc_postcode = '" . $this->db->escape($data['dsc_postcode']) . "', dsc_address_1 = '" . $this->db->escape($data['dsc_address_1']) . "', dsc_opc_not_call_me = '" . (int)$data['dsc_opc_not_call_me'] . "', dsc_country = '" . (int)$data['dsc_country'] . "', dsc_zone = '" . (int)$data['dsc_zone'] . "', dsc_parcelLocker = '" . $this->db->escape($data['parcelLocker']) . "', dsc_parcelAddressLocker = '" . $this->db->escape($data['parcelAddressLocker']) . "', dsc_currency = '" . $data['dsc_currency'] . "', dsc_language = '" . $data['dsc_language'] . "', dsc_vat = '" . $data['dsc_vat'] . "', dsc_faktyre = '" . (isset($data['dsc_faktyre']) ? (int)$this->db->escape($data['dsc_faktyre']) : '0') . "', dsc_privat_faktyre = '" . (isset($data['dsc_privat_faktyre']) ? (int)$this->db->escape($data['dsc_privat_faktyre']) : '0') . "' WHERE customer_id = '" . (int)$customer_id . "'");

		if (isset($data['dsc_nip'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET dsc_nip = '" . $this->db->escape($data['dsc_nip']) . "' WHERE customer_id = '" . (int)$customer_id . "'");	
		}

		if (isset($data['dsc_vatcode'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET dsc_vatcode = '" . $this->db->escape($data['dsc_vatcode']) . "' WHERE customer_id = '" . (int)$customer_id . "'");	
		}

		$customer_info_old = $this->getCustomer($customer_id);
		$customer_group_id_old = $customer_info_old['customer_group_id'];
		$customer_group_id_new = (int)$data['customer_group_id'];
		if (isset($data['customer_group_id'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET customer_group_id = '" . (int)$data['customer_group_id'] . "' WHERE customer_id = '" . (int)$customer_id . "'");	

			if ($customer_group_id_new != $customer_group_id_old) {
				$this->load->model('account/customer_group');
    			$customer_group_new = $this->model_account_customer_group->getCustomerGroup($customer_group_id_new);
				$this->load->controller('extension/module/telnotification/getPricelevelData', array('name'=>html_entity_decode($data['firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($data['lastname'], ENT_QUOTES, 'UTF-8'),'level'=>html_entity_decode($customer_group_new['name'], ENT_QUOTES, 'UTF-8')));
			}
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET customer_group_id = '" . (isset($data['customer_group_id']) ? (int)$data['customer_group_id'] : '1') . "' WHERE customer_id = '" . (int)$customer_id . "'");
		}
		if (isset($data['customer_type'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET customer_type = '" . (int)$data['customer_type'] . "' WHERE customer_id = '" . (int)$customer_id . "'");	
		}

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET country_id = '" . (isset($data['country_id']) ? (int)$data['country_id'] : '') . "', zone_id = '" . (isset($data['zone_id']) ? (int)$data['zone_id'] : '') . "', company_nip = '" . (isset($data['company_nip']) ? $this->db->escape($data['company_nip']) : '') . "', company_vatcode = '" . ( isset($data['company_vatcode']) ? $this->db->escape($data['company_vatcode']) : '') . "', company_nrreg = '" . ( isset($data['company_nrreg']) ? $this->db->escape($data['company_nrreg']) : '') . "', company_bank = '" . (isset($data['company_bank']) ? $this->db->escape($data['company_bank']) : '') . "', company_name = '" . (isset($data['company_name']) ? $this->db->escape($data['company_name']) : '') . "', pl_company_name = '" . (isset($data['pl_company_name']) ? $this->db->escape($data['pl_company_name']) : '') . "' WHERE customer_id = '" . (int)$customer_id . "'");


		$this->db->query("UPDATE " . DB_PREFIX . "customer SET company_country = '" . (isset($data['company_country']) ? $this->db->escape($data['company_country']) : '') . "' WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET company_region = '" . (isset($data['company_region']) ? $this->db->escape($data['company_region']) : '') . "' WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET company_city = '" . (isset($data['company_city']) ? $this->db->escape($data['company_city']) : '') . "' WHERE customer_id = '" . (int)$customer_id . "'");

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET company_address = '" . (isset($data['company_address']) ? $this->db->escape($data['company_address']) : '') . "' WHERE customer_id = '" . (int)$customer_id . "'");

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET company_phone = '" . (isset($data['company_phone']) ? $this->db->escape($data['company_phone']) : '') . "' WHERE customer_id = '" . (int)$customer_id . "'");

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET company_nrreg = '" . (isset($data['company_nrreg']) ? $this->db->escape($data['company_nrreg']) : '') . "' WHERE customer_id = '" . (int)$customer_id . "'");

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET company_zip = '" . (isset($data['company_zip']) ? $this->db->escape($data['company_zip']) : '') . "' WHERE customer_id = '" . (int)$customer_id . "'");

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET invoice_banks = '" . $this->db->escape(isset($data['invoice_banks']) ? json_encode($data['invoice_banks']) : json_encode(array())) . "' WHERE customer_id = '" . (int)$customer_id . "'");
		

		if (!empty($data['password'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET  salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', code = '' WHERE customer_id = '" . (int)$customer_id . "'");	
		}

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET language_id = '" . (int)$data['language_id'] . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}


	public function editPassword($email, $password) {

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "', code = '' WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

	}


	public function editPasswordApi($email, $password) {

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "', code = '' WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

	}


	public function deleteCustomer($customer_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_activity WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_affiliate WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_approval WHERE customer_id = '" . (int)$customer_id . "'");
 		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_history WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_ip WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");
	}


	public function editAddressId($customer_id, $address_id) {

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");

	}



	public function editCode($email, $code) {

		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET code = '" . $this->db->escape($code) . "' WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

	}



	public function editNewsletter($newsletter) {

		$this->db->query("UPDATE " . DB_PREFIX . "customer SET newsletter = '" . (int)$newsletter . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");

	}



	public function getCustomer($customer_id) {

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");



		return $query->row;

	}



	public function getCustomerByEmail($email) {

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");



		return $query->row;

	}



	public function getCustomerByCode($code) {

		$query = $this->db->query("SELECT customer_id, firstname, lastname, email FROM `" . DB_PREFIX . "customer` WHERE code = '" . $this->db->escape($code) . "' AND code != ''");



		return $query->row;

	}



	public function getCustomerByToken($token) {

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE token = '" . $this->db->escape($token) . "' AND token != ''");



		$this->db->query("UPDATE " . DB_PREFIX . "customer SET token = ''");



		return $query->row;

	}



	public function getTotalCustomersByEmail($email) {

		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");



		return $query->row['total'];

	}

	public function getTotalCustomersByTelephone($telephone) {
		$telephone = str_replace([' ', '+', '-', '(', ')'], '', $telephone);

		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer WHERE REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(telephone, ' ', ''), '+', ''), '-', ''), '(', ''), ')', '') = '" . $this->db->escape($telephone) . "'");

		return $query->row['total'];
	}

	public function addTransaction($customer_id, $description, $amount = '', $order_id = 0) {

		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" . (int)$customer_id . "', order_id = '" . (float)$order_id . "', description = '" . $this->db->escape($description) . "', amount = '" . (float)$amount . "', date_added = NOW()");

	}



	public function deleteTransactionByOrderId($order_id) {

		$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");

	}



	public function getTransactionTotal($customer_id) {

		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "customer_transaction WHERE customer_id = '" . (int)$customer_id . "'");



		return $query->row['total'];

	}



	public function getTotalTransactionsByOrderId($order_id) {

		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");



		return $query->row['total'];

	}



	public function getRewardTotal($customer_id) {

		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "customer_reward WHERE customer_id = '" . (int)$customer_id . "'");



		return $query->row['total'];

	}



	public function getIps($customer_id) {

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_ip` WHERE customer_id = '" . (int)$customer_id . "'");



		return $query->rows;

	}



	public function addLoginAttempt($email) {

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_login WHERE email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");



		if (!$query->num_rows) {

			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_login SET email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', total = 1, date_added = '" . $this->db->escape(date('Y-m-d H:i:s')) . "', date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'");

		} else {

			$this->db->query("UPDATE " . DB_PREFIX . "customer_login SET total = (total + 1), date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE customer_login_id = '" . (int)$query->row['customer_login_id'] . "'");

		}

	}



	public function getLoginAttempts($email) {

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");



		return $query->row;

	}



	public function deleteLoginAttempts($email) {

		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");

	}



	public function addAffiliate($customer_id, $data) {

		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_affiliate SET `customer_id` = '" . (int)$customer_id . "', `company` = '" . $this->db->escape($data['company']) . "', `website` = '" . $this->db->escape($data['website']) . "', `tracking` = '" . $this->db->escape(token(64)) . "', `commission` = '" . (float)$this->config->get('config_affiliate_commission') . "', `tax` = '" . $this->db->escape($data['tax']) . "', `payment` = '" . $this->db->escape($data['payment']) . "', `cheque` = '" . $this->db->escape($data['cheque']) . "', `paypal` = '" . $this->db->escape($data['paypal']) . "', `bank_name` = '" . $this->db->escape($data['bank_name']) . "', `bank_branch_number` = '" . $this->db->escape($data['bank_branch_number']) . "', `bank_swift_code` = '" . $this->db->escape($data['bank_swift_code']) . "', `bank_account_name` = '" . $this->db->escape($data['bank_account_name']) . "', `bank_account_number` = '" . $this->db->escape($data['bank_account_number']) . "', `status` = '" . (int)!$this->config->get('config_affiliate_approval') . "'");



		if ($this->config->get('config_affiliate_approval')) {

			$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_approval` SET customer_id = '" . (int)$customer_id . "', type = 'affiliate', date_added = NOW()");

		}

	}



	public function editAffiliate($customer_id, $data) {

		$this->db->query("UPDATE " . DB_PREFIX . "customer_affiliate SET `company` = '" . $this->db->escape($data['company']) . "', `website` = '" . $this->db->escape($data['website']) . "', `commission` = '" . (float)$this->config->get('config_affiliate_commission') . "', `tax` = '" . $this->db->escape($data['tax']) . "', `payment` = '" . $this->db->escape($data['payment']) . "', `cheque` = '" . $this->db->escape($data['cheque']) . "', `paypal` = '" . $this->db->escape($data['paypal']) . "', `bank_name` = '" . $this->db->escape($data['bank_name']) . "', `bank_branch_number` = '" . $this->db->escape($data['bank_branch_number']) . "', `bank_swift_code` = '" . $this->db->escape($data['bank_swift_code']) . "', `bank_account_name` = '" . $this->db->escape($data['bank_account_name']) . "', `bank_account_number` = '" . $this->db->escape($data['bank_account_number']) . "' WHERE `customer_id` = '" . (int)$customer_id . "'");

	}



	public function getAffiliate($customer_id) {

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_affiliate` WHERE `customer_id` = '" . (int)$customer_id . "'");



		return $query->row;

	}



	public function getAffiliateByTracking($tracking) {

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_affiliate` WHERE `tracking` = '" . $this->db->escape($tracking) . "'");



		return $query->row;

	}


	public function getCustomers($data = array()) {

		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, c.company_name, cgd.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id)";



		if (!empty($data['filter_affiliate'])) {

			$sql .= " LEFT JOIN " . DB_PREFIX . "customer_affiliate ca ON (c.customer_id = ca.customer_id)";

		}



		$sql .= " WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";



		$implode = array();



		if (!empty($data['filter_name'])) {

			$implode[] = "CONCAT(c.firstname, ' ', c.lastname, ' ', c.company_name) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";

		}



		if (!empty($data['filter_email'])) {

			$implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";

		}



		if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {

			$implode[] = "c.newsletter = '" . (int)$data['filter_newsletter'] . "'";

		}



		if (!empty($data['filter_customer_group_id'])) {

			$implode[] = "c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";

		}



		if (!empty($data['filter_affiliate'])) {

			$implode[] = "ca.status = '" . (int)$data['filter_affiliate'] . "'";

		}



		if (!empty($data['filter_ip'])) {

			$implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";

		}



		if (isset($data['filter_status']) && $data['filter_status'] !== '') {

			$implode[] = "c.status = '" . (int)$data['filter_status'] . "'";

		}



		if (!empty($data['filter_date_added'])) {

			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";

		}



		if ($implode) {

			$sql .= " AND " . implode(" AND ", $implode);

		}



		$sort_data = array(

			'name',

			'c.email',

			'customer_group',

			'c.status',

			'c.ip',

			'c.date_added'

		);



		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {

			$sql .= " ORDER BY " . $data['sort'];

		} else {

			$sql .= " ORDER BY name";

		}



		if (isset($data['order']) && ($data['order'] == 'DESC')) {

			$sql .= " DESC";

		} else {

			$sql .= " ASC";

		}



		if (isset($data['start']) || isset($data['limit'])) {

			if ($data['start'] < 0) {

				$data['start'] = 0;

			}



			if ($data['limit'] < 1) {

				$data['limit'] = 20;

			}



			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];

		}



		$query = $this->db->query($sql);



		return $query->rows;

	}

}