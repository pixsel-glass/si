<?php 

/**
 * 
 */
class ControllerExtensionModuleTelnotification extends Controller
{
	// New order
	public function getOrderData($order_id) {

		$this->load->model('checkout/order');
		$this->load->model('account/customer');
		$this->load->model('account/address');
		$this->load->model('extension/module/telnotification');


		if($this->model_extension_module_telnotification->getStatus() && ($this->config->get('module_telnotification_neworder') == 1 || $this->config->get('module_telnotification_neworder') == 'on')) {

			// tax minus
			$rate = $this->config->get('module_pixsel_price_tax_rate');
			$tax_amount_minus = $rate / 100 + 1;

			$this->load->language('extension/module/telnotification');

			//Array with variables for substitution
			$substitution = ['{invoiceOrder}'   =>false, 
							 '{orderedBy}'		=>false,
							 '{products}'       =>false, 
							 '{recipientName}'  =>false, 
							 '{recipientGroup}' =>false, 
							 '{email}'          =>false,
							 '{telephone}'      =>false,
							 '{paymentMethod}'  =>false, 
							 '{shippingMethod}' =>false, 
							 '{orderStatus}'    =>false, 
							 '{orderSum}'    	=>false, 
							 '{shippingAddress}'=>false,
							 '{opcNotCallMe}'	=>false,
							 '{opcInfakt}'		=>false,
							 '{opcVat}'			=>false,
							 '{country}'		=>false,
							 '{comment}'		=>false,
							];

			//Get sample message
			$sample = $this->model_extension_module_telnotification->getMessage('message');

			//Message matching check array elements
			foreach ($substitution as $key => &$value) {
				if(stripos($sample, $key) !== false) {
					$value = true;
				}
			}

			unset($value);

			//Get order data
			$order_data = $this->model_checkout_order->getOrder($order_id);

			// {invoiceOrder}
			if($substitution['{invoiceOrder}']){
				$sample = str_replace('{invoiceOrder}', '<b>'.$this->language->get('text_norder').''.$order_id.'</b>', $sample);
			}
			// {orderedBy}
			$fc_customer_id = $order_data['fc_customer_id'];
			if($substitution['{orderedBy}'] && $fc_customer_id != 0) {
				$this->load->language('extension/module/telnotification');

				$this->load->model('account/customer');
				$customer_info = $this->model_account_customer->getCustomer($fc_customer_id);
				if ($customer_info['customer_type'] == 2) {
					$order_firstname = $customer_info['firstname'];
					$order_lastname = $customer_info['lastname'];
				} else {
					$order_firstname = $order_data['firstname'];
					$order_lastname = $order_data['lastname'];
				}

				$sample = str_replace('{orderedBy}', $this->language->get('text_ordered_by') . "\r\n" . '' . $order_firstname . ' ' . $order_lastname . "\r\n", $sample);
			} else {
				$sample = str_replace('{orderedBy}', '', $sample);
			}

			// SPACE
			
			// {recipientGroup}
			if($substitution['{recipientGroup}']){
				$this->load->model('account/customer');
				$this->load->model('account/customer_group');
				$this->load->language('extension/module/telnotification');
				$customer_info = $this->model_account_customer->getCustomer($order_data['customer_id']);
				if (!empty($customer_info) && $customer_info['customer_group_id'] > 0) {
					$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_info['customer_group_id']);
					if (($customer_info['customer_type'] == 1 || $customer_info['customer_type'] == 2) && !empty($customer_info['company_name']) && (!empty($order_data['infakt_nip']) || !empty($order_data['infakt_vatcode']))) {
						if (!empty($customer_info['company_name'])) {
							$company_name = $customer_info['company_name'];
						} else {
							$company_name = $customer_info['pl_company_name'];
						}

						if ($order_data['infakt_privat_faktyre'] == 0 && !empty($order_data['infakt_nip'])) {
							$nip = 'NIP: ' . $order_data['infakt_nip'] . '';
						}
		
						if ($order_data['infakt_privat_faktyre'] == 0 && $order_data['shipping_country_id'] != 170 && !empty($order_data['infakt_vatcode'])) {
							$nip = 'VAT: ' . $order_data['infakt_vatcode'] . '';
						}

						$sample = str_replace('{recipientGroup}', html_entity_decode($customer_group_info['name'], ENT_QUOTES, 'UTF-8') . "\r\n" . $company_name . "\r\n" . $nip, $sample);
					} else {
						$sample = str_replace('{recipientGroup}', html_entity_decode($customer_group_info['name'], ENT_QUOTES, 'UTF-8'), $sample);
					}

				} else {
					$sample = str_replace('{recipientGroup}', $this->language->get('customer_group_info_error'), $sample);
				}
			}
			// {recipientName}
			if($substitution['{recipientName}']) {
				$name = $order_data['firstname'] . " " . $order_data['lastname'];
				$sample = str_replace('{recipientName}', $name, $sample);
			}
			// {telephone}
			if($substitution['{telephone}']){
				$sample = str_replace('{telephone}', $order_data['telephone'], $sample);
			}
			// {email}
			if($substitution['{email}']){
				$sample = str_replace('{email}', $order_data['email'], $sample);
			}
			// {country}
			if($substitution['{country}']){
				$sample = str_replace('{country}', $order_data['shipping_country'] , $sample);
			}

			// SPACE

			// {shippingMethod}
			if($substitution['{shippingMethod}']){
				if ($order_data['shipping_code'] == 'inpost_shipping_1.inpost_shipping_1_6' || $order_data['shipping_code'] == 'inpost_shipping_2.inpost_shipping_2_6') {
					$order_data['shipping_method'] = $order_data['shipping_method'] . ' InPost';
				}
				if ($order_data['shipping_code'] == 'pickup.pickup') {
					$order_data['shipping_method'] = $this->language->get('text_odbor');
				}
				$sample = str_replace('{shippingMethod}',  "\r\n" . (!empty($order_data['parcelLocker']) ? strip_tags($order_data['shipping_method']) . ', ' . $order_data['parcelLocker'] : strip_tags($order_data['shipping_method']) ), $sample);
			}
			// {shippingAddress}
			$address = '';
			if($substitution['{shippingAddress}']){
				$address = (!empty($order_data['shipping_postcode'])? $order_data['shipping_postcode'] . ", " : '');
				$address .= (!empty($order_data['shipping_country'])? $order_data['shipping_country'] . ", " : '');
				$address .= (!empty($order_data['shipping_city'])? $order_data['shipping_city'] . ", " : '') . $order_data['shipping_address_1'];
				$address .= $order_data['shipping_address_2']? " (". $order_data['shipping_address_2'] . ")" : "";
				if ($order_data['shipping_code'] == 'pickup.pickup') {
					$address = '';
				} else {
					$address = rtrim($address, ',');
				}
				$sample = str_replace('{shippingAddress}', $address, $sample);
			}

			// SPACE

			// {paymentMethod}
			if($substitution['{paymentMethod}']){
				$sample = str_replace('{paymentMethod}',  (!empty($address) ? "\r\n" . $order_data['payment_method'] : $order_data['payment_method']), $sample);
			}

			// SPACE

			// {opcNotCallMe}
			if($substitution['{opcNotCallMe}'] && $order_data['opc_not_call_me'] == 0) {
				$this->load->language('extension/module/telnotification');
				$sample = str_replace('{opcNotCallMe}', "\r\n" . $this->language->get('text_call_me'), $sample);
			} else {
				$this->load->language('extension/module/telnotification');
				$sample = str_replace('{opcNotCallMe}',  "remove", $sample);
			}
			// {opcInfakt}
			if($substitution['{opcInfakt}'] && $order_data['infakt_need'] == 1) {
				$this->load->language('extension/module/telnotification');

				$fakt_info = '';
				if ($order_data['infakt_privat_faktyre'] == 1) {
					$fakt_info = ' (' . $this->language->get('text_privat') . ')';
				}

				if ($order_data['infakt_privat_faktyre'] == 0 && !empty($order_data['infakt_nip'])) {
					$fakt_info = ' (NIP: ' . $order_data['infakt_nip'] . ')';
				}

				if ($order_data['infakt_privat_faktyre'] == 0 && $order_data['shipping_country_id'] != 170 && !empty($order_data['infakt_vatcode'])) {
					$fakt_info = ' (VAT: ' . $order_data['infakt_vatcode'] . ')';
				}

				$sample = str_replace('{opcInfakt}', $this->language->get('text_infakt') . '' . $fakt_info, $sample);
			} else {
				$this->load->language('extension/module/telnotification');
				$sample = str_replace('{opcInfakt}', "remove", $sample);
			}
			// {opcVat}
			$this->load->model('account/customer');
			if ($this->customer->isLogged()){
				$client_info = $this->model_account_customer->getCustomer($this->customer->getId());

				// if ($client_info['dsc_vat'] == 0) {
				if ($order_data['infakt_vat'] == 0) {
					$this->load->language('extension/module/telnotification');
					$sample = str_replace('{opcVat}', $this->language->get('text_novat'), $sample);
				} else {
					$this->load->language('extension/module/telnotification');
					$sample = str_replace('{opcVat}', "remove", $sample);
				}
			} else {
				$this->load->language('extension/module/telnotification');
				$sample = str_replace('{opcVat}', "remove", $sample);
			}

			// SPACE

			// {products}
			if($substitution['{products}']){
				$products = $this->model_checkout_order->getOrderProducts($order_id);
				$products_string = "\r\n";

				$pr = 0;

				foreach ($products as $product) {

					$order_options = $this->model_checkout_order->getOrderOptions($order_id, $product['order_product_id']);

					if(count($order_options) > 0) {
						$option_sku = '';
						$options_str = '(';
						foreach ($order_options as $option) {
							$option_value = $this->model_checkout_order->getOrderOptionsValues($option['product_option_value_id']);

							$options_str .= $option['value'];
							$option_sku = $option_value['pixsel_sku'];
						}
						$options_str .= ')';

						$price_netto = $product['price'] / $tax_amount_minus;

						if ($pr > 0) {
							$products_string .= "\r\n";
						}

						$products_string .= $product['quantity'] . " x <b>" . $option_sku  . "</b> - " . $product['name'] . " " . $options_str . " — " . $this->currency->format($product['quantity']*$product['price'], $this->session->data['currency']) . " (" . $this->currency->format($product['quantity']*$price_netto, $this->session->data['currency']) . " " . $this->language->get('text_netto') . ")\r\n";
					} else {
						$price_netto = $product['price'] / $tax_amount_minus;
						$products_string .= $product['quantity'] . " x <b>" . $product['name'] . "</b> — " . $this->currency->format($product['quantity']*$product['price'], $this->session->data['currency']) . " (" . $this->currency->format($product['quantity']*$price_netto, $this->session->data['currency']) . " " . $this->language->get('text_netto') . ")\r\n";
					}

					$pr++;
				}
				$sample = str_replace('{products}', $products_string, $sample);
			}

			// SPACE

			// {orderSum}
			if($substitution['{orderSum}']){
				$total_netto = $order_data['total'] / $tax_amount_minus;

				$sample = str_replace('{orderSum}', "<b>" . $this->language->get('text_totalb') . '' . $this->currency->format(number_format((float)$order_data['total'], 2, '.', ''), $this->session->data['currency']) . "</b>\r\n" . $this->language->get('text_totaln') . '' . $this->currency->format(number_format((float)$total_netto, 2, '.', ''), $this->session->data['currency']) . "\r\n", $sample);
			}

			
			// {orderStatus}
			if($substitution['{orderStatus}']){
				$sample = str_replace('{orderStatus}', $order_data['order_status'], $sample);
			}

			// {comment}
			if($substitution['{comment}']){
				$sample = str_replace('{comment}', $order_data['comment'], $sample);
			}

			$sample_new = '';
			$sm_arr = preg_split("/\r\n|\n|\r/", $sample);
			for ($sm=0; $sm < count($sm_arr); $sm++) { 
				//if ($sm > 0) {
				//	if (!empty($sm_arr[$sm-1])) {
					if ($sm_arr[$sm] != 'remove') {
						$sample_new .= $sm_arr[$sm] . "\r\n";
					}
				//	}
				//} else {
				//	$sample_new .= $sm_arr[$sm];
				//}
			}

			$this->sendOrderToTelegram($sample_new);
		}
		
	}

	// Edit order
	public function getEditOrderData($data) {

		$order_id = $data['order_id'];
		$editedby = $data['editedby'];

		$this->load->model('checkout/order');
		$this->load->model('account/customer');
		$this->load->model('account/address');
		$this->load->model('extension/module/telnotification');


		if($this->model_extension_module_telnotification->getStatus() && ($this->config->get('module_telnotification_neworder') == 1 || $this->config->get('module_telnotification_neworder') == 'on')) {

			// tax minus
			$rate = $this->config->get('module_pixsel_price_tax_rate');
			$tax_amount_minus = $rate / 100 + 1;

			$this->load->language('extension/module/telnotification');

			//Array with variables for substitution
			$substitution = ['{invoiceOrder}'   =>false, 
							 '{orderedBy}'		=>false,
							 '{products}'       =>false, 
							 '{recipientName}'  =>false, 
							 '{recipientGroup}' =>false, 
							 '{email}'          =>false,
							 '{telephone}'      =>false,
							 '{paymentMethod}'  =>false, 
							 '{shippingMethod}' =>false, 
							 '{orderStatus}'    =>false, 
							 '{orderSum}'    	=>false, 
							 '{shippingAddress}'=>false,
							 '{opcNotCallMe}'	=>false,
							 '{opcInfakt}'		=>false,
							 '{opcVat}'			=>false,
							 '{country}'		=>false,
							 '{comment}'		=>false,
							];

			//Get sample message
			$sample = $this->model_extension_module_telnotification->getMessage('message');

			//Message matching check array elements
			foreach ($substitution as $key => &$value) {
				if(stripos($sample, $key) !== false) {
					$value = true;
				}
			}

			unset($value);

			//Get order data
			$order_data = $this->model_checkout_order->getOrder($order_id);

			// {invoiceOrder}
			if($substitution['{invoiceOrder}']){
				$sample = str_replace('{invoiceOrder}', '<b>№ '.$order_id.'</b>', $sample);
			}
			// {orderedBy}
			$sample = str_replace('{orderedBy}', '🔴 ' . $this->language->get('text_oedited_by') . "\r\n" . '' . $editedby . "\r\n", $sample);

			// SPACE
			
			// {recipientGroup}
			if($substitution['{recipientGroup}']){
				$this->load->model('account/customer');
				$this->load->model('account/customer_group');
				$this->load->language('extension/module/telnotification');
				$customer_info = $this->model_account_customer->getCustomer($order_data['customer_id']);
				if (!empty($customer_info) && $customer_info['customer_group_id'] > 0) {
					$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_info['customer_group_id']);
					if (($customer_info['customer_type'] == 1 || $customer_info['customer_type'] == 2) && !empty($customer_info['company_name']) && !empty($order_data['infakt_nip'])) {
						$sample = str_replace('{recipientGroup}', html_entity_decode($customer_group_info['name'], ENT_QUOTES, 'UTF-8') . "\r\n" . $customer_info['company_name'] . "\r\n" . 'NIP: ' . $order_data['infakt_nip'], $sample);
					} else {
						$sample = str_replace('{recipientGroup}', html_entity_decode($customer_group_info['name'], ENT_QUOTES, 'UTF-8'), $sample);
					}

				} else {
					$sample = str_replace('{recipientGroup}', $this->language->get('customer_group_info_error'), $sample);
				}
			}
			// {recipientName}
			if($substitution['{recipientName}']) {
				$name = $order_data['firstname'] . " " . $order_data['lastname'];
				$sample = str_replace('{recipientName}', $name, $sample);
			}
			// {telephone}
			if($substitution['{telephone}']){
				$sample = str_replace('{telephone}', $order_data['telephone'], $sample);
			}
			// {email}
			if($substitution['{email}']){
				$sample = str_replace('{email}', $order_data['email'], $sample);
			}
			// {country}
			if($substitution['{country}']){
				$sample = str_replace('{country}', $order_data['shipping_country'] , $sample);
			}

			// SPACE

			// {shippingMethod}
			if($substitution['{shippingMethod}']){
				if ($order_data['shipping_code'] == 'inpost_shipping_1.inpost_shipping_1_6' || $order_data['shipping_code'] == 'inpost_shipping_2.inpost_shipping_2_6') {
					$order_data['shipping_method'] = $order_data['shipping_method'] . ' InPost';
				}
				if ($order_data['shipping_code'] == 'pickup.pickup') {
					$order_data['shipping_method'] = $this->language->get('text_odbor');
				}
				$sample = str_replace('{shippingMethod}',  "\r\n" . (!empty($order_data['parcelLocker']) ? strip_tags($order_data['shipping_method']) . ', ' . $order_data['parcelLocker'] : strip_tags($order_data['shipping_method']) ), $sample);
			}
			// {shippingAddress}
			$address = '';
			if($substitution['{shippingAddress}']){
				$address = (!empty($order_data['shipping_postcode'])? $order_data['shipping_postcode'] . ", " : '');
				$address .= (!empty($order_data['shipping_country'])? $order_data['shipping_country'] . ", " : '');
				$address .= (!empty($order_data['shipping_city'])? $order_data['shipping_city'] . ", " : '') . $order_data['shipping_address_1'];
				$address .= $order_data['shipping_address_2']? " (". $order_data['shipping_address_2'] . ")" : "";
				if ($order_data['shipping_code'] == 'pickup.pickup') {
					$address = '';
				} else {
					$address = rtrim($address, ',');
				}
				$sample = str_replace('{shippingAddress}', $address, $sample);
			}

			// SPACE

			// {paymentMethod}
			if($substitution['{paymentMethod}']){
				$sample = str_replace('{paymentMethod}',  (!empty($address) ? "\r\n" . $order_data['payment_method'] : $order_data['payment_method']), $sample);
			}

			// SPACE

			// {opcNotCallMe}
			if($substitution['{opcNotCallMe}'] && $order_data['opc_not_call_me'] == 0) {
				$this->load->language('extension/module/telnotification');
				$sample = str_replace('{opcNotCallMe}', "\r\n" . $this->language->get('text_call_me'), $sample);
			} else {
				$this->load->language('extension/module/telnotification');
				$sample = str_replace('{opcNotCallMe}',  "remove", $sample);
			}
			// {opcInfakt}
			if($substitution['{opcInfakt}'] && $order_data['infakt_need'] == 1) {
				$this->load->language('extension/module/telnotification');

				$fakt_info = '';
				if ($order_data['infakt_privat_faktyre'] == 1) {
					$fakt_info = ' (' . $this->language->get('text_privat') . ')';
				}

				if ($order_data['infakt_privat_faktyre'] == 0 && !empty($order_data['infakt_nip'])) {
					$fakt_info = ' (NIP: ' . $order_data['infakt_nip'] . ')';
				}

				if ($order_data['infakt_privat_faktyre'] == 0 && $order_data['shipping_country_id'] != 170 && !empty($order_data['infakt_vatcode'])) {
					$fakt_info = ' (VAT: ' . $order_data['infakt_vatcode'] . ')';
				}

				$sample = str_replace('{opcInfakt}', $this->language->get('text_infakt') . '' . $fakt_info, $sample);
			} else {
				$this->load->language('extension/module/telnotification');
				$sample = str_replace('{opcInfakt}', "remove", $sample);
			}
			// {opcVat}
			$this->load->model('account/customer');
			if ($this->customer->isLogged()){
				$client_info = $this->model_account_customer->getCustomer($this->customer->getId());

				// if ($client_info['dsc_vat'] == 0) {
				if ($order_data['infakt_vat'] == 0) {
					$this->load->language('extension/module/telnotification');
					$sample = str_replace('{opcVat}', $this->language->get('text_novat'), $sample);
				} else {
					$this->load->language('extension/module/telnotification');
					$sample = str_replace('{opcVat}', "remove", $sample);
				}
			} else {
				$this->load->language('extension/module/telnotification');
				$sample = str_replace('{opcVat}', "remove", $sample);
			}

			// SPACE

			// {products}
			if($substitution['{products}']){
				$products = $this->model_checkout_order->getOrderProducts($order_id);
				$products_string = "\r\n";

				$pr = 0;

				foreach ($products as $product) {

					$order_options = $this->model_checkout_order->getOrderOptions($order_id, $product['order_product_id']);

					if(count($order_options) > 0) {
						$option_sku = '';
						$options_str = '(';
						foreach ($order_options as $option) {
							$option_value = $this->model_checkout_order->getOrderOptionsValues($option['product_option_value_id']);

							$options_str .= $option['value'];
							$option_sku = $option_value['pixsel_sku'];
						}
						$options_str .= ')';

						$price_netto = $product['price'] / $tax_amount_minus;

						if ($pr > 0) {
							$products_string .= "\r\n";
						}

						$products_string .= $product['quantity'] . " x <b>" . $option_sku  . "</b> - " . $product['name'] . " " . $options_str . " — " . $this->currency->format($product['quantity']*$product['price'], $this->session->data['currency']) . " (" . $this->currency->format($product['quantity']*$price_netto, $this->session->data['currency']) . " " . $this->language->get('text_netto') . ")\r\n";
					} else {
						$price_netto = $product['price'] / $tax_amount_minus;
						$products_string .= $product['quantity'] . " x <b>" . $product['name'] . "</b> — " . $this->currency->format($product['quantity']*$product['price'], $this->session->data['currency']) . " (" . $this->currency->format($product['quantity']*$price_netto, $this->session->data['currency']) . " " . $this->language->get('text_netto') . ")\r\n";
					}

					$pr++;
				}
				$sample = str_replace('{products}', $products_string, $sample);
			}

			// SPACE

			// {orderSum}
			if($substitution['{orderSum}']){
				$total_netto = $order_data['total'] / $tax_amount_minus;

				$sample = str_replace('{orderSum}', "<b>" . $this->language->get('text_totalb') . '' . $this->currency->format(number_format((float)$order_data['total'], 2, '.', ''), $this->session->data['currency']) . "</b>\r\n" . $this->language->get('text_totaln') . '' . $this->currency->format(number_format((float)$total_netto, 2, '.', ''), $this->session->data['currency']) . "\r\n", $sample);
			}

			
			// {orderStatus}
			if($substitution['{orderStatus}']){
				$sample = str_replace('{orderStatus}', $order_data['order_status'], $sample);
			}

			// {comment}
			if($substitution['{comment}']){
				$sample = str_replace('{comment}', $order_data['comment'], $sample);
			}

			$sample_new = '';
			$sm_arr = preg_split("/\r\n|\n|\r/", $sample);
			for ($sm=0; $sm < count($sm_arr); $sm++) { 
					if ($sm_arr[$sm] != 'remove') {
						$sample_new .= $sm_arr[$sm] . "\r\n";
					}
			}

			$this->sendOrderToTelegram($sample_new);
		}
		
	}

	// Fast order
	public function getFastOrderData($data=array()) {
		$this->load->model('extension/module/telnotification');

		if ($this->model_extension_module_telnotification->getStatus() && ($this->config->get('module_telnotification_quickorder') == 1 || $this->config->get('module_telnotification_quickorder') == 'on')) {
			$this->load->language('extension/module/telnotification');

			$this->load->model('catalog/product');
			// $product_info = $this->model_catalog_product->getProduct($data['product_id']);

			//Array with variables for substitution
			$substitution = ['{invoiceOrder}'   =>false, 
							 '{product}'        =>false, 
							 '{quantity}'       =>false, 
							 '{recipientName}'  =>false, 
							 '{email}'          =>false,
							 '{telephone}'      =>false,
							 '{comment}'       	=>false,
							 '{orderSum}'		=>false,
							];
			
			//Get sample message
			$sample = $this->model_extension_module_telnotification->getMessage('quickorder_message');

			//Message matching check array elements
			foreach ($substitution as $key => &$value) {
				if(stripos($sample, $key)!==false){
					$value = true;
				}
			}

			unset($value);

			if($substitution['{invoiceOrder}']){
				$sample = str_replace('{invoiceOrder}', '<b>'.$this->language->get('text_fnorder').''.$data['order_id'].'</b>', $sample);
			}			

			if($substitution['{product}']){
				// $sample = str_replace('{product}', $product_info['name'], $sample);
				$this->load->model('checkout/order');

				$products = $data['products'];
				$products_string = '';

				foreach ($products as $product) {

					$product_options = $product['option'];

					if(count($product_options) > 0) {
						$option_sku = '';
						$options_str = '(';
						foreach ($product_options as $option) {
							$option_values = $this->db->query('SELECT * FROM `oc_product_option_value` WHERE product_option_value_id="' . $option['product_option_value_id'] . '"');

							// file_put_contents("1111111111111.txt", print_r($option_values, TRUE));

							foreach($option_values->rows as $ovalue) {
								$options_str .= $option['value'];
								$option_sku = $ovalue['pixsel_sku'];
							}
						}
						$options_str .= ')';

						$products_string .= $product['quantity'] . " x <b>" . $option_sku  . "</b> - " . $product['name'] . " " . $options_str . " — " . $this->currency->format($product['quantity']*$product['price'], $this->session->data['currency']) . " " . $this->session->data['currency'] . "\r\n";
					} else {
						$products_string .= $product['quantity'] . " x <b>" . $product['name'] . "</b> — " . $this->currency->format($product['quantity']*$product['price'], $this->session->data['currency']) . " " . $this->session->data['currency'] . "\r\n";
					}
				}
				$sample = str_replace('{product}', $products_string, $sample);				
			}

			if($substitution['{quantity}']){
				$sample = str_replace('{quantity}', $data['quantity'], $sample);
			}

			if($substitution['{recipientName}']){
				$sample = str_replace('{recipientName}', $data['name'], $sample);
			}

			if($substitution['{email}']){
				$sample = str_replace('{email}', $data['email'], $sample);
			}

			if($substitution['{telephone}']){
				$sample = str_replace('{telephone}', $data['telephone'], $sample);
			}
			if($substitution['{comment}']){
				$sample = str_replace('{comment}', $data['comment'], $sample);
			}

			if($substitution['{orderSum}']){
				$sample = str_replace('{orderSum}', $this->language->get('text_total') . '' . $this->currency->format(number_format((float)$data['total'], 2, '.', ''), $this->session->data['currency']) . " " . $this->session->data['currency'], $sample);
			}			

			$this->sendOrderToTelegram($sample);
		}
	}

	// Callback
	public function getCallbackData($data=array()) {
		$this->load->model('extension/module/telnotification');

		if ($this->model_extension_module_telnotification->getStatus() && ($this->config->get('module_telnotification_callback') == 1 || $this->config->get('module_telnotification_callback') == 'on')) {

			//Array with variables for substitution
			$substitution = ['{name}'  =>false, 
							 '{telephone}'      =>false,
							 '{comment}'       =>false,
							];
			
			//Get sample message
			$sample = $this->model_extension_module_telnotification->getMessage('callback_message');

			//Message matching check array elements
			foreach ($substitution as $key => &$value) {
				if(stripos($sample, $key)!==false){
					$value = true;
				}
			}

			unset($value);

			if($substitution['{name}']){
				$sample = str_replace('{name}', $data['name'], $sample);
			}

			if($substitution['{telephone}']){
				$sample = str_replace('{telephone}', $data['telephone'], $sample);
			}
			if($substitution['{comment}']){
				$sample = str_replace('{comment}', $data['comment'], $sample);
			}			

			$this->sendOrderToTelegram($sample);
		}
	}

	// New user
	public function getRegisterData($data=array()) {
		$this->load->model('extension/module/telnotification');

		if ($this->model_extension_module_telnotification->getStatus() && ($this->config->get('module_telnotification_newuser') == 1 || $this->config->get('module_telnotification_newuser') == 'on')) {

			$this->load->language('extension/module/telnotification');

			//Array with variables for substitution
			$substitution = ['{type}'	   => false, 
							 '{name}'  	   => false, 
							 '{email}'     => false,
							 '{telephone}' => false,
							];
			
			//Get sample message
			$sample = $this->model_extension_module_telnotification->getMessage('newuser_message');

			//Message matching check array elements
			foreach ($substitution as $key => &$value) {
				if(stripos($sample, $key)!==false){
					$value = true;
				}
			}

			unset($value);

			if($substitution['{type}']) {
				$type = '';
				if ($data['type'] == 0) {
					$type = $this->language->get('text_privat');
				} else if ($data['type'] == 1) {
					$type = $this->language->get('text_firma');
				} else if ($data['type'] == 2) {
					$type = $this->language->get('text_worker');
				} else {
					$type = $this->language->get('text_privat');
				}

				$sample = str_replace('{type}', $type = $this->language->get('text_type') . ' ' . $type, $sample);
			}

			if($substitution['{name}']) {
				$sample = str_replace('{name}', $data['name'], $sample);
			}

			if($substitution['{email}']) {
				$sample = str_replace('{email}', $data['email'], $sample);
			}
			if($substitution['{telephone}']) {
				$sample = str_replace('{telephone}', $data['telephone'], $sample);
			}			

			$this->sendOrderToTelegram($sample);
		}
	}

	// Contact page
	public function getContactData($data=array()) {
		$this->load->model('extension/module/telnotification');

		if ($this->model_extension_module_telnotification->getStatus() && ($this->config->get('module_telnotification_contact') == 1 || $this->config->get('module_telnotification_contact') == 'on')) {

			//Array with variables for substitution
			$substitution = ['{name}'  =>false, 
							 '{email}'      =>false,
							 '{message}'       =>false,
							];
			
			//Get sample message
			$sample = $this->model_extension_module_telnotification->getMessage('contact_message');

			//Message matching check array elements
			foreach ($substitution as $key => &$value) {
				if(stripos($sample, $key)!==false){
					$value = true;
				}
			}

			unset($value);

			if($substitution['{name}']){
				$sample = str_replace('{name}', $data['name'], $sample);
			}

			if($substitution['{email}']){
				$sample = str_replace('{email}', $data['email'], $sample);
			}
			if($substitution['{message}']){
				$sample = str_replace('{message}', $data['message'], $sample);
			}

			$this->load->language('extension/module/telnotification');

			$sample = '<b>' . $this->language->get('text_contactus') . '</b>' . "\r\n" . $sample;

			$this->sendOrderToTelegram($sample);
		}
	}

	// Contact page
	public function getHistoryData($data=array()) {
		$this->load->model('extension/module/telnotification');

		if ($this->model_extension_module_telnotification->getStatus() && ($this->config->get('module_telnotification_orderstatus') == 1 || $this->config->get('module_telnotification_orderstatus') == 'on')) {

			//Array with variables for substitution
			$substitution = ['{invoiceOrder}'  =>false, 
							 '{orderStatus}'      =>false,
							 '{comment}'       =>false,
							];
			
			//Get sample message
			$sample = $this->model_extension_module_telnotification->getMessage('orderstatus_message');

			//Message matching check array elements
			foreach ($substitution as $key => &$value) {
				if(stripos($sample, $key)!==false){
					$value = true;
				}
			}

			unset($value);

			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder($data['order_id']);

			if($substitution['{invoiceOrder}']){
				$sample = str_replace('{invoiceOrder}', $data['order_id'], $sample);
			}

			if($substitution['{orderStatus}']){
				$sample = str_replace('{orderStatus}', $order_info['order_status'], $sample);
			}
			if($substitution['{comment}']){
				$sample = str_replace('{comment}', $data['comment'], $sample);
			}			

			$this->sendOrderToTelegram($sample);
		}
	}

	// Price level
	public function getPricelevelData($data=array()) {
		$this->load->model('extension/module/telnotification');

		$telnotStatus = $this->config->get('module_telnotification_status');

		if ($telnotStatus && ($this->config->get('module_telnotification_pricelevel') == 1 || $this->config->get('module_telnotification_pricelevel') == 'on')) {

			//Array with variables for substitution
			$substitution = ['{name}'  =>false, 
							 '{level}'      =>false,
							];
			
			//Get sample message
			$sample = $this->config->get('module_telnotification_pricelevel_message');

			//Message matching check array elements
			foreach ($substitution as $key => &$value) {
				if(stripos($sample, $key)!==false){
					$value = true;
				}
			}

			unset($value);

			if($substitution['{name}']){
				$sample = str_replace('{name}', $data['name'], $sample);
			}

			if($substitution['{level}']){
				$sample = str_replace('{level}', $data['level'], $sample);
			}

			$this->sendOrderToTelegramAdmin($sample);
		}
	}

	private function sendOrderToTelegram($order){
		$bot_token = $this->model_extension_module_telnotification->getBotId();
		$recipients = $this->model_extension_module_telnotification->getRecipients();
		
		foreach ($recipients as $recipient){
			$botdata['chat_id'] = $recipient['id'];
			$botdata['text'] = $order;
			$botdata['parse_mode']='html';

			$ch = curl_init("https://api.telegram.org/bot${bot_token}/sendMessage");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($botdata));

			$response= curl_exec($ch);
			curl_close($ch);
		}
	}

	private function sendOrderToTelegramAdmin($order){
		$bot_token = $this->config->get('module_telnotification_bot_id');
		$recipients = json_decode( $this->config->get('module_telnotification_recipients'), true);
		
		foreach ($recipients as $recipient){
			$botdata['chat_id'] = $recipient['id'];
			$botdata['text'] = $order;
			$botdata['parse_mode']='html';

			$ch = curl_init("https://api.telegram.org/bot${bot_token}/sendMessage");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($botdata));

			$response= curl_exec($ch);
			curl_close($ch);
		}
	}	
}