<?php
class ModelExtensionTotalShipping extends Model {
	public function getTotal($total) {
		if ($this->cart->hasShipping() && isset($this->session->data['shipping_method'])) {

			$country_delivery_id = 0;

			if (isset($this->request->post['country_delivery_id'])) {
				$country_delivery_id = $this->session->data['customer']['country_delivery_id'];
			}

			if ($country_delivery_id) {
				$this->load->model('checkout/onepcheckout');
				$this->load->model('extension/shipping/easyship');
				$delivery_info = $this->model_checkout_onepcheckout->getCountryDelivery($country_delivery_id);
				$allowed_methods = json_decode($delivery_info['shipping_methods'], true);

				$totals 	= $this->model_extension_shipping_easyship->getTotals();
				$sub_total  = $totals['total'];

				$free_shipping= $allowed_methods[$this->session->data['shipping_method']['code']]['free_shipping'];

				if($free_shipping > 0 && $sub_total >= $free_shipping){
					$current_cost=0;
				}else{
					$current_cost=$allowed_methods[$this->session->data['shipping_method']['code']]['cost'];
				}

				if ($delivery_info && isset($current_cost) && $current_cost > 0) {
					$this->session->data['shipping_method']['cost'] = (float)$current_cost;
				}
			}

			$total['totals'][] = array(
				'code'       => 'shipping',
				'title'      => $this->session->data['shipping_method']['title'],
				'value'      => $this->session->data['shipping_method']['cost'],
				'sort_order' => $this->config->get('total_shipping_sort_order')
			);

			if ($this->session->data['shipping_method']['tax_class_id']) {
				$tax_rates = $this->tax->getRates($this->session->data['shipping_method']['cost'], $this->session->data['shipping_method']['tax_class_id']);

				foreach ($tax_rates as $tax_rate) {
					if (!isset($total['taxes'][$tax_rate['tax_rate_id']])) {
						$total['taxes'][$tax_rate['tax_rate_id']] = $tax_rate['amount'];
					} else {
						$total['taxes'][$tax_rate['tax_rate_id']] += $tax_rate['amount'];
					}
				}
			}

			$total['total'] += $this->session->data['shipping_method']['cost'];
		}
	}
}