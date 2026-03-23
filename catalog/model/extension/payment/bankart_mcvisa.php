<?php

include_once(DIR_SYSTEM . 'library/bankart/autoload.php');
require_once(DIR_APPLICATION . 'model/extension/payment/bankart.php');

/**
 * Модель для оплати Visa/Mastercard/Maestro
 * Використовує тип 'mcvisa' з основних налаштувань Bankart
 */
class ModelExtensionPaymentBankartMcvisa extends ModelExtensionPaymentBankart
{
    protected $payment_type = 'mcvisa';
}
