<?php

include_once(DIR_SYSTEM . 'library/bankart/autoload.php');

use Bankart\Client\Client;
use Bankart\Client\Callback\Result as CallbackResult;
use Bankart\Client\Data\Customer;
use Bankart\Client\Transaction\Debit;
use Bankart\Client\Transaction\Preauthorize;
use Bankart\Client\Transaction\Result as TransactionResult;
use Bankart\BankartGateway;
use Bankart\BankartPlugin;

/**
 * Контролер для оплати Visa/Mastercard/Maestro
 * Використовує тип 'mcvisa' з основних налаштувань Bankart
 */
final class ControllerExtensionPaymentBankartMcvisa extends Controller
{
    use BankartGateway;

    private $prefix = BankartPlugin::PREFIX . '_';
    private $payment_type = 'mcvisa';

    private $bankart_order_states;
    protected $order;
    protected $customer;

    public function index($data = null)
    {
        // Визначаємо payment_type для цього методу
        $data['payment_type'] = $this->payment_type;
        
        // Змінюємо action URL на поточний контролер
        $data['action'] = $this->url->link('extension/payment/bankart_' . $this->payment_type . '/confirm', '', true);

        $this->load->language('extension/payment/bankart_' . $this->payment_type);
        $data['loading_text'] = $this->language->get('loading_text');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['instalments_description'] = $this->language->get('instalments_description');
        $data['instalments_number'] = $this->language->get('instalments_number');
        $data['credit_card_type_prompt'] = $this->language->get('credit_card_type_prompt');

        // Додаємо всі мовні змінні для форми
        $data['card_holder'] = $this->language->get('card_holder');
        $data['card_number'] = $this->language->get('card_number');
        $data['card_cvv'] = $this->language->get('card_cvv');
        $data['card_expiry_date'] = $this->language->get('card_expiry_date');
        $data['card_holder_missing'] = $this->language->get('card_holder_missing');
        $data['card_number_missing'] = $this->language->get('card_number_missing');
        $data['card_number_invalid'] = $this->language->get('card_number_invalid');
        $data['card_cvv_missing'] = $this->language->get('card_cvv_missing');
        $data['card_cvv_invalid'] = $this->language->get('card_cvv_invalid');
        $data['card_expiry_date_missing'] = $this->language->get('card_expiry_date_missing');
        $data['card_expired'] = $this->language->get('card_expired');

        $creditCards = $this->getCreditCardsPublic();
        $creditCards = $this->updateCardsWithInstaments($creditCards);
        
        // Фільтруємо тільки картки для цього типу оплати
        $filteredCards = array();
        foreach ($creditCards as $cardType => $cardData) {
            if ($cardType === $this->payment_type) {
                $filteredCards[$cardType] = $cardData;
            }
        }
        
        $data['credit_cards'] = $filteredCards;
        $data['credit_cards_json'] = json_encode($filteredCards);

        $year = date('Y');
        $data['months'] = range(1, 12);
        $data['years'] = range($year, $year + 50);

        $apiHost = rtrim($this->getConfig('api_host'), '/') . '/';
        $data['api_host'] = $apiHost;

        return $this->load->view('extension/payment/bankart', $data);
    }

    private function updateCardsWithInstaments($creditCards)
    {
        foreach ($creditCards as $cardType => $creditCard) {
            if (array_key_exists('instalments_geo_zone', $creditCard) && isset($this->session->data['payment_address'])) {
                $address = $this->session->data['payment_address'];
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$creditCard['instalments_geo_zone'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
                if (!$query->num_rows) {
                    $creditCards[$cardType]['instalments'] = 1;
                }
            }
            if ($creditCards[$cardType]['instalments'] > 1) {
                $max_calc = floor($this->cart->getTotal()/$creditCard['instalments_amount']);
                $creditCards[$cardType]['instalments'] = ($creditCard['instalments'] <= $max_calc) ? $creditCard['instalments'] : $max_calc;
            }
            unset($creditCards[$cardType]['instalments_amount']);
        }
        return $creditCards;
    }

    private function loadOrderStates() {
        $this->bankart_order_states['started'] = $this->getConfig('order_status_started');
        $this->bankart_order_states['failed'] = $this->getConfig('order_status_failed');
        $this->bankart_order_states['preauthorized'] = $this->getConfig('order_status_preauthorized');
        $this->bankart_order_states['voided'] = $this->getConfig('order_status_voided');
        $this->bankart_order_states['captured'] = $this->getConfig('order_status_captured');
        $this->bankart_order_states['debit_approved'] = $this->getConfig('order_status_debit_approved');
    }

    public function confirm()
    {
        // Перенаправляємо на оригінальний контролер з параметром типу оплати
        $this->request->post['payment_type'] = $this->payment_type;
        
        // Завантажуємо оригінальний контролер
        require_once(DIR_APPLICATION . 'controller/extension/payment/bankart.php');
        $bankartController = new ControllerExtensionPaymentBankart($this->registry);
        return $bankartController->confirm();
    }

    public function callback()
    {
        // Перенаправляємо на оригінальний контролер
        require_once(DIR_APPLICATION . 'controller/extension/payment/bankart.php');
        $bankartController = new ControllerExtensionPaymentBankart($this->registry);
        return $bankartController->callback();
    }

    public function response()
    {
        // Перенаправляємо на оригінальний контролер
        require_once(DIR_APPLICATION . 'controller/extension/payment/bankart.php');
        $bankartController = new ControllerExtensionPaymentBankart($this->registry);
        return $bankartController->response();
    }
}
