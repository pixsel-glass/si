<?php

include_once(DIR_SYSTEM . 'library/bankart/autoload.php');

use Bankart\BankartPlugin;

/**
 * Базова модель для всіх варіантів оплати Bankart
 * Кожен варіант (card, visa, account) успадковує цю модель
 */
class ModelExtensionPaymentBankart extends Model
{
    protected $prefix = BankartPlugin::PREFIX . '_';
    protected $payment_type = ''; // Буде перевизначено в дочірніх класах (card, visa, account)

    /**
     * Перевірка статусу доступності методу оплати
     */
    protected function checkStatus($address, $total)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get($this->prefix . 'geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get($this->prefix . 'order_total') > 0 && $this->config->get($this->prefix . 'order_total') > $total) {
            return false;
        } elseif (!$this->config->get($this->prefix . 'geo_zone_id')) {
            return true;
        } elseif ($query->num_rows) {
            return true;
        }
        
        return false;
    }

    /**
     * Головний метод для отримання методу оплати
     */
    public function getMethod($address, $total)
    {

        // Перевіряємо чи увімкнений загальний статус модуля
        if (!$this->config->get($this->prefix . 'status')) {
            return array();
        }

        // Перевіряємо чи увімкнений цей конкретний тип картки (cc_status_card)
        if (!$this->config->get($this->prefix . 'cc_status_' . $this->payment_type)) {
            return array();
        }

        // Перевіряємо чи є API ключі для цього типу
        if (empty($this->config->get($this->prefix . 'cc_api_key_' . $this->payment_type)) || 
            empty($this->config->get($this->prefix . 'cc_api_secret_' . $this->payment_type))) {
            return array();
        }

        $status = $this->checkStatus($address, $total);

        $method_data = array();

        if ($status) {
            // Завантажуємо мовний файл для конкретного варіанту оплати
            $this->load->language('extension/payment/bankart_' . $this->payment_type);
            
            $code = $this->session->data['language'];
            $code = substr($code, 0, 2);

            // Отримуємо назву з налаштувань типу картки (cc_title_card)
            $title_config = $this->config->get($this->prefix . 'cc_title_' . $this->payment_type);
            
            if (!empty($title_config)) {
                $title = $title_config;
            } else {
                // Якщо немає налаштування, використовуємо назву з мовного файлу
                $title = $this->language->get('text_title');
            }

            $method_data = array(
                'code' => 'bankart_' . $this->payment_type,
                'title' => $title,
                'terms' => '',
                'sort_order' => $this->config->get($this->prefix . 'sort_order'),
            );
        }

        return $method_data;
    }
}
