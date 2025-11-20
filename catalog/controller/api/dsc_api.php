<?php
class ControllerApidscapi extends Controller {
	public function shippingMethods() {

			$shipping_methods = array();

			$this->load->model('setting/extension');

			$shipping_results = $this->model_setting_extension->getExtensions('shipping');

			foreach ($shipping_results as $result) {
				if ($this->config->get('shipping_' . $result['code'] . '_status')) {
					$this->load->model('extension/shipping/' . $result['code']);


						$quote = $this->{'model_extension_shipping_' . $result['code']}->getQuote(['country_id' => 170, 'geo_zone_id' => 6, 'zone_id' => 6]);


					if ($quote){
						$shipping_methods[$result['code']] = array(
							'title'       => $quote['title'],
							'quote'       => $quote['quote'],
							'sort_order'  => $quote['sort_order'],
							'error'       => $quote['error'],
							'geo_zone_id' => $this->config->get('shipping_' . $result['code'] . '_geo_zone_id')
						);
					}
				}
			}

			$sort_order = array();

			foreach ($shipping_methods as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $shipping_methods);

			if(!empty($shipping_methods)){
				foreach ($shipping_methods as $code => $shipping_method) {

					if($shipping_method['quote']){

						foreach ($shipping_method['quote'] as $quote => $shipping_result) {
							$this->load->language('extension/shipping/' . $code);

							$data['smethods'][] = array(
								's_method' 	=> $shipping_result['code'],
								'text' 	=> $shipping_method['title'],
								'geo_zone_id' 	=> $shipping_method['geo_zone_id'],
							);
						}
					}
				}
			}

			header('Content-Type: application/json');
			echo json_encode($data);
	}

	public function paymentMethods() {
			$data['payment_methods'] = array();

			$this->load->model('setting/extension');

			$results = $this->model_setting_extension->getExtensions('payment');

			$recurring = $this->cart->hasRecurringProducts();

			foreach ($results as $result) {
				if ($this->config->get('payment_' . $result['code'] . '_status')) {
					$this->load->language('extension/payment/' . $result['code']);

					$method_data = array();

					$method_data = array(
						'code'       => $result['code'],
						'title'      => $this->language->get('text_title'),
						'terms'      => '',
						'sort_order' => $this->config->get('payment_cod_sort_order'),
						'geo_zone_id' => $this->config->get('payment_' . $result['code'] . '_geo_zone_id')
					);

					$data['payment_methods'][] = $method_data;
				}
			}

			$sort_order = array();

			foreach ($data['payment_methods'] as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $data['payment_methods']);

			header('Content-Type: application/json');
			echo json_encode($data);
	}
}
