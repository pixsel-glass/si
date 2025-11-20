<?php
class ControllerExtensionPaymentRedsys extends Controller {
    protected $error = array();
    protected $name = null;

    public function __construct( $registry ) {
        parent::__construct( $registry );
        $this->name = basename( __FILE__, '.php' );
        $this->type = 'payment';
    }

    public function index() {

        /* START ERRORS */
        $errors = array(
            'warning'
        );
        /* END ERRORS */


        /* START COMMON STUFF */
        $data            = array();
        $data            = array_merge( $data, $this->load->language( 'extension/payment/' . $this->name ) );
        $extension_route = 'marketplace/extension';

        $this->document->setTitle( $this->language->get( 'heading_title' ) );

        $settings_model = $this->getSettingsModel();

        if ( ( $this->request->server['REQUEST_METHOD'] == 'POST' ) && ( $this->validate( $errors ) ) ) {
            foreach ( $this->request->post as $key => $value ) {
                if ( is_array( $value ) ) {
                    $this->request->post[ $key ] = implode( ',', $value );
                }
            }
            $settings_model->editSetting( $this->type . '_' . $this->name, $this->request->post );
            $this->session->data['success'] = $this->language->get( 'text_success' );
            $this->response->redirect( $this->_sessionlink( $extension_route, 'type=' . $this->type ) );
        }

        $data['text_edit']   = $this->language->get( 'text_edit' );
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'href'      => $this->_sessionlink( 'common/home' ),
            'text'      => $this->language->get( 'text_home' ),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'href'      => $this->_sessionlink( $extension_route, 'type=' . $this->type ),
            'text'      => $this->language->get( 'text_' . $this->type ),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'href'      => $this->_sessionlink( 'extension/payment/' . $this->name ),
            'text'      => $this->language->get( 'heading_title' ),
            'separator' => ' :: '
        );
        if ( ! isset( $this->request->get['module_id'] ) ) {
            $data['action'] = $this->_sessionlink( 'extension/' . $this->type . '/' . $this->name );
        } else {
            $data['action'] = $this->_sessionlink( 'extension/' . $this->type . '/' . $this->name, 'module_id=' . $this->request->get['module_id'] );
        }
        $data['cancel'] = $this->_sessionlink( $extension_route, 'type=' . $this->type );

        /* 14x backwards compatibility */
        if ( method_exists( $this->document, 'addBreadcrumb' ) ) { //1.4.x
            $this->document->breadcrumbs = $data['breadcrumbs'];
            unset( $data['breadcrumbs'] );
        }//

        $this->children = array(
            'common/header',
            'common/footer'
        );

        foreach ( $errors as $error ) {
            if ( isset( $this->error[ $error ] ) ) {
                $data[ 'error_' . $error ] = $this->error[ $error ];
            } else {
                $data[ 'error_' . $error ] = '';
            }
        }
        /* END COMMON STUFF */


        /* START FIELDS */
        $this->addFields( $data );
        /* END FIELDS */

        $data['header']      = $this->load->controller( 'common/header' );
        $data['column_left'] = $this->load->controller( 'common/column_left' );
        $data['footer']      = $this->load->controller( 'common/footer' );
        $this->response->setOutput( $this->load->view( 'extension/payment/redsys', $data ) );

    }


    protected function getSettingsModel() {
        $this->load->model( 'setting/setting' );
        $this->_settings_model = $this->model_setting_setting;

        return $this->_settings_model;
    }

    protected function validate( $errors = array() ) {
        if ( ! $this->user->hasPermission( 'modify', 'extension/payment/' . $this->name ) ) {
            $this->error['warning'] = $this->language->get( 'error_permission' );
        }

        foreach ( $errors as $error ) {
            if ( isset( $this->request->post[ $this->name . '_' . $error ] ) && ! $this->request->post[ $this->name . '_' . $error ] ) {
                $this->error[ $error ] = $this->language->get( 'error_' . $error );
            }
        }

        if ( ! $this->error ) {
            return true;
        } else {
            return false;
        }
    }

    public function isCore() {
        return $this->name == basename( __FILE__, '.php' );
    }

    private function _sessionlink( $route, $params = '' ) {
        $token_name = 'user_token';
        if ( isset( $this->session->data[ $token_name ] ) ) {
            $token_val = $this->session->data[ $token_name ];
        } else {
            $token_val = 0;
        }

        return $this->url->link( $route, $token_name . '=' . $token_val . '&' . $params, 'SSL' );
    }

    protected function addFields( &$data ) {
        $data['extension_class'] = 'payment';
        $data['tab_class']       = 'htabs';

        $order_statuses = array();

        $this->load->model( 'localisation/order_status' );

        foreach ( $this->model_localisation_order_status->getOrderStatuses() as $order_status ) {
            $order_statuses[ $order_status['order_status_id'] ] = $order_status['name'];
        }

        $this->load->model('localisation/geo_zone');

        $geo_zones[0] = $this->language->get('text_all_zones');
        foreach ($this->model_localisation_geo_zone->getGeoZones() as $geozone) {
            $geo_zones[$geozone['geo_zone_id']] = $geozone['name'];
        }

        // Reload the language as the individual payment loading will overwrite some data
        $data = array_merge( $data, $this->load->language( 'extension/payment/' . $this->name ) );

        $data['tabs'] = array();

        $data['tabs'][] = array(
            'id'    => 'tab_general',
            'title' => $this->language->get( 'tab_general' )
        );

        $data['fields'] = array();

        $data['fields'][] = array(
            'entry'    => $this->language->get( 'entry_status' ),
            'type'     => 'select',
            'name'     => $this->type . '_' . $this->name . '_status',
            'value'    => $this->getConfigValue( 'status', 0 ),
            'required' => true,
            'options'  => array(
                '0' => $this->language->get( 'text_disabled' ),
                '1' => $this->language->get( 'text_enabled' )
            )
        );

        $data['fields'][] = array(
            'entry'    => $this->language->get( 'entry_merchant_code' ),
            'type'     => 'text',
            'size'     => '30',
            'required' => true,
            'name'     => $this->type . '_' . $this->name . '_merchant_code',
            'value'    => $this->getConfigValue( 'merchant_code', '999008881' ),
        );

        $data['fields'][] = array(
            'entry'    => $this->language->get( 'entry_merchant_terminal' ),
            'type'     => 'text',
            'size'     => '3',
            'required' => true,
            'name'     => $this->type . '_' . $this->name . '_merchant_terminal',
            'value'    => $this->getConfigValue( 'merchant_terminal' , '1'),
        );

        $data['fields'][] = array(
            'entry'    => $this->language->get( 'entry_merchant_clave_real' ),
            'type'     => 'text',
            'size'     => '30',
            'required' => true,
            'name'     => $this->type . '_' . $this->name . '_merchant_clave_real',
            'value'    => $this->getConfigValue( 'merchant_clave_real' ),
        );

        $data['fields'][] = array(
            'entry'    => $this->language->get( 'entry_merchant_clave_pruebas' ),
            'type'     => 'text',
            'size'     => '30',
            'required' => true,
            'name'     => $this->type . '_' . $this->name . '_merchant_clave_pruebas',
            'value'    => $this->getConfigValue( 'merchant_clave_pruebas', ' sq7HjrUOBfKmC576ILgskD5srU870gJ7' ),
        );

        $data['fields'][] = array(
            'entry'    => $this->language->get( 'entry_env' ),
            'type'     => 'select',
            'name'     => $this->type . '_' . $this->name . '_env',
            'value'    => $this->getConfigValue( 'env', 0 ),
            'required' => true,
            'options'  => array(
                '0' => $this->language->get( 'text_real' ),
                '1' => $this->language->get( 'text_test' ),
                '2' => $this->language->get( 'text_integration' ),
            )
        );

        $data['fields'][] = array(
            'entry'    => $this->language->get( 'entry_merchant_currency' ),
            'type'     => 'text',
            'size'     => '3',
            'required' => true,
            'name'     => $this->type . '_' . $this->name . '_merchant_currency',
            'value'    => $this->getConfigValue( 'merchant_currency', '9378' ),
        );

        $data['fields'][] = array(
            'entry'    => $this->language->get( 'entry_order_status' ),
            'type'     => 'select',
            'name'     => $this->type . '_' . $this->name . '_order_status_id',
            'value'    => $this->getConfigValue( 'order_status_id', 2 ),
            'required' => true,
            'options'  => $order_statuses
        );

        $data['fields'][] = array(
            'entry'    => $this->language->get( 'entry_geo_zone' ),
            'type'     => 'select',
            'name'     => $this->type . '_' . $this->name . '_geo_zone_id',
            'value'    => $this->getConfigValue( 'geo_zone_id', 2 ),
            'required' => true,
            'options'  => $geo_zones
        );

        $data['fields'][] = array(
            'entry' => $this->language->get( 'entry_sort_order' ),
            'type'  => 'number',
            'size'  => '10',
            'name'  => $this->type . '_' . $this->name . '_sort_order',
            'value' => $this->getConfigValue( 'sort_order' )
        );

        // Stores
        $this->load->model('setting/store');

        $stores = array();

        $stores[0] = $this->language->get('text_default');

        $results = $this->model_setting_store->getStores();

        foreach ($results as $result) {
            $stores[$result['store_id']] = $result['name'];
        }

        $data['fields'][] = array(
            'entry'    => $this->language->get( 'entry_store' ),
            'type'     => 'select',
            'name'     => $this->type . '_' . $this->name . '_store_ids[]',
            'value'    => $this->getConfigValue( 'store_ids', 0 ),
            'required' => true,
            'multiple' => true,
            'options'  => $stores
        );
    }



    protected function getConfigValue( $name, $default = null ) {
        $value = $this->config->get( $this->type . '_' . $this->name . '_' . $name );
        if ( !$value && ! is_null( $default ) ) {
            return $default;
        }

        return $value;
    }
}
