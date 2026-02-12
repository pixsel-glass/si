<?php

include_once(DIR_SYSTEM . 'library/bankart/autoload.php');
require_once(DIR_APPLICATION . 'model/extension/payment/bankart.php');

/**
 * Модель для оплати карткою
 * Використовує тип 'cc' з основних налаштувань Bankart
 */
class ModelExtensionPaymentBankartCc extends ModelExtensionPaymentBankart
{
    protected $payment_type = 'cc';
}
