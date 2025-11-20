<?php
class Opc_Rewrite {
    private $config;

    public function __construct($config) {
        $this->config = $config;
    }

    public function rewrite($url) {

        $get_route = isset($_GET['route']) ? $_GET['route'] : (isset($_GET['_route_']) ? $_GET['_route_'] : '');

        if ($this->config->get('config_show_onepcheckout') && strpos($url, 'checkout/checkout') && $get_route != 'checkout/checkout') {
            $url = str_replace('checkout/checkout', 'checkout/onepcheckout', $url);
        }

        return $url;
    }
}