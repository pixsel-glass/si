<?php
class ControllerExtensionModulePriceProduct extends Controller {
    public function addProductToCart() {
        $cart_total = $this->cart->getTotal();
        $rules = $this->config->get('module_price_product_price_rules');

        $this->session->data['module_price_product_description'] = '';
        $this->session->data['module_price_product_free'] = '';

        $excluded_countries = explode(',', $this->config->get('module_price_product_excluded_countries'));

        // customer
        $type_customer = 0;

        if ($this->customer->isLogged()){
            $this->load->model('account/customer');

            $type_customer = !empty($this->customer->getCustomerType()) ? $this->customer->getCustomerType() : 0;
        }

        $cg_id = 0;
        if($type_customer == 2){
            if (isset($this->request->post['cg_id'])) {
                $cg_id = $this->request->post['cg_id'];
            } else {
                $cg_id = 0;
            }
        }
        // customer

        $in_rule = 0;

        $lang_code = "en-gb";
        $lngcode = $this->language->get('code');
        if ($lngcode == 'pl') {
            $lang_code = "pl-pl";
        }
        if ($lngcode == 'uk') {
            $lang_code = "uk-ua";
        }
        if ($lngcode == 'ru') {
            $lang_code = "ru-ru";
        }
        if ($lngcode == 'ee' || $lngcode == 'et') {
            $lang_code = "et-ee";
        }
        if ($lngcode == 'lv') {
            $lang_code = "lv-lv";
        }
        if ($lngcode == 'lt') {
            $lang_code = "lt-lt";
        }

        $currency = $this->session->data['currency'];

        foreach ($rules as $rule) {
            if ($currency != 'EUR') {
                $min_price = $rule['min_price']/$this->currency->getValue('EUR');
                $max_price = $rule['max_price']/$this->currency->getValue('EUR');

                if ($cart_total >= $min_price && $cart_total < $max_price && !in_array($this->session->data['shipping_address']['country_id'], $excluded_countries)) {
                    $this->removeRuleProduct();
                    $this->cart->add($rule['product_id'], 1, array('delivery'), 0, $cg_id);
                    // $this->session->data['module_price_product_description'] = $this->config->get('module_price_product_description')[$this->config->get('config_language_id')];        
                    // $this->session->data['module_price_product_description'] = sprintf($this->config->get('module_price_product_description')[$lang_code], $rule['max_price']);
                    if (count($this->cart->getProducts()) > 1) {
                        $data['module_price_product_description'] = $this->session->data['module_price_product_description'] = str_replace("%s", $this->currency->format($max_price, $currency), $this->config->get('module_price_product_description')[$lang_code]);

                        $data['module_price_product_free'] = $this->session->data['module_price_product_free'] = str_replace("%s", $this->currency->format($max_price, $currency), $this->config->get('module_price_product_free')[$lang_code]);

                        $in_rule = 1;
                    }
                    break;
                }
            } else {
                $min_price = $rule['min_price']*$this->currency->getValue('PLN');
                $max_price = $rule['max_price']/$this->currency->getValue('EUR');
                // echo ' -- '.$cart_total; echo ' -- '.$rule['min_price']; echo ' -- '.$rule['max_price'];
                // if ($cart_total >= $rule['min_price'] && $cart_total < $rule['max_price'] && !in_array($this->session->data['shipping_address']['country_id'], $excluded_countries)) {
                if ($cart_total >= $min_price && $cart_total < $max_price && !in_array($this->session->data['shipping_address']['country_id'], $excluded_countries)) {
                    $this->removeRuleProduct();
                    $this->cart->add($rule['product_id'], 1, array('delivery'), 0, $cg_id);
                    if (count($this->cart->getProducts()) > 1) {
                        $data['module_price_product_description'] = $this->session->data['module_price_product_description'] = str_replace("%s", $this->currency->format($rule['max_price'], $currency, '1'), $this->config->get('module_price_product_description')[$lang_code]);

                        $data['module_price_product_free'] = $this->session->data['module_price_product_free'] = str_replace("%s", $this->currency->format($rule['max_price'], $currency, '1'), $this->config->get('module_price_product_free')[$lang_code]);

                        $in_rule = 1;
                    }
                    break;
                }
            }
        }

        if ($in_rule == 0) {
            $this->removeRuleProduct();
        }

    }

    public function removeRuleProduct() {
        $rules = $this->config->get('module_price_product_price_rules');
        foreach ($this->cart->getProducts() as $product) {
            foreach ($rules as $rule) {
                if ($product['product_id'] == $rule['product_id']) {
                    $this->cart->remove($product['cart_id']);

                    break;
                }
            }
        }
    }
}