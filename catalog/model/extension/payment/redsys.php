<?php
class ModelExtensionPaymentRedsys extends Model {
    protected $name = 'redsys';

    public function getMethod() {
        $this->load->language('extension/payment/' . $this->name);
        $status = $this->config->get('payment_'. $this->name . '_status');
        $method_data = array();

        if(!$this->config->get('payment_redsys_env') &&
           $this->config->get('payment_redsys_test_customer_id')>0 &&
           $this->config->get('payment_redsys_test_customer_id')!=$this->session->data['customer_id'])
            return false;

        // Get Address Data (Model)
        $address = array();
        if (isset($this->session->data['payment_address_id']) && $this->session->data['payment_address_id']) { // Normal checkout
            $this->load->model('account/address');
            $address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
        } else { // Guest checkout
            $address = (isset($this->session->data['guest'])) ? $this->session->data['guest'] : array();
        }

        $country_id = (isset($address['country_id'])) ? $address['country_id'] : 0;
        $zone_id    = (isset($address['zone_id'])) ? $address['zone_id'] : 0;
        //
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_redsys_geo_zone_id') . "' AND country_id = '" . (int)$country_id . "' AND (zone_id = '" . (int)$zone_id . "' OR zone_id = '0')");
        if ($this->config->get('payment_redsys_geo_zone_id') && !$query->num_rows) {
            return $method_data;
        }



        if ($status) {
            $method_data = array(
                'code'       => $this->name,
                'title'      => $this->language->get('text_title'),
                'terms'      => '',
                'sort_order' => $this->config->get('payment_' . $this->name . '_sort_order')
            );
        }

        return $method_data;
    }
}
