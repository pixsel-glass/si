<?php

include_once(DIR_SYSTEM . 'library/bankart/autoload.php');
require_once(DIR_APPLICATION . 'model/extension/payment/bankart.php');

/**
 * Модель для оплати Flik
 * Використовує тип 'flik' з основних налаштувань Bankart
 */
class ModelExtensionPaymentBankartFlik extends ModelExtensionPaymentBankart
{
    protected $payment_type = 'flik';
}
