<?php
require_once( DIR_APPLICATION . 'controller/extension/payment/redsys.php' );
class ControllerExtensionPaymentBizum extends ControllerExtensionPaymentRedsys {
    protected $error = array();
    protected $name = null;

    public function __construct( $registry ) {
        parent::__construct( $registry );
        $this->name = basename( __FILE__, '.php' );
    }

    protected function addFields( &$data ) {
        $data['extension_class'] = 'payment';
        $data['tab_class']       = 'htabs';

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
            'entry' => $this->language->get( 'entry_sort_order' ),
            'type'  => 'number',
            'size'  => '10',
            'name'  => $this->type . '_' . $this->name . '_sort_order',
            'value' => $this->getConfigValue( 'sort_order' )
        );
    }
}
