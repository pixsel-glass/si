<?php
require_once(DIR_APPLICATION.'controller/extension/payment/redsys.php');
class ControllerExtensionPaymentBizum extends ControllerExtensionPaymentRedsys {
    protected $name = 'bizum';
    protected $pay_method = 'z';
}