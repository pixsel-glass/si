<?php
class ControllerExtensionPaymentPrzelewyblik24 extends Controller {
    //public $version = '3.0.0';
    private $error = array();
    /*
    private $text_data = array(
        'heading_title'             ,
        'text_portmone'             ,
        'text_edit'                 ,
        'text_enabled'              ,
        'text_disabled'             ,
        'text_all_zones'            ,
        'text_pay'                  ,
        'text_card'                 ,
        'tab_general'               ,
        'tab_order_status'          ,
        'entry_status'              ,
        'h_entry_status'            ,
        'entry_payee_id'            ,
        'h_entry_payee_id'          ,
        'entry_login'               ,
        'h_entry_login'             ,
        'entry_pass'                ,
        'h_entry_pass'              ,
        'entry_order_stat'          ,
        'h_entry_order_stat'        ,
        'entry_order_stat_fa'       ,
        'h_entry_order_stat_fa'     ,
        'entry_order_stat_preauth'  ,
        'h_entry_order_stat_preauth',
        'entry_geo_zone'            ,
        'entry_preauth_flag'        ,
        'h_entry_preauth_flag'      ,
        'entry_showlogo'            ,
        'h_entry_showlogo'          ,
        'entry_sort_order'          ,
        'h_entry_sort_order'        ,
        'OP_version'                ,
        'Plugin_version'            ,
        'help_total'                ,
        'button_save'               ,
        'button_cancel'             ,
        'h_entry_name'              ,
        'entry_name'                ,
    );
    */
     private $error_data = array(
        'warning'   ,
        'key_order'  ,
        'key_api'     ,
        'key_crc'      ,
        'type'      ,
    );
    
    private $post_data = array(
        'status'                    ,
        'name'                      ,
        'key_order'                 ,
        'key_api'                   ,
        'key_crc'                   ,
        'order_stat_id'             ,
        'order_stat_fal_id'         ,
        //'order_stat_not_verified_id',
        //'order_stat_preauth_id'     ,
        //'entry_preauth_flag'        ,
        'entry_showlogo'            ,
        'sort_order'                ,
        'geo_zone_id'               ,
    );
    private $currency_add_uan = array (
        'title'         => 'Гривна',
        'code'          => 'UAN',
        'symbol_left'   => '₴' ,
        'symbol_right'  => 'грн' ,
        'decimal_place' => '2' ,
        'value'         => '0.00000000' ,
        'status'        => '0',
    );
    /*
    public $statuses = [
        'payment_portmone_order_stat_id'                => ['language_id' => 1,'name' => '<b style="color:#2abb1a;">(Portmone.com) Оплачен</b>'],
        'payment_portmone_order_stat_not_verified_id'   => ['language_id' => 1,'name' => '<b style="color:#13580b;">(Portmone.com) Оплачено (но не проверено)</b>'],
        'payment_portmone_order_stat_fal_id'            => ['language_id' => 1,'name' => '<b style="color:#ef0c0c;">(Portmone.com) Оплата не прошла</b>'],
        'payment_portmone_order_stat_preauth_id'        => ['language_id' => 1,'name' => '<b style="color:#ffd400;">(Portmone.com) Оплачено (блокировка средств)</b>']
    ];
    */
    public function __construct($registry) {
        parent::__construct($registry);
        $this->load->language('extension/payment/przelewyblik24');
        $this->document->setTitle($this->language->get('heading_title'));
    }

    public function index() {
        $this->load->language('extension/payment/przelewyblik24');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');
        $this->load->model('localisation/currency');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_przelewyblik24', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->makeUrl('extension/payment/przelewyblik24'));
        }

        //$data['entry_OP_version'] = VERSION;
        //$data['entry_Plugin_version'] = $this->version;

        // foreach ($this->text_data as $value) {
        //    $data[$value] = $this->language->get($value);
        // }

        /*
        $currency_uan = $this->model_localisation_currency->getCurrencyByCode('UAN');
        if(empty($currency_uan)){
            $this->currency_add_uan();
        }
        */

        foreach ($this->error_data as $value) {
            if (isset($this->error[$value])) {
                $data['error_'.$value] = $this->error[$value];
            } else {
                $data['error_'.$value] = '';
            }
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->makeUrl('common/dashboard')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->makeUrl('marketplace/extension')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->makeUrl('extension/payment/przelewyblik24')
        );

        if(!empty($this->session->data['success'])){
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $data['currencies'] = $this->model_localisation_currency->getCurrencies();
        $data['payment_przelewyblik24_currency'] = $this->config->get('payment_przelewyblik24_currency');

        $data['action'] = $this->makeUrl('extension/payment/przelewyblik24');
        $data['cancel'] = $this->makeUrl('marketplace/extension');
        $this->load->model('localisation/order_status');
        $this->load->model('localisation/geo_zone');

        $data['order_statuses']                 = $this->model_localisation_order_status->getOrderStatuses();
        $data['geo_zones']                      = $this->model_localisation_geo_zone->getGeoZones();

        foreach ($this->post_data as $value) {
            if (isset($this->request->post['payment_przelewyblik24_'.$value])) {
                $data['payment_przelewyblik24_'.$value] = $this->request->post['payment_przelewyblik24_'.$value];
            } else {
                $data['payment_przelewyblik24_'.$value] = $this->config->get('payment_przelewyblik24_'.$value);
            }
        }

        $payment_przelewyblik24_name_val = $this->config->get('payment_przelewyblik24_name');
        if(!isset($payment_przelewyblik24_name_val)){
            $data['payment_przelewyblik24_name'] = 'przelewy24.pl';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/payment/przelewyblik24', $data));
    }

    /*
    private function createOrderStatusesPortmone() {
        $this->model_extension_payment_portmone->updateTableStatusOrders();
        $this->model_extension_payment_portmone->addOrderStatus($this->statuses);
    }

    private function deleteOrderStatusesPortmone() {
        $this->model_extension_payment_portmone->deleteOrderStatus($this->statuses);
    }


    private function currency_add_uan() {
        $this->model_localisation_currency->addCurrency($this->currency_add_uan);
    }
    */

    
    public function install() {
        //$this->load->model('extension/payment/portmone');
        //$this->createOrderStatusesPortmone();
    }

    public function uninstall() {
        //$this->load->model('extension/payment/portmone');
        //$this->deleteOrderStatusesPortmone();
    }
    

    protected function makeUrl($route, $url = '') {
        return str_replace('&amp;', '&', $this->url->link($route, $url . '&user_token=' . $this->session->data['user_token'], 'SSL'));
    }

    protected function validate() {
        
        if (!$this->user->hasPermission('modify', 'extension/payment/przelewyblik24')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        
        if (!$this->request->post['payment_przelewyblik24_key_order']) {
            $this->error['key_order'] = $this->language->get('error_key_order');
        }
        if (!$this->request->post['payment_przelewyblik24_key_api']) {
            $this->error['key_api'] = $this->language->get('error_key_api');
        }
        if (!$this->request->post['payment_przelewyblik24_key_crc']) {
            $this->error['key_crc'] = $this->language->get('error_key_crc');
        }
        
        return !$this->error;
    }
}
